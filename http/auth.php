<?php
session_start();

# check if username & password submitted
if(isset($_POST['email']) && isset($_POST['password'])){
    # database connection file
    include '../config/config.php';
   
    # get data from POST request and store them in vars
    $password = $_POST['password'];
    $email = $_POST['email'];
   
    # Simple form validation
    if(empty($email)){
        $em = "Email is required";
    } else if(empty($password)){
        $em = "Password is required";
    } else {
        $sql = "SELECT user_id, email, password, name FROM user_form WHERE email=:email";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();
      
        # Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
        if($user){
            # Verify the encrypted password
            if (password_verify($password, $user['password'])) {
                # Successfully logged in
                # Creating the SESSION
                $_SESSION['username'] = $user['email']; // Use email as the username
                $_SESSION['name'] = $user['name'];
                $_SESSION['user_id'] = $user['user_id'];
                
                # Redirect to 'chat.php'
                header("Location: chat.php");
                exit;
            } else {
                # Incorrect password
                $em = "Incorrect password";
            }
        } else {
            # Email not found
            $em = "Email not found";
        }
    }
    
    # If login fails, redirect to 'index.php' with error message
    if(isset($em)) {
        header("Location: index.php?error=$em");
        exit;
    }
} else {
    # Redirect to 'index.php' if no email and password submitted
    header("Location: index.php");
    exit;
}
?>
