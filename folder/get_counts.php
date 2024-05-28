<?php

// Start the session
session_start();

// Retrieve counts from session variables
$notificationCount = isset($_SESSION['notification_count']) ? $_SESSION['notification_count'] : 0;
$activityCount = isset($_SESSION['activity_count']) ? $_SESSION['activity_count'] : 0;

// Return counts as JSON
echo json_encode(array(
    'notification_count' => $notificationCount,
    'activity_count' => $activityCount
));
?>
