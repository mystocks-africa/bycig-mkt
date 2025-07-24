<?php
    require 'vendor/autoload.php';

    include 'database.php';

    error_reporting(E_ALL & ~E_DEPRECATED);

    use React\EventLoop\Loop;
    use React\MySQL\QueryResult;
    // Deferred class is used to create a Promise function 
    use React\Promise\Deferred;

    $loop = Loop::get();
    
    $request_method = $_SERVER["REQUEST_METHOD"];

    $ip = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);

    function set_rate_limit() {
        global $ip;

        $ttl = 120; // 2 minutes (60 seconds * 2)
        $expires_at = time() + $ttl;

        $new_payload = json_encode([
            'attempts' => 0,
            'expires_at' => $expires_at,
        ]);

        apcu_store("$ip:rate_limit", $new_payload, $ttl);
    }

    function update_rate_limit($payload) {      
        global $ip;

        $ttl = $payload['expires_at'] - time();
        $new_value = $payload['attempts'] + 1;

        if ($ttl <= 0) return false; // Already expired

        $new_encoded_payload = json_encode([
            'attempts' => $new_value,
            'expires_at' => $payload['expires_at'] // Do not update expires_at (should remain same)
        ]);

        apcu_store("$ip:rate_limit", $new_encoded_payload, $ttl);
    }  
        
    function get_rate_limit() {
        $rate_limit = apcu_fetch("rate_limit");
        if ($rate_limit == false) return false;

        $payload = json_decode($rate_limit, true);
        return $payload;
    }

    function redirect_to_result($message, $type) {
        header("Location: redirect.php?message=" . urlencode($message) . "&message_type=$type");
    }

    function insert_proposal($title, $content, $slug, $date, $guid, $email, $stockName) {
        global $mysql;

        $deferred = new Deferred();

        $user_query = "
            SELECT ID 
            FROM wp_users 
            WHERE user_email = ?
        ";

        $mysql->query($user_query, [$email])
            ->then(function (QueryResult $result) use ($mysql, $title, $content, $slug, $date, $guid, $stockName, $deferred) {
                if (count($result->resultRows) === 0) {
                    $deferred->reject(new Exception("User not found with that email address."));
                    return;
                }

                $user_id = $result->resultRows[0]['ID'];

                $proposal_insert_query = "
                    INSERT INTO wp_2_posts (
                        post_author,
                        post_date,
                        post_date_gmt,
                        post_content,
                        post_title,
                        post_excerpt,
                        post_status,
                        comment_status,
                        ping_status,
                        post_password,
                        post_name,
                        to_ping,
                        pinged,
                        post_modified,
                        post_modified_gmt,
                        post_content_filtered,
                        post_parent,
                        guid,
                        menu_order,
                        post_type,
                        post_mime_type,
                        comment_count
                    )
                    VALUES (
                        ?, ?, ?, ?, ?, '', 'publish', 'closed', 'closed', '', ?, '', '', ?, ?, '', 0, ?, 0, 'proposal', '', 0
                    );
                ";

                $params = [
                    $user_id,
                    $date,
                    $date,
                    $content,
                    $title,
                    $slug,
                    $date,
                    $date,
                    $guid
                ];

                $mysql->query($proposal_insert_query, $params)
                    ->then(function (QueryResult $insert_result) use ($stockName, $deferred) {
                        $deferred->resolve($insert_result);
                    })
                    ->otherwise(function (Exception $error) use ($deferred) {
                        $deferred->reject($error);
                    });
            })
            ->otherwise(function (Exception $error) use ($deferred) {
                $deferred->reject($error);
            });

        return $deferred->promise();
    }

    function insert_proposal_meta($post_id, $leader) {
        global $mysql;

        $deferred = new Deferred();

        $query = "
            INSERT INTO wp_2_postmeta (
                post_id,
                meta_key,
                meta_value
            )
            VALUES (?, 'proposal_cluster_leader', ?)
        ";

        $params = [$post_id, $leader];

        $mysql->query($query, $params)
            ->then(function (QueryResult $result) {
                global $deferred;

                $deferred->resolve($result);
            })
            ->otherwise(function (Exception $error) {
                global $deferred;

                $deferred->reject($error);
            });

        return $deferred->promise();
    }

    if ($request_method === 'POST') {
        $rate_limit_payload = get_rate_limit();

        if ($rate_limit_payload == false) {
            set_rate_limit();
            $rate_limit_payload = get_rate_limit();
        } 

        if ($rate_limit_payload["attempts"] == 2) {
            $message = "Cannot add proposal at this moment. Limit reached for posting proposals.";
            redirect_to_result($message, "error");
            exit;        
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $leader = filter_input(INPUT_POST, 'leader_select', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$email || !$stockName || !$title || !$content) {
            echo "All fields are required.";
            exit;
        }

        // == IN THE FUTURE SEND EMAIL TO USER TO VERIFY IT IS THEM ==
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $title)));
        $date = date('Y-m-d H:i:s');
        $guid = "https://member.bycig.org/proposal/$slug/";

        insert_proposal($title, $content, $slug, $date, $guid, $email, $stockName)
            ->then(function ($insert_result) use ($leader) {
                insert_proposal_meta($insert_result->insertId, $leader);

                $message = "Thank you for contributing to BYCIG's platform!";
                redirect_to_result($message, "success");
            })
            ->otherwise(function (Exception $error) {
                $message = "There has been an error in submitting proposal " . $error->getMessage();
                redirect_to_result($message, "error");
                exit;
            });

        update_rate_limit($rate_limit_payload);
        exit;
    }

    $mysql->quit();
    $loop->stop();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
    <script src="static/javascript/submit-proposal.js"></script>
</head>
<body>
    <?= apcu_fetch("rate_limit") ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br><br>
        
        <label>Proposal Title:</label>
        <input type="text" name="title" required>
        <br><br>
        
        <label>Proposal Content:</label>
        <textarea name="content" required></textarea>
        <br><br>
        
        <label>Pick a stock:</label>
        <br><br>
        
        <!-- Option 1: Select from loaded stocks -->
        <input type="radio" id="useSelect" name="stock_option" value="select" checked onchange="toggleStockInput()">
        <label for="useSelect">Choose from list:</label>
        <select id="stockSelect" name="stock_select">
            <option value="">Loading stocks...</option>
        </select>
        <br><br>
        
        <!-- Option 2: Enter custom stock -->
        <input type="radio" id="useCustom" name="stock_option" value="custom" onchange="toggleStockInput()">
        <label for="useCustom">Can't find your ticker? Enter a custom one:</label>
        <input type="text" id="customStock" name="custom_stock" placeholder="e.g. AAPL, TSLA" disabled>
        <br><br>
        
        <!-- Hidden field to send the final stock value -->
        <input type="hidden" id="finalStockName" name="stock_name">

        <button id="fetchNewStockBtn" type="button" onclick="fetchNewStockBatch()">Fetch more stocks</button>
        <br><br>

        <label>Choose your Cluster Leader:</label>
        <select id="leaderSelect" name="leader_select">
            <option value="">Select your leader</option>
        </select>
        <br><br>
        
        <button type="submit" onclick="setFinalStockValue()">Submit</button>
    </form>
</body>
</html>
