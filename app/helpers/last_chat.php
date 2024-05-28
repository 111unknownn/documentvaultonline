<?php

function lastChat($id_1, $id_2, $conn) {
    // Prepare the statement
    $sql = "SELECT * FROM chats
            WHERE (from_id=? AND to_id=?)
            OR    (to_id=? AND from_id=?)
            ORDER BY chat_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return '';
    }

    // Bind parameters
    $stmt->bind_param("iiii", $id_1, $id_2, $id_1, $id_2);

    // Execute the query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch row
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['message'];
    } else {
        return '';
    }
}

?>
