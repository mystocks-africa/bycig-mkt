<?php 
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        header('Content-Type: application/json');

        $current_batch_number = $_GET["current_batch_number"];
        $batches_count = apcu_fetch("count");

        if ($current_batch_number >= $batches_count) {
            echo json_encode("no_more_stocks");
        } else {
            $batch = json_decode(apcu_fetch("symbols_" . $current_batch_number), true);            
            echo json_encode($batch);
        }
    }

    else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $key = $_POST["key"];
        $value = $_POST['value'] ?? [];
        var_dump(value: $value);

        apcu_store($key, $value);
        echo "Cache key-value pair is added.";

    } 
?>
