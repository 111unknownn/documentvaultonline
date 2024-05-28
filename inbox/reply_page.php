<?php
session_start();
@include '../config/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the sender information is provided in the URL
if (isset($_GET['sender']) && isset($_GET['type']) && isset($_GET['content'])) {
    $sender = htmlspecialchars($_GET['sender']);
    $type = htmlspecialchars($_GET['type']);
    $content = htmlspecialchars($_GET['content']);
    $file = isset($_GET['file']) ? htmlspecialchars($_GET['file']) : '';

    // Display the sender information
    echo "<h1>Reply to: $sender</h1>";
    echo "<p>Type: $type</p>";
    echo "<p>Content: $content</p>";

    // You can include a form here for composing a reply
    // Example form:
    ?>
    <form action="send_reply.php" method="post">
        <input type="hidden" name="recipient" value="<?= $sender ?>">
        <label for="replyContent">Reply Content:</label>
        <textarea name="replyContent" id="replyContent" rows="4" required></textarea>
        <br>
        <input type="submit" value="Send Reply">
    </form>
    <?php
} else {
    // Redirect to the inbox page if sender information is not provided
    header("Location: inbox.php");
    exit();
}
?>
