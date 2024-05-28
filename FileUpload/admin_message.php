<?php
include '../config/config.php';
session_start();
// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_name'])) {
    header('Location: ../login');
    exit();
}

$adminName = $_SESSION['admin_name'];
$thisUserId = $_SESSION['user_id'];

// Process the messaging feature
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../vendor/autoload.php';

    // Validate and sanitize the input data
    $recipientName = $_POST['recipient'];
    $messageType = $_POST['messageType'];
    $messageContent = $_POST['message'];
    $fileUploadPath = 'messages/user_messages/';

    // Fetch the recipient's ID
    $recipientQuery = "SELECT user_id FROM user_form WHERE name = ?";
    $recipientStmt = $conn->prepare($recipientQuery);
    $recipientStmt->bind_param("s", $recipientName);
    $recipientStmt->execute();
    $recipientResult = $recipientStmt->get_result();

    if ($recipientResult->num_rows > 0) {
        $recipientRow = $recipientResult->fetch_assoc();
        $recipientId = $recipientRow['user_id'];

        // Handle file upload if it's a file message
        if ($messageType === 'file' && isset($_FILES['file'])) {
            $fileName = $_FILES['file']['name'];
            $fileTemp = $_FILES['file']['tmp_name'];
            $fileDestination = $fileUploadPath . $fileName;

            if (!move_uploaded_file($fileTemp, '../' . $fileDestination)) {
                echo "Error uploading file.";
                exit();
            }
        } else {
            $fileDestination = null;
        }

        // Insert the message into the database
        $insertQuery = "INSERT INTO user_messages (sender_id, recipient_id, message_type, message_content, file_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iisss", $thisUserId, $recipientId, $messageType, $messageContent, $fileDestination);

        if ($stmt->execute()) {
            // Insert log entry into activities table
            $description = "You sent a message to $recipientName";
            $timestamp = date('Y-m-d H:i:s');

            $insertActivityQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, ?)";
            $status = 'unread'; // Set status to 'unread'
            $activityStmt = $conn->prepare($insertActivityQuery);
            $activityStmt->bind_param("isss", $thisUserId, $description, $timestamp, $status);

            if ($activityStmt->execute()) {
                // Insert log entry into user_logs table
                $currentAction = "Send Message";
                $actionDescription = "$adminName sent a message to $recipientName";
                $logTimestamp = date('Y-m-d H:i:s');

                $insertLogQuery = "INSERT INTO user_logs (user_id, action_description, timestamp, name, current_action) VALUES (?, ?, ?, ?, ?)";
                $logStmt = $conn->prepare($insertLogQuery);
                $logStmt->bind_param("issss", $thisUserId, $actionDescription, $logTimestamp, $adminName, $currentAction);

                if ($logStmt->execute()) {
                    // Create notification for the recipient
                    $notificationMessage = "You have a new message from $adminName";
                    $insertNotificationQuery = "INSERT INTO notifications (sender_id, receiver_id, description) VALUES (?, ?, ?)";
                    $insertNotificationStmt = $conn->prepare($insertNotificationQuery);
                    $insertNotificationStmt->bind_param("iis", $thisUserId, $recipientId, $notificationMessage);
                    $insertNotificationStmt->execute();

                    echo "Message/file sent successfully!";
                } else {
                    echo "Error storing log entry.";
                }
            } else {
                echo "Error storing activity log entry.";
            }
        } else {
            echo "Error sending message/file.";
        }
    } else {
        echo "Recipient not found.";
    }
}
?>


