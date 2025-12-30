<?php
/**
 * Edit Grade Page
 * 
 * Admin interface for modifying student grade records.
 * Features:
 * - Edit all grade components (project, mid, finals)
 * - Support for second final exam (makeup)
 * - Pre-filled form with current grades
 * - Displays student and course information
 * - Session-based success messaging
 * - Clears search filters after update
 * - Faculty-based access control
 * 
 * Grade Components:
 * - Project Grade (max 30 points)
 * - Midterm Grade (max 30 points)
 * - First Final (max 40 points)
 * - Second Final (optional makeup, max 40 points)
 * 
 * Form Workflow:
 * 1. POST with 'edit' from list page: Loads grade data
 * 2. POST with 'update_grade': Saves changes
 * 
 * Query:
 * - Joins grades, enrollment, students, to_enrol, mcs, courses, doctors
 * - Filters by grade_id and faculty
 * - Shows complete context (student, course, professor, semester)
 * 
 * Update Logic:
 * - If second_final provided: Updates all 4 components
 * - If second_final empty: Updates first 3 components only
 * - Sets session success flag
 * - Clears search filters from session
 * - Redirects to list page
 * 
 * Security:
 * - Session validation
 * - Faculty-based filtering
 * - Integer validation on grade_id
 */

session_start();
require_once '../../../connection.php';
// tn2akad luser msajal
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$con = $con;
$fac = $_SESSION['Faculty'];

// njib grade_id mn lPOST only (from list POST button 'edit' or form hidden 'grade_id')
$grade_id = 0;
if (isset($_POST['edit'])) {
    $grade_id = intval($_POST['edit']);
} elseif (isset($_POST['grade_id'])) {
    $grade_id = intval($_POST['grade_id']);
}

if ($grade_id <= 0) {
    header('Location: list.php');
    exit;
}

// n3alej form lma ytsave
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_grade'])) {
    $project_grade = intval($_POST['project_grade']);
    $mid_grade = intval($_POST['mid_grade']);
    $first_final = intval($_POST['first_final']);
    $second_final = !empty($_POST['second_final']) ? intval($_POST['second_final']) : null;
    
    // n7adeth l3alamat
    if ($second_final !== null) {
        $update_query = "UPDATE grades SET project_grade='$project_grade', mid_grade='$mid_grade', first_final='$first_final', second_final='$second_final' WHERE grade_id='$grade_id'";
    } else {
        $update_query = "UPDATE grades SET project_grade='$project_grade', mid_grade='$mid_grade', first_final='$first_final' WHERE grade_id='$grade_id'";
    }
    
    if (mysqli_query($con, $update_query)) {
        $_SESSION['success'] = '2';
        // Clear search filters after successful update
        unset($_SESSION['student_id']);
        unset($_SESSION['course_id']);
        unset($_SESSION['dr_id']);
        unset($_SESSION['semester_id']);
        header("Location: list.php");
        exit;
    } else {
        $error_msg = "Error updating grade: " . mysqli_error($con);
    }
}

// njib byenat lgrade ma3 ltollab w lkurs

$page_title = 'Edit Grade';
include '../../includes/head.php';
?>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'grades';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; 
        $query = "SELECT g.grade_id,g.enrol_id,g.project_grade,g.mid_grade,g.first_final,g.second_final , s.first_name as student_fname, s.last_name as student_lname, s.student_id,
          c.course_id, c.course_name, d.first_name as dr_fname, d.last_name as dr_lname, mcs.semester_id
          FROM grades g
          JOIN enrollment e ON g.enrol_id = e.enrol_id
          JOIN students s ON e.student_id = s.student_id
          JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
          JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
          JOIN courses c ON mcs.course_id = c.course_id
          JOIN doctors d ON t.dr_id = d.dr_id
          WHERE g.grade_id = '$grade_id' AND t.faculty_id = '$fac'";
          
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: list.php');
    exit;
}

