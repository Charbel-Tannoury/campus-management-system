<?php
/**
 * View Mail Details JSON Endpoint
 * 
 * AJAX endpoint returning mail message details as JSON.
 * Features:
 * - Returns mail information in JSON format
 * - Faculty-based access control
 * - Formats receiver and faculty scope text
 * - Displays creation timestamp
 * - Used by modal/popup to show full message
 * 
 * URL Parameters:
 * - id: Mail ID (required, numeric)
 * 
 * JSON Response:
 * Success:
 * {
 *   "success": true,
 *   "mail_id": 123,
 *   "faculty_name": "Faculty of Sciences",
 *   "receivers_text": "Students Only",
 *   "related_faculties_text": "Just this University",
 *   "mail_title": "Subject",
 *   "mail_info": "Message content",
 *   "created_at": "2024-01-15 10:30:00"
 * }
 * 
 * Error:
 * { "success": false, "message": "Error description" }
 * 
 * Receivers Mapping:
 * - 0: All Users
 * - 1: Students Only
 * - 2: Professors Only
 * 
 * Related Faculties Mapping:
 * - 0: Just this University
 * - Numeric: All Faculties
 * 
 * Security:
 * - Session validation
 * - Faculty-based filtering (related_faculties check)
 * - Numeric ID validation
 * 
 * Use Case: AJAX call from list.php to display full message
 */

session_start();
require_once '../../../connection.php';

header('Content-Type: application/json');

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid mail ID']);
    exit;
}

$mail_id = intval($_GET['id']);
$fac = $_SESSION['Faculty'];

// Fetch mail data
$query = "SELECT m.*, u.faculty_name 
          FROM mails m 
          LEFT JOIN university u ON m.faculty_id = u.faculty_id 
          WHERE m.mail_id = '$mail_id' AND (m.related_faculties = '$fac' OR m.related_faculties = 0)";

$result = mysqli_query($con, $query);

if($row = mysqli_fetch_assoc($result)) {
    // Format receivers text
    if($row['receivers'] == 0) {
        $receivers_text = "All Users";
    } else if($row['receivers'] == 1) {
        $receivers_text = "Students Only";
    } else if($row['receivers'] == 2) {
        $receivers_text = "Professors Only";
    } else {
        $receivers_text = "Unknown";
    }
    
    // Format related faculties text
    if($row['related_faculties'] == 0) {
        $related_text = "Just this University";
    } else {
        $related_text = "All Faculties";
    }
    
    echo json_encode([
        'success' => true,
        'mail_id' => $row['mail_id'],
        'faculty_name' => $row['faculty_name'],
        'receivers_text' => $receivers_text,
        'related_faculties_text' => $related_text,
        'mail_title' => $row['mail_title'],
        'mail_info' => $row['mail_info'],
        'created_at' => explode(".", $row['created_at'])[0]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mail not found']);
}

mysqli_close($con);
?>
