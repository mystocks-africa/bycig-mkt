<?php
require 'vendor/autoload.php';

include 'utils/database.php';
include 'utils/env.php';
include 'utils/redirection.php';

use PHPMailer\PHPMailer\PHPMailer;
use Firebase\JWT\JWT;

$ip = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);
$request_method = $_SERVER["REQUEST_METHOD"];

function set_rate_limit() {
    global $ip;
    $ttl = 120; // 2 minutes
    $expires_at = time() + $ttl;

    $new_payload = json_encode([
        'attempts' => 0,
        'expires_at' => $expires_at,
    ]);

    apcu_store("$ip:rate_limit", $new_payload, $ttl);
}

function update_rate_limit($payload) {
    global $ip;

    $ttl = $payload['expires_at'] - time();
    if ($ttl <= 0) return false;

    $new_value = $payload['attempts'] + 1;

    $new_encoded_payload = json_encode([
        'attempts' => $new_value,
        'expires_at' => $payload['expires_at']
    ]);

    apcu_store("$ip:rate_limit", $new_encoded_payload, $ttl);
    return true;
}

function get_rate_limit() {
    global $ip;
    $rate_limit = apcu_fetch("$ip:rate_limit");
    if ($rate_limit === false) return false;
    return json_decode($rate_limit, true);
}

// Simple function to validate PDF upload
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

