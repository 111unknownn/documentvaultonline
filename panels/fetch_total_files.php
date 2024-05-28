<?php

include '../config/config.php';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
// Query to fetch total count of files from the document table
$query = "SELECT COUNT(*) AS total_files FROM document";
$result = mysqli_query($conn, $query);

// Check if query executed successfully
if ($result) {
    // Fetch the total count of files
    $row = mysqli_fetch_assoc($result);
    $total_files = $row['total_files'];
    
    // Output total files
    echo $total_files;
} else {
    // Error handling if query fails
    echo 'Error fetching total files';
}
?>