<?php
session_start();
@include '../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $recipient = isset($_POST['recipient']) ? htmlspecialchars($_POST['recipient']) : '';
    $replyContent = isset($_POST['replyContent']) ? htmlspecialchars($_POST['replyContent']) : '';

    // You can add additional validation here

    // Fetch the name of the sender associated with the user_id from the user_logs table
    $senderQuery = "SELECT name FROM user_logs WHERE user_id = ?";
    $senderStmt = $conn->prepare($senderQuery);
    $senderStmt->bind_param("i", $_SESSION['user_id']);
    $senderStmt->execute();
    $senderResult = $senderStmt->get_result();
    $senderRow = $senderResult->fetch_assoc();
    $senderName = $senderRow['name'];

    // Fetch the name of the recipient associated with the recipient_id from the user_logs table
    $recipientQuery = "SELECT name FROM user_logs WHERE user_id = ?";
    $recipientStmt = $conn->prepare($recipientQuery);
    $recipientStmt->bind_param("i", $recipient);
    $recipientStmt->execute();
    $recipientResult = $recipientStmt->get_result();
    $recipientRow = $recipientResult->fetch_assoc();
    $recipientName = $recipientRow['name'];

    // Example: Insert the reply into the database (assuming you have a table for messages)
    $userId = $_SESSION['user_id'];
    $query = "INSERT INTO user_messages (sender_id, recipient_id, message_type, message_content) VALUES (?, ?, 'text', ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $userId, $recipient, $replyContent);

    if ($stmt->execute()) {
        // Successful reply insertion

        // Insert the reply action along with the sender's and receiver's names into the user_logs table
        $currentAction = "Replied to message";
        $actionDescription = "$senderName replied to a message of $recipientName";

        $logQuery = "INSERT INTO user_logs (user_id, name, current_action, action_description) VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("isss", $userId, $senderName, $currentAction, $actionDescription);
        $logStmt->execute();

        // Create a notification for the recipient
        $notification_message = "You have a new reply from user $senderName";
        $insertNotificationQuery = "INSERT INTO notifications (sender_id, receiver_id, description) VALUES (?, ?, ?)";
        $insertNotificationStmt = $conn->prepare($insertNotificationQuery);
        $insertNotificationStmt->bind_param("iis", $userId, $recipient, $notification_message);
        $insertNotificationStmt->execute();

        header("Location: inbox.php?reply_success=1"); // Redirect to inbox with success query parameter
        exit();
    } else {
        // Handle the case where reply insertion fails
        echo "Error sending reply.";
    }
} else {
    // Redirect to the inbox page if the form is not submitted
    header("Location: inbox.php");
    exit();
}
?>
