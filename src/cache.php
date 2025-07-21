<?php 
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $cache = apcu_fetch("symbols");

    echo $cache;
}
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symbols = $_POST['symbols'] ?? [];
    var_dump($_POST['symbols']);

    apcu_store("symbols", $symbols);
    echo "Cache added.";
}
?>
