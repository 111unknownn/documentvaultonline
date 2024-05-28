<?php
// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
include("../config/config.php");
session_start();

// Check if the user is logged in and obtain the user ID
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in";
    exit();
}

$thisUserId = $_SESSION['user_id'];

if ($_FILES["upload_file"]["name"] != '') {
    $file_size = $_FILES['upload_file']['size']; // Size of the file in bytes
    $max_file_size = 25 * 1024 * 1024; // 25MB in bytes

    if ($file_size <= $max_file_size) {
        $data = explode(".", $_FILES["upload_file"]["name"]);
        $extension = end($data); // Get the file extension
        $allowed_extension = array("xlsx", "pdf", "docx", "doc", "ppt", "pptx", "txt", "zip");

        if (in_array($extension, $allowed_extension)) {
            $new_file_name = $_FILES["upload_file"]["name"]; // Use the original file name
            $path = $_POST["hidden_folder_name"] . '/' . $new_file_name;

            if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $path)) {
                echo 'File Uploaded';

                // Insert log entry
                $current_action = "Upload";
                $action_description = "You uploaded a file: $new_file_name in private.";
                $timestamp = date('Y-m-d H:i:s');
                $status = 'unread'; // Add status variable
                $insertActivityQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertActivityQuery);
                $stmt->bind_param("isss", $thisUserId, $action_description, $timestamp, $status); // Bind all parameters

                if ($stmt->execute()) {
                    // Log entry successfully inserted
                } else {
                    echo "Error inserting log entry: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo 'There was an error uploading the file';
            }
        } else {
            echo 'Invalid File Format';
        }
    } else {
        echo 'File size exceeds the 25MB limit';
    }
} else {
    echo 'No file selected';
}
?>
