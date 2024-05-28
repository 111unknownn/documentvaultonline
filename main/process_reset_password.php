<?php
// process_reset_password.php

@include '../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Password Reset Result</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="shortcut icon" href="../images/favicons.png" type="image/x-icon">
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="text-center">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
        $token = isset($_POST['token']) ? mysqli_real_escape_string($conn, $_POST['token']) : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Check if passwords match
        if ($newPassword !== $confirmPassword) {
            echo "<div class='alert alert-danger' role='alert'>Passwords do not match. Please try again.</div>";
            exit();
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateSql = "UPDATE user_form SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ? AND reset_token = ?";

        $stmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt, "sss", $hashedPassword, $email, $token);

        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='alert alert-success' role='alert'>Password reset successful. You can now <a href='../main/index'>login</a> with your new password.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error updating password. Please try again.</div>";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='alert alert-danger' role='alert'>Invalid request.</div>";
    }
    ?>
</div>

</body>

</html>
