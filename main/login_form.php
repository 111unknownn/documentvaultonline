<?php
// Your existing PHP code
@include '../config/config.php';

session_start();

if (isset($_POST['submit'])) {
   $error = []; // Initialize an array to store errors

   $username = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
   $pass = isset($_POST['password']) ? $_POST['password'] : '';

   // Check if username and password are not empty
   if (empty($username) || empty($pass)) {
      $error['username_password'] = 'Username and password are required!';
   }

   // Only perform the database query if there are no errors
   if (empty($error)) {
      $select = "SELECT * FROM user_form WHERE email = ? ";
      $stmt = mysqli_prepare($conn, $select);
      mysqli_stmt_bind_param($stmt, "s", $username);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if (mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $hashedPassword = $row['password'];

         // Verify the hashed password
         if (password_verify($pass, $hashedPassword)) {
            $storedOtp = $row['otp'];

            // Check if OTP is provided and matches the stored OTP
            if (isset($_POST['otp']) && $_POST['otp'] == $storedOtp) {
               if ($row['verify_status'] == 0) {
                  // Update verify_status to 1 for an active account
                  $update_verify_status_query = "UPDATE user_form SET verify_status='1' WHERE email='$username' LIMIT 1";
                  $update_verify_status_query_run = mysqli_query($conn, $update_verify_status_query);

                  if (!$update_verify_status_query_run) {
                     $error['update_status'] = 'Failed to update verify status';
                  }
               }

               // Set the session variable $thisUserId to the value of 'userId'
               $_SESSION['user_id'] = $row['user_id'];

               // Log successful registration into user_logs table
               $userId = $row['user_id'];
               $name = $row['name'];
               $currentAction = "Registered";
               $actionDescription = "$name registered successfully";

               $logQuery = "INSERT INTO user_logs (user_id, name, current_action, action_description) VALUES (?, ?, ?, ?)";
               $logStmt = $conn->prepare($logQuery);
               $logStmt->bind_param("isss", $userId, $name, $currentAction, $actionDescription);
               $logStmt->execute();

               // Redirect to the appropriate page based on user type
               if ($row['user_type'] == 'admin') {
                  $_SESSION['admin_name'] = $row['name'];
                  header('location: ../panels/admin_page');
                  exit();
               } elseif ($row['user_type'] == 'user') {
                  $_SESSION['user_name'] = $row['name'];
                  header('location: ../panels/user_page');
                  exit();
               }
            } else {
               $error['otp'] = 'Incorrect OTP!';
            }
         } else {
            $error['username_password'] = 'Incorrect Username or password!';
         }
      } else {
         $error['username_password'] = 'Incorrect Username or password!';
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
    <title>Login Form</title>
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

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: calc(100% - 40px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
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

        .error-msg {
            color: red;
            font-size: 14px;
            text-align: left;
            margin-bottom: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .form-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #1877f2;
            text-decoration: none;
        }

        .form-link:hover {
            text-decoration: underline;
        }

        .form-btn-orange {
            background-color: orange;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .form-btn-orange:hover {
            background-color: darkorange;
        }

        /* Style the eye icon */
        .eye-icon {
            position: absolute;
            top: 38%;
            right: 28px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
        }
    </style>
</head>

<body>

<div class="form-container">
    <form action="" method="post">
        <h3>Login Now</h3>
        <h5 style="text-align:center;">Please Check Your Email To Confirm The Otp!</h5>
        <?php
        if (isset($_SESSION['status'])) {
            ?>
            <div class="alert-success">
                <h5><?= $_SESSION['status']; ?></h5>
            </div>
            <?php
            unset($_SESSION['status']);
        }
        ?>
        <div class="input-group">
            <input type="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" required placeholder="Enter your password">
            <?php
            if (isset($error['username_password'])) {
                echo '<span class="error-msg">' . $error['username_password'] . '</span>';
            }
            ?>
            <!-- Eye icon for password visibility toggle -->
            <img src="../images/close_eye.svg" class="eye-icon" onclick="togglePassword('password')" alt="Show Password">
            <!-- Error message element -->
        <div id="js-error-msg" class="error-msg" style="display: none;"></div>
        </div>
        <div class="input-group">
            <input type="text" id="otp" name="otp" maxlength="6" required placeholder="Enter OTP">
            <?php
            if (isset($error['otp'])) {
                echo '<span class="error-msg">' . $error['otp'] . '</span>';
            }
            ?>
        </div>
        <input type="submit" style="background-color:#ff7200; margin-top:10px;" name="submit" value="Login Now" class="form-btn-orange">
        <?php
        if (isset($error['update_status'])) {
            echo '<span class="error-msg">' . $error['update_status'] . '</span>';
        }
        ?>
        <a href="resend-email-verification" style="color:#ff7200;" class="form-link">Resend OTP</a>
    </form>
</div>


<script>
    // Function to toggle password visibility
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

    function disableSpace(event) {
        if (event.keyCode === 32) {
            event.preventDefault();
            showJsError("Spacebar input is disabled for the password.");
        }
    }

    // Add event listener to the password field to disable space input
    document.getElementById("password").addEventListener("keydown", disableSpace);

    // Function to restrict OTP input to numbers only
    document.getElementById('otp').addEventListener('input', function (event) {
        var input = event.target;
        input.value = input.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        if (input.value.length > 6) {
            input.value = input.value.slice(0, 6); // Restrict length to 6 characters
        }
    });

    // Function to open modal
    function openModal() {
        document.getElementById("myModal").style.display = "block";
    }

    // Function to close modal
    function closeModal() {
        document.getElementById("myModal").style.display = "none";
    }
</script>

</body>

</html>