<?php

session_start();

# Include necessary files
include '../../config.php';

# Function to get user by user_id
function getUserByUserId($user_id, $conn) {
    $sql = "SELECT username FROM user_form WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user['username'] : null;
}

# Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    # Check if the key is submitted
    if(isset($_POST['key'])){
       # Creating simple search algorithm :)
       $key = "%{$_POST['key']}%";

       $sql = "SELECT * FROM user_form
               WHERE user_id LIKE ? OR name LIKE ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("ss", $key, $key);
       $stmt->execute();

       $result = $stmt->get_result();

       if($result->num_rows > 0){ 
         $users = $result->fetch_all(MYSQLI_ASSOC);

         foreach ($users as $user) {
            if ($user['user_id'] == $_SESSION['user_id']) continue;
            // Get username if not directly available
            $username = isset($user['username']) ? $user['username'] : getUserByUserId($user['user_id'], $conn);
       ?>
       <li class="list-group-item">
            <a href="#" class="open-chat d-flex justify-content-between align-items-center p-2" data-username="<?=$username?>">
                <div class="d-flex align-items-center">
                    <img src="../uploads/<?=$user['p_p']?>" class="w-10 rounded-circle" style="width:40px;">
                    <h3 class="fs-xs m-2" style="font-size:15px; color:black;">
                        <?=$user['name']?>
                    </h3>                
                </div>
            </a>
       </li>
       <?php } } else { ?>
         <div class="alert alert-info text-center">
           <i class="fa fa-user-times d-block fs-big"></i>
           The user "<?=htmlspecialchars($_POST['key'])?>" is not found.
        </div>
    <?php }
    }

} else {
    header("Location: ../../index.php");
    exit;
}

?>



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
                <!-- Chat content will be loaded dynamically here -->
            </div>
        </div>
    </div>
</div>


<script>
  $(document).ready(function(){
    // Function to open the chat modal and load chat.php content
    function openChatModal(username) {
        $.ajax({
            url: '../chat.php',
            type: 'GET',
            data: { user: username },
            success: function(response) {
                $('#chatModalBody').html(response); // Update modal content
                $('#chatModal').modal('show'); // Show the modal
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Event listener for clicks on chat buttons (both for search results and existing conversations)
    $(document).on('click', '.open-chat', function(e) {
        e.preventDefault(); // Prevent default link behavior
        var username = $(this).data('username');
        openChatModal(username);
    });

    // Event listener for modal close event
    $('#chatModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove(); // Remove modal backdrop
        setTimeout(function() {
            location.reload(); // Refresh the page after a short delay
        }, 100); // 100 milliseconds delay
    });
});

</script>
