<?php
/**
 * Mail Edit Processing Script
 * 
 * Backend processor for updating mail/message records.
 * Features:
 * - Updates all mail fields
 * - Redirects with status messages
 * - No validation or security measures
 * - Direct SQL UPDATE query
 * 
 * POST Parameters:
 * - mail_id: ID of message to update
 * - mail_title: Subject/title
 * - receivers: Target audience (0=all, 1=students, 2=professors)
 * - related_faculties: Scope (0=just this university, 1=all faculties)
 * - priority: Priority level
 * - mail_info: Message content
 * 
 * Success Flow:
 * - Redirects to list.php?status=success&message=...
 * 
 * Error Flow:
 * - Redirects to edit.php?id=X&status=error&message=...
 * 
 * Security Issues:
 * - No session validation
 * - Direct POST variable usage (SQL injection risk)
 * - No prepared statements
 * - No input validation
 * - No faculty access control
 * 
 * Recommended Improvements:
 * - Add session validation
 * - Use prepared statements
 * - Validate mail ownership/access
 * - Sanitize inputs
 * - Add error handling
 */

require_once '../../../connection.php';
$mail_id = $_POST['mail_id'];
$mail_title = $_POST['mail_title'];
$receivers = $_POST['receivers'];
$related_faculties = $_POST['related_faculties'];
$priority = $_POST['priority'];
$mail_info = $_POST['mail_info'];
$query = "UPDATE mails SET mail_title='$mail_title', receivers='$receivers', related_faculties='$related_faculties', priority='$priority', mail_info='$mail_info' WHERE mail_id='$mail_id'";
if (mysqli_query($con, $query)) {
    header("Location: list.php?status=success&message=Message updated successfully");
    exit();
} else {
    header("Location: edit.php?id=$mail_id&status=error&message=Failed to update message");
    exit();
}
?>