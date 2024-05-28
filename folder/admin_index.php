<?php
session_start();
include '../config/config.php';
require_once '../session_timeout.php';

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_name'])) {
  // Redirect to login page or display an error message
  header('Location: ../login');
  exit();
}

// Retrieve user's name from session
$adminName = $_SESSION['admin_name'];

// Function to check if the user has permission to access a file
function hasFileAccess($userId, $filePath)
{
  // Logic to determine file access based on user ID and file ownership
  // You may query your database or check file ownership here
  // Return true if the user has access, false otherwise
}

// Check if the user is authorized to access the file
if (isset($_POST['action']) && $_POST['action'] === 'fetch_files') {
  $folder_name = $_POST['folder_name'];
  $folder_path = "../../docuvault/folder/tmpFiles/{$folder_name}"; // Modify this path as per your server setup

  // Check if the user has access to the requested folder
  if (hasFileAccess($_SESSION['user_id'], $folder_path)) {
    // Proceed with fetching and displaying files
    // Your existing code to fetch and display files goes here
  } else {
    // Display an error message or redirect the user to a permission denied page
    echo "You are not authorized to access this folder.";
  }
}

// Fetch the count of unread messages for the current user from the `chats` table
$unreadChatsQuery = "SELECT COUNT(*) AS unread_count FROM chats WHERE to_id = ? AND opened = 0";
$unreadChatsStmt = mysqli_prepare($conn, $unreadChatsQuery);
mysqli_stmt_bind_param($unreadChatsStmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($unreadChatsStmt);
$unreadChatsResult = mysqli_stmt_get_result($unreadChatsStmt);
$unreadChatsRow = mysqli_fetch_assoc($unreadChatsResult);
$unreadCount = $unreadChatsRow['unread_count'] ?? 0;

// If unread count exceeds 9, display "9+"
$unreadCountDisplay = ($unreadCount > 9) ? "9+" : $unreadCount;

$currentPage = basename($_SERVER['PHP_SELF'], '.php'); // Gets the current page name without the '.php' extension
?>

<!DOCTYPE html>
<html>

<head>
  <title>FOLDER MANAGEMENT</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://kit.fontawesome.com/fe90e88d78.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../css/admin_pannel.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="icon" type="image/png" href="../images/favicons.png">
  <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/main.css">
</head>
<style>
    /* Dropdown menu styling */
    .dropdown-menu {
    
    max-height: 400px;
    overflow-y: auto;
}

/* Unread notification styling */
.unread-notification .dropdown-item {
    font-weight: bold;
    background-color: #f8f9fa;
}

/* Notification item styling */
.dropdown-item, .activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border: 1px solid #dee2e6; /* Add border */
    border-radius: 5px; /* Optional: add rounded corners */
    margin-bottom: 5px; /* Optional: space between notifications */
}

