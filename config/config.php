<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'dms_db';

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set the time zone for the current session
mysqli_query($conn, "SET time_zone = '+8:00'"); // Set the correct offset for 'Asia/Manila'

// Execute the SQL query to check MariaDB time zone
$query = "SELECT @@global.time_zone AS global_time_zone, @@session.time_zone AS session_time_zone";
$result = mysqli_query($conn, $query);




?>
