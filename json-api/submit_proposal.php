<?php
$BASE_DIR =  "../";
require  $BASE_DIR . 'vendor/autoload.php';

include_once $BASE_DIR . 'utils/auth.php';
include_once $BASE_DIR . 'utils/database.php';
include_once $BASE_DIR . 'utils/env.php';
include_once $BASE_DIR . 'utils/redirection.php';
include_once $BASE_DIR . 'utils/rate_limit.php';
include_once $BASE_DIR . 'utils/auth.php';

serverside_check_auth();

use PHPMailer\PHPMailer\PHPMailer;
use Firebase\JWT\JWT;

header("Content-Type: application/json");

$ip = $_SERVER["REMOTE_ADDR"];
$request_method = $_SERVER["REQUEST_METHOD"];

$email = get_session()['email'];

// This API file is ONLY for POST requests
if ($request_method !== 'POST') {
    exit();
}

function validate_pdf_upload($file) {
    $allowed_mime = 'application/pdf';
    $max_size = 5 * 1024 * 1024; // 5MB max size

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "File upload error.";
    }
    if ($file['type'] !== $allowed_mime) {
        return "Only PDF files are allowed.";
    }
    if ($file['size'] > $max_size) {
        return "File size exceeds 5MB.";
    }
    return true;
}


function upload_to_ftp($file) {
global $env;

$ftp_conn = ftp_connect($env["FTP_SERVER"]) or die("FTP error");
ftp_login($ftp_conn, $env["FTP_USER"], $env["FTP_PASS"]);

$filename = "uploads/" . bin2hex(random_bytes(5)) . ".pdf";
$result = ftp_put($ftp_conn, $filename, $file["tmp_name"], FTP_BINARY);
ftp_close($ftp_conn);
return $result ? $filename : false;
}

function email_cluster_leader($cluster_leader_id, $proposal_id) {
    global $mysqli;
    global $env;
    global $email;

    $mail = new PHPMailer();
    $host = $env["SMTP_HOST"];
    $username = $env["SMTP_USERNAME"];
    $password = $env["SMTP_PASSWORD"];
    $port = $env["SMTP_PORT"];


    $find_cluster_email_query = "
        SELECT cluster_leader
        FROM users
        WHERE email = ?
        LIMIT 1;
    ";  

    $stmt = $mysqli->prepare($find_cluster_email_query);
    $stmt->bind_param("i", $email);

    if ($stmt->execute()) {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username; 
        $mail->Password = $password;
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port = $port;

        // Create a JWT for an authentication token (on admin panel)
        $secret_key = $env["JWT_SECRET"];
        $payload = [
            'proposal_id' => $proposal_id,
            'cluster_leader_id' => $cluster_leader_id,
            'exp' => time() + (30 * 24 * 60 * 60) // 30 days from now
        ];
        $jwt = JWT::encode($payload, $secret_key,'HS256', null);

        $mail->setFrom($username, 'No Reply');
        $mail->addAddress('hemitvpatel@gmail.com', 'Cluster Leader');
        $mail->Subject = 'New proposal submission - BYCIG';
        $mail->Body = "Hi! You have a new proposal submission. Go to our platform admin panel and use the following token to access it. <br> JWT authentication token: $jwt";
        $mail->isHTML(true);

        $mail->send();
    }
}

$rate_limit_payload = get_rate_limit();

if ($rate_limit_payload === false) {
    set_rate_limit();
    $rate_limit_payload = get_rate_limit();
}

if ($rate_limit_payload["attempts"] >= 2) {
    redirect_to_result("Cannot add proposal at this moment. Limit reached for posting proposals.", "error");
}

// Sanitize inputs
$stock_ticker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
$stock_name = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
$subject_line = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
$thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
$bid_price = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
$target_price = filter_input(INPUT_POST, 'target_price', FILTER_VALIDATE_FLOAT);
$file = $_FILES["proposal_file"] ?? null;

if (
    empty($stock_ticker) ||
    empty($stock_name) ||
    empty($subject_line) ||
    empty($thesis) ||
    !isset($bid_price) || !is_numeric($bid_price) ||
    !isset($target_price) || !is_numeric($target_price) ||
    empty($file)
) {
    $message = "All fields are required and must be valid.";
    redirect_to_result($message, "error");
    exit();
}

if ($target_price < $bid_price) {
    $message = "Target price cannot be lower than bid price.";
    redirect_to_result($message, "error");
    exit();
}

$file_validation_result = validate_pdf_upload($_FILES['proposal_file']);

if ($file_validation_result !== true) {
    redirect_to_result($file_validation_result, "error");
    exit();
} 

$pathname = upload_to_ftp($file);

$insert_proposal_query = "
INSERT INTO proposals (
post_author, stock_ticker, stock_name,
subject_line, thesis, bid_price, target_price, proposal_file
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";


$stmt = $mysqli->prepare($insert_proposal_query);
$stmt->bind_param(
'ssssssss',
$email,
$stock_ticker,
$stock_name,
$subject_line,
$thesis,
$bid_price,
$target_price,
$pathname
);

if (!$stmt->execute()) {
redirect_to_result("Error inserting proposal extra info: " . $stmt->error, "error");
} 

$proposal_id = $mysqli->insert_id; 
$stmt->close();

update_rate_limit($rate_limit_payload);
email_cluster_leader($cluster_leader_id, $proposal_id);
redirect_to_result("Thank you for contributing to BYCIG's platform!", "success");

$mysqli->close();