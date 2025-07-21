<?php 
    // allow composer files to be used (not needed in php frameworks)
    require 'vendor/autoload.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $stock_name = filter_input(INPUT_POST, "stock_name", FILTER_SANITIZE_SPECIAL_CHARS);

        echo $first_name . $last_name . $stock_name;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BYCIG Stock Proposal Submission</title>
</head>
<body>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">

        <label>First Name:</label>
        <input type="text" name="first_name">
        <br>

        <label>Last Name:</label>
        <input type="text" name="last_name">
        <br>
        
        <label>Stock:</label>
        <input type="text" name="stock_name">
        <br>
        
        <button type="submit">Submit</button>
    </form>
</body>
</html>
