
<?php
@include '../config/config.php';
require 'login.php';

session_start();

if (isset($_POST['submit'])) {
   $error = []; // Initialize an array to store errors

   $username = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
   $pass = isset($_POST['password']) ? $_POST['password'] : '';

   // Check if username and password are not empty
   if (empty($username) || empty($pass)) {
      $error[] = 'Username and password are required!';
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
            $otp = mt_rand(100000, 999999); // Generate OTP

            // Store OTP in session
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $username;

            if ($row['verify_status'] == 0) {
               $error[] = 'Account not yet verified. Please check your email for OTP.';
            } else {
               // Redirect to OTP verification page after checking verification status and password
               header('verify_status');
               exit();
            }
         } else {
            $error[] = 'Incorrect Username or password!';
         }
      } else {
         $error[] = 'Incorrect Username or password!';
      }
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>DocuVault - login or sign up</title>
    <link rel="stylesheet" href="style.css">
    
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <link rel="icon" type="image/png" href="images/favicons.png">
</head>
<body>

<div class="main">
      <div class="navbar">
         <div class="logo">
            <h2>DocuVault</h2>
         </div>
         <div class="menu">
            <ul>
               <li><a href="index">HOME</a></li>
               <li><a href="about">ABOUT</a></li>
               <li><a href="#">SERVICES</a></li>
               <li><a href="#">DESIGN</a></li>
            </ul>
         </div>
      </div>   
<div class="content">
   <h1>Document Management  <span>System</span> </h1>
   <br>
   <p class="par">Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt neque
      expedita atque eveniet <br> quis nesciunt. Quos nulla vero consequuntur, fugit nemo ad delectus
      <br> a quae totam ipsa illum minus laudantium?
   </p>



   <div class="form">
      <form method="POST" action="">
         <h2>Login Here</h2>
         <?php
         if (isset($error)) {
            foreach ($error as $errMsg) {
               echo '<span class="error-msg">' . $errMsg . '</span>';
            }
         }
         ?>
         <?php
         if (isset($_SESSION['status'])) {
            ?>
            <div class="alert-success">
               <h5>
                  <?= $_SESSION['status']; ?>
               </h5>
            </div>
            <?php
            unset($_SESSION['status']);
            ?>

            <?php
         }
         ?>
            <input type="email" name="email" placeholder="Enter Email Here" required>
            <input type="password" name="password" placeholder="Enter Password Here" required>
            <input type="text" name="otp" required placeholder="Enter OTP">
            <input type="submit" name="submit" value="Login Now" class="form-btn">
            </form>
            <p class="link">
               Don't have an account<br>
               <a href="register_form" id="signUpBtn" class="black-text">Sign up</a> here</a>
            </p>
      <p class="liw">Log in with</p>
      <div class="icons">
         <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
         <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
         <a href="#"><ion-icon name="logo-twitter"></ion-icon></a>
         <a href="#"><ion-icon name="logo-google"></ion-icon></a>
         <a href="#"><ion-icon name="logo-skype"></ion-icon></a>
         
         

      </div>

   </div>
</div>
</div>
</div>
</div>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>