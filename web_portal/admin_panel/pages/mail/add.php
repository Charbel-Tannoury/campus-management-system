<?php
/**
 * Add Mail/Message Form Page
 * 
 * Admin interface for creating new announcements and messages.
 * Features:
 * - Recipient selection (all users, students only, professors only)
 * - Subject and message content fields
 * - Priority selection (normal/urgent)
 * - Faculty scope selection (local/new)
 * - Form validation (required fields)
 * - Breadcrumb navigation
 * - Session-based success messaging
 * 
 * Form Fields:
 * - Send To (dropdown): All Users, Students Only, Professors Only
 * - Subject (text, required)
 * - Message (textarea, required)
 * - Priority (dropdown): Normal, Urgent
 * - Faculty (dropdown): New, Local
 * 
 * Processing:
 * - Submits to add_process.php
 * - Creates mail record in mails table
 * - Associates with current faculty from session
 * 
 * Recipient Types:
 * - 0: All Users (students + professors)
 * - 1: Students Only
 * - 2: Professors Only
 * 
 * Faculty Scope:
 * - 0: Local (just this faculty)
 * - 1: New (all faculties)
 * 
 * Security:
 * - Session validation
 * - Faculty ID from session
 * - XSS protection needed on output
 * 
 * Use Case: Faculty administrators broadcasting announcements
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
$page_title = 'Add Mail';
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
                <h1>Add New Message</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <a href="list.php">Mail</a>
                    <span>/</span>
                    <span>Add Message</span>
                </div>
            </div>

            <!-- Add Mail Form -->
            <div class="card">
                <div class="card-header">
                    <h3>Message Information</h3>
                </div>
                <div class="card-body">
                    <form id="addMailForm" action="add_process.php" method="post">
                        
                        <div class="form-group">
                            <label>Send To <span style="color: #f56565;">*</span></label>
                            <select class="form-control" name="recipient_type" id="recipientType" required>
                                <option value="">Select Recipients</option>
                                <option value="0">All Users</option>
                                <option value="1">Students Only</option>
                                <option value="2">Professors Only</option>
                            </select>
                        </div>

</div>

                        <div class="form-group">
                            <label>Subject <span style="color: #f56565;">*</span></label>
                            <input type="text" class="form-control" name="subject" required placeholder="Message subject">
                        </div>

                        <div class="form-group">
                            <label>Message <span style="color: #f56565;">*</span></label>
                            <textarea class="form-control" name="message" required placeholder="Write your message content here..." rows="8"></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            
                            <div class="form-group">
                                <label>Priority</label>
                                <select class="form-control" name="priority">
                                    <option value="normal" selected>Normal</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Faculty</label>
                                <select class="form-control" name="for_faculty">
                                    <option value="new" selected>New</option>
                                    <option value="0">Local</option>
                                    <option value="1">Global</option>
                                </select>
                            </div>

                        </div>

                        <div class="form-group">
                            <label>Attachments</label>
                            <input type="file" class="form-control" name="attachments" multiple>
                            <small style="display: block; margin-top: 5px; color: #718096;">You can attach multiple files (PDF, DOC, JPG, PNG)</small>
                        </div>
                        <?php
                            if (isset($_SESSION['success'])) {
                                echo $_SESSION['success'];
                                unset($_SESSION['success']);
                            }
                        ?>

                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Message
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i>
                                Send Now
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

    <!-- JavaScript -->
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