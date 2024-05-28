<?php

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'dms_db';

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);


$thisUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;



// AES-256-CBC encryption/decryption functions
define("FIXED_IV", hex2bin('000102030405060708090a0b0c0d0e0f'));
define("FIXED_KEY", "!@#ADSA_+)&*ASFasfbasf+_^%asda");

// Encrypt a file using AES-256-CBC
function encryptAES256File($filePath)
{
    $fileContent = file_get_contents($filePath);
    $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', FIXED_KEY, 0, FIXED_IV);
    $encryptedFile = $filePath;
    file_put_contents($encryptedFile, FIXED_IV . $encryptedContent);
    return "Encrypted file successfully";
}

// Decrypt a file using AES-256-CBC
function decryptAES256File($encryptedFile, $decryptedPath)
{
    $encryptedContent = file_get_contents($encryptedFile);
    $ivSize = openssl_cipher_iv_length('aes-256-cbc');
    $encryptedContent = substr($encryptedContent, $ivSize);
    $decryptedContent = openssl_decrypt($encryptedContent, 'aes-256-cbc', FIXED_KEY, 0, FIXED_IV);
    $decryptedFile = $decryptedPath;
    file_put_contents($decryptedFile, $decryptedContent);
    return "Decrypted file successfully";
}

// Function to execute Git commands
function execGitCommand($command, &$output = null, &$returnVar = null) {
    exec($command, $output, $returnVar);
}

   // Upload file
if (isset($_FILES['uploadFile']) && isset($_POST['uploadTitle']) && isset($_POST['uploadAuthor']) && isset($_POST['uploadKeywords'])) {
    $files = $_FILES['uploadFile'];
    $title = mysqli_real_escape_string($conn, $_POST['uploadTitle']);
    $author = mysqli_real_escape_string($conn, $_POST['uploadAuthor']);
    $tags = mysqli_real_escape_string($conn, $_POST['uploadKeywords']);

    // Maximum file size allowed (25MB)
    $maxFileSize = 25 * 1024 * 1024; // 25 MB in bytes

    $size = $files['size'];
    $error = $files['error'];
    $name = $files['name'];
    $ext = substr($name, strrpos($name, "."));
    $baseName = substr($name, 0, strrpos($name, "."));
    $folder = "C:/xampp/htdocs/docuvault/panels/encryptedFiles/";

    // Check file size
    if ($size > $maxFileSize) {
        echo "File size exceeds the maximum limit of 25MB.";
        exit();
    }

    // Initialize version number
    $version = 1;
    $titleWithVersion = $title;

    // Check if a file with the same base name and extension already exists
    $newFileName = $title . $ext;
    $filePath = $folder . $newFileName;
    while (file_exists($filePath)) {
        // Only increment version if the filename matches exactly
        $version++;
        $titleWithVersion = $title . '_v' . $version;
        $newFileName = $titleWithVersion . $ext;
        $filePath = $folder . $newFileName;
    }

    if (move_uploaded_file($files['tmp_name'], $filePath)) {
        // Encrypt file
        encryptAES256File($filePath);

        // Add new data into `document` table
        $stmt = mysqli_prepare($conn, "INSERT INTO `document`(`title`,`author`,`category`,`tags`,`file_size`,`file_path`,`user_id`, `created_at`) VALUES(?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP())");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssdss", $titleWithVersion, $author, $ext, $tags, $size, $newFileName, $thisUserId);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                // Add new data into `version` table
                mysqli_query($conn, "INSERT INTO `version`(`document_id`,`date_uploaded`,`download`) VALUES(LAST_INSERT_ID(), NOW(), '0')");

                // Git commands to add and commit the new file
                execGitCommand("cd " . escapeshellarg($folder) . " && git add " . escapeshellarg($newFileName));
                execGitCommand("cd " . escapeshellarg($folder) . " && git commit -m 'User $thisUserId uploaded $newFileName'");

                $status_symbol = "✔️"; // Success status symbol

                // Insert activity into `activities` table
                $description = "You uploaded file name " . $newFileName;
                $timestamp = date('Y-m-d H:i:s');
                $insertActivityQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, 'unread')";
                $activityStmt = $conn->prepare($insertActivityQuery);
                $activityStmt->bind_param("iss", $thisUserId, $description, $timestamp);
                if ($activityStmt->execute()) {
                    $activityStmt->close();
                } else {
                    echo "❌ Error inserting activity: " . $activityStmt->error;
                }
            } else {
                echo "Error executing statement: " . mysqli_error($conn);
                $status_symbol = "❌"; // Failure status symbol
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
            $status_symbol = "❌"; // Failure status symbol
        }

        // Query to fetch user's name based on user_id
        $userQuery = mysqli_query($conn, "SELECT name FROM user_form WHERE user_id = $thisUserId");

        if ($userQuery && mysqli_num_rows($userQuery) > 0) {
            $userData = mysqli_fetch_assoc($userQuery);
            $name = $userData['name'];
        } else {
            $name = "Unknown User";
        }

        // Insert log entry for the user's current action
        $current_action = "Upload";
        $action_description = "$name uploaded a file with title: " . $newFileName;
        $timestamp = date('Y-m-d H:i:s');
        $user_id = $thisUserId;

        $insert_log_query = "INSERT INTO user_logs (user_id, action_description, timestamp, current_action, name) VALUES ($user_id, '$action_description', '$timestamp', '$current_action', '$name')";
        if (mysqli_query($conn, $insert_log_query)) {
            echo $status_symbol . " Upload Successfully!.";
        } else {
            echo $status_symbol . " Error inserting log entry: " . mysqli_error($conn);
        }
    } else {
        echo "❌ Error moving uploaded file.";
    }
}




