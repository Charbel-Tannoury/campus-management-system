<?php
/**
 * Student Course Enrollment Page
 * 
 * This is the main enrollment interface where students can:
 * - View approved enrollment applications (status=1 from paid_students)
 * - Browse available courses by faculty, major, and semester
 * - Enroll in courses for the current academic year
 * - View current pending and approved applications
 * - Check prerequisite requirements (must pass previous semester courses)
 * 
 * Enrollment Process:
 * 1. Student must have verified email (verification=1)
 * 2. Student submits application via paid_students table (status=0)
 * 3. Admin approves payment (status=1)
 * 4. Student can then enroll in courses via this page
 * 
 * AJAX Features:
 * - Dynamic major loading based on selected faculty
 * - Returns JSON data for client-side dropdown population
 * 
 * Validation:
 * - Prevents duplicate enrollments in same course
 * - Checks semester prerequisites (must pass previous semester)
 * - Verifies course availability for selected faculty/major
 */

session_start();
require_once '../connection.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
$current_year = date('Y');
// AJAX handler for getting majors by faculty
// This endpoint returns JSON data for dynamic major dropdown population
// Query parameters: ajax=get_majors, faculty_id=<int>
if(isset($_GET['ajax']) && $_GET['ajax'] === 'get_majors' && isset($_GET['faculty_id'])){
  $faculty_id = (int)$_GET['faculty_id'];
  $majors = [];
  
  // Get distinct majors that have courses available in to_enrol for this faculty
  // Filters by current year to show only active course offerings
  // JOIN chain: majors -> major_course_semester -> to_enrol (filtered by faculty + year)
  $qMajors = "SELECT DISTINCT m.major_id, m.major_name 
              FROM majors m 
              JOIN major_course_semester mcs ON m.major_id = mcs.major_id 
              JOIN to_enrol te ON mcs.mcs_id = te.mcs_id 
              WHERE te.faculty_id = ? AND te.year = ?
              ORDER BY m.major_name ASC";
  $stMajors = mysqli_prepare($con, $qMajors);
  mysqli_stmt_bind_param($stMajors, "ii", $faculty_id, $current_year);
  mysqli_stmt_execute($stMajors);
  $rMajors = mysqli_stmt_get_result($stMajors);
  while($row = mysqli_fetch_assoc($rMajors)){
    $majors[] = $row;
  }
  mysqli_stmt_close($stMajors);
  
  header('Content-Type: application/json');
  echo json_encode($majors);
  exit();
}

if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}
$student_id = $_SESSION['id'];

// Get student info from database
// Retrieves student_id from session and loads full student record
$qStudent = "SELECT s.*, s.verification FROM students s WHERE s.student_id = ?";
$stStudent = mysqli_prepare($con, $qStudent);
mysqli_stmt_bind_param($stStudent, "i", $student_id);
mysqli_stmt_execute($stStudent);
$studentInfo = mysqli_fetch_assoc(mysqli_stmt_get_result($stStudent));
mysqli_stmt_close($stStudent);

$studentEmail = $studentInfo['email'];
$isVerified = ($studentInfo['verification'] == 1);  // Email verification status (1=verified, 0=not verified)
$current_year = date('Y');

$successMsg = '';
$errorMsg = '';

// Get all faculties
$faculties = [];
$qFac = "SELECT faculty_id, faculty_name, faculty_number FROM university ORDER BY faculty_name ASC";
$rFac = mysqli_query($con, $qFac);
while($f = mysqli_fetch_assoc($rFac)){
  $faculties[] = $f;
}

// Check if student has approved paid_students entries (status=1) for current year
// These are payment approvals that allow the student to enroll in courses
// Status: 0=pending admin approval, 1=approved and ready to enroll
$approved_enrollments = [];
$qApproved = "SELECT ps.*, u.faculty_name, u.faculty_number, m.major_name 
              FROM paid_students ps 
              JOIN university u ON ps.faculty_id = u.faculty_id 
              JOIN majors m ON ps.major_id = m.major_id 
              WHERE ps.student_id = ? AND ps.status = 1 AND ps.year = ?";
