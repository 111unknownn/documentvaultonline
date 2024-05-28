<?php
// Include the set_cookie.php file


// Start session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Perform your login validation and authentication here
    // For example:
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Your login validation and authentication logic here...
    // For demonstration purposes, let's assume the login is successful
    $login_successful = true;
    $username = $email; // Assuming username is the same as email

    if ($login_successful) {
        // Call the setLoginCookie function to set the cookie
        setLoginCookie($username);

        // Redirect the user to the dashboard or any other page
        header("Location: index");
        exit();
    } else {
        // Display an error message or handle unsuccessful login
        // For example:
        $error = "Invalid email or password. Please try again.";
    }
}
?>
