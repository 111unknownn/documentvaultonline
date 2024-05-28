<?php
session_start();
include '../config/config.php';
require_once '../session_timeout.php';

// Update last activity time
$_SESSION['last_activity'] = time();

//ADMIN PAGE

// Check if the admin is logged in
if (isset($_SESSION['admin_name'])) {
    $adminName = $_SESSION['admin_name'];
}



if (!isset($_SESSION['admin_name'])) {
    header('location:');
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/fe90e88d78.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/admin_pannel.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/main.css">
    <title>Logs</title>
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
    h4 {
        text-align: center;
        margin-bottom: 20px;
    }

    .table-container {
        max-width: 100%;
        margin: auto;
    }

    table.dataTable {
        width: 100% margin: 10px auto;
        clear: both;
        border-collapse: collapse;
        border-spacing: 0;
    }

    table.dataTable thead th,
    table.dataTable tfoot th {
        font-weight: bold;
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table.dataTable thead th {
        background-color: #f2f2f2;
    }

    table.dataTable tbody td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    table.dataTable tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table.dataTable tbody tr:hover {
        background-color: #f2f2f2;
    }

    body {
        margin: 0;
        padding: 0;
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

    /* Custom CSS to adjust modal text flow */
    #viewDocumentModal .modal-body {
        writing-mode: ;
        /* Vertical text flow */
        text-orientation: mixed;
        /* Ensures characters are upright */
        white-space: nowrap;
        /* Prevents text from wrapping to the next line */
        overflow-x: hidden;
        /* Hides horizontal scrollbar if content overflows */
    }

    .eye-icon {
        max-height: 24px;
        /* Adjust the maximum height as needed */
        max-width: 24px;
        /* Adjust the maximum width as needed */
        cursor: pointer;
    }

    .input-group-text {
        cursor: pointer;
    }

    .btn-orange {
        background-color: orange;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-orange:hover {
        background-color: darkorange;
    }


    .sidebar-header b,
    .sidebar-item b {
        color: black;
    }

    .sidebar-logo a {
        color: black;
    }

    /* Footer styles */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 15px 2px;
        text-align: left;
        font-size: 14px;
        font-weight: bold;
    }

    .wrapper {
        height: 100%;
        overflow-y: auto;
        /* Enable scrolling for the content */
        padding-right: 10px;
        /* Add padding to compensate for scrollbar */
    }

    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;
    }

    .pagination-button {
        padding: 5px 10px;
        margin: 0 3px;
        border: 1px solid #ccc;
        border-radius: 3px;
        text-decoration: none;
        color: #333;
    }

    .pagination-button:hover {
        background-color: #f0f0f0;
    }

    .pagination-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .current-page {
        margin: 0 5px;
        font-weight: bold;
    }

    .custom-width {
        width: 999px;
        /* Adjust the width as needed */
    }

    .btn-red {
        background-color: red;
        color: white;
    }

    .btn-red:hover {
        background-color: darkred;
    }

    #uploadVersionDiv {
        display: none;
    }



    /* CSS for printing */
    @media print {
        body * {
            visibility: hidden;
        }

        .table-container,
        .table-container * {
            visibility: visible;
        }

        .table-container {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>


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
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notificationMenu">
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
              placeholder="Enter new full name">
          </div>
          <div class="mb-3">
            <label for="updateEmail" class="form-label">Email</label>
            <input required id="updateEmail" name="updateEmail" type="email" class="form-control"
              placeholder="Enter new email">
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

        <body>
            <div class="container-fluid" style="background-color:#CED4DA;">
                <h4 style="text-align: left; margin-left:10px; margin-top:5px; font-weight:500;">User Logs</h4>
                <div class="table-container">
                    <div class="filter-container">
                        <label for="dateFilter" style="margin-left:5px;">Filter by Date:</label>
                        <input type="date" id="dateFilter" name="dateFilter">
                        <button onclick="applyDateFilter()">Apply</button>
                    </div>
                    <div class="table-responsive">
                        <!-- Add table-responsive class to make the table scrollable on smaller screens -->
                        <table id="logTable" class="table table-bordered table-striped custom-width">
                            <!-- Table header -->
                            <thead class="thead-dark">
                                <tr>
                                    <th>Log ID</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                    <th>Login Time</th>
                                    <th>Logout Time</th>
                                    <th>Status</th>
                                    <th>Current Action</th>
                                    <th>Tools</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php
                               include "../config/config.php";

                                // Connect to the database
                                $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

                                // Check connection
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }

                                // Fetch user logs from the database with date filter
                                if (isset($_POST['dateFilter']) && !empty($_POST['dateFilter'])) {
                                    $dateFilter = $_POST['dateFilter'];
                                    $sql = "SELECT ul.*, uf.status AS user_status
            FROM user_logs ul
            JOIN user_form uf ON ul.user_id = uf.user_id
            WHERE DATE(ul.login_time) = '$dateFilter'";
                                } else {
                                    $sql = "SELECT ul.*, uf.status AS user_status
            FROM user_logs ul
            JOIN user_form uf ON ul.user_id = uf.user_id";
                                }
                                $result = mysqli_query($conn, $sql);

                                // Initialize a variable to track overall status
                                $overallStatus = "Enabled";

                                if (mysqli_num_rows($result) > 0) {
                                    // Output data of each row
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["log_id"] . "</td>";
                                        echo "<td>" . $row["name"] . "</td>";
                                        echo "<td>" . $row["action_description"] . "</td>";
                                        echo "<td>" . $row["login_time"] . "</td>";
                                        echo "<td>" . $row["logout_time"] . "</td>";
                                        echo "<td>" . $row["status_symbol"] . "</td>";
                                        echo "<td>" . $row["current_action"] . "</td>";
                                        echo "<td>";

                                        // Determine button class and text based on user status
                                        $buttonStatus = $row['user_status'] === "disabled" ? "disabled" : "enabled";
                                        $buttonClass = $buttonStatus === "disabled" ? "btn btn-sm btn-success" : "btn btn-sm btn-danger"; // Blue for enabled
                                        $buttonText = $buttonStatus === "disabled" ? "Enable" : "Disable";

                                        // Define the action to perform based on the button's current state
                                        $action = $buttonStatus === "disabled" ? "enable" : "disable";

                                        // Display the button with confirmation message
                                        echo "<button type='button' class='$buttonClass' onclick='confirmAction(\"" . $row["user_id"] . "\", \"$action\")'>$buttonText</button>";

                                        echo "</td>";
                                        echo "</tr>";

                                        // Store button state in session storage
                                        $_SESSION['user_status'][$row["user_id"]] = $buttonStatus;
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No user logs found</td></tr>";
                                }
                                ?>



 <!-- Settings Modal -->
 <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: white; color: black;">
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
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
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
                            <button type="button" class="btn " style="background-color:#D59D80;"
                                id="changePasswordButton">Change Password</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>






                                <!-- Confirmation Modal for Disabling -->
                                <div class="modal fade" id="disableConfirmationModal" tabindex="-1" role="dialog"
                                    aria-labelledby="disableConfirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="disableConfirmationModalLabel">Disable
                                                    Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p id="disableConfirmationMessage"></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary confirm-button"
                                                    id="confirmDisableAction">Confirm</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation Modal for Enabling -->
                                <div class="modal fade" id="enableConfirmationModal" tabindex="-1" role="dialog"
                                    aria-labelledby="enableConfirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="enableConfirmationModalLabel">Enable
                                                    Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p id="enableConfirmationMessage"></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-primary confirm-button"
                                                    id="confirmEnableAction">Confirm</button>
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
                    <form id="uploadForm" class="" enctype="multipart/form-data" method="POST"
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

</div>

                                <!-- Modal OF MESSAGE! -->
                                <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog"
                                    aria-labelledby="sendMessageModalLabel" aria-hidden="true" data-bs-backdrop="static"
                                    data-bs-keyboard="false">
                                    <form action="../FileUpload/admin_message" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color:white;">
                                                    <h5 class="modal-title" id="sendMessageModalLabel"
                                                        style="color:black;">Send Message or File
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Message or File form goes here -->
                                                    <p>Fill out the form to send a message or file.</p>

                                                    <!-- FETCHING THE SENDER NAME IN THE DATABASE! -->
                                                    <div class="form-group">
                                                        <input type="hidden" class="form-control" id="sender"
                                                            name="sender"
                                                            value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">
                                                        <p>Sender:
                                                            <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : ''; ?>
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
                                                        <select class="form-control" id="messageType" name="messageType"
                                                            required>
                                                            <option value="" disabled selected>Select Message Type
                                                            </option>
                                                            <option value="message">Message</option>
                                                            <option value="file">File</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group" id="messageInput" style="display: none;">
                                                        <label for="messageOrFile">Message:</label>
                                                        <textarea class="form-control" id="messageOrFile" name="message"
                                                            rows="4"></textarea>
                                                    </div>

                                                    <div class="form-group" id="fileAttachment" style="display: none;">
                                                        <label for="file">Attachment:</label>
                                                        <input type="file" class="form-control-file" id="file"
                                                            name="file">
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

                             
        
        <script type="text/javascript">

                                    //printing the table 
                                    function printTable() {
                                        // Hide elements not to be printed
                                        var filterContainer = document.querySelector('.filter-container');
                                        var printButton = document.querySelector('button');

                                        filterContainer.style.display = 'none';
                                        printButton.style.display = 'none'; // Hide the print button itself

                                        // Trigger print functionality
                                        window.print();

                                        // Restore display of hidden elements after printing
                                        filterContainer.style.display = 'block';
                                        printButton.style.display = 'block';
                                    }

                                    function confirmAction(userId, action) {
                                        var confirmationMessage = '';
                                        if (action === 'enable') {
                                            confirmationMessage = 'Are you sure you want to enable the account?';
                                        } else {
                                            confirmationMessage = 'Are you sure you want to disable the account?';
                                        }
                                        if (confirm(confirmationMessage)) {
                                            updateUserStatus(userId, action);
                                        }
                                    }

                                    //updating user status if disable or enable
                                    function updateUserStatus(userId, action) {
                                        // Create a new XMLHttpRequest object
                                        var xhr = new XMLHttpRequest();

                                        // Configure the request
                                        xhr.open("POST", "update_user_status", true);
                                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                                        // Define the callback function
                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState === XMLHttpRequest.DONE) {
                                                if (xhr.status === 200) {
                                                    // Handle the response from the server if needed
                                                    console.log(xhr.responseText);
                                                    // Reload the page or update UI as needed
                                                    location.reload(); // For example, you can reload the page after the update
                                                } else {
                                                    // Handle any errors that occur during the request
                                                    console.error('Error:', xhr.status);
                                                }
                                            }
                                        };

                                        // Send the request with the user ID and action
                                        xhr.send("user_id=" + encodeURIComponent(userId) + "&action=" + encodeURIComponent(action));
                                    }


                                    $(document).ready(function () {
                                        $('#example').DataTable();
                                    });


                                    // about document
                                    $('.about-document-cta').off('click').on('click', function () {
                                        var documentId = $(this).attr('data-id')
                                        var fileName = $(this).attr('data-name')
                                        $('#aboutDocumentModalLabel').text(fileName)

                                        $.get("main", { aboutDocumentId: documentId }, function (response, status) {
                                            if (status) {
                                                $('#aboutModalBody').html(response)
                                            }
                                        })
                                    })


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
                                                url: "update_password", // Replace with your actual backend endpoint
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



                                        //open and close password eyes
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

                                        //Prevent CLicking Outside The Notification Modal
                                        $(document).ready(function () {
                                            console.log("After modal initialization"); $(document).ready(function () {
                                                // Initialize the profile modal without showing it immediately
                                                $('#profileModal').modal({
                                                    backdrop: 'static',  // Disable clicking outside the modal
                                                    keyboard: false  // Disable closing the modal with the keyboard
                                                });

                                                // Show the profile modal after a short delay (e.g., 500 milliseconds)
                                                setTimeout(function () {
                                                    $('#profileModal').modal('hide');
                                                }, 500);
                                            });

                                            // Initialize the modal without showing it immediately
                                            $('#notificationModal').modal({
                                                backdrop: 'static',  // Disable clicking outside the modal
                                                keyboard: false  // Disable closing the modal with the keyboard
                                            });

                                            // Show the modal after a short delay (e.g., 500 milliseconds)
                                            setTimeout(function () {
                                                $('#notificationModal').modal('hide');
                                                console.log("Modal shown");
                                            }, 500);
                                        });

                                        //Prevent CLicking Outside The Settings Modal
                                        $(document).ready(function () {
                                            console.log("After modal initialization"); $(document).ready(function () {
                                                // Initialize the profile modal without showing it immediately
                                                $('#profileModal').modal({
                                                    backdrop: 'static',  // Disable clicking outside the modal
                                                    keyboard: false  // Disable closing the modal with the keyboard
                                                });

                                                // Show the profile modal after a short delay (e.g., 500 milliseconds)
                                                setTimeout(function () {
                                                    $('#settingsModal').modal('hide');
                                                }, 500);
                                            });

                                            // Initialize the modal without showing it immediately
                                            $('#settingsModal').modal({
                                                backdrop: 'static',  // Disable clicking outside the modal
                                                keyboard: false  // Disable closing the modal with the keyboard
                                            });

                                            // Show the modal after a short delay (e.g., 500 milliseconds)
                                            setTimeout(function () {
                                                $('#settingsModal').modal('hide');
                                                console.log("Modal shown");
                                            }, 500);
                                        });

                                        //Prevent CLicking Outside The Profile Modal
                                        $(document).ready(function () {
                                            console.log("After modal initialization"); $(document).ready(function () {
                                                // Initialize the profile modal without showing it immediately
                                                $('#profileModal').modal({
                                                    backdrop: 'static',  // Disable clicking outside the modal
                                                    keyboard: false  // Disable closing the modal with the keyboard
                                                });

                                                // Show the profile modal after a short delay (e.g., 500 milliseconds)
                                                setTimeout(function () {
                                                    $('#ProfileModal').modal('hide');
                                                }, 500);
                                            });

                                            // Initialize the modal without showing it immediately
                                            $('#ProfileModal').modal({
                                                backdrop: 'static',  // Disable clicking outside the modal
                                                keyboard: false  // Disable closing the modal with the keyboard
                                            });

                                            // Show the modal after a short delay (e.g., 500 milliseconds)
                                            setTimeout(function () {
                                                $('#ProfileModal').modal('hide');
                                                console.log("Modal shown");
                                            }, 500);
                                        });

                                        // Function to apply date filter
                                        function applyDateFilter() {
                                            var dateFilter = document.getElementById('dateFilter').value;
                                            var xhr = new XMLHttpRequest();
                                            xhr.onreadystatechange = function () {
                                                if (xhr.readyState === XMLHttpRequest.DONE) {
                                                    if (xhr.status === 200) {
                                                        var logs = JSON.parse(xhr.responseText);
                                                        // Update the table with filtered logs
                                                        updateTable(logs);
                                                    } else {
                                                        console.error('Error fetching logs: ' + xhr.status);
                                                    }
                                                }
                                            };
                                            xhr.open('GET', 'date_filter?dateFilter=' + dateFilter, true);
                                            xhr.send();
                                        }

                                        function updateTable(logs) {
                                            var table = $('#logTable').DataTable();
                                            table.clear().draw();
                                            logs.forEach(function (log) {
                                                // Determine button class and text based on user status
                                                var buttonClass = log.user_status === 'enabled' ? 'btn-danger' : 'btn-success';
                                                var buttonText = log.user_status === 'enabled' ? 'Disable' : 'Enable';


                                                var toggleButton = '<button class="btn ' + buttonClass + '" onclick="toggleUserStatus(\'' + log.user_id + '\', \'' + log.name + '\', \'' + log.user_status + '\', \'' + (log.user_status === 'enabled' ? 'disableConfirmationModal' : 'enableConfirmationModal') + '\')">' + buttonText + '</button>';
                                                // Add row to the table
                                                table.row.add([
                                                    log.log_id,
                                                    log.name,
                                                    log.action_description,
                                                    log.login_time,
                                                    log.logout_time,
                                                    log.status_symbol,
                                                    log.current_action,
                                                    toggleButton
                                                ]).draw();
                                            });
                                        }


                                        // Initialize DataTable
                                        $(document).ready(function () {
                                            $('#logTable').DataTable();
                                        });

                                        function toggleUserStatus(userId, userName, userStatus) {
                                            var confirmationMessage = '';
                                            var modalId = '';
                                            var confirmButtonId = '';

                                            if (userStatus === "enabled") {
                                                confirmationMessage = "Are you sure you want to disable the account of " + userName + "?";
                                                modalId = '#disableConfirmationModal';
                                                confirmButtonId = '#confirmDisableAction';
                                            } else {
                                                confirmationMessage = "Are you sure you want to enable the account of " + userName + "?";
                                                modalId = '#enableConfirmationModal';
                                                confirmButtonId = '#confirmEnableAction';
                                            }

                                            $(modalId).find('.modal-body').text(confirmationMessage);
                                            $(modalId).modal('show');

                                            $(confirmButtonId).off('click').on('click', function () {
                                                handleToggleUserStatus(userId, userStatus, modalId);
                                                // Store the updated status in sessionStorage
                                                var updatedStatus = userStatus === 'enabled' ? 'disabled' : 'enabled';
                                                sessionStorage.setItem('button_status_' + userId, updatedStatus);
                                            });
                                        }

                                        function handleToggleUserStatus(userId, userStatus, modalId) {
                                            $.ajax({
                                                url: 'toggle_user_status',
                                                type: 'POST',
                                                data: { user_id: userId },
                                                success: function (response) {
                                                    var data = JSON.parse(response);
                                                    if (data.success) {
                                                        var button = $('[onclick^="toggleUserStatus"][onclick*="' + userId + '"]');
                                                        if (data.new_status === "enabled") {
                                                            button.text("Disable");
                                                            button.removeClass("btn-success").addClass("btn-danger");
                                                        } else {
                                                            button.text("Enable");
                                                            button.removeClass("btn-danger").addClass("btn-success");
                                                        }
                                                    } else {
                                                        alert(data.message);
                                                    }
                                                },
                                                error: function (xhr, status, error) {
                                                    console.error(error);
                                                    alert("An error occurred while updating user status.");
                                                },
                                                complete: function () {
                                                    // Hide the correct modal after confirming the action
                                                    $(modalId).modal('hide');
                                                }
                                            });
                                        }

                                        //Upload Function and sends ti main.php
                                        $(document).ready(function () {
                                            // Show the modal when the page loads
                                            $('#uploadModal').modal({
                                                backdrop: 'static',  // Disable clicking outside the modal
                                                keyboard: false      // Disable closing the modal with the keyboard
                                            });

                                            // Handle upload button click
                                            $('#uploadFileButton').click(function () {
                                                // Submit the form
                                                $('#uploadForm').submit();
                                            });

                                            // Handle form submission
                                            $('#uploadForm').on('submit', function (e) {
                                                e.preventDefault();  // Prevent the default form submission

                                                // Show loading spinner and progress bar
                                                $('#loadingSpinner').removeClass('d-none');
                                                $('#uploadLabel').removeClass('d-none');
                                                $('.progress').removeClass('d-none');
                                                $('#uploadFileButton').prop('disabled', true);

                                                var formData = new FormData(this);
                                                $.ajax({
                                                    url: "../panels/main",
                                                    type: "POST",
                                                    data: formData,
                                                    contentType: false,
                                                    processData: false,
                                                    xhr: function () {
                                                        var xhr = new window.XMLHttpRequest();
                                                        xhr.upload.addEventListener("progress", function (evt) {
                                                            if (evt.lengthComputable) {
                                                                var percentComplete = (evt.loaded / evt.total) * 100;
                                                                $('#progressBar').width(percentComplete + '%');
                                                                $('#progressText').text(percentComplete.toFixed(2) + '%');
                                                            }
                                                        }, false);
                                                        return xhr;
                                                    },
                                                    success: function (response) {
                                                        if ($.trim(response) === "") {
                                                            alert("Upload file completed");
                                                            // Reset form and hide elements
                                                            $('#uploadForm')[0].reset();
                                                            $('#loadingSpinner').addClass('d-none');
                                                            $('#uploadLabel').addClass('d-none');
                                                            $('.progress').addClass('d-none');
                                                            $('#uploadFileButton').prop('disabled', false);
                                                            // Hide modal and redirect to admin page
                                                            $('#uploadModal').modal('hide');
                                                            window.location.href = '../panels/admin_page';
                                                        } else {
                                                            alert(response);
                                                            $('#loadingSpinner').addClass('d-none');
                                                            $('#uploadLabel').addClass('d-none');
                                                            $('.progress').addClass('d-none');
                                                            $('#uploadFileButton').prop('disabled', false);
                                                        }
                                                    },
                                                    error: function (xhr, status, error) {
                                                        alert("An error occurred: " + error);
                                                    }
                                                });
                                            });
                                        });

                                        // when i click close button it will back in admin_dicuments.php
                                        function goBack() {
                                            // Check if the upload modal was opened from the admin_documents page
                                            if (window.location.pathname.includes('logs')) {
                                                window.location.href = 'logs'; // Navigate back to admin_documents.php
                                            } else {
                                                // Default behavior: navigate back one step in history
                                                window.history.back();
                                            }
                                        }


                                        //disable upload button unless they all completed put in iput fields
                                        document.addEventListener('DOMContentLoaded', function () {
                                            // Function to check if all required fields are filled and enable/disable the upload button accordingly
                                            function checkFields() {
                                                var fileInput = document.getElementById('uploadFile');
                                                var titleInput = document.getElementById('uploadTitle');
                                                var authorInput = document.getElementById('uploadAuthor');
                                                var keywordsInput = document.getElementById('uploadKeywords');
                                                var versionInput = document.getElementById('uploadVersion');

                                                var uploadButton = document.getElementById('uploadFileButton');

                                                if (fileInput.value && titleInput.value && authorInput.value && keywordsInput.value) {
                                                    uploadButton.disabled = false;
                                                } else {
                                                    uploadButton.disabled = true;
                                                }
                                            }

                                            // Event listeners to call checkFields() when input values change
                                            var fileInput = document.getElementById('uploadFile');
                                            var titleInput = document.getElementById('uploadTitle');
                                            var authorInput = document.getElementById('uploadAuthor');
                                            var keywordsInput = document.getElementById('uploadKeywords');
                                            var versionInput = document.getElementById('uploadVersion');

                                            if (fileInput && titleInput && authorInput && keywordsInput && versionInput) {
                                                fileInput.addEventListener('change', checkFields);
                                                titleInput.addEventListener('input', checkFields);
                                                authorInput.addEventListener('input', checkFields);
                                                keywordsInput.addEventListener('input', checkFields);
                                                versionInput.addEventListener('input', checkFields);
                                            }
                                        });

                                        // Function to handle sending a message
                                        function sendMessage() {
                                            // Close the modal after 2 seconds and reload messages
                                            setTimeout(function () {
                                                $('#sendMessageModal').modal('hide');
                                                loadMessages(1); // Assuming you have a loadMessages function, replace it with the correct function if needed
                                            }, 2000);
                                        }

                                        $(document).ready(function () {
                                            // Show/hide message input based on message type
                                            $('#messageType').change(function () {
                                                if ($(this).val() === 'file') {
                                                    $('#fileAttachment').show();
                                                    $('#messageInput').hide();
                                                } else if ($(this).val() === 'message') {
                                                    $('#fileAttachment').hide();
                                                    $('#messageInput').show();
                                                } else {
                                                    $('#fileAttachment').hide();
                                                    $('#messageInput').hide();
                                                }
                                            });

                                            // On form submission
                                            $('form').submit(function (e) {
                                                e.preventDefault();

                                                // AJAX request to handle form submission
                                                $.ajax({
                                                    type: 'POST',
                                                    url: $(this).attr('action'),
                                                    data: new FormData(this),
                                                    processData: false,
                                                    contentType: false,
                                                    success: function (response) {
                                                        // Display the success message
                                                        $('#confirmationMessage').html('<div class="alert alert-success" role="alert">' + response + '</div>').show();
                                                        $('#errorMessage').hide();
                                                        // Call the sendMessage function to close and refresh the modal after 2 seconds
                                                        sendMessage();
                                                        updateNotification(); // Call the function to update notification number
                                                    },
                                                    error: function (error) {
                                                        // Display the error message
                                                        $('#errorMessage').html('<div class="alert alert-danger" role="alert">Error sending message/file.</div>').show();
                                                        $('#confirmationMessage').hide();
                                                    }
                                                });
                                            });
                                        });


                                        // Function to update notification number
                                        function updateNotification() {
                                            setInterval(function () {
                                                var xhttp = new XMLHttpRequest();
                                                xhttp.onreadystatechange = function () {
                                                    if (this.readyState == 4 && this.status == 200) {
                                                        document.getElementById("noti_number").innerHTML = this.responseText;
                                                    }
                                                };
                                                xhttp.open("GET", "data", true);
                                                xhttp.send();
                                            }, 1000);
                                        }

                                        function goBackToUserPage() {
                                            window.location.href = '../panels/admin_page';
                                        }

                                        function goBackToDashboard() {
                                            window.location.href = '../panels/admin_page';
                                        }     
                 
                                        const hamBurger = document.querySelector(".toggle-btn");

                            hamBurger.addEventListener("click", function () {
                                document.querySelector("#sidebar").classList.toggle("expand");
                            });

                           

                  </script>
                  <script>
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

$(document).ready(function() {
    // Function to fetch initial counts and display notifications and recent activities
    function fetchInitialCountsAndData() {
        // Fetch initial counts from session variables
        $.ajax({
            url: '../panels/get_counts.php',
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
            url: '../panels/mark_activities_as_read.php',
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
            url: '../panels/mark_notifications_as_read.php',
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
        url: '../panels/fetch_data.php',
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
        url: '../panels/fetch_data.php',
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
        </body >

</html >