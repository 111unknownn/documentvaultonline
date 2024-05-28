<?php
include '../config/config.php';
session_start();

// Check if the sender's ID exists
$thisUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$thisUserId) {
    $response = array('success' => false, 'newMessagesCount' => 0);
    echo json_encode($response);
    exit; // Stop execution if sender not found
}

// Query to fetch the count of unread notifications for the sender
$query = "SELECT COUNT(*) AS new_messages_count FROM user_messages WHERE recipient_id = ? AND is_read = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $thisUserId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$newMessagesCount = $row['new_messages_count'];

$response = array('success' => true, 'newMessagesCount' => $newMessagesCount);
echo json_encode($response);
?>
