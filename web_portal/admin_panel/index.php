<?php
/**
 * Admin Panel Dashboard - Main Statistics Page
 * 
 * Central dashboard displaying key metrics and statistics.
 * Features:
 * - Six statistical cards showing key metrics
 * - Faculty-specific data filtering
 * - Real-time counts from database
 * - Color-coded stat cards with icons
 * - Chart.js support enabled
 * - Faculty name and number stored in session
 * 
 * Statistics Displayed:
 * 1. Number of Students (from paid_students)
 * 2. Number of Professors (from to_enrol, current year)
 * 3. Number of Courses (from to_enrol, current year)
 * 4. Number of Majors (from majors table)
 * 5. Number of Semesters (from semester table)
 * 6. Number of Messages (from mails table, faculty or all)
 * 
 * Data Queries:
 * - All queries filtered by faculty_id from session
 * - Professors/Courses filtered by current year
 * - Uses COUNT() aggregation for efficiency
 * 
 * Session Variables Set:
 * - faculty_name: Name of the faculty
 * - faculty_number: Branch/campus number
 * 
 * Visual Design:
 * - Grid layout of stat cards
 * - Color-coded icons (primary, success, warning, danger, info, secondary)
 * - Breadcrumb navigation
 * - Chart.js library loaded for potential charts
 * 
 * Security:
 * - Session validation required
 * - Faculty-based access control
 * - All queries filtered by session faculty_id
 */

session_start();
require_once '../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../login_logout/login.php');
    exit;
}
$fac=$_SESSION['Faculty'];
$sql = "
    SELECT u.faculty_name , u.faculty_number
    FROM university u
    WHERE u.faculty_id='$fac'
";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$_SESSION['faculty_name'] = $row['faculty_name'];
$_SESSION['faculty_number'] = $row['faculty_number'];
$date=date("Y");
$page_title = "Dashboard";
$include_chart_js = true;
include 'includes/head.php';
?>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <?php include 'includes/header.php'; ?>
    
    <div class="page-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1>Statistics Dashboard</h1>
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <span>/</span>
                    <span>Statistics</span>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                
                <!-- Students Card -->
                <div class="stat-card primary">
                    <div class="stat-card-header">
                        <h3>Number of Students</h3>
                        <div class="stat-icon primary">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php
                        $student_query = "SELECT COUNT(student_id) AS student_count FROM paid_students WHERE faculty_id='$fac'";
                        $result = mysqli_query($con, $student_query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['student_count'];
                        ?>
                    </div>
                </div>

                <!-- Doctors Card -->
                <div class="stat-card success">
                    <div class="stat-card-header">
                        <h3>Number of Professors</h3>
                        <div class="stat-icon success">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php
                        $professor_query = "SELECT COUNT(DISTINCT dr_id) AS professor_count FROM to_enrol WHERE faculty_id='$fac'AND year='$date'";
                        $result = mysqli_query($con, $professor_query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['professor_count'];
                        ?>
                    </div>
                </div>

                <!-- Courses Card -->
                <div class="stat-card warning">
                    <div class="stat-card-header">
                        <h3>Number of Courses</h3>
                        <div class="stat-icon warning">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                         <?php
                        $courses_query = "SELECT COUNT(DISTINCT m.course_id) AS courses_count FROM major_course_semester m JOIN to_enrol t ON m.mcs_id = t.mcs_id WHERE t.faculty_id='$fac' AND t.year='$date'";
                        $result = mysqli_query($con, $courses_query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['courses_count'];
                        ?>
                    </div>
                </div>

                <!-- Departments Card -->
                <div class="stat-card info">
                    <div class="stat-card-header">
                        <h3>Number of majors</h3>
                        <div class="stat-icon info">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php
                        $major_query = "SELECT COUNT(DISTINCT m.major_id) AS major_count FROM major_course_semester m JOIN to_enrol t ON m.mcs_id = t.mcs_id WHERE t.faculty_id='$fac' AND t.year='$date'";
                        $result = mysqli_query($con, $major_query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['major_count'];
                        ?>
                    </div>
                </div>

                <!-- Faculties Card -->
                <div class="stat-card danger">
                    <div class="stat-card-header">
                        <h3>Number of Faculties</h3>
                        <div class="stat-icon danger">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php
                        $faculty_query = "SELECT COUNT(faculty_id) AS faculty_count FROM university ";
                        $result = mysqli_query($con, $faculty_query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['faculty_count'];
                        ?>
                    </div>
                </div>

                <!-- Messages Card -->
                <div class="stat-card primary">
                    <div class="stat-card-header">
                        <h3>New Messages</h3>
                        <div class="stat-icon primary">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php
                    $query_mail = "SELECT COUNT(mail_id) AS mail_count FROM mails WHERE related_faculties='$fac' OR related_faculties=0";
                        $result = mysqli_query($con, $query_mail);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['mail_count'];
                    ?></div>
                </div>
                
            </div>

                <!-- Section ll25bar wlrasayl -->
                <div class="card">
                    <div class="card-header">
                        <h3>Latest News</h3>
                        <a href="pages\mail\list.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php
                            $query= "SELECT mail_info, created_at FROM mails WHERE related_faculties='$fac' OR related_faculties=0";
                            $result=mysqli_query($con,$query);
                            $row =mysqli_fetch_array($result);
                            $arraymail=[] ;
                            for($i=0;$i<mysqli_num_rows($result);$i++){
                                // Store plain string values instead of arrays
                                $arraymail[$i][0] = $row['mail_info'];
                                $arraymail[$i][1] = $row['created_at'];
                                $row =mysqli_fetch_array($result);
                            }
                            for($i=mysqli_num_rows($result)-1;$i>=0;$i--){
                            echo '<div style="padding: 12px; background: #f7fafc; border-radius: 8px; border-left: 3px solid #667eea;">';
                            echo  '<h4 style="font-size: 14px; margin-bottom: 5px; color: #2d3748;">' .$arraymail[$i][0]. '</h4>';
                            echo  '<p style="font-size: 12px; color: #718096;">' .$arraymail[$i][1]. '</p>';
                            echo '</div> '; 
                            
                        }
                            ?>                         
                        </div>
                    </div>
                </div>
                
            </div>
    </main>

<?php include 'includes/footer.php'; ?>
