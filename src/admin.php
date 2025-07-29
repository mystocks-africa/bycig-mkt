<?php
require 'vendor/autoload.php';

include 'utils/env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_TOKEN = filter_input(INPUT_GET, "jwt", FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($JWT_TOKEN) && $_SERVER["REQUEST_METHOD"] === "GET") {
    $secret_key = $env["JWT_SECRET"];

    try {
        $decoded = JWT::decode($JWT_TOKEN, new Key($secret_key, 'HS256'));

        $decoded_json = json_encode([
            "cluster_leader_id"=>$decoded->cluster_leader_id,
            "proposal_id"=> $decoded->proposal_id,
        ]);

        echo $decoded_json;
    } catch(Exception $error) {
        $error_json = json_encode([
            "error" => $error->getMessage()
        ]);

        echo $error_json;
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="static/javascript/admin.js"></script>
</head>
<body>
    
</body>
</html>