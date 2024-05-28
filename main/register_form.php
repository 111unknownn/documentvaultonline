<?php
@include '../config/config.php';

session_start();

// Define the function to generate a 4-digit PIN
function generatePIN()
{
    return mt_rand(1000, 9999); // Generate a random number between 1000 and 9999
}

$pin = generatePIN();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';


$success_message='';
function sendOtp($email, $otp)
{
   $mail = new PHPMailer(true);
   $mail->SMTPDebug = 0; // Disable SMTP debugging
   $mail->isSMTP(); // Send using SMTP
   $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
   $mail->SMTPAuth = true; // Enable SMTP authentication
   $mail->Username = 'documentvaultonline@gmail.com'; // Your Gmail email address
   $mail->Password = 'tlcd ninu ctog chqd';  // Use the generated App Password here
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
   $mail->Port = 465;

   $mail->setFrom("documentvaultonline@gmail.com");
   $mail->addAddress($email);

   $mail->isHTML(true);
   $mail->Subject = "OTP FOR REGISTRATION";

   $email_template = "<h5>Use This OTP to Verify Your Email Address</h5>
                           <br/><br/>                        
                        Your OTP is: $otp
                        <h2>DOCU VAULT MANAGEMENT</h2>";
                        $email_template = "
                        <html>
                        <head>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                }
                                .container {
                                    max-width: 600px;
                                    margin: 0 auto;
                                    padding: 20px;
                                    border: 1px solid #ccc;
                                    border-radius: 5px;
                                    background-color: #f9f9f9;
                                }
                                .header {
                                    background-color: darkorange;
                                    color: #fff;
                                    padding: 10px;
                                    text-align: center;
                                    border-radius: 5px 5px 0 0;
                                }
                                .content {
                                    padding: 20px;
                                }
                                .otp {
                                    font-size: 24px;
                                    font-weight: bold;
                                    color:darkorange;
                                }
                                .logo {
                                    max-width: 100px;
                                    height: auto;
                                    margin: 0 auto;
                                    display: block;
                                }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                   
                                </div>
                                <div class='content'>
                                    <p>Use the following OTP to login:</p>
                                    <p class='otp'>$otp</p>
                                    <h2>DOCU VAULT MANAGEMENT</h2>
                                </div>
                            </div>
                        </body>
                        </html>
                    "; 
                    
                    
                    
                        $mail->Body = $email_template;
                    
                        try {
                            $mail->send();
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                    }

if (isset($_POST['submit'])) {
   $error = []; // Initialize an array to store errors

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = $_POST['password'];
   $cpass = $_POST['cpassword'];
   $user_type = 'user';
   $otp = strval(mt_rand(100000, 999999)); // Generate a random OTP

   sendOtp($email, $otp); // Send the OTP to the user

   // Check if the password and confirm password match
   if ($password !== $cpass) {
      $error[] = 'Passwords do not match!';
   } else {
      // Hash the password using bcrypt
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $select = "SELECT * FROM user_form WHERE email = '$email'";
      $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        // Check if the password and confirm password match
        if ($password !== $cpass) {
            $error[] = 'Passwords do not match!';
        } else {
            // Hash the password using bcrypt
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the user into the database
            $insert = "INSERT INTO user_form (name, email, password, otp, user_type, pin, user_id) VALUES ('$name', '$email', '$hashedPassword', '$otp', '$user_type', '$pin', NULL)";
            $query_success = mysqli_query($conn, $insert);

            if ($query_success) {
                // Show success message
                $success_message = "Registration successful! Check your email for the OTP.";
            } else {
                $error[] = 'Error in query execution: ' . mysqli_error($conn);
            }
        }
    }
}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <title>Register Form</title>
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
            position: relative; /* Added for positioning eye icon */
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 40px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #1877f2;
        }

        .eye-icon {
            position: absolute;
            top: 35%;
            right: 28px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
        }

        select[name="user_type"] {
            display: none;
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

        /* Hide the modal by default */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        /* Modal content */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>

<body>
<div class="form-container">
    <!-- Success message -->
<?php if ($success_message): ?>
    <p style="color: green; text-align: center;"><?php echo $success_message; ?></p>
    <?php
        // Redirect to login_form.php after 5 seconds
        header("refresh:3; url=login_form.php");
        exit();
    ?>
<?php endif; ?>

    <h3>Create an Account</h3>
    <form action="" method="post" onsubmit="return validatePasswordAndAgreement()">
        <div class="input-group">
            <input type="text" name="name" required placeholder="Full Name">
            <span id="nameError" class="error-message"></span>
        </div>
        <div class="input-group">
            <input type="email" name="email" required placeholder="Email Address">
            <span id="emailError" class="error-message">
                <?php if (isset($error) && in_array('User already exists!', $error)) echo 'User already exists!'; ?>
            </span>
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" required placeholder="Password"
                pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]).+$" required maxlength="16"
                minlength="8">
           <img src="../images/close_eye.svg" class="eye-icon" onclick="togglePassword('password')"
                alt="Show Password">
            <span id="passwordError" class="error-message"></span>
        </div>
        <div class="input-group">
            <input type="password" id="cpassword" name="cpassword" required placeholder="Confirm Password"
                maxlength="16">
            <img src="../images/close_eye.svg" class="eye-icon" onclick="togglePassword('cpassword')"
                alt="Show Password">
            <span id="cpasswordError" class="error-message"></span>
        </div>
        <input type="checkbox" id="termsCheckbox" required>
        <label for="termsCheckbox">I agree to the <a href="#" onclick="openModal()">Terms and Conditions</a></label>
        <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Terms and Conditions</h2>

