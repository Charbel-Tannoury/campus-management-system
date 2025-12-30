<?php
/**
 * Course Edit Processing Script
 * 
 * Backend processor for updating course information across multiple tables.
 * Features:
 * - Updates three related tables in one transaction
 * - Checks for duplicate course assignments
 * - Transaction management (rollback on error)
 * - Updates course details, major associations, and professor assignment
 * - Validation of unique constraint (major+course+semester)
 * 
 * Tables Updated:
 * 1. courses: course_id, course_name, course_details
 * 2. major_course_semester: major_id, course_id, credits, semester_id
 * 3. to_enrol: dr_id (professor assignment)
 * 
 * Transaction Flow:
 * 1. Begin transaction
 * 2. Find mcs_id from original course
 * 3. Check for duplicate (same major+course+semester)
 * 4. UPDATE courses table
 * 5. UPDATE major_course_semester table
 * 6. UPDATE to_enrol table
 * 7. Commit or rollback
 * 
 * POST Parameters:
 * - course_code: New course ID
 * - course_name: Course name
 * - description: Course details
 * - major: Major ID
 * - doctor: Professor ID
 * - credits: Credit hours
 * - semester: Semester ID
 * - original_course_id: Current course ID (for lookup)
 * 
 * Error Handling:
 * - Rollback on any SQL error
 * - Displays specific error messages
 * - Validates duplicate before updating
 * 
 * Note: Should use prepared statements for security
 */
require_once '../../../connection.php';

$course_code = trim($_POST['course_code']);
$course_name = trim($_POST['course_name']);
$description = trim($_POST['description']);
$major = trim($_POST['major']);
$doctor = trim($_POST['doctor']);
$credits = trim($_POST['credits']);
$semester = trim($_POST['semester']);
$original_course_id = trim($_POST['original_course_id']);

mysqli_begin_transaction($con);

$getMcs = "
    SELECT mcs_id 
    FROM major_course_semester
    WHERE course_id = '$original_course_id'
    LIMIT 1
";
$resMcs = mysqli_query($con, $getMcs);
if (!$resMcs || mysqli_num_rows($resMcs) == 0) {
    mysqli_rollback($con);
    die("Course not found in major_course_semester");
}
$rowMcs = mysqli_fetch_assoc($resMcs);
$mcs_id = $rowMcs['mcs_id'];

$check = "
    SELECT mcs_id 
    FROM major_course_semester
    WHERE major_id='$major'
      AND course_id='$course_code'
      AND semester_id='$semester'
      AND mcs_id != '$mcs_id'
    LIMIT 1
";
$resCheck = mysqli_query($con, $check);
if (mysqli_num_rows($resCheck) > 0) {
    mysqli_rollback($con);
    die("❌ This course already exists for this major in this semester.");
}


$query1 = "
    UPDATE courses
    SET course_id='$course_code',
        course_name='$course_name',
        course_details='$description'
    WHERE course_id='$original_course_id'
";
if (!mysqli_query($con, $query1)) {
    mysqli_rollback($con);
    die("Courses error: " . mysqli_error($con));
}

$query2 = "
    UPDATE major_course_semester
    SET major_id='$major',
        course_id='$course_code',
        credits='$credits',
        semester_id='$semester'
    WHERE mcs_id='$mcs_id'
";
if (!mysqli_query($con, $query2)) {
    mysqli_rollback($con);
    die("Major_course_semester error: " . mysqli_error($con));
}


$query3 = "
    UPDATE to_enrol
    SET dr_id='$doctor'
    WHERE mcs_id='$mcs_id'
";
if (!mysqli_query($con, $query3)) {
    mysqli_rollback($con);
    die("To_enrol error: " . mysqli_error($con));
}

mysqli_commit($con);
header("Location: list.php");
exit();
?>
