<?php
session_start();
include '../config/config.php';
require_once '../session_timeout.php';

// Update last activity time
$_SESSION['last_activity'] = time();


// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    // Redirect to login page or display an error message
    header('Location: ../login');
    exit();
}

// Check if the user's name is set in the session
if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} else {
    // Retrieve the user's name from the database
    $user_id = $_SESSION['user_id']; // Assuming you have stored the user_id in the session
    $query = "SELECT name FROM user_form WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $userData = mysqli_fetch_assoc($result);
        if ($userData) {
            // User data found, you can now access the 'name' field
            $userName = $userData['name'];
        } else {
            echo "<p>User data not found</p>";
            exit();
        }
    } else {
        echo "<p>Error fetching user data: " . mysqli_error($conn) . "</p>";
        exit();
    }
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    // Redirect to login page or display an error message
    header('Location: ../login');
    exit();
}

$userName = $_SESSION['user_name'];

// Retrieve the category from the URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Initial query without category filter
$sql = "SELECT * FROM `document`";

// Append category condition if a category is specified
if (!empty($category)) {
    $sql .= " WHERE `category` = ?";
}

// Sort order
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'Newer';
$sortColumn = 'created_at'; // Assuming you have a column named 'created_at' for file creation timestamp

// Determine the sort direction based on user selection
$sortDirection = ($sortOrder === 'Newer') ? 'DESC' : 'ASC';

// Append sorting to the query
$sql .= " ORDER BY $sortColumn $sortDirection";

// Execute the query with prepared statement
$query = mysqli_prepare($conn, $sql);
if ($category) {
    mysqli_stmt_bind_param($query, 's', $category);
}
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);


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
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/main.css">
    <title>User Page</title>
</head>

<style>

