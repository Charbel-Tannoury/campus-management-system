<?php
/**
 * Edit Student Information Page
 * 
 * Admin interface for modifying student records.
 * Features:
 * - Update personal information (name, email, phone)
 * - Pre-filled form with current data
 * - Form validation and error handling
 * - Session-based success/error messages
 * - Breadcrumb navigation
 * - Faculty access control
 * 
 * Editable Fields:
 * - First Name
 * - Last Name
 * - Email
 * - Phone Number
 * 
 * Non-Editable:
 * - Student ID (used as hidden field)
 * - Password (separate change password feature)
 * - Major/Enrollment (requires different workflow)
 * 
 * Form Processing:
 * - Validates student ID from URL
 * - Fetches current data from students table
 * - On submit: updates record and redirects to list
 * - Displays error if student not found
 * 
 * Security:
 * - Session validation (Faculty + emp_id)
 * - mysqli_real_escape_string for SQL injection prevention
 * - Student ID cast to integer
 * 
 * Use Case: Faculty admin updating student contact information
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// Handle form submission
if(isset($_POST['submit'])) {
    $student_id = intval($_POST['student_db_id']);
    $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone_num = mysqli_real_escape_string($con, $_POST['phone_num']);
    
    $query = "UPDATE students SET
        first_name = '$first_name',
        last_name = '$last_name',
        email = '$email',
        phone_num = '$phone_num'
        WHERE student_id = $student_id";
    
    $result = mysqli_query($con, $query);
    
    if($result) {
        $_SESSION['message'] = 'Student updated successfully';
        header('Location: list.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update student';
    }
}

// Get student ID from URL
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($student_id == 0) {
    $_SESSION['error'] = 'Invalid student ID.';
    header('Location: list.php');
    exit;
}

// Fetch student data
$query = "SELECT * FROM students WHERE student_id = $student_id";
$result = mysqli_query($con, $query);

if(!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = 'Student not found.';
    header('Location: list.php');
    exit;
}

$row = mysqli_fetch_assoc($result);
$page_title = 'Edit Student';
include '../../includes/head.php';
?>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'students';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1>Edit Student</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <a href="list.php">Students</a>
                    <span>/</span>
                    <span>Edit Student</span>
                </div>
            </div>

            <!-- Edit Student Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Personal Information</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div style="background: #fed7d7; color: #c53030; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="editStudentForm" method="POST" novalidate>
                        <input type="hidden" name="student_db_id" value="<?php echo $row['student_id']; ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>First Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="first_name" required placeholder="First Name" value="<?php echo htmlspecialchars($row['first_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="last_name" required placeholder="Last Name" value="<?php echo htmlspecialchars($row['last_name']); ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Student ID <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="student_id" readonly value="<?php echo htmlspecialchars($row['student_id']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Email <span style="color: #f56565;">*</span></label>
                                <input type="email" class="form-control" name="email" required placeholder="Email" value="<?php echo htmlspecialchars($row['email']); ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone_num" placeholder="xxx-xxxxxxxx" value="<?php echo htmlspecialchars($row['phone_num'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Verification Status</label>
                                <div style="padding: 12px; background: #f7fafc; border-radius: 8px;">
                                    <?php 
                                    if($row['verification']) {
                                        echo '<span class="badge-status active">Verified</span>';
                                        if($row['verified_at']) {
                                            echo '<br><small>Verified on: ' . date('M d, Y', strtotime($row['verified_at'])) . '</small>';
                                        }
                                    } else {
                                        echo '<span class="badge-status inactive">Unverified</span>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Created At</label>
                                <input type="text" class="form-control" readonly value="<?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>">
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Student Data
                            </button>
                            <a href="list.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/main.js"></script>

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
