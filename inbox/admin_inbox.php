<?php
@include '../config/config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or display an error message
    header('Location: ../login.php');
    exit();
}

// Fetch the count of unread messages for the current user
$unreadMessagesQuery = "SELECT COUNT(*) AS unread_count FROM user_messages WHERE recipient_id = ? AND is_read = 0";
$unreadMessagesStmt = $conn->prepare($unreadMessagesQuery);
$unreadMessagesStmt->bind_param("i", $_SESSION['user_id']);
$unreadMessagesStmt->execute();
$unreadMessagesResult = $unreadMessagesStmt->get_result();
$unreadMessagesRow = $unreadMessagesResult->fetch_assoc();
$unreadCount = $unreadMessagesRow['unread_count'];

// Inbox query to fetch unread messages
$messagesPerPage = 5; // Adjusted to 5 messages per page
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $messagesPerPage;

$inboxQuery = "SELECT message_id, sender_id, message_type, message_content, file_path, created_at FROM user_messages WHERE recipient_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT ?, ?";
$inboxStmt = $conn->prepare($inboxQuery);
$inboxStmt->bind_param("iii", $_SESSION['user_id'], $offset, $messagesPerPage);
$inboxStmt->execute();
$inboxResult = $inboxStmt->get_result();

// Update is_read column for the specific message
$updateIsReadQuery = "UPDATE user_messages SET is_read = 1 WHERE message_id = ?";
$updateIsReadStmt = $conn->prepare($updateIsReadQuery);

