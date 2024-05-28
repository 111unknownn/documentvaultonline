<?php
// session_timeout.php
include("config.php");

// Set the session timeout period in seconds (e.g., 15 minutes)
$timeout = 60 * 60; // 1 hour (60 minutes * 60 seconds)


// Check if the last activity time is set
if (isset($_SESSION['last_activity'])) {
    // If the session is older than the timeout period, destroy it and redirect to the login page
    if (time() - $_SESSION['last_activity'] > $timeout) {
        // Destroy the session
        session_destroy();
        
        // Display a styled message to the user
        echo '<div id="session-expired-message" style="display: none; background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                Your session has expired due to inactivity. Please <a href="../main/index.php" style="color: #721c24; font-weight: bold;">click here</a> to log in again.
              </div>';
        echo '<script>
                document.getElementById("session-expired-message").style.display = "block";
              </script>';
        exit();
    }
} 

// Update last activity time
$_SESSION['last_activity'] = time();
?>
<script>
    // Function to display the session expiry message
    function displaySessionExpiredMessage() {
        // Display the session expiry message
        document.getElementById("session-expired-message").style.display = "block";
        // You might want to add code here to redirect to the login page instead
    }

    // Function to reset the session timeout when mouse movement or keyboard event is detected
    function resetSessionTimeout() {
        <?php echo 'var timeout = ' . $timeout * 1000 . ';'; ?> // Convert timeout to milliseconds
        clearTimeout(window.sessionTimeout); // Clear any existing timeout
        window.sessionTimeout = setTimeout(displaySessionExpiredMessage, timeout);
    }

    // Attach event listener for mouse movement
    document.addEventListener("mousemove", resetSessionTimeout);

    // Attach event listener for mouse click
    document.addEventListener("click", resetSessionTimeout);
    
    // Initial call to start the session timeout countdown
    resetSessionTimeout();
</script>
