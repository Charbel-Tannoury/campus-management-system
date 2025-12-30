<?php
/**
 * Student Registration Processing Script
 * 
 * Handles the backend processing of new student registrations.
 * Features:
 * - Input validation (name, email, password format)
 * - Password strength requirements (8+ chars, letter + number)
 * - Password confirmation matching
 * - Email format validation with regex
 * - BCrypt password hashing
 * - Dual table insertion (students + paid_students)
 * - Session-based error messaging
 * - Automatic student ID generation (LAST_INSERT_ID)
 * 
 * Validation Rules:
 * - First/Last Name: Letters, spaces, hyphens only
 * - Email: Valid email format (regex)
 * - Password: Min 8 chars, at least 1 letter and 1 number
 * - Password confirmation must match
 * 
 * Database Operations:
 * 1. INSERT into students table (creates student account)
 * 2. INSERT into paid_students table (enrollment request with status=0)
 * 
 * Success Flow:
 * - Student account created
 * - Payment record created (pending approval)
 * - Redirect to login page
 * 
 * Error Flow:
 * - Validation errors stored in session
 * - Redirect back to registration form
 * - Errors displayed to user
 */

require_once '../connection.php';
session_start();
$year=date('Y');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $faculty = trim($_POST['Faculty']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $major = trim($_POST['major']);
    
    
    if ( empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['msg'] = "Please fill in all required fields.";
        header("Location: register_student.php");
        exit();
    }
    
    if (!preg_match("/^[a-zA-Z\s\-]+$/", $first_name)) {
       $msg .= "First name can only contain letters, spaces, and hyphens."."<br>";
    }
    
    // Validate last name (only letters, spaces, hyphens)
    if (!preg_match("/^[a-zA-Z\s\-]+$/", $last_name)) {
        $msg .= "Last name can only contain letters, spaces, and hyphens."."<br>";
    }
    
    // Validate email format
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
       $msg .= "Invalid email format."."<br>";
    }
    
    // Validate password strength (minimum 8 characters, at least one letter and one number)
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/", $password)) {
        $msg .= "Password must be at least 8 characters and contain at least one letter and one number."."<br>";
    }
    
    if ($password !== $confirm_password) {
        $msg .= "Passwords do not match."."<br>";
    }
    if (!empty($msg)) {
        $_SESSION['msg'] = $msg;
        header("Location: register_student.php");
        exit();
    }
    $password = password_hash($password, PASSWORD_BCRYPT);
    $query = "INSERT INTO students (first_name, last_name, password, email) VALUES ('$first_name', '$last_name', '$password', '$email')";
    $result = mysqli_query($con, $query);
    if ($result) {
        $query = "INSERT INTO paid_students (faculty_id, student_id, major_id, year) VALUES ('$faculty', LAST_INSERT_ID(), '$major', '$year')";
        $result = mysqli_query($con, $query);

        header("Location: ../login_logout/login.php");
        $_SESSION['msg']="you where added succesfuly";

        exit();
    } else {
        $_SESSION['msg']="there was a problem please try again";
        header("Location: register_student.php");
        exit();
    }

}

mysqli_close($con);
?>
