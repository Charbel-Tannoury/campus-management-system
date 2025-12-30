<?php
/**
 * Delete Anonymous Complaint Script
 * 
 * Safely removes a complaint record from the system.
 * Features:
 * - Validates complaint exists before deletion
 * - Uses prepared statements for security
 * - Session-based access control
 * - Success/error messaging via URL parameters
 * - Redirects back to list page
 * 
 * Parameters:
 * - id: Complaint ID from GET parameter
 * 
 * Validation:
 * - Checks if ID is positive integer
 * - Verifies complaint exists in database
 * - Returns error if not found
 * 
 * Delete Process:
 * 1. Validate complaint ID
 * 2. Check complaint exists (prepared statement)
 * 3. Delete record (prepared statement)
 * 4. Return success or error message
 * 
 * Success Flow:
 * - Redirect to list.php?success=Complaint deleted successfully
 * 
 * Error Flows:
 * - Invalid ID: list.php?error=Invalid complaint ID
 * - Not found: list.php?error=Complaint not found
 * - Delete failed: list.php?error=Failed to delete complaint
 * 
 * Security:
 * - Session validation
 * - Prepared statements (SQL injection prevention)
 * - Integer validation on ID
 * - No direct SQL parameter usage
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// Get complaint ID from URL
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($complaint_id <= 0) {
    header('Location: list.php?error=Invalid complaint ID');
    exit();
}

// Check if complaint exists
$checkQuery = "SELECT ac_id FROM anonymous_complaint WHERE ac_id = ?";
$stmt = $con->prepare($checkQuery);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: list.php?error=Complaint not found');
    exit();
}

// Delete the complaint
$deleteQuery = "DELETE FROM anonymous_complaint WHERE ac_id = ?";
$stmt = $con->prepare($deleteQuery);
$stmt->bind_param("i", $complaint_id);

if ($stmt->execute()) {
    header('Location: list.php?success=Complaint deleted successfully');
} else {
    header('Location: list.php?error=Failed to delete complaint');
}

$stmt->close();
$con->close();
exit();
?>