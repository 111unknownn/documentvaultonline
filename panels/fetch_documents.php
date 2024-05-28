<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include("../config/config.php");

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Fetch documents based on category
$category = isset($_GET['category']) ? $_GET['category'] : '';
$category = $conn->real_escape_string($category); // Escape category

$sql = "SELECT * FROM document WHERE category = '$category'";
$result = $conn->query($sql);

$documents = array();
if ($result) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error executing query: " . $conn->error]);
    $conn->close();
    exit();
}

// Close connection
$conn->close();

// Output documents as JSON
header('Content-Type: application/json');
echo json_encode($documents);
?>
