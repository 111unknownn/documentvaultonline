<?php

@include '../config/config.php';

if (isset($_POST['submit'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $username = mysqli_real_escape_string($conn, $_POST['username']);
   $password = $_POST['password'];
   $cpass = $_POST['cpassword'];
   $user_type = $_POST['user_type'];

   // Check if the password and confirm password match
   if ($password !== $cpass) {
       $error[] = 'Passwords do not match!';
   } else {
       // Hash the password using bcrypt
       $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

       $select = "SELECT * FROM user_form WHERE username= '$username'";
       $result = mysqli_query($conn, $select);

       if (mysqli_num_rows($result) > 0) {
           $error[] = 'User already exists!';
       } else {
           $insert = "INSERT INTO user_form (name, username, password, user_type) VALUES ('$name', '$username', '$hashedPassword', '$user_type')";
           mysqli_query($conn, $insert);
           header('location: login_form');
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
   <title>register form</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Category Add</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="name" required placeholder="enter your name">
      <input type="username" name="username" required placeholder="enter your username">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="password" name="cpassword" required placeholder="confirm your password">
      <select name="user_type">
         <option value="user">user</option>
         <option value="admin">admin</option>
      </select>
      <input type="submit" name="submit" value="Add" class="form-btn">
     
   </form>

</div>

</body>
</html>