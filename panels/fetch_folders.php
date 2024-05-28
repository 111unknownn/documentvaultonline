<?php
session_start();
// Include your database connection file
include "../config/config.php";

// Check if user_id is set
if(isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Fetch folders for the user
    $folderQuery = mysqli_query($conn, "SELECT folder_id, folder_name FROM folders WHERE user_id = $userId");
    $folders = [];
    if(mysqli_num_rows($folderQuery) > 0) {
        while($folderRow = mysqli_fetch_assoc($folderQuery)) {
            $folders[] = $folderRow;
        }
        echo json_encode(['success' => true, 'folders' => $folders]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
