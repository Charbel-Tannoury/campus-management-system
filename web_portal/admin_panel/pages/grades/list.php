<?php
/**
 * Grades List Management Page
 * 
 * Comprehensive grade viewing and management interface.
 * Features:
 * - Advanced filtering (student, course, professor, semester)
 * - Session-based filter persistence
 * - Edit and view grade actions
 * - Success message display
 * - Faculty-filtered access
 * - Displays all grade components
 * - Reset filters button
 * 
 * Filtering Options:
 * - Student (dropdown)
 * - Course (dropdown)
 * - Professor (dropdown)
 * - Semester (dropdown)
 * - Filters stored in session (persist across page loads)
 * - Reset button clears all filters
 * 
 * Table Columns:
 * - # (row number)
 * - Student Name
 * - Course
 * - Professor
 * - Semester
 * - Project Grade (/30)
 * - Mid Grade (/30)
 * - First Final (/40)
 * - Second Final (/40) - if taken
 * - Total (/100)
 * - Actions (Edit button)
 * 
 * Query:
 * - Joins grades, enrollment, students, to_enrol, mcs, courses, doctors, semester
 * - Filters by faculty and any active search criteria
 * - Orders by student name
 * 
 * Success Messages:
 * - '1': Grade added successfully
 * - '2': Grade updated successfully
 * - Cleared after display
 * 
 * Security:
 * - Session validation
 * - Faculty-based access control
 */

session_start();
require_once '../../../connection.php';
// tn2akad luser msajal
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];

// Handle reset button to clear filters (MUST be before any output)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["reset"])){
    unset($_SESSION['student_id']);
    unset($_SESSION['course_id']);
    unset($_SESSION['dr_id']);
    unset($_SESSION['semester_id']);
    header("Location: list.php");
    exit;
}

// Handle search form submission and store filters in session (MUST be before any output)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"])){
    $_SESSION['student_id'] = $_POST["student_id"] ?? '';
    $_SESSION['course_id'] = $_POST["course_id"] ?? '';
    $_SESSION['dr_id'] = $_POST["dr_id"] ?? '';
    $_SESSION['semester_id'] = $_POST["semester_id"] ?? '';
}

// Get filters from session or defaults
$student_id = $_SESSION['student_id'] ?? '';
$course_id = $_SESSION['course_id'] ?? '';
$dr_id = $_SESSION['dr_id'] ?? '';
$semester_id = $_SESSION['semester_id'] ?? '';

// n7dod success message
$success_msg = '';
if (isset($_SESSION['success'])) {
    if ($_SESSION['success'] == '1') {
        $success_msg = "Grade added successfully!";
    } elseif ($_SESSION['success'] == '2') {
        $success_msg = "Grade updated successfully!";
    }
    unset($_SESSION['success']); // Clear after displaying
}

