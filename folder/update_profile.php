<?php
date_default_timezone_set('Asia/Manila');
session_start();
include '../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect or handle unauthorized access
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

// Get the user's ID
$userID = $_SESSION['user_id'];

// Validate and sanitize input
$fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
$email = mysqli_real_escape_string($conn, $_POST['email']);

// Perform additional validation as needed

// Update the user's profile information in the database
$query = "UPDATE user_form SET name='$fullName', email='$email' WHERE user_id = $userID";

if (mysqli_query($conn, $query)) {
    // Insert activity into activities table
    $description = "You updated your profile information";
    $timestamp = date('Y-m-d H:i:s');
    $insertQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES ($userID, '$description', '$timestamp', 'unread')";
    if (mysqli_query($conn, $insertQuery)) {
        // Send JSON response with success status
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        // Handle error in inserting activity
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Error updating profile']);
    }
} else {
    // Handle error (e.g., database error)
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['status' => 'error', 'message' => 'Error updating profile']);
}
?>
