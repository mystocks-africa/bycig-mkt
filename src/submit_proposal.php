<?php
    // PHP API 
    
    error_reporting(E_ALL & ~E_DEPRECATED);
    require 'vendor/autoload.php';

    use React\EventLoop\Loop;
    use React\Http\Browser;
    use React\MySQL\Factory;
    use React\MySQL\QueryResult;

    $loop = Loop::get();
    $browser = new Browser($loop);

    $factory =  new Factory();
    $env = parse_ini_file('.env');
    $mysql_uri = $env["MYSQL_URI"];
    $mysql = $factory->createLazyConnection($mysql_uri);
    
    $request_method = $_SERVER["REQUEST_METHOD"];
    
    function client_expects_json(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return stripos($accept, 'application/json') !== false;
    }

    if ($request_method == "GET" && client_expects_json()) {
        header('Content-Type: application/json');

        $cluster_leader_query = "
            SELECT user_login 
            FROM `wp_usermeta` 
            INNER JOIN `wp_users` ON `wp_usermeta`.`user_id` = `wp_users`.`ID` 
            WHERE `meta_key` = 'wp_capabilities' 
            AND `meta_value` = 'a:1:{s:14:\"cluster-leader\";b:1;}';
        ";

        $cluster_leaders = [];

        $mysql->query($cluster_leader_query)->then(function (QueryResult $command) {
            global $cluster_leaders;

            foreach ($command->resultRows as $row) {
                $one_cluster_leader = $row["user_login"];
                array_push($cluster_leaders, $one_cluster_leader);
            }

            echo json_encode($cluster_leaders);
        }, function (Exception $error) {
            echo "Error: {$error->getMessage()}" . PHP_EOL;
        });

        exit;
    }

    else if ($request_method === 'POST') {
        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $leader = filter_input(INPUT_POST, 'leader_select', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validate required fields
        if (!$email || !$stockName || !$title || !$content) {
            echo "All fields are required.";
            exit;
        }

        // ** IN THE FUTURE SEND EMAIL TO USER TO VERIFY IT IS THEM **
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $title)));
        $date = date('Y-m-d H:i:s');
        $guid = "https://member.bycig.org/proposal/$slug/";

        // 1. Get user ID by email
        $user_query = "SELECT ID FROM wp_users WHERE user_email = ?";

        $mysql->query($user_query, [$email])->then(function (QueryResult $result) use ($mysql, $title, $content, $slug, $date, $guid, $stockName) {
            if (count($result->resultRows) === 0) {
                echo "User not found with that email address.";
                return;
            }

            $user_id = $result->resultRows[0]['ID'];

            // 2. Insert proposal with dynamic values
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
                VALUES (?, ?, ?, ?, ?, '', 'publish', 'closed', 'closed', '', ?, '', '', ?, ?, '', 0, ?, 0, 'proposal', '', 0);
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

        return $mysql->query($proposal_insert_query, $params);
            })->then(function (?QueryResult $result) use ($stockName) {
                if ($result instanceof QueryResult) {
                    $message = "Thank you for contributing to BYCIG! Your proposal for $stockName has been submitted.";
                    header("Location: redirect.php?message=" . urlencode($message) . "&message_type=success");
                    exit;
                }
            }, function (Exception $error) {
                echo "Error: " . $error->getMessage();
            });

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

        <button type="button" onclick="fetchNewStockBatch()">Fetch more stocks</button>
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