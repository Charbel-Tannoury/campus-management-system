<?php
/**
 * Student Requests & Messaging Page
 * 
 * Comprehensive messaging system for students to communicate with faculty administration.
 * Features:
 * - Two-way messaging with faculty (like a chat/ticket system)
 * - Anonymous complaint submission
 * - Conversation history with multiple faculties
 * - File attachment support
 * - Real-time message display
 * - Faculty selection dropdown
 * 
 * Message Types:
 * 1. Student Statement (student_statment table)
 *    - Creates a conversation thread with a specific faculty
 *    - Messages stored in ss_messages table
 *    - Shows as conversation history
 * 
 * 2. Anonymous Complaint (anonymous_complaint table)
 *    - Faculty-directed complaints
 *    - No conversation thread
 *    - One-way submission
 * 
 * Interface Sections:
 * - Left sidebar: List of conversation threads with faculties
 * - Main area: Selected conversation messages
 * - Right panel: New message/complaint forms
 * 
 * URL Parameters:
 * - chat: ss_id of conversation to display
 * 
 * Data Flow:
 * - Loads all conversations for student
 * - Displays messages for selected conversation
 * - Allows sending new messages or creating new conversations
 * - Anonymous complaints submitted separately
 */

session_start();
require_once '../connection.php';


function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}
$qFac = "SELECT s.email FROM students s WHERE s.student_id = $student_id";
$rFac = mysqli_query($con, $qFac);
$studentEmail = mysqli_fetch_assoc($rFac)['email'] ;

$successReq  = '';
$successAnon = '';
$errorMsg    = '';


$faculties = [];
$qFac = "SELECT faculty_id, faculty_name ,faculty_number FROM university ORDER BY faculty_name ASC";
$rFac = mysqli_query($con, $qFac);
if($rFac){
  while($f = mysqli_fetch_assoc($rFac)){
    $faculties[] = $f;
  }
}

// Fetch conversations for this student from student_statment
$conversations = [];
$qConv = "SELECT ss.ss_id, ss.faculty_id, u.faculty_name, u.faculty_number 
          FROM student_statment ss 
          JOIN university u ON ss.faculty_id = u.faculty_id 
          WHERE ss.student_id = $student_id 
          ORDER BY ss.created_at DESC";
$rConv = mysqli_query($con, $qConv);
if($rConv){
  while($c = mysqli_fetch_assoc($rConv)){
    $conversations[] = $c;
  }
}

// Get selected conversation messages
$selected_ss_id = isset($_GET['chat']) ? (int)$_GET['chat'] : (isset($conversations[0]) ? $conversations[0]['ss_id'] : 0);
$chat_messages = [];
$selected_faculty_name = '';

if($selected_ss_id > 0){
  // Get faculty name for selected conversation
  foreach($conversations as $c){
    if($c['ss_id'] == $selected_ss_id){
      $selected_faculty_name = $c['faculty_name'] . ' ' . $c['faculty_number'];
      break;
    }
  }
  
  // Fetch messages for this conversation
  $qMsg = "SELECT message_id, from_id, message, attach, created_at FROM ss_messages WHERE ss_id = $selected_ss_id ORDER BY created_at ASC";
  $rMsg = mysqli_query($con, $qMsg);
  if($rMsg){
    while($m = mysqli_fetch_assoc($rMsg)){
      $chat_messages[] = $m;
    }
  }
}


function faculty_id_from_name(mysqli $con, string $faculty_name){
  $faculty_name_esc = mysqli_real_escape_string($con, $faculty_name);
  $sql = "SELECT faculty_id FROM university WHERE faculty_name = '$faculty_name_esc' LIMIT 1";
  $res = mysqli_query($con, $sql);
  
  if(!$res) return [false, "Query failed (faculty lookup): ".mysqli_error($con)];

  $row = mysqli_fetch_assoc($res);
  if(!$row) return [false, "Faculty not found."];
  return [(int)$row['faculty_id'], null];
}


