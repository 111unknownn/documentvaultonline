<?php
// forgot_password

@include '../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

date_default_timezone_set('Asia/Manila');

function generateResetToken()
{
    return bin2hex(random_bytes(32));
}

function sendResetEmail($email, $token)
{
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'docuvault0@gmail.com';
    $mail->Password = 'uxcu qoui pmue vzhz'; // Replace with your actual password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom("docuvault0@gmail.com");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Password Reset Request";
    
    $resetLink = "http://localhost/docuvault/main/reset_password?email=$email&token=$token";

    $email_template = "<h5>Password Reset Request</h5>
                  <p>You have requested to reset your password. Click the link below to reset it:</p>
                  <a href='$resetLink'>$resetLink</a>";

    
    $mail->Body = $email_template;

    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['submit'])) {
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

    // Check if email exists in the database
    $select = "SELECT * FROM user_form WHERE email = ?";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result !== false && mysqli_num_rows($result) > 0) {
        $token = generateResetToken();
        
        // Store the reset token and expiration time in the database
        $updateSql = "UPDATE user_form SET reset_token='$token', reset_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email='$email'";
        mysqli_query($conn, $updateSql);

        // Send the reset email
        if (sendResetEmail($email, $token)) {
            // Display JavaScript alert when reset email is sent
            echo "<script>alert('The Link Sent in your Email to Reset Your Password!!!');</script>";
        } else {
            echo "Error sending reset email.";
        }
    } else {
        echo "Email not found in our records.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>DocuVault - Forgot Password</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../images/favicons.png" type="image/x-icon">
</head>
<style>
  .form input{
    width: 250px;
    height: 35px;
    background: transparent;
    border-bottom: 1px solid #ff7200;
    border-top: none;
    border-right: none;
    border-left: none;
    color: #fff;
    font-size: 15px;
    letter-spacing: 1px;
    margin-top: 30px;
    font-family: sans-serif;
}
.form{
    width: 250px;
    height: 360px;
}
</style>
<body>
    <div class="main">
        <div class="navbar">
            <div class="logo">
                <h2>DocuVault</h2>
            </div>
        </div>
        <div class="content">
            <h1>Document Management <span>System</span> </h1>
            <br>
            <p class="par">A Document Management System (DMS) is a software solution that facilitates <br> the
                organization, storage, and management of digital documents within an organization.<br>
                It streamlines document-centric processes, enhances collaboration, and ensures<br>
                version control, thereby improving efficiency and reducing the risk of information loss.
            </p>

            <div class="form">
                <form method="POST" action="../main/forgot_password">
                    <h2>Forgot Password</h2>
                    <div>
        <h4 style="text-align:center; color:gray; font-size:15px;">Please put your email address to send the link in your email to reset your password.</h4>
    </div>
                    <input type="email" name="email" placeholder="Enter Email Here" required>
                    <input type="submit" name="submit" value="Submit" class="btnn">
                </form>

                <p class="link">
                    Remember your password? <a href="../main/index" class="black-text">Login here</a>.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
