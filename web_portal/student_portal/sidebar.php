<?php
/**
 * Student Portal Sidebar Navigation
 * 
 * Reusable navigation sidebar for all student portal pages.
 * Features:
 * - Student name display from session
 * - Profile photo with rounded border
 * - Navigation menu with Font Awesome icons
 * - Active page highlighting
 * - Google Translate widget integration
 * - Logout button at bottom
 * - Logo link to home page
 * 
 * Menu Items:
 * - News: Latest announcements
 * - My Courses: Enrolled courses and materials
 * - Grades: View grade reports
 * - Profile: Personal information
 * - Requests: Faculty messaging/chat system
 * - Enrollment: Course enrollment
 * - Logout: Session termination
 * 
 * Usage:
 * - Set $base_path before including (e.g., './' or '../')
 * - Set $current_page to highlight active menu item
 * - Requires active session with student ID
 * 
 * Styling:
 * - Dark background with white text
 * - Hover effects on menu items
 * - Active state with different color
 * - Separator before logout
 */

$base_path = $base_path ?? './';
$current_page = $current_page ?? '';

// Get student info from session
$student_name = 'Student';
if (isset($_SESSION['id']) && isset($con)) {
    $sid = $_SESSION['id'];
    $student_query = mysqli_query($con, "SELECT first_name, last_name FROM students WHERE student_id = '$sid'");
    if ($student_query && mysqli_num_rows($student_query) > 0) {
        $student_data = mysqli_fetch_assoc($student_query);
        $student_name = $student_data['first_name'] . ' ' . $student_data['last_name'];
    }
}
?>
<aside class="sidebar">
    <div class="sidebar-header">
         <a href="<?php echo $base_path; ?>home.php" <?php echo ($current_page == 'home') ? 'class="active"' : ''; ?>>
        <img src="../student_portal/assets/img/logo.png" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px; border: 3px solid rgba(255,255,255,0.2);">
    </a>
        <h2>Student Portal</h2>
        <p><?php echo htmlspecialchars($student_name); ?></p>
        
        <!-- Google Translate Widget -->
        <div id="google_translate_element" style="margin-top: 10px;"></div>
    </div>
    
    <ul class="sidebar-menu">
        
        <li>
            <a href="<?php echo $base_path; ?>news.php" <?php echo ($current_page == 'news') ? 'class="active"' : ''; ?>>
                <i class="fas fa-newspaper"></i>
                <span>News</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_path; ?>materials.php" <?php echo ($current_page == 'courses') ? 'class="active"' : ''; ?>>
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_path; ?>grades.php" <?php echo ($current_page == 'grades') ? 'class="active"' : ''; ?>>
                <i class="fas fa-chart-bar"></i>
                <span>Grades</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_path; ?>profile.php" <?php echo ($current_page == 'profile') ? 'class="active"' : ''; ?>>
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_path; ?>requests.php" <?php echo ($current_page == 'requests') ? 'class="active"' : ''; ?>>
                <i class="fas fa-envelope"></i>
                <span>Requests</span>
            </a>
        </li>
        
        <li>
            <a href="<?php echo $base_path; ?>enroll.php" <?php echo ($current_page == 'enroll') ? 'class="active"' : ''; ?>>
                <i class="fas fa-graduation-cap"></i>
                <span>Enrollment</span>
            </a>
        </li>
        
        <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
            <a href="../login_logout/logout.php" style="color: #f56565;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
<div class="sidebar-overlay"></div>
<script src="assets/js/main.js"></script>
