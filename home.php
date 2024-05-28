<?php
session_start();
include("app/helpers/user.php");
include("app/helpers/conversations.php");
include("app/helpers/last_chat.php");
include 'app/helpers/timeAgo.php';

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit(); // Stop further execution
}

// Include the configuration file
include("config.php");

// Fetch user data
$user = getUser($_SESSION['user_id'], $conn);

// Check if user data is retrieved successfully
if (!$user || !isset($user['user_id'])) {
    // Handle the case where user data cannot be retrieved
    echo "Error retrieving user data. Please try again later.";
    exit(); // Stop further execution
}

// Fetch user conversations
$conversations = getConversation($user['user_id'], $conn);

// Fetch user type
$userType = getUserType($user['user_id'], $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat- Home</title>
   
    <link rel="icon" href="../img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:white;">
                <h5 class="modal-title" id="chatModalLabel" style="color:black;">Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="chatModalBody">
                <!-- Placeholder for dynamic content -->
                <div>
                    <div class="d-flex mb-3 p-3 bg-light justify-content-between align-items-center" style="font-size:10px;">
                        <div class="d-flex align-items-center" style="width:50px;">
                            <img src="../uploads/<?=$user['p_p']?>" class="w-20 rounded-circle" style="width:50px;">
                            <h3 class="fs-xs m-2" style="width:50px; font-size:20px"><?=$user['name']?></h3>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" placeholder="Search..." id="searchText" class="form-control">
                        <button class="btn btn-primary" id="searchBtn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                    <ul id="chatList" class="list-group mvh-30 overflow-auto" style="font-size: 13px;">
                        <?php if (!empty($conversations)) { ?>
                            <?php foreach ($conversations as $conversation){ ?>
                                <li class="list-group-item" style="10px;">
                                    <button class="d-flex justify-content-between align-items-center p-1 open-chat" data-username="<?=$conversation['username']?>" style="width: 100%;">
                                        <div class="d-flex align-items-center"> 
                                            <img src="../uploads/<?=$conversation['p_p']?>" class="w-10 rounded-circle" style="width:50px;">
                                            <h3 class="fs-xs m-2" style="font-size:15px;">
                                                <?=$conversation['name']?><br>
                                                <small>
                                                    <?php 
                                                    echo lastChat($_SESSION['user_id'], $conversation['user_id'], $conn);
                                                    ?>
                                                </small>
                                            </h3>
                                        </div>
                                    </button>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-comments d-block fs-big"></i>
                                No messages yet, Start the conversation
                            </div>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Function to open the home modal -->
<script>
    function openHomeModal() {
        $('#chatModal').modal('hide'); // Hide the chat modal
        $('#chatModal').modal('show'); // Show the home modal
    }
</script>

<!-- Scripts -->
<script>
    $(document).ready(function(){
        // Search
        $("#searchText").on("input", function(){
            var searchText = $(this).val();
            if(searchText == "") return;
            $.post('../app/ajax/search.php', { key: searchText }, function(data, status){
                $("#chatList").html(data);
            });
        });

        // Search using the button
        $("#searchBtn").on("click", function(){
            var searchText = $("#searchText").val();
            if(searchText == "") return;
            $.post('../app/ajax/search.php', { key: searchText }, function(data, status){
                $("#chatList").html(data);
            });
        });

        // Auto update last seen for logged in user
        let lastSeenUpdate = function(){
            $.get("../app/ajax/update_last_seen.php");
        }
        lastSeenUpdate();
        setInterval(lastSeenUpdate, 10000);
    });

    // Function to go back to the previous page in history or admin/user page based on user role
    function goBack() {
        var userRole = "<?php echo $userType; ?>";
        if (userRole === 'admin') {
            window.location.href = './panels/admin_page.php';
        } else if (userRole === 'user') {
            window.location.href = './panels/user_page.php';
        } else {
            window.history.back();
        }
    }

    // Function to open the chat modal and load chat.php content
    function openChatModal(username) {
        $.ajax({
            url: '../chat.php',
            type: 'GET',
            data: { user: username },
            success: function(response) {
                $('#chatModalBody').html(response); // Update modal content
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Event listener for clicks on chat buttons
    $('.open-chat').click(function() {
        var username = $(this).data('username');
        openChatModal(username);
    });

    // Function to force the page to scroll to the top after the modal is closed
    $('#chatModal').on('hidden.bs.modal', function () {
        $('html, body').animate({scrollTop: 0}, 'slow'); // Scroll to top
    });

   

     // Refresh the page when the upload modal is closed
     var uploadModal = document.getElementById('uploadModal');
    uploadModal.addEventListener('hidden.bs.modal', function () {
        location.reload();
    });

    // Refresh the page when the chat modal is closed
    var chatModal = document.getElementById('chatModal');
    chatModal.addEventListener('hidden.bs.modal', function () {
        location.reload();
    });
</script>

</body>
</html>
