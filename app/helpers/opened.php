<?php

function opened($id_1, $conn, $chats){
    foreach ($chats as $chat) {
        // Check if the "opened" key exists in the $chat array
        if (isset($chat['opened']) && $chat['opened'] == 0) {
            $opened = 1;
            $chat_id = $chat['chat_id'];

            $sql = "UPDATE chats
                    SET opened = ?
                    WHERE from_id=? 
                    AND chat_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$opened, $id_1, $chat_id]);
        }
    }
}
?>
