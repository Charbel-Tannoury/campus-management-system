<?php
/**
 * Student Payment Verification Script
 * 
 * Admin tool to approve student payment/enrollment requests.
 * Features:
 * - Updates paid_students status from 0 (pending) to 1 (approved)
 * - Sends email notification to student upon approval
 * - Simple UPDATE query based on paid_id
 * - Redirect back to payment verification list
 * 
 * Workflow:
 * 1. Admin clicks "verify" button on pay.php page
 * 2. paid_id submitted via POST
 * 3. Status updated to 1 (approved) in paid_students table
 * 4. Student email fetched via JOIN (students + paid_students)
 * 5. Email sent to student with verification confirmation
 * 6. Redirect back to pay.php
 * 
 * Email Details:
 * - To: Student's registered email
 * - Subject: Account verification confirmation
 * - Body: Simple text with student's first name
 * - From: tonyyyyy345@gmail.com (hardcoded sender)
 * 
 * Security Notes:
 * - Uses POST data directly (potential SQL injection risk)
 * - Should use prepared statements in production
 * - No session validation (assumes admin is logged in)
 * 
 * After Approval:
 * - Student can enroll in courses
 * - Status=1 indicates payment verified
 */

require_once '../connection.php';
$date=date("Y-m-d H:i:s");
$paid_id=$_POST['submit'];
$query="UPDATE paid_students SET status=1 where paid_id='$paid_id';";
$update=mysqli_query($con,$query);
if($update){
    $query2="SELECT email, first_name FROM students s JOIN paid_students p ON p.student_id = s.student_id where paid_id='$paid_id';";
    $result=mysqli_query($con,$query2);
    $row=mysqli_fetch_array($result);
    $to_email = $row['email'];
    $subject = "Your acc is verified Account Verified";
    $body = "Dear ".$row['first_name'].",\nYou has been successfully verified. You can now log in to your account using your student ID and password.\n";
    $headers = "From: tonyyyyy345@gmail.com";
    mail($to_email, $subject, $body, $headers);
    header("Location: pay.php");
    exit();
}
else{
    echo "there was an error please try again";
}
?>