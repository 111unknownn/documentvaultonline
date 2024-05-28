<?php
//VERFYING EMAIL
session_start();
include("../config/config.php");


if (isset($_GET['otp'])) {
    $otp = $_GET['otp'];
    $verify_query = "SELECT otp, verify_status FROM user_form WHERE otp = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $verify_query);
    mysqli_stmt_bind_param($stmt, "s", $otp);
    mysqli_stmt_execute($stmt);
    $verify_query_run = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_array($verify_query_run);
        if ($row['verify_status'] == "0") {
            $clicked_otp = $row['otp'];
            $update_query = "UPDATE user_form SET verify_status = '1' WHERE otp = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $clicked_otp);
            $update_query_run = mysqli_stmt_execute($stmt);

            if ($update_query_run) {
                $_SESSION['status'] = "Your Account Has Been Verified";
                header("Location: login_form");
                exit(0);
            } else {
                $_SESSION['status'] = "Verification Failed";
                header("Location: login_form");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Account Already Verified. Please Login";
            header("Location: login_form");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "Invalid OTP. Please check and try again";
        header("Location: login_form");
        exit(0);
    }
} else {
    $_SESSION['status'] = "Not Allowed";
    header("Location: login_form");
    exit(0);
}
?>
