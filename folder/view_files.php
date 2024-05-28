<?php
include("../config/config.php");
// Connect to the database
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check if the action is set and equals 'view_files'
if (isset($_POST["action"]) && $_POST["action"] == "view_files") {
    // Retrieve folder name from the POST data
    $folder_name = isset($_POST["folder_name"]) ? $_POST["folder_name"] : '';

    // Define the path to the tmpFiles folder (relative path)
    $folder_path = '../../docuvault/folder/tmpFiles/' . $folder_name;

    // Initialize the file list HTML
    $file_list_html = '';

    // Check if the folder exists
    if (is_dir($folder_path)) {
        // Open the folder
        if ($handle = opendir($folder_path)) {
            // Iterate through files in the folder
            while (false !== ($entry = readdir($handle))) {
                // Exclude "." and ".." entries
                if ($entry != "." && $entry != "..") {
                    // Add the file to the file list HTML
                    $file_list_html .= '<li class="file-item"><a href="' . $folder_path . '/' . $entry . '" class="file-link" data-type="' . mime_content_type($folder_path . '/' . $entry) . '">' . $entry . '</a></li>';
                }
            }
            // Close the folder handle
            closedir($handle);
        }
    } else {
        // If the folder doesn't exist, display a message
        $file_list_html = '<li class="no-files">No files uploaded in this folder.</li>';
    }

    // Echo the file list HTML
    echo '<ul class="file-list">' . $file_list_html . '</ul>';
} else {
    // If the action is not set or not equal to 'view_files', display an error message
    echo 'Invalid request.';
}
?>
<style>
    /* CSS for file list */
    .file-list {
        list-style: none;
        padding: 0;
    }

    .file-item {
        margin-bottom: 5px;
    }

    .file-link {
        display: inline-block;
        padding: 5px 10px;
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 5px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .file-link:hover {
        background-color: #e0e0e0;
    }

    .no-files {
        color: #777;
    }
</style>

<!-- Modal for file actions -->
<div id="fileActionsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:white;">
                <h5 class="modal-title" style="color:black;">File Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Choose an action for the file:</p>
                <button id="viewFileBtn" class="btn btn-primary">View</button>
                <a id="downloadFileBtn" class="btn btn-secondary" download>Download</a>
            </div>
        </div>
    </div>
</div>


<!-- Modal for file content -->
<div id="fileContentModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:white;">
                <h5 class="modal-title" style="color:black;">File Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fileContent"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to handle file link clicks
    document.querySelectorAll('.file-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default behavior
            var fileUrl = this.href;
            var fileType = this.getAttribute('data-type');

            $('#fileActionsModal').modal('show');

            // View file button click handler
            document.getElementById('viewFileBtn').addEventListener('click', function () {
                // Handle different file types
                switch (fileType) {
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                        // For .docx files, open in Word
                        window.open('ms-word:ofe|u|' + fileUrl);
                        break;
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        // For .xlsx files, open in Excel
                        window.open('ms-excel:ofe|u|' + fileUrl);
                        break;
                    case 'application/pdf':
                        // For PDF files, open in default viewer
                        window.open(fileUrl, '_blank');
                        break;
                    default:
                        // For other file types, display in a modal
                        $.ajax({
                            url: fileUrl,
                            dataType: 'text',
                            success: function (data) {
                                $('#fileContent').text(data); // Set file content in modal
                                $('#fileContentModal').modal('show'); // Show content modal
                            },
                            error: function (xhr, status, error) {
                                console.error(error);
                                alert("An error occurred while fetching file content.");
                            }
                        });
                        break;
                }
                $('#fileActionsModal').modal('hide');
            });

            // Download file button click handler
            document.getElementById('downloadFileBtn').addEventListener('click', function () {
                window.location.href = fileUrl;
                $('#fileActionsModal').modal('hide');
            });
        });
    });

</script>