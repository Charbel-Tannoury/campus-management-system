<?php
/**
 * Complaint Details View Page
 * 
 * Displays full details of a single anonymous complaint.
 * Features:
 * - ID-based complaint retrieval from URL parameter
 * - Detailed complaint information display
 * - Faculty association (if applicable)
 * - Timestamp information
 * - Formatted message display with proper text wrapping
 * - Navigation back to complaints list
 * - Error handling for invalid/missing complaint IDs
 * 
 * URL Parameters:
 * - id: Complaint ID to display
 * 
 * Security:
 * - Requires admin session (Faculty and emp_id)
 * - Validates complaint ID as integer
 * - Redirects to list on invalid ID
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// njib id lshakwa mn URL
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($complaint_id <= 0) {
    header('Location: list.php');
    exit();
}

// njib tafasil lshakwa
$query = "SELECT ac.ac_id, ac.faculty_id, ac.message, ac.created_at, u.faculty_name
          FROM anonymous_complaint ac 
          LEFT JOIN university u ON ac.faculty_id = u.faculty_id 
          WHERE ac.ac_id = $complaint_id";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($con));
}

$complaint = mysqli_fetch_assoc($result);

if (!$complaint) {
    header('Location: list.php?error=Complaint not found');
    exit();
}
$page_title = 'Complaint Details';
include '../../includes/head.php';
?>
    <style>
        .complaint-detail-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .complaint-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .complaint-body {
            padding: 30px;
        }
        .complaint-message {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
            font-size: 1.1em;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .meta-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .meta-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }
        .meta-item h4 {
            margin: 0 0 8px 0;
            color: #333;
            font-size: 0.9em;
            text-transform: uppercase;
            font-weight: 600;
        }
        .meta-item p {
            margin: 0;
            color: #666;
            font-size: 1em;
        }
        .action-buttons {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
        }
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }

      
    </style>
</head>
<body>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'complaints';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            <div class="page-header">
                <h1><i class="fas fa-eye"></i> Complaint Details</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <a href="list.php">Complaints</a>
                    <span>/</span>
                    <span>Complaint #<?= $complaint['ac_id'] ?></span>
                </div>
            </div>

            <div class="complaint-detail-card">
                <div class="complaint-header">
                    <h2><i class="fas fa-exclamation-circle"></i> Anonymous Student Complaint #<?= $complaint['ac_id'] ?></h2>
                    <p><i class="fas fa-calendar"></i> Submitted on <?= date('F j, Y \a\t g:i A', strtotime($complaint['created_at'])) ?></p>
                </div>

                <div class="complaint-body">
                    <h3><i class="fas fa-comment-alt"></i> Complaint Message</h3>
                    <div class="complaint-message">
                        <?= nl2br(htmlspecialchars($complaint['message'])) ?>
                    </div>

                    <h3><i class="fas fa-info-circle"></i> Additional Information</h3>
                    <div class="meta-info">
                        <div class="meta-item">
                            <h4><i class="fas fa-university"></i> Faculty</h4>
                            <p><?= htmlspecialchars($complaint['faculty_name'] ?: 'Unknown Faculty') ?></p>
                        </div>
                        <div class="meta-item">
                            <h4><i class="fas fa-calendar-alt"></i> Submission Date</h4>
                            <p><?= date('l, F j, Y', strtotime($complaint['created_at'])) ?></p>
                        </div>
                        <div class="meta-item">
                            <h4><i class="fas fa-clock"></i> Submission Time</h4>
                            <p><?= date('g:i A', strtotime($complaint['created_at'])) ?></p>
                        </div>
                        <div class="meta-item">
                            <h4><i class="fas fa-hourglass-half"></i> Time Since Submission</h4>
                            <p>
                                <?php 
                                $diff = date_diff(date_create($complaint['created_at']), date_create());
                                if ($diff->days > 0) {
                                    echo $diff->days . ' day(s) ago';
                                } elseif ($diff->h > 0) {
                                    echo $diff->h . ' hour(s) ago';
                                } else {
                                    echo $diff->i . ' minute(s) ago';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <div>
                        <a href="list.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <div style="display: flex; gap: 10px;">
                       
                        <a href="delete.php?id=<?= $complaint['ac_id'] ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this complaint? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/main.js"></script>
    <style>
        @media print {
            .sidebar, .top-header, .action-buttons, .page-header { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .complaint-detail-card { box-shadow: none; border: 1px solid #ddd; }
            body { background: white !important; }
        }
    </style>

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