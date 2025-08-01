<?php
include "../../utils/database.php";

$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method == "POST") {
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $post_user_query = "
        INSERT INTO users (full_name, email, pwd)
        VALUES (?, ?, ?)
    ";

    try {
        $stmt = $mysqli->prepare($post_user_query);
        $stmt->bind_param(
            "sss", 
            $full_name,
            $email, 
            $hash_password
        );
        $stmt->execute();
        $stmt->close();
    } catch (Exception $error) {
        $error_message = json_encode([
            "error" => $error->getMessage()
        ]);

        echo $error_message;
    }

}