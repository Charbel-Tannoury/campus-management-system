<?php
/**
 * Admin Panel Top Header Bar
 * 
 * Displays user information and navigation controls.
 * Features:
 * - Mobile menu toggle button
 * - Google Translate widget for multi-language support
 * - User profile display with avatar
 * - Faculty name display
 * - Employee name from session
 * - Dropdown menu with settings and logout
 * - Responsive path handling
 * 
 * Header Elements:
 * 1. Left Side:
 *    - Mobile menu toggle (hamburger icon)
 * 
 * 2. Right Side:
 *    - Google Translate widget
 *    - User profile section:
 *      * Avatar (generated from name)
 *      * Employee name
 *      * Faculty name
 *      * Dropdown arrow
 * 
 * 3. Dropdown Menu:
 *    - Settings link (university.php)
 *    - Logout link
 * 
 * Dynamic Data:
 * - Fetches employee name from employee table
 * - Fetches faculty name from university table
 * - Uses session variables (emp_id, Faculty)
 * - Generates avatar URL via ui-avatars.com
 * 
 * Usage:
 * - Included in all admin pages after sidebar
 * - Requires active database connection
 * - Requires authenticated session
 */

$script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
if (strpos($script_path, '/admin_panel/pages/') !== false) {

    $base_path = '../../';
} else {
    $base_path = '';
}
$show_search = $show_search ?? true;

// njib ism lfaculty
$header_faculty_name = 'Faculty of Sciences';
if (isset($con) && isset($_SESSION['Faculty'])) {
    $fac_id = $_SESSION['Faculty'];
    $fac_query = mysqli_query($con, "SELECT faculty_name FROM university WHERE faculty_id = $fac_id");
    if ($fac_query && mysqli_num_rows($fac_query) > 0) {
        $header_faculty_name = mysqli_fetch_assoc($fac_query)['faculty_name'];
    }
}

// njib ism lmwazaf
$emp_name = 'Admin';
if (isset($con) && isset($_SESSION['emp_id'])) {
    $emp_id = $_SESSION['emp_id'];
    $emp_query = mysqli_query($con, "SELECT first_name, last_name FROM employee WHERE emp_id = $emp_id");
    if ($emp_query && mysqli_num_rows($emp_query) > 0) {
        $emp_data = mysqli_fetch_assoc($emp_query);
        $emp_name = $emp_data['first_name'] . ' ' . $emp_data['last_name'];
    }
}
?>
<header class="top-header">
    <div class="header-left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        
    </div>
    
    <div class="header-right">
        
        <!-- Google Translate Widget -->
        <div id="google_translate_element" style="margin-right: 15px;"></div>
       
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($emp_name); ?>&background=667eea&color=fff" alt="<?php echo htmlspecialchars($emp_name); ?>">
            <div class="user-info">
                <h4><?php echo htmlspecialchars($emp_name); ?></h4>
                <p><?php echo htmlspecialchars($header_faculty_name); ?></p>
            </div>
            <i class="fas fa-chevron-down"></i>
            <!-- Dropdown for user profile -->
            <div class="dropdown-content">
                <div class="dropdown-body">
                    <a href="<?php echo $base_path; ?>pages/settings/university.php" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="<?php echo $base_path; ?>../login_logout/logout.php" class="dropdown-item" style="color: #f56565;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
