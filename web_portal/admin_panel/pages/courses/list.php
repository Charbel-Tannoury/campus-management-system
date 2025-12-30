<?php
/**
 * Courses List Management Page
 * 
 * Displays all courses for a specific faculty with management actions.
 * Features:
 * - Table view of all courses
 * - Faculty-filtered course list
 * - Delete functionality (POST request)
 * - Edit course links
 * - Add new course button
 * - Displays course details, major, credits, professor
 * - Session-based access control
 * - Status messages (success/error)
 * 
 * Table Columns:
 * - # (row number)
 * - Major ID
 * - Course Code
 * - Course Name
 * - Details (description)
 * - Credits
 * - Actions (Edit/Delete buttons)
 * 
 * Query:
 * - Joins courses, major_course_semester, to_enrol, doctors
 * - Filters by session faculty_id
 * - Shows professor first and last name
 * 
 * Delete Functionality:
 * - Uses prepared statement for security
 * - Deletes from courses table (cascade should handle relations)
 * - Redirect with status parameter
 * - Validates course_id input
 * 
 * Navigation:
 * - "Add New Course" button links to add.php
 * - Edit icon links to edit.php?id=[course_id]
 * - Delete button submits form
 * 
 * Security:
 * - Session validation
 * - Faculty-based access control
 * - Prepared statement for deletion
 */

session_start();
require_once '../../../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
if (isset($_POST['delete_course_id'])) {
    $course_id = isset($_POST['delete_course_id']) ? trim((string)$_POST['delete_course_id']) : '';

    if ($course_id === '') {
        // Missing/invalid input
        header("Location: list.php?status=error");
        exit;
    }

    // Use procedural mysqli with basic error handling
    $stmt = mysqli_prepare($con, "DELETE FROM courses WHERE course_id = ?");
    if ($stmt === false) {
        header("Location: list.php?status=error");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $course_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: list.php?status=" . ($ok ? "success" : "error"));
    exit;
}
$query1="SELECT c.course_id, c.course_name, c.course_details,m.major_id,  m.credits, d.first_name, d.last_name FROM courses c join major_course_semester m on c.course_id=m.course_id join to_enrol t on m.mcs_id=t.mcs_id join doctors d on t.dr_id=d.dr_id where t.faculty_id={$_SESSION['Faculty']}";
$result = mysqli_query($con, $query1);

$page_title = "Courses List";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>

<div class="page-content">
    <div class="page-header">
        <h1>Courses List</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>All Courses</h3>
            <a href="add.php" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add New Course
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="courseTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Major</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Details</th>
                            <th>Credits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)):
                        $i++;
                    ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['major_id'] ?></td>
                            <td><?= $row['course_id'] ?></td>
                            <td><?= $row['course_name'] ?></td>
                            <td><?= $row['course_details'] ?></td>
                            <td><?= $row['credits'] ?></td>
                            <td>                                <button
                                    class="btn btn-sm btn-info"
                                    data-modal-target="viewCourseModal"
                                    onclick="viewCourse(this)"
                                    data-major="<?= $row['major_id'] ?>"
                                    data-code="<?= $row['course_id'] ?>"
                                    data-name="<?= $row['course_name'] ?>"
                                    data-details="<?= $row['course_details'] ?>"
                                    data-doctor="<?= $row['first_name'].' '.$row['last_name'] ?>"
                                    data-credits="<?= $row['credits'] ?>"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                    <a href='edit.php?id="<?= $row['course_id'] ?>"' class='btn btn-sm btn-warning' title='Edit'><i class='fas fa-edit'></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</main>

    <!-- View Course Modal -->
    <div class="modal" id="viewCourseModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>View Course</h3>
                <button class="modal-close" data-modal-close>&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Major:</label>
                    <p id="modalMajor" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Course Code:</label>
                    <p id="modalCode" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Course Name:</label>
                    <p id="modalName" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Details:</label>
                    <p id="modalDetails" style="padding: 15px; background: #f7fafc; border-radius: 8px; margin-top: 5px; line-height: 1.8;"></p>
                </div>
                <div class="form-group">
                    <label>Doctor:</label>
                    <p id="modalDoctor" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
                <div class="form-group">
                    <label>Credits:</label>
                    <p id="modalCredits" style="padding: 12px; background: #f7fafc; border-radius: 8px; margin-top: 5px;"></p>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline" data-modal-close>Close</button>
            </div>
        </div>
    </div>

<script src="../../assets/js/main.js"></script>
<script>
    initTableSearch('searchCourse', 'courseTable');
    function viewCourse(btn) {
        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '';
        };

        setText('modalMajor', btn.dataset.major);
        setText('modalCode', btn.dataset.code);
        setText('modalName', btn.dataset.name);
        setText('modalDoctor', btn.dataset.doctor);
        setText('modalCredits', btn.dataset.credits);

        const details = btn.dataset.details || '';
        const detEl = document.getElementById('modalDetails');
        if (detEl) {
            detEl.textContent = '';
            const parts = details.split('\n');
            for (let i = 0; i < parts.length; i++) {
                detEl.appendChild(document.createTextNode(parts[i]));
                if (i < parts.length - 1) detEl.appendChild(document.createElement('br'));
            }
        }

        const targetId = btn.dataset.modalTarget || 'viewCourseModal';
        const modal = document.getElementById(targetId);
        if (modal) {
            modal.classList.add('show');
        }
    }

    document.querySelectorAll('[data-modal-close]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
            }
        });
    });

    const modal = document.getElementById('viewCourseModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    }
          
</script>

<?php include '../../includes/footer.php'; ?>