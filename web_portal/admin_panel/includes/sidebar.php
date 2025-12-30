<?php
/**
 * Admin Panel Sidebar Navigation
 * 
 * Main navigation menu for faculty administrators.
 * Features:
 * - Dynamic faculty information display
 * - Logo with link to dashboard
 * - Multi-level menu with submenus
 * - Active page highlighting
 * - Font Awesome icons for visual clarity
 * - Responsive path handling (pages vs. root)
 * 
 * Menu Structure:
 * 1. Mail: Messages list, Add message
 * 2. Student Chat: Conversations with students
 * 3. Complaints: Anonymous complaint viewing
 * 4. Courses: Course list, Add course
 * 5. Students: Student list, Student profile
 * 6. Professors: Professor list, Add professor
 * 7. Grades: Grade list, Add/Edit grades
 * 8. Settings: University/Faculty settings
 * 
 * Dynamic Elements:
 * - Faculty name from university table
 * - Faculty branch number
 * - Base URL adjustment based on current path
 * - Active menu highlighting via $current_page
 * 
 * Usage:
 * - Set $current_page before including
 * - Fetches faculty data from session
 * - Automatically adjusts paths for different depths
 * 
 * Styling:
 * - Dark sidebar background
 * - White/light text
 * - Submenu with indentation
 * - Hover effects
 */

$script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
if (strpos($script_path, '/admin_panel/pages/') !== false) {
    
    $base_url = '../../';
} else {
  
    $base_url = '';
}


$sidebar_show = [];
if (isset($con) && isset($_SESSION['Faculty'])) {
    $fac_id = $_SESSION['Faculty'];
    $f_info = mysqli_query($con, "SELECT faculty_name, faculty_number FROM university WHERE faculty_id = $fac_id");
    if ($f_info && mysqli_num_rows($f_info) > 0) {
        $sidebar_show = mysqli_fetch_assoc($f_info);
    }
}

// l2im l2asasiye
$f_name = $sidebar_show['faculty_name'] ?? '';
$f_number = $sidebar_show['faculty_number'] ?? '';
$current_page = $current_page ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-header">
         <a href="<?php echo $base_url; ?>index.php" <?php echo ($current_page == 'dashboard') ? 'class="active"' : ''; ?>>
            <img src="<?php echo $base_url; ?>assets/img/logo.png" alt="CCTJ - University" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 15px; border-radius: 50%; background: white; padding: 5px;">
        </a>
            <h2>CCTJ - University</h2>
        <p><?php echo htmlspecialchars($f_name); ?> - Branch <?php echo htmlspecialchars($f_number); ?></p>
    </div>
    
    <ul class="sidebar-menu">
        <li class="has-submenu <?php echo ($current_page == 'mail') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-envelope"></i>
                <span>Mail</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/mail/list.php">Messages List</a></li>
                <li><a href="<?php echo $base_url; ?>pages/mail/add.php">Add Message</a></li>
            </ul>
        </li>
        <li class="has-submenu <?php echo ($current_page == 'chat') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-comments"></i>
                <span>Student Chat</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/students_messages/list.php">Conversations</a></li>
            </ul>
        </li>
        <li class="has-submenu <?php echo ($current_page == 'complaints') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Complaints</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/complaints/list.php">View Complaints</a></li>
            </ul>
        </li>
        <li class="has-submenu <?php echo ($current_page == 'courses') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/courses/list.php">Courses List</a></li>
                <li><a href="<?php echo $base_url; ?>pages/courses/add.php">Add Course</a></li>
            </ul>
        </li>
        <li class="has-submenu <?php echo ($current_page == 'students') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/students/list.php">Students List</a></li>
            </ul>
        </li>

        <li class="has-submenu <?php echo ($current_page == 'doctors') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Professors</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/doctors/list.php">Professors List</a></li>
                <li><a href="<?php echo $base_url; ?>pages/doctors/add.php">Add Professor</a></li>
            </ul>
        </li>
        <li class="has-submenu <?php echo ($current_page == 'grades') ? 'active' : ''; ?>">
            <a href="javascript:void(0)">
                <i class="fas fa-star"></i>
                <span>Grades</span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $base_url; ?>pages/grades/list.php">Grades List</a></li>
                <li><a href="<?php echo $base_url; ?>pages/grades/add.php">Add Grades</a></li>
            </ul>
        </li>
    </ul>
</aside>
