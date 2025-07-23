<?php
    $request_method = $_SERVER["REQUEST_METHOD"];

    if ($request_method == "GET" && isset($_GET["current_batch_number"])) {
        header('Content-Type: application/json');
        $current_batch_number = $_GET["current_batch_number"];
        $batches_count = apcu_fetch("count");

        if ($current_batch_number >= $batches_count) {
            echo json_encode(["error" => "No more stock cache batches available"]);
        } else {
            $batch = json_decode(apcu_fetch("symbols_" . $current_batch_number), true);            
            echo json_encode($batch);
        }
        exit;
    }

    else if ($request_method == "GET" && !isset($_GET["current_batch_number"])) {
        echo json_encode(["error"=> "current_batch_number query parameter is required"]);
        exit;
    }

    else if ($request_method == "POST") {
        $key = $_POST["key"];
        $value = $_POST['value'] ?? [];
        var_dump(value: $value);

        apcu_store($key, $value);
        echo "Cache key-value pair is added.";
        exit;
} 