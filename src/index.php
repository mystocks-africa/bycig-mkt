<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require 'vendor/autoload.php';

use React\EventLoop\Loop;
use React\Http\Browser;

$loop = Loop::get();
$browser = new Browser($loop);
$data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);

    // only temp (need something to mimick the behvaiour of I/o calls)
    $loop->addTimer(1.5, function () {
        echo "Test timer done";
    });

    echo "$firstName . $lastName . $stockName \n";

    $loop->run();

    // Message should be short and sweet
    
    $message = "Thank you for contributing to BYCIG!";
    header("Location: redirect.php?message={$message}&message_type=success");

    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
    <link rel="stylesheet" href="static/css/index.css" >
</head>
<body>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <label>First Name:</label>
        <input type="text" name="first_name" required>
        <br>
        <label>Last Name:</label>
        <input type="text" name="last_name" required>
        <br>
        <label>Pick a stock:</label>
        <input type="text" name="stock_name" required>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