$page_title = 'Grades List';
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
            <div class="page-header"><h1>Grades List</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><span>Grades</span><span>/</span><span>Grades List</span></div></div>
            
            <?php if ($success_msg): ?>
            <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="color: #155724;"></i>
                <span style="color: #155724;"><?php echo $success_msg; ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Filters - filtrat lba7as -->
             <form method="POST">
            <div class="card" style="margin-bottom: 25px;">
                <div class="card-header"><h3>Filter Results</h3></div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <select class="form-control" id="filterStudent" name="student_id">
                            <option value="">All Students</option>
                            <?php
                            $students_q = "SELECT DISTINCT s.student_id, s.first_name, s.last_name FROM students s 
                                          JOIN enrollment e ON s.student_id = e.student_id
                                          JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                                          WHERE t.faculty_id = '$fac'";
                            $students_r = mysqli_query($con, $students_q);
                            while ($st = mysqli_fetch_assoc($students_r)) {
                                echo "<option value='" . $st['student_id']."'";
                                if($student_id==$st['student_id'])echo "selected";
                                echo ">" . htmlspecialchars($st['first_name'] . ' ' . $st['last_name']) . "</option>";
                            }
                            ?>
                        </select>
                        <select class="form-control" id="filterCourse" name = "course_id">
                            <option value="">All Courses</option>
                            <?php
                            $courses_q = "SELECT DISTINCT c.course_id, c.course_name FROM courses c
                                         JOIN major_course_semester mcs ON c.course_id = mcs.course_id
                                         JOIN to_enrol t ON mcs.mcs_id = t.mcs_id
                                         WHERE t.faculty_id = '$fac'";
                            $courses_r = mysqli_query($con, $courses_q);
                            while ($cr = mysqli_fetch_assoc($courses_r)) {
                                echo "<option value='" . $cr['course_id'] . "'";
                                if($course_id==$cr['course_id'])echo "selected";
                                echo ">" . htmlspecialchars($cr['course_id'] . ' - ' . $cr['course_name']) . "</option>";
                            }
                            ?>
                        </select>
                        <select class="form-control" id="filterDoctor" name="dr_id">
                            <option value="">All Professors</option>
                            <?php
                            $doctors_q = "SELECT DISTINCT d.dr_id, d.first_name, d.last_name FROM doctors d
                                         JOIN to_enrol t ON d.dr_id = t.dr_id
                                         WHERE t.faculty_id = '$fac'";
                            $doctors_r = mysqli_query($con, $doctors_q);
                            while ($dr = mysqli_fetch_assoc($doctors_r)) {
                                echo "<option value='" . $dr['dr_id'] . "'";
                                if($dr_id==$dr['dr_id'])echo "selected";
                                echo ">Dr. " . htmlspecialchars($dr['first_name'] . ' ' . $dr['last_name']) . "</option>";
                            }
                            ?>
                        </select>
                        <select class="form-control" id="filterSemester" name="semester_id">
                            <option value="">All Semesters</option>
                            <?php
                            $semesters_q = "SELECT * FROM semester";
                            $semesters_r = mysqli_query($con, $semesters_q);
                            while ($sem = mysqli_fetch_assoc($semesters_r)) {
                                echo "<option value='" . $sem['semester_id'] . "'";
                                if($semester_id==$sem['semester_id'])echo "selected";
                                echo ">" . htmlspecialchars($sem['semester_id']) . "</option>";
                            }
                            ?>
                        </select>
                        <button class="btn btn-primary" type="submit" name="submit" value="1"><i class="fas fa-search"></i>Search</button>
                        <button class="btn btn-outline" type="submit" name="reset" value="1"><i class="fas fa-redo"></i>Reset</button>
                        
                    </div>
                </div>
            </div>
             </form>
            <div class="card">
                <div class="card-header"><h3>All Grades</h3>
                    <div style="display: flex; gap: 10px;">
                        <a href="add.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i>Add Grades</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gradeTable">
                            <thead><tr><th>#</th><th>Student</th><th>Student ID</th><th>Course</th><th>Professor</th><th>Project</th><th>Mid</th><th>Final 1</th><th>Final 2</th><th>Total</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php
                            // njib ld3alamat mn ldatabase
                            $fac = $_SESSION['Faculty'];
                            $query = "SELECT g.*, e.student_id as sid, e.enrol_id,
                                      s.first_name as student_fname, s.last_name as student_lname,
                                      mcs.course_id as course_id, c.course_name,
                                      d.first_name as dr_fname, d.last_name as dr_lname
                                      FROM grades g
                                      JOIN enrollment e ON g.enrol_id = e.enrol_id
                                      JOIN students s ON e.student_id = s.student_id
                                      JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                                      JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
                                      JOIN courses c ON mcs.course_id = c.course_id
                                      JOIN doctors d ON t.dr_id = d.dr_id
                                      WHERE t.faculty_id = '$fac'";
                                      if($student_id!=""){
                                          $query.=" AND e.student_id='$student_id'";
                                      }
                                      if($course_id!=""){
                                          $query.=" AND mcs.course_id='$course_id'";
                                      }
                                      if($dr_id!=""){
                                          $query.=" AND t.dr_id='$dr_id'";
                                      }
                                      if($semester_id!=""){
                                          $query.=" AND mcs.semester_id='$semester_id'";
                                      }
                                      $query.="ORDER BY g.grade_id DESC";
                            $result = mysqli_query($con, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $student_name = htmlspecialchars($row['student_fname'] . ' ' . $row['student_lname']);
                                    $course = htmlspecialchars($row['course_id'] . ' - ' . $row['course_name']);
                                    $professor = 'Dr. ' . htmlspecialchars($row['dr_fname'] . ' ' . $row['dr_lname']);
                                    
                                    // 7sab lmajmou3
                                    $project = $row['project_grade'] ?? 0;
                                    $mid = $row['mid_grade'] ?? 0;
                                    $final1 = $row['first_final'] ?? 0;
                                    $final2 = $row['second_final'] ?? 0;
                                    $total = $project + $mid + max($final1, $final2);
                                    
                                    // t7did lgrade
                                    if ($total >= 90) $grade = 'A+';
                                    elseif ($total >= 85) $grade = 'A';
                                    elseif ($total >= 80) $grade = 'B+';
                                    elseif ($total >= 75) $grade = 'B';
                                    elseif ($total >= 70) $grade = 'C+';
                                    elseif ($total >= 65) $grade = 'C';
                                    elseif ($total >= 60) $grade = 'D';
                                    else $grade = 'F';
                                    
                                    $badgeClass = ($total >= 60) ? 'active' : 'inactive';
                                    
                                    echo "<tr>";
                                    echo "<td>" . $row['grade_id'] . "</td>";
                                    echo "<td>" . $student_name . "</td>";
                                    echo "<td>" . $row['sid'] . "</td>";
                                    echo "<td>" . $course . "</td>";
                                    echo "<td>" . $professor . "</td>";
                                    echo "<td>" . ($project ?: '-') . "</td>";
                                    echo "<td>" . ($mid ?: '-') . "</td>";
                                    echo "<td>" . ($final1 ?: '-') . "</td>";
                                    echo "<td>" . ($final2 ?: '-') . "</td>";
                                    echo "<td><span class='badge-status " . $badgeClass . "'>" . $total . " (" . $grade . ")</span></td>";
                                    echo "<td>
                                    <form method='post' action='edit.php' style='display:inline;'>";
                                    echo "<button type='submit' name='edit' value='" . $row['grade_id'] . "' class='btn btn-sm btn-primary' style='margin-right: 5px;'><i class='fas fa-edit'></i></button>";
                                    echo "</form>";
                                
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No grades found.</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
    <script>
        initTableSearch('searchGrade', 'gradeTable');
        
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
