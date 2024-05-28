<?php
// Function to get the size of a folder
function get_folder_size($folder_name) {
    $total_size = 0;
    if (is_dir($folder_name)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder_name)) as $file) {
            $total_size += $file->getSize();
        }
    }
    return format_size_units($total_size);
}

// Function to format size in human-readable units
function format_size_units($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

// Database configuration
include ('../config/config.php');
session_start();

// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (isset($_POST["action"])) {
    if ($_POST["action"] == "fetch") {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Redirect or handle unauthorized access
            exit("User not logged in");
        }

        // Fetch folders associated with the logged-in user
        $user_id = $_SESSION['user_id'];
        $query = "SELECT folder_name FROM user_folders WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $output = '
            <div class="table-responsive" id="folder_table">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <table class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Folder Name</th>
                                        <th>Total Files</th>
                                        <th>Size</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $folder_name = $row['folder_name'];

                    // Check if the directory exists
                    if (is_dir($folder_name)) {
                        $files = scandir($folder_name);

                        // Check if scandir succeeded
                        if ($files !== false) {
                            $total_files = count($files) - 2; // Subtracting 2 for '.' and '..'
                        } else {
                            $total_files = 0;
                            // Log the error or handle it as needed
                        }

                        $size = get_folder_size($folder_name);
                    } else {
                        $total_files = 0;
                        $size = "N/A";
                        // Log the error or handle it as needed
                    }

                    $output .= '
                    <tr>
                        <td>' . htmlspecialchars($folder_name, ENT_QUOTES, 'UTF-8') . '</td>
                        <td>' . $total_files . '</td>
                        <td>' . $size . '</td>
                        <td>
                            <button type="button" class="btn btn-xs update" style="background-color: #D59D80;" data-name="' . htmlspecialchars($folder_name, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-xs delete" style="background-color: #D59D80;" data-name="' . htmlspecialchars($folder_name, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-trash-alt"></i></button>
                            <button type="button" class="btn btn-xs upload" style="background-color: #D59D80;" data-name="' . htmlspecialchars($folder_name, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-upload"></i></button>
                            <button type="button" class="btn btn-xs view_files" style="background-color: #D59D80;" data-name="' . htmlspecialchars($folder_name, ENT_QUOTES, 'UTF-8') . '"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>';
                }
            } else {
                $output .= '
                <tr>
                    <td colspan="4">No folders found</td>
                </tr>';
            }
            
            $output .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>';
            
            echo $output;
        } else {
            // Handle query error
            echo "Error fetching folders.";
        }
    }

   


    include '../config/config.php';

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        exit("User not logged in");
    }
    
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Check if the action is defined
    if (isset($_POST["action"])) {;


// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
    
        // Check if the connection is successful
        if (!$conn) {
            exit("Database connection failed: " . mysqli_connect_error());
        }
    
        // Define the action based on the POST value
        switch ($_POST["action"]) {
            case "create":
                // Check if the folder name is provided
                if (!empty($_POST["folder_name"])) {
                    $folder_name = $_POST["folder_name"];
                    // Check if the folder already exists in the database
                    $check_query = "SELECT * FROM user_folders WHERE user_id = $user_id AND folder_name = '$folder_name'";
                    $check_result = mysqli_query($conn, $check_query);
                    if (mysqli_num_rows($check_result) > 0) {
                        echo 'Folder Already Created';
                    } else {
                        // Insert folder name into the database
                        $insert_query = "INSERT INTO user_folders (user_id, folder_name) VALUES ($user_id, '$folder_name')";
                        if (mysqli_query($conn, $insert_query)) {
                            // Create the folder in the file system
                            if (!file_exists($folder_name)) {
                                mkdir($folder_name, 0777, true);
                                echo 'Folder Created';
                            } else {
                                echo 'Folder Already Created';
                            }
                        } else {
                            echo 'Error creating folder: ' . mysqli_error($conn);
                        }
                    }
                } else {
                    echo 'Folder name not provided';
                }
                break;
    
            case "change":
                // Check if both old and new folder names are provided
                if (!empty($_POST["old_name"]) && !empty($_POST["folder_name"])) {
                    $old_name = $_POST["old_name"];
                    $new_name = $_POST["folder_name"];
                    // Check if the new folder name already exists in the database
                    $check_query = "SELECT * FROM user_folders WHERE user_id = $user_id AND folder_name = '$new_name'";
                    $check_result = mysqli_query($conn, $check_query);
                    if (mysqli_num_rows($check_result) > 0) {
                        echo 'Folder Name Already Exists';
                    } else {
                        // Update the folder name in the database
                        $update_query = "UPDATE user_folders SET folder_name = '$new_name' WHERE user_id = $user_id AND folder_name = '$old_name'";
                        if (mysqli_query($conn, $update_query)) {
                            // Rename the folder in the file system
                            if (!file_exists($new_name)) {
                                rename($old_name, $new_name);
                                echo 'Folder Name Changed';
                            } else {
                                echo 'Folder Already Created';
                            }
                        } else {
                            echo 'Error updating folder name: ' . mysqli_error($conn);
                        }
                    }
                } else {
                    echo 'Folder name(s) not provided';
                }
                break;
    
            case "delete":
                // Check if the folder name is provided
                if (!empty($_POST["folder_name"])) {
                    $folder_name = $_POST["folder_name"];
                    // Delete the folder from the database
                    $delete_query = "DELETE FROM user_folders WHERE user_id = $user_id AND folder_name = '$folder_name'";
                    if (mysqli_query($conn, $delete_query)) {
                        // Delete the folder and its contents from the file system
                        $files = scandir($folder_name);
                        foreach ($files as $file) {
                            if ($file !== '.' && $file !== '..') {
                                unlink($folder_name . '/' . $file);
                            }
                        }
                        if (rmdir($folder_name)) {
                            echo 'Folder Deleted';
                        }
                    } else {
                        echo 'Error deleting folder: ' . mysqli_error($conn);
                    }
                } else {
                    echo 'Folder name not provided';
                }
                break;
    
            default:
                echo '';
                break;
        }
    
        // Close the database connection
        mysqli_close($conn);
    } else {
        echo 'Action not defined';
    }


    
    if($_POST["action"] == "fetch_files")
{
    $file_data = scandir($_POST["folder_name"]);
    $output = '
    <table class="table table-bordered table-striped">
        <tr>
            <th>File Name</th>
            <th>Delete</th>
        </tr>
    ';

    foreach($file_data as $file)
    {
        if($file === '.' or $file === '..')
        {
            continue;
        }
        else
        {
            $path = $_POST["folder_name"] . '/' . $file;
            $output .= '
            <tr>
                <td><a href="" class="file_container" data-folder_name="'.$_POST["folder_name"].'"  data-file_name="'.$file.'" data-path="'.$path.'">'.$file.'</a></td>
                <td><button name="remove_file" class="remove_file btn btn-danger btn-xs" id="'.$path.'">Remove</button></td>
            </tr>
            ';
        }
    }
    $output .='</table>';
    echo $output;
}


    if ($_POST["action"] == "remove_file") {
        if (file_exists($_POST["path"])) {
            unlink($_POST["path"]);
            echo 'File Deleted';
        }
    }

    if ($_POST["action"] == "change_file_name") {
        $old_name = $_POST["folder_name"] . '/' . $_POST["old_file_name"];
        $new_name = $_POST["folder_name"] . '/' . $_POST["new_file_name"];
        if (rename($old_name, $new_name)) {
            echo 'File name change successfully';
        } else {
            echo 'There is an error';
        }
    }
}
?>
