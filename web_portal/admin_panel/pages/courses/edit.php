<?php
/**
 * Edit Course Form Page
 * 
 * Admin interface for modifying existing course records.
 * Features:
 * - Pre-populated form with current course data
 * - Updates course code, name, description
 * - Modifies major, credits, semester associations
 * - Changes professor assignment
 * - Breadcrumb navigation
 * - Faculty-based access control
 * - Validates course existence before display
 * 
 * Form Fields:
 * - Course Code (can be changed)
 * - Course Name
 * - Description (textarea)
 * - Major (dropdown)
 * - Professor (dropdown)
 * - Credits (number input)
 * - Semester (dropdown)
 * 
 * Query Logic:
 * - Joins courses, major_course_semester, to_enrol
 * - Filters by course_id and faculty_id
 * - Uses prepared statement for security
 * - Redirects if course not found or not in faculty
 * 
 * Processing:
 * - Submits to edit_process.php
 * - Includes original_course_id as hidden field
 * - Allows changing course_id itself
 * 
 * Security:
 * - Session validation
 * - Faculty-based filtering
 * - XSS protection via htmlspecialchars
 * - Prepared statements for queries
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];
$course_id = isset($_GET['id']) ? trim(str_replace('"', '', $_GET['id'])) : '';

if ($course_id === '') {
    header('Location: list.php');
    exit;
}

// Fetch course data
$query = "SELECT c.course_id, c.course_name, c.course_details, m.major_id, m.credits, m.semester_id, t.dr_id 
          FROM courses c 
          JOIN major_course_semester m ON c.course_id = m.course_id 
          JOIN to_enrol t ON m.mcs_id = t.mcs_id 
          WHERE c.course_id = ? AND t.faculty_id = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $course_id, $fac);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header('Location: list.php');
    exit;
}

$course = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$page_title = "Edit Course";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>

<div class="page-content">
    
    <!-- Page Header -->
    <div class="page-header">
        <h1>Edit Course</h1>
        <div class="breadcrumb">
            <a href="../../index.php">Home</a>
            <span>/</span>
            <a href="list.php">Courses</a>
            <span>/</span>
            <span>Edit Course</span>
        </div>
    </div>

    <!-- Edit Course Card -->
    <div class="card">
        <div class="card-header">
            <h3>Course Information</h3>
        </div>
        <div class="card-body">
            <form id="editCourseForm" action="edit_process.php" method="POST">
                
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="original_course_id" value="<?php echo htmlspecialchars($course['course_id']); ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    
                    <div class="form-group">
                        <label>Course Code <span style="color: #f56565;">*</span></label>
                        <input type="text" class="form-control" name="course_code" required placeholder="Example: CS101" value="<?php echo htmlspecialchars($course['course_id']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Course Name <span style="color: #f56565;">*</span></label>
                        <input type="text" class="form-control" name="course_name" required placeholder="Example: Introduction to Computer Science" value="<?php echo htmlspecialchars($course['course_name']); ?>">
                    </div>

                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" placeholder="Course description..." rows="4"><?php echo htmlspecialchars($course['course_details']); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    
                    <div class="form-group">
                        <label>Major <span style="color: #f56565;">*</span></label>
                        <?php
                            $query_majors = "SELECT DISTINCT(m.major_id), m.major_name 
                                           FROM majors m 
                                           JOIN major_course_semester mm ON m.major_id = mm.major_id 
                                           JOIN to_enrol t ON mm.mcs_id = t.mcs_id 
                                           WHERE t.faculty_id='$fac'";
                            $result_majors = mysqli_query($con, $query_majors);

                            echo '<select class="form-control" name="major" required>';
                            while ($major_row = mysqli_fetch_assoc($result_majors)) {
                                $selected = ($major_row['major_id'] == $course['major_id']) ? 'selected' : '';
                                echo '<option value="'.$major_row['major_id'].'" '.$selected.'>'.$major_row['major_name'].'</option>';
                            }
                            echo '</select>';
                        ?>
                    </div>

                    <div class="form-group">
                        <label>Responsible Professor</label>
                        <?php
                            $query_doctors = "SELECT dr_id, first_name, last_name FROM doctors";
                            $result_doctors = mysqli_query($con, $query_doctors);

                            echo '<select class="form-control" name="doctor">';
                            echo '<option value="">-- Select Professor --</option>';
                            while ($doc_row = mysqli_fetch_assoc($result_doctors)) {
                                $selected = ($doc_row['dr_id'] == $course['dr_id']) ? 'selected' : '';
                                echo '<option value="'.$doc_row['dr_id'].'" '.$selected.'>'.$doc_row['first_name'].' '.$doc_row['last_name'].'</option>';
                            }
                            echo '</select>';
                        ?>
                    </div>

                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    
                    <div class="form-group">
                        <label>Credits <span style="color: #f56565;">*</span></label>
                        <input type="number" class="form-control" name="credits" required min="1" max="6" value="<?php echo htmlspecialchars($course['credits']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Semester <span style="color: #f56565;">*</span></label>
                        <?php
                            $query_semesters = "SELECT semester_id FROM semester";
                            $result_semesters = mysqli_query($con, $query_semesters);
                            
                            echo '<select class="form-control" name="semester" required>';
                            while ($sem_row = mysqli_fetch_assoc($result_semesters)) {
                                $selected = ($sem_row['semester_id'] == $course['semester_id']) ? 'selected' : '';
                                echo '<option value="'.$sem_row['semester_id'].'" '.$selected.'>'.$sem_row['semester_id'].'</option>';
                            }
                            echo '</select>';
                        ?>
                    </div>

                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    <a href="list.php" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

</main>

<script src="../../assets/js/main.js"></script>

<?php include '../../includes/footer.php'; ?>
