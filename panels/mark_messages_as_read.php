<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_POST['from_id'])) {
    include '../../config.php';
    include 'functions.php'; // Ensure this file contains the `opened` function

    $user_id = $_SESSION['user_id'];
    $from_id = intval($_POST['from_id']);

    // Fetch chats that need to be marked as opened
    $sql = "SELECT * FROM chats WHERE from_id = ? AND to_id = ? AND opened = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $from_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chats = $result->fetch_all(MYSQLI_ASSOC);

    // Mark the chats as opened
    opened($user_id, $conn, $chats);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters or not logged in']);
}
?>
