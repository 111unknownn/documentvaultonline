<?php  
// Check if username, password, name submitted
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['name'])) {
    // Database connection file
    include '../../config/config.php';
    
    // Get data from POST request and store them in variables
    $name = $_POST['name'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    // Making URL data format
    $data = 'name=' . $name . '&username=' . $username;

    // Simple form validation
    if (empty($name)) {
        // Error message
        $em = "Name is required";
        // Redirect to 'signup.php' and passing error message
        header("Location: ../../signup.php?error=$em");
        exit;
    } elseif (empty($username)) {
        // Error message
        $em = "Username is required";
        // Redirect to 'signup.php' and passing error message and data
        header("Location: ../../signup.php?error=$em&$data");
        exit;
    } elseif (empty($password)) {
        // Error message
        $em = "Password is required";
        // Redirect to 'signup.php' and passing error message and data
        header("Location: ../../signup.php?error=$em&$data");
        exit;
    } else {
        // Checking the database if the username is taken
        $sql = "SELECT username FROM user_form WHERE username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the results
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $em = "The username ($username) is taken";
            header("Location: ../../signup.php?error=$em&$data");
            exit;
        } else {
            // Profile Picture Uploading
            if (isset($_FILES['pp'])) {
                // Get data and store them in variables
                $img_name  = $_FILES['pp']['name'];
                $tmp_name  = $_FILES['pp']['tmp_name'];
                $error  = $_FILES['pp']['error'];

                // If there is not error occurred while uploading
                if ($error === 0) {
                    // Get image extension and store it in variable
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);

                    // Convert the image extension into lower case and store it in variable
                    $img_ex_lc = strtolower($img_ex);

                    // Array that stores allowed to upload image extensions
                    $allowed_exs = array("jpg", "jpeg", "png");

                    // Check if the the image extension is present in $allowed_exs array
                    if (in_array($img_ex_lc, $allowed_exs)) {
                        // Renaming the image with user's username
                        $new_img_name = $username. '.'.$img_ex_lc;

                        // Creating upload path on root directory
                        $img_upload_path = '../../uploads/'.$new_img_name;

                        // Move uploaded image to ./upload folder
                        move_uploaded_file($tmp_name, $img_upload_path);
                    } else {
                        $em = "You can't upload files of this type";
                        header("Location: ../../signup.php?error=$em&$data");
                        exit;
                    }
                }
            }

            // Password hashing
            $password = password_hash($password, PASSWORD_DEFAULT);

            // Inserting data into database
            if (isset($new_img_name)) {
                $sql = "INSERT INTO user_form (name, username, password, p_p) VALUES (:name, :username, :password, :new_img_name)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':username', $username, PDO::PARAM_STR);
                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt->bindValue(':new_img_name', $new_img_name, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                $sql = "INSERT INTO user_form (name, username, password) VALUES (:name, :username, :password)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':username', $username, PDO::PARAM_STR);
                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt->execute();
            }

            // Success message
            $sm = "Account created successfully";

            // Redirect to 'index.php' and passing success message
            header("Location: ../../index.php?success=$sm");
            exit;
        }
    }
} else {
    header("Location: ../../signup.php");
    exit;
}
?>
