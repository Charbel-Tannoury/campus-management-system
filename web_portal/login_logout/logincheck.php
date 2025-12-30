<?php
/**
 * Login Authentication Handler
 * 
 * Processes login requests for both employees (admins) and students.
 * Verifies credentials, checks verification status, and manages session creation.
 */

session_start();
require_once '../connection.php';

// Check if login form was submitted
if(isset($_POST['login'])){
    // Validate that both username and password fields are provided
    if(!empty($_POST['user_id']) && !empty($_POST['password'])) {
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
    } else {
        // Redirect with error if fields are empty
        $_SESSION['msg']="All fields are required";
        header('Location: login.php');
        exit;
    }
}
if (isset($_POST['is_employee'])) {
    $faculty=$_POST['Faculty'];
    $query = "SELECT * FROM uniadmins WHERE faculty_id = $faculty AND emp_id='$user_id' ";
    $state=mysqli_query($con,$query);
    $row=mysqli_fetch_array($state);
    
    if($row){
        // Verify password hash matches stored hash
        if(password_verify($password, $row['password'])){
            // Login successful - create session and redirect to admin panel
            echo "Login Successful";
            $_SESSION['Faculty'] = $_POST['Faculty'];  
            $_SESSION['emp_id'] = $user_id;
            header('Location: ../admin_panel/index.php');
            exit;
        } else {
            // Password doesn't match
            $_SESSION['msg']="invalid username or password";
            header('Location: login.php'); 
            exit;
        }
    } else {
        // Employee not found in database
        $_SESSION['msg']="USER NOT FOUND";
        header('Location: login.php');
        exit;
    }
}
else{
    // Student login flow
    $query="SELECT password, verification from students where student_id='$user_id';";
    $state=mysqli_query($con, $query);
    $row=mysqli_fetch_array($state);
    
    if($row){
        // Verify student password
        if(password_verify($password, $row['password'])){
            // Check if student's email is verified
            if($row['verification']==0){
                $_SESSION['msg']="Your account is not verified yet. Please check your email for the verification link.";
                header('Location: login.php');
                exit;
            }
            else{
                // Student login successful - create session and redirect to student portal
                echo "Login Successful";
                $_SESSION['id'] = $user_id;    
                header('Location: ../student_portal/home.php');
                exit;
            }
        }
        else{
            // Invalid student password
            $_SESSION['msg']="invalid username or password";
            header('Location: login.php'); 
            exit;
        }
    }
}
 mysqli_close($con);   
?>
