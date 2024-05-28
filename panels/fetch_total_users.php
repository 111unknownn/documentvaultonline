<?php
// Assuming you have already established a database connection
include '../config/config.php';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Query to fetch total users from the database
$query = "SELECT COUNT(*) AS total_users FROM user_form";
$result = mysqli_query($conn, $query);

// Check if query executed successfully
if ($result) {
    // Fetch the total number of users
    $row = mysqli_fetch_assoc($result);
    $total_users = $row['total_users'];
    echo $total_users; // Output total users
} else {
    echo 'Error fetching total users';
}


?>
