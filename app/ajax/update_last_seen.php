<?php

session_start();

# check if the user is logged in
if (isset($_SESSION['username'])) {
	
	# database connection file
	include './config/config.php';

	# get the logged in user's username from SESSION
	$id = $_SESSION['user_id'];

	$sql = "UPDATE user_form
	        SET last_seen = NOW() 
	        WHERE user_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $id);
	$stmt->execute();

}else {
	header("Location: ../../index.php");
	exit;
}
?>