while ($messageRow = $inboxResult->fetch_assoc()) {
    // Update is_read for each message
    $messageId = $messageRow['message_id'];
    $updateIsReadStmt->bind_param("i", $messageId);
    $updateIsReadStmt->execute();
}

    //SUCCESS MESSAGE WHEN SUCCESFULL REPLY TO THE MESSAGE!!!!
       // Check for reply success query parameter
       if (isset($_GET['reply_success']) && $_GET['reply_success'] == 1) {
        echo '<div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your reply has been sent successfully.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    
        // JavaScript to close the alert after 2 seconds
        echo '<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>';
        echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>';
        echo '<script>
                $(document).ready(function(){
                    setTimeout(function(){
                        $("#successAlert").alert("close");
                    }, 2000);
                });
              </script>';
    }
    ?>
    
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" href="../images/favicons.png">
            <title>Inbox</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <link rel="stylesheet" href="../css/inbox.css">
            <script>
            function loadMessages(page) {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById("inbox-container").innerHTML = xhr.responseText;
                        addClickHandlers();
                    }
                };
                xhr.open("GET", "inbox.php?page=" + page, true);
                xhr.send();
            }
    
            // Refresh messages every 30 seconds (adjust as needed)
            setInterval(function () {
                loadMessages(1);
            }, 30000); // 30 seconds
    
            function openMessage(messageContainer, senderName, messageType, messageContent, filePath) {
                $('#messageModal').modal('show');
                document.getElementById('modalSender').innerText = 'From: ' + senderName;
                document.getElementById('modalType').innerText = 'Type: ' + messageType;
                document.getElementById('modalContent').innerText = 'Content: ' + messageContent;
    
                if (messageType === 'file') {
                    document.getElementById('modalFileLink').innerHTML = '<a href="' + filePath + '" target="_blank">Download File</a>';
                } else {
                    document.getElementById('modalFileLink').innerHTML = '';
                }
    
                // Add a reply button to open the reply modal
                var replyButton = document.createElement('button');
                replyButton.className = 'btn btn-primary';
                replyButton.innerText = 'Reply';
                replyButton.onclick = function () {
                    openReplyModal(senderName);
                };
    
                // Append the reply button to the modal footer
                var modalFooter = document.querySelector('#messageModal .modal-footer');
                modalFooter.innerHTML = ''; // Clear existing content
                modalFooter.appendChild(replyButton);
    
                // Display the sent time in the modal
                var modalTimeValue = document.getElementById('modalTimeValue');
                modalTimeValue.innerText = messageContainer.getAttribute('data-time');
            }
    
            function addClickHandlers() {
                var messageContainers = document.getElementsByClassName('message-container');
                for (var i = 0; i < messageContainers.length; i++) {
                    messageContainers[i].setAttribute('onclick', 'openMessage(this,' +
                        JSON.stringify(messageContainers[i].getAttribute('data-sender')) + ',' +
                        JSON.stringify(messageContainers[i].getAttribute('data-type')) + ',' +
                        JSON.stringify(messageContainers[i].getAttribute('data-content')) + ',' +
                        JSON.stringify(messageContainers[i].getAttribute('data-file')) +
                        ')');
                }
            }
    
            // Initial load
            document.addEventListener("DOMContentLoaded", function () {
                loadMessages(1);
            });
    
            //FUNCTION IN REPLYING THE MESSAGE!!
            function replyToSender() {
            // Extract sender information from the modal
            var senderName = document.getElementById('modalSender').innerText.replace('From: ', '');
            var messageType = document.getElementById('modalType').innerText.replace('Type: ', '');
            var messageContent = document.getElementById('modalContent').innerText.replace('Content: ', '');
            var filePath = document.getElementById('modalFileLink').innerText;
    
            // Redirect to the reply page with the sender information
            window.location.href = 'reply_page.php?sender=' + encodeURIComponent(senderName) +
                '&type=' + encodeURIComponent(messageType) +
                '&content=' + encodeURIComponent(messageContent) +
                '&file=' + encodeURIComponent(filePath);
        }
    
        function openReplyModal(sender) {
            // Set the recipient value in the reply modal form
            document.getElementById('recipientInput').value = sender;
            // Show the reply modal
            $('#replyModal').modal('show');
        }
    
            function goBack(page) {
            if (page === 'user_page') {
                window.location.href = 'user_page.php'; // Navigate back to user_page.php
            } else if (page === 'admin_page') {
                window.location.href = 'admin_page.php'; // Navigate back to admin_page.php
            } else {
                // Default behavior: navigate back one step in history
                window.history.back();
            }
        }
    
        </script>
    
        </head>
        <div class="inbox-container" id="inbox-container">
            <a class="back-button" href="javascript:history.go(-1);">
                Back </a>
            <?php
            // Function to get sender's name based on sender_id
            function getSenderName($senderId, $conn)
            {
                $query = "SELECT name FROM user_form WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $senderId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
    
                if ($result && $row = mysqli_fetch_assoc($result)) {
                    return $row['name'];
                }
    
                return "Unknown Sender";
            }
    
            // Placeholder values for total messages and messages per page
            $messagesPerPage = 5; // Adjusted to 5 messages per page
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($currentPage - 1) * $messagesPerPage;
    
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                // Retrieve the user_id and user_type from the session
                $thisUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                $userType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
    
                $inboxQuery = "SELECT message_id, sender_id, message_type, message_content, file_path, created_at FROM user_messages WHERE recipient_id = ? OR recipient_id IS NULL ORDER BY created_at DESC LIMIT ?, ?";
    
                $inboxStmt = $conn->prepare($inboxQuery);
                $inboxStmt->bind_param("iii", $thisUserId, $offset, $messagesPerPage);
                $inboxStmt->execute();
                $inboxResult = $inboxStmt->get_result();
    
                // Output inbox HTML instead of echoing directly
                $inboxHTML = '';
    
                while ($messageRow = $inboxResult->fetch_assoc()) {
                    // Display each message
                    $inboxHTML .= '<div class="message-container" data-sender="' . $messageRow['sender_id'] . '" data-type="' . $messageRow['message_type'] . '" data-content="' . $messageRow['message_content'] . '" data-file="' . $messageRow['file_path'] . '" data-time="' . $messageRow['created_at'] . '">';
                    $senderName = getSenderName($messageRow['sender_id'], $conn);
                    $inboxHTML .= '<div class="message-header">From: ' . $senderName . '</div>';
                    $inboxHTML .= '<div class="message-body">';
                    $inboxHTML .= '<div class="message-type">Type: ' . $messageRow['message_type'] . '</div>';
                    $inboxHTML .= '<div class="message-content">Content: ' . $messageRow['message_content'] . '</div>';
                    $inboxHTML .= '<div class="message-time">Receive Time: ' . $messageRow['created_at'] . '</div>';
    
                    if ($messageRow['message_type'] === 'file') {
                        $inboxHTML .= '<a class="file-link" href="' . $messageRow['file_path'] . '" target="_blank">Download File</a>';
                    }
    
                    $inboxHTML .= '</div>';
                    $inboxHTML .= '</div>';
                    $inboxHTML .= '<hr>';
                }
    
                // Pagination links
                $totalMessagesQuery = "SELECT COUNT(*) as total FROM user_messages WHERE recipient_id = ? OR recipient_id IS NULL";
                $totalMessagesStmt = $conn->prepare($totalMessagesQuery);
                $totalMessagesStmt->bind_param("i", $thisUserId);
                $totalMessagesStmt->execute();
                $totalMessagesResult = $totalMessagesStmt->get_result();
                $totalMessagesRow = $totalMessagesResult->fetch_assoc();
                $totalMessages = $totalMessagesRow['total'];
                $totalPages = ceil($totalMessages / $messagesPerPage);
                
                $inboxHTML .= '<div class="pagination">';
                for ($i = 1; $i <= $totalPages; $i++) {
                    $inboxHTML .= '<a href="inbox.php?page=' . $i . '">' . $i . '</a>';
                }
                $inboxHTML .= '</div>';
    
                echo $inboxHTML;
            }
            ?>
    
        </div>
    
        <!--View Content of Message -->
        <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="modalSender"></p>
                        <p id="modalType"></p>
                        <p id="modalContent"></p>
                        <p id="modalTime"><strong>Receive Time:</strong> <span id="modalTimeValue"></span></p>
                        <p id="modalFileLink"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!-- Add the Reply button here -->
                        <button type="button" class="btn btn-primary" onclick="replyToSender()">Reply</button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Reply Modal -->
    
        <!-- Bootstrap Modal for Reply -->
        <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replyModalLabel">Reply to Message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Reply form goes here -->
                        <form action="send_reply.php" method="post">
                            <input type="hidden" name="recipient" id="recipientInput" value="">
                            <div class="mb-3">
                                <label for="replyContent" class="form-label">Reply Content:</label>
                                <textarea class="form-control" name="replyContent" id="replyContent" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        </body>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </html>
    