<?php
/**
 * Student Conversations List Page
 * 
 * Displays all active student-faculty conversations.
 * Features:
 * - Lists all conversation threads for faculty
 * - Shows student name and email
 * - Displays last message preview
 * - Shows unread message count
 * - Indicates unread conversations with badge
 * - Click to open chat interface
 * - Displays message timestamp
 * - Avatar display for each student
 * 
 * Interface Elements:
 * - Chat item with avatar
 * - Student name and email
 * - Last message preview (truncated)
 * - Timestamp of last message
 * - Unread badge (if unread messages exist)
 * - Visual highlighting for unread conversations
 * 
 * Query:
 * - Fetches from student_statment table
 * - Joins with students for name/email
 * - Subquery for last message preview
 * - Counts unread messages (where from_id > 0 and seen = 0)
 * - Filters by faculty_id
 * - Orders by most recent message
 * 
 * Click Behavior:
 * - Redirects to chat.php?ss_id=[conversation_id]
 * - Opens full chat interface
 * 
 * Visual Indicators:
 * - Unread conversations: white background, purple border, shadow
 * - Read conversations: gray background
 * - Hover effect: darker background, purple border
 * 
 * Security:
 * - Session validation
 * - Faculty-based filtering
 * - XSS protection
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];
$page_title = 'Student Messages';
include '../../includes/head.php';
?>
    <style>
        .chat-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .chat-item:hover {
            background: #e9ecef;
            border-left-color: #667eea;
        }
        .chat-item.unread {
            background: #fff;
            border-left-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
        }
        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        .chat-info {
            flex: 1;
        }
        .chat-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .chat-preview {
            color: #666;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
        .chat-meta {
            text-align: right;
        }
        .chat-time {
            font-size: 0.8rem;
            color: #999;
            margin-bottom: 5px;
        }
        .chat-badge {
            background: #667eea;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
        }
        .no-messages {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .no-messages i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php
// l2e3dadat lsidebar w lheader
$base_path = '../../';
$current_page = 'chat';
include '../../includes/sidebar.php';
?>

    <main class="main-content">
        <?php include '../../includes/header.php'; ?>

        <div class="page-content">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1>Student Conversations</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <span>Student Chat</span>
                    <span>/</span>
                    <span>Conversations</span>
                </div>
            </div>

            <!-- Chat List Card -->
            <div class="card">
                <div class="card-header">
                    <h3>All Conversations</h3>
                 
                </div>
                <div class="card-body">
                    <div class="chat-list">
                        <?php
                        // njib lrsa2il
                        $query = "SELECT ss.ss_id, ss.student_id, ss.created_at, 
                                         s.first_name, s.last_name, s.email,
                                         (SELECT message FROM ss_messages WHERE ss_id = ss.ss_id ORDER BY message_id DESC LIMIT 1) as last_message,
                                         (SELECT COUNT(*) FROM ss_messages WHERE ss_id = ss.ss_id) as message_count
                                  FROM student_statment ss
                                  JOIN students s ON ss.student_id = s.student_id
                                  WHERE ss.faculty_id = '$fac'
                                  ORDER BY ss.created_at DESC";
                        $result = mysqli_query($con, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $studentName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($studentName) . "&background=667eea&color=fff";
                                $lastMessage = $row['last_message'] ? htmlspecialchars(substr($row['last_message'], 0, 50)) . '...' : 'No messages yet';
                                $dateCreated = date('M d, Y', strtotime($row['created_at']));
                        ?>
                        <a href="chat.php?ss_id=<?php echo $row['ss_id']; ?>" class="chat-item">
                            <img src="<?php echo $avatarUrl; ?>" alt="<?php echo $studentName; ?>" class="chat-avatar">
                            <div class="chat-info">
                                <div class="chat-name"><?php echo $studentName; ?></div>
                                <div class="chat-preview"><?php echo $lastMessage; ?></div>
                            </div>
                            <div class="chat-meta">
                                <div class="chat-time"><?php echo $dateCreated; ?></div>
                                <?php if ($row['message_count'] > 0) { ?>
                                <span class="chat-badge"><?php echo $row['message_count']; ?> messages</span>
                                <?php } ?>
                            </div>
                        </a>
                        <?php
                            }
                        } else {
                        ?>
                        <div class="no-messages">
                            <i class="fas fa-comments"></i>
                            <h3>No Conversations Yet</h3>
                            <p>When students send messages, they will appear here.</p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="../../assets/js/main.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchChat').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const chatItems = document.querySelectorAll('.chat-item');
            
            chatItems.forEach(item => {
                const name = item.querySelector('.chat-name').textContent.toLowerCase();
                const preview = item.querySelector('.chat-preview').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || preview.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
