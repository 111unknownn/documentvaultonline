<?php
@include '../config/config.php';

// Encryption function


// Function to encrypt the file content
function encryptFile($sourceFile, $key)
{
    // Generate a random initialization vector (IV)
    $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Read the file content
    $fileContent = file_get_contents($sourceFile);

    // Encrypt the file content using AES-256-CBC algorithm
    $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $key, 0, $iv);

    // Combine IV and ciphertext
    $encryptedData = $iv . $encryptedContent;

    // Write IV and ciphertext to the source file
    return file_put_contents($sourceFile, $encryptedData) !== false;
}




// Decryption function
function decryptFileName($encryptedFile, $key)
{
    // Check if the file exists
    if (!file_exists($encryptedFile)) {
        return false; // File not found
    }

    // Read the ciphertext from the file
    $ciphertext = file_get_contents($encryptedFile);

    // Check if reading the file failed
    if ($ciphertext === false) {
        return false; // Failed to read the file
    }

    // Decode the base64-encoded ciphertext
    $ciphertext = base64_decode($ciphertext);

    // Define the encryption cipher
    $cipher = "aes-256-cbc";

    // Determine the length of the initialization vector (IV)
    $ivlen = openssl_cipher_iv_length($cipher);

    // Extract IV, HMAC, and ciphertext from the decoded ciphertext
    $iv = substr($ciphertext, 0, $ivlen);
    $hmac = substr($ciphertext, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($ciphertext, $ivlen + $sha2len);

    // Decrypt the ciphertext
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);

    // Calculate the HMAC for integrity verification
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

    // Check if the calculated HMAC matches the received HMAC
    if (hash_equals($hmac, $calcmac)) {
        return $original_plaintext; // Return the decrypted data
    }

    return false; // Invalid HMAC, data integrity compromised
}

?>