function generate_rand_string() {
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function upload_to_ftp($file) {
    global $env;

    $ftp_server = $env["FTP_SERVER"];
    $ftp_conn = ftp_connect($ftp_server) or redirect_to_result("Could not connect too $ftp_server", "error");
    $ftp_user = $env["FTP_USER"];
    $ftp_pass = $env["FTP_PASS"];
    $remote_file_name = "uploads" . "/" . basename(generate_rand_string() . ".pdf");

    ftp_login($ftp_conn, $ftp_user, $ftp_pass);
    $save_file = ftp_put($ftp_conn, $remote_file_name, $file["tmp_name"], FTP_BINARY);
    ftp_close($ftp_conn);
    
    if($save_file) {
        return $remote_file_name;
    } else {
        redirect_to_result("Error in uploading pdf file.", "error");
        exit;
    }
}

function email_cluster_leader($cluster_leader_id, $proposal_id) {
    global $mysqli;
    global $env;

    $mail = new PHPMailer();
    $host = $env["SMTP_HOST"];
    $username = $env["SMTP_USERNAME"];
    $password = $env["SMTP_PASSWORD"];
    $port = $env["SMTP_PORT"];

    $find_cluster_email_query = "
        SELECT user_email
        FROM `wp_usermeta` 
        INNER JOIN `wp_users` ON `wp_usermeta`.`user_id` = `wp_users`.`ID` 
        WHERE `meta_key` = 'wp_capabilities' 
        AND `meta_value` = 'a:1:{s:14:\"cluster-leader\";b:1;}'
        AND `user_id` = ?
        LIMIT 1;
    ";  

    $stmt = $mysqli->prepare($find_cluster_email_query);
    $stmt->bind_param("i", $cluster_leader_id);

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

if ($request_method === 'POST') {
    $rate_limit_payload = get_rate_limit();

    if ($rate_limit_payload === false) {
        set_rate_limit();
        $rate_limit_payload = get_rate_limit();
    }

    if ($rate_limit_payload["attempts"] >= 2) {
        redirect_to_result("Cannot add proposal at this moment. Limit reached for posting proposals.", "error");
    }

    // Sanitize inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $cluster_leader_id = filter_input(INPUT_POST, 'cluster_leader_id', FILTER_VALIDATE_INT);
    $stock_ticker = filter_input(INPUT_POST, 'stock_ticker', FILTER_SANITIZE_SPECIAL_CHARS);
    $stock_name = filter_input(INPUT_POST, 'stock_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $subject_line = filter_input(INPUT_POST, 'subject_line', FILTER_SANITIZE_SPECIAL_CHARS);
    $thesis = filter_input(INPUT_POST, 'thesis', FILTER_SANITIZE_SPECIAL_CHARS);
    $bid_price = filter_input(INPUT_POST, 'bid_price', FILTER_VALIDATE_FLOAT);
    $target_price = filter_input(INPUT_POST, 'target_price', FILTER_VALIDATE_FLOAT);
    $file = $_FILES["proposal_file"] ?? null;

    if (!$email || !$cluster_leader_id || !$stock_ticker || !$stock_name || !$subject_line
        || !$thesis || $bid_price === false || $target_price === false || !isset($file)) {
        $message = "All fields are required and must be valid.";
        redirect_to_result($message, "error");
        exit;
    }

    if ($target_price < $bid_price) {
        $message = "Target price cannot be lower than bid price.";
        redirect_to_result($message, "error");
        exit;
    }

    $file_validation_result = validate_pdf_upload($_FILES['proposal_file']);

    if ($file_validation_result !== true) {
        redirect_to_result($file_validation_result, "error");
        exit;
    } 

    $pathname = upload_to_ftp($file);

    $stmt = $mysqli->prepare("SELECT ID, display_name FROM wp_users WHERE user_email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $message = "Email address does not exist on database";
        redirect_to_result($message, "error");
        exit;
    }
    
    $user = $result->fetch_assoc();
    $post_author_id = $user["ID"];

    // display_name will give us the full name of a user (important info for proposal)
    $post_author_name = $user["display_name"];

    $stmt->close();

    $insert_proposal_query = "
        INSERT INTO wp_2_proposals (
            post_author, full_name, email, cluster_leader_id, stock_ticker, stock_name,
            subject_line, thesis, bid_price, target_price, proposal_file
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";


    $stmt = $mysqli->prepare($insert_proposal_query);
    $stmt->bind_param(
        'ississsssds',
        $post_author_id,
        $post_author_name,
        $email,
        $cluster_leader_id,
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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="static/javascript/submit-proposal.js"></script>
    <link rel="stylesheet" href="static/css/index.css" >
</head>
<body id="submit-proposal-body">
    <h1>Submit Your Stock Proposal</h1>
    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email Address:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label>Choose your Cluster Leader:</label>
        <select id="leaderSelect" name="cluster_leader_id">
            <option value="">Select your leader</option>
        </select>
        <br><br>

        <input type="radio" id="useSelect" name="stock_option" value="select" checked onchange="toggleStockInput()">
        <label for="useSelect">Choose from list:</label>
        <select id="stockSelect" name="stock_ticker">
            <option value="">Loading stocks...</option>
        </select>
        <br><br>

        <input type="radio" id="useCustom" name="stock_option" value="custom" onchange="toggleStockInput()">
        <label for="useCustom">Can't find your ticker? Enter a custom one:</label>
        <input type="text" id="customStock" name="stock_ticker" placeholder="e.g. AAPL, TSLA" disabled>
        <br><br>

        <button id="fetchNewStockBtn" type="button" onclick="fetchNewStockBatch()">Fetch more stocks</button>
        <br><br>

        <label for="stock_name">Stock Name:</label><br>
        <input type="text" id="stock_name" name="stock_name" maxlength="255" required><br><br>

        <label for="subject_line">Subject Line:</label><br>
        <input type="text" id="subject_line" name="subject_line" maxlength="255" required><br><br>

        <label for="thesis">1 Sentence Thesis:</label><br>
        <textarea id="thesis" name="thesis" maxlength="1000" required></textarea><br><br>

        <label for="bid_price">Bid Price (where you want us to buy at):</label><br>
        <input type="number" id="bid_price" name="bid_price" step="0.01" min="0" required><br><br>

        <label for="target_price">Target Price (must be ≥ Bid Price):</label><br>
        <input type="number" id="target_price" name="target_price" step="0.01" min="0" required><br><br>

        <label for="proposal_file">Upload Proposal (PDF only, max 5MB):</label><br>
        <input type="file" id="proposal_file" name="proposal_file" accept="application/pdf" required><br><br>

        <button type="submit">Submit Proposal</button>
    </form>
</body>
</html>
