<?php
include '../config/config.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo "Unauthorized";
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Method Not Allowed";
    exit();
}

// Update the status of notifications to "read" in the database
$userId = $_SESSION['user_id'];

$sql = "UPDATE notifications SET status = 'read' WHERE receiver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    http_response_code(200); // OK
    echo "Notifications marked as read";
} else {
    http_response_code(500); // Internal Server Error
    echo "Error marking notifications as read";
}
?>
