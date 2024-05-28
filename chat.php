<?php

session_start();

include './config.php';
include 'app/helpers/user.php';
include 'app/helpers/conversations.php';
include 'app/helpers/chat.php';
include 'app/helpers/opened.php';

// Function to get user by username
function getUserByUsername($username, $conn) {
    $sql = "SELECT user_id, name, p_p FROM user_form WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

// Check if the user parameter is set
if (!isset($_GET['user'])) {
    echo "No user specified";
    exit();
}



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Getting User data by username
$username = $_GET['user'];
$chatWith = getUserByUsername($username, $conn);

if (empty($chatWith)) {
    echo "User not found: " . htmlspecialchars($username);
    exit();
}

$chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);
opened($chatWith['user_id'], $conn, $chats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
   
    <link rel="stylesheet" href="../css/style.css"> 
    <link rel="icon" href="img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        /* Fixed height and scrolling for the chat box */
        .chat-box {
            max-height: 500px; /* Adjust the max-height as needed */
            overflow-y: auto; /* Enable vertical scrolling */
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div>
        <div class="d-flex align-items-center">
            <img src="../uploads/<?=$chatWith['p_p']?>" class="w-15 rounded-circle" style="width:60px">
            <h3 class="display-4 fs-sm m-2" style="font-size:20px;">
                <?=$chatWith['name']?>
                <br>
                <div class="d-flex align-items-center" title="online" >
                    <div class="online"></div>
                    <small class="d-block p-1" style="font-size:10px;"></small>
                </div>
            </h3>
        </div>
        <!-- Chat box with fixed height and scrolling -->
        <div class="shadow p-4 rounded d-flex flex-column mt-2 chat-box" id="chatBox">
        <?php 
if (!empty($chats)) {
    foreach($chats as $chat){
        if($chat['from_id'] == $_SESSION['user_id']) { ?>
            <p class="rtext align-self-end border rounded p-2 mb-1" style="background-color: #007bff; color: #fff;">
                <?php 
                // Check if the message is a file
                if (substr($chat['message'], 0, 11) === "File attached") {
                    // Extract the file path from the message
                    $filePath = substr($chat['message'], 14);
                    // Display the file as a link
                    echo "<a href='$filePath' target='_blank'>File attached</a>"; 
                } else {
                    // Display regular text message
                    echo $chat['message'];
                }
                ?> 
                <small class="d-block"><?=$chat['created_at']?></small>
            </p>
        <?php } else { ?>
            <p class="ltext border rounded p-2 mb-1">
                <?php 
                // Check if the message is a file
                if (substr($chat['message'], 0, 11) === "File attached") {
                    // Extract the file path from the message
                    $filePath = substr($chat['message'], 14);
                    // Display the file as a link
                    echo "<a href='$filePath' target='_blank'>File attached</a>"; 
                } else {
                    // Display regular text message
                    echo $chat['message'];
                }
                ?> 
                <small class="d-block"><?=$chat['created_at']?></small>
            </p>
        <?php } 
    }
} else { ?>
    <div class="alert alert-info text-center">
        <i class="fa fa-comments d-block fs-big"></i>
        No messages yet, Start the conversation
    </div>
<?php } ?>

        </div>
        <div class="input-group mb-3">
            <!-- Attachment icon inside the form control -->
            <span class="input-group-text" onclick="document.getElementById('fileInput').click()"><i class="fa fa-paperclip"></i></span>
            <!-- Hidden file input -->
            <input type="file" id="fileInput" style="display: none;" onchange="displaySelectedFile()">
            <!-- Textarea for the message -->
            <textarea cols="3" id="message" class="form-control"></textarea>
            <!-- Button to send the message -->
            <button class="btn btn-primary" id="sendBtn">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
function displaySelectedFile() {
    const fileInput = document.getElementById('fileInput');
    const messageTextarea = document.getElementById('message');
    const selectedFile = fileInput.files[0];
    if (selectedFile) {
        const fileName = selectedFile.name;
        messageTextarea.value = fileName;
    }
}


// Function to set the default scroll position to the bottom without automatic scrolling
function setDefaultScrollPosition() {
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollDown = chatBox.scrollHeight;
}

// Function to scroll the chat box to the bottom
function scrollDown() {
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollDown = chatBox.scrollHeight;
}

// Call the function to set default scroll position when the window loads
window.onload = setDefaultScrollPosition;

$(document).ready(function(){
    $("#sendBtn").on('click', function(){
        const messageTextarea = $("#message");
        const message = messageTextarea.val();
        const to_id = <?php echo json_encode($chatWith['user_id']); ?>;
        let formData = new FormData();

        // Check if a file is attached
        const fileInput = document.getElementById('fileInput');
        const selectedFile = fileInput.files[0];

        // If a file is attached, append it to the FormData object
        if (selectedFile) {
            formData.append('attachment', selectedFile);
        }

        // Append message and to_id to the FormData object
        formData.append('message', message);
        formData.append('to_id', to_id);

        $.ajax({
            url: "../app/ajax/insert.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data, status) {
                data = typeof data === 'string' ? JSON.parse(data) : data;
                if (data.success) {
                    const newMessage = `<p class="rtext align-self-end border rounded p-2 mb-1">
                        ${data.message}
                        <small class="d-block">${data.time}</small>
                    </p>`;
                    messageTextarea.val("");
                    $("#chatBox").append(newMessage);
                    scrollDown();
                } else {
                    alert(data.message);
                }
            }
        });
    });

    // Function to display the selected file name in the textarea
    function displaySelectedFile() {
        const fileInput = document.getElementById('fileInput');
        const messageTextarea = document.getElementById('message');
        const selectedFile = fileInput.files[0];
        if (selectedFile) {
            const fileName = selectedFile.name;
            messageTextarea.value = `File: ${fileName}`;
        }
    }
});


    let lastSeenUpdate = function(){
        $.get("../app/ajax/update_last_seen.php");
    }
    lastSeenUpdate();
    setInterval(lastSeenUpdate, 10000);

    // Function to fetch and display chat messages
    const fetchData = function(){
        $.post("../app/ajax/getMessage.php", 
            {
                id_2: <?php echo json_encode($chatWith['user_id']); ?>
            },
            function(data, status){
                data = typeof data === 'string' ? JSON.parse(data) : data;
                if (data.success) {
                    let chatsHtml = '';
                    data.chats.forEach(chat => {
                        const messageClass = chat.from_id == <?php echo json_encode($_SESSION['user_id']); ?> ? 'rtext align-self-end' : 'ltext';
                        // Apply background color style to sender's messages
                        const backgroundColor = chat.from_id == <?php echo json_encode($_SESSION['user_id']); ?> ? 'background-color: #007bff; color: #fff;' : '';
                        chatsHtml += `<p class="${messageClass} border rounded p-2 mb-1" style="${backgroundColor}">
                            ${chat.message}
                            <small class="d-block">${chat.created_at}</small>
                        </p>`;
                    });
                    $("#chatBox").html(chatsHtml);
                    scrollDown();
                }
            });
    }

    // Fetch and display chat messages initially
    fetchData();

    // Set interval to continuously fetch and update chat messages
    setInterval(fetchData, 500);

</script>

</body>
</html>