$stApproved = mysqli_prepare($con, $qApproved);
mysqli_stmt_bind_param($stApproved, "ii", $student_id, $current_year);
mysqli_stmt_execute($stApproved);
$rApproved = mysqli_stmt_get_result($stApproved);
while($row = mysqli_fetch_assoc($rApproved)){
  $approved_enrollments[] = $row;
}
mysqli_stmt_close($stApproved);

// Check if student already applied this year (for SAME faculty+major)
// Student can apply to different faculties/majors
$current_applications = [];
$qApplied = "SELECT ps.*, u.faculty_name, u.faculty_number, m.major_name 
             FROM paid_students ps 
             JOIN university u ON ps.faculty_id = u.faculty_id 
             JOIN majors m ON ps.major_id = m.major_id 
             WHERE ps.student_id = ? AND ps.year = ?";
$stApplied = mysqli_prepare($con, $qApplied);
mysqli_stmt_bind_param($stApplied, "ii", $student_id, $current_year);
mysqli_stmt_execute($stApplied);
$rApplied = mysqli_stmt_get_result($stApplied);
while($app = mysqli_fetch_assoc($rApplied)){
  $current_applications[] = $app;
}
mysqli_stmt_close($stApplied);

// Function to check if student passed all courses they enrolled in from a semester
// This is used to enforce prerequisite requirements (must pass semester N before enrolling in N+1)
// @param $con Database connection
// @param $student_id Student ID to check
// @param $semester_id Semester to verify (e.g., 1, 2, 3)
// @param $major_id Major program ID
// @param $faculty_id Faculty ID
// @return bool True if all enrolled courses were passed (grade >= 60), false otherwise
function hasPassedSemester($con, $student_id, $semester_id, $major_id, $faculty_id) {
    // Get courses the student actually enrolled in for this semester
    // Only checks courses the student took, not all courses in the curriculum
    $qEnrolled = "SELECT e.enrol_id, g.project_grade, g.mid_grade, g.first_final, g.second_final
                  FROM enrollment e
                  JOIN to_enrol te ON e.to_enrol_id = te.to_enrol_id
                  JOIN major_course_semester mcs ON te.mcs_id = mcs.mcs_id
                  LEFT JOIN grades g ON e.enrol_id = g.enrol_id
                  WHERE e.student_id = ? AND mcs.major_id = ? AND mcs.semester_id = ? AND te.faculty_id = ?";
    $st = mysqli_prepare($con, $qEnrolled);
    mysqli_stmt_bind_param($st, "issi", $student_id, $major_id, $semester_id, $faculty_id);
    mysqli_stmt_execute($st);
    $result = mysqli_stmt_get_result($st);
    
    $hasEnrolledCourses = false;
    while($row = mysqli_fetch_assoc($result)){
        $hasEnrolledCourses = true;
        $project = $row['project_grade'] ?? 0;
        $mid = $row['mid_grade'] ?? 0;
        $final = ($row['second_final'] !== null) ? $row['second_final'] : ($row['first_final'] ?? 0);
        $total = $project + $mid + $final;
        if($total < 50){
            mysqli_stmt_close($st);
            return false; // Found a course not passed yet
        }
    }
    mysqli_stmt_close($st);
    
    // If student never enrolled in any courses from this semester, they can't progress yet
    // (they need to complete the semester first)
    // If they enrolled and passed all, return true
    return $hasEnrolledCourses;
}

// Function to get available semesters for enrollment
function getAvailableSemesters($con, $student_id, $major_id, $faculty_id) {
    $available = [];
    
    // Odd semesters group: 1, 3, 5
    $odd_available = ['semester1']; // Always can take semester1
    if(hasPassedSemester($con, $student_id, 'semester1', $major_id, $faculty_id)){
        $odd_available[] = 'semester3';
    }
    if(hasPassedSemester($con, $student_id, 'semester3', $major_id, $faculty_id)){
        $odd_available[] = 'semester5';
    }
    
    // Even semesters group: 2, 4, 6
    $even_available = ['semester2']; // Always can take semester2
    if(hasPassedSemester($con, $student_id, 'semester2', $major_id, $faculty_id)){
        $even_available[] = 'semester4';
    }
    if(hasPassedSemester($con, $student_id, 'semester4', $major_id, $faculty_id)){
        $even_available[] = 'semester6';
    }
    
    return ['odd' => $odd_available, 'even' => $even_available];
}

