<!DOCTYPE html>
<html lang="en">
<!--
/**
 * Payment Verification Dashboard
 * 
 * Admin page for verifying student tuition payments.
 * Displays all unverified payments (status=0) from the paid_students table.
 * 
 * Features:
 * - Lists students pending payment verification
 * - Shows student info, faculty, major, and year
 * - Provides verification button for each payment
 * - Submits to verify_student.php for status update
 * 
 * Table Joins:
 * - students: Student personal information
 * - paid_students: Payment records with status flag
 * - university: Faculty details
 * - majors: Major program information
 */
-->
    <body>
        <form method="post" action="verify_student.php">
        <table border='1'>
            <tr>
                <th>Faculty ID</th>
                <th>Facultie Name</th>
                <th>Facultie Number</th>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Major ID</th>
                <th>Major Name</th>
                <th>Year</th>
                <th>paid</th>
            </tr>
        <?php
        require_once '../connection.php';
        
        // Fetch all unverified payment records
        // Joins 4 tables to show complete student/payment information
        // WHERE status=0: Only shows pending payments awaiting verification
        $query = "SELECT * FROM students s 
        JOIN paid_students ps ON s.student_id = ps.student_id
        JOIN university u ON ps.faculty_id = u.faculty_id
        JOIN majors m ON ps.major_id = m.major_id
        WHERE status=0
        ";
        $result = mysqli_query($con,$query);
        
        // Display each unverified payment as a table row
        // Each row includes a verify button with the paid_id as value
        while($row=mysqli_fetch_array($result)){
            echo "<tr>
            <td>".$row['faculty_id']."</td>
            <td>".$row['faculty_name']."</td>
            <td>".$row['faculty_number']."</td>
            <td>".$row['student_id']."</td> 
            <td>".$row['first_name']."</td> 
            <td>".$row['last_name']."</td>
            <td>".$row['major_id']."</td> 
            <td>".$row['major_name']."</td> 
            <td>".$row['year']."</td>
            <td><button type='submit' name='submit' value=".$row['paid_id'].">verify</button></td>
            </tr>";
           
        }
        ?>
        </table>
        </form>
    </body>
</html>