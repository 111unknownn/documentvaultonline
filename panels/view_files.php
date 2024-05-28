<?php
// Start session
session_start();

// Check if user is logged in and get the user ID from session
if (isset($_SESSION['user_id'])) {
    $thisUserId = $_SESSION['user_id'];
} else {
    // Handle case where user is not logged in or session is not set
    // You may redirect the user to a login page or display an error message
}


// Include database connection file
include '../config/config.php';

// Code to retrieve folders
$folderQuery = mysqli_query($conn, "SELECT folder_id, folder_name FROM folders WHERE user_id = $thisUserId");
if (mysqli_num_rows($folderQuery) > 0) {
    while ($folderRow = mysqli_fetch_assoc($folderQuery)) { ?>
        <a href="view_files?folder_id=<?php echo $folderRow['folder_id']; ?>" class="text-decoration-none">
            <div class="shadow p-3 rounded text-center border" style="width: 295px;">
                <img src="./images/file folder.svg" class="blue-logo" alt="folder" loading="lazy" width="130" height="80">
                <footer class="d-flex align-items-center justify-content-between gap-2 mt-1">
                    <aside>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($folderRow['folder_name']); ?></p>
                    </aside>
                    <aside class="d-flex align-items-center justify-content-center gap-1">
                        <!-- Add actions for folders if needed -->
                    </aside>
                </footer>
            </div>
        </a>
        <?php
    }
}
?>

<?php
// folder name clicked
if (isset($_GET['folder_id'])) {
    $folderId = $_GET['folder_id'];
    $_SESSION['loadMoreDataFilesFoldersOffset'] = 0;
    folderFilesMethod($folderId);
}

function folderFilesMethod($folderId)
{
    include '../config/config.php';

    // Get the folder ID
    $folderId = mysqli_real_escape_string($conn, $folderId);

    // Query to fetch documents based on the folder ID
    $query = "SELECT d.title, d.category FROM document d JOIN folders f ON d.folder_id = f.folder_id WHERE d.folder_id = $folderId";

    $result = mysqli_query($conn, $query);

    // Display the documents
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Display each document title and category
            echo "<p>Title: {$row['title']}</p>";
            echo "<p>Category: {$row['category']}</p>";
        }
    } else {
        echo "No documents found in this folder.";
    }
}

?>