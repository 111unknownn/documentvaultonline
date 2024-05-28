<?php
// Start session to access session variables
session_start();

// Include your database connection file
include "../config/config.php"; // Update this with the path to your database connection script

// Check if the folder name is set and not empty
if (isset($_POST['folderName']) && !empty($_POST['folderName'])) {
    $folderName = $_POST['folderName']; // Get the folder name from the POST request

    // Check if the user_id is set in the session
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Get the user_id from the session

        // Query the folders table to check if the folder already exists for the specified user
        $checkFolderQuery = "SELECT folder_id FROM folders WHERE user_id = ? AND folder_name = ?";

        if ($stmt = mysqli_prepare($conn, $checkFolderQuery)) {
            mysqli_stmt_bind_param($stmt, "is", $userId, $folderName);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if a folder with the same name already exists for the user
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    echo 'Error: A folder with the name "' . $folderName . '" already exists for the user.';
                } else {
                    // No folder found with the same name for the user, proceed to create a new folder

                    // Get the current timestamp
                    $folderCreated = date('Y-m-d H:i:s');

                    // Generate a folder token (you can use a secure method like UUID or a hash function)
                    $folderToken = sha1(uniqid());

                    // Check if default folder already created or not
                    $query1 = mysqli_query($conn, "SELECT * FROM `folders` WHERE `folder_id` = 1");
                    if (mysqli_num_rows($query1) <= 0) {
                        // Create default folder 
                        mysqli_query($conn, "INSERT INTO `folders` (`folder_name`, `folder_created`, `user_id`, `folder_token`, `main_id`) VALUES ('Default', NOW(), $userId, '43fdlfjdlfjr3434443jfl', 1)");
                    }

                    // Get the maximum main_id from existing folders
                    $query2 = mysqli_query($conn, "SELECT MAX(main_id) AS max_main_id FROM folders");
                    $row = mysqli_fetch_assoc($query2);
                    $maxMainId = $row['max_main_id'] ?? 0; // If no folder exists, set maxMainId to 0

                    // Increment the maximum main_id to get the next main_id
                    $mainId = $maxMainId + 1;

                    // Prepare the SQL statement to insert the folder details into the 'folders' table
                    $insertFolderQuery = "INSERT INTO folders (user_id, folder_name, folder_created, folder_token, main_id) VALUES (?, ?, ?, ?, ?)";

                    // Prepare and bind parameters to prevent SQL injection
                    if ($stmt = mysqli_prepare($conn, $insertFolderQuery)) {
                        mysqli_stmt_bind_param($stmt, "isssi", $userId, $folderName, $folderCreated, $folderToken, $mainId);

                        // Execute the statement
                        if (mysqli_stmt_execute($stmt)) {
                            // Folder creation successful
                            echo 'Folder "' . $folderName . '" created successfully.';
                        } else {
                            // Folder creation failed
                            echo 'Error: Unable to create folder "' . $folderName . '".';
                        }

                        // Close the statement

                    } else {
                        // Error preparing the SQL statement
                        echo 'Error: Unable to prepare SQL statement.';
                    }
                }
            } else {
                // Error executing the statement
                echo 'Error: Unable to execute SQL statement.';
            }

            // Close the statement

        } else {
            // Error preparing the SQL statement
            echo 'Error: Unable to prepare SQL statement.';
        }
    } else {
        // user_id not found in session or empty
        echo 'Error: user_id not found in session or empty.';
    }
} else {
    // Folder name not provided or empty
    echo 'Error: Folder name not provided or empty.';
}
?>