<?php
/**
 * Add Mail/Message Processing Script
 * 
 * Handles the backend logic for creating new mail messages.
 * Features:
 * - Validates required fields exist
 * - Handles two faculty scope scenarios
 * - Session-based success/error messaging
 * - Redirects back to add page
 * 
 * POST Parameters:
 * - recipient_type: Target audience (0=all, 1=students, 2=professors)
 * - subject: Message title (required)
 * - message: Message content (required)
 * - priority: Priority level (required)
 * - for_faculty: Scope (0=just this university, 1=all faculties)
 * 
 * Logic:
 * - If for_faculty=0: Sets related_faculties to current faculty ID
 * - If for_faculty=1: Sets related_faculties to 1 (all faculties)
 * 
 * Database:
 * - Inserts into mails table
 * - faculty_id: Current faculty from session
 * - receivers: From POST
 * - priority: From POST
 * - related_faculties: Conditional based on for_faculty
 * 
 * Error Handling:
 * - Checks all required fields
 * - Sets session success message
 * - Redirects to add.php
 * 
 * Security Issues:
 * - Direct POST variable usage (SQL injection risk)
 * - No prepared statements
 * - Should sanitize inputs
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac=$_SESSION['Faculty'];
if($_POST['recipient_type']!="" && isset($_POST['subject']) && isset($_POST['message'])   && isset($_POST['priority']) && isset($_POST['for_faculty']))
    {
    $rec=$_POST['recipient_type'];
    $subj=$_POST['subject'];
    $msg=$_POST['message'];
    $prio=$_POST['priority'];
    $stat=$_POST['for_faculty'];
    if($stat==0){
    $query="INSERT INTO mails (faculty_id, receivers, priority, related_faculties, mail_title, mail_info) VALUES ('$fac', '$rec', '$prio', '$fac', '$subj', '$msg')";
    $result=mysqli_query($con,$query);
    if($result){
        $_SESSION['success']="Mail added successfully";
        header('Location: add.php');
        exit();
    }
    else{
        $_SESSION['success']="There was an error please try again";
        header('Location: add.php');
        exit();
}
}if($stat==1){
    $query="INSERT INTO mails (faculty_id, receivers, priority, related_faculties, mail_title, mail_info) VALUES ('$fac', '$rec', '$prio', '$stat', '$subj', '$msg')";
    $result=mysqli_query($con,$query);
    if($result){
        $_SESSION['success']="Mail added successfully";
        header('Location: add.php');
        exit();
    }
    else{
        $_SESSION['success']="There was an error please try again";
        header('Location: add.php');
        exit();
}}}
?>