/* Timestamp styling */
.notification-timestamp, .activity-timestamp {
    font-size: 0.8em;
    color: #6c757d;
}

  .main {
    min-height: 100vh;
    width: 100%;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
    background-color: #fafbfe;
  }

  #sidebar {
    width: 70px;
    min-width: 80px;
    z-index: 1000;
    transition: all 0.3s ease-in-out;
    /* Adjusted to match the main transition */
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
  }

  #sidebar.expand {
    width: 260px;
    min-width: 260px;
  }

  .toggle-btn {
    background-color: transparent;
    cursor: pointer;
    border: 0;
    padding: 1rem 1.5rem;
  }

  .toggle-btn i {
    font-size: 1.5rem;
    color: #FFF;
  }

  .sidebar-logo {
    margin: auto 0;
  }

  .sidebar-logo a {
    color: #FFF;
    font-size: 1.15rem;
    font-weight: 600;
  }

  #sidebar:not(.expand) .sidebar-logo {
    display: block;
  }

  #sidebar:not(.expand) .sidebar-logo span {
    display: none;
  }

  #sidebar:not(.expand) a.sidebar-link span {
    display: none;
  }

  .sidebar-nav {
    padding: 2rem 0;
    flex: 1 1 auto;
    background-color: #fd8522;
  }

  a.sidebar-link {
    padding: .625rem 1.625rem;
    color: #FFF;
    display: block;
    font-size: 0.9rem;
    white-space: nowrap;
    border-left: 3px solid transparent;
  }

  .sidebar-footer {
    background-color: #fd8522;
  }

  .sidebar-footer span {
    color: #000;
    font-weight: bold;
  }

  .sidebar-link i {
    font-size: 1.1rem;
    margin-right: .75rem;
  }

  a.sidebar-link:hover {
    background-color: rgba(255, 255, 255, .075);
    border-left: 3px solid #f86300;
  }

  .sidebar-item {
    position: relative;
  }

  #sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
    position: absolute;
    top: 0;
    left: 70px;
    background-color: #0e2238;
    padding: 0;
    min-width: 15rem;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.3s ease, opacity 0.3s ease;

  }

  #sidebar:not(.expand) .sidebar-item:hover .has-dropdown+.sidebar-dropdown {
    max-height: 15em;
    opacity: 1;
  }

  #sidebar.expand .sidebar-link[data-bs-toggle="collapse"]::after {
    border: solid;
    border-width: 0 .075rem .075rem 0;
    content: "";
    display: inline-block;
    padding: 2px;
    position: absolute;
    right: 1.5rem;
    top: 1.4rem;
    transform: rotate(-135deg);
    transition: transform 0.3s ease-out;
  }

  #sidebar.expand .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
    transform: rotate(45deg);
    transition: transform 0.3s ease-out;
  }
</style>

<body>

  <div class="wrapper">
    <aside id="sidebar">
    <div class="d-flex" style="background-color:; border-bottom: 1px solid  #e4e4e4; margin-top:-9px;">
        <div class="sidebar-logo">
          <a href="../panels/admin_page.php">
          <img src="../images/logo.webp" alt="DOCU-VAULT Logo" style="width: 60px; height: auto; margin-left:-10px;">
          </a>
          <span style="color:black; width:60px; font-weight:600; font-size:20px;">DOCU-VAULT</span>
        </div>
      </div>

      <ul class="sidebar-nav">
        <li class="sidebar-item <?php echo $currentPage === 'documents' ? 'active' : ''; ?>">
          <a href="../panels/admin_documents" class="sidebar-link">
            <i class="fa-solid fa-file pe-2"></i>
            <span style="color:black; font-weight:bold;">Documents</span>
          </a>
        </li>
        <li class="sidebar-item">
           <span class="notification-badge"><?php echo $unreadCountDisplay; ?></span>
    <a class="sidebar-link" id="openChatModalLink">
        <i class="fa-solid fa-comment-dots pe-2"></i>
        <span style="color:black; font-weight:bold;">Chat</span>
        <span class="badge bg-danger" id="chatNotificationCount" style="display: none;"></span>
    </a>
