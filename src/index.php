<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require 'vendor/autoload.php';

use React\EventLoop\Loop;
use React\Http\Browser;
use Psr\Http\Message\ResponseInterface;

$loop = Loop::get();
$browser = new Browser($loop);
$data = null;

$env = parse_ini_file('.env');
$finnhub_api_key = $env["FINNHUB_API_KEY"];

$browser->get("https://finnhub.io/api/v1/stock/symbol?exchange=US&token=" . $finnhub_api_key, ['Content-Type' => 'application/json'])
        ->then(
            function (ResponseInterface $response) {
                global $data;

                $data = json_decode((string) $response->getBody(), true);
                echo "Received " . count($data) . " stock symbols.\n";
            },
            function (Exception $error) {
                global $loop;
                
                $message = urlencode("Error when fetching for stocks: " . $error->getMessage());
                header("Location: redirect.php?message=$message&message_type=error");
                $loop->stop();
            }
);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $stockName = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);

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

    <script>
        const jsVar = "<?php echo $data; ?>"
        console.log(jsVar)
    </script>
</body>
</html>