// Function to get current credits enrolled for a semester group
function getCurrentCredits($con, $student_id, $semesters, $current_year) {
    if(empty($semesters)) return 0;
    
    $placeholders = implode(',', array_fill(0, count($semesters), '?'));
    $qCredits = "SELECT SUM(mcs.credits) as total_credits 
                 FROM enrollment e 
                 JOIN to_enrol te ON e.to_enrol_id = te.to_enrol_id 
                 JOIN major_course_semester mcs ON te.mcs_id = mcs.mcs_id 
                 WHERE e.student_id = ? AND te.year = ? AND mcs.semester_id IN ($placeholders)";
    
    $st = mysqli_prepare($con, $qCredits);
    $types = "ii" . str_repeat("s", count($semesters));
    $params = array_merge([$student_id, $current_year], $semesters);
    mysqli_stmt_bind_param($st, $types, ...$params);
    mysqli_stmt_execute($st);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    mysqli_stmt_close($st);
    
    return (int)($result['total_credits'] ?? 0);
}

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $action = $_POST['action'] ?? '';

  // Part 1: Apply to Major
  if($action === 'apply_major'){
    if(!$isVerified){
      $errorMsg = "Your account must be verified to apply for a major.";
    } else {
      $faculty_id = (int)($_POST['faculty_id'] ?? 0);
      $major_id = trim($_POST['major_id'] ?? '');
      
      if($faculty_id <= 0 || $major_id === ''){
        $errorMsg = "Please select both faculty and major.";
      } else {
        // Check if already applied to this faculty (only one application per faculty per year)
        $already_applied_here = false;
        foreach($current_applications as $app){
          if($app['faculty_id'] == $faculty_id || $app['major_id'] == $major_id){
            $already_applied_here = true;
            break;
          }
        }
        
        if($already_applied_here){
          $errorMsg = "You have already applied to this faculty this year. You can only apply once per faculty per year.";
        } else {
          // Insert into paid_students
          $qInsert = "INSERT INTO paid_students (student_id, faculty_id, major_id, year, status) VALUES (?, ?, ?, ?, 0)";
          $stInsert = mysqli_prepare($con, $qInsert);
          mysqli_stmt_bind_param($stInsert, "iisi", $student_id, $faculty_id, $major_id, $current_year);
          
          if(mysqli_stmt_execute($stInsert)){
            $successMsg = "Your application has been submitted successfully! Please wait for approval.";
            // Get faculty and major names for display
            $qFacName = "SELECT faculty_name, faculty_number FROM university WHERE faculty_id = ?";
            $stFacName = mysqli_prepare($con, $qFacName);
            mysqli_stmt_bind_param($stFacName, "i", $faculty_id);
            mysqli_stmt_execute($stFacName);
            $facData = mysqli_fetch_assoc(mysqli_stmt_get_result($stFacName));
            mysqli_stmt_close($stFacName);
            
            $qMajName = "SELECT major_name FROM majors WHERE major_id = ?";
            $stMajName = mysqli_prepare($con, $qMajName);
            mysqli_stmt_bind_param($stMajName, "s", $major_id);
            mysqli_stmt_execute($stMajName);
            $majData = mysqli_fetch_assoc(mysqli_stmt_get_result($stMajName));
            mysqli_stmt_close($stMajName);
            
            // Refresh applications list with full data
            $current_applications[] = [
              'faculty_id' => $faculty_id, 
              'major_id' => $major_id, 
              'status' => 0,
              'faculty_name' => $facData['faculty_name'] ?? '',
              'faculty_number' => $facData['faculty_number'] ?? '',
              'major_name' => $majData['major_name'] ?? ''
            ];
          } else {
            $errorMsg = "Failed to submit application: " . mysqli_error($con);
          }
          mysqli_stmt_close($stInsert);
        }
      }
    }
  }

  // Part 2: Enroll in Courses
  if($action === 'enroll_courses'){
    if(empty($approved_enrollments)){
      $errorMsg = "You must have an approved enrollment to register for courses.";
    } else {
      $selected_courses = $_POST['courses'] ?? [];
      $selected_faculty = (int)($_POST['selected_faculty'] ?? 0);
      
      // Find the matching approved enrollment
      $current_enrollment = null;
      foreach($approved_enrollments as $ae){
        if($ae['faculty_id'] == $selected_faculty){
          $current_enrollment = $ae;
          break;
        }
      }
      
      if(!$current_enrollment){
        $errorMsg = "Invalid faculty selection.";
      } elseif(empty($selected_courses)){
        $errorMsg = "Please select at least one course.";
      } else {
        $faculty_id = $current_enrollment['faculty_id'];
        $major_id = $current_enrollment['major_id'];
        
        $availableSemesters = getAvailableSemesters($con, $student_id, $major_id, $faculty_id);
        $oddCredits = getCurrentCredits($con, $student_id, $availableSemesters['odd'], $current_year);
        $evenCredits = getCurrentCredits($con, $student_id, $availableSemesters['even'], $current_year);
        
        $newOddCredits = 0;
        $newEvenCredits = 0;
        $coursesToEnroll = [];
        
        // Validate each selected course
        foreach($selected_courses as $to_enrol_id){
          $to_enrol_id = (int)$to_enrol_id;
          
          // Get course info (no year filter)
          $qCourse = "SELECT te.*, mcs.semester_id, mcs.credits, mcs.major_id 
                      FROM to_enrol te 
                      JOIN major_course_semester mcs ON te.mcs_id = mcs.mcs_id 
                      WHERE te.to_enrol_id = ? AND te.faculty_id = ? AND mcs.major_id = ?";
          $stCourse = mysqli_prepare($con, $qCourse);
          mysqli_stmt_bind_param($stCourse, "iis", $to_enrol_id, $faculty_id, $major_id);
          mysqli_stmt_execute($stCourse);
          $course = mysqli_fetch_assoc(mysqli_stmt_get_result($stCourse));
          mysqli_stmt_close($stCourse);
          
          if(!$course){
            continue; // Invalid course
          }
          
          // Check if already enrolled
          $qCheck = "SELECT enrol_id FROM enrollment WHERE student_id = ? AND to_enrol_id = ?";
          $stCheck = mysqli_prepare($con, $qCheck);
          mysqli_stmt_bind_param($stCheck, "ii", $student_id, $to_enrol_id);
          mysqli_stmt_execute($stCheck);
          if(mysqli_num_rows(mysqli_stmt_get_result($stCheck)) > 0){
            mysqli_stmt_close($stCheck);
            continue; // Already enrolled
          }
          mysqli_stmt_close($stCheck);
          
          // Check semester availability
          $semester = $course['semester_id'];
          $credits = (int)$course['credits'];
          $isOdd = in_array($semester, ['semester1', 'semester3', 'semester5']);
          
          if($isOdd){
            if(!in_array($semester, $availableSemesters['odd'])){
              continue; // Semester not available
            }
            $newOddCredits += $credits;
          } else {
            if(!in_array($semester, $availableSemesters['even'])){
              continue; // Semester not available
            }
            $newEvenCredits += $credits;
          }
          
          $coursesToEnroll[] = ['to_enrol_id' => $to_enrol_id, 'credits' => $credits, 'isOdd' => $isOdd];
        }
        
        // Check credit limits
        if(($oddCredits + $newOddCredits) > 30){
          $errorMsg = "Odd semester credits would exceed 30. Current: $oddCredits, Selected: $newOddCredits";
        } elseif(($evenCredits + $newEvenCredits) > 30){
          $errorMsg = "Even semester credits would exceed 30. Current: $evenCredits, Selected: $newEvenCredits";
        } elseif(empty($coursesToEnroll)){
          $errorMsg = "No valid courses selected for enrollment.";
        } else {
          // Enroll in courses
          $enrolled_count = 0;
          foreach($coursesToEnroll as $course){
            // Insert into enrollment
            $qEnroll = "INSERT INTO enrollment (student_id, to_enrol_id, active) VALUES (?, ?, 1)";
            $stEnroll = mysqli_prepare($con, $qEnroll);
            mysqli_stmt_bind_param($stEnroll, "ii", $student_id, $course['to_enrol_id']);
            
            if(mysqli_stmt_execute($stEnroll)){
              $enrol_id = mysqli_insert_id($con);
              
              // Insert into grades with NULL values
              $qGrade = "INSERT INTO grades (enrol_id) VALUES (?)";
              $stGrade = mysqli_prepare($con, $qGrade);
              mysqli_stmt_bind_param($stGrade, "i", $enrol_id);
              mysqli_stmt_execute($stGrade);
              mysqli_stmt_close($stGrade);
              
              $enrolled_count++;
            }
            mysqli_stmt_close($stEnroll);
          }
          
          if($enrolled_count > 0){
            $successMsg = "Successfully enrolled in $enrolled_count course(s)!";
          } else {
            $errorMsg = "Failed to enroll in any courses.";
          }
        }
      }
    }
  }
}

