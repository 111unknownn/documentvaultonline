<?php

function getChats($id_1, $id_2, $conn) {
    // Prepare the statement
    $sql = "SELECT * FROM chats
            WHERE (from_id=? AND to_id=?)
            OR    (to_id=? AND from_id=?)
            ORDER BY chat_id ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return [];
    }

    // Bind parameters
    $stmt->bind_param("iiii", $id_1, $id_2, $id_1, $id_2);

    // Execute the query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch all rows
    $chats = [];
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }

    // Check if there are any rows returned
    if (count($chats) > 0) {
        return $chats;
    } else {
        return [];
    }
}

?>
