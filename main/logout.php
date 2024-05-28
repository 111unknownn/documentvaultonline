<?php
@include '../config/config.php';
session_start();
// Include session timeout functionality
require_once '../session_timeout.php';
// Set the time zone to Manila, Philippines
date_default_timezone_set('Asia/Manila');

// Retrieve admin ID from the session
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Debugging statement
echo "Admin ID from session: " . $userId;

// Check if the admin is logged in
if ($userId) {
    // Debugging statement
    echo "<br>Admin is logged in. Performing logout actions.";

    // Update the user_logs record
    $logoutTime = date('Y-m-d H:i:s');
    $status = 'offline';
    $statusSymbol = '○'; // Unicode character for an empty circle symbol for offline users
    $statusColor = 'gray'; // Set the status color for offline users

    // Use prepared statements to update admin status to 'offline'
    $updateStatusSql = "UPDATE user_logs SET status = 'offline' WHERE user_id=?";
    $updateStatusStmt = mysqli_prepare($conn, $updateStatusSql);
    mysqli_stmt_bind_param($updateStatusStmt, "i", $userId);
    mysqli_stmt_execute($updateStatusStmt);
    mysqli_stmt_close($updateStatusStmt);

    // Use prepared statements to update user_logs record
    $updateLogQuery = "UPDATE user_logs SET logout_time = ?, status = ?, status_symbol = ? WHERE user_id = ? AND logout_time IS NULL";
    $updateLogStmt = mysqli_prepare($conn, $updateLogQuery);

    if ($updateLogStmt) {
        mysqli_stmt_bind_param($updateLogStmt, "sssi", $logoutTime, $status, $statusSymbol, $userId);
        mysqli_stmt_execute($updateLogStmt);
        mysqli_stmt_close($updateLogStmt);
    } else {
        // Print the error message for debugging
        die("Error preparing update statement: " . mysqli_error($conn));
    }

    // Clear all session variables
    session_unset();

    // Destroy the session if it's active
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    // Debugging statement
    echo "<br>Logout successful.";

    // Redirect to the login page
    header('Location: ../main/index');
    exit(); // Ensure that no further code is executed after the redirect
} else {
    // Debugging statement if the admin is not logged in
    echo "<br>Admin is not logged in. No logout actions performed.";
}
?>
