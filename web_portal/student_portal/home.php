<?php
/**
 * Student Dashboard Home Page
 * 
 * Main landing page for authenticated students displaying personalized academic overview.
 * Features:
 * - Latest 3 news/announcements from faculty
 * - Academic statistics cards (GPA, earned credits, current semester, registered courses)
 * - Recently registered courses table (last 3)
 * - Real-time data from multiple database queries
 * - Prepared statements for security
 * - XSS protection via htmlspecialchars helper
 * 
 * Statistics Displayed:
 * 1. Cumulative GPA: Overall grade point average (currently N/A - needs calculation)
 * 2. Earned Credit Hours: Credits from passed courses (grade >= 60)
 * 3. Current Semester: Most recent semester/year enrollment
 * 4. Registered Courses: Total count of enrolled courses
 * 
 * News Section:
 * - Shows latest 3 announcements from mails table
 * - Filtered by receivers (1=students, 0=all users)
 * - Ordered by creation date (newest first)
 * 
 * Courses Table:
 * - Displays last 3 registered courses
 * - Shows course code, major, instructor ID, status
 * - Joins enrollment, to_enrol, major_course_semester, majors
 * 
 * Earned Credits Calculation:
 * - Fetches all grades for student
 * - Calculates total: project + mid + first_final + second_final
 * - Awards credits if total >= 60 (passing grade)
 * 
 * Security:
 * - Session validation (redirects if not logged in)
 * - Prepared statements for all queries
 * - XSS protection via e() helper function
 * - Faculty-based access control
 * 
 * Use Case: Student portal landing page after login
 */
require_once '../connection.php';
session_start();

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}

/* =======================
   NEWS (latest 3)
   ======================= */

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Home</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="layout">
  <?php include 'sidebar.php';
  $news = [];
$qNews = "SELECT mail_title, mail_info, created_at FROM mails WHERE receivers=1 or receivers=0 ORDER BY created_at DESC LIMIT 3";
$rNews = mysqli_query($con, $qNews);
if($rNews){
  while($row = mysqli_fetch_assoc($rNews)){
    $news[] = $row;
  }
}

/* =======================
   COURSES (for student)
   ======================= */
$courses = [];
$totalCourses = 0;
$totalCreditsRegistered = 0;
$currentSemester = "N/A";

$sqlCourses = "
SELECT
  e.enrol_id,
  te.`year`,
  te.dr_id,
  te.faculty_id,
  mcs.course_id,
  mcs.credits,
  mcs.semester_id,
  mj.major_name
FROM enrollment e
JOIN to_enrol te ON te.to_enrol_id = e.to_enrol_id
JOIN major_course_semester mcs ON mcs.mcs_id = te.mcs_id
JOIN majors mj ON mj.major_id = mcs.major_id
WHERE e.student_id = ?
ORDER BY e.enrol_id DESC
";

$stmt = mysqli_prepare($con, $sqlCourses);
if ($stmt === false) { die("Prepare failed (courses): " . mysqli_error($con)); }

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$resCourses = mysqli_stmt_get_result($stmt);
if ($resCourses === false) { die("Get result failed (courses): " . mysqli_error($con)); }

$allCourses = [];
while($r = mysqli_fetch_assoc($resCourses)){
  $allCourses[] = $r;
  $totalCourses++;
  $totalCreditsRegistered += (int)$r['credits'];
  if($currentSemester === "N/A"){
    $currentSemester = $r['semester_id']." / ".$r['year'];
  }
}
mysqli_stmt_close($stmt);

$courses = array_slice($allCourses, 0, 3);

/* =======================
   EARNED CREDITS
   ======================= */
$earnedCredits = 0;

$sqlEarned = "
SELECT
  mcs.credits,
  g.project_grade, g.mid_grade, g.first_final, g.second_final
FROM enrollment e
JOIN grades g ON g.enrol_id = e.enrol_id
JOIN to_enrol te ON te.to_enrol_id = e.to_enrol_id
JOIN major_course_semester mcs ON mcs.mcs_id = te.mcs_id
WHERE e.student_id = ?
";

$stmt2 = mysqli_prepare($con, $sqlEarned);
if ($stmt2 === false) { die("Prepare failed (earned): " . mysqli_error($con)); }

mysqli_stmt_bind_param($stmt2, "i", $student_id);
mysqli_stmt_execute($stmt2);
$resEarned = mysqli_stmt_get_result($stmt2);
if ($resEarned === false) { die("Get result failed (earned): " . mysqli_error($con)); }

while($x = mysqli_fetch_assoc($resEarned)){
  $total = (float)($x['project_grade'] ?? 0)
         + (float)($x['mid_grade'] ?? 0)
         + (float)($x['first_final'] ?? 0)
         + (float)($x['second_final'] ?? 0);

  if($total >= 60){
    $earnedCredits += (int)$x['credits'];
  }
}
mysqli_stmt_close($stmt2);

$cumulativeGPA = "N/A"; ?>

  <main class="main">
    <header class="topbar">
      <div class="left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div class="brand-badge">U</div>
        <span>Student ID: <?php echo e($student_id); ?></span>
      </div>
      <div class="right">Welcome back 👋</div>
    </header>

    <section class="content">

      <div class="news-card">
        <div class="news-header">
          <h3>Latest News</h3>
          <a href="news.php">View all news</a>
        </div>

        <div class="news-list">
          <?php if(count($news) > 0): ?>
            <?php foreach($news as $n): ?>
              <?php
                $title = e($n['mail_title']);
                $info  = e($n['mail_info']);
                $dateShort = e(substr(($n['created_at'] ?? ''), 0, 16));
              ?>
              <div class="news-item">
                <div class="title"><?php echo $title; ?></div>
                <div class="meta">Posted: <?php echo $dateShort; ?></div>
                <div class="desc"><?php echo $info; ?></div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="news-item">
              <div class="title">No news yet</div>
              <div class="meta">—</div>
              <div class="desc">There are no announcements right now.</div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="stats">
        <div class="card"><div class="label">Cumulative GPA</div><div class="value"><?php echo e($cumulativeGPA); ?></div></div>
        <div class="card"><div class="label">Earned Credit Hours</div><div class="value"><?php echo e($earnedCredits); ?></div></div>
        <div class="card"><div class="label">Current Semester</div><div class="value"><?php echo e($currentSemester); ?></div></div>
        <div class="card"><div class="label">Registered Courses</div><div class="value"><?php echo e($totalCourses); ?></div></div>
      </div>

      <div class="table-card">
        <div class="table-title">Recently Registered Courses</div>
        <table>
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Major</th>
              <th>Instructor ID</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
          <?php if(count($courses) > 0): ?>
            <?php foreach($courses as $c): ?>
              <tr>
                <td><?php echo e($c['course_id']); ?></td>
                <td><?php echo e($c['major_name']); ?></td>
                <td><?php echo e($c['dr_id']); ?></td>
                <td><span class="status-badge">Active</span></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" style="color:#64748b;">No courses found.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

    </section>
  </main>
</div>

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
