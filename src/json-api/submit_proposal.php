<?php
require '../vendor/autoload.php';
include '../utils/database.php';
include '../utils/env.php';
include '../utils/redirection.php';
include '../utils/rate_limit.php';

use PHPMailer\PHPMailer\PHPMailer;
use Firebase\JWT\JWT;

header("Content-Type: application/json");

$ip = $_SERVER["REMOTE_ADDR"];
$request_method = $_SERVER["REQUEST_METHOD"];

if ($request_method !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

function validate_pdf($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) return "File upload error.";
    if ($file['type'] !== 'application/pdf') return "Only PDF files are allowed.";
    if ($file['size'] > 5 * 1024 * 1024) return "File exceeds 5MB.";
    return true;
}

function upload_to_ftp($file, $env) {
    $ftp_conn = ftp_connect($env["FTP_SERVER"]) or die("FTP error");
    ftp_login($ftp_conn, $env["FTP_USER"], $env["FTP_PASS"]);

    $filename = "uploads/" . bin2hex(random_bytes(5)) . ".pdf";
    $result = ftp_put($ftp_conn, $filename, $file["tmp_name"], FTP_BINARY);
    ftp_close($ftp_conn);
    return $result ? $filename : false;
}

function send_email($cluster_leader_id, $proposal_id, $env, $mysqli) {
    $stmt = $mysqli->prepare("SELECT user_email FROM wp_users WHERE ID = ?");
    $stmt->bind_param("i", $cluster_leader_id);
    $stmt->execute();
    $email = $stmt->get_result()->fetch_assoc()["user_email"];
    $stmt->close();

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $env["SMTP_HOST"];
    $mail->SMTPAuth = true;
    $mail->Username = $env["SMTP_USERNAME"];
    $mail->Password = $env["SMTP_PASSWORD"];
    $mail->SMTPSecure = 'ssl';
    $mail->Port = $env["SMTP_PORT"];

    $token = JWT::encode([
        'proposal_id' => $proposal_id,
        'cluster_leader_id' => $cluster_leader_id,
        'exp' => time() + (30 * 24 * 60 * 60)
    ], $env["JWT_SECRET"], 'HS256');

    $mail->setFrom($env["SMTP_USERNAME"], 'No Reply');
    $mail->addAddress($email);
    $mail->Subject = "New proposal submitted";
    $mail->isHTML(true);
    $mail->Body = "New proposal received. JWT: $token";
    $mail->send();
}

$data = $_POST;
$file = $_FILES['proposal_file'];

$required_fields = ['email', 'cluster_leader_id', 'stock_ticker', 'stock_name', 'subject_line', 'thesis', 'bid_price', 'target_price'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing or invalid fields"]);
        exit();
    }
}

if ($data['target_price'] < $data['bid_price']) {
    http_response_code(400);
    echo json_encode(["error" => "Target price must be ≥ bid price"]);
    exit();
}

if (($msg = validate_pdf($file)) !== true) {
    http_response_code(400);
    echo json_encode(["error" => $msg]);
    exit();
}



$ftp_path = upload_to_ftp($file, $env);
if (!$ftp_path) {
    http_response_code(500);
    echo json_encode(["error" => "FTP upload failed"]);
    exit();
}

check_rate_limit_and_update();

$stmt = $mysqli->prepare("SELECT ID, display_name FROM wp_users WHERE user_email = ?");
$stmt->bind_param('s', $data['email']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(400);
    echo json_encode(["error" => "User not found"]);
    exit;
}

$stmt = $mysqli->prepare("
    INSERT INTO wp_2_proposals (post_author, full_name, email, cluster_leader_id, stock_ticker, stock_name, subject_line, thesis, bid_price, target_price, proposal_file)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'ississsssds',
    $user["ID"],
    $user["display_name"],
    $data['email'],
    $data['cluster_leader_id'],
    $data['stock_ticker'],
    $data['stock_name'],
    $data['subject_line'],
    $data['thesis'],
    $data['bid_price'],
    $data['target_price'],
    $ftp_path
);
$stmt->execute();
$proposal_id = $mysqli->insert_id;
$stmt->close();

send_email($data['cluster_leader_id'], $proposal_id, $env, $mysqli);

echo json_encode(["success" => true, "message" => "Proposal submitted successfully"]);
