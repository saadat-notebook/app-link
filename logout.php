<?php
// logout.php (NEW SECURE VERSION)

// We need the database connection to delete the token.
require_once 'admin/config.php';

// Check if a "remember_me" cookie exists
if (isset($_COOKIE['remember_me'])) {
    $cookie = $_COOKIE['remember_me'];
    list($selector) = explode(':', $cookie, 2);

    if (!empty($selector)) {
        // Find the token in the database by its selector and delete it
        $stmt = $conn->prepare("DELETE FROM user_auth_tokens WHERE selector = ?");
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $stmt->close();
    }
    
    // Tell the browser to delete the cookie by setting its expiration date to the past.
    setcookie('remember_me', '', time() - 3600, '/');
}

// Now, perform the standard session destruction
// The session should already be started by config.php

// Unset all of the session variables
$_SESSION = array();

// Destroy the session itself
session_destroy();

// Safely close the database connection
if (isset($conn)) {
    $conn->close();
}

// Finally, redirect the user to the login page
header("location: login.php");
exit;
?>