// Get available courses for enrollment for ALL approved enrollments
$courses_by_enrollment = [];

foreach($approved_enrollments as $enrollment){
  $faculty_id = $enrollment['faculty_id'];
  $major_id = $enrollment['major_id'];
  
  $availableSemesters = getAvailableSemesters($con, $student_id, $major_id, $faculty_id);
  $allAvailable = array_merge($availableSemesters['odd'], $availableSemesters['even']);
  
  $oddCreditsUsed = getCurrentCredits($con, $student_id, $availableSemesters['odd'], $current_year);
  $evenCreditsUsed = getCurrentCredits($con, $student_id, $availableSemesters['even'], $current_year);
  
  $available_courses = [];
  $enrolled_courses = [];
  
  if(!empty($allAvailable)){
    $placeholders = implode(',', array_fill(0, count($allAvailable), '?'));
    
    // Get available courses (no year filter - show all available courses for this faculty/major)
    $qCourses = "SELECT te.to_enrol_id, c.course_id, c.course_name, mcs.semester_id, mcs.credits,
                        d.first_name as dr_fname, d.last_name as dr_lname, te.year
                 FROM to_enrol te 
                 JOIN major_course_semester mcs ON te.mcs_id = mcs.mcs_id 
                 JOIN courses c ON mcs.course_id = c.course_id
                 JOIN doctors d ON te.dr_id = d.dr_id
                 WHERE te.faculty_id = ? AND mcs.major_id = ? 
                 AND mcs.semester_id IN ($placeholders)
                 ORDER BY mcs.semester_id, c.course_name";
    
    $stCourses = mysqli_prepare($con, $qCourses);
    $types = "is" . str_repeat("s", count($allAvailable));
    $params = array_merge([$faculty_id, $major_id], $allAvailable);
    mysqli_stmt_bind_param($stCourses, $types, ...$params);
    mysqli_stmt_execute($stCourses);
    $rCourses = mysqli_stmt_get_result($stCourses);
    
    while($course = mysqli_fetch_assoc($rCourses)){
      // Check if already enrolled
      $qCheck = "SELECT enrol_id FROM enrollment WHERE student_id = ? AND to_enrol_id = ?";
      $stCheck = mysqli_prepare($con, $qCheck);
      mysqli_stmt_bind_param($stCheck, "ii", $student_id, $course['to_enrol_id']);
      mysqli_stmt_execute($stCheck);
      $isEnrolled = mysqli_num_rows(mysqli_stmt_get_result($stCheck)) > 0;
      mysqli_stmt_close($stCheck);
      
      $course['enrolled'] = $isEnrolled;
      $course['isOdd'] = in_array($course['semester_id'], ['semester1', 'semester3', 'semester5']);
      
      if($isEnrolled){
        $enrolled_courses[] = $course;
      } else {
        $available_courses[] = $course;
      }
    }
    mysqli_stmt_close($stCourses);
  }
  
  $courses_by_enrollment[] = [
    'enrollment' => $enrollment,
    'available' => $available_courses,
    'enrolled' => $enrolled_courses,
    'oddCredits' => $oddCreditsUsed,
    'evenCredits' => $evenCreditsUsed
  ];
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Enrollment</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .enrollment-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .enrollment-card h4 {
      margin: 0 0 15px;
      color: #333;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-not-verified { background: #f8d7da; color: #721c24; }
    .course-list {
      display: grid;
      gap: 10px;
    }
    .course-item {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      background: #f8fafc;
      border-radius: 8px;
      border: 2px solid transparent;
      transition: all 0.2s;
    }
    .course-item:hover {
      border-color: #667eea;
    }
    .course-item.enrolled {
      background: #e8f5e9;
      border-color: #4caf50;
    }
    .course-item input[type="checkbox"] {
      margin-right: 12px;
      width: 18px;
      height: 18px;
    }
    .course-info {
      flex: 1;
    }
    .course-info .name {
      font-weight: 600;
      color: #333;
    }
    .course-info .details {
      font-size: 12px;
      color: #666;
      margin-top: 4px;
    }
    .course-credits {
      background: #667eea;
      color: white;
      padding: 4px 10px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 600;
    }
    .credits-summary {
      display: flex;
      gap: 20px;
      padding: 15px;
      background: #e3f2fd;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .credits-box {
      flex: 1;
      text-align: center;
    }
    .credits-box .label {
      font-size: 12px;
      color: #666;
    }
    .credits-box .value {
      font-size: 24px;
      font-weight: 700;
      color: #1976d2;
    }
    .semester-group {
      margin-bottom: 20px;
    }
    .semester-group h5 {
      margin: 0 0 10px;
      padding: 10px 15px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 8px;
    }
    .info-alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }
    .info-alert.warning {
      background: #fff3cd;
      border-left: 4px solid #ffc107;
      color: #856404;
    }
    .info-alert.info {
      background: #e3f2fd;
      border-left: 4px solid #2196f3;
      color: #0d47a1;
    }
    .info-alert.success {
      background: #d4edda;
      border-left: 4px solid #28a745;
      color: #155724;
    }
  </style>
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
        <span><?php echo e($studentEmail); ?></span>
      </div>
      <div class="right"><i class="fas fa-graduation-cap"></i> Enrollment</div>
    </header>

    <section class="content">

      <div class="page-title">Major Application & Course Enrollment</div>

      <?php if($errorMsg): ?><div class="error-box"><?php echo e($errorMsg); ?></div><?php endif; ?>
      <?php if($successMsg): ?><div class="success-box"><?php echo e($successMsg); ?></div><?php endif; ?>

      <div class="tabs">
        <button class="tab-btn active" onclick="showTab(1)" type="button">Part 1: Apply to Major</button>
        <button class="tab-btn" onclick="showTab(2)" type="button">Part 2: Course Enrollment</button>
      </div>

      <!-- TAB 1: Apply to Major -->
      <div id="tab1" class="section active">
        <div class="enrollment-card">
          <h4><i class="fas fa-university"></i> Apply to a Major</h4>
          
          <?php if(!$isVerified): ?>
            <div class="info-alert warning">
              <i class="fas fa-exclamation-triangle"></i>
              <div>
                <strong>Account Not Verified</strong><br>
                Your account must be verified before you can apply to a major. Please contact administration.
              </div>
            </div>
          <?php else: ?>
            <?php if(!empty($current_applications)): ?>
              <div style="margin-bottom:20px;">
                <h5 style="margin:0 0 10px; color:#333;">Your Applications for <?php echo $current_year; ?>:</h5>
                <?php foreach($current_applications as $app): ?>
                  <div class="info-alert <?php echo ($app['status'] == 1) ? 'success' : 'info'; ?>" style="margin-bottom:10px;">
                    <i class="fas fa-<?php echo ($app['status'] == 1) ? 'check-circle' : 'clock'; ?>"></i>
                    <div>
                      <strong><?php echo e($app['faculty_name'] . ' ' . $app['faculty_number']); ?></strong> - <?php echo e($app['major_name']); ?><br>
                      Status: <span class="status-badge <?php echo ($app['status'] == 1) ? 'status-approved' : 'status-pending'; ?>">
                        <?php echo ($app['status'] == 1) ? 'Approved' : 'Pending Approval'; ?>
                      </span>
                      <?php if($app['status'] == 1): ?>
                        <span style="color:#28a745; font-size:12px;"> - You can enroll in courses for this major in Part 2</span>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">
              <h5 style="margin:0 0 15px; color:#333;">Apply to Another Faculty/Major:</h5>
            <?php endif; ?>
            
            <form method="POST">
              <input type="hidden" name="action" value="apply_major">
              
              <div class="form-grid">
                <div class="field">
                  <label>Select Faculty <span style="color:#f56565;">*</span></label>
                  <select class="select" name="faculty_id" id="faculty_select" onchange="loadMajors(this.value)" required>
                    <option value="">-- Select Faculty --</option>
                    <?php foreach($faculties as $f): ?>
                      <option value="<?php echo $f['faculty_id']; ?>">
                        <?php echo e($f['faculty_name'] . ' - Branch ' . $f['faculty_number']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="field">
                  <label>Select Major <span style="color:#f56565;">*</span></label>
                  <select class="select" name="major_id" id="major_select" required>
                    <option value="">-- Select Faculty First --</option>
                  </select>
                </div>
              </div>

              <div class="info-alert info" style="margin-top:15px;">
                <i class="fas fa-info-circle"></i>
                <div>
                  <strong>Important:</strong> You can only apply to one major at one faculty per year. 
                  Your application will be reviewed by the administration.
                </div>
              </div>

              <div style="margin-top:15px;">
                <button class="btn btn-success" type="submit">
                  <i class="fas fa-paper-plane"></i> Submit Application
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- TAB 2: Course Enrollment -->
      <div id="tab2" class="section">
        <div class="enrollment-card">
          <h4><i class="fas fa-book"></i> Course Enrollment</h4>
          
          <?php if(empty($approved_enrollments)): ?>
            <div class="info-alert warning">
              <i class="fas fa-exclamation-triangle"></i>
              <div>
                <strong>Not Eligible for Course Enrollment</strong><br>
                You must have an approved major application to enroll in courses. 
                <?php if(empty($current_applications)): ?>
                  Please apply for a major first in Part 1.
                <?php else: ?>
                  Your application is pending approval.
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            
            <?php if(count($courses_by_enrollment) > 1): ?>
              <!-- Major selector tabs when multiple approved majors -->
              <div class="major-tabs" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
                <?php foreach($courses_by_enrollment as $idx => $data): ?>
                  <button type="button" class="major-tab-btn <?php echo ($idx === 0) ? 'active' : ''; ?>" 
                          onclick="showMajorTab(<?php echo $idx; ?>)"
                          style="padding:10px 15px; border:2px solid #667eea; border-radius:8px; background:<?php echo ($idx === 0) ? '#667eea' : 'white'; ?>; color:<?php echo ($idx === 0) ? 'white' : '#667eea'; ?>; cursor:pointer; font-weight:600;">
                    <?php echo e($data['enrollment']['major_name']); ?><br>
                    <small style="font-weight:400;"><?php echo e($data['enrollment']['faculty_name'] . ' ' . $data['enrollment']['faculty_number']); ?></small>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            
            <?php foreach($courses_by_enrollment as $idx => $data): 
              $enrollment = $data['enrollment'];
              $available_courses = $data['available'];
              $enrolled_courses = $data['enrolled'];
              $oddCreditsUsed = $data['oddCredits'];
              $evenCreditsUsed = $data['evenCredits'];
            ?>
              <div class="major-content" id="major-<?php echo $idx; ?>" style="<?php echo ($idx !== 0) ? 'display:none;' : ''; ?>">
                <div class="info-alert success">
                  <i class="fas fa-check-circle"></i>
                  <div>
                    <strong>Major:</strong> <?php echo e($enrollment['major_name']); ?><br>
                    <strong>Faculty:</strong> <?php echo e($enrollment['faculty_name'] . ' ' . $enrollment['faculty_number']); ?><br>
                    <strong>Year:</strong> <?php echo $current_year; ?>
                  </div>
                </div>

                <div class="credits-summary">
                  <div class="credits-box">
                    <div class="label">Odd Semesters (1,3,5)</div>
                    <div class="value"><?php echo $oddCreditsUsed; ?>/30</div>
                  </div>
                  <div class="credits-box">
                    <div class="label">Even Semesters (2,4,6)</div>
                    <div class="value"><?php echo $evenCreditsUsed; ?>/30</div>
                  </div>
                </div>

                <?php if(!empty($enrolled_courses)): ?>
                  <div class="semester-group">
                    <h5><i class="fas fa-check"></i> Currently Enrolled Courses</h5>
                    <div class="course-list">
                      <?php foreach($enrolled_courses as $course): ?>
                        <div class="course-item enrolled">
                          <i class="fas fa-check-circle" style="color:#4caf50; margin-right:12px;"></i>
                          <div class="course-info">
                            <div class="name"><?php echo e($course['course_id'] . ' - ' . $course['course_name']); ?></div>
                            <div class="details">
                              <?php echo e($course['semester_id']); ?> | 
                              Dr. <?php echo e($course['dr_fname'] . ' ' . $course['dr_lname']); ?>
                            </div>
                          </div>
                          <span class="course-credits"><?php echo $course['credits']; ?> cr</span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if(!empty($available_courses)): ?>
                  <form method="POST">
                    <input type="hidden" name="action" value="enroll_courses">
                    <input type="hidden" name="selected_faculty" value="<?php echo $enrollment['faculty_id']; ?>">
                    
                    <div class="semester-group">
                      <h5><i class="fas fa-plus-circle"></i> Available Courses</h5>
                      <div class="course-list">
                        <?php foreach($available_courses as $course): ?>
                          <label class="course-item">
                            <input type="checkbox" name="courses[]" value="<?php echo $course['to_enrol_id']; ?>"
                                   data-credits="<?php echo $course['credits']; ?>"
                                   data-odd="<?php echo $course['isOdd'] ? '1' : '0'; ?>">
                            <div class="course-info">
                              <div class="name"><?php echo e($course['course_id'] . ' - ' . $course['course_name']); ?></div>
                              <div class="details">
                                <?php echo e($course['semester_id']); ?> | 
                                Dr. <?php echo e($course['dr_fname'] . ' ' . $course['dr_lname']); ?>
                              </div>
                            </div>
                            <span class="course-credits"><?php echo $course['credits']; ?> cr</span>
                          </label>
                        <?php endforeach; ?>
                      </div>
                    </div>

                    <div style="margin-top:15px;">
                      <button class="btn btn-success" type="submit">
                        <i class="fas fa-save"></i> Enroll in Selected Courses
                      </button>
                    </div>
                  </form>
                <?php elseif(empty($enrolled_courses)): ?>
                  <div class="info-alert info">
                    <i class="fas fa-info-circle"></i>
                    <div>No courses available for enrollment at this time for this major.</div>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
            
          <?php endif; ?>
        </div>
      </div>

    </section>
  </main>
</div>

<script>
  function showTab(n){
    document.querySelectorAll(".tab-btn").forEach((b,i)=> b.classList.toggle("active", i===n-1));
    document.querySelectorAll(".section").forEach((s,i)=> s.classList.toggle("active", i===n-1));
  }
  
  function showMajorTab(idx){
    document.querySelectorAll(".major-tab-btn").forEach((b,i)=> {
      b.classList.toggle("active", i===idx);
      b.style.background = (i===idx) ? '#667eea' : 'white';
      b.style.color = (i===idx) ? 'white' : '#667eea';
    });
    document.querySelectorAll(".major-content").forEach((c,i)=> {
      c.style.display = (i===idx) ? 'block' : 'none';
    });
  }

  function loadMajors(facultyId){
    const majorSelect = document.getElementById('major_select');
    majorSelect.innerHTML = '<option value="">Loading...</option>';
    
    if(!facultyId){
      majorSelect.innerHTML = '<option value="">-- Select Faculty First --</option>';
      return;
    }
    
    fetch('enroll.php?ajax=get_majors&faculty_id=' + facultyId)
      .then(response => response.json())
      .then(majors => {
        if(majors.length === 0){
          majorSelect.innerHTML = '<option value="">-- No Majors Available --</option>';
        } else {
          majorSelect.innerHTML = '<option value="">-- Select Major --</option>';
          majors.forEach(m => {
            majorSelect.innerHTML += '<option value="' + m.major_id + '">' + m.major_name + '</option>';
          });
        }
      })
      .catch(err => {
        majorSelect.innerHTML = '<option value="">-- Error Loading Majors --</option>';
        console.error(err);
      });
  }
</script>
</body>
</html>