</li>

       
        <li class="sidebar-item <?php echo $currentPage === 'user_management' ? 'active' : ''; ?>">
          <a href="../user_management/user_management" class="sidebar-link">
            <i class="fa-solid fa-user pe-2"></i>
            <span style="color:black; font-weight:bold;">User Management</span>
          </a>
        </li>

        

        <li class="sidebar-item <?php echo $currentPage === 'private_file_upload' ? 'active' : ''; ?>">
          <a href="../folder/admin_index" class="sidebar-link">
            <i class="fa-solid fa-folder pe-2"></i>
            <span style="color:black; font-weight:bold;">Private File Upload</span>
          </a>
        </li>

        <li class="sidebar-item <?php echo $currentPage === 'logs' ? 'active' : ''; ?>">
          <a href="../user_management/logs.php" class="sidebar-link">
            <i class="fa-solid fa-file-lines pe-2"></i>
            <span style="color:black; font-weight:bold;">Logs</span>
          </a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal"
          style="background-color:red;">
          <i class="lni lni-exit"></i>
          <span style="color:white;">Logout</span>
        </a>
      </div>
    </aside>

    <div class="main" style="background-color:#CED4DA;">
    <nav class="navbar navbar-expand px-3 border-bottom">
    <button class="toggle-btn" type="button">
        <i class="lni lni-menu" style="color:black;"></i>
    </button>
    <div class="navbar-collapse navbar">
        <ul class="navbar-nav">
            <!-- Notification bell -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="notificationDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="lni lni-alarm" style="color:black;"></i>
                    <!-- Notification badge -->
                    <span class="badge bg-danger" id="notificationBadge">0</span>
                </a>
                <!-- Notification dropdown menu -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notificationMenu">
                    <!-- Notification items will be appended here dynamically -->
                </ul>
            </li>
            <!-- Recent activity -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="activityDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="lni lni-recent" style="color:black;"></i>
                    <!-- Recent activity badge -->
                    <span class="badge bg-info" id="activityBadge">0</span>
                    <span>Activity</span> <!-- Added word "Activity" -->
                </a>
                <!-- Activity dropdown menu -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="activityDropdown" id="activityMenu">
                    <!-- Recent activity items will be appended here dynamically -->
                </ul>
            </li>
            <!-- Profile dropdown -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div style="display: inline-block;">
                        <img src="../css/profile/profile.jpg" class="avatar img-fluid rounded" alt="">
                        <span style="font-style:Arial; font-weight:bold;">Welcome Admin</span>
                        <!-- Welcome message -->
                    </div>
                </a>
                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#profileModal">Profile</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#settingsModal">Settings</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
      <!-- Modal to display generated PIN -->
      <div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="pinModalLabel">Generated PIN</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>The PIN generated for registration is: <?php echo $pin; ?></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Logout Modal -->
      <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color:white; color:black;">
              <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to logout?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <a href="../main/logout" class="btn btn-danger">Logout</a>
            </div>
          </div>
        </div>
      </div>
    
</body>


<div class="container">
  <h2 align="center" style="margin-top:20px;">Folder Management</a></h2>
  <br />
  <div align="right" style="margin-right:-172px;">
    <button type="button" name="create_folder" id="create_folder" class="btn"
      style="background-color: #D59D80;">Create</button>
  </div>
  <br />
</div>
<div class="table-responsive" id="folder_table">
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>

</html>

