<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dms_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, set the character set
if (!$conn->set_charset("utf8mb4")) {
    echo "Error setting character set: " . $conn->error;
}

?>
