<?php
require_once '../config/bootstrap.php';


// Log admin action if admin is logging out
if (isAdminLoggedIn()) {
    logAdminAction($_SESSION['admin_id'], 'LOGOUT', NULL, NULL, 'Admin logged out');
}

// Destroy all session data
destroySession();

// Redirect to home page
header('Location: ../index.php?message=Logged out successfully');
exit();
?>
