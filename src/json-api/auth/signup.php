<?php
include "../../utils/database.php";

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $post_user_query = "
        INSERT INTO users (email, password)
        VALUES (?, ?)
    ";

    try {
        $stmt = $mysqli->prepare($post_user_query);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $error) {
        $error_message = json_encode([
            "error" => $error->getMessage()
        ]);

        echo $error_message;
    }

}