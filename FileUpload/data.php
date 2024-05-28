<?php
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'dms_db';

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Get the user ID from the session
$thisUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($thisUserId !== null) {
    // Fetch the number of notifications for the logged-in user
    $sql = "SELECT COUNT(*) AS notification_count FROM notification_data WHERE receiver_user_id = $thisUserId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['notification_count'];
    } else {
        echo "0";
    }
} else {
    echo "User ID not found.";
}

