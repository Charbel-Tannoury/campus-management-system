<?php
/**
 * Anonymous Complaints List Management Page
 * 
 * Displays and manages all anonymous student complaints.
 * Features:
 * - Paginated list of complaints (10 per page)
 * - Search functionality (message or faculty name)
 * - View complaint details in modal
 * - Delete complaint action
 * - Export to CSV functionality
 * - Days since submission calculation
 * - Faculty name display
 * - Responsive table layout
 * 
 * Pagination:
 * - 10 complaints per page
 * - URL parameter: ?page=X
 * - Shows page numbers with prev/next
 * 
 * Search:
 * - Filters by message content or faculty name
 * - URL parameter: ?search=keyword
 * - Case-insensitive LIKE search
 * 
 * Table Columns:
 * - ID
 * - Faculty
 * - Message (truncated preview with hover)
 * - Submitted Date
 * - Days Since Submission
 * - Actions (View/Delete)
 * 
 * Actions:
 * - View: Opens modal with full message
 * - Delete: Redirects to delete.php with confirmation
 * - Export CSV: exports all to CSV file
 * 
 * Query:
 * - Joins anonymous_complaint with university
 * - Orders by created_at DESC (newest first)
 * - Applies search filter if present
 * 
 * Security:
 * - Session validation
 * - XSS protection on output
 * 
 * Note: Shows ALL complaints regardless of faculty
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// njib ra2m lsaf7a
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// ba7s
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "WHERE ac.message LIKE '%$search%' OR u.faculty_name LIKE '%$search%'";
}

// njib l3adad lkamil
$totalQuery = "SELECT COUNT(*) as total FROM anonymous_complaint ac 
               LEFT JOIN university u ON ac.faculty_id = u.faculty_id 
               $searchCondition";
$totalResult = $con->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// njib byenat lshkawe
$query = "SELECT ac.ac_id, ac.faculty_id, ac.message, ac.created_at, u.faculty_name 
          FROM anonymous_complaint ac 
          LEFT JOIN university u ON ac.faculty_id = u.faculty_id 
          $searchCondition 
          ORDER BY ac.created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = $con->query($query);
$page_title = 'Student Complaints';
include '../../includes/head.php';
?>
    <style>
        .complaint-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .complaint-message {
            font-size: 0.9em;
            color: #666;
            line-height: 1.4;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .status-new {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .priority-high {
            border-left: 4px solid #f44336;
        }
        .priority-medium {
            border-left: 4px solid #ff9800;
        }
        .priority-low {
            border-left: 4px solid #4caf50;
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
                <h1><i class="fas fa-exclamation-triangle"></i> Student Complaints</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <span>Complaints</span>
                    <span>/</span>
                    <span>View Complaints</span>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i> Anonymous Student Complaints</h3>
                    <div style="display: flex; gap: 10px;">
                     
                        <span class="badge" style="background: #667eea; color: white; padding: 5px 10px; border-radius: 20px;">
                            Total: <?= $totalRows ?> complaints
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table id="complaintsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Faculty</th>
                                        <th>Complaint Message</th>
                                        <th>Submitted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = $offset + 1;
                                    while ($row = $result->fetch_assoc()): 
                                        $messagePreview = strlen($row['message']) > 100 ? substr($row['message'], 0, 100) . '...' : $row['message'];
                                        $facultyName = $row['faculty_name'] ? $row['faculty_name'] : 'Unknown Faculty';
                                        $submittedDate = date('M j, Y g:i A', strtotime($row['created_at']));
                                    ?>
                                    <tr>
                                        <td><?= $counter ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($facultyName) ?></strong>
                                        </td>
                                        <td>
                                            <div class="complaint-preview">
                                                <?= htmlspecialchars($messagePreview) ?>
                                            </div>
                                            <?php if (strlen($row['message']) > 100): ?>
                                                <small><em>Click "View" to read full message</em></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500;"><?= $submittedDate ?></div>
                                            <small class="text-muted">
                                                <?= date_diff(date_create($row['created_at']), date_create())->format('%a days ago') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
                                                <a href="view.php?id=<?= $row['ac_id'] ?>" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $row['ac_id'] ?>" class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this complaint? This action cannot be undone.')" 
                                                   title="Delete Complaint">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    $counter++;
                                    endwhile; 
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination-wrapper" style="margin-top: 20px; text-align: center;">
                                <div class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-outline">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" 
                                           class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-outline">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; color: #ddd;"></i>
                            <h3>No Complaints Found</h3>
                            <p>There are no student complaints to display at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/main.js"></script>
    <script>


     
        
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            
            // Auto-submit search form with slight delay
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.form.submit();
                    }, 500);
                });
            }
        });
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