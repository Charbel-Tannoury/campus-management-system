
<?php
/**
 * Logout Script
 * 
 * Terminates user session and redirects to login.
 * Features:
 * - Sets isloggedin flag to 0
 * - Destroys entire session
 * - Redirects to login page
 * - Works for all user types (admin, student, employee)
 * 
 * Security:
 * - Clears all session data
 * - Forces re-authentication
 * - Prevents back-button access
 * 
 * Usage:
 * - Linked from all sidebars/headers
 * - Called when user clicks logout
 * - Immediate redirect after logout
 * 
 * Flow:
 * 1. Start session
 * 2. Set isloggedin = 0
 * 3. Destroy session
 * 4. Redirect to login.php
 */

session_start();
$_SESSION['isloggedin']=0;
session_destroy();

header("Location: login.php");
?>