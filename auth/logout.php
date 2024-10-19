<?php
// Start the session
session_start();
// Unset all session variables
$_SESSION = [];


// Destroy the session to log out the user
session_destroy();

// Redirect to the index page
header("Location: ../index.php"); // Adjust the path as needed
exit();
?>
