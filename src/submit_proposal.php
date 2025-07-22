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

    else if ($request_method === 'POST' && $is_ajax) {
        global $mysql;


        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);

        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize or increment batch number
        $current_batch_number = isset($_SESSION["current_batch_number"]) ? $_SESSION["current_batch_number"] + 1 : 1;
        $_SESSION["current_batch_number"] = $current_batch_number;

        $loop->addTimer(1.5, function () {
            echo "Timer done (temp)";
        });

        // Short and sweet message
        $message = "Thank you for contributing to BYCIG!";
        header("Location: redirect.php?message=" . urlencode($message) . "&message_type=success");
        
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
        <label>First Name:</label>
        <input type="text" name="first_name" required>
        <br><br>
        
        <label>Last Name:</label>
        <input type="text" name="last_name" required>
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
        <button onclick="fetchNewStockBatch()">Fetch more stocks</button>
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