<?php
include '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (!$userId) {
        echo "0";
        exit;
    }

    // Query to count unread notifications for the user
    $countQuery = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();

    if ($countResult->num_rows > 0) {
        $countRow = $countResult->fetch_assoc();
        echo $countRow['unread_count'];
    } else {
        echo "0";
    }
}
?>
