<?php
/**
 * Anonymous Complaints CSV Export
 * 
 * Exports all student complaints to downloadable CSV file.
 * Features:
 * - CSV format with proper headers
 * - Includes all complaint details
 * - Date-stamped filename
 * - Calculates days since submission
 * - Faculty name lookup
 * - Proper CSV encoding
 * - Direct download (no page display)
 * 
 * CSV Columns:
 * 1. ID: Complaint ID
 * 2. Faculty: Faculty name (or 'Unknown Faculty')
 * 3. Message: Complaint text
 * 4. Submitted Date: Timestamp
 * 5. Days Since Submission: Auto-calculated age
 * 
 * Output Format:
 * - Filename: student_complaints_YYYY-MM-DD.csv
 * - Content-Type: text/csv
 * - Content-Disposition: attachment (forces download)
 * 
 * Query:
 * - Joins anonymous_complaint with university table
 * - Orders by creation date (newest first)
 * - Includes all complaints (no faculty filter)
 * 
 * Data Processing:
 * - Formats dates as Y-m-d H:i:s
 * - Calculates days difference from creation to now
 * - Handles missing faculty names gracefully
 * - CSV escaping via fputcsv()
 * 
 * Use Case: Faculty administrators exporting complaints for analysis
 * 
 * Security:
 * - Session validation required
 * - All complaints visible (cross-faculty access)
 */

session_start();
require_once '../../../connection.php';

if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_complaints_' . date('Y-m-d') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['ID', 'Faculty', 'Message', 'Submitted Date', 'Days Since Submission']);

// Get complaints data
$query = "SELECT ac.ac_id, ac.faculty_id, ac.message, ac.created_at, u.faculty_name 
          FROM anonymous_complaint ac 
          LEFT JOIN university u ON ac.faculty_id = u.faculty_id 
          ORDER BY ac.created_at DESC";
$result = $con->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facultyName = $row['faculty_name'] ?: 'Unknown Faculty';
        $submittedDate = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $daysSince = date_diff(date_create($row['created_at']), date_create())->format('%a');
        
        fputcsv($output, [
            $row['ac_id'],
            $facultyName,
            $row['message'],
            $submittedDate,
            $daysSince
        ]);
    }
}

fclose($output);
exit();
?>