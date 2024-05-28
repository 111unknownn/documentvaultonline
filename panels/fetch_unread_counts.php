<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    include './../config.php';
    
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT COUNT(*) AS unread_count FROM chats WHERE to_id = ? AND opened = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'unread_count' => $row['unread_count']]);
    } else {
        echo json_encode(['success' => false, 'unread_count' => 0]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>
