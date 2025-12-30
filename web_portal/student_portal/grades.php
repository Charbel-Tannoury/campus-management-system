<?php
/**
 * Student Grades Display Page
 * 
 * This page shows a student's complete academic record including:
 * - All enrolled courses with individual component grades
 * - Project, midterm, and final exam grades
 * - Total grade calculation and pass/fail status
 * - Letter grade assignment
 * - Total earned credits calculation
 * - Course count and academic year grouping
 * 
 * Grading System:
 * - Passing grade: >= 60 points (out of 100)
 * - Components: Project + Midterm + First Final (or Second Final if retake)
 * - Earned credits only counted for passed courses
 * 
 * Visual Status Indicators:
 * - Green badge: Passed course
 * - Red badge: Failed course
 * - Yellow badge: Pending grades (not yet graded)
 */

session_start();
require_once '../connection.php';


function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}

/* If your connection variable is NOT $con, change it here */
if (!isset($con)) {
  die("DB connection variable \$con not found. Check connection-db.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Grades</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="layout">
  <?php include 'sidebar.php';
  
  // Complex query joining multiple tables to retrieve complete grade information
  // Joins:
  // - enrollment: Links student to course offerings
  // - grades: Contains all grade components
  // - to_enrol: Course offering details (year, faculty, professor)
  // - major_course_semester: Course metadata (credits, semester)
  // Results ordered by: year DESC, semester ASC, course_id ASC
  $sql = "
SELECT
  e.enrol_id,
  e.student_id,
  te.`year`,
  te.dr_id,
  te.faculty_id,
  mcs.course_id,
  mcs.major_id,
  mcs.semester_id,
  mcs.credits,

  g.project_grade,
  g.mid_grade,
  g.first_final,
  g.second_final

FROM enrollment e
JOIN grades g
  ON g.enrol_id = e.enrol_id

JOIN to_enrol te
  ON te.to_enrol_id = e.to_enrol_id

JOIN major_course_semester mcs
  ON mcs.mcs_id = te.mcs_id

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
$totalCreditsEarned = 0;  // Accumulator for passed courses only
$courseCount = 0;  // Total number of courses taken

while($r = mysqli_fetch_assoc($result)){
  // Parse individual grade components, defaulting null values to 0
  $proj = (float)($r['project_grade'] ?? 0);
  $mid  = (float)($r['mid_grade'] ?? 0);
  $ff   = (float)($r['first_final'] ?? 0);  // First final exam attempt
  $sf   = (float)($r['second_final'] ?? 0);  // Second final (retake)

  // Calculate total grade (max 100 points)
  $total = $proj + $mid + $ff + $sf;
  $r['total'] = $total;

  // Determine pass/fail status and visual indicators
  // Pending: No final grades entered yet
  if(($r['first_final'] === null && $r['second_final'] === null) || $total == 0){
    $r['status'] = 'Pending';
    $r['badge'] = 'b-pend';  // Yellow badge CSS class
    $r['gradeClass'] = 'pend';
    $r['letter'] = '--';  // No letter grade yet
  } else {
    // Course has been graded, check if passed (>= 60 points)
    if($total >= 60){
      $r['status'] = 'Passed';
      $r['badge'] = 'b-pass';
      $r['gradeClass'] = 'pass';
      $totalCreditsEarned += (int)$r['credits'];
    } else {
      $r['status'] = 'Failed';
      $r['badge'] = 'b-fail';
      $r['gradeClass'] = 'fail';
    }

    if($total >= 90) $r['letter'] = 'A';
    elseif($total >= 85) $r['letter'] = 'A-';
    elseif($total >= 80) $r['letter'] = 'B+';
    elseif($total >= 75) $r['letter'] = 'B';
    elseif($total >= 70) $r['letter'] = 'C+';
    elseif($total >= 65) $r['letter'] = 'C';
    elseif($total >= 60) $r['letter'] = 'D';
    else $r['letter'] = 'F';
  }

  $rows[] = $r;
  $courseCount++;
}

mysqli_stmt_close($stmt);

$currentSemester = isset($rows[0]) ? ($rows[0]['semester_id']." / ".$rows[0]['year']) : "N/A";
?>

  <main class="main">
    <header class="topbar">
      <div class="left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="brand-badge">U</div>
        <span>Student ID: <?php echo e($student_id); ?></span>
      </div>
      <div class="right"><i class="fas fa-chart-bar"></i> Grades Page</div>
    </header>

    <section class="content">
      <div class="page-head">
        <div class="page-title">My Grades</div>
        <div class="tools">
          <input class="input" type="text" placeholder="Search for a course..." onkeyup="searchTable(this.value)">
        </div>
      </div>

      <div class="stats">
        <div class="card"><div class="label">Courses</div><div class="value"><?php echo e($courseCount); ?></div></div>
        <div class="card"><div class="label">Earned Credit Hours</div><div class="value"><?php echo e($totalCreditsEarned); ?></div></div>
        <div class="card"><div class="label">Faculty ID</div><div class="value"><?php echo e($rows[0]['faculty_id'] ?? 'N/A'); ?></div></div>
        <div class="card"><div class="label">Current Semester</div><div class="value"><?php echo e($currentSemester); ?></div></div>
      </div>

      <div class="table-card">
        <table id="gradesTable">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Major</th>
              <th>Instructor ID</th>
              <th>Credits</th>
              <th>Project</th>
              <th>Mid</th>
              <th>Final 1</th>
              <th>Final 2</th>
              <th>Total</th>
              <th>Letter</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php if(count($rows) > 0): ?>
            <?php foreach($rows as $r): ?>
              <tr>
                <td><?php echo e($r['course_id']); ?></td>
                <td><?php echo e($r['major_id']); ?></td>
                <td><?php echo e($r['dr_id']); ?></td>
                <td><?php echo e($r['credits']); ?></td>
                <td><?php echo e($r['project_grade'] ?? '--'); ?></td>
                <td><?php echo e($r['mid_grade'] ?? '--'); ?></td>
                <td><?php echo e($r['first_final'] ?? '--'); ?></td>
                <td><?php echo e($r['second_final'] ?? '--'); ?></td>
                <td class="grade <?php echo e($r['gradeClass']); ?>">
                  <?php echo ($r['status'] === 'Pending') ? 'Pending' : e($r['total']); ?>
                </td>
                <td><?php echo e($r['letter']); ?></td>
                <td><span class="badge <?php echo e($r['badge']); ?>"><?php echo e($r['status']); ?></span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="11" style="color:#64748b;">No grades found for this student.</td></tr>
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
    document.querySelectorAll("#gradesTable tbody tr").forEach(r=>{
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
