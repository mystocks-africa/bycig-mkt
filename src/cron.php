<?php 
    $curl_handle = curl_init();

    $env = parse_ini_file('.env');
    $finnhub_api_key = $env["FINNHUB_API_KEY"];

    curl_setopt_array($curl_handle, [
        CURLOPT_URL => "https://finnhub.io/api/v1/stock/symbol?exchange=US&token=" . $finnhub_api_key,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($curl_handle);
    $http_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($curl_handle);
    
    $data = json_decode($response, true);
    $symbols = [];
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Error: " . json_last_error_msg() . "\n";
        exit;
    }

    foreach ($data as $item) {
        $symbols[] = $item["symbol"]; 
    }

            
    $post = [
        "symbols" => $symbols
    ];
    
    curl_setopt_array($curl_handle, [
        CURLOPT_URL => "http://localhost:3000/src/cache.php",
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
?>