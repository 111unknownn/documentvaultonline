<?php

include '../config/config.php';
session_start();
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

/* insert_data */
if (isset($_POST['Save_Changes'])) {
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['user_type'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password']; // Plain text password from the form
        $user_type = $_POST['user_type'];

        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        /* insert_data */
        $insert_query = "INSERT INTO user_form (name, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $user_type);
        $insert_query_run = mysqli_stmt_execute($stmt);


        if ($insert_query_run) {
            $_SESSION['status'] = "Data inserted successfully";
            header('location: user_management');
        } else {
            $_SESSION['status'] = "Insert data failed";
            header('location: user_management');
        }
    } else {
        $_SESSION['status'] = "Missing data in the form.";
        header('location: user_management');
    }
}

/* Fetch User Data */
if (isset($_POST['fetch_user_data']) && isset($_POST['user_id'])) {
    $id = $_POST['user_id'];

    $fetch_query = "SELECT * FROM user_form WHERE user_id='$id'";
    $fetch_query_result = mysqli_query($conn, $fetch_query);

    if (mysqli_num_rows($fetch_query_result) > 0) {
        $row = mysqli_fetch_assoc($fetch_query_result);
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No record found']);
    }
}

/* Log Activity */
if (isset($_POST['log_activity']) && isset($_POST['description'])) {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in
    $timestamp = date('Y-m-d H:i:s');

    $insertActivityQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, 'unread')";
    $stmt = $conn->prepare($insertActivityQuery);
    $stmt->bind_param("iss", $user_id, $description, $timestamp);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Activity logged successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error logging activity']);
    }
    exit();
}

/* Edit Data */
if (isset($_POST['click_edit_btn']) && isset($_POST['user_id'])) {
    $id = $_POST['user_id'];

    $fetch_query = "SELECT * FROM user_form WHERE user_id='$id'";
    $fetch_query_result = mysqli_query($conn, $fetch_query);

    if (mysqli_num_rows($fetch_query_result) > 0) {
        $row = mysqli_fetch_assoc($fetch_query_result);
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No record found']);
    }
}

/* Update Data */
if (isset($_POST['update_data']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['user_type']) && isset($_POST['password'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $user_type = $_POST['user_type'];
        $new_password = $_POST['password']; // New plain text password from the form

        // Hash the new password using bcrypt
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        /* Update Data */
        $update_query = "UPDATE user_form SET name=?, email=?, user_type=?, password=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $user_type, $hashed_password, $id);
        $update_query_run = mysqli_stmt_execute($stmt);

        if ($update_query_run) {
            $_SESSION['status'] = "Data updated successfully";
            header('location: user_management');
        } else {
            $_SESSION['status'] = "Update failed: " . mysqli_error($conn);
            header('location: user_management');
        }
    } else {
        $_SESSION['status'] = "Missing data in the form.";
        header('location: user_management');
    }
}

/* Confirm Delete Data */
if (isset($_POST['confirm_delete_btn']) && isset($_POST['user_id'])) {
    $id = $_POST['user_id'];

    $confirm_delete_query = "DELETE FROM user_form WHERE user_id=?";
    $stmt = mysqli_prepare($conn, $confirm_delete_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $confirm_delete_query_run = mysqli_stmt_execute($stmt);

    if ($confirm_delete_query_run) {
        $response = array('status' => 'success', 'message' => 'Data Deleted Successfully');
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => 'Data Deletion Failed: ' . mysqli_error($conn));
        echo json_encode($response);
    }
}

/* Change Password */
if (isset($_POST['change_password'])) {
    $newPassword = $_POST['newPassword'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $user_id = $_SESSION['user_id']; // Assuming the user is logged in

    $update_password_query = "UPDATE user_form SET password=? WHERE user_id=?";
    $stmt = $conn->prepare($update_password_query);
    $stmt->bind_param("si", $hashedPassword, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error changing password']);
    }

    // Log password change activity
    $description = "Changed password successfully";
    $timestamp = date('Y-m-d H:i:s');

    $insertActivityQuery = "INSERT INTO activities (user_id, description, timestamp, status) VALUES (?, ?, ?, 'unread')";
    $stmt = $conn->prepare($insertActivityQuery);
    $stmt->bind_param("iss", $user_id, $description, $timestamp);

    $stmt->execute();
}

?>