$grade_data = mysqli_fetch_assoc($result);
?>

        <div class="page-content">
            <div class="page-header">
                <h1>Edit Grade</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a><span>/</span>
                    <a href="list.php">Grades</a><span>/</span>
                    <span>Edit Grade</span>
                </div>
            </div>

            <!-- res2il success/error -->
            <?php if (isset($error_msg)): ?>
            <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle" style="color: #721c24;"></i>
                <span style="color: #721c24;"><?php echo $error_msg; ?></span>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h3>Grade Information - Edit</h3></div>
                <div class="card-body">
                    <form id="editGradeForm" method="POST" action="">
                        <input type="hidden" name="grade_id" value="<?php echo $grade_id; ?>">
                        <input type="hidden" name="update_grade" value="1">

                        <!-- byenat ltaleb w lkurs (read-only) -->
                        <div style="background: #f7fafc; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                            <h4 style="margin: 0 0 15px; color: #667eea;"><i class="fas fa-user-graduate"></i> Student & Course Information</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label>Student Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($grade_data['student_fname'] . ' ' . $grade_data['student_lname']); ?>" readonly style="background: #edf2f7; cursor: not-allowed;">
                                </div>
                                <div class="form-group">
                                    <label>Student ID</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($grade_data['student_id']); ?>" readonly style="background: #edf2f7; cursor: not-allowed;">
                                </div>
                                <div class="form-group">
                                    <label>Course</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($grade_data['course_id'] . ' - ' . $grade_data['course_name']); ?>" readonly style="background: #edf2f7; cursor: not-allowed;">
                                </div>
                                <div class="form-group">
                                    <label>Professor</label>
                                    <input type="text" class="form-control" value="Dr. <?php echo htmlspecialchars($grade_data['dr_fname'] . ' ' . $grade_data['dr_lname']); ?>" readonly style="background: #edf2f7; cursor: not-allowed;">
                                </div>
                                <div class="form-group">
                                    <label>Semester</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($grade_data['semester_id']); ?>" readonly style="background: #edf2f7; cursor: not-allowed;">
                                </div>
                            </div>
                        </div>

                        <h3 style="margin: 30px 0 20px; padding-top: 20px; border-top: 2px solid #e2e8f0;">Edit Grades</h3>

                        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Project Grade <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="project_grade" id="projectGrade" required min="0" max="30" value="<?php echo intval($grade_data['project_grade']); ?>">
                                <small style="color: #718096; display: block; margin-top: 5px;">Max: 30</small>
                            </div>

                            <div class="form-group">
                                <label>Mid Grade <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="mid_grade" id="midGrade" required min="0" max="30" value="<?php echo intval($grade_data['mid_grade']); ?>">
                                <small style="color: #718096; display: block; margin-top: 5px;">Max: 30</small>
                            </div>

                            <div class="form-group">
                                <label>First Final <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="first_final" id="firstFinal" required min="0" max="40" value="<?php echo intval($grade_data['first_final']); ?>">
                                <small style="color: #718096; display: block; margin-top: 5px;">Max: 40</small>
                            </div>

                            <div class="form-group">
                                <label>Second Final</label>
                                <input type="number" class="form-control" name="second_final" id="secondFinal" min="0" max="40" value="<?php echo $grade_data['second_final'] ? intval($grade_data['second_final']) : ''; ?>" placeholder="Retake">
                                <small style="color: #718096; display: block; margin-top: 5px;">Optional retake</small>
                            </div>

                            <div class="form-group">
                                <label>Total Grade</label>
                                <input type="number" class="form-control" name="total_grade" id="totalGrade" readonly style="background: #f7fafc; cursor: not-allowed;">
                                <small style="color: #718096; display: block; margin-top: 5px;">Auto calculated</small>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Letter Grade</label>
                                <input type="text" class="form-control" name="letter_grade" id="letterGrade" readonly style="background: #f7fafc; cursor: not-allowed;">
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control" name="status" id="statusGrade" readonly style="background: #f7fafc; cursor: not-allowed;">
                            </div>
                        </div>

                        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 8px; margin: 20px 0;">
                            <h4 style="margin: 0 0 10px; color: #1976d2;"><i class="fas fa-info-circle"></i> Grading Info:</h4>
                            <p style="margin: 0; font-size: 14px;">Total = Project (30) + Mid (30) + MAX(First Final, Second Final) (40) = 100</p>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Grade
                            </button>
                            <a href="list.php" class="btn btn-outline">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/main.js"></script>
    <script>
        // n7sob l3alamat automatically
        function calculateGrades() {
            const projectGrade = parseFloat(document.getElementById('projectGrade').value) || 0;
            const midGrade = parseFloat(document.getElementById('midGrade').value) || 0;
            const firstFinal = parseFloat(document.getElementById('firstFinal').value) || 0;
            const secondFinal = parseFloat(document.getElementById('secondFinal').value) || 0;
            
            // total = project + mid + max(first, second)
            const finalUsed = Math.max(firstFinal, secondFinal);
            const totalGrade = projectGrade + midGrade + finalUsed;
            document.getElementById('totalGrade').value = totalGrade;

            // n7dod letter grade
            let letterGrade = '';
            if (totalGrade >= 90) letterGrade = 'A+';
            else if (totalGrade >= 85) letterGrade = 'A';
            else if (totalGrade >= 80) letterGrade = 'B+';
            else if (totalGrade >= 75) letterGrade = 'B';
            else if (totalGrade >= 70) letterGrade = 'C+';
            else if (totalGrade >= 60) letterGrade = 'C';
            else if (totalGrade >= 50) letterGrade = 'D';
            else letterGrade = 'F';

            document.getElementById('letterGrade').value = letterGrade;
            document.getElementById('statusGrade').value = totalGrade >= 50 ? 'Pass' : 'Fail';
        }

        // event listeners la 7sab l3alamat
        ['projectGrade', 'midGrade', 'firstFinal', 'secondFinal'].forEach(id => {
            document.getElementById(id).addEventListener('input', function() {
                const max = id === 'firstFinal' || id === 'secondFinal' ? 40 : 30;
                if (this.value > max) this.value = max;
                if (this.value < 0) this.value = 0;
                calculateGrades();
            });
        });

        // n7sob 3al awwal lma tft7 lsaf7a
        calculateGrades();
    </script>

    <script type="text/javascript">
    function googleTranslateElementInit() {
  new google.translate.TranslateElement(
    {pageLanguage: 'en'},
    'google_translate_element'
  );
}
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
</body>
</html>
