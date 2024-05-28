<?php
session_start();
include '../config/config.php';

// Check if the admin is logged in
if (isset($_SESSION['admin_name'])) {
    $adminName = $_SESSION['admin_name'];
} else {
    header('location: valid_location.php');
    exit(); // Stop further execution
}

// Function to insert a notification into the database
function insertNotification($user_id, $description) {
    global $conn;
    $query = "INSERT INTO notifications (receiver_id, description, status, created_at) VALUES (?, ?, 'unread', NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $description);
    $stmt->execute();
}

// Function to insert an activity into the database
function insertActivity($user_id, $description) {
    global $conn;
    $query = "INSERT INTO activities (user_id, description) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $description);
    $stmt->execute();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT status FROM user_form WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $current_status = $row['status'];

            if ($current_status === 'enabled') {
                $new_status = 'disabled';
                $message = "User account disabled. Please contact the administrator to enable it again.";
            } else {
                $new_status = 'enabled';
                $message = "User account enabled.";
            }

            $query = "UPDATE user_form SET status = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $new_status, $user_id);
            $stmt->execute();

            $notification_description = "User account status changed: " . ($new_status === 'enabled' ? "Enabled" : "Disabled");
            insertNotification($user_id, $notification_description);

            $activity_description = "Changed status of user ID " . $user_id . " to " . $new_status;
            insertActivity($user_id, $activity_description);

            echo json_encode(array("success" => true, "message" => $message, "new_status" => $new_status));
        } else {
            echo json_encode(array("success" => false, "message" => "User not found."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "User ID not provided."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request method."));
}
?>
