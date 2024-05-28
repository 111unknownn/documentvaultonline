<?php
// Get the file path from the query parameters
$folder_path = isset($_GET['folder_path']) ? $_GET['folder_path'] : '';

// Check if the file exists and is a file with the correct extension
if (file_exists($folder_path) && is_file($folder_path) && pathinfo($folder_path, PATHINFO_EXTENSION) === 'docx') {
    // Read the content of the file
    $file_content = file_get_contents($folder_path);

    // Return the content as the response
    echo $file_content;
} else {
    // If the file doesn't exist, is not a file, or has an invalid extension, return an error message
    if (!file_exists($folder_path)) {
        echo 'File not found.';
    } elseif (!is_file($folder_path)) {
        echo 'Path is not a file.';
    } else {
        echo 'Invalid file type. Only .docx files are supported.';
    }
}
?>
