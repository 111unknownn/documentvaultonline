<?php
// Database connection parameters
include ("../config/config.php");

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user_id and action are received via POST
if (isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    // Define the new status based on the action
    $new_status = ($action === "enable") ? "enabled" : "disabled";

    // Update the user status in the database
    $sql = "UPDATE user_form SET status = '$new_status' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $sql)) {
        echo "User status updated successfully.";
    } else {
        echo "Error updating user status: " . mysqli_error($conn);
    }
} else {
    echo "Missing parameters: user_id and action.";
}

// Close the database connection
mysqli_close($conn);
?>