// Get key
if (isset($_GET['keyDocumentId'])) {
    $documentId = $_GET['keyDocumentId'];
    $query = mysqli_query($conn, "SELECT * FROM `document` WHERE `document_id`=$documentId");
    if (mysqli_num_rows($query) > 0) {
        $rows = mysqli_fetch_assoc($query);
        echo sha1($rows['file_path']);
    }
}

// Download now
if (isset($_POST['downloadDocumentKey']) && isset($_POST['downloadDocumentId'])) {
    $yourKey = mysqli_real_escape_string($conn, $_POST['downloadDocumentKey']);
    $documentId = $_POST['downloadDocumentId'];
    $download = 0;

    // Assuming you store the logged-in user's ID and name in the session
    $userId = $_SESSION['user_id'];
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

    // If the user's name is not in the session, fetch it from the database
    if (is_null($userName)) {
        $userQuery = mysqli_query($conn, "SELECT `name` FROM `user_form` WHERE `user_id`='$userId'");
        if (mysqli_num_rows($userQuery) > 0) {
            $userRow = mysqli_fetch_assoc($userQuery);
            $userName = $userRow['name'];
        } else {
            // Handle the case where the user is not found in the database
            echo "User not found.";
            exit;
        }
    }

    $query = mysqli_query($conn, "SELECT * FROM `document` WHERE `document_id`=$documentId");
    if (mysqli_num_rows($query) > 0) {
        $rows = mysqli_fetch_assoc($query);

        // No key entered
        if (empty($yourKey)) {
            echo "empty," . $rows['file_path'];
            $download = 1;

            // Decrypt the file for download
            decryptAES256File("encryptedFiles/" . $rows['file_path'], "tmpFiles/" . $rows['file_path']);
        }
        // There is a key
        else {
            // Correct key
            if (sha1($rows['file_path']) == $yourKey) {
                echo "correct," . $rows['file_path'];
                $download = 1;

                // Decrypt the file for download
                decryptAES256File("encryptedFiles/" . $rows['file_path'], "tmpFiles/" . $rows['file_path']);
            }
            // Incorrect key
            else {
                echo "incorrect";
            }
        }

        // Get the latest version id of the files
        if ($download == 1) {
            $query1 = mysqli_query($conn, "SELECT * FROM `version` WHERE `document_id`=$documentId ORDER BY `version_id` DESC LIMIT 1");
            if (mysqli_num_rows($query1) > 0) {
                $rows1 = mysqli_fetch_assoc($query1);
                $versionId = $rows1['version_id'];
                $downloadValue = intval($rows1['download']) + $download;

                // Set download value
                mysqli_query($conn, "UPDATE `version` SET `download`='$downloadValue' WHERE `version_id`=$versionId");

                // Insert the activity into the activities table
                $description = "You downloaded document id $documentId";
                $timestamp = date("Y-m-d H:i:s");
                $status = 'completed';

                $insertActivityQuery = "INSERT INTO `activities` (`user_id`, `description`, `timestamp`, `status`) VALUES ('$userId', '$description', '$timestamp', '$status')";
                mysqli_query($conn, $insertActivityQuery);

                // Insert the activity into the user_logs table
                $logDescription = "Downloaded document ID: $documentId";
                $logStatus = 'completed';
                $logTimestamp = date("Y-m-d H:i:s");
                $current_action = "Download";
                $insertLogQuery = "INSERT INTO `user_logs` (`user_id`, `action_description`, `name`, `timestamp`, `current_action`) VALUES ('$userId', '$logDescription', '$userName', '$logTimestamp', '$current_action')";
                mysqli_query($conn, $insertLogQuery);
            }
        }
    }
}






