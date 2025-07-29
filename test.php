<?php
include "./src/database.php";

$find_cluster_email_query = "
    SELECT user_email
    FROM `wp_usermeta` 
    INNER JOIN `wp_users` ON `wp_usermeta`.`user_id` = `wp_users`.`ID` 
    WHERE `meta_key` = 'wp_capabilities' 
    AND `meta_value` = 'a:1:{s:14:\"cluster-leader\";b:1;}'
    AND `user_id` = ?
    LIMIT 1;
";

$id = 21;

$stmt = $mysqli->prepare($find_cluster_email_query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "Email: " . $row["user_email"];
} else {
    echo "Query failed: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

