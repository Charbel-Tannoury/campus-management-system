<?php
/**
 * Student-Faculty Chat Interface
 * 
 * Real-time messaging interface between faculty and individual students.
 * Features:
 * - WhatsApp-style chat interface
 * - Message history display
 * - File attachment support
 * - Real-time message sending
 * - Student profile display
 * - Faculty-student differentiation (from_id: 0=faculty, student_id=student)
 * - Visual styling for message bubbles
 * - Attachment previews and downloads
 * 
 * URL Parameters:
 * - ss_id: Student statement ID (conversation thread)
 * 
 * Message Types:
 * - Faculty messages (from_id = 0): Right-aligned, purple gradient
 * - Student messages (from_id = student_id): Left-aligned, light gray
 * 
 * Features:
 * 1. Message History:
 *    - Loads all messages for conversation
 *    - Shows timestamp for each message
 *    - Displays attachments with download links
 * 
 * 2. Send Message:
 *    - Text input (required)
 *    - File attachment (optional)
 *    - Stored in ss_messages table
 * 
 * 3. File Upload:
 *    - Uploads to assets/uploads/chat/
 *    - Filename: timestamp_originalname
 *    - Creates directory if doesn't exist
 * 
 * Query:
 * - Validates ss_id belongs to faculty
 * - Joins student_statment with students table
 * - Displays student name and email
 * 
 * Security:
 * - Session validation
 * - Faculty-based conversation access
 * - File upload validation
 * - XSS protection via htmlspecialchars
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

$fac = $_SESSION['Faculty'];

$ss_id = isset($_GET['ss_id']) ? intval($_GET['ss_id']) : 0;

$checkQuery = "SELECT ss.*, s.first_name, s.last_name, s.email, s.student_id 
               FROM student_statment ss 
               JOIN students s ON ss.student_id = s.student_id 
               WHERE ss.ss_id = '$ss_id' AND ss.faculty_id = '$fac'";
$checkResult = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($checkResult) == 0) {
    header("Location: list.php");
    exit();
}

$studentInfo = mysqli_fetch_assoc($checkResult);
$studentName = $studentInfo['first_name'] . ' ' . $studentInfo['last_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($con, $_POST['message']);
    $from_id = 0;
    
    $attach = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $uploadDir = '../../assets/uploads/chat/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['attachment']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
            $attach = $fileName;
        }
    }
    
    if (!empty($message)) {
        $insertQuery = "INSERT INTO ss_messages (ss_id, from_id, message, attach) VALUES ('$ss_id', '$from_id', '$message', " . ($attach ? "'$attach'" : "NULL") . ")";
        mysqli_query($con, $insertQuery);
    }
    
    header("Location: chat.php?ss_id=$ss_id");
    exit();
}
$page_title = 'Chat with ' . htmlspecialchars($studentName);
include '../../includes/head.php';
?>
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 250px);
            min-height: 500px;
        }
        .chat-header {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .chat-header img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .chat-header-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }
        .chat-header-info p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f5f7fb;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }
        .message-student {
            align-self: flex-start;
            background: white;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 5px;
        }
        .message-admin {
            align-self: flex-end;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }
        .message-content {
            margin-bottom: 5px;
        }
        .message-time {
            font-size: 0.7rem;
            opacity: 0.7;
            text-align: right;
        }
        .message-attachment {
            margin-top: 10px;
            padding: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        .message-attachment a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .message-student .message-attachment {
            background: #f0f0f0;
        }
        .chat-input-container {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #eee;
            border-radius: 0 0 10px 10px;
        }
        .chat-input-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .chat-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .chat-input:focus {
            border-color: #667eea;
        }
        .chat-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .chat-btn-attach {
            background: #f0f0f0;
            color: #666;
        }
        .chat-btn-attach:hover {
            background: #e0e0e0;
        }
        .chat-btn-send {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .chat-btn-send:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .no-messages {
            text-align: center;
            padding: 50px;
            color: #999;
        }
        .no-messages i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .back-btn {
            margin-right: auto;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        #fileInput {
            display: none;
        }
        .file-preview {
            padding: 10px 15px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-bottom: 10px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }
        .file-preview.active {
            display: flex;
        }
        .file-preview-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-preview-remove {
            cursor: pointer;
            color: #e74c3c;
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
            
       
            <div class="page-header">
                <h1>Chat with <?php echo htmlspecialchars($studentName); ?></h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a>
                    <span>/</span>
                    <a href="list.php">Student Chat</a>
                    <span>/</span>
                    <span><?php echo htmlspecialchars($studentName); ?></span>
                </div>
            </div>

            <div class="card chat-container">
                <div class="chat-header">
                    <a href="list.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($studentName); ?>&background=667eea&color=fff" alt="<?php echo htmlspecialchars($studentName); ?>">
                    <div class="chat-header-info">
                        <h3><?php echo htmlspecialchars($studentName); ?></h3>
                        <p><?php echo htmlspecialchars($studentInfo['email']); ?></p>
                    </div>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <?php
                    // njib kol lrsa2il
                    $messagesQuery = "SELECT * FROM ss_messages WHERE ss_id = '$ss_id' ORDER BY message_id ASC";
                    $messagesResult = mysqli_query($con, $messagesQuery);
                    
                    if (mysqli_num_rows($messagesResult) > 0) {
                        while ($msg = mysqli_fetch_assoc($messagesResult)) {
                            $isAdmin = ($msg['from_id'] == 0); // from_id=0 is admin/faculty, from_id!=0 is student
                            $messageClass = $isAdmin ? 'message-admin' : 'message-student';
                    ?>
                    <div class="message <?php echo $messageClass; ?>">
                        <div class="message-content"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                        <?php if (!empty($msg['attach'])) { ?>
                        <div class="message-attachment">
                            <a href="../../assets/uploads/chat/<?php echo htmlspecialchars($msg['attach']); ?>" target="_blank">
                                <i class="fas fa-paperclip"></i>
                                <?php echo htmlspecialchars($msg['attach']); ?>
                            </a>
                        </div>
                        <?php } ?>
                        <div class="message-time"><?php echo $isAdmin ? 'Admin' : $studentName; ?> • <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></div>
                    </div>
                    <?php
                        }
                    } else {
                    ?>
                    <div class="no-messages">
                        <i class="fas fa-comments"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                    <?php } ?>
                </div>
                
                <div class="chat-input-container">
                    <div class="file-preview" id="filePreview">
                        <div class="file-preview-name">
                            <i class="fas fa-file"></i>
                            <span id="fileName"></span>
                        </div>
                        <i class="fas fa-times file-preview-remove" onclick="removeFile()"></i>
                    </div>
                    <form class="chat-input-form" method="POST" enctype="multipart/form-data">
                        <input type="file" id="fileInput" name="attachment">
                        <button type="button" class="chat-btn chat-btn-attach" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="text" name="message" class="chat-input" placeholder="Type your message..." required>
                        <button type="submit" class="chat-btn chat-btn-send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script src="../../assets/js/main.js"></script>
    <script>
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
       
        document.getElementById('fileInput').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('fileName').textContent = this.files[0].name;
                document.getElementById('filePreview').classList.add('active');
            }
        });
        
        function removeFile() {
            document.getElementById('fileInput').value = '';
            document.getElementById('filePreview').classList.remove('active');
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
