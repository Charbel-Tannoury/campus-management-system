<?php
/**
 * Student Profile Detail Page (Admin View)
 * 
 * Comprehensive student profile for faculty administrators.
 * Features:
 * - Personal information display
 * - Academic major and enrollment details
 * - List of enrolled courses with professors
 * - Complete grade report with all components
 * - GPA calculation and display
 * - Faculty-filtered access (only shows students in admin's faculty)
 * - Semester-wise grade organization
 * 
 * Data Displayed:
 * 1. Student Information:
 *    - Name, email, phone
 *    - Major, enrollment status
 * 
 * 2. Enrolled Courses:
 *    - Course name, credits
 *    - Professor name
 *    - Semester
 * 
 * 3. Grades:
 *    - Project grade (30 points)
 *    - Midterm grade (30 points)
 *    - Final exam (40 points - first or second attempt)
 *    - Total score
 * 
 * 4. Academic Performance:
 *    - Overall GPA calculation
 *    - Converted to 4.0 scale
 * 
 * Security:
 * - Session validation (faculty + employee ID)
 * - Faculty-based access control
 * - Student ID validation from URL
 * 
 * Use Case: Faculty administrators viewing detailed student records
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];

// Get student ID from URL
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    header('Location: list.php');
    exit();
}

// Fetch student information
$query = "SELECT s.*, m.major_name, m.major_id
          FROM students s
          LEFT JOIN enrollment e ON s.student_id = e.student_id
          LEFT JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
          LEFT JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
          LEFT JOIN majors m ON mcs.major_id = m.major_id
          WHERE s.student_id = $student_id AND t.faculty_id = '$fac'
          LIMIT 1";
$result = mysqli_query($con, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header('Location: list.php?error=Student not found');
    exit();
}

// Fetch enrolled courses for this student
$courses_query = "SELECT c.course_id, c.course_name, mcs.credits, mcs.semester_id,
                         CONCAT(d.first_name, ' ', d.last_name) AS professor_name
                  FROM enrollment e
                  JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                  JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
                  JOIN courses c ON c.course_id = mcs.course_id
                  JOIN doctors d ON t.dr_id = d.dr_id
                  WHERE e.student_id = $student_id AND t.faculty_id = '$fac'";
$courses_result = mysqli_query($con, $courses_query);

// Fetch grades for this student
$grades_query = "SELECT c.course_id, c.course_name, mcs.semester_id,
                        g.project_grade, g.mid_grade, g.first_final, g.second_final
                 FROM grades g
                 JOIN enrollment e ON g.enrol_id = e.enrol_id
                 JOIN to_enrol t ON e.to_enrol_id = t.to_enrol_id
                 JOIN major_course_semester mcs ON t.mcs_id = mcs.mcs_id
                 JOIN courses c ON c.course_id = mcs.course_id
                 WHERE e.student_id = $student_id AND t.faculty_id = '$fac'";
$grades_result = mysqli_query($con, $grades_query);

// Calculate GPA
$total_points = 0;
$total_courses = 0;
$grades_data = [];
if ($grades_result && mysqli_num_rows($grades_result) > 0) {
    while ($grade = mysqli_fetch_assoc($grades_result)) {
        $grades_data[] = $grade;
        $total = ($grade['project_grade'] ?? 0) + ($grade['mid_grade'] ?? 0) + 
                 ($grade['second_final'] ?? $grade['first_final'] ?? 0);
        $total_courses++;
        // Simple GPA calculation (assuming 100 scale to 4.0)
        $total_points += ($total / 100) * 4;
    }
}
$gpa = $total_courses > 0 ? number_format($total_points / $total_courses, 2) : '0.00';
$page_title = 'Student Profile';
include '../../includes/head.php';
?>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'students';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            <div class="page-header"><h1>Student Profile</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><a href="list.php">Students</a><span>/</span><span>Student Profile</span></div></div>
            
            <!-- Student Info Card -->
            <div class="card">
                <div class="card-header"><h3>Student Information</h3><a href="edit.php?id=<?= $student['student_id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i>Edit</a></div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 30px;">
                        <div><img src="https://ui-avatars.com/api/?name=<?= urlencode($student['first_name'] . '+' . $student['last_name']) ?>&background=667eea&color=fff&size=200" style="width: 100%; border-radius: 12px; border: 3px solid #667eea;"></div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div><strong>Student ID:</strong> <?= $student['student_id'] ?></div>
                            <div><strong>Full Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                            <div><strong>Major:</strong> <?= htmlspecialchars($student['major_name'] ?? 'Not Assigned') ?></div>
                            <div><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></div>
                            <div><strong>Phone Number:</strong> <?= $student['phone_num'] ? htmlspecialchars($student['phone_num']) : 'N/A' ?></div>
                            <div><strong>Registered:</strong> <?= date('M d, Y', strtotime($student['created_at'])) ?></div>
                            <div><strong>Status:</strong> <span class="badge-status <?= $student['verification'] == 1 ? 'active' : 'pending' ?>"><?= $student['verification'] == 1 ? 'Verified' : 'Pending' ?></span></div>
                            <?php if ($student['verified_at']): ?>
                            <div><strong>Verified At:</strong> <?= date('M d, Y', strtotime($student['verified_at'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="card">
                <div class="card-header"><h3>Enrolled Courses</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Course Code</th><th>Course Name</th><th>Professor</th><th>Credits</th><th>Semester</th></tr></thead>
                            <tbody>
                                <?php if ($courses_result && mysqli_num_rows($courses_result) > 0): ?>
                                    <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['course_id']) ?></td>
                                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                                        <td>Dr. <?= htmlspecialchars($course['professor_name']) ?></td>
                                        <td><?= $course['credits'] ?></td>
                                        <td><?= htmlspecialchars($course['semester_id']) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" style="text-align: center;">No courses enrolled</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grades -->
            <div class="card">
                <div class="card-header"><h3>Grades and Results</h3></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Course</th><th>Semester</th><th>Project</th><th>Midterm</th><th>Final</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php if (!empty($grades_data)): ?>
                                    <?php foreach ($grades_data as $grade): 
                                        $final = $grade['second_final'] ?? $grade['first_final'] ?? 0;
                                        $total = ($grade['project_grade'] ?? 0) + ($grade['mid_grade'] ?? 0) + $final;
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($grade['course_id'] . ' - ' . $grade['course_name']) ?></td>
                                        <td><?= htmlspecialchars($grade['semester_id']) ?></td>
                                        <td><?= $grade['project_grade'] ?? '-' ?></td>
                                        <td><?= $grade['mid_grade'] ?? '-' ?></td>
                                        <td><?= $final ?: '-' ?></td>
                                        <td><strong><?= $total ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" style="text-align: center;">No grades recorded</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 20px; padding: 15px; background: #f7fafc; border-radius: 8px;">
                        <strong>GPA:</strong> <?= $gpa ?> / 4.00
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>

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
