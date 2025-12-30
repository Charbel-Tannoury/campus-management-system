<?php
/**
 * Student Course Materials Page
 * 
 * Displays all courses the student is enrolled in, organized by semester and year.
 * Note: Despite the filename "materials.php", this page shows course list, not course materials/files.
 * 
 * Features:
 * - Complete enrollment history display
 * - Course information (code, name, credits, semester)
 * - Major and year information for each course
 * - Total courses and credits calculation
 * - Organized by year (DESC) and semester (ASC)
 * - Session-based authentication
 * - Prepared statements for security
 * 
 * Data Display:
 * - Each enrolled course with its details
 * - Faculty and major associations
 * - Academic year and semester
 * - Credit hours per course
 * - Summary statistics at top
 * 
 * Query: Joins enrollment -> to_enrol -> major_course_semester -> majors
 */

session_start();
require_once '../connection.php';


function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

$student_id = $_SESSION['id'] ?? null;
if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}

// Complex query to retrieve all enrolled courses for the student
// Joins multiple tables to get complete course information:
// - enrollment: Base enrollment records
// - to_enrol: Course offering details (year, faculty, professor)
// - major_course_semester: Course metadata (credits, semester)
// - majors: Major program name
// Results ordered by year DESC (newest first), then semester ASC, then course_id
$sql = "
SELECT
  e.enrol_id,
  e.student_id,
  te.`year`,
  te.dr_id,
  te.faculty_id,

  mcs.course_id,
  mcs.credits,
  mcs.semester_id,
  mcs.major_id,

  mj.major_name

FROM enrollment e
JOIN to_enrol te
  ON te.to_enrol_id = e.to_enrol_id
JOIN major_course_semester mcs
  ON mcs.mcs_id = te.mcs_id
JOIN majors mj
  ON mj.major_id = mcs.major_id

WHERE e.student_id = ?
ORDER BY te.`year` DESC, mcs.semester_id ASC, mcs.course_id ASC
";

$stmt = mysqli_prepare($con, $sql);
if ($stmt === false) {
  die("Prepare failed: " . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
if ($result === false) {
  die("Get result failed: " . mysqli_error($con));
}

$rows = [];
$totalCourses = 0;
$totalCredits = 0;
$currentSemester = "N/A";

while($r = mysqli_fetch_assoc($result)){
  $rows[] = $r;
  $totalCourses++;
  $totalCredits += (int)$r['credits'];

  if($currentSemester === "N/A"){
    $currentSemester = $r['semester_id']." / ".$r['year'];
  }
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Courses</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="layout">
  <?php include 'sidebar.php'; ?>

  <main class="main">
    <header class="topbar">
      <div class="left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="brand-badge">U</div>
        <span>Student ID: <?php echo e($student_id); ?></span>
      </div>
      <div class="right"><i class="fas fa-book"></i> Courses Page</div>
    </header>

    <section class="content">
      <div class="page-head">
        <div class="page-title">Registered Courses</div>
        <div class="tools">
          <input class="input" type="text" placeholder="Search..." onkeyup="searchTable(this.value)">
        </div>
      </div>

      <div class="stats">
        <div class="card"><div class="label">Current Courses</div><div class="value"><?php echo e($totalCourses); ?></div></div>
        <div class="card"><div class="label">Registered Credit Hours</div><div class="value"><?php echo e($totalCredits); ?></div></div>
        <div class="card"><div class="label">Faculty ID</div><div class="value"><?php echo e($rows[0]['faculty_id'] ?? 'N/A'); ?></div></div>
        <div class="card"><div class="label">Current Semester</div><div class="value"><?php echo e($currentSemester); ?></div></div>
      </div>

      <div class="table-card">
        <table id="coursesTable">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Major</th>
              <th>Credits</th>
              <th>Semester</th>
              <th>Year</th>
              <th>Instructor ID</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
          <?php if(count($rows) > 0): ?>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?php echo e($r['course_id']); ?></td>
                <td><?php echo e($r['major_name']); ?></td>
                <td><?php echo e($r['credits']); ?></td>
                <td><?php echo e($r['semester_id']); ?></td>
                <td><?php echo e($r['year']); ?></td>
                <td><?php echo e($r['dr_id']); ?></td>
                <td><span class="badge b-active">Active</span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="color:#64748b;">No courses found for this student.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </section>
  </main>
</div>

<script>
  function searchTable(q){
    q = (q || "").toLowerCase();
    document.querySelectorAll("#coursesTable tbody tr").forEach(r=>{
      r.style.display = r.innerText.toLowerCase().includes(q) ? "" : "none";
    });
  }
</script>

<!-- Google Translate Script -->
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