<div id="folderModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:white;">
        <h4 class="modal-title"><span id="change_title" style="color:black;">Create Folder</span></h4>
      </div>
      <div class="modal-body">
        <p>Enter Folder Name
          <input type="text" name="folder_name" id="folder_name" class="form-control" />
        </p>
        <br />
        <input type="hidden" name="action" id="action" />
        <input type="hidden" name="old_name" id="old_name" />
        <input type="button" name="folder_button" id="folder_button" class="btn btn-primary" value="Create" />


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Upload Modal -->
<div id="uploadModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:white;">
        <h4 class="modal-title" style="color:black;">Upload File</h4>
      </div>
      <div class="modal-body">
        <form method="post" id="upload_form" enctype='multipart/form-data'>
          <label for="uploadFile" class="form-label">File (Max: 25mb)</label>
          <p>
            <input type="file" name="upload_file" id="upload_file" accept=".xlsx, .pdf, .docx, .doc, .ppt, .pptx, .txt, .zip" />
          </p>
          <div id="file_size_error" style="color: red; display: none;">File size exceeds the maximum limit of 25MB.</div>
          <div id="file_type_error" style="color: red; display: none;">Unsupported file type. Please upload a valid file.</div>
          <br />
          <input type="hidden" name="hidden_folder_name" id="hidden_folder_name" />
          <input type="submit" name="upload_button" id="upload_button" class="btn btn-info"
            style="background-color:#E8BCB9; border-style:none; margin-bottom:10px;" value="Upload" disabled />
        </form>
        <div class="progress" style="display:none;">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="filelistModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color:white;">
      </div>
      <div class="modal-body" id="file_list">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color:white;">
        <h5 class="modal-title" id="profileModalLabel" style="color:black;">Profile</h5>
      </div>
      <div class="modal-body">
        <!-- Profile Update Form -->
        <form id="profileUpdateForm">
          <div class="mb-3">
            <label for="updateFullName" class="form-label">Full Name</label>
            <input required id="updateFullName" name="updateFullName" type="text" class="form-control"
              placeholder="Enter full name">
          </div>
          <div class="mb-3">
            <label for="updateEmail" class="form-label">Email</label>
            <input required id="updateEmail" name="updateEmail" type="email" class="form-control"
              placeholder="Enter email">
          </div>
          <button type="button" class="btn " style="background-color:#D59D80;" id="updateProfileButton">Update
            Profile</button>
        </form>

        <!-- Display Current Profile Information -->
        <div class="mt-4">
          <h6>Your Current Profile Information:</h6>
          <p><strong>Full Name:</strong> <span id="currentFullName"></span></p>
          <p><strong>Email:</strong> <span id="currentEmail"></span></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: white;">
        <h5 class="modal-title" id="settingsModalLabel" style="color:black;">Settings</h5>
      </div>
      <div class="modal-body">
        <!-- Form for changing password -->
        <form id="changePasswordForm">
          <!-- Removed the current password field -->
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <div class="input-group">
              <input required id="newPassword" name="newPassword" type="password" class="form-control"
                placeholder="Enter new password">
              <div class="input-group-append">
                <span class="input-group-text toggle-password" id="toggleNewPassword">
                  <img src="../images/open_eye.svg" alt="Open Eye" class="eye-icon" id="openEyeNewPassword">
                  <img src="../images/close_eye.svg" alt="Close Eye" class="eye-icon" id="closeEyeNewPassword">
                </span>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <div class="input-group">
              <input required id="confirmPassword" name="confirmPassword" type="password" class="form-control"
                placeholder="Confirm new password">
              <div class="input-group-append">
                <span class="input-group-text toggle-password" id="toggleConfirmPassword">
                  <img src="../images/open_eye.svg" alt="Open Eye" class="eye-icon" id="openEyeConfirmPassword">
                  <img src="../images/close_eye.svg" alt="Close Eye" class="eye-icon" id="closeEyeConfirmPassword">
                </span>
              </div>
            </div>
          </div>
          <button type="button" class="btn" style="background-color:#D59D80;" id="changePasswordButton">Change
            Password</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {

    load_folder_list();

    function load_folder_list() {
      var action = "fetch";
      $.ajax({
        url: "action.php",
        method: "POST",
        data: { action: action },
        success: function (data) {
          $('#folder_table').html(data);
        }
      });
    }

    $(document).on('click', '#create_folder', function () {
      $('#action').val("create");
      $('#folder_name').val('');
      $('#folder_button').val('Create');
      $('#folderModal').modal('show');
      $('#old_name').val('');
      $('#change_title').text("Create Folder");
    });

    $(document).on('click', '#folder_button', function () {
      var folder_name = $('#folder_name').val();
      var old_name = $('#old_name').val();
      var action = $('#action').val();
      if (folder_name != '') {
        $.ajax({
          url: "action.php",
          method: "POST",
          data: { folder_name: folder_name, old_name: old_name, action: action },
          success: function (data) {
            $('#folderModal').modal('hide');
            load_folder_list();
            alert(data);
          }
        });
      }
      else {
        alert("Enter Folder Name");
      }
    });

    $(document).on("click", ".update", function () {
      var folder_name = $(this).data("name");
      $('#old_name').val(folder_name);
      $('#folder_name').val(folder_name);
      $('#action').val("change");
      $('#folderModal').modal("show");
      $('#folder_button').val('Update');
      $('#change_title').text("Change Folder Name");
    });

    $(document).on("click", ".delete", function () {
      var folder_name = $(this).data("name");
      var action = "delete";
      if (confirm("Are you sure you want to remove it?")) {
        $.ajax({
          url: "action.php",
          method: "POST",
          data: { folder_name: folder_name, action: action },
          success: function (data) {
            load_folder_list();
            alert(data);
          }
        });
      }
    });
//upload file
    $(document).on('click', '.upload', function () {
    var folder_name = $(this).data("name");
    $('#hidden_folder_name').val(folder_name);
    $('#uploadModal').modal('show');
});

$('#upload_form').on('submit', function (e) {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    var formData = new FormData(this);
    var progressBar = $('.progress-bar');
    var progressContainer = $('.progress');

    $.ajax({
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable) {
                    var percentComplete = Math.round((event.loaded / event.total) * 100);
                    progressBar.width(percentComplete + '%');
                    progressBar.attr('aria-valuenow', percentComplete);
                    progressBar.text(percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        url: "upload.php",
        method: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
            progressContainer.show();
            progressBar.width('0%');
            progressBar.attr('aria-valuenow', 0);
            progressBar.text('0%');
        },
        success: function (data) {
            load_folder_list();
            alert(data);
            progressContainer.hide();
        },
        error: function() {
            alert('There was an error uploading the file');
            progressContainer.hide();
        }
    });
});
      return false; // Prevent form submission
    });

    //view file
    $(document).on('click', '.view_files', function () {
      var folder_name = $(this).data("name");
      var action = "fetch_files";
      $.ajax({
        url: "action.php",
        method: "POST",
        data: { action: action, folder_name: folder_name },
        success: function (data) {
          $('#file_list').html(data);
          $('#filelistModal').modal('show');
        }
      });
    });

    //remove file
    $(document).on('click', '.remove_file', function () {
      var path = $(this).attr("id");
      var action = "remove_file";
      if (confirm("Are you sure you want to remove this file?")) {
        $.ajax({
          url: "action.php",
          method: "POST",
          data: { path: path, action: action },
          success: function (data) {
            alert(data);
            $('#filelistModal').modal('hide');
           
          }
        });
      }
    });

    //change file
    $(document).on('blur', '.change_file_name', function () {
      var folder_name = $(this).data("folder_name");
      var old_file_name = $(this).data("file_name");
      var new_file_name = $(this).text();
      var action = "change_file_name";
      $.ajax({
        url: "action.php",
        method: "POST",
        data: { folder_name: folder_name, old_file_name: old_file_name, new_file_name: new_file_name, action: action },
        success: function (data) {
          alert(data);
        }
      });
    });

    // Function to handle clicking on file names
    $(document).on('click', '.file_container', function (e) {
      e.preventDefault(); // Prevent default link behavior

      var file_path = $(this).data("path");
      var file_name = $(this).text(); // Get the file name from the text content of the container

      // Check if file_name is defined and not empty
      if (file_name && file_name.trim() !== '') {
        var extension = file_name.split('.').pop().toLowerCase(); // Get file extension

        // Create a container for the options
        var optionsContainer = $('<div class="file_options"></div>');

        // Button to view
        var viewButton = $('<button class="btn btn-primary view_button">View</button>');
        viewButton.on('click', function () {
          // Check file extension to determine action
          switch (extension) {
            case 'pdf':
              // If PDF, open in default PDF viewer
              window.open(file_path, '_blank');
              break;
            case 'doc':
            case 'docx':
              // For .docx files, open in Word
              window.location.href = 'ms-word:ofe|u|' + encodeURIComponent(file_path);
              break;
            case 'xls':
            case 'xlsx':
              // If Excel document, open in Excel
              window.open('ms-excel:ofe|u|' + encodeURIComponent(file_path));
              break;
            case 'ppt':
            case 'pptx':
              // If PowerPoint document, open in PowerPoint
              window.open('ms-powerpoint:ofe|u|' + encodeURIComponent(file_path));
              break;
            default:
              // For other file types, display a message or handle accordingly
              alert('Cannot open this file type directly. Please download it.');
          }
        });

        // Button to download
        var downloadButton = $('<a class="btn btn-success download_button" href="' + file_path + '" download="' + file_name + '">Download</a>');

        // Add buttons to container
        optionsContainer.append(viewButton);
        optionsContainer.append(downloadButton);

        // Display options
        $(this).after(optionsContainer);
      } else {
        // Handle case where file name is not available
        alert('File name is not available.');
      }
    });



  document.getElementById('upload_form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent normal form submission

    var fileInput = document.getElementById('upload_file');
    var file = fileInput.files[0];
    var formData = new FormData();
    formData.append('upload_file', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php', true);

    // Progress event listener
    xhr.upload.addEventListener('progress', function (event) {
      if (event.lengthComputable) {
        var percentComplete = (event.loaded / event.total) * 100;
        var progressBar = document.querySelector('.progress-bar');
        progressBar.style.width = percentComplete + '%';
        progressBar.innerHTML = percentComplete.toFixed(2) + '%';
      }
    });

    // On successful upload
    xhr.onload = function () {
      if (xhr.status === 200) {
        // Handle successful upload
        console.log('Upload successful');
        // Hide progress bar after successful upload
        document.querySelector('.progress').style.display = 'none';
      } else {
        // Handle upload error
        console.error('Upload failed');
      }
    };

    // Send form data
    xhr.send(formData);

    // Show progress bar
    document.querySelector('.progress').style.display = 'block';
  });


 
  $(document).ready(function() {
    // Function to fetch initial counts and display notifications and recent activities
    function fetchInitialCountsAndData() {
        // Fetch initial counts from session variables
        $.ajax({
            url: 'get_counts.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#notificationBadge').text(response.notification_count);
                $('#activityBadge').text(response.activity_count);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching counts:', error);
            }
        });

        // Fetch and display notifications
        fetchNotifications();

        // Fetch and display recent activities
        fetchRecentActivities();
    }

    // Attach click event listener to the activity dropdown
    $('#activityDropdown').click(function() {
        // Reset activity count to zero in the UI
        $('#activityBadge').text('0');

        // Fetch recent activities when the dropdown is clicked
        fetchRecentActivities();

        // Optionally, you can mark activities as "read" in the database here (AJAX request)
        // This part depends on your application logic
        // Mark notifications as "read" in the database (AJAX request)
        $.ajax({
            url: 'mark_activities_as_read.php',
            method: 'POST',
            success: function(response) {
                console.log('Activities marked as read');
            },
            error: function(xhr, status, error) {
                console.error('Error marking activities as read:', error);
                // Handle error (e.g., display an error message)
            }
        });
    });

    // Attach click event listener to the notification bell icon
    $('#notificationDropdown').click(function() {
        // Reset notification count to zero in the UI
        $('#notificationBadge').text('0');

        // Fetch and display notifications when the bell icon is clicked
        fetchNotifications();

        // Mark notifications as "read" in the database (AJAX request)
        $.ajax({
            url: 'mark_notifications_as_read.php',
            method: 'POST',
            success: function(response) {
                console.log('Notifications marked as read');
            },
            error: function(xhr, status, error) {
                console.error('Error marking notifications as read:', error);
                // Handle error (e.g., display an error message)
            }
        });
    });

    // Fetch initial counts and data when the document is ready
    fetchInitialCountsAndData();
});

