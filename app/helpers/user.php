<?php

function getUser($identifier, $conn) {
    // Check if the identifier is numeric (user ID) or alphanumeric (username)
    if (is_numeric($identifier)) {
        $sql = "SELECT user_id, name, username, email, user_type, p_p FROM user_form WHERE user_id = ?";
    } else {
        $sql = "SELECT user_id, name, username, email, user_type, p_p FROM user_form WHERE username = ?";
    }

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return [];
    }

    // Bind parameters
    $stmt->bind_param("s", $identifier);

    // Execute the query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch the result
    $user = $result->fetch_assoc();

    // Check if user exists
    if ($user) {
        return $user;
    } else {
        return []; // Return an empty array if user does not exist
    }
}

function getUserType($userId, $conn) {
    // Query to get user type based on user ID
    $query = "SELECT user_type FROM user_form WHERE user_id = ?";
    
    // Prepare the statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        return null;
    }

    // Bind parameters
    $stmt->bind_param("i", $userId);

    // Execute the query
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch the user type
    $userType = $result->fetch_assoc();

    // Return user type
    return $userType['user_type'];
}

// Function to get user data by user ID
function getUserById($userId, $conn) {
    try {
        // Prepare SQL statement
        $sql = "SELECT user_id, name, username, email, user_type, p_p FROM user_form WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId); // 'i' indicates an integer parameter
        // Execute the statement
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();
        // Fetch user data
        $user = $result->fetch_assoc();
        // Return user data
        return $user;
    } catch (mysqli_sql_exception $e) {
        // Handle any exceptions
        echo "Error retrieving user data: " . $e->getMessage();
        return null; // Return null in case of error
    }
}


?>
