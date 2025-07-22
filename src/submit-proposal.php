<?php
    error_reporting(E_ALL & ~E_DEPRECATED);
    require 'vendor/autoload.php';

    use React\EventLoop\Loop;
    use React\Http\Browser;

    $loop = Loop::get();
    $browser = new Browser($loop);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);

        // Update session batch count
        $_SESSION["current_batch_number"] = $current_batch_number;

        $loop->addTimer(1.5, function () {
            echo "Timer done (temp)";
        });

        // Short and sweet message
        $message = "Thank you for contributing to BYCIG!";
        header("Location: redirect.php?message=" . urlencode($message) . "&message_type=success");
        exit;
    }

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
        <br>
        
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
        
        <button type="submit" onclick="setFinalStockValue()">Submit</button>
    </form>

    <button onclick="fetchNewStockBatch()">Fetch more stocks</button>
</body>
</html>