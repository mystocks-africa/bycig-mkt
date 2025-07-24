<?php
include 'database.php';

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

function redirect_to_result($message, $type) {
    header("Location: redirect.php?message=" . urlencode($message) . "&message_type=$type");
    exit();
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

    if (!$email || !$cluster_leader_id || !$stock_ticker || !$stock_name || !$subject_line
        || !$thesis || $bid_price === false || $target_price === false || !isset($_FILES["proposal_file"])) {
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

    $insert_extra_sql = "
        INSERT INTO wp_2_proposals (
            post_author, full_name, email, cluster_leader_id, stock_ticker, stock_name,
            subject_line, thesis, bid_price, target_price, proposal_file
        ) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    // == THIS WILL HAVE A UPLOADED FILE PATH == 
    $proposal_file_db_path = "TEST";

    $stmt = $mysqli->prepare($insert_extra_sql);
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
        $proposal_file_db_path
    );

    if (!$stmt->execute()) {
        redirect_to_result("Error inserting proposal extra info: " . $stmt->error, "error");
    } else {
        $stmt->close();
        update_rate_limit($rate_limit_payload);
        redirect_to_result("Thank you for contributing to BYCIG's platform!", "success");
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>BYCIG Stock Proposal Submission</title>
    <script src="static/javascript/submit-proposal.js"></script>
</head>
<body>
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
