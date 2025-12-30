<?php
/**
 * Student Verification Toggle Script
 * 
 * Admin tool to verify or unverify student accounts.
 * Features:
 * - Toggle verification status (0 = unverified, 1 = verified)
 * - Updates verification timestamp
 * - Session-based success/error messages
 * - Supports both POST and GET requests
 * - Redirect back to student list
 * 
 * Actions:
 * 1. Verify: Sets verification=1 and verified_at=NOW()
 * 2. Unverify: Sets verification=0 and verified_at=NULL
 * 
 * Parameters:
 * - id: Student ID (required)
 * - action: 'verify' or 'unverify' (default: verify)
 * 
 * Security:
 * - Session validation required (Faculty + emp_id)
 * - Student ID validation (must be > 0)
 * - Redirect to login if not authenticated
 * 
 * Flow:
 * 1. Validate session and student ID
 * 2. Update students table based on action
 * 3. Set success/error message in session
 * 4. Redirect back to list.php
 * 
 * Note: Verification status may control student portal access
 */

session_start();
require_once '../../../connection.php';

// check session ladmin
if (!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$con = $con;
$id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
$action = $_POST['action'] ?? $_GET['action'] ?? 'verify';

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid student ID';
    header('Location: list.php');
    exit;
}

if ($action === 'unverify') {
    // l8a2 verification ltaleb
    $query = "UPDATE students SET verification = 0, verified_at = NULL WHERE student_id = '$id'";
} else {
    // verify ltaleb
    $query = "UPDATE students SET verification = 1, verified_at = NOW() WHERE student_id = '$id'";
}

if (mysqli_query($con, $query)) {
    $_SESSION['message'] = ($action === 'unverify') ? 'Student unverified successfully!' : 'Student verified successfully!';
} else {
    $_SESSION['error'] = 'Database error: ' . mysqli_error($con);
}

header('Location: list.php');
exit;
