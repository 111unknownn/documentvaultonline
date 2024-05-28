<?php

function getConversation($user_id, $conn) {
    /**
    Getting all the conversations 
    for current (logged in) user
    **/
    $sql = "SELECT * FROM conversations
            WHERE user_1=? OR user_2=?
            ORDER BY conversation_id DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return [];
    }

    // Bind parameters
    $stmt->bind_param("ii", $user_id, $user_id); // Assuming user_id is an integer

    // Execute the query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $conversations = [];
        // Fetch all rows
        while ($conversation = $result->fetch_assoc()) {
            // if conversations user_1 row equal to user_id
            if ($conversation['user_1'] == $user_id) {
                $sql2 = "SELECT * FROM user_form WHERE user_id=?";
                $stmt2 = $conn->prepare($sql2);
                if (!$stmt2) {
                    echo "Error preparing statement: " . $conn->error;
                    continue;
                }
                $stmt2->bind_param("i", $conversation['user_2']);
            } else {
                $sql2 = "SELECT * FROM user_form WHERE user_id=?";
                $stmt2 = $conn->prepare($sql2);
                if (!$stmt2) {
                    echo "Error preparing statement: " . $conn->error;
                    continue;
                }
                $stmt2->bind_param("i", $conversation['user_1']);
            }

            // Execute the second query
            $stmt2->execute();
            
            // Get result of the second query
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $allConversations = $result2->fetch_all(MYSQLI_ASSOC);
                // Push the data into the array 
                array_push($conversations, $allConversations[0]);
            }
        }
        return $conversations;
    } else {
        return [];
    }
}

?>
