<?php
include 'database.php'; 

error_reporting(E_ALL & ~E_DEPRECATED);

$ip = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);
$request_method = $_SERVER["REQUEST_METHOD"];

// RATE LIMIT FUNCTIONS
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
    $stockName = null;
    // Determine stock name based on submitted option
    if ($_POST['stock_option'] === 'select') {
        $stockName = filter_input(INPUT_POST, 'stock_select', FILTER_SANITIZE_SPECIAL_CHARS);
    } elseif ($_POST['stock_option'] === 'custom') {
        $stockName = filter_input(INPUT_POST, 'custom_stock', FILTER_SANITIZE_SPECIAL_CHARS);
    }

    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
    $leader = filter_input(INPUT_POST, 'leader_select', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$email || !$stockName || !$title || !$content) {
        echo "All fields are required.";
        exit;
    }

    // Find user ID from email
    $stmt = $mysqli->prepare("SELECT ID FROM wp_users WHERE user_email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "User not found with that email address.";
        exit;
    }
    $user = $result->fetch_assoc();
    $user_id = $user['ID'];
    $stmt->close();

    // Prepare values
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $title)));
    $date = date('Y-m-d H:i:s');
    $guid = "https://member.bycig.org/proposal/$slug/";

    // Insert proposal
    $insert_sql = "
        INSERT INTO wp_2_posts (
            post_author,
            post_date,
            post_date_gmt,
            post_content,
            post_title,
            post_excerpt,
            post_status,
            comment_status,
            ping_status,
            post_password,
            post_name,
            to_ping,
            pinged,
            post_modified,
            post_modified_gmt,
            post_content_filtered,
            post_parent,
            guid,
            menu_order,
            post_type,
            post_mime_type,
            comment_count
        )
        VALUES (
            ?, ?, ?, ?, ?, '', 'publish', 'closed', 'closed', '', ?, '', '', ?, ?, '', 0, ?, 0, 'proposal', '', 0
        )
    ";

    $stmt = $mysqli->prepare($insert_sql);
    $stmt->bind_param(
        'issssssss',
        $user_id,
        $date,
        $date,
        $content,
        $title,
        $slug,
        $date,
        $date,
        $guid
    );

    if (!$stmt->execute()) {
        redirect_to_result("There has been an error submitting proposal: " . $stmt->error, "error");
    }

    $post_id = $stmt->insert_id;
    $stmt->close();

    // Insert post meta
    $meta_sql = "INSERT INTO wp_2_postmeta (post_id, meta_key, meta_value) VALUES (?, 'proposal_cluster_leader', ?)";
    $stmt = $mysqli->prepare($meta_sql);
    $stmt->bind_param('is', $post_id, $leader);
    if (!$stmt->execute()) {
        redirect_to_result("Error inserting proposal meta: " . $stmt->error, "error");
    }
    $stmt->close();

    update_rate_limit($rate_limit_payload);

    redirect_to_result("Thank you for contributing to BYCIG's platform!", "success");
}

// Close DB connection at the end
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
    <?php
    $rate_limit_display = apcu_fetch("$ip:rate_limit");
    if ($rate_limit_display) {
        echo htmlspecialchars($rate_limit_display);
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br><br>

        <label>Proposal Title:</label>
        <input type="text" name="title" required>
        <br><br>

        <label>Proposal Content:</label>
        <textarea name="content" required></textarea>
        <br><br>

        <label>Pick a stock:</label>
        <br><br>

        <input type="radio" id="useSelect" name="stock_option" value="select" checked onchange="toggleStockInput()">
        <label for="useSelect">Choose from list:</label>
        <select id="stockSelect" name="stock_select">
            <option value="">Loading stocks...</option>
        </select>
        <br><br>

        <input type="radio" id="useCustom" name="stock_option" value="custom" onchange="toggleStockInput()">
        <label for="useCustom">Can't find your ticker? Enter a custom one:</label>
        <input type="text" id="customStock" name="custom_stock" placeholder="e.g. AAPL, TSLA" disabled>
        <br><br>

        <button id="fetchNewStockBtn" type="button" onclick="fetchNewStockBatch()">Fetch more stocks</button>
        <br><br>

        <label>Choose your Cluster Leader:</label>
        <select id="leaderSelect" name="leader_select">
            <option value="">Select your leader</option>
        </select>
        <br><br>

        <button type="submit" onclick="setFinalStockValue()">Submit</button>
    </form>
</body>
</html>
