<?php
/**
 * Add Course Page
 * 
 * This page provides a form for faculty administrators to add new courses to the system.
 * It handles:
 * - Session validation for logged-in employees
 * - Faculty-based major filtering (shows only majors available in the employee's faculty)
 * - Dynamic semester selection
 * - Professor assignment to courses
 * - Form validation for required fields (course code, name, major, credits, semester)
 */

// Start the session and include database connection
session_start();
require_once '../../../connection.php';
// Security check: Ensure user is logged in with valid session
// Redirects to login if Faculty or emp_id session variables are not set
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// Store current faculty ID for filtering queries
$fac=$_SESSION['Faculty'];

$page_title = "Add Course";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>

        <!-- han section mnzyd course -->
        <div class="page-content">
            <div class="page-header"><h1>Add New Course</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><a href="list.php">Courses</a><span>/</span><span>Add Course</span></div></div>
            <div class="card">
                <div class="card-header"><h3>Course Information</h3></div>
                <div class="card-body">
                    <form id="addCourseForm" data-validate action="add_process.php" method="post">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <?php
                                // Display error message if set from previous request
                                // Message is stored in session to survive redirect
                                if (isset($_SESSION['msg'])) {
                                    echo '<div class="alert alert-danger">'.$_SESSION['msg'].'</div>';
                                    unset($_SESSION['msg']); // Clear message after display
                                }
                            ?>
                            <div class="form-group"><label>Course Code <span style="color: #f56565;">*</span></label><input type="text" class="form-control" name="course_code" required placeholder="Example: CS101"></div>
                            <div class="form-group"><label>Course Name <span style="color: #f56565;">*</span></label><input type="text" class="form-control" name="course_name" required placeholder="Example: Introduction to Computer Science"></div>
                        </div>
                        <div class="form-group"><label>Description</label><textarea class="form-control" name="description" placeholder="Course description..." rows="4"></textarea></div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group"><label>Major <span style="color: #f56565;">*</span></label>
                            <?php
                                // Fetch all majors available in the current faculty
                                // Uses complex join to ensure only faculty-relevant majors are shown
                                // Query joins: majors -> major_course_semester -> to_enrol (filtered by faculty_id)
                                $query = "SELECT  DISTINCT(m.major_id),m.major_name FROM majors m JOIN major_course_semester mm on m.major_id = mm.major_id JOIN to_enrol t ON mm.mcs_id = t.mcs_id WHERE t.faculty_id='$fac'";
                                $result = mysqli_query($con, $query);

                                echo '<select class="form-control" name="major" required>';
                                while ($dept_row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="'.$dept_row['major_id'].'">'.$dept_row['major_name'].'</option>';
                                }
                                echo '</select>';
                            ?>
                            <div class="form-group"><label>Responsible Professor</label>
                                <?PHP
                                    $query = "SELECT dr_id, first_name, last_name FROM doctors";
                                    $result = mysqli_query($con, $query);

                                    echo '<select class="form-control" name="doctor">';
                                    echo '<option value="" selected>-- Select Professor --</option>';
                                    while ($doc_row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="'.$doc_row['dr_id'].'">'.$doc_row['first_name'].' '.$doc_row['last_name'].'</option>';
                                    }
                                    echo '</select>';
                                ?>
                        </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group"><label>Credits <span style="color: #f56565;">*</span></label><input type="number" class="form-control" name="credits" required min="1" max="6" value="3"></div>
                            <div class="form-group"><label>Semester <span style="color: #f56565;">*</span></label>
                                    <?php
                                    $query = "SELECT semester_id FROM semester";
                                    $result = mysqli_query($con, $query);
                                    echo '<select class="form-control" name="semester" required>';
                                    while ($sem_row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="'.$sem_row['semester_id'].'">'.$sem_row['semester_id'].'</option>';
                                    }
                                    echo '</select>';   
                                    ?>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Save Course</button>
                            <a href="list.php" class="btn btn-outline"><i class="fas fa-times"></i>Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
   <?php include '../../includes/footer.php'; ?>
