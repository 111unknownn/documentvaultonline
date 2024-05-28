<?php
session_start();
header('Content-Type: application/json');

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Include necessary files
include '../../config.php';

// Function to get user's name by their user_id
function getUsernameById($user_id, $conn) {
    $sql = "SELECT name FROM user_form WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['name'];
    } else {
        return null;
    }
}

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['message']) && isset($_POST['to_id'])) {

        $message = htmlspecialchars($_POST['message']);
        $to_id = intval($_POST['to_id']);
        $from_id = $_SESSION['user_id'];
        $file_path = null;

        // Handle file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'file_attachments/';
            $fileName = basename($_FILES['attachment']['name']);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFilePath)) {
                $file_path = 'file_attachments/' . $fileName;
                // If the message is a file, set the message to a description
                $message = "File: " . $fileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error uploading file']);
                exit();
            }
        }

        $sql = "INSERT INTO chats (from_id, to_id, message, file_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $from_id, $to_id, $message, $file_path);
        $success = $stmt->execute();

        if ($success) {
            $time = date("h:i:s a");

            $sql2 = "SELECT * FROM conversations WHERE (user_1=? AND user_2=?) OR (user_2=? AND user_1=?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("iiii", $from_id, $to_id, $from_id, $to_id);
            $stmt2->execute();

            $result = $stmt2->get_result();

            if ($result->num_rows == 0) {
                $sql3 = "INSERT INTO conversations (user_1, user_2) VALUES (?, ?)";
                $stmt3 = $conn->prepare($sql3);
                $stmt3->bind_param("ii", $from_id, $to_id);
                $stmt3->execute();
            }

            // Fetch the receiver's name
            $receiver_name = getUsernameById($to_id, $conn);

            if ($receiver_name) {
                // Insert activity with the receiver's name
                $description = "You sent a message to $receiver_name";
                $timestamp = date("Y-m-d H:i:s");
                $status = 1; // Assuming 1 means active or completed
                $sql4 = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, ?)";
                $stmt4 = $conn->prepare($sql4);
                $stmt4->bind_param("issi", $from_id, $description, $timestamp, $status);
                $stmt4->execute();
            }

            // Create notification for the recipient
            $sender_name = getUsernameById($from_id, $conn);
            $notificationMessage = "You have a new message from $sender_name";
            $insertNotificationQuery = "INSERT INTO notifications (sender_id, receiver_id, description) VALUES (?, ?, ?)";
            $insertNotificationStmt = $conn->prepare($insertNotificationQuery);
            $insertNotificationStmt->bind_param("iis", $from_id, $to_id, $notificationMessage);
            $insertNotificationStmt->execute();

            echo json_encode(['success' => true, 'message' => $message, 'file_path' => $file_path, 'time' => $time]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error inserting message']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>
