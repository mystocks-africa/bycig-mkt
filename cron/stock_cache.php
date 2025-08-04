<?php 
include "../utils/env.php";
    $curl_handle = curl_init();
    $symbols = [];
    function fetch_from_finnhub() {
        global $curl_handle;
        global $symbols;
        global $env;

        $finnhub_api_key = $env["FINNHUB_API_KEY"];

        curl_setopt_array($curl_handle, [
            CURLOPT_URL => "https://finnhub.io/api/v1/stock/symbol?exchange=US&token=" . $finnhub_api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($curl_handle);
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Error: " . json_last_error_msg() . "\n";
            exit;
        }

        foreach ($data as $item) {
            array_push($symbols, $item["symbol"]);
        }
    }

    function add_cache($key, $value) {
        global $curl_handle;

        $post = [
            "key" => $key,
            "value" => json_encode($value)
        ];

        // a CLI script (CRON), so we need to request a server (can't directly access APCu)
        curl_setopt_array($curl_handle, [
            CURLOPT_URL => "http://localhost:3000/src/cache/stock_cache.php",
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10
        ]);

        $response2 = curl_exec($curl_handle);

        if (isset($response2)) {
            echo $response2 . "\n";
            echo "=== CRON JOB FINISHED ===";
        }

        curl_close($curl_handle);
    }
    
    fetch_from_finnhub();

    $batches = array_chunk($symbols, 1000);
    $batches_count = count($batches);

    for ($index = 0; $index < count($batches); $index++) {
        add_cache("symbols_" . $index, $batches[$index]);
        print_r($batches[$index]);
    }

    add_cache("count", $batches_count);
    echo $batches_count;
