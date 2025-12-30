<?php
/**
 * Student News & Announcements Page
 * 
 * Displays all faculty news and announcements relevant to students.
 * Features:
 * - Lists all messages where receivers = 1 (students) or 0 (all)
 * - Shows message content, creation date, and faculty source
 * - Ordered by creation date (oldest first - ASC)
 * - Clean card-based layout
 * - XSS protection via htmlspecialchars
 * - Displays student email in header
 * - Auto-updated badge indicator
 * 
 * Query Logic:
 * - Joins mails table with university table
 * - Filters by receivers field (1=students, 0=all users)
 * - Orders by created_at ASC (oldest first)
 * - Shows faculty name for each message source
 * 
 * Display:
 * - Each message in a separate card/item
 * - Date formatted and displayed
 * - Faculty name attribution
 * - Message content with XSS protection
 * 
 * Use Case: Student portal home page or dedicated news section
 */

session_start();
require_once '../connection.php';
if(!isset($_SESSION['id'])) {
  header("location: ../login_logout/login.php"); exit();
}else{$student_id = $_SESSION['id'];}


$query = "SELECT m.mail_info, m.created_at, f.faculty_name FROM mails m JOIN university f ON m.faculty_id = f.faculty_id WHERE m.receivers=1 or m.receivers=0 ORDER BY m.created_at ASC";
$result = mysqli_query($con, $query);

$row = mysqli_fetch_array($result);
$arraymail = [];
$query1 = "SELECT email FROM students WHERE student_id=$student_id";
$result1 = mysqli_query($con, $query1);

$row1 = mysqli_fetch_array($result1);
for($i=0; $i<mysqli_num_rows($result); $i++){
  $arraymail[$i][0] = $row['mail_info'];
  $arraymail[$i][1] = $row['created_at'];
  $arraymail[$i][2] = $row['faculty_name'];
  $row = mysqli_fetch_array($result);
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Portal - News</title>
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
      <div class="top-left">
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <div>
          <div style="font-weight:1000;color:#fff;line-height:1.1;"><?php echo $row1["email"]  ?></div>
          <div style="font-size:12px;color:#cbd5e1;">Faculty News & Announcements</div>
        </div>
      </div>

    </header>

    <div class="content">

      <div class="header">
        <div>
          <h1 class="title">Latest News</h1>
          <div class="subtitle">Announcements & updates.</div>
        </div>
        <div class="pill">Updated automatically</div>
      </div>

      <div class="grid">

        <section class="card">
          <div class="card-title">
            <span>Announcements</span>
            <span class="tag">News</span>
          </div>

          <?php
            if($result && count($arraymail) > 0){
              for($i = count($arraymail)-1; $i >= 0; $i--){
                $info = htmlspecialchars($arraymail[$i][0] ?? '', ENT_QUOTES, 'UTF-8');

                $dateRaw = $arraymail[$i][1] ?? '';
                $dateNoMicro = explode('.', $dateRaw)[0];
                $dateToMinutes = substr($dateNoMicro, 0, 16);
                $dateSafe = htmlspecialchars($dateToMinutes, ENT_QUOTES, 'UTF-8');


                echo '<div class="news-item">';
                echo '  <div class="dot"></div>';
                echo '  <div class="news-body">';
                echo '    <p class="news-title">'.$info.'</p>';
                echo '    <p class="news-meta">Posted: '.$dateSafe.'</p>';
                echo '    <p class="news-meta">From: '.$arraymail[$i][2].'</p>';
                echo '  </div>';
                echo '</div>';
              }
            } else {
              echo '<div style="padding:12px;border:1px dashed var(--line);border-radius:16px;background:var(--soft);color:var(--muted);font-size:13px;">';
              echo 'No news found yet.';
              echo '</div>';
            }
          ?>

        </section>

        <aside>
          <div class="card">
            <div class="card-title">
              <span>Quick Info</span>
              <span class="tag">Overview</span>
            </div>

            <div class="stat">
              <div class="k">Total Announcements</div>
              <div class="v"><?php echo count($arraymail); ?></div>
            </div>

            <div class="stat">
              <div class="k">Tip</div>
              <div style="font-size:13px;color:#334155;line-height:1.6;">
                Check the news daily for exam schedules, deadlines, and announcements.
              </div>
            </div>
          </div>
        </aside>

      </div>
    </div>

  </main>
</div>

<!-- Google Translate Script -->
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
