<?php
// THIS IS THE LOGIC WHERE THE RESET PASSWORD!!!

@include '../config/config.php';


if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Check if the provided email and token match a record in the database
    $select = "SELECT * FROM user_form WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()";
    $stmt = mysqli_prepare($conn, $select);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result !== false && mysqli_num_rows($result) > 0) {

    } else {
        echo "";
    }
} else {
    echo "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>DocuVault - Reset Password</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="shortcut icon" href="../images/favicons.png" type="image/x-icon">
</head>
<style>
    #newPasswordError{
        font-size: 14px;
        color:red;
    }
    #confirmPasswordError{
        font-size: 14px;
        color: red;
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

            <!-- reset_password.php -->
            <div class="form">
                <form method="POST" action="process_reset_password" onsubmit="return validatePassword()">
                    <h2>Reset Password</h2>
                    <input type="hidden" name="email" value="<?php echo $email; ?>">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">

                    <!-- New Password input field with toggle icon -->
                    <div class="password-container">
                        
                        <div class="password-input">
                              <!-- PASSWORD MUST HAVE UPPERCASE AND SPECIAL CHARACTERS -->
                            <input type="password" id="new_password" name="new_password" required
                                pattern="^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]).+$"
                                title="Password must contain at least one uppercase letter and one special character"
                                placeholder="Enter New Password">
                                <div id="js-error-msg" class="error-msg" style="display: none;"></div>
                                <!--TOGGLE KANG VIEW AND HIDE-->
                                <div class="toggle-password hide" onclick="togglePasswordVisibility('new_password')"></div>
                        </div>
                        <span id="newPasswordError" class="error-message"></span> <!-- Error message for new password -->

                    </div>

                    <!-- Confirm Password input field with toggle icon -->
                    <div class="password-container">
                        <div class="password-input">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                placeholder="Confirm Password">
                                <!--TOGGLE KANG VIEW AND HIDE-->
                            <div class="toggle-password hide" onclick="togglePasswordVisibility('confirm_password')"></div>
                        </div>
                        <span id="confirmPasswordError" class="error-message"></span> <!-- Error message for confirm password -->

                    </div>

                    <input type="submit" name="submit" value="Reset Password" class="btnn">
                    <p style="color:gray; font-size:small; margin-top:5px;">Password should contain at least 8 characters including uppercase, lowercase, numbers, and special characters.</p>
                    <p></p>
                </form>
            </div>

            <!--STYLE OF CONTAINER AND THE TOGGLE EYE-->
            <style>
                .password-container {
                    position: relative;
                }

                .password-input {
                    position: relative;
                }

                .toggle-password {
                    position: absolute;
                    top: 70%;
                    right: 10px;
                    transform: translateY(-50%);
                    cursor: pointer;
                    width: 20px;
                    height: 20px;
                    background-color: #fff;
                }

                .toggle-password.show {
                    background-image: url('../images/view.png'); /* Replace with the path to your show icon */
                }

                .toggle-password.hide {
                    background-image: url('../images/hide.png'); /* Replace with the path to your hide icon */
                }

                .toggle-password:hover {
                    filter: brightness(110%); /* Add a brightness effect on hover */
                }
            </style>

            <script>
                /* validation of password*/
                function validatePassword() {
                    var newPassword = document.getElementById("new_password").value;
                    var confirmPassword = document.getElementById("confirm_password").value;
                    var newPasswordError = document.getElementById("newPasswordError");
                    var confirmPasswordError = document.getElementById("confirmPasswordError");
                    var pattern = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]).+$/;

                    if (newPassword !== confirmPassword) {
                        newPasswordError.textContent = "";
                        confirmPasswordError.textContent = "Passwords do not match!";
                        return false;
                    }

                    if (!pattern.test(newPassword)) {
                        newPasswordError.textContent = "Password Must Contain One Special Character and Uppercase Letter!";
                        confirmPasswordError.textContent = "";
                        return false;
                    }

                    return true;
                }

                /* function in the view and hide password*/
                function togglePasswordVisibility(inputId) {
                    const passwordInput = document.getElementById(inputId);
                    const toggleIcon = passwordInput.nextElementSibling;

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

               // Function to disable space input and show error message
function disableSpace(event) {
    if (event.keyCode === 32) {
        event.preventDefault(); // Prevent space input
        document.getElementById("newPasswordError").textContent = "Spacebar input is disabled for the password.";
        return false; // Prevent form submission
    } else {
        document.getElementById("newPasswordError").textContent = ""; // Clear error message
        return true;
    }
}

// Add event listeners to password fields to disable space input
document.getElementById("new_password").addEventListener("keydown", disableSpace);
document.getElementById("confirm_password").addEventListener("keydown", disableSpace);

            </script>

        </div>
    </div>
</body>

</html>
