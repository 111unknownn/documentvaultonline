<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['id_2'])) {
        include '../../config.php';

        $id_1 = $_SESSION['user_id'];
        $id_2 = $_POST['id_2'];

        $user = getUser($id_2, $conn);

        if (!empty($user)) {
            $sql = "SELECT * FROM chats
                    WHERE (to_id=? AND from_id=?) OR (to_id=? AND from_id=?)
                    ORDER BY created_at ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $id_1, $id_2, $id_2, $id_1);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $chats = [];
                while ($row = $result->fetch_assoc()) {
                    $chats[] = $row;
                }
                echo json_encode(['success' => true, 'chats' => $chats]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No messages found.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
}

function getUser($user_id, $conn){
    $sql = "SELECT user_id, name, username, email, user_type, p_p FROM user_form WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return [];
    }
}
?>
