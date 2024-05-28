<?php
include '../config/config.php';

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];

    // Validate and sanitize the filename to prevent directory traversal attacks
    $filename = urlencode($filename);

    // Construct the full path to the encrypted file (adjust the path accordingly)
    $encryptedFilePath = '../upload/encrypted_file/' . $filename;

    // Check if the file exists
    if (file_exists($encryptedFilePath)) {
        // Get the file's MIME type
        $mime = mime_content_type($encryptedFilePath);

        // Set the appropriate content type in the HTTP header
        header("Content-Type: $mime");
        header("Content-Disposition: inline; filename=" . $filename);

        // Output the encrypted content directly
        readfile($encryptedFilePath);
    } else {
        // Handle the case where the file is not found
        header("HTTP/1.0 404 Not Found");
        echo 'File not found';
    }
}
?>
