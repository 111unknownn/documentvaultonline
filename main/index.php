<?php
@include '../config/config.php';

session_start();



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

// Add this line at the beginning of your script
date_default_timezone_set('Asia/Manila'); // Set your desired timezone

// Constants
define('MAX_FAILED_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 60); // 5 minutes in seconds



    function sendOtp($email, $otp)
    {

        
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0; // Disable SMTP debugging
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'documentvaultonline@gmail.com'; // Your Gmail email address
        $mail->Password = 'tlcd ninu ctog chqd'; // Use the generated App Password here
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
        $mail->Port = 465;
        
        $mail->setFrom("documentvaultonline@gmail.com"); // Set the sender address and name
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = "OTP FOR LOGIN";
    

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

// After successful login
if (isset($_POST['submit'])) {
    $error = []; // Initialize an array to store errors

    $username = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';
    $otp = isset($_POST['otp']) ? $_POST['otp'] : '';

    // Check if username and password are not empty
    if (empty($username) || empty($pass)) {
        $error[] = 'Username and password are required!';
    }

    // Only perform the database query if there are no errors
    if (empty($error)) {
        $select = "SELECT * FROM user_form WHERE email = ?";
        $stmt = mysqli_prepare($conn, $select);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        // Check for query execution success
        if ($stmt) {
            $result = mysqli_stmt_get_result($stmt);

            // Check if there are rows in the result
            if ($result !== false) {
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    // Check if the account is enabled
                    if ($row['status'] == 'enabled') {
                        // Check if the account is locked
                        if ($row['locked_until'] !== null && time() < strtotime($row['locked_until'])) {
                            $error[] = 'Account is temporarily locked. Please try again later after 1 min.';
                        } else {
                            $hashedPassword = $row['password'];

                            // Verify the hashed password
                            if (password_verify($pass, $hashedPassword)) {
                                // Reset failed login attempts on successful login
                                resetFailedLoginAttempts($row['user_id']);

                                if ($row['verify_status'] == 1) {
                                    // Generate a random OTP
                                    $otp = strval(mt_rand(100000, 999999));

                                    // Set the OTP expiry time (e.g., 5 minutes from now)
                                    $otp_expiry = date('Y-m-d H:i:s', time() + 300); // 300 seconds = 5 minutes

                                    // Send OTP email and update database
                                    sendOtp($username, $otp);

                                    $updateSql = "UPDATE user_form SET otp=?, otp_expiry=?, status='enabled' WHERE user_id=?";
                                    $stmt = mysqli_prepare($conn, $updateSql);
                                    
                                    // Check if the prepared statement was created successfully
                                    if ($stmt) {
                                        mysqli_stmt_bind_param($stmt, "ssi", $otp, $otp_expiry, $row['user_id']);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);
                                    } else {
                                        // Handle the case where the prepared statement creation failed
                                        echo "Error creating prepared statement: " . mysqli_error($conn);
                                    }
                                    
                                    // Add this line for debugging
                                    echo "SQL Query: $updateSql";
                                    
                                    // Set session variables
                                    $_SESSION['temp_user'] = ['id' => $row['user_id'], 'otp' => $otp];

                                    // Redirect the user for OTP verification
                                    header("Location: otp_verification");
                                    exit();
                                } else {
                                    // Existing code for handling verified users
                                    if ($row['user_type'] == 'admin') {
                                        $_SESSION['admin_id'] = $row['id'];
                                        $_SESSION['admin_name'] = $row['name'];
                                        // Update user status to 'online'
                                        $updateStatusSql = "UPDATE user_form SET status = 'enable' WHERE user_id=" . $row['user_id'];
                                        mysqli_query($conn, $updateStatusSql);
                                        header("Location:../panels/admin_page");
                                        exit();
                                    } elseif ($row['user_type'] == 'user') {
                                        $_SESSION['user_id'] = $row['id'];
                                        $_SESSION['user_name'] = $row['name'];
                                        // Update user status to 'online'
                                        $updateStatusSql = "UPDATE user_form SET status = 'enable' WHERE user_id=" . $row['user_id'];
                                        mysqli_query($conn, $updateStatusSql);
                                        header("Location:../panels/user_page");
                                        exit();
                                    }
                                }
                            } else {
                                // Increment failed login attempts on incorrect password
                                incrementFailedLoginAttempts($row['user_id']);

                                $remainingAttempts = MAX_FAILED_LOGIN_ATTEMPTS - $row['failed_login_attempts'];

                                if ($row['failed_login_attempts'] >= MAX_FAILED_LOGIN_ATTEMPTS) {
                                    // Lock the account if the threshold is reached
                                    lockAccount($row['user_id']);
                                    $error[] = 'Account locked. Please try again later after 5 mins.';
                                } else {
                                    $error[] = 'Incorrect username or password. ' . $remainingAttempts . ' attempts remaining. Please try again.';
                                    
                                    // Log the failed login attempt
                                    logFailedLogin($username);
                                }
                            }
                        }
                    } else {
                        // User account is disabled
                        $error[] = 'Your account has been disabled. Please contact the administrator!';
                    }
                } else {
                    $error[] = 'Incorrect username or password. Please try again.';
                    
                    // Log the failed login attempt
                    logFailedLogin($username);
                }
            } else {
                // Handle the case where there was an issue with the query result
                $error[] = 'Error fetching results from the database.';
            }
        } else {
            // Handle the case where there was an issue with the query execution
            $error[] = 'Error executing the database query.';
        }
    }
}


