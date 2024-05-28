<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $to_id = $_POST['to_id'];
    $from_id = $_SESSION['user_id'];
    $created_at = date("Y-m-d H:i:s");

    // Handle file upload
    $file_name = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = basename($file['name']);
        $file_path = '../uploads/' . $file_name;
        move_uploaded_file($file['tmp_name'], $file_path);
    }

    $sql = "INSERT INTO chats (from_id, to_id, message, file, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $from_id, $to_id, $message, $file_name, $created_at);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response = [
            'success' => true,
            'message' => $message,
            'file' => $file_name,
            'time' => $created_at
        ];
    } else {
        $response = ['success' => false, 'message' => 'Failed to send message.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
?>
