<?php
session_start();
include("config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {


       // After successfully saving the file, insert a record into received_files table
       $senderName = $_SESSION['username']; // You may need to modify this based on your authentication logic
       $fileName = $_FILES["file"]["name"];
       $receivedTime = date("Y-m-d H:i:s");

       $insertQuery = "INSERT INTO received_files (sender_name, file_name, received_time) VALUES (?, ?, ?)";
       $stmt = mysqli_prepare($conn, $insertQuery);
       mysqli_stmt_bind_param($stmt, "sss", $senderName, $fileName, $receivedTime);
       mysqli_stmt_execute($stmt);
}
// Fetch received files from the database
$sqlReceivedFiles = "SELECT * FROM received_files ORDER BY received_time DESC";
$resultReceivedFiles = mysqli_query($conn, $sqlReceivedFiles);

if (!$resultReceivedFiles) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #1e87f0;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .back-btn:hover {
            background-color: #45a049;
        }

        .inbox-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .inbox-title {
            font-size: 24px;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Inbox</h2>

    <table>
        <thead>
            <tr>
                <th>Sender</th>
                <th>File Name</th>
                <th>Received Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($rowReceivedFile = mysqli_fetch_assoc($resultReceivedFiles)): ?>
                <tr>
                    <td><?php echo $rowReceivedFile['sender_name']; ?></td>
                    <td><?php echo $rowReceivedFile['file_name']; ?></td>
                    <td><?php echo $rowReceivedFile['received_time']; ?></td>
                    <td>
                        <a href="<?php echo $rowReceivedFile['file_path']; ?>" download>Download</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Back button -->
<div class="back-btn" onclick="goBack()">Back to Admin Page</div>
</div>

<script>
    function goBack() {
        window.location.href = "admin_page.php";
    }
</script>
</body>
</html>
