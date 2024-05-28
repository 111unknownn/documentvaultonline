<?php
include '../config/config.php';

// Function to insert a notification into the database
function insertNotification($receiver_id, $description) {
    global $conn;

    // Prepare and execute the SQL statement to insert the notification
    $query = "INSERT INTO notifications (receiver_id, description, status, created_at) VALUES (?, ?, 'unread', NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $receiver_id, $description);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Check if user_id and description are provided
    if ($userId === null || $description === '') {
        http_response_code(400); // Bad Request
        echo "Invalid data";
        exit();
    }

    // Insert the notification
    if (insertNotification($userId, $description)) {
        http_response_code(200); // OK
        echo "Notification inserted successfully";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error inserting notification";
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Method Not Allowed";
    exit();
}
?>
