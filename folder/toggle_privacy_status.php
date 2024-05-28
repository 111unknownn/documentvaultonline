<?php
// Include database configuration file
include '../config/config.php';
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if folder_name is set in POST data
if (isset($_POST["folder_name"])) {
    $folder_name = mysqli_real_escape_string($conn, $_POST["folder_name"]);

    // Fetch the current privacy_status of the folder
    $query = "SELECT privacy_status FROM folder_management WHERE folder_name = '$folder_name'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['privacy_status'];
        $new_status = ($current_status == 'locked') ? 'unlocked' : 'locked';

        // Update the privacy_status of the folder
        $update_query = "UPDATE folder_management SET privacy_status = '$new_status' WHERE folder_name = '$folder_name'";
        if (mysqli_query($conn, $update_query)) {
            // Return success message and the new status
            $response = array(
                'success' => true,
                'new_status' => $new_status
            );
            echo json_encode($response);
            exit;
        } else {
            // Return error message if update fails
            $response = array(
                'success' => false,
                'message' => 'Error updating privacy status.'
            );
            echo json_encode($response);
            exit;
        }
    } else {
        // Return error message if fetch fails
        $response = array(
            'success' => false,
            'message' => 'Error fetching current privacy status.'
        );
        echo json_encode($response);
        exit;
    }
} else {
    // Return error message if folder_name is not set in POST data
    $response = array(
        'success' => false,
        'message' => 'Folder name not provided.'
    );
    echo json_encode($response);
    exit;
}
?>
