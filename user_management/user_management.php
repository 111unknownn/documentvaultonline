<?php
// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
$page_title = "User Management";
session_start();
include ('../includes/header.php');
@include '../config/config.php';

require_once '../session_timeout.php';

// Update last activity time
$_SESSION['last_activity'] = time();


// Number of records per page
$records_per_page = 10;

// Get the current page number
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $records_per_page;

// Fetch records from the database with LIMIT and OFFSET
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
$fetch_query = "SELECT * FROM user_form LIMIT $records_per_page OFFSET $offset";
$fetch_query_result = mysqli_query($conn, $fetch_query);

// Calculate total number of records
$total_records_query = "SELECT COUNT(*) AS total_records FROM user_form";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records = mysqli_fetch_assoc($total_records_result)['total_records'];

// Calculate total number of pages
$total_pages = ceil($total_records / $records_per_page);


// Check if the user is an admin to determine whether to show the delete button
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
    $show_delete_button = false; // Set to false for admins
} else {
    $show_delete_button = true; // Set to true for non-admins
}

$fetch_query = "SELECT user_id, name, email, user_type FROM user_form LIMIT $records_per_page OFFSET $offset";

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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/fe90e88d78.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/admin_pannel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/main.css">
    <title>User Management</title>
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
    .dropdown-item,
    .activity-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border: 1px solid #dee2e6;
        /* Add border */
        border-radius: 5px;
        /* Optional: add rounded corners */
        margin-bottom: 5px;
        /* Optional: space between notifications */
    }

    /* Timestamp styling */
    .notification-timestamp,
    .activity-timestamp {
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

<div class="wrapper">
    <aside id="sidebar">
        <div class="d-flex" style="background-color:; border-bottom: 1px solid  #e4e4e4; margin-top:-9px;">
            <div class="sidebar-logo">
                <a href="../panels/admin_page.php">
                    <img src="../images/logo.webp" alt="DOCU-VAULT Logo"
                        style="width: 60px; height: auto; margin-left:-10px;">
                </a>
                <span style="color:black; width:60px; font-weight:600; font-size:20px;">DOCU-VAULT</span>
            </div>
        </div>

        <ul class="sidebar-nav">
            <a href="../panels/admin_documents" class="sidebar-link">
                <i class="fa-solid fa-file pe-2"></i>
                <span style="color:black; font-weight:bold;">Documents</span>
            </a>
            </li>


            <li class="sidebar-item">
                <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fa-solid fa-upload pe-2"></i>
                    <span style="color:black; font-weight:bold;">Upload</span>
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

            <li class="sidebar-item">
                <a href="../user_management/user_management" class="sidebar-link">
                    <i class="fa-solid fa-user pe-2"></i>
                    <span style="color:black; font-weight:bold;">User Management</span>
                </a>
            </li>


            <li class="sidebar-item">
                <a href="../folder/admin_index" class="sidebar-link">
                    <i class="fa-solid fa-folder pe-2"></i>
                    <span style="color:black; font-weight:bold;">Private File Upload</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="../user_management/logs.php" class="sidebar-link">
                    <i class="fa-solid fa-file-lines pe-2"></i>
                    <span style="color:black; font-weight:bold;">Logs </span>
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

    <div class="main" style="background-color: #CED4DA;">
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
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                            id="notificationMenu">
                            <!-- Notification items will be appended here dynamically -->
                        </ul>
                    </li>
                    <!-- Recent activity -->
                    <li class="nav-item dropdown" style="300px;">
                        <a href="#" class="nav-link dropdown-toggle" id="activityDropdown" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="lni lni-recent" style="color:black;"></i>
                            <!-- Recent activity badge -->
                            <span class="badge bg-info" id="activityBadge">0</span>
                            <span>Activity</span> <!-- Added word "Activity" -->
                        </a>
                        <!-- Activity dropdown menu -->
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="activityDropdown"
                            id="activityMenu">
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

        <!-- Profile Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h5 class="modal-title" id="profileModalLabel">Profile</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Profile Update Form -->
                        <form id="profileUpdateForm">
                            <div class="mb-3">
                                <label for="updateFullName" class="form-label">Full Name</label>
                                <input required id="updateFullName" name="updateFullName" type="text"
                                    class="form-control" placeholder="Enter full name">
                            </div>
                            <div class="mb-3">
                                <label for="updateEmail" class="form-label">Email</label>
                                <input required id="updateEmail" name="updateEmail" type="email" class="form-control"
                                    placeholder="Enter email">
                            </div>
                            <button type="button" class="btn " style="background-color:#D59D80;"
                                id="updateProfileButton">Update
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
                                    <input required id="newPassword" name="newPassword" type="password"
                                        class="form-control" placeholder="Enter new password">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" id="toggleNewPassword">
                                            <img src="../images/open_eye.svg" alt="Open Eye" class="eye-icon"
                                                id="openEyeNewPassword">
                                            <img src="../images/close_eye.svg" alt="Close Eye" class="eye-icon"
                                                id="closeEyeNewPassword">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New
                                    Password</label>
                                <div class="input-group">
                                    <input required id="confirmPassword" name="confirmPassword" type="password"
                                        class="form-control" placeholder="Confirm new password">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" id="toggleConfirmPassword">
                                            <img src="../images/open_eye.svg" alt="Open Eye" class="eye-icon"
                                                id="openEyeConfirmPassword">
                                            <img src="../images/close_eye.svg" alt="Close Eye" class="eye-icon"
                                                id="closeEyeConfirmPassword">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn" style="background-color:#D59D80;"
                                id="changePasswordButton">Change Password</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


                  
                     <!-- Upload Modal Form -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white;">
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" class="shadow rounded p-3" enctype="multipart/form-data" method="POST"
                        action="main.php">
                        <h2 class="text-center">Upload File</h2>
                        <div class="mb-3">
                            <label for="uploadFile" class="form-label">File (Max: 25mb)</label>
                            <input required id="uploadFile" name="uploadFile" type="file"
                                accept=".xlsx, .docx, .doc, .ppt, .pptx, .txt, .zip, .pdf" class="form-control">
                            <div class="invalid-feedback" id="fileSizeError" style="display: none;">
                                File size exceeds the maximum limit of 25MB.
                            </div>
                            <div class="invalid-feedback" id="fileFormatError" style="display: none;">
                                Invalid file format. Accepted formats are .zip, .pptx, .pdf, .docx, .txt, and .xlsx.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="uploadTitle" class="form-label">Title</label>
                            <input required id="uploadTitle" name="uploadTitle" type="text" class="form-control"
                                placeholder="Enter title...">
                        </div>
                        <div class="mb-3">
                            <label for="uploadAuthor" class="form-label">Author</label>
                            <input required id="uploadAuthor" name="uploadAuthor" type="text" class="form-control"
                                placeholder="Enter author...">
                        </div>
                        <div class="mb-3">
                            <label for="uploadKeywords" class="form-label">Keywords (Searchable)</label>
                            <input required id="uploadKeywords" name="uploadKeywords" type="text" class="form-control"
                                placeholder="ex: (documents, .ppt)">
                        </div>
                        <div id="uploadVersionDiv" class="mb-3">
                            <label for="uploadVersion" class="form-label">Version</label>
                            <input id="uploadVersion" name="uploadVersion" type="text" class="form-control"
                                placeholder="ex: 1.0.0">
                        </div>
                        <footer class="d-flex align-items-center justify-content-between gap-2">
                            <div class="text-start">
                                <button type="button" class="btn btn-primary" id="uploadFileButton"
                                    style="background-color:darkorange; border-style:none; color:black;"
                                    disabled>Upload</button>
                            </div>
                            <p class="mb-0 d-none" id="uploadLabel">Uploading & Encrypting, please wait...</p>
                            <div class="progress d-none" style="width: 100%;">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" id="progressBar">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                            <div class="spinner-border text-success d-none" role="status" id="loadingSpinner">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </footer>
                    </form>
                </div>
                <div class="modal-footer"></div>
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
        <!--insert  Modal -->
        <div class="modal fade" id="insertdata" tabindex="-1" aria-labelledby="insertdataLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="insertdataLabel">INSERT DATA</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="../user_management/insert" method="POST">
                        <div class="modal-body">
                            <label for=>Name</label>
                            <div class="form-group mb-3">
                                <input type="name" class="form-control" name="name" required
                                    placeholder="Enter Your Name">
                            </div>
                            <label for=>Email Address</label>
                            <div class="form-group mb-3">
                                <input type="email" class="form-control" name="email" required
                                    placeholder="Enter Your Email">
                            </div>
                            <label for=>Password</label>
                            <div class="form-group mb-3">
                                <input type="password" class="form-control" name="password" required
                                    placeholder="Enter Your Password">
                            </div>
                            <label for=>user_type</label>
                            <div class="form-group mb-3">
                                <select name="user_type">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="Save_Changes" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--insert modal-->

        <!--View Modal-->
        <div class="modal fade" id="viewusermodal" tabindex="-1" aria-labelledby="viewusermodalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="viewusermodalLabel" style="color:black" ;>User Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="view_user_data">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <!--view--modal-->

        <!-- edit Modal -->
        <div class="modal fade" id="editdata" tabindex="-1" aria-labelledby="editdataLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="editdataLabel" style="color:black;">Update Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="insert" method="POST">
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <input type="hidden" class="form-control" id="user_id" name="id">
                            </div>

                            <label for="name">Name</label>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Enter Your Name">
                            </div>
                            <label for="email">Email Address</label>
                            <div class="form-group mb-3">
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter Your Email Address">
                            </div>
                            <label for="password">Password</label>
                            <div class="form-group mb-3">
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter Your Password">
                            </div>
                            <label for="user_type">User Type</label>
                            <div class="form-group mb-3">
                                <select name="user_type" id="user_type">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="update_data" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- edit modal -->

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteusermodal" tabindex="-1" aria-labelledby="deleteusermodalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="deleteusermodalLabel" style="color:black;">Delete User Data
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteForm">
                        <div class="modal-body">
                            <input type="hidden" class="form-control" name="user_id" id="confirm_delete_id">
                            <div class="view_user_data">
                                <h4>
                                    Are you sure you want to delete this data?
                                </h4>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes! Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->

        <!-- Modal of message!!! -->
        <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog"
            aria-labelledby="sendMessageModalLabel" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <form action="../FileUpload/admin_message" method="POST" enctype="multipart/form-data">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:white;">
                            <h5 class="modal-title" style="color:black;" id="sendMessageModalLabel">Send
                                Message
                                or File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Message or File form goes here -->
                            <p>Fill out the form to send a message or file.</p>

                            <!-- fetching the name of the sender stored in database !-->
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="sender" name="sender"
                                    value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">

                                <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>
                                </p>
                            </div>

                            <div class="form-group">
                                <?php
                                $query = "SELECT name, user_type FROM user_form";
                                $result = mysqli_query($conn, $query);

                                if ($result->num_rows > 0) {
                                    echo '<label for="recipient">Recipient:</label>';
                                    echo '<select class="form-control" id="recipient" name="recipient" required>';
                                    echo '<option value="" disabled selected>Select Recipient</option>';
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['name'] . '">' . $row['name'] . ' (' . $row['user_type'] . ')</option>';
                                    }
                                    echo '</select>';
                                } else {
                                    echo 'No users found.';
                                }
                                ?>
                            </div>

                            <div class="form-group">
                                <label for="messageType">Message Type:</label>
                                <select class="form-control" id="messageType" name="messageType" required>
                                    <option value="" disabled selected>Select Message Type</option>
                                    <option value="message">Message</option>
                                    <option value="file">File</option>
                                </select>
                            </div>

                            <div class="form-group" id="messageInput" style="display: none;">
                                <label for="messageOrFile">Message:</label>
                                <textarea class="form-control" id="messageOrFile" name="message" rows="4"></textarea>
                            </div>

                            <div class="form-group" id="fileAttachment" style="display: none;">
                                <label for="file">Attachment:</label>
                                <input type="file" class="form-control-file" id="file" name="file">
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton"
                                style="margin-top: 10px; background-color:darkorange; color:black; border-style:none;">Send</button>
                            <div id="confirmationMessage" style="display: none;"></div>
                            <div id="errorMessage" style="color: red; display: none;"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <?php
                    if (isset($_SESSION['status']) && $_SESSION['status'] != '') {

                        ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Hey!</strong>
                            <?php echo $_SESSION['status']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                        unset($_SESSION['status']);
                    }
                    ?>
                    <div class="card">
                        <div class="card-header text-center">
                            <h4 class="mb-0">USER MANAGEMENT</h4>
                        </div>

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col text-start">
                                    <button type="button" class="btn "
                                        style="margin-top:15px; background-color:#E8BCB9;" data-bs-toggle="modal"
                                        data-bs-target="#insertdata">
                                        ADD USER
                                    </button>
                                </div>
                                <div class="col text-end">
                                    <a href="../panels/admin_page" class="btn "
                                        style="background-color:#E8BCB9; margin-top:15px;">Back</a>
                                </div>
                                <!-- Pagination links -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>"
                                                    aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                    <span class="visually-hidden">Previous</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php
                                        $start_page = max(1, $current_page - 5);
                                        $end_page = min($total_pages, $current_page + 4);

                                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li class="page-item <?php if ($i == $current_page)
                                                echo 'active'; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>"
                                                    aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="visually-hidden">Next</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>

                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">User Type</th>
                                    <th scope="col">Action</th> <!-- Combine buttons into one column -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Your existing PHP code to fetch data from the database...
                                
                                // Check if the user is an admin to determine whether to show the delete button
                                if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
                                    $show_delete_button = false;
                                } else {
                                    $show_delete_button = true;
                                }

                                // Iterate over each fetched row and display them in the table
                                if (mysqli_num_rows($fetch_query_result) > 0) {
                                    while ($row = mysqli_fetch_array($fetch_query_result)) {
                                        ?>
                                        <tr>
                                            <!-- Add a hidden cell for user ID -->
                                            <td class="user_id" style="display: none;">
                                                <?php echo $row['user_id']; ?>
                                            </td>

                                            <td>
                                                <?php echo $row['name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['email']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['user_type']; ?>
                                            </td>
                                            <td>
                                                <!-- Action buttons -->
                                                <button type="button" style="background-color:#E8BCB9;" name="view_data"
                                                    data-name="<?php echo $row['user_id']; ?>" class="view_data btn"><i
                                                        class="fas fa-eye"></i></button>
                                                <button type="button" style="background-color:#E8BCB9;" name="edit_data"
                                                    data-name="<?php echo $row['user_id']; ?>" class="edit_data btn"><i
                                                        class="fas fa-edit"></i></button>
                                                <?php if ($row['user_type'] !== 'admin'): ?>
                                                    <!-- Only display the delete button if the user is not an admin -->
                                                    <button type="button" name="confirm_delete"
                                                        data-name="<?php echo $row['user_id']; ?>"
                                                        class="btn btn-danger btn-sm confirm_delete_btn"><i
                                                            class="fas fa-trash-alt"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="4">No Record Found</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <?php include ('../includes/footer.php'); ?>





                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script type="text/javascript">
                    // Set the timezone to Asia/Manila

                    $(document).ready(function () {
                        /* View Data */
                        $('.view_data').click(function (e) {
                            e.preventDefault();
                            var user_id = $(this).closest('tr').find('.user_id').text();

                            // Fetch user data to get the name
                            $.ajax({
                                type: "POST",
                                url: "insert.php",
                                data: {
                                    'fetch_user_data': true,
                                    'user_id': user_id,
                                },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status === 'success') {
                                        var userData = response.data;
                                        var userName = userData.name;

                                        // Create the description for the activity
                                        var description = "You viewed user data of " + userName;

                                        // Log the activity
                                        logActivity(description);

                                        // Update modal content
                                        var userInfo = '<h6>User ID : ' + userData.user_id + '</h6>' +
                                            '<h6>Full Name : ' + userData.name + '</h6>' +
                                            '<h6>Email Address : ' + userData.email + '</h6>' +
                                            '<h6>User Type : ' + userData.user_type + '</h6>';
                                        $('.view_user_data').html(userInfo);
                                        $('#viewusermodal').modal('show');
                                    } else {
                                        console.error(response.message);
                                        $('.view_user_data').html('<p>Error loading data.</p>');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error(xhr.responseText);
                                    $('.view_user_data').html('<p>Error loading data.</p>');
                                }
                            });
                        });

                        /* Edit Data */
                        $('.edit_data').click(function (e) {
                            e.preventDefault();
                            var user_id = $(this).closest('tr').find('.user_id').text();

                            $.ajax({
                                type: "POST",
                                url: "insert.php",
                                data: {
                                    'click_edit_btn': true,
                                    'user_id': user_id,
                                },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status === 'success') {
                                        var userData = response.data;
                                        $('#user_id').val(user_id);
                                        $('#name').val(userData.name);
                                        $('#email').val(userData.email);
                                        $('#password').val(userData.password);
                                        $('#user_type').val(userData.user_type);

                                        $('#editdata').modal('show');

                                        // Log edit activity
                                        var description = "You edited user data of " + userData.name;
                                        logActivity(description);
                                    } else {
                                        console.error(response.message);
                                        alert('Error loading user data.');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error(xhr.responseText);
                                    alert('Error loading user data.');
                                }
                            });
                        });

                        /* Confirm Delete */
                        $('.confirm_delete_btn').click(function (e) {
                            e.preventDefault();
                            var user_id = $(this).closest('tr').find('.user_id').text();
                            $('#confirm_delete_id').val(user_id);
                            $('#deleteusermodal').modal('show');
                        });

                        // Move the AJAX call to the "Yes! Delete" button click event
                        $('#confirmDeleteBtn').click(function (e) {
                            e.preventDefault();
                            var user_id = $('#confirm_delete_id').val();

                            $.ajax({
                                method: "POST",
                                url: "insert.php",
                                data: {
                                    'confirm_delete_btn': true,
                                    'user_id': user_id,
                                },
                                dataType: 'json',
                                success: function (response) {
                                    console.log(response);

                                    // Close the confirmation modal
                                    $('#deleteusermodal').modal('hide');

                                    // Display a message based on the response
                                    if (response.status === 'success') {
                                        alert(response.message);

                                        // Log delete activity
                                        var description = "You Deleted user data ID: " + user_id;
                                        logActivity(description);

                                        // Reload the page after a short delay (e.g., 1 second)
                                        setTimeout(function () {
                                            location.reload();
                                        }, 1000);
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    // Handle errors
                                    console.error(xhr.responseText);
                                    alert('Error: ' + error);
                                }
                            });
                        });

                        // Function to log activity
                        function logActivity(description) {
                            $.ajax({
                                method: "POST",
                                url: "insert.php",
                                data: {
                                    'log_activity': true,
                                    'description': description,
                                },
                                success: function (response) {
                                    console.log("Activity logged:", response);
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error logging activity:", error);
                                }
                            });
                        }

                        // Function to validate password
                        function validatePassword(password) {
                            var uppercaseRegex = /[A-Z]/;
                            var specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
                            return (password.length >= 8 && uppercaseRegex.test(password) && specialCharRegex.test(password));
                        }

                        // Function to handle password change
                        function changePassword() {
                            var newPassword = $('#newPassword').val();
                            var confirmPassword = $('#confirmPassword').val();

                            // Perform validation
                            if (newPassword !== confirmPassword) {
                                alert("New password and confirm password do not match");
                                return;
                            }

                            if (!validatePassword(newPassword)) {
                                alert("Password must contain at least one uppercase letter, one special character, and have a minimum length of 8 characters");
                                return;
                            }

                            // AJAX request to update password
                            $.ajax({
                                url: "update_password.php",
                                type: "POST",
                                data: {
                                    newPassword: newPassword
                                },
                                dataType: "json",
                                success: function (response) {
                                    if (response.status === "success") {
                                        alert("Password changed successfully");
                                        $('#settingsModal').modal('hide');

                                        // Log password change activity
                                        var description = " You Changed password successfully";
                                        logActivity(description);
                                    } else {
                                        alert("Error changing password: " + response.message);
                                    }
                                },
                                error: function (error) {
                                    alert("An error occurred: " + error.responseText);
                                }
                            });
                        }

                        // Add event listener to change password button
                        $('#changePasswordButton').on('click', function () {
                            changePassword();
                        });
                    });

                    const hamBurger = document.querySelector(".toggle-btn");

                    hamBurger.addEventListener("click", function () {
                        document.querySelector("#sidebar").classList.toggle("expand");
                    });

                    $(document).ready(function () {
                        // Event listener for the button to open the chat modal
                        $('#openChatModalLink').click(function () {
                            // Send AJAX request to home.php to open the chat modal
                            $.ajax({
                                url: '../home.php', // URL of the home.php file
                                type: 'GET', // HTTP request type
                                success: function (response) {
                                    // Insert the response (which includes the chat modal) into the DOM
                                    $('body').append(response);
                                    // Show the chat modal
                                    $('#chatModal').modal('show');
                                },
                                error: function (xhr, status, error) {
                                    console.error(xhr.responseText); // Log any errors to the console
                                }
                            });
                        });
                    });

                    $(document).ready(function () {
                        // Function to fetch initial counts and display notifications and recent activities
                        function fetchInitialCountsAndData() {
                            // Fetch initial counts from session variables
                            $.ajax({
                                url: '../panels/get_counts.php',
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    $('#notificationBadge').text(response.notification_count);
                                    $('#activityBadge').text(response.activity_count);
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error fetching counts:', error);
                                }
                            });

                            // Fetch and display notifications
                            fetchNotifications();

                            // Fetch and display recent activities
                            fetchRecentActivities();
                        }

                        // Attach click event listener to the activity dropdown
                        $('#activityDropdown').click(function () {
                            // Reset activity count to zero in the UI
                            $('#activityBadge').text('0');

                            // Fetch recent activities when the dropdown is clicked
                            fetchRecentActivities();

                            // Optionally, you can mark activities as "read" in the database here (AJAX request)
                            // This part depends on your application logic
                            // Mark notifications as "read" in the database (AJAX request)
                            $.ajax({
                                url: '../panels/mark_activities_as_read.php',
                                method: 'POST',
                                success: function (response) {
                                    console.log('Activities marked as read');
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error marking activities as read:', error);
                                    // Handle error (e.g., display an error message)
                                }
                            });
                        });

                        // Attach click event listener to the notification bell icon
                        $('#notificationDropdown').click(function () {
                            // Reset notification count to zero in the UI
                            $('#notificationBadge').text('0');

                            // Fetch and display notifications when the bell icon is clicked
                            fetchNotifications();

                            // Mark notifications as "read" in the database (AJAX request)
                            $.ajax({
                                url: '../panels/mark_notifications_as_read.php',
                                method: 'POST',
                                success: function (response) {
                                    console.log('Notifications marked as read');
                                },
                                error: function (xhr, status, error) {
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
                            url: '../panels/fetch_data.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                var notifications = data.notifications;
                                var unreadCount = notifications.filter(notification => notification.status === 'unread').length;
                                $('#notificationBadge').text(unreadCount);

                                $('#notificationMenu').empty();

                                notifications.forEach(function (notification) {
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
                            error: function (xhr, status, error) {
                                console.error('Error fetching notifications:', error);
                            }
                        });
                    }

                    function fetchRecentActivities() {
                        $.ajax({
                            url: '../panels/fetch_data.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                var activities = data.recentActivities;

                                // Update the activity badge count
                                $('#activityBadge').text(activities.length);

                                $('#activityMenu').empty();

                                activities.forEach(function (activity) {
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
                            error: function (xhr, status, error) {
                                console.error('Error fetching recent activities:', error);
                            }
                        });
                    }

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
                                url: "../folder/update_profile.php", // Replace with your actual backend endpoint
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
                                url: "../folder/fetch_profile.php", // Replace with your actual backend endpoint
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

                    // Refresh the page when the modal is closed
                    var uploadModal = document.getElementById('uploadModal');
                    uploadModal.addEventListener('hidden.bs.modal', function () {
                        location.reload();
                    });

                  
    //INPUT VALIDATION OF THE FILE EXCEED 25MB AND FILE TYPE
    document.addEventListener('DOMContentLoaded', function () {
    const uploadForm = document.getElementById('uploadForm');
    const uploadFileInput = document.getElementById('uploadFile');
    const uploadButton = document.getElementById('uploadFileButton');
    const uploadLabel = document.getElementById('uploadLabel');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const fileSizeError = document.getElementById('fileSizeError');
    const fileFormatError = document.getElementById('fileFormatError');
    const maxFileSize = 25 * 1024 * 1024 + 1024; // 25.001 MB in bytes
    const acceptedFormats = ['.zip', '.pptx', '.pdf', '.docx', '.txt', '.xlsx'];

    uploadFileInput.addEventListener('change', function () {
        const file = uploadFileInput.files[0];
        if (file) {
            // Check file size
            if (file.size > maxFileSize) {
                fileSizeError.style.display = 'block';
                fileFormatError.style.display = 'none'; // Hide format error message
                uploadButton.disabled = true;
            } else {
                fileSizeError.style.display = 'none';
                // Check file format
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (!acceptedFormats.includes('.' + fileExtension)) {
                    fileFormatError.style.display = 'block';
                    fileSizeError.style.display = 'none'; // Hide size error message
                    uploadButton.disabled = true;
                } else {
                    fileFormatError.style.display = 'none';
                    uploadButton.disabled = false;
                }
            }
        } else {
            fileSizeError.style.display = 'none';
            fileFormatError.style.display = 'none';
            uploadButton.disabled = true;
        }
    });

    uploadForm.addEventListener('submit', function (event) {
        const file = uploadFileInput.files[0];
        const title = document.getElementById('uploadTitle').value.trim();
        const author = document.getElementById('uploadAuthor').value.trim();
        const keywords = document.getElementById('uploadKeywords').value.trim();

        if (!file || file.size > maxFileSize || !acceptedFormats.includes('.' + file.name.split('.').pop().toLowerCase())) {
            alert('Please select a valid file.');
            event.preventDefault();
            return;
        }

        if (title === '' || author === '' || keywords === '') {
            alert('Please fill in all required fields (Title, Author, Keywords).');
            event.preventDefault();
            return;
        }

        // Display uploading message and progress bar
        uploadLabel.classList.remove('d-none');
        loadingSpinner.classList.remove('d-none');
        uploadButton.disabled = true;
    });

    // Dismiss uploading message and progress bar when clicked
    uploadLabel.addEventListener('click', function () {
        uploadLabel.classList.add('d-none');
        loadingSpinner.classList.add('d-none');
        uploadButton.disabled = false;
    });
});


            document.addEventListener('DOMContentLoaded', function() {
    const uploadFileInput = document.getElementById('uploadFile');
    const uploadTitleInput = document.getElementById('uploadTitle');
    const uploadAuthorInput = document.getElementById('uploadAuthor');
    const uploadKeywordsInput = document.getElementById('uploadKeywords');
    const uploadButton = document.getElementById('uploadFileButton');
    
    // Add event listeners to input fields
    uploadFileInput.addEventListener('change', toggleUploadButton);
    uploadTitleInput.addEventListener('input', toggleUploadButton);
    uploadAuthorInput.addEventListener('input', toggleUploadButton);
    uploadKeywordsInput.addEventListener('input', toggleUploadButton);
    
    // Function to toggle upload button based on input fields' values
    function toggleUploadButton() {
        const file = uploadFileInput.files[0];
        const title = uploadTitleInput.value.trim();
        const author = uploadAuthorInput.value.trim();
        const keywords = uploadKeywordsInput.value.trim();
        
        if (file && title !== '' && author !== '' && keywords !== '') {
            uploadButton.disabled = false;
        } else {
            uploadButton.disabled = true;
        }
    }
});


                </script>



                </body>

</html>