<?php
// Function to decrypt a file using a custom encryption method
function decryptFile($sourceFile, $key)
{
    // Read the encrypted data from the source file
    $encryptedData = file_get_contents($sourceFile);

    if ($encryptedData === false) {
        echo 'Error reading encrypted file.';
        exit;
    }

    // Extract IV and ciphertext
    $iv = substr($encryptedData, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $ciphertext = substr($encryptedData, openssl_cipher_iv_length('aes-256-cbc'));

    // Decrypt the ciphertext using AES-256-CBC algorithm
    $decryptedContent = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);

    if ($decryptedContent === false) {
        echo 'Decryption failed. Check the encryption key or file integrity.';
        exit;
    }

    return $decryptedContent;
}

// Check if the file parameter is set
if (isset($_GET['file'])) {
    $fileToDownload = $_GET['file'];

    // Specify the path for the encrypted file
    $encryptedFilePath = '../upload/encrypted_file/' . $fileToDownload;

    // Get the encryption key from the file
    $encryptionKeyFilePath = '../../key/encryption_key.txt';
    $encryptionKey = file_get_contents($encryptionKeyFilePath);

    if ($encryptionKey === false) {
        echo 'Encryption key not found.';
        exit;
    }

    // Retrieve sender_id and recipient_id from the database
    $fileInfo = getFileInfoFromDatabase($fileToDownload);

    // Check if the user is logged in
    session_start();
    if (isset($_SESSION['user_id'])) {
        $loggedInUserId = $_SESSION['user_id'];

        // Check if the logged-in user is the recipient
        $isRecipient = ($loggedInUserId == $fileInfo['recipient_id']);

        // Debugging: Output file info and decryption key
        echo '<pre>';
        echo 'File Info: ' . print_r($fileInfo, true) . PHP_EOL;
        echo 'Decryption Key: ' . htmlspecialchars($encryptionKey) . PHP_EOL;
        echo '</pre>';

        // If the user is the recipient, decrypt the file
        if ($isRecipient) {
            $decryptedContent = decryptFile($encryptedFilePath, $encryptionKey);

            // Debugging: Output the decrypted content
            echo '<pre>';
            echo 'Decrypted Content: ' . htmlspecialchars($decryptedContent);
            echo '</pre>';

            if ($decryptedContent === false) {
                echo 'Decryption failed. Check the encryption key or file integrity.';
                exit;
            }

            // Output the decrypted content for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fileToDownload) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($decryptedContent));

            echo $decryptedContent;

            // Exit to stop further execution
            exit;
        } else {
            // If the user is not the intended recipient, output encrypted content
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($fileToDownload) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($encryptedFilePath));

            readfile($encryptedFilePath);

            // Exit to stop further execution
            exit;
        }
    } else {
        // Redirect to the login page if the user is not logged in
        header("Location: ../index");
        exit;
    }
} else {
    echo 'Invalid request.';
    exit;
}

// Implement this function to retrieve sender_id and recipient_id from your database
function getFileInfoFromDatabase($fileToDownload)
{
    // Replace this with your actual logic to fetch sender_id and recipient_id from the database
    // For example, you might have a database query to get this information based on the file name.
    // Make sure to handle SQL injection properly in your production code.
    include ('../config/config.php');
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    $query = "SELECT sender_id, recipient_id FROM upload_file WHERE original_filename = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $fileToDownload);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $senderId, $recipientId);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return ['sender_id' => $senderId, 'recipient_id' => $recipientId];
}
?>