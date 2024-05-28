<?php
session_start();
include '../config/config.php';
// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../vendor/autoload.php';

    // Validate and sanitize the input data
    $senderName = $_POST['sender']; // Assuming sender name is provided in the form
    $recipientName = $_POST['recipient'];
    $messageType = $_POST['messageType'];
    $messageContent = $_POST['message'];

    // Fetch the recipient's id based on the selected recipient's name from the user_form table
    $recipientQuery = "SELECT user_id FROM user_form WHERE name = ?";
    $recipientStmt = $conn->prepare($recipientQuery);
    $recipientStmt->bind_param("s", $recipientName);
    $recipientStmt->execute();
    $recipientResult = $recipientStmt->get_result();

    if ($recipientResult->num_rows > 0) {
        $recipientRow = $recipientResult->fetch_assoc();
        $recipientId = $recipientRow['user_id'];
        
        // Insert the message into the database
        $insertQuery = "INSERT INTO user_messages (sender_id, recipient_id, message_type, message_content) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iiss", $senderId, $recipientId, $messageType, $messageContent);

        if ($stmt->execute()) {
            echo "Message sent successfully!";
        } else {
            echo "Error sending message.";
        }
    } else {
        echo "Recipient not found.";
    }
}
?>
