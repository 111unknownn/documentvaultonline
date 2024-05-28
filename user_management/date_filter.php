<?php
// Connect to the database
include '../config/config.php';

// Check if the date filter parameter is set
if(isset($_GET['dateFilter']) && !empty($_GET['dateFilter'])) {
    $dateFilter = $_GET['dateFilter'];
    // Prepare and execute the query to fetch filtered logs with user status
$sql = "SELECT ul.*, uf.status AS user_status FROM user_logs ul INNER JOIN user_form uf ON ul.user_id = uf.user_id WHERE DATE(ul.login_time) = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $dateFilter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Initialize an array to store the filtered logs
$filteredLogs = array();

// Fetch filtered logs and add them to the array
while ($row = mysqli_fetch_assoc($result)) {
    $filteredLogs[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Send the filtered logs as JSON response
header('Content-Type: application/json');
echo json_encode($filteredLogs);
}
?>