// Function to log failed login attempts
function logFailedLogin($username) {
    global $conn;

    // Prepare and execute the query to fetch user_id and name based on username
    $selectQuery = "SELECT user_id, name FROM user_form WHERE email = ?";
    $stmt = mysqli_prepare($conn, $selectQuery);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $userId = $row['user_id'];
    $name = $row['name'];

    // Insert the failed login attempt into the user_logs table
    $insertQuery = "INSERT INTO user_logs (user_id, name, current_action) VALUES (?, ?, 'Putting Wrong Username or Password!')";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "is", $userId, $name);
    mysqli_stmt_execute($stmt);
}



// Function to reset failed login attempts
function resetFailedLoginAttempts($userId)
{
    global $conn;

    // Replace this with your actual database update query
    $updateQuery = "UPDATE user_form SET failed_login_attempts = 0 WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    // Use prepared statements to prevent SQL injection
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Function to increment failed login attempts
function incrementFailedLoginAttempts($userId)
{
    global $conn;

    // Replace this with your actual database update query
    $updateQuery = "UPDATE user_form SET failed_login_attempts = failed_login_attempts + 1 WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    // Use prepared statements to prevent SQL injection
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Function to lock user account
function lockAccount($userId)
{
    global $conn;

    // Calculate the lockout time based on the LOCKOUT_DURATION constant
    $lockUntil = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);

    // Replace this with your actual database update query
    $updateQuery = "UPDATE user_form SET locked_until = ?, failed_login_attempts = 0 WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    // Use prepared statements to prevent SQL injection
    mysqli_stmt_bind_param($stmt, "si", $lockUntil, $userId);
    mysqli_stmt_execute($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>

<!-- HTML code with added classes -->
<!DOCTYPE html>
<html lang="en">

<head>
    <title>DocuVault - login or sign up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <link rel="stylesheet" href="../style.css">
    
</head>

<body>
    <div class="main">
        <div class="navbar">
            <div class="logo" style="display: flex; align-items: center;">
                <img src="../images/logo.webp" alt="" style="max-width: 170px; max-height: 50px; margin-top:10px;">
                <span style="font-size: 1.5em; margin-left: 15px; font-weight:bold; margin-top:10px; margin-right:430px;">DocuVault</span>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="index">HOME</a></li>
                    <li><a href="about">ABOUT</a></li>
                    <li><a href="services">SERVICES</a></li>
                </ul>
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
                <form method="POST" action="../main/index">
                    <h2>Login Here</h2>
                    <input type="email" name="email" placeholder="Enter Email Here" required>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="password-input" placeholder="Enter Password Here" maxlength="16" required>
                        <div class="toggle-password hide" onclick="togglePasswordVisibility()"></div>
                    </div>
                    <!-- Error messages will be displayed here -->
                    <div id="js-error-msg" class="error-msg" style="display: none;">
                        <?php
                        if (isset($error)) {
                            foreach ($error as $errMsg) {
                                echo $errMsg . '<br>';
                            }
                            // Add a message to contact the administrator
                            echo 'Please contact the administrator for further assistance.';
                        }
                        ?>
                    </div>
                    <input type="submit" name="submit" value="Login Now" class="btnn">
                    <!-- Forgot Password link -->
                    <p class="link">
                        <a href="../main/forgot_password" class="black-text">Forgot Password?</a>
                    </p>
                    <!-- Sign up link -->
                    <p class="link">
                        Don't have an account?<br>
                        <a href="../main/register_form" id="signUpBtn" class="black-text">Sign up</a> here</a>
                    </p>
                </form>
                <!-- Administrator logo -->
                <div class="admin_logo fixed">
                    <a href="#" id="contactAdminLogo" data-toggle="modal" data-target="#contactAdminModal">
                        <img src="../images/admin1.svg" alt="Contact Administrator" style="width: 30px; height: auto; filter: brightness(0) invert(1);">
                    </a>
                </div>
            </div>


            <!-- The Contact Administrator Modal -->
            <div class="modal fade" id="contactAdminModal" tabindex="-1" role="dialog" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="color:black;" id="contactAdminModalLabel">Contact Administrator</h5>
                            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <img src="../images/close.svg" alt="Close" style="width: 24px; height: 24px;">
                            </a>
                        </div>
                        <div class="modal-body" style="color:black;">
                            <p>Your account has been disabled. Please contact the administrator to enable it again.</p>
                            <p>To contact the administrator, please provide a brief explanation of why your account was disabled:</p>
                            <form action="mailto:documentvaultonline@gmail.com" method="post" enctype="text/plain">
                                <textarea name="message" rows="4" cols="50" required></textarea><br>
                                <input type="submit" value="Send" class="btn btn-primary">
                            </form>
                            <p>You can also directly email the administrator at <a href="mailto:documentvaultonline@gmail.com">documentvaultonline@gmail.com</a> to enable your account.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>

// JavaScript function to show an error message
function showJsError(message) {
    var jsErrorMsgElement = document.getElementById('js-error-msg');
    jsErrorMsgElement.innerHTML = message;
    jsErrorMsgElement.style.display = 'block';
}

// PHP-generated error messages will still be shown
<?php
if (isset($error)) {
    echo 'showJsError("';
    foreach ($error as $errMsg) {
        echo $errMsg . '<br>';
    }
    echo '");';
}
?>

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('hide');
        toggleIcon.classList.add('show');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('show');
        toggleIcon.classList.add('hide');
    }
}

function disableSpace(event) {
    if (event.keyCode === 32) {
        event.preventDefault();
        showJsError("Spacebar input is disabled for the password.");
    }
}

// Add event listeners to password fields to disable space input
document.getElementById("password").addEventListener("keydown", disableSpace);


function openModal() {
    document.getElementById("myModal").style.display = "block";
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

    </script>
</body>

</html>

