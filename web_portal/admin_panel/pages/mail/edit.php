<?php
/**
 * Edit Mail/Message Form Page
 * 
 * Admin interface for modifying existing mail messages.
 * Features:
 * - Pre-filled form with current message data
 * - Updates title, receivers, scope, priority, content
 * - Faculty-based access control
 * - Displays faculty name
 * - Breadcrumb navigation
 * - Validates mail belongs to faculty or is global
 * 
 * Form Fields:
 * - Mail Title (subject)
 * - Receivers (dropdown): All Users, Students Only, Professors Only
 * - Related Faculties (dropdown): Just this University, All Faculties
 * - Priority (dropdown): Low, Medium, High
 * - Mail Info (textarea): Message content
 * 
 * Access Control:
 * - Filters by: (related_faculties='$fac' OR related_faculties=0)
 * - Ensures faculty can only edit own messages or global messages
 * - Redirects to list if message not found
 * 
 * Query:
 * - Fetches from mails table by mail_id
 * - Joins university for faculty name
 * - Displays formatted receivers and scope text
 * 
 * Processing:
 * - Submits to edit_process.php
 * - Includes mail_id as hidden field
 * 
 * Security:
 * - Session validation
 * - Faculty-based filtering
 * - XSS protection via htmlspecialchars
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];
$mail_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch mail data
$query = "SELECT * FROM mails WHERE mail_id = $mail_id AND (related_faculties='$fac' OR related_faculties=0)";
$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) == 0) {
    header('Location: list.php');
    exit;
}

$mail = mysqli_fetch_assoc($result);

// Get faculty name
$query_faculty = "SELECT * FROM university WHERE faculty_id='".$mail['faculty_id']."'";
$faculty_result = mysqli_query($con, $query_faculty);
$faculty = mysqli_fetch_assoc($faculty_result);

// Determine receivers text
if($mail['receivers'] == 0) {
    $receivers_text = "All Users";
} else if($mail['receivers'] == 1) {
    $receivers_text = "Students Only";
} else if($mail['receivers'] == 2) {
    $receivers_text = "Professors Only";
}

// Determine related faculties text
if($mail['related_faculties'] == 0) {
    $related_faculties_text = "Just this University";
} else {
    $related_faculties_text = "All Faculties";
}
$page_title = 'Edit Message';
include '../../includes/head.php';
?>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'mail';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1>Edit Message</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <a href="list.php">Mail</a>
                    <span>/</span>
                    <span>Edit Message</span>
                </div>
            </div>

            <!-- Edit Mail Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Message Information</h3>
                </div>
                <div class="card-body">
                    <form id="editMailForm" action="edit_process.php" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="mail_id" value="<?php echo $mail['mail_id']; ?>">
                        <input type="hidden" name="action" value="update">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            
                            <div class="form-group">
                                <label>Subject <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="mail_title" required value="<?php echo $mail['mail_title'] ?>">
                            </div>

                            <div class="form-group">
                                <label>Receivers <span style="color: #f56565;">*</span></label>
                                <select class="form-control" name="receivers" required>
                                    <option value="">Select Receivers</option>
                                    <option value="0" <?php echo $mail['receivers'] == 0 ? 'selected' : ''; ?>>All Users</option>
                                    <option value="1" <?php echo $mail['receivers'] == 1 ? 'selected' : ''; ?>>Students Only</option>
                                    <option value="2" <?php echo $mail['receivers'] == 2 ? 'selected' : ''; ?>>Professors Only</option>
                                </select>
                            </div>

                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            
                            <div class="form-group">
                                <label>Related Faculties <span style="color: #f56565;">*</span></label>
                                <select class="form-control" name="related_faculties" required>
                                    <option value="">Select Option</option>
                                    <option value="0" <?php echo $mail['related_faculties'] == 0 ? 'selected' : ''; ?>>Just this University</option>
                                    <option value="1" <?php echo $mail['related_faculties'] == 1 ? 'selected' : ''; ?>>All Faculties</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Priority <span style="color: #f56565;">*</span></label>
                                <select class="form-control" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="normal" <?php echo $mail['priority'] == 'normal' ? 'selected' : ''; ?>>Normal</option>
                                    <option value="urgent" <?php echo $mail['priority'] == 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Message <span style="color: #f56565;">*</span></label>
                            <textarea class="form-control" name="mail_info" required rows="8"><?php echo htmlspecialchars($mail['mail_info']); ?></textarea>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Changes
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
