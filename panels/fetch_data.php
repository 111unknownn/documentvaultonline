<?php
include("../config/config.php");
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$thisUserId = $_SESSION['user_id'];

// Fetch recent activities
$recentActivitiesQuery = "SELECT description, timestamp FROM activities WHERE user_id = ? ORDER BY timestamp DESC LIMIT 5";
$recentActivitiesStmt = $conn->prepare($recentActivitiesQuery);
$recentActivitiesStmt->bind_param("i", $thisUserId);
$recentActivitiesStmt->execute();
$recentActivitiesResult = $recentActivitiesStmt->get_result();
$recentActivities = [];

while ($row = $recentActivitiesResult->fetch_assoc()) {
    $recentActivities[] = $row;
}

// Fetch notifications
$notificationsQuery = "SELECT description, status, created_at FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC";
$notificationsStmt = $conn->prepare($notificationsQuery);
$notificationsStmt->bind_param("i", $thisUserId);
$notificationsStmt->execute();
$notificationsResult = $notificationsStmt->get_result();
$notifications = [];

while ($row = $notificationsResult->fetch_assoc()) {
    $notifications[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['recentActivities' => $recentActivities, 'notifications' => $notifications]);
?>
