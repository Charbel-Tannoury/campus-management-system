<?php
/**
 * Student Profile Page
 * 
 * Displays comprehensive student information including:
 * - Personal details (name, email, phone)
 * - Account status (email verification)
 * - Enrollment history (majors, faculties, years)
 * - Account creation and verification timestamps
 * 
 * Features:
 * - Session-based authentication
 * - XSS protection via htmlspecialchars helper function
 * - Multi-query data aggregation (student info + enrollment details)
 * - Prepared statements for SQL injection prevention
 * - Handles students with multiple major/faculty enrollments
 * 
 * Data Sources:
 * - students table: Personal information
 * - enrollment + to_enrol + major_course_semester: Enrollment history
 * - majors + university: Program and faculty names
 */

session_start();
require_once '../connection.php';
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Profile</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="layout">
  <?php include 'sidebar.php'; ?>
<?php $student = null;
$major_name = "N/A";
$faculty_id = "N/A";

if($student_id > 0){

  // ===== Student Info =====
  $sqlStudent = "SELECT student_id, first_name, last_name, email, phone_num, verification, verified_at, created_at
                 FROM students
                 WHERE student_id = ?
                 LIMIT 1";
  $stmt = mysqli_prepare($con, $sqlStudent);
  if($stmt === false) die("Prepare failed (student): ".mysqli_error($con));

  mysqli_stmt_bind_param($stmt, "i", $student_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $student = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  // ===== Major + Faculty =====
  $sqlMajor = "
    SELECT DISTINCT mj.major_name, te.faculty_id, te.year, u.faculty_name,u.faculty_number
    FROM enrollment e
    JOIN to_enrol te ON te.to_enrol_id = e.to_enrol_id
    JOIN major_course_semester mcs ON mcs.mcs_id = te.mcs_id
    JOIN majors mj ON mj.major_id = mcs.major_id
    JOIN university u ON u.faculty_id = te.faculty_id
    WHERE e.student_id = ?
    ORDER BY te.year ASC
  ";
  $stmt2 = mysqli_prepare($con, $sqlMajor);
  if($stmt2 === false) die("Prepare failed (major): ".mysqli_error($con));

  mysqli_stmt_bind_param($stmt2, "i", $student_id);
  mysqli_stmt_execute($stmt2);
  $res2 = mysqli_stmt_get_result($stmt2);
  $m = mysqli_fetch_all($res2, MYSQLI_ASSOC);
  mysqli_stmt_close($stmt2);
}

$first_name = $student['first_name'] ?? "Unknown";
$last_name  = $student['last_name'] ?? "Student";
$email      = $student['email'] ?? "N/A";
$phone      = $student['phone_num'] ?? "N/A";
$created_at = $student['created_at'] ?? "";
$verification = isset($student['verification']) ? (int)$student['verification'] : null;
$created_short = $created_at ? substr($created_at, 0, 16) : "N/A";
?>
  <main class="main">
    <header class="topbar">
      <div class="left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <span><?php echo e($email); ?></span>
      </div>
      <div class="right"><i class="fas fa-user"></i> Profile</div>
    </header>

    <section class="content">
      <div class="page-title">Student Information</div>

      <?php if($student_id <= 0): ?>
        <div class="warn">
          No <b>student_id</b> in session. After login do:
          <b>$_SESSION['id'] = $row['id'];</b>
        </div>
      <?php elseif(!$student): ?>
        <div class="warn">Student not found for ID: <b><?php echo e($student_id); ?></b></div>
      <?php endif; ?>

      <div class="profile-card">
        <div class="profile-img">
          <div style="font-weight:800;font-size:14px;">
            Student: <?php echo e($first_name.' '.$last_name); ?>
          </div>
          <div class="small">Student ID: <?php echo e($student_id ?: "N/A"); ?></div>
        </div>

        <div class="profile-info">
          <div class="field"><label>First Name</label><div class="val"><?php echo e($first_name); ?></div></div>
          <div class="field"><label>Last Name</label><div class="val"><?php echo e($last_name); ?></div></div>
          <div class="field"><label>Email</label><div class="val"><?php echo e($email); ?></div></div>
          <div class="field"><label>Phone</label><div class="val"><?php echo e($phone); ?></div></div>
          <div class="field"><label>Account Created</label><div class="val"><?php echo e($created_short); ?></div></div>
          <div class="field">
            <label>Verification</label>
            <div class="val">
              <?php
                if($verification === null) echo "N/A";
                else echo ($verification === 1 ? "Verified" : "Not Verified");
              ?>
            </div>
          </div>
          <div class="field"><label>Status</label><div class="val">Active</div></div>
        </div>
      </div>

    </section>
    <section class="content">
          <?php if($m){ echo '<div class="page-title">Enrollment Informations</div>'; 
          foreach($m as $value){?>
          <div class="profile-info">
          <div class="field"><label>Faculty ID</label><div class="val"><?php echo $value["faculty_id"]; ?></div></div>
          <div class="field"><label>Faculty ID</label><div class="val"><?php echo $value["faculty_name"]." - Branch".$value["faculty_number"]; ?></div></div>
          <div class="field"><label>Major</label><div class="val"><?php echo $value["major_name"]; ?></div></div>
          <div class="field"><label>Year</label><div class="val"><?php echo $value["year"]; ?></div></div>
          
        
      </div>
        
          <?php }

        }
        ?>
            
          </div>
        </div>
      
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
