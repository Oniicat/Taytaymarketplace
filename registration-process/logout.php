<?php
// Start the session
session_start();

// Destroy all session variables
session_unset(); 
session_destroy();

// Redirect to the login page
header('Location: ../login_module/signin_page.php');
exit();
?>