.notification-container {
      position: relative;
  }

  .notification-badge {
      position: absolute;
      top: -1px;
      /* Adjust the value as needed */
      right: 135px;
      /* Adjust the value as needed */
      background-color: red ;
      color: white;
      font-weight: bold;
      border-radius: 40%;
      padding: 3px 5px;
      font-size: 12px;
  }
    .btn-custom {
        background-color: white;
        color: color;
        border-color: black;
    }

    .btn-custom:hover {
        background-color: darkorange;
        color: white;
        border-color: white;
    }

    /* Dropdown menu styling */
    .dropdown-menu {
        width: 700px;
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

    a.sidebar-link-1 {
        padding: .625rem 1.625rem;
        color: #FFF;
        display: block;
        font-size: 0.9rem;
        white-space: nowrap;
        border-left: 3px solid transparent;
        background-color: ;
        border-top: 1px solid #e4e4e4;
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
    }

    a.sidebar-link-1 {
        padding: .625rem 1.625rem;
        color: #FFF;
        display: block;
        font-size: 0.9rem;
        white-space: nowrap;
        border-left: 3px solid transparent;
        background-color: ;
        border-top: 1px solid #e4e4e4;
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

<script>
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
                url: "update_profile", // Replace with your actual backend endpoint
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
                url: "fetch_profile", // Replace with your actual backend endpoint
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

    //open and close eye for password hide and show
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



    function goBackToUserPage() {
        window.location.href = '../panels/user_page';
    }

    function goBackToDashboard() {
        window.location.href = '../panels/user_page';
    }


</script>


<style>
    </span>
</style>

<body>

    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex" style="background-color:; border-bottom: 1px solid  #e4e4e4; margin-top:-9px;">
                <div class="sidebar-logo">
                    <a href="user_page.php">
                        <img src="../images/logo.webp" alt="DOCU-VAULT Logo"
                            style="width: 60px; height: auto; margin-left:-10px;">
                    </a>
                    <span style="color:black; width:60px; font-weight:600; font-size:20px;">DOCU-VAULT</span>
                </div>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="documents" class="sidebar-link">
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
                <!-- Your sidebar item -->
                <li class="sidebar-item">
                    <span class="notification-badge"><?php echo $unreadCountDisplay; ?></span>
                    <a class="sidebar-link" id="openChatModalLink">
                        <i class="fa-solid fa-comment-dots pe-2"></i>
                        <span style="color:black; font-weight:bold;">Chat</span>
                        <span class="badge bg-danger" id="chatNotificationCount" style="display: none;"></span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../folder/index" class="sidebar-link">
                        <i class="fa-solid fa-folder pe-2"></i>
                        <span style="color:black; font-weight:bold;">Private File Upload</span>
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
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                                id="notificationMenu">
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
                                    <span style="font-style:Arial; font-weight:bold;"><?php echo $userName; ?></span>
                                    <!-- Display user's name -->
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
            <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel"
                aria-hidden="true">
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

<main class="content px-3 py-4" style="background-color:CED4DA;">
    <div class="container-fluid">
        <div class="mb-3">
            <h4><strong>User Dashboard</strong></h4>
            <div class="row" style="margin-top:50px;">
                <div class="col-12 col-md-6-flex">
                    <div class="card flex-fill border-0 illustration">
                        <div class="card-body p-0 d-flex flex-fill">
                            <div class="row g-0 w-100">
                                <div class="p-3 m-1">
                                    <h4 class="welcome-header">
                                        Welcome Back,
                                        <?php if (isset($userName)) {
                                            echo $userName;
                                        } else {
                                            echo "User";
                                        }
                                        ?>
                                    </h4>
                                    <p class="dashboard-text mb-0">User Dashboard </p>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <section class="main" style="background-color:#CED4DA; margin-top:80px; ">
        <div class="row justify-content-center">
            <!-- DOCS FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-word fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">DOCS FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openCategoryModal('.docx')">View</button>
                    </div>
                </div>
            </div>
            <!-- PPTX FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-powerpoint fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">PPTX FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openPPTXCategoryModal('.pptx')">View</button>
                    </div>
                </div>
            </div>
            <!-- TEXT FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-alt fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">TEXT FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openTXTCategoryModal('.txt')">View</button>
                    </div>
                </div>
            </div>
            <!-- EXCEL FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-excel fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">EXCEL FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openEXCELCategoryModal('.xlsx')">View</button>

                    </div>
                </div>
            </div>
            <!-- PDF FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-pdf fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">PDF FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openPDFCategoryModal('.pdf')">View</button>
                    </div>
                </div>
            </div>
            <!-- ZIP FILE -->
            <div class="col-6 col-md-4 col-lg-2 mb-4">
                <div class="card border-white" style="background-color: white;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <i class="fa fa-file-archive fa-2x mb-3 text-black"></i>
                        <h3 class="card-title">ZIP FILE</h3>
                        <button class="btn btn-custom w-100" onclick="openZIPCategoryModal('.zip')">View</button>
                    </div>
                </div>
            </div>
        </div>
    </section>






    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white;">
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





    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
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
                        <button type="button" class="btn" style="background-color:#D59D80;"
                            id="updateProfileButton">Update Profile</button>
                    </form>

                    <!-- Display Current Profile Information -->
                    <div class="mt-4">
                        <h6>Your Current Profile Information:</h6>
                        <p><strong>Full Name:</strong> <span id="currentFullName">John Doe</span></p>
                        <p><strong>Email:</strong> <span id="currentEmail">john.doe@example.com</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FOR VIEWING DOCS FILE -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="categoryModalLabel">Documents in DOCS FILE Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.docx';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }

                        // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </section>
                </div>

            </div>
        </div>
    </div>


    <!-- MODAL FOR VIEWING PDF FILES -->
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="pdfModalLabel">Documents in PDF Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.pdf';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }

                        // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </section>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL FOR VIEWING PPTX FILES -->
    <div class="modal fade" id="pptxModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="pdfModalLabel">Documents in PPT Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.pptx';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }


                        ?>
                    </section>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL FOR VIEWING ZIP FILE -->
    <div class="modal fade" id="zipModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="categoryModalLabel">Documents in ZIP FILE Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.zip';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }

                        ?>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FOR VIEWING EXCEL FILE -->
    <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="categoryModalLabel">Documents in EXCEL FILE Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.xlsx';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    } elseif ($rows['category'] === ".xlsx") {
                                        $fileImage = "excel.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }


                        ?>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL FOR VIEWING TEXT FILE -->
    <div class="modal fade" id="txtModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:white; color:black;">
                    <h5 class="modal-title" id="categoryModalLabel">Documents in TEXT FILE Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                        <?php
                        // Establish a database connection
                        // Replace 'your_host', 'your_username', 'your_password', and 'your_database' with your actual credentials
                        include "../config/config.php";
                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        // Execute your query to fetch documents based on category
                        // Replace 'your_category' with the actual category you want to fetch
                        $category = '.txt';
                        $sql = "SELECT * FROM document WHERE category = '$category'";
                        $result = mysqli_query($conn, $sql);

                        // Check if there are any results
                        if (mysqli_num_rows($result) > 0) {
                            // Loop through the fetched documents and display them
                            while ($rows = mysqli_fetch_assoc($result)) { ?>
                                <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                                    <?php
                                    $fileImage = "";
                                    $fileClass = "";
                                    if ($rows['category'] === ".docx" || $rows['category'] === ".doc") {
                                        $fileImage = "word.svg";
                                        $fileClass = "orange-icon"; // Add a class for word files
                                    } elseif ($rows['category'] === ".pptx" || $rows['category'] === ".ppt") {
                                        $fileImage = "powerpoint.svg";
                                        $fileClass = "orange-icon"; // Add a class for powerpoint files
                                    } elseif ($rows['category'] === ".txt") {
                                        $fileImage = "text.svg";
                                    } elseif ($rows['category'] === ".zip") {
                                        $fileImage = "zip.svg";
                                        $fileClass = "orange-icon"; // Add a class for zip files
                                    } elseif ($rows['category'] === ".pdf") {
                                        $fileImage = "pdf.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    } elseif ($rows['category'] === ".xlsx") {
                                        $fileImage = "excel.svg";
                                        $fileClass = "orange-icon"; // Add a class for pdf files
                                    }
                                    ?>
                                    <img src="./images/<?php echo $fileImage ?>"
                                        class="<?php echo $fileClass ?? '' ?> blue-logo" alt="file" loading="lazy" width="130"
                                        height="70">
                                    <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                        <aside>
                                            <p class="mb-0 fw-bold">
                                                <?php echo htmlspecialchars($rows['title'] . $rows['category']) ?>
                                            </p>
                                        </aside>
                                    </footer>
                                </div>
                            <?php }
                        } else {
                            echo "0 results";
                        }


                        ?>
                    </section>
                </div>
            </div>
        </div>
    </div>


    <!--NOTIFICATION MODAL -->
    <div class="modal fade" id="notificationModal" tabindex="2" role="dialog" aria-labelledby="notificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:darkorange;">
                    <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>

                </div>
                <div class="modal-body" id="notificationContent">
                    <!-- Notification content will be dynamically populated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal of message!!! -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <form action="../FileUpload/message" method="POST" enctype="multipart/form-data">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h5 class="modal-title" style="color:black;" id="sendMessageModalLabel">Send Message or File
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Message or File form goes here -->
                        <p>Fill out the form to send a message or file.</p>

                        <!-- fetching the name of the sender stored in database !-->
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="sender" name="sender"
                                value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>">
                            <p>Sender: <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?></p>
                        </div>

                        <div class="form-group">
                            <label for="recipient">Recipient:</label>
                            <input type="text" class="form-control" id="recipient" name="recipient"
                                placeholder="Search recipient..." autocomplete="off" required>
                            <div id="recipientSuggestions"></div>
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

</div>

    <script src="script.js"></script>
    <script>
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