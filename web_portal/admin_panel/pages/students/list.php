<?php
/**
 * Students List Page
 * 
 * Displays a comprehensive list of all students in the system.
 * Features:
 * - Student information display (ID, name, email, phone, verification status)
 * - Avatar generation using UI Avatars API
 * - Verification/Unverification toggle
 * - Links to profile view and edit pages
 * - Session-based authentication check
 */

session_start();
require_once '../../../connection.php';

// Authentication check - redirect to login if not authenticated
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// Page configuration
$page_title = 'Students List';
include '../../includes/head.php';
?>
<?php
// Include sidebar and header components
$base_path = '../../';           // Base path for asset linking
$current_page = 'students';      // Highlight students menu item
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            <div class="page-header"><h1>Students List</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><span>Students</span><span>/</span><span>Students List</span></div></div>
            <div class="card">
                <div class="card-header">
                    <h3>All Students</h3>
                </div>
                <div class="card-body">
                    <?php 
                    // Display success message if exists
                    if (isset($_SESSION['message'])): ?>
                        <div style="background: #c6f6d5; color: #276749; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php 
                    // Display error message if exists
                    if (isset($_SESSION['error'])): ?>
                        <div style="background: #fed7d7; color: #c53030; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="studentTable">
                            <thead><tr><th>#</th><th>Photo</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Verified</th><th>Created</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php
                            // Fetch all students from database ordered by ID
                            $query = "SELECT * FROM students ORDER BY student_id ASC";
                            $result = mysqli_query($con, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Prepare student data for display
                                    $name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                    
                                    // Generate avatar using UI Avatars API
                                    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($row['first_name'] . '+' . $row['last_name']) . "&background=667eea&color=fff";
                                    
                                    // Create verification status badge
                                    $verified = $row['verification'] ? '<span class="badge-status active">Verified</span>' : '<span class="badge-status inactive">Unverified</span>';
                                    $verifiedAt = $row['verified_at'] ? '<br><small>' . date('M d, Y', strtotime($row['verified_at'])) . '</small>' : '';
                                    
                                    echo "<tr>";
                                    echo "<td>" . $row['student_id'] . "</td>";
                                    echo "<td><img src='" . $avatar . "' style='width: 40px; height: 40px; border-radius: 50%;'></td>";
                                    echo "<td>" . $name . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phone_num'] ?? '-') . "</td>";
                                    echo "<td>" . $verified . $verifiedAt . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                                    echo "<td style='white-space: nowrap;'>";
                                    
                                    // Display Verify/Unverify button based on current verification status
                                    if ($row['verification']) {
                                        // Student is verified - show unverify button
                                        echo "<form method='post' action='verify_student.php' style='display:inline' onsubmit=\"return confirm('Unverify this student?');\">";
                                        echo "<input type='hidden' name='id' value='" . (int)$row['student_id'] . "'>";
                                        echo "<input type='hidden' name='action' value='unverify'>";
                                        echo "<button class='btn btn-sm btn-secondary' type='submit' title='Unverify'><i class='fas fa-times-circle'></i></button>";
                                        echo "</form> ";
                                    } else {
                                        // Student is not verified - show verify button
                                        echo "<form method='post' action='verify_student.php' style='display:inline' onsubmit=\"return confirm('Verify this student?');\">";
                                        echo "<input type='hidden' name='id' value='" . (int)$row['student_id'] . "'>";
                                        echo "<input type='hidden' name='action' value='verify'>";
                                        echo "<button class='btn btn-sm btn-success' type='submit' title='Verify'><i class='fas fa-check-circle'></i></button>";
                                        echo "</form> ";
                                    }
                                    
                                    // Profile, edit action buttons
                                    echo "<a href='profile.php?id=" . $row['student_id'] . "' class='btn btn-sm btn-info' title='Profile'><i class='fas fa-user'></i></a> ";
                                    echo "<a href='edit.php?id=" . $row['student_id'] . "' class='btn btn-sm btn-warning' title='Edit'><i class='fas fa-edit'></i></a> ";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No students found.</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
    <script>initTableSearch('searchStudent', 'studentTable');</script>

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
