<?php
/**
 * Add Grades Page
 * 
 * Admin interface for adding grades to student enrollments.
 * Features:
 * - Lists all enrollments WITHOUT grades (using LEFT JOIN with NULL check)
 * - Faculty-based filtering (only shows enrollments from admin's faculty)
 * - Form for entering grade components:
 *   - Project grade
 *   - Midterm grade
 *   - First final exam grade
 * - Validation to prevent duplicate grades
 * - Statistics display: total enrollments, graded, pending
 * - Automatic redirect to list page after successful grade insertion
 * 
 * Grade Components:
 * - project_grade: Project/assignment grade
 * - mid_grade: Midterm exam grade
 * - first_final: First attempt final exam grade
 * - second_final: Not entered here (used for retake exams)
 * 
 * Workflow:
 * 1. Display enrollments without grades
 * 2. Admin selects enrollment and enters grades
 * 3. Validation checks if enrollment exists and has no grade
 * 4. Insert new grade record
 * 5. Redirect to grades list
 */

session_start();
require_once '../../../connection.php';
// Security check: Verify employee is logged in
// tn2akad luser msajal (ensure user is registered)
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];  // Current faculty ID for filtering
$con = $con;  // Database connection

// Process grade submission form
// n3alej form lma ytsave (handle form when saved)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_grade'])) {
    // Sanitize and validate input
    $enrol_id = intval($_POST['enrol_id']);
    $project_grade = intval($_POST['project_grade']);
    $mid_grade = intval($_POST['mid_grade']);
    $first_final = intval($_POST['first_final']);
    
    // Validation Step 1: Check if enrollment ID exists
    // n2akad eno lenrol_id mwjoud (ensure enrol_id exists)
    $check_query = "SELECT enrol_id FROM enrollment WHERE enrol_id = '$enrol_id'";
    $result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        // Validation Step 2: Check if grade already exists for this enrollment
        // n2akad ma fi grade la hal enrollment (ensure no grade exists for this enrollment)
        $check_grade_query = "SELECT grade_id FROM grades WHERE enrol_id = '$enrol_id'";
        $grade_result = mysqli_query($con, $check_grade_query);
        
        if (mysqli_num_rows($grade_result) > 0) {
            $error_msg = "Grade already exists for this enrollment!";
        } else {
            // Insert new grade record
            // ndkhel grade jdide (insert new grade)
            $insert_query = "INSERT INTO grades (enrol_id, project_grade, mid_grade, first_final) VALUES ('$enrol_id', '$project_grade', '$mid_grade', '$first_final')";
            
            if (mysqli_query($con, $insert_query)) {
                $success_msg = "Grade added successfully!";
                $_SESSION['success'] = '1';
                header("Location: list.php");
                exit;
            } else {
                $error_msg = "Error adding grade: " . mysqli_error($con);
            }
        }
    } else {
        $error_msg = "Invalid enrollment selected!";
    }
}

// Fetch all enrollments in this faculty that DO NOT have grades yet
// njib ltollab lmsajlin b hal faculty - bas lli ma 3ndon grades
// (get students enrolled in this faculty - only those without grades)
// Uses LEFT JOIN with NULL check to find enrollments without corresponding grade records
$students_query = "SELECT DISTINCT s.student_id, s.first_name, s.last_name, s.student_id as reg_number,
                   e.enrol_id, c.course_id, c.course_name, d.first_name as dr_fname, d.last_name as dr_lname
                   FROM students s
                   JOIN enrollment e ON s.student_id = e.student_id
                   JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                   JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
                   JOIN courses c ON mcs.course_id = c.course_id
                   JOIN doctors d ON t.dr_id = d.dr_id
                   LEFT JOIN grades g ON e.enrol_id = g.enrol_id
                   WHERE t.faculty_id = '$fac' AND g.grade_id IS NULL
                   ORDER BY s.last_name, s.first_name, c.course_id";
$enrollments_result = mysqli_query($con, $students_query);

// njib kll lenrollments la hal faculty (7ata lli 3ndon grades) la n3rod count
$all_enrollments_query = "SELECT COUNT(*) as total FROM enrollment e
                          JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                          WHERE t.faculty_id = '$fac'";