// Edit document
date_default_timezone_set('Asia/Manila');
if (isset($_GET['editDocumentId'])) {
    $documentId = $_GET['editDocumentId'];
    $_SESSION['editDocumentId'] = $documentId;

    // Fetch the document details for editing
    $query = mysqli_query($conn, "SELECT * FROM `document` WHERE `document_id`=$documentId");
    if (mysqli_num_rows($query) > 0) {
        $rows = mysqli_fetch_assoc($query);

        // Display the form for editing the document
        ?>
        <div class="mb-3">
            <label for="updateTitle" class="form-label">Title</label>
            <input required id="updateTitle" name="updateTitle" type="text" class="form-control" placeholder="Enter title..."
                value="<?php echo $rows['latest_filename'] ?>">
        </div>
        <div class="mb-3">
            <label for="updateAuthor" class="form-label">Author</label>
            <input required id="updateAuthor" name="updateAuthor" type="text" class="form-control" placeholder="Enter author..."
                value="<?php echo $rows['author'] ?>">
        </div>
        <div class="mb-3">
            <label for="updateKeywords" class="form-label">Keywords (Searchable)</label>
            <input required id="updateKeywords" name="updateKeywords" type="text" class="form-control"
                placeholder="ex: (documents, .ppt)" value="<?php echo $rows['tags'] ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <?php
    }
}

// Update file 
date_default_timezone_set('Asia/Manila');
if (isset($_POST['updateTitle']) && isset($_POST['updateAuthor']) && isset($_POST['updateKeywords'])) {
    $documentId = $_SESSION['editDocumentId'];
    $title = mysqli_real_escape_string($conn, $_POST['updateTitle']);
    $author = mysqli_real_escape_string($conn, $_POST['updateAuthor']);
    $keywords = mysqli_real_escape_string($conn, $_POST['updateKeywords']);

    // Update details and last edit time, but not date_uploaded
    $updateQuery = "UPDATE `document` SET `latest_filename`='$title', `author`='$author', `tags`='$keywords', `last_edit_time` = CURRENT_TIMESTAMP WHERE `document_id`=$documentId";
    if (mysqli_query($conn, $updateQuery)) {
        // Record the activity in the activities table
        $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
        $description = "You edit document id $documentId";
        $status = 'Edited';
        $timestamp = date('Y-m-d H:i:s');

        $activityQuery = "INSERT INTO `activities` (`user_id`, `description`, `timestamp`, `status`) VALUES ('$userId', '$description', '$timestamp', '$status')";
        mysqli_query($conn, $activityQuery);
    }
}






