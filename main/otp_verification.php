<?php
session_start();

include '../config/config.php';

// Set the timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if 'status_symbol' and 'name' columns exist in 'user_logs' table
$checkColumnQuery = "SHOW COLUMNS FROM user_logs LIKE 'status_symbol'";
$checkColumnResult = mysqli_query($conn, $checkColumnQuery);

if (!$checkColumnResult || mysqli_num_rows($checkColumnResult) == 0) {
    // Execute SQL query to add 'status_symbol' column to 'user_logs' table
    $alterTableQuery = "ALTER TABLE user_logs ADD COLUMN status_symbol VARCHAR(1) NOT NULL";
    mysqli_query($conn, $alterTableQuery);
}

// Check if 'name' column exists in 'user_logs' table
$checkNameColumnQuery = "SHOW COLUMNS FROM user_logs LIKE 'name'";
$checkNameColumnResult = mysqli_query($conn, $checkNameColumnQuery);

if (!$checkNameColumnResult || mysqli_num_rows($checkNameColumnResult) == 0) {
    // Execute SQL query to add 'name' column to 'user_logs' table
    $alterNameColumnQuery = "ALTER TABLE user_logs ADD COLUMN name VARCHAR(255)";
    mysqli_query($conn, $alterNameColumnQuery);
}

if (!isset($_SESSION['temp_user'])) {
    header("Location: index");
    exit();
}

$email = isset($_SESSION['temp_user']['email']) ? $_SESSION['temp_user']['email'] : '';

$error = ''; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];
    $stored_otp = $_SESSION['temp_user']['otp'];
    $user_id = $_SESSION['temp_user']['id'];

    $sql = "SELECT * FROM user_form WHERE user_id='$user_id' AND otp='$user_otp'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    //putting a cookie in user authentication
    if ($row) {
        $otp_expiry = strtotime($row['otp_expiry']);
        if ($otp_expiry >= time()) {
            // Generate a token for the user
            $user_token = generateToken(); // Assuming you have a function to generate a token

            // Store the token in the user_form table
            $updateTokenQuery = "UPDATE user_form SET user_token = '$user_token' WHERE user_id = '$user_id'";
            mysqli_query($conn, $updateTokenQuery);

            // Insert login record with action description into user_logs table
            $loginTime = date('Y-m-d H:i:s');
            $status = 'online';
            $statusSymbol = '●'; // Unicode character for a filled circle symbol for online users

            // Action description for logging in
            $currentAction = "Logged In"; 

            $insertLogQuery = "INSERT INTO user_logs (user_id, login_time, status, status_symbol, current_action, name) VALUES (?, ?, ?, ?, ?, ?)";
            $insertLogStmt = mysqli_prepare($conn, $insertLogQuery);

            if ($insertLogStmt) {
                mysqli_stmt_bind_param($insertLogStmt, "isssss", $row['user_id'], $loginTime, $status, $statusSymbol, $currentAction, $row['name']);
                mysqli_stmt_execute($insertLogStmt);
                mysqli_stmt_close($insertLogStmt);

                // Set the session variable $thisUserId to the value of 'user_id'
                $_SESSION['user_id'] = $row['user_id'];

                if ($row['user_type'] == 'admin') {
                    $_SESSION['admin_name'] = $row['name'];
                    header('Location: ../panels/admin_page');
                    exit();
                } elseif ($row['user_type'] == 'user') {
                    $_SESSION['user_name'] = $row['name'];
                    header('Location: ../panels/user_page');
                    exit();
                }
            } else {
                // Print the error message for debugging
                die("Error preparing insert statement: " . mysqli_error($conn));
            }
        } else {
            // Handle the case when the OTP has expired
            $error = "The OTP has expired. Please request a new OTP.";
        }
    } else {
        // Handle the case when the OTP is incorrect
        $error = "Incorrect OTP. Please try again.";
    }
}

// Function to generate a random token
function generateToken() {
    return bin2hex(random_bytes(32)); // Generates a 64-character hexadecimal token
}
?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../images/favicons.png">
    <title></title>
    <style type="text/css">
        #container {
            border: 2px solid black;
            width: 420px;
            height: 350px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-size: cover; /* Optional: Adjusts the background image size */
            color: black; /* Optional: Set text color */
        }

        form {
            margin-left: 50px;
        }

        p {
            text-align: center;
            margin-top: 20px; /* Adjust the margin-top as needed */
            color: grey;
        }

        h2 {
            text-align: center;
            margin-top: 20px; /* Adjust the margin-top as needed */
        }

        input[type=number] {
            width: 290px;
            padding: 10px;
            margin-top: 10px;
        }

        button {
            background-color: #fd8522; /* Change the background color to #fd8522 */
            border: 1px solid black;
            width: 200px;
            padding: 9px;
            margin-left: 50px;
            color: white; /* Set text color to white for better visibility */
            text-align: center;
        }

        button:hover {
            cursor: pointer;
            opacity: .9;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div id="container">
        <h2> Two-Factor Authentication</h2>
        <p>A unique code has been sent to your email address. <br>Please check your inbox and enter the code below to complete the login process. <?php echo $email; ?></p>
        <form method="post" action="otp_verification">
            <label style="font-weight: bold; font-size: 18px;" for="otp">Enter OTP Code:</label><br>
            <input type="number" name="otp" pattern="\d{6}" maxlength="6" placeholder="Six-Digit OTP" required><br><br>
            <button type="submit">Verify OTP</button>
        </form>

        <?php
        if (!empty($error)) {
            echo '<p class="error-message">' . $error . '</p>';
        }
        ?>
    </div>
</body>

</html>
