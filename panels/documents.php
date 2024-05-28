<?php
session_start();
// Database configuration
include '../config/config.php';
require_once '../session_timeout.php';

// Update last activity time
$_SESSION['last_activity'] = time();

//USER PAGE


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
// Check if the admin is logged in
if (isset($_SESSION['user_name'])) {
    $adminName = $_SESSION['user_name'];
}



if (!isset($_SESSION['user_name'])) {
    header('location:');
}

$search = mysqli_real_escape_string($conn, isset($_GET['q']) ? $_GET['q'] : "");
$filterBy = mysqli_real_escape_string($conn, isset($_GET['fb']) ? $_GET['fb'] : "");
$thisUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


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

    <!-- Include Chart.js from a CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>User Page</title>
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

    body {
        margin: 0;
        padding: 0;
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

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
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

    #uploadVersionDiv {
        display: none;
    }
</style>
</style>
<div class="wrapper">

    <aside id="sidebar">
        <div class="d-flex" style="background-color: #fd8522;">
            <div class="sidebar-logo">
                <a href="user_page.php">
                <img src="../images/logo.webp" alt="DOCU-VAULT Logo" style="width: 60px; height: auto; margin-left:-10px;">
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

        <main class=" rounded m-3 p-3 vh-100 overflow-auto" style="background-color: #CED4DA;">
            <h3>Documents</h3>
            <header class="w-100 mb-3 d-flex align-items-center justify-content-between gap-2">
                <aside>
                    <form action="" method="get" class="d-flex align-items-center justify-content-center gap-2">
                        <label for="filterByDocuments" class="form-label mb-0" style="white-space: nowrap;">Filter
                            by</label>
                        <select id="filterByDocuments" name="fb" class="form-select">
                            <?php
                            if ($filterBy === "Owned") { ?>
                                <option value="All">All</option>
                                <option selected value="Owned">Owned</option><?php
                            } else { ?>
                                <option selected value="All">All</option>
                                <option value="Owned">Owned</option><?php
                            }
                            ?>
                        </select>
                        <input type="text" class="d-none" name="q" value="<?php echo $search ?>">
                        <button type="submit" class="d-none" id="filterByDocumentsButton"></button>
                    </form>
                </aside>
                <aside>
                    <form action="documents.php" method="get"
                        class="d-flex align-items-center justify-content-center gap-2">
                        <input type="text" name="fb" value="<?php echo $filterBy; ?>" class="form-control d-none">
                        <input type="search" name="q" class="form-control" placeholder="Search files here...">
                        <button type="submit" class="btn" style="background-color:darkorange;">Search</button>
                    </form>

                </aside>

                <form action="" method="get" class="d-flex align-items-center justify-content-center gap-2">
                    <label for="sortByDocuments" class="form-label mb-0" style="white-space: nowrap;">Sort by</label>
                    <select id="sortByDocuments" name="sort" class="form-select">
                        <?php
                        $sortOptions = ['Newer', 'Older'];
                        foreach ($sortOptions as $option) {
                            if (isset($_GET['sort']) && $_GET['sort'] === $option) {
                                echo "<option selected value='$option'>$option</option>";
                            } else {
                                echo "<option value='$option'>$option</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" id="sortByDocumentsButton" class="btn "
                        style="color: black; font-weight: 500;background-color:darkorange;">Apply</button>
                </form>

                </aside>
            </header>

            <?php
            // Set the number of entries per page
            $entriesPerPage = 20;

            // Retrieve the current page number
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

            // Calculate the offset for SQL query
            $offset = ($page - 1) * $entriesPerPage;

            // Initial query without category filter
            $sql = $filterBy === "Owned" ? "SELECT * FROM `document` WHERE `user_id`=$thisUserId AND (`title` LIKE '%$search%' OR `tags` LIKE '%$search%')" : "SELECT * FROM `document` WHERE `title` LIKE '%$search%' OR `tags` LIKE '%$search%'";
            // Sort order
            
            // Retrieve the category from the URL
            $category = isset($_GET['category']) ? $_GET['category'] : '';

            // Initialize category condition
            $categoryCondition = '';

            // Append category condition if a category is specified
            if (!empty($category)) {
                $categoryCondition = "AND `category` = '$category'";
            }

            // Append category condition to the query
            $sql .= " $categoryCondition";

            // Sort order
            $sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'Newer';
            $sortColumn = 'created_at'; // Assuming you have a column named 'created_at' for file creation timestamp
            
            // Determine the sort direction based on user selection
            $sortDirection = ($sortOrder === 'Newer') ? 'DESC' : 'ASC';

            // Append sorting to the query
            $sql .= " ORDER BY $sortColumn $sortDirection";

            // Add pagination limits to the query
            $sql .= " LIMIT $entriesPerPage OFFSET $offset";

            // Execute the query
            $query = mysqli_query($conn, $sql);

            // Set default values for pagination variables
            $totalEntries = 0;
            $previousPage = 1;
            $nextPage = 1;
            ?>
            <?php
            // Check if there are any entries
            if (mysqli_num_rows($query) > 0) {
                ?>
                <section class="d-flex align-items-start justify-content-start gap-3 flex-wrap">
                    <?php
                    while ($rows = mysqli_fetch_assoc($query)) { ?>
                        <div class="shadow p-3 rounded text-center border" style="width: 280px;">
                            <?php
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
                                $fileClass = "orange-icon"; // Add a class for excel files
                            } elseif ($rows['category'] === ".zip") {
                                $fileImage = "zip.svg";
                                $fileClass = "orange-icon"; // Add a class for zip files
                            }
                            ?>
                            <img src="./images/<?php echo $fileImage ?>" class="<?php echo $fileClass ?? '' ?> blue-logo"
                                alt="file" loading="lazy" width="130" height="70">
                            <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                                <aside>
                                    <?php
                                    // Check if latest_filename is not empty, use it to display the filename
                                    if (!empty($rows['latest_filename'])) {
                                        echo '<p class="mb-0 fw-bold">' . htmlspecialchars($rows['latest_filename']) . htmlspecialchars($rows['category']) . '</p>';
                                    } else {
                                        // If latest_filename is empty, display the original title
                                        echo '<p class="mb-0 fw-bold">' . htmlspecialchars($rows['title']) . htmlspecialchars($rows['category']) . '</p>';
                                    }
                                    ?>
                                </aside>
                                <aside class="d-flex align-items-center justify-content-center gap-1">
                                    <button type="button" class="border-0 btn btn-light download-document-cta"
                                        data-bs-toggle="modal" data-bs-target="#downloadDocumentModal"
                                        data-id="<?php echo $rows['document_id'] ?>"
                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"><img
                                            src="./images/download.svg" width="20" height="20" loading="lazy"></button>

                                    <div class="dropdown">
                                        <button type="button" class="border-0 btn btn-light dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false"></button>
                                        <ul class="dropdown-menu">
                                            <?php
                                            // Always include the "View" button
                                            ?>
                                            <li><a class="dropdown-item fw-bold view-document-cta" href="#"
                                                    data-id="<?php echo $rows['document_id'] ?>"
                                                    data-name="<?php echo $rows['title'] . $rows['category'] ?>">View</a></li>
                                            <?php

                                            // Optionally, you can keep the other items for the owner check
                                            if ($thisUserId == $rows['user_id']) { ?>
                                                <li><a class="dropdown-item fw-bold edit-document-cta" href="#"
                                                        data-id="<?php echo $rows['document_id'] ?>"
                                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#editDocumentModal">Edit</a></li>
                                                <li><a class="dropdown-item fw-bold key-document-cta" href="#"
                                                        data-id="<?php echo $rows['document_id'] ?>"
                                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#keyDocumentModal">KEY</a></li>
                                                <li><a class="dropdown-item fw-bold about-document-cta" href="#"
                                                        data-id="<?php echo $rows['document_id'] ?>"
                                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#aboutDocumentModal">About</a></li>
                                                <!-- Add the delete button if the file is from the author -->
                                                <li>
                                                    <a class="dropdown-item fw-bold delete-document-cta" href="#"
                                                        data-id="<?php echo $rows['document_id'] ?>"
                                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#deleteDocumentModal">Delete</a>
                                                </li>

                                            <?php } else { ?>
                                                <li><a class="dropdown-item fw-bold about-document-cta" href="#"
                                                        data-id="<?php echo $rows['document_id'] ?>"
                                                        data-name="<?php echo $rows['title'] . $rows['category'] ?>"
                                                        data-bs-toggle="modal" data-bs-target="#aboutDocumentModal">About</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </aside>
                            </footer>
                        </div>
                        <?php
                    }
                    ?>
                </section>
                <?php
            } else {
                // Display a message if there are no entries
                echo "<p>No documents found.</p>";
            }
            ?>

            <!-- Pagination -->
            <div class="pagination mt-3">
                <?php
                // Fetch total number of entries (without pagination)
                $totalEntriesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `document` WHERE `title` LIKE '%$search%' OR `tags` LIKE '%$search%'");
                $totalEntries = mysqli_fetch_assoc($totalEntriesQuery)['total'];

                // Calculate total number of pages
                $totalPages = ceil($totalEntries / $entriesPerPage);

                // Determine previous and next page numbers
                $previousPage = $page > 1 ? $page - 1 : 1;
                $nextPage = $page < $totalPages ? $page + 1 : $totalPages;

                // Output pagination links
                echo "<span>Showing " . ($offset + 1) . " to " . min(($offset + mysqli_num_rows($query)), $totalEntries) . " of $totalEntries entries</span>";
                ?>
                <div class="pagination-buttons">
                    <?php
                    // Previous button
                    if ($page > 1) {
                        echo "<a href='?page=$previousPage' class='pagination-button prev'>Previous</a>";
                    } else {
                        echo "<span class='pagination-button disabled'>Previous</span>";
                    }

                    // Current page number
                    echo "<span class='current-page'>$page</span>";

                    // Next button
                    if ($page < $totalPages) {
                        echo "<a href='?page=$nextPage' class='pagination-button next'>Next</a>";
                    } else {
                        echo "<span class='pagination-button disabled'>Next</span>";
                    }
                    ?>
                </div>
            </div>
        </main>



        <!-- Download document -->
        <div class="modal fade" id="downloadDocumentModal" tabindex="-1" aria-labelledby="downloadDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="downloadDocumentModalLabel" style="color:black;">Modal title
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="downloadDocumentForm">
                            <div class="mb-3">
                                <label class="form-label" for="downloadDocumentKey">KEY (optional)</label>
                                <input id="downloadDocumentKey" name="downloadDocumentKey" type="text"
                                    placeholder="Enter KEY" class="form-control">
                            </div>
                            <footer class="d-flex align-items-center justify-content-between gap-2">
                                <button type="submit" class="btn " id="downloadDocumentDownloadNow"
                                    style="background-color:#D59D80;">Download
                                    now</button>
                                <p class="mb-0 d-none" id="downloadDocumentLabel">Please wait...</p>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key document -->
        <div class="modal fade" id="keyDocumentModal" tabindex="-1" aria-labelledby="keyDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="keyDocumentModalLabel" style="color:black;">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="getDocumentKey">KEY</label>
                            <input id="getDocumentKey" type="text" readonly class="form-control" value="3434">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit document -->
        <div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="editDocumentModalLabel" style="color:black;">Edit file</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateFileForm" class="border rounded p-3">
                            <!-- ... -->
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- About document -->
        <div class="modal fade" id="aboutDocumentModal" tabindex="-1" aria-labelledby="aboutDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="aboutDocumentModalLabel" style="color:black;">About</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="aboutModalBody">
                        <!-- .. -->
                    </div>
                </div>
            </div>
        </div>


        <!-- Delete Document Modal -->
        <div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h5 class="modal-title" id="deleteDocumentModalLabel" style="color:black;">Delete Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this document?</p>
                        <input type="hidden" id="deleteDocumentId" name="deleteDocumentId" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteDocument">Delete</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- View document -->
        <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:white;">
                        <h1 class="modal-title fs-5" id="viewDocumentModalLabel"
                            style="white-space: normal; color:black;"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <pre id="fileContent" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
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



        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script type="text/javascript">


          

            const hamBurger = document.querySelector(".toggle-btn");

            hamBurger.addEventListener("click", function () {
                document.querySelector("#sidebar").classList.toggle("expand");
            });

            // Sort by
            $('#sortByDocuments').on('change', function () {
                var sortByValue = $(this).val();
                var filterByValue = $('#filterByDocuments').val();
                var searchValue = $('input[name="q"]').val();
                window.location.href = "documents.php?sort=" + sortByValue + "&fb=" + filterByValue + "&q=" + searchValue;
            });

            // Filter by
            $('#filterByDocuments').on('change', function () {
                var filterByValue = $(this).val();
                var sortByValue = $('#sortByDocuments').val();
                var searchValue = $('input[name="q"]').val();
                window.location.href = "documents.php?sort=" + sortByValue + "&fb=" + filterByValue + "&q=" + searchValue;
            });


            // Key document
            $(".key-document-cta").on('click', function (e) {
                e.preventDefault();
                var documentId = $(this).data('id');
                var fileName = $(this).data('name');
                $('#keyDocumentModalLabel').text(fileName);

                $.get("main.php", { keyDocumentId: documentId }, function (response, status) {
                    if (status === "success") {
                        $('#getDocumentKey').val(response);
                    }
                });
            });

            // Function to delete a document
            function deleteDocument(documentId) {
                // Send a POST request to main.php with the document ID to trigger deletion
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Optionally, you can handle success response here
                            // For example, you can redirect the user or display a success message
                            location.reload(); // Reload the page after successful deletion
                        } else {
                            // Handle error response here
                            console.error('Error: Unable to delete the document.');
                        }
                    }
                };
                xhr.open('POST', 'main.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('deleteDocumentId=' + encodeURIComponent(documentId));
            }

            // Event listener for delete button click
            document.addEventListener('DOMContentLoaded', function () {
                var deleteButtons = document.querySelectorAll('.delete-document-cta');
                deleteButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        // Get the document ID from the button's data attribute
                        var documentId = this.getAttribute('data-id');

                        // Update the hidden input field in the modal with the document ID
                        document.getElementById('deleteDocumentId').value = documentId;
                    });
                });

                // Event listener for confirming deletion within the modal
                var confirmDeleteButton = document.getElementById('confirmDeleteDocument');
                confirmDeleteButton.addEventListener('click', function () {
                    // Get the document ID from the hidden input field in the modal
                    var documentId = document.getElementById('deleteDocumentId').value;

                    // Call the deleteDocument function with the document ID
                    deleteDocument(documentId);
                });
            });


            // download document
            $('.download-document-cta').off('click').on('click', function () {
                var documentId = $(this).attr('data-id')
                var fileName = $(this).attr('data-name')
                $('#downloadDocumentModalLabel').text(fileName)

                // download now
                $('#downloadDocumentForm').off('submit').on('submit', function (e) {
                    e.preventDefault()
                    $('#downloadDocumentDownloadNow').prop("disabled", true)
                    $('#downloadDocumentLabel').removeClass('d-none')

                    var formData = new FormData(this)
                    formData.append("downloadDocumentId", documentId)
                    $.ajax({
                        url: "main",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            if ($.trim(response) === "incorrect") {
                                alert('Incorrect KEY!')
                                $('#downloadDocumentDownloadNow').prop("disabled", false)
                                $('#downloadDocumentLabel').addClass('d-none')
                            }
                            else {
                                var getResponse = response.substring(0, response.indexOf(","))
                                var filePath = response.substring(response.indexOf(",") + 1)

                                if (getResponse === "empty") {
                                    $('#downloadDocumentDownloadNow').prop("disabled", false)
                                    $('#downloadDocumentLabel').addClass('d-none')

                                    // create temporary element
                                    var body = document.body
                                    var createElement = document.createElement("a")
                                    createElement.setAttribute("href", 'encryptedFiles/' + filePath)
                                    createElement.setAttribute("target", '_blank')
                                    createElement.setAttribute("download", fileName)
                                    body.appendChild(createElement)
                                    createElement.click()

                                }
                                else if (getResponse === "correct") {
                                    $('#downloadDocumentDownloadNow').prop("disabled", false)
                                    $('#downloadDocumentLabel').addClass('d-none')

                                    // create temporary element
                                    var body = document.body
                                    var createElement = document.createElement("a")
                                    createElement.setAttribute("href", 'tmpFiles/' + filePath)
                                    createElement.setAttribute("target", '_blank')
                                    createElement.setAttribute("download", fileName)
                                    body.appendChild(createElement)
                                    createElement.click()
                                }
                                else {
                                    alert(response)
                                    $('#downloadDocumentDownloadNow').prop("disabled", false)
                                    $('#downloadDocumentLabel').addClass('d-none')
                                }
                            }
                        }
                    })
                })
            })

            // edit document
            $('.edit-document-cta').off('click').on('click', function () {
                var documentId = $(this).attr('data-id')
                var fileName = $(this).attr('data-name')

                $.get("main", { editDocumentId: documentId }, function (response, status) {
                    if (status) {
                        $("#updateFileForm").html(response)

                        // update file
                        $('#updateFileForm').off('submit').on('submit', function (e) {
                            e.preventDefault()

                            var formData = new FormData(this)
                            $.ajax({
                                url: "main",
                                type: "POST",
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (response) {
                                    if ($.trim(response) === "") {
                                        alert("Updated succesfully")
                                    }
                                }
                            })
                        })

                        //change file
                        $('#changeFileForm').off('submit').on('submit', function (e) {
                            e.preventDefault()
                            $('#changeFileLabel').removeClass('d-none')
                            $('#changeFileButton').prop('disabled', true)

                            var formData = new FormData(this)
                            formData.append("changeDocumentId", documentId)

                            $.ajax({
                                url: "main",
                                type: "POST",
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function (response) {
                                    if ($.trim(response) === "") {
                                        alert("Upload file completed");
                                        $('#changeFileLabel').addClass('d-none')
                                        $('#changeFileButton').prop('disabled', false)
                                        $('#changeFileForm input').val('')
                                    }
                                    else {
                                        alert(response)
                                        $('#changeFileLabel').addClass('d-none')
                                        $('#changeFileButton').prop('disabled', false)
                                    }
                                }
                            })
                        })
                    }
                })
            })

            // view document
            $('.view-document-cta').off('click').on('click', function () {
                var documentId = $(this).attr('data-id');
                var fileName = $(this).attr('data-name');

                // Set the modal title
                $('#viewDocumentModalLabel').text(fileName);

                // Request the content from the server
                $.get("main", { viewDocumentId: documentId }, function (response, status) {
                    if (status === 'success') {
                        // Set the content in the modal body
                        $('#fileContent').html(response);

                        // Show the modal
                        $('#viewDocumentModal').modal('show');
                    } else {
                        // Handle error
                        console.error('Failed to fetch document content.');
                    }
                });
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




            function goBackToUserPage() {
                window.location.href = '../panels/admin_page';
            }

            function goBackToDashboard() {
                window.location.href = '../panels/admin_page';
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
                        url: "main",
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
                if (window.location.pathname.includes('admin_documents')) {
                    window.location.href = 'admin_documents'; // Navigate back to admin_documents.php
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

            // Function to open the docs category modal
            function openCategoryModal(category) {
                // Fetch documents based on category from the database
                fetchDocuments(category);
                // Display the modal
                $('#categoryModal').modal('show');
            }

            // Function to open the PDF category modal
            function openPDFCategoryModal(category) {
                // Fetch PDF documents based on category from the database
                fetchPDFDocuments(category);
                // Display the modal
                $('#pdfModal').modal('show');
            }

            // Function to open the PPTcategory modal
            function openPPTXCategoryModal(category) {
                // Fetch PDF documents based on category from the database
                fetchPDFDocuments(category);
                // Display the modal
                $('#pptxModal').modal('show');
            }

            // Function to open the docs category modal
            function openZIPCategoryModal(category) {
                // Fetch documents based on category from the database
                fetchDocuments(category);
                // Display the modal
                $('#zipModal').modal('show');
            }

            // Function to open the docs category modal
            function openEXCELCategoryModal(category) {
                // Fetch documents based on category from the database
                fetchDocuments(category);
                // Display the modal
                $('#excelModal').modal('show');
            }

            // Function to open the txt category modal
            function openTXTCategoryModal(category) {
                // Fetch documents based on category from the database
                fetchDocuments(category);
                // Display the modal
                $('#txtModal').modal('show');
            }


            // Function to fetch documents .docx from the database
            function fetchDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (index % 5 === 0) { // Start a new row for every 5th document
                                row = $('<div class="row"></div>');
                                $('#categoryDocuments').append(row);
                            }
                            var documentItem = $('<div class="col-md-2 document-item"></div>');
                            var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                            documentItem.append(documentLink);
                            row.append(documentItem);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }


            // Function to fetch PDF documents from the database
            function fetchPDFDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (doc.category === ".pdf") {
                                if (index % 5 === 0) { // Start a new row for every 5th document
                                    row = $('<div class="row"></div>');
                                    $('#categoryDocuments').append(row);
                                }
                                var documentItem = $('<div class="col-md-2 document-item"></div>');
                                var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                                documentItem.append(documentLink);
                                row.append(documentItem);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }


            // Function to fetch PPT documents from the database
            function fetchPDFDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (doc.category === ".pptx") {
                                if (index % 5 === 0) { // Start a new row for every 5th document
                                    row = $('<div class="row"></div>');
                                    $('#categoryDocuments').append(row);
                                }
                                var documentItem = $('<div class="col-md-2 document-item"></div>');
                                var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                                documentItem.append(documentLink);
                                row.append(documentItem);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }

            // Function to fetch ZIP documents from the database
            function fetchPDFDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (doc.category === ".zip") {
                                if (index % 5 === 0) { // Start a new row for every 5th document
                                    row = $('<div class="row"></div>');
                                    $('#categoryDocuments').append(row);
                                }
                                var documentItem = $('<div class="col-md-2 document-item"></div>');
                                var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                                documentItem.append(documentLink);
                                row.append(documentItem);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }

            // Function to fetch EXCEL documents from the database
            function fetchPDFDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (doc.category === ".xlsx") {
                                if (index % 5 === 0) { // Start a new row for every 5th document
                                    row = $('<div class="row"></div>');
                                    $('#categoryDocuments').append(row);
                                }
                                var documentItem = $('<div class="col-md-2 document-item"></div>');
                                var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                                documentItem.append(documentLink);
                                row.append(documentItem);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }


            // Function to fetch EXCEL documents from the database
            function fetchPDFDocuments(category) {
                $.ajax({
                    url: 'fetch_documents.php', // Path to your PHP script
                    type: 'GET',
                    data: { category: category }, // Pass category as a parameter
                    dataType: 'json',
                    success: function (response) {
                        // Clear previous document list
                        $('#categoryDocuments').empty();
                        // Loop through the fetched documents and display them
                        var row;
                        $.each(response, function (index, doc) {
                            if (doc.category === ".txt") {
                                if (index % 5 === 0) { // Start a new row for every 5th document
                                    row = $('<div class="row"></div>');
                                    $('#categoryDocuments').append(row);
                                }
                                var documentItem = $('<div class="col-md-2 document-item"></div>');
                                var documentLink = $('<a href="' + doc.file_path + '" class="document-link" >' + doc.title + '</a>');
                                documentItem.append(documentLink);
                                row.append(documentItem);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching documents:', error);
                    }
                });
            }
        </script>

        <!-- Settings Modal -->
        <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: white;">
                        <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
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
                    <div class="modal-header" style="background-color:white;">
                        <h5 class="modal-title" id="profileModalLabel" style="color:black;">Profile</h5>
                    </div>
                    <div class="modal-body">
                        <!-- Profile Update Form -->
                        <form id="profileUpdateForm">
                            <div class="mb-3">
                                <label for="updateFullName" class="form-label">Full Name</label>
                                <input required id="updateFullName" name="updateFullName" type="text"
                                    class="form-control" placeholder="Enter new full name">
                            </div>
                            <div class="mb-3">
                                <label for="updateEmail" class="form-label">Email</label>
                                <input required id="updateEmail" name="updateEmail" type="email" class="form-control"
                                    placeholder="Enter new email">
                            </div>
                            <button type="button" class="btn " style="background-color:#D59D80;"
                                id="updateProfileButton">Update Profile</button>
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


        <!-- MODAL FOR VIEWING DOCS FILE -->

        <!-- Add this modal structure where you see fit in your HTML -->
        <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Documents in DOCS FILE Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Display documents in the modal body -->
                        <div id="categoryDocuments"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        <!--NOTIFICATION MODAL -->
        <div class="modal fade" id="notificationModal" tabindex="2" role="dialog"
            aria-labelledby="notificationModalLabel" aria-hidden="true">
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
    </div>


    <!-- Modal of message!!! -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                            <p>Sender:
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
<script>
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