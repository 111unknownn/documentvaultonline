<?php include('view_modal.php'); ?>



<?php
if (isset($_GET['file'])) {
    // Get the file path from the URL parameter
    $file = $_GET['file'];

    // Check if the file exists
    if (file_exists($file)) {
        // Set the appropriate content type for the file
        $content_type = mime_content_type($file);
        header('Content-Type: ' . $content_type);

        // Open and output the file
        readfile($file);
    } else {
        // File not found, handle the error
        echo 'File not found';
    }
} else {
    // No file parameter provided, handle the error
    echo 'File parameter not provided';
}
