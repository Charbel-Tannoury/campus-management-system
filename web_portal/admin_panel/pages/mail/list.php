<?php
/**
 * Mail Messages List Page
 * 
 * Admin interface displaying all mail/announcement messages relevant to the faculty.
 * Features:
 * - Lists all messages sent by or to the admin's faculty
 * - Displays sender, receivers, subject, date, and faculty information
 * - "Add New Message" button for creating announcements
 * - Action buttons for viewing and deleting messages
 * - Filters messages based on related_faculties field:
 *   - Shows messages where related_faculties = admin's faculty
 *   - OR related_faculties = 0 (broadcast to all)
 * 
 * Message Fields:
 * - Sender: Faculty name that created the message
 * - Receivers: Target audience (All, Students, Professors, etc.)
 * - Subject: mail_title field
 * - Date: created_at timestamp
 * - Faculty: Faculty association
 * - Actions: View and Delete buttons
 * 
 * Data Processing:
 * - Loops through mails table
 * - Joins with university table for faculty names
 * - Decodes receivers field (0=All, 1=Students, 2=Professors)
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac=$_SESSION['Faculty'];
$page_title = 'Mail List';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <?php 
    include '../../includes/head.php';
    ?>
</head>
<body>
<?php
// Setup for sidebar and header includes
// l2e3dadat lsidebar w lheader (settings for sidebar and header)
$base_path = '../../';
$current_page = 'mail';  // Highlights mail menu item in sidebar
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1>Messages List</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <span>Mail</span>
                    <span>/</span>
                    <span>Messages List</span>
                </div>
            </div>

            <!-- Mail List Card -->
            <div class="card">
                <div class="card-header">
                    <h3>All Messages</h3>
                    <div style="display: flex; gap: 10px;">
                       
                        <a href="add.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i>
                            Add New Message
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mailTable">
                            <thead>
                                <tr>
                                    <th>Sender</th>
                                    <th>Receivers</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Faculty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                    <?php
                                // Fetch all mails relevant to this faculty
                                // Shows messages where:
                                // - related_faculties matches admin's faculty
                                // - OR related_faculties = 0 (broadcast to all faculties)
                                $query= "SELECT * FROM mails WHERE related_faculties='$fac' OR related_faculties=0";
                                $result=mysqli_query($con,$query);
                                $row =mysqli_fetch_array($result);
                                $arraymail=[] ;  // Array to store processed mail data
                                
                                // Loop through all mails and fetch associated faculty names
                                for($i=0;$i<mysqli_num_rows($result);$i++){
                                    // Get faculty name for the sender
                                    $query2= "SELECT * FROM university WHERE faculty_id='".$row['faculty_id']."'";
                                    $arraymail[$i][0]= mysqli_fetch_array(mysqli_query($con,$query2))['faculty_name']." - Branch".mysqli_fetch_array(mysqli_query($con,$query2))['faculty_number'];
                                    
                                    // Decode receivers field (0=All, 1=Students, 2=Professors, etc.)
                                    if($row['receivers']==0){
                                        $arraymail[$i][1]= "All Users";
                                    }
                                    else if($row['receivers']==1){
                                        $arraymail[$i][1]= "Students Only";
                                    }
                                    else if($row['receivers']==2){
                                        $arraymail[$i][1]= "Professors Only";
                                    }
                                    if($row['related_faculties']==0){
                                        $arraymail[$i][4]= "Just this University";
                                    }
                                    else{
                                        $arraymail[$i][4]= "All Faculties";
                                    }
                                    $arraymail[$i][2] = $row['mail_title'];
                                    $arraymail[$i][3] = explode(".", $row['created_at'])[0];
                                    $arraymail[$i][5] = $row['mail_info'];
                                    $arraymail[$i][6] = $row['mail_id'];
                                    $arraymail[$i][7] = $row['priority'];
                                    $row =mysqli_fetch_array($result);
                                }
                                for($i=mysqli_num_rows($result)-1;$i>=0;$i--){
                                    echo '<tr>';
                                    echo '<td>' . $arraymail[$i][0] . '</td>';
                                    echo '<td>' . $arraymail[$i][1] . '</td>';
                                    echo '<td>' . $arraymail[$i][2] . '</td>';
                                    echo '<td>' . $arraymail[$i][3] . '</td>';
                                    if($arraymail[$i][7]=="urgent")
                                        echo '<td><span class="badge-status inactive" >' . $arraymail[$i][4] . '</span></td>';
                                    else
                                    echo '<td><span class="badge-status active" >' . $arraymail[$i][4] . '</span></td>';
                                    echo '<td>
                                            <button class="btn btn-sm btn-info" data-modal-target="viewMailModal" onclick="viewMail(' . $arraymail[$i][6] . ')">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                        <a href="edit.php?id=' . $arraymail[$i][6] . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        </td>';
                                    echo '</tr>';
                                }     
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        
    </main>

    <!-- View Mail Modal -->
    <div class="modal" id="viewMailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>View Message</h3>
                <button class="modal-close" data-modal-close>&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Sender:</label>
                    <p id="modalSender" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Target:</label>
                    <p id="modalTarget" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Subject:</label>
                    <p id="modalSubject" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Message:</label>
                    <p id="modalMessage" style="padding: 15px; background: #f7fafc; border-radius: 8px; margin-top: 5px; line-height: 1.8;">
                    </p>
                </div>
                <div class="form-group">
                    <label>Date:</label>
                    <p id="modalDate" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" data-modal-close>Close</button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    
    <script>
        // ba7s ljdwal
        initTableSearch('searchMail', 'mailTable');
        
        // 3ared tafasil lmail
        function viewMail(mailId) {
            // Fetch mail data from database
            fetch('view.php?id=' + mailId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalSender').textContent = data.faculty_name;
                        document.getElementById('modalTarget').textContent = data.receivers_text;
                        document.getElementById('modalSubject').textContent = data.mail_title;
                        document.getElementById('modalMessage').innerHTML = data.mail_info.replace(/\n/g, '<br>');
                        document.getElementById('modalDate').textContent = data.created_at;
                    } else {
                        alert('Error loading mail data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading mail data');
                });
        }
        
    </script>
    
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
