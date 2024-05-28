<?php
include '../config/config.php';
session_start();

// Fetch notifications and count unread ones
// Your existing code to fetch notifications

// Store the count in a session variable
$_SESSION['notification_count'] = $unreadCount;

// Return notifications
echo json_encode($notifications);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo "Unauthorized";
    exit();
}

$userId = $_SESSION['user_id'];

// Query to fetch notifications for the logged-in user
$sql = "SELECT * FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch notifications as an associative array
$notifications = [];
while ($row = $result->fetch_assoc()) {
    // Include the created_at field in the notification array
    $notifications[] = [
        'id' => $row['id'],
        'description' => $row['description'],
        'status' => $row['status'],
        'created_at' => $row['created_at'] // Include the created_at field
    ];
}

// Encode notifications as JSON and return
echo json_encode($notifications);
?>
