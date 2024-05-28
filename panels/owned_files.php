<?php
session_start();

// Assuming you have already established a database connection
include '../config/config.php';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT COUNT(*) AS total_files FROM document WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_files = $row['total_files'];
        echo $total_files;
    } else {
        echo 'Error fetching total files';
    }
} else {
    echo 'User not logged in';
}
?>