// about document
if (isset($_GET['aboutDocumentId'])) {
    $documentId = $_GET['aboutDocumentId'];

    $query = mysqli_query($conn, "SELECT * FROM `document` INNER JOIN `user_form` ON `document`.`user_id` = `user_form`.`user_id` WHERE `document`.`document_id`=$documentId");
    if (mysqli_num_rows($query) > 0) {
        $rows = mysqli_fetch_assoc($query); ?>

        <div class="mb-2 d-flex aling-items-center justify-content-start gap-2">
            <p class="mb-0">Author:</p>
            <p class="mb-0 fw-bold"><?php echo $rows['author'] ?></p>
        </div>
        <div class="mb-2 d-flex aling-items-center justify-content-start gap-2">
            <p class="mb-0">Contact via:</p>
            <p class="mb-0 fw-bold"><?php echo $rows['email'] ?></p>
        </div>
        <div class="mb-2 d-flex aling-items-center justify-content-start gap-2">
            <p class="mb-0">Uploaded by:</p>
            <p class="mb-0 fw-bold"><?php echo $rows['name'] ?></p>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date uploaded</th>
                    <th>Last Edit</th>
                    <th>Original FileName</th>
                    <th>Latest FileName</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query1 = mysqli_query($conn, "SELECT version.date_uploaded AS upload_time, document.last_edit_time, document.title, document.category, document.latest_filename FROM `document` INNER JOIN `version` ON document.document_id = version.document_id WHERE document.document_id = $documentId");
                if (mysqli_num_rows($query1) > 0) {
                    while ($rows1 = mysqli_fetch_assoc($query1)) {
                        ?>
                        <tr>
                            <td><?php echo $rows1['upload_time']; ?></td>
                            <td><?php echo $rows1['last_edit_time']; ?></td>
                            <td><?php echo $rows1['title'] . '' . $rows1['category']; ?></td>
                            <td><?php echo $rows1['latest_filename'] . '' . $rows1['category']; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <?php
        // Record the viewing activity in the activities table
        $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
        $description = "You viewed information about Document ID $documentId";
        $status = 'Viewed';
        $timestamp = date('Y-m-d H:i:s');

        $activityQuery = "INSERT INTO `activities` (`user_id`, `description`, `timestamp`, `status`) VALUES ('$userId', '$description', '$timestamp', '$status')";
        mysqli_query($conn, $activityQuery);

        // Retrieve the name of the logged-in user
        $userQuery = mysqli_query($conn, "SELECT `name` FROM `user_form` WHERE `user_id` = '$userId'");
        if (mysqli_num_rows($userQuery) > 0) {
            $userRow = mysqli_fetch_assoc($userQuery);
            $userName = $userRow['name'];
        } else {
            // Default to a generic name if the user is not found
            $userName = "Unknown User";
        }

        // Record the viewing activity in the user_logs table along with the user's name
        $logQuery = "INSERT INTO `user_logs` (`user_id`, `name`, `action_description`, `timestamp`, `status`) VALUES ('$userId', '$userName', '$description', '$timestamp', '$status')";
        mysqli_query($conn, $logQuery);

    }
}

// View content
if (isset($_GET['viewDocumentId'])) {
    $documentId = $_GET['viewDocumentId'];

    // Fetch file path based on documentId
    $sql = "SELECT file_path FROM `document` WHERE `document_id` = $documentId";
    $result = mysqli_query($conn, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $filePath = "encryptedFiles/" . $row['file_path']; // Assuming the files are in the "encryptedFiles" folder

        // Check if the file exists
        if (file_exists($filePath)) {
            // Assuming 'content' is the field containing the content
            $content = file_get_contents($filePath);

            // Display the content
            echo $content;

            // Record the viewing activity in the activities table
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
            $description = "You viewed the content of document id $documentId";
            $status = 'Viewed';
            $timestamp = date('Y-m-d H:i:s');

            $activityQuery = "INSERT INTO `activities` (`user_id`, `description`, `timestamp`, `status`) VALUES ('$userId', '$description', '$timestamp', '$status')";
            mysqli_query($conn, $activityQuery);

            // Insert the viewing activity into the user_logs table
            $current_action = "View";
            $logDescription = "Viewed content of document id $documentId";
            $logStatus = 'Viewed';
            $logTimestamp = date('Y-m-d H:i:s');
            $userName = $_SESSION['user_name']; // Assuming you have the user's name stored in session

            $insertLogQuery = "INSERT INTO `user_logs` (`user_id`, `action_description`, `name`, `timestamp`, `current_action`, `status`) VALUES ('$userId', '$logDescription', '$userName', '$logTimestamp', '$current_action', '$logStatus')";
            mysqli_query($conn, $insertLogQuery);
        } else {
            // Handle error (e.g., file not found)
            echo "Error: File not found. Path: $filePath";
        }
    } else {
        // Handle error (e.g., document not found)
        echo "Error: Document not found.";
    }

    exit();
}


// delete file
if (isset($_POST['deleteDocumentId'])) {
    // Get the document ID to be deleted
    $documentId = $_POST['deleteDocumentId'];

    // Check if the document exists and if the logged-in user is the author
    $query = mysqli_query($conn, "SELECT * FROM `document` WHERE `document_id` = $documentId AND `user_id` = $thisUserId");
    if (mysqli_num_rows($query) > 0) {
        // Delete the document record from the database
        $deleteQuery = "DELETE FROM `document` WHERE `document_id` = $documentId";
        if (mysqli_query($conn, $deleteQuery)) {
            // If deletion is successful, you may want to also delete associated files from the server
            // Assuming the file path is stored in the database, retrieve it first
            $fileQuery = mysqli_query($conn, "SELECT file_path FROM `document` WHERE `document_id` = $documentId");
            if ($fileQuery && $fileRow = mysqli_fetch_assoc($fileQuery)) {
                $filePath = "encryptedFiles/" . $fileRow['file_path'];
                // Check if the file exists before attempting deletion
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file from the server
                }
            }

            // Log the deletion activity in the activities table
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
            $description = "You deleted document ID $documentId";
            $status = 'Deleted';
            $timestamp = date('Y-m-d H:i:s');

            $activityQuery = "INSERT INTO `activities` (`user_id`, `description`, `timestamp`, `status`) VALUES ('$userId', '$description', '$timestamp', '$status')";
            mysqli_query($conn, $activityQuery);

            // Retrieve the name of the logged-in user
            $userQuery = mysqli_query($conn, "SELECT `name` FROM `user_form` WHERE `user_id` = '$userId'");
            if(mysqli_num_rows($userQuery) > 0) {
                $userRow = mysqli_fetch_assoc($userQuery);
                $userName = $userRow['name'];
            } else {
                // Default to a generic name if the user is not found
                $userName = "Unknown User";
            }
            $currentAction="Deleted";
            $actionDescription="Deleted document ID $documentId";
            // Record the deletion activity in the user_logs table along with the user's name
            $logQuery = "INSERT INTO `user_logs` (`user_id`, `name`, `action_description`,`current_action` ,`timestamp`, `status`) VALUES ('$userId', '$userName', '$actionDescription', '$currentAction','$timestamp', '$status')";
            mysqli_query($conn, $logQuery);

            // Return a success response
            http_response_code(200);
            exit();
        } else {
            // Return an error response
            http_response_code(500);
            echo "Error: Unable to delete the document.";
            exit();
        }
    } else {
        // Return an unauthorized response
        http_response_code(403);
        echo "Error: You are not authorized to delete this document.";
        exit();
    }
}
?>