function get_or_create_ss_id(mysqli $con, int $student_id, int $faculty_id){
  $sqlGet = "SELECT ss_id FROM student_statment WHERE student_id=$student_id AND faculty_id=$faculty_id LIMIT 1";
  $res = mysqli_query($con, $sqlGet);
  
  if(!$res) return [false, "Query failed (get ss_id): ".mysqli_error($con)];

  $row = mysqli_fetch_assoc($res);
  if($row && isset($row['ss_id'])){
    return [(int)$row['ss_id'], null];
  }

  $sqlIns = "INSERT INTO student_statment (student_id, faculty_id) VALUES ($student_id, $faculty_id)";
  $ok = mysqli_query($con, $sqlIns);
  
  if(!$ok) return [false, "Insert failed (student_statment): ".mysqli_error($con)];
  $newId = mysqli_insert_id($con);
  return [(int)$newId, null];
}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $action = $_POST['action'] ?? '';

  // ===== Option 1: Request =====
  if($action === 'send_request'){
    $faculty_name = trim($_POST['faculty_name'] ?? '');
    $title        = trim($_POST['title'] ?? '');
    $details      = trim($_POST['details'] ?? '');

    if($faculty_name === '' || $title === '' || $details === ''){
      $errorMsg = "Please fill all request fields.";
    } else {
      [$faculty_id, $errF] = faculty_id_from_name($con, $faculty_name);

      if($faculty_id === false){
        $errorMsg = $errF;
      } else {
        [$ss_id, $err] = get_or_create_ss_id($con, (int)$student_id, (int)$faculty_id);

        if($ss_id === false){
          $errorMsg = $err;
        } else {
          $msg = "REQUEST: ".$title."\n\n".$details;
          $msg_esc = mysqli_real_escape_string($con, $msg);

          $sqlMsg = "INSERT INTO ss_messages (ss_id, from_id, message, attach) VALUES ($ss_id, 1, '$msg_esc', NULL)";
          $ok = mysqli_query($con, $sqlMsg);

          if($ok) $successReq = "Your request has been submitted successfully!";
          else $errorMsg = "Insert failed (ss_messages): ".mysqli_error($con);
        }
      }
    }
  }

  //Option 2: Anonymous complaint 
  if($action === 'send_anonymous'){
    $faculty_name = trim($_POST['faculty_name_anonymous'] ?? '');
    $anon_title   = trim($_POST['anon_title'] ?? '');
    $anon_desc    = trim($_POST['anon_description'] ?? '');

    if($faculty_name === '' || $anon_title === '' || $anon_desc === ''){
      $errorMsg = "Please choose faculty and fill title and description.";
    } else {
      [$faculty_id, $errF] = faculty_id_from_name($con, $faculty_name);

      if($faculty_id === false){
        $errorMsg = $errF;
      } else {
        $combined = "TITLE: ".$anon_title."\n\n".$anon_desc;
        $combined_esc = mysqli_real_escape_string($con, $combined);
       
        $sqlAnon = "INSERT INTO anonymous_complaint (faculty_id, message) VALUES ($faculty_id, '$combined_esc')";
        $ok = mysqli_query($con, $sqlAnon);

        if($ok) $successAnon = "Anonymous complaint sent successfully!";
        else $errorMsg = "Insert failed (anonymous_complaint): ".mysqli_error($con);
      }
    }
  }

  // Option 3: Send chat message
  if($action === 'send_chat_message'){
    $ss_id = (int)($_POST['ss_id'] ?? 0);
    $chat_message = trim($_POST['chat_message'] ?? '');
    
    if($ss_id > 0 && $chat_message !== ''){
      $chat_message_esc = mysqli_real_escape_string($con, $chat_message);
      $sqlChat = "INSERT INTO ss_messages (ss_id, from_id, message, created_at) VALUES ($ss_id, $student_id, '$chat_message_esc', NOW())";
      mysqli_query($con, $sqlChat);
    }
    // Redirect back to the chat
    header("Location: requests.php?chat=" . $ss_id);
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - Requests</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

<div class="layout">
  <?php include 'sidebar.php'; ?>

  <main class="main">
    <header class="topbar">
      <div class="left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <span><?php echo e($studentEmail); ?></span>
      </div>
      <div class="right"><i class="fas fa-envelope"></i> Requests</div>
    </header>

    <section class="content">

      <div class="page-title">Send Request + Anonymous Complaint</div>

      <?php if($errorMsg): ?><div class="error-box"><?php echo e($errorMsg); ?></div><?php endif; ?>
      <?php if($successReq): ?><div class="success-box"><?php echo e($successReq); ?></div><?php endif; ?>
      <?php if($successAnon): ?><div class="success-box"><?php echo e($successAnon); ?></div><?php endif; ?>

      <div class="tabs">
        <button class="tab-btn active" onclick="showTab(1)" type="button">Option 1: Request</button>
        <button class="tab-btn" onclick="showTab(2)" type="button">Option 2: Anonymous</button>
        <button class="tab-btn" onclick="showTab(3)" type="button">Old Requests</button>
      </div>

      <!-- TAB 1 -->
      <div id="tab1" class="section active">
        <h3 style="margin:0 0 10px;font-size:15px;"><i class="fas fa-pencil-alt"></i> Request to Administration</h3>

        <form method="POST">
          <input type="hidden" name="action" value="send_request">

          <div class="form-grid">
            <div class="field">
              <label>Faculty</label>
              <select class="select" name="faculty_name" required>
                <option value="">Select faculty</option>
                <?php foreach($faculties as $f): ?>
                  <option value="<?php echo e($f['faculty_name']); ?>"><?php echo e($f['faculty_name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field" style="grid-column:1/-1;">
              <label>Request title</label>
              <input class="input" name="title" type="text" required>
            </div>

            <div class="field" style="grid-column:1/-1;">
              <label>Request description</label>
              <textarea class="textarea" name="details" required></textarea>
            </div>
          </div>

          <div style="margin-top:10px;display:flex;gap:8px;">
            <button class="btn btn-success" type="submit">Submit Request</button>
            <button class="btn btn-primary" type="reset">Clear</button>
          </div>
        </form>
      </div>

      <!-- TAB 2 -->
      <div id="tab2" class="section">
        <h3 style="margin:0 0 10px;font-size:15px;"><i class="fas fa-user-secret"></i> Anonymous Complaint</h3>

        <form method="POST">
          <input type="hidden" name="action" value="send_anonymous">

          <div class="field">
            <label>Faculty</label>
            <select class="select" name="faculty_name_anonymous" required>
              <option value="">Select faculty</option>
              <?php foreach($faculties as $f): ?>
                <option value="<?php echo e($f['faculty_name']); ?>">
                  <?php echo e($f['faculty_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field" style="margin-top:12px;">
            <label>Title</label>
            <input class="input" name="anon_title" type="text" required>
          </div>

          <div class="field" style="margin-top:12px;">
            <label>Description</label>
            <textarea class="textarea" name="anon_description" required></textarea>
          </div>

          <div style="margin-top:10px;display:flex;gap:8px;">
            <button class="btn btn-success" type="submit">Send Anonymous</button>
            <button class="btn btn-primary" type="reset">Clear</button>
          </div>
        </form>
      </div>
      <div id="tab3" class="section">
        <h3 style="margin:0 0 10px;font-size:15px;"><i class="fas fa-comments"></i> My Conversations</h3>

        <div class="chat-container">
          <!-- Left: Conversation List -->
          <div class="chat-list" style="max-height: 400px; overflow-y: auto;">
            <?php if(empty($conversations)): ?>
              <div class="no-conversations">No conversations yet. Send a request to start one.</div>
            <?php else: ?>
              <?php foreach($conversations as $c): ?>
                <a href="?chat=<?php echo $c['ss_id']; ?>" class="chat-item <?php echo ($c['ss_id'] == $selected_ss_id) ? 'active' : ''; ?>" onclick="showTab(3)">
                  <i class="fas fa-university"></i>
                  <span><?php echo e($c['faculty_name'] . ' ' . $c['faculty_number']); ?></span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Right: Chat Box -->
          <div class="chat-box">
            <?php if($selected_ss_id > 0 && !empty($selected_faculty_name)): ?>
              <div class="chat-header">
                <i class="fas fa-university"></i>
                <span><?php echo e($selected_faculty_name); ?></span>
              </div>
              <div class="chat-messages" id="chatMessages" style="max-height: 350px; overflow-y: auto;">
                <?php if(empty($chat_messages)): ?>
                  <div class="no-messages">No messages yet in this conversation.</div>
                <?php else: ?>
                  <?php foreach($chat_messages as $msg): ?>
                    <?php $isStudent = ($msg['from_id'] != 0); // from_id=0 is faculty, from_id!=0 is student ?>
                    <div class="message <?php echo $isStudent ? 'student' : 'faculty'; ?>">
                      <div class="message-content">
                        <p><?php echo nl2br(e($msg['message'])); ?></p>
                        <?php if($msg['attach']): ?>
                          <div class="attachment"><i class="fas fa-paperclip"></i> <?php echo e($msg['attach']); ?></div>
                        <?php endif; ?>
                        <span class="message-time"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
              <div class="chat-input">
                <form method="POST" style="display:flex;gap:10px;width:100%;">
                  <input type="hidden" name="action" value="send_chat_message">
                  <input type="hidden" name="ss_id" value="<?php echo $selected_ss_id; ?>">
                  <input type="text" name="chat_message" class="input" placeholder="Type your message..." style="flex:1;" required>
                  <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </form>
              </div>
            <?php else: ?>
              <div class="no-chat-selected">
                <i class="fas fa-comments" style="font-size:48px;color:#cbd5e1;"></i>
                <p>Select a conversation to view messages</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
  function showTab(n){
    document.querySelectorAll(".tab-btn").forEach((b,i)=> b.classList.toggle("active", i===n-1));
    document.querySelectorAll(".section").forEach((s,i)=> s.classList.toggle("active", i===n-1));
  }
  // Auto-scroll chat to bottom
  var chatBox = document.getElementById('chatMessages');
  if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
  
  // If chat param exists, show tab 3
  if(window.location.search.includes('chat=')) showTab(3);
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