function fetchNotifications() {
    $.ajax({
        url: 'fetch_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var notifications = data.notifications;
            var unreadCount = notifications.filter(notification => notification.status === 'unread').length;
            $('#notificationBadge').text(unreadCount);

            $('#notificationMenu').empty();

            notifications.forEach(function(notification) {
                var timestamp = new Date(notification.created_at);
                var formattedTimestamp = timestamp.toLocaleString();

                var notificationLink = $('<a>')
                    .addClass('dropdown-item')
                    .attr('href', '')
                    .text(notification.description);

                var timestampSpan = $('<span>')
                    .addClass('notification-timestamp')
                    .text(formattedTimestamp);

                notificationLink.append(timestampSpan);

                var listItem = $('<li>').append(notificationLink);

                if (notification.status === 'unread') {
                    listItem.addClass('unread-notification');
                }

                $('#notificationMenu').append(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching notifications:', error);
        }
    });
}

function fetchRecentActivities() {
    $.ajax({
        url: 'fetch_data.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var activities = data.recentActivities;

            // Update the activity badge count
            $('#activityBadge').text(activities.length);

            $('#activityMenu').empty();

            activities.forEach(function(activity) {
                var timestamp = new Date(activity.timestamp);
                var formattedTimestamp = timestamp.toLocaleString();
            
                var activityLink = $('<a>')
                    .addClass('dropdown-item activity-item')
                    .attr('href', '#')
                    .text(activity.description);
            
                var timestampSpan = $('<span>')
                    .addClass('activity-timestamp')
                    .text(formattedTimestamp);
            
                activityLink.append(timestampSpan);
            
                var listItem = $('<li>').append(activityLink);
            
                if (activity.status === 'unread') {
                    listItem.addClass('unread-activity');
                }
            
                $('#activityMenu').append(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching recent activities:', error);
        }
    });
}

  const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});

$(document).ready(function() {
    // Event listener for the button to open the chat modal
    $('#openChatModalLink').click(function() {
        // Send AJAX request to home.php to open the chat modal
        $.ajax({
            url: '../home.php', // URL of the home.php file
            type: 'GET', // HTTP request type
            success: function(response) {
                // Insert the response (which includes the chat modal) into the DOM
                $('body').append(response);
                // Show the chat modal
                $('#chatModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Log any errors to the console
            }
        });
    });
});


$(document).ready(function(){
        // Other scripts...

        // Function to reload the page after closing the modal
        $('#chatModal').on('hidden.bs.modal', function () {
            location.reload(); // Reload the page
        });
    });
 //UPDATE PROFILE
 $(document).ready(function () {
            // Fetch and display the user's current profile information
            fetchCurrentProfile();
    
            $('#updateProfileButton').on('click', function () {
                console.log("Update Profile Button clicked");
    
                var fullName = $('#updateFullName').val();
                var email = $('#updateEmail').val();
    
                // Perform validation (you can add more validation as needed)
                if (!fullName || !email) {
                    alert("Please enter both full name and email");
                    return;
                }
    
                // AJAX request to update profile
                $.ajax({
                    url: "update_profile.php", // Replace with your actual backend endpoint
                    type: "POST",
                    data: {
                        fullName: fullName,
                        email: email
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            alert("Profile updated successfully");
                            // Fetch and display the updated profile information
                            fetchCurrentProfile();
                            $('#profileModal').modal('hide'); // Close the modal on success
                        } else {
                            alert("Error updating profile: " + response.message);
                        }
                    },
                    error: function (error) {
                        alert("An error occurred: " + error.responseText);
                    }
                });
            });
    
            // Function to fetch and display the user's current profile information
            function fetchCurrentProfile() {
                // AJAX request to fetch profile information
                $.ajax({
                    url: "fetch_profile.php", // Replace with your actual backend endpoint
                    type: "GET",
                    dataType: "json",
                    success: function (profile) {
                        // Display current profile information
                        $('#currentFullName').text(profile.fullName);
                        $('#currentEmail').text(profile.email);
                    },
                    error: function (error) {
                        console.log("Error fetching profile: " + error.responseText);
                    }
                });
            }
        });
        
         //CHANGE PASSWORD
    $(document).ready(function () {
        $('#changePasswordButton').on('click', function () {
            console.log("Button clicked");
    
            var newPassword = $('#newPassword').val();
            var confirmPassword = $('#confirmPassword').val();
    
            // Perform validation
            if (newPassword !== confirmPassword) {
                alert("New password and confirm password do not match");
                return;
            }
    
            // Additional validation: Check for at least one uppercase letter and one special character
            var uppercaseRegex = /[A-Z]/;
            var specialCharacterRegex = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/;
    
            if (!uppercaseRegex.test(newPassword) || !specialCharacterRegex.test(newPassword)) {
                alert("Password must contain at least one uppercase letter and one special character");
                return;
            }
    
            // AJAX request to update password
            $.ajax({
                url: "update_password.php", // Replace with your actual backend endpoint
                type: "POST",
                data: {
                    newPassword: newPassword
                },
                dataType: "json", // Expect JSON response from the server
                success: function (response) {
                    // Handle the response from the server
                    if (response.status === "success") {
                        alert("Password changed successfully");
                        $('#settingsModal').modal('hide'); // Close the modal on success
                    } else {
                        alert("Error changing password: " + response.message);
                    }
                },
                error: function (error) {
                    alert("An error occurred: " + error.responseText);
                }
            });
        });
    });
    
    
        //open and close eye for password 
        document.addEventListener('DOMContentLoaded', function () {
        // Function to toggle password visibility
        function togglePasswordVisibility(inputId, openEyeId, closeEyeId) {
            const passwordInput = document.getElementById(inputId);
            const openEye = document.getElementById(openEyeId);
            const closeEye = document.getElementById(closeEyeId);
    
            openEye.addEventListener('click', function () {
                passwordInput.type = 'text';
                openEye.style.display = 'none';
                closeEye.style.display = 'block';
            });
    
            closeEye.addEventListener('click', function () {
                passwordInput.type = 'password';
                openEye.style.display = 'block';
                closeEye.style.display = 'none';
            });
    
            // Set the initial state to show closed eyes
            passwordInput.type = 'password';
            openEye.style.display = 'block';
            closeEye.style.display = 'none';
        }
    
        // Toggle password visibility for new password
        togglePasswordVisibility('newPassword', 'closeEyeNewPassword', 'openEyeNewPassword');
    
        // Toggle password visibility for confirm password
        togglePasswordVisibility('confirmPassword', 'closeEyeConfirmPassword', 'openEyeConfirmPassword');
    });

    document.addEventListener('DOMContentLoaded', function() {
    const uploadFileInput = document.getElementById('upload_file');
    const uploadButton = document.getElementById('upload_button');
    const fileSizeError = document.getElementById('file_size_error');
    const fileTypeError = document.getElementById('file_type_error');
    const maxFileSize = 25 * 1024 * 1024; // 25MB in bytes
    const supportedFileTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'application/zip'
    ];

    uploadFileInput.addEventListener('change', function() {
        const file = uploadFileInput.files[0];
        if (file) {
            let fileSizeValid = file.size <= maxFileSize;
            let fileTypeValid = supportedFileTypes.includes(file.type);

            if (!fileSizeValid) {
                fileSizeError.style.display = 'block';
            } else {
                fileSizeError.style.display = 'none';
            }

            if (!fileTypeValid) {
                fileTypeError.style.display = 'block';
            } else {
                fileTypeError.style.display = 'none';
            }

            uploadButton.disabled = !fileSizeValid || !fileTypeValid;
        } else {
            fileSizeError.style.display = 'none';
            fileTypeError.style.display = 'none';
            uploadButton.disabled = true;
        }
    });
});

</script>
