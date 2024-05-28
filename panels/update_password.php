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

// Get the new password from the POST request
$newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

// Validate the new password
if (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $newPassword)) {
    $response = array('status' => 'error', 'message' => 'Password must contain at least one uppercase letter and one special character');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Hash the new password (you might want to use a more secure hashing method)
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the user's password in the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "UPDATE user_form SET password = '$hashedPassword' WHERE user_id = $userID";
$result = mysqli_query($conn, $query);

// Check if the update was successful
if ($result) {
    // Insert activity into activities table
    $description = " You changed your password";
    $timestamp = date('Y-m-d H:i:s');
    $insertQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES ($userID, '$description', '$timestamp', 'unread')";
    $activityResult = mysqli_query($conn, $insertQuery);
    
    if ($activityResult) {
        $response = array('status' => 'success', 'message' => 'Password changed successfully');
    } else {
        $response = array('status' => 'error', 'message' => 'Error inserting activity');
    }
} else {
    $response = array('status' => 'error', 'message' => 'Error changing password');
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
