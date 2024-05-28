<?php
session_start();
include '../config/config.php';


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Log unauthorized access
    error_log("Unauthorized access to fetch_profile.php. User not logged in.");

    // Redirect or handle unauthorized access
    header('HTTP/1.1 401 Unauthorized');
    exit();
}


// Get the user's ID
$userID = $_SESSION['user_id'];

// Fetch the user's current profile information
$query = "SELECT name as fullName, email FROM user_form WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if ($result && $row = mysqli_fetch_assoc($result)) {
    // Send JSON response with profile information
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    // Handle error (e.g., profile not found)
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error fetching profile information']);
}
