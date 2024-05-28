<?php
// Include your database connection file
include('../config/config.php');

if (isset($_POST['searchQuery'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_POST['searchQuery']);

    // Fetch users from the database based on the search query
    $query = "SELECT * FROM `user_form` WHERE `name` LIKE '%$searchQuery%'";
    $result = mysqli_query($conn, $query);

    // Generate HTML for the list of users
    $html = '';
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $html .= '<li class="list-group-item">' . $row['name'] . '</li>';
        }
    } else {
        $html .= '<li class="list-group-item">No users found</li>';
    }

    echo $html;
}
?>
