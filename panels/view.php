<?php
session_start();
include("functions.php");

$id = $_GET['id'];

// Mark the notification as read
$query = "UPDATE `notifications` SET `status` = 'read' WHERE `id` = $id AND `user_id` = {$_SESSION['user_id']}";
performQuery($query);

// Fetch the notification details
$query = "SELECT * FROM `notifications` WHERE `id` = $id AND `user_id` = {$_SESSION['user_id']}";
$notification = fetchAll($query);

if(count($notification) > 0){
    foreach($notification as $i){
        echo ucfirst($i['name']).": ".$i['message']."<br/>".$i['date'];
    }
} else {
    echo "Notification not found.";
}
?><br/>
<a href="index.php">Back</a>
