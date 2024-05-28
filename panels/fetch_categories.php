<?php
// Database connection
include '../config/config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch documents based on category
$category = $_GET['category'];
$sql = "SELECT * FROM documents WHERE category = '$category'";
$result = $conn->query($sql);

$documents = array();
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    echo "0 results";
}

// Close connection
$conn->close();

// Output documents as JSON
echo json_encode($documents);
?>