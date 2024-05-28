<?php
@include '../config/config.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Define the sendOtpEmail function
function sendOtpEmail($name, $email, $otp) {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; // Disable SMTP debugging
    $mail->isSMTP(); // Send using SMTP
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'documentvaultonline@gmail.com'; // Your Gmail email address
    $mail->Password = 'tlcd ninu ctog chqd'; // Use the generated App Password here
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
    $mail->Port = 465;


    $mail->setFrom("documentvaultonline@gmail.com");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "OTP RESEND";

    $email_template = "<h2>Resend Your OTP</h2>
                        <h5>Use the OTP below to verify your email address</h5>
                        <br/><br/>
                        <p>Your OTP: $otp</p>
                        
    ";
    $mail->Body = $email_template;

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['resend'])) {
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    // Check if the email exists in the database
    $select = "SELECT * FROM user_form WHERE email = ? ";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $otp = $row['otp'];

        // Send a new OTP email
        sendOtpEmail($row['name'], $email, $otp);
        $_SESSION['status'] = 'New OTP has been sent to your email. Please check your email for instructions.';
    } else {
        $_SESSION['status'] = 'Email not found. Please check your email address.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend OTP</title>
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.5)50%,rgba(0,0,0,0.5)50%), url(../images/dms.jpg);
            background-position: center;
            background-size: cover;
    
        }

        .form-container {
            width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff7200;
        }

        .input-group {
            margin-bottom: 20px;
        }

        input[type="email"] {
            width: calc(100% - 40px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus {
            border-color: #1877f2;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #1877f2;
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #166fe5;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        p a {
            color: #ff7200;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form action="" method="POST">
            <h3>Resend OTP</h3>
            <?php
            if (isset($_SESSION['status'])) {
                echo '<div class="alert-success">';
                echo '<h5>' . $_SESSION['status'] . '</h5>';
                echo '</div>';
                unset($_SESSION['status']);
            }
            ?>
            <div class="input-group">
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <input type="submit" style="background-color:#ff7200;" name="resend" value="Resend OTP">
            <p>Remember your OTP? <a href="login_form.php">Login Now</a></p>
        </form>
    </div>
</body>

</html>
