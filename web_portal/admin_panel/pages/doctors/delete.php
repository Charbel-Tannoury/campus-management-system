<?php
/**
 * Delete Professor Script
 * 
 * Simple deletion endpoint for removing professor records.
 * Features:
 * - Deletes doctor by ID from doctors table
 * - Redirects back to professor list
 * - No confirmation or validation
 * - Direct query execution
 * 
 * Parameters:
 * - id: Doctor ID from GET parameter
 * 
 * Security Issues:
 * - No session validation (should check admin login)
 * - Direct use of GET parameter (SQL injection risk)
 * - No prepared statements
 * - No error handling
 * - No cascade consideration (may leave orphaned records)
 * 
 * Recommended Improvements:
 * - Add session validation
 * - Use prepared statements
 * - Check for related records (to_enrol table)
 * - Add confirmation step
 * - Add success/error messaging
 * - Consider soft delete (status=inactive) instead
 * 
 * Note: This is a minimal implementation that should be enhanced
 */

 $doctor_id = $_GET['id'];
$sql = "DELETE FROM doctors WHERE dr_id = '$doctor_id'";
$con->query($sql);
header("Location: list.php");
?>