<?php
/**
 * Add Course Processing Script
 * 
 * Handles the backend logic for creating new courses.
 * Features:
 * - Sequential multi-table insertion
 * - Creates course record
 * - Associates with major and semester
 * - Assigns professor and faculty
 * - Session-based error messaging
 * - Validation of required fields
 * - Current year assignment
 * 
 * Database Operations:
 * 1. INSERT into courses (course_id, course_name, course_details)
 * 2. INSERT into major_course_semester (major, course, semester, credits)
 * 3. INSERT into to_enrol (mcs_id, year, doctor, faculty)
 * 
 * POST Parameters:
 * - course_code: Unique course identifier
 * - course_name: Full course name (required)
 * - description: Course details (optional)
 * - major: Major ID (required)
 * - doctor: Professor ID
 * - credits: Credit hours (required, integer)
 * - semester: Semester ID (required)
 * 
 * Validation:
 * - Checks all required fields exist
 * - Converts credits to integer
 * - Uses current year automatically
 * 
 * Error Handling:
 * - Session messages for each step failure
 * - Redirects back to add.php on error
 * - Shows specific error context
 * 
 * Success:
 * - Redirects to list.php
 * 
 * Note: Should use transactions and prepared statements
 */

session_start();
    require_once '../../../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
    $fac=$_SESSION['Faculty'];
    $date = date("Y");
    if(!isset($_POST['course_code']) || !isset($_POST['course_name']) || !isset($_POST['major']) || !isset($_POST['credits']) || !isset($_POST['semester'])){
        $_SESSION['msg']="Please fill in all required fields.";
        header("Location: add.php");
        exit();
    }
    else{
        $course_code = trim($_POST['course_code']);
        $course_name = trim($_POST['course_name']);
        $description = trim($_POST['description']);
        $major = trim($_POST['major']);
        $doctor = trim($_POST['doctor']);
        $credits = (int)$_POST['credits'];
        $semester = trim($_POST['semester']);

        $query="INSERT INTO courses (course_id, course_name,course_details) VALUES ('$course_code', '$course_name', '$description')";
        $result = mysqli_query($con, $query);
        if(!$result){
            $_SESSION['msg']="There was an error adding the course. Please try again.";
            header("Location: add.php");
            exit();
        }
        $query2="INSERT INTO major_course_semester (major_id, course_id, semester_id, credits) VALUES ('$major', '$course_code', '$semester', '$credits')";
        $result2 = mysqli_query($con, $query2);
        if(!$result2){
            $_SESSION['msg']="There was an error associating the course with the major and semester. Please try again.";
            header("Location: add.php");
            exit();
        }
        $query3="INSERT INTO to_enrol (mcs_id, year, dr_id, faculty_id) VALUES ((SELECT mcs_id FROM major_course_semester WHERE  course_id='$course_code'), $date, $doctor, '$fac')";
        $result3 = mysqli_query($con, $query3);
        if(!$result3){
            $_SESSION['msg']="There was an error finalizing the course addition. Please try again.";
            header("Location: add.php");
            exit();
        }
        header("Location: list.php");
        exit();
    }
?>