<ol>
    <li>
        <h3>Acceptance of Terms</h3>
        <p>
            By accessing this website, you agree to be bound by these terms and conditions, all applicable laws, and regulations, and agree that you are responsible for compliance with any applicable local laws.
        </p>
    </li>

    <li>
        <h3>Use of Our Services</h3>
        <p>
            Our DMS is designed to provide document management and storage solutions. You agree to use our services only for lawful purposes and in compliance with all applicable laws and regulations.
        </p>
    </li>

    <li>
        <h3>User Accounts</h3>
        <p>
            To access certain features of our website, you may be required to register and create a user account. You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer.
        </p>
    </li>

    <li>
        <h3>Intellectual Property</h3>
        <p>
            All content included on this website, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of our company and protected by international copyright laws.
        </p>
    </li>

    <li>
        <h3>Governing Law</h3>
        <p>
            These terms and conditions shall be governed by and construed in accordance with the laws of Philippines, and any disputes relating to these terms and conditions will be subject to the exclusive jurisdiction of the courts of Jurisdiction.
        </p>
    </li>


    </div>
    </div>
    <input type="submit" style="background-color:#ff7200; margin-top:15px;" name="submit" value="Register Now"
            class="form-btn-orange">
        <p style="color:gray; font-size:small;">Password should contain at least 8 characters including uppercase, lowercase, numbers, and special characters.</p>
        <p>Already Have an Account? <a href="index">Login Now</a></p>
    </form>

   
</div>

<script>
    function togglePassword(inputId) {
        var input = document.getElementById(inputId);
        var eyeIcon = input.nextElementSibling;
        if (input.type === "password") {
            input.type = "text";
            eyeIcon.src = "../images/open_eye.svg";
        } else {
            input.type = "password";
            eyeIcon.src = "../images/close_eye.svg";
        }
    }

    function validatePasswordAndAgreement() {
        var password = document.getElementById("password").value;
        var cpassword = document.getElementById("cpassword").value;
        var errorMessage = '';

        if (password !== cpassword) {
            errorMessage += "Passwords do not match!\n";
            document.getElementById("cpasswordError").textContent = "Passwords do not match!";
        } else {
            document.getElementById("cpasswordError").textContent = "";
        }

        var pattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]).+$/;
        if (!pattern.test(password)) {
            errorMessage += "Password must contain one special character and uppercase letter!\n";
            document.getElementById("passwordError").textContent = "Password must contain one special character and uppercase letter!";
        } else {
            document.getElementById("passwordError").textContent = "";
        }

        if (!document.getElementById("termsCheckbox").checked) {
            errorMessage += "Please agree to the Terms and Conditions!";
            alert("Please agree to the Terms and Conditions!");
        }

        if (errorMessage !== '') {
            return false;
        }

        return true;
    }

    function disableSpace(event) {
        if (event.keyCode === 32) {
            event.preventDefault();
            document.getElementById("passwordError").textContent = "Spacebar input is disabled for the password.";
        }
    }

    // Add event listeners to password fields to disable space input
    document.getElementById("password").addEventListener("keydown", disableSpace);
    document.getElementById("cpassword").addEventListener("keydown", disableSpace);

    function openModal() {
        document.getElementById("myModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }
</script>

</body>

</html>