$all_result = mysqli_query($con, $all_enrollments_query);
$all_count = mysqli_fetch_assoc($all_result)['total'] ?? 0;

// njib count lli 3ndon grades
$graded_query = "SELECT COUNT(*) as graded FROM enrollment e
                 JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                 JOIN grades g ON e.enrol_id = g.enrol_id
                 WHERE t.faculty_id = '$fac'";
$graded_result = mysqli_query($con, $graded_query);
$graded_count = mysqli_fetch_assoc($graded_result)['graded'] ?? 0;

$pending_count = $all_count - $graded_count;
$page_title = 'Add Grades';
include '../../includes/head.php';
?>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'grades';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            <div class="page-header">
                <h1>Add New Grades</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a><span>/</span>
                    <a href="list.php">Grades</a><span>/</span>
                    <span>Add Grades</span>
                </div>
            </div>

            <!-- Res2il lna7 aw lkhata2 -->
            <?php if (isset($success_msg)): ?>
            <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="color: #155724;"></i>
                <span style="color: #155724;"><?php echo $success_msg; ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error_msg)): ?>
            <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle" style="color: #721c24;"></i>
                <span style="color: #721c24;"><?php echo $error_msg; ?></span>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header"><h3>Grade Information</h3></div>
                <div class="card-body">
                    <form id="addGradeForm" method="POST" action="">
                        <!-- 5atar ltollab - njibhon mn database -->
                        <h3 style="margin: 0 0 20px; color: #667eea;"><i class="fas fa-user-graduate"></i> Select Student & Course</h3>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Select Student - Course Enrollment <span style="color: #f56565;">*</span></label>
                            <select class="form-control" name="enrol_id" id="enrolSelect" required>
                                <option value="">-- Select Enrollment --</option>
                                <?php
                                if ($enrollments_result && mysqli_num_rows($enrollments_result) > 0) {
                                    while ($row = mysqli_fetch_assoc($enrollments_result)) {
                                        echo "<option value='" . $row['enrol_id'] . "' 
                                              data-student='" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "'
                                              data-course='" . htmlspecialchars($row['course_id'] . ' - ' . $row['course_name']) . "'
                                              data-doctor='Dr. " . htmlspecialchars($row['dr_fname'] . ' ' . $row['dr_lname']) . "'>";
                                        echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . " (" . $row['reg_number'] . ") - " . $row['course_id'] . ' - ' . $row['course_name'];
                                        echo "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <small style="color: #718096; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Total Enrollments: <?php echo $all_count; ?> | 
                                Already Graded: <?php echo $graded_count; ?> | 
                                <strong>Pending: <?php echo $pending_count; ?></strong>
                            </small>
                        </div>
                        
                        <?php if ($pending_count == 0 && $all_count > 0): ?>
                        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                            <i class="fas fa-info-circle" style="color: #856404;"></i>
                            <span style="color: #856404;"><strong>Note:</strong> All students in this faculty already have grades. You can edit existing grades from the <a href="list.php">Grades List</a>.</span>
                        </div>
                        <?php elseif ($all_count == 0): ?>
                        <div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                            <i class="fas fa-exclamation-circle" style="color: #721c24;"></i>
                            <span style="color: #721c24;"><strong>No Enrollments Found!</strong> There are no student enrollments for this faculty. Please add enrollments first.</span>
                        </div>
                        <?php endif; ?>

                        <!-- byenat ltaleb w lkurs lma y5tar -->
                        <div id="selectedInfo" style="display: none; background: #f7fafc; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                            <h4 style="margin: 0 0 15px; color: #667eea;"><i class="fas fa-info-circle"></i> Selected Information</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                                <div><strong>Student:</strong> <span id="displayStudent">-</span></div>
                                <div><strong>Course:</strong> <span id="displayCourse">-</span></div>
                                <div><strong>Professor:</strong> <span id="displayDoctor">-</span></div>
                            </div>
                        </div>

                        <!-- 3alamat - project, mid, final hasab ljadwel -->
                        <h3 style="margin: 30px 0 20px; padding-top: 20px; border-top: 2px solid #e2e8f0;">Grades</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Project Grade <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="project_grade" id="projectGrade" required min="0" max="30" placeholder="out of 30">
                                <small style="color: #718096; display: block; margin-top: 5px;">Maximum: 30</small>
                            </div>

                            <div class="form-group">
                                <label>Mid Grade <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="mid_grade" id="midGrade" required min="0" max="30" placeholder="out of 30">
                                <small style="color: #718096; display: block; margin-top: 5px;">Maximum: 30</small>
                            </div>

                            <div class="form-group">
                                <label>First Final <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="first_final" id="firstFinal" required min="0" max="40" placeholder="out of 40">
                                <small style="color: #718096; display: block; margin-top: 5px;">Maximum: 40</small>
                            </div>

                            <div class="form-group">
                                <label>Total Grade</label>
                                <input type="number" class="form-control" name="total_grade" id="totalGrade" readonly placeholder="out of 100" style="background: #f7fafc; cursor: not-allowed;">
                                <small style="color: #718096; display: block; margin-top: 5px;">Calculated automatically</small>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Letter Grade</label>
                                <input type="text" class="form-control" name="letter_grade" id="letterGrade" readonly placeholder="A+, A, B+, ..." style="background: #f7fafc; cursor: not-allowed;">
                                <small style="color: #718096; display: block; margin-top: 5px;">Calculated automatically based on total</small>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control" name="status" id="statusGrade" readonly placeholder="Pass/Fail" style="background: #f7fafc; cursor: not-allowed;">
                                <small style="color: #718096; display: block; margin-top: 5px;">Pass if >= 50</small>
                            </div>
                        </div>

                        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 8px; margin: 20px 0;">
                            <h4 style="margin: 0 0 10px; color: #1976d2;"><i class="fas fa-info-circle"></i> Grading System:</h4>
                            <div style="font-size: 14px;">
                                <p><strong>Total = Project (30) + Mid (30) + First Final (40) = 100</strong></p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                    <div>90-100 = A+</div>
                                    <div>85-89 = A</div>
                                    <div>80-84 = B+</div>
                                    <div>75-79 = B</div>
                                    <div>70-74 = C+</div>
                                    <div>60-69 = C</div>
                                    <div>50-59 = D</div>
                                    <div>Below 50 = F</div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="save_grade" value="1">

                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Grades
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-redo"></i> Reset
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
        // lma y5tar enrollment, n3rod lbyenat
        document.getElementById('enrolSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const infoDiv = document.getElementById('selectedInfo');
            
            if (this.value) {
                document.getElementById('displayStudent').textContent = selectedOption.dataset.student || '-';
                document.getElementById('displayCourse').textContent = selectedOption.dataset.course || '-';
                document.getElementById('displayDoctor').textContent = selectedOption.dataset.doctor || '-';
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        });
                
                // n7sob l3alamat automatically
        function calculateGrades() {
            const projectGrade = parseFloat(document.getElementById('projectGrade').value) || 0;
            const midGrade = parseFloat(document.getElementById('midGrade').value) || 0;
            const firstFinal = parseFloat(document.getElementById('firstFinal').value) || 0;
            
            // total = project + mid + final
            const totalGrade = projectGrade + midGrade + firstFinal;
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
        document.getElementById('projectGrade').addEventListener('input', function() {
            if (this.value > 30) this.value = 30;
            if (this.value < 0) this.value = 0;
            calculateGrades();
        });

        document.getElementById('midGrade').addEventListener('input', function() {
            if (this.value > 30) this.value = 30;
            if (this.value < 0) this.value = 0;
            calculateGrades();
        });

        document.getElementById('firstFinal').addEventListener('input', function() {
            if (this.value > 40) this.value = 40;
            if (this.value < 0) this.value = 0;
            calculateGrades();
        });

        // reset form
        function resetForm() {
            document.getElementById('addGradeForm').reset();
            document.getElementById('totalGrade').value = '';
            document.getElementById('letterGrade').value = '';
            document.getElementById('statusGrade').value = '';
            document.getElementById('selectedInfo').style.display = 'none';
        }
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
