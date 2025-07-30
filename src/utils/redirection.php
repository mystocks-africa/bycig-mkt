<?php
function redirect_to_result($message, $type) {
    header("Location: /src/redirect.php?message=" . urlencode($message) . "&message_type=$type");
    exit();
}