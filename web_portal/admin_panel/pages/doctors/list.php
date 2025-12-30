<?php
/**
 * Professors/Doctors List Management Page
 * 
 * Displays all professors with management actions.
 * Features:
 * - Table view of all professors (all faculties, not filtered)
 * - Avatar generation via ui-avatars.com
 * - Status display (Active/Inactive)
 * - Fixed employment indicator
 * - Edit button for each professor
 * - Add new professor button
 * - Clean card-based layout
 * 
 * Table Columns:
 * - # (Doctor ID)
 * - Photo (auto-generated avatar)
 * - Full Name (first + last)
 * - Email
 * - Status (active/inactive)
 * - Fixed (employment status badge)
 * - Actions (Edit button)
 * 
 * Query:
 * - Fetches ALL doctors (no faculty filter)
 * - Should ideally filter by faculty for access control
 * 
 * Edit Functionality:
 * - Form submits to edit.php with POST
 * - Passes doctor ID via 'edit' parameter
 * 
 * Display Features:
 * - Responsive table layout
 * - Color-coded status badges
 * - Breadcrumb navigation
 * 
 * Security Note:
 * - Session validation present
 * - No faculty filtering on query (shows all professors)
 * - Should add faculty-based access control
 */

session_start();
include '../../../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
$fac=$_SESSION['Faculty'];
$query = "SELECT * FROM doctors";
$result = mysqli_query($con, $query);

$page_title = "Professors List";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>
        <div class="page-content">
            <div class="page-header"><h1>Professors List</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><span>Professors</span><span>/</span><span>Professors List</span></div></div>
            <div class="card">
                <div class="card-header"><h3>All Professors</h3>
                    <div style="display: flex; gap: 10px;">
                        <a href="add.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i>Add New Professor</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="doctorTable">
                            <thead><tr><th>#</th><th>Photo</th><th>Full Name</th><th>Email</th><th>Status</th><th>Fixed</th><th>Actions</th></tr></thead>
                            <tbody>
                                <form method='post' action='edit.php' style='display:inline;'>
                         <?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['dr_id']."</td>";
        echo "<td><img src='https://ui-avatars.com/api/?name=".$row['first_name']."+".$row['last_name']."' style='width:40px; height:40px; border-radius:50%;'></td>";
        echo "<td>".$row['first_name']." ".$row['last_name']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".($row['status'] == 'active' ? 'Active' : 'Inactive')."</td>";
        echo "<td>".($row['fixed'] == 1 ? '<span class="badge-status active">Fixed</span>' : '<span class="badge-status inactive">Not Fixed</span>')."</td>";
        echo "<td>
                <a class='btn btn-sm btn-warning'><button type='submit' name='edit' value='".$row['dr_id']."' class='btn btn-sm btn-edit' style='margin: 0; padding: 0; border: none; background: none;'><i class='fas fa-edit'></i></button></a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No professors found.</td></tr>";
}
?>
                        </form>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
   
<?php include '../../includes/footer.php'; ?>
