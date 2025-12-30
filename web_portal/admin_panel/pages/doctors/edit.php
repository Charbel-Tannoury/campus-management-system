<?php
/**
 * Edit Professor Information Page
 * 
 * Admin interface for modifying professor/doctor records.
 * Features:
 * - Dual-purpose form (load data + process update)
 * - Email uniqueness validation
 * - Status and fixed employment updates
 * - Error and success messages
 * - Read-only fields (ID, names)
 * - Faculty access control
 * 
 * Form Workflow:
 * 1. POST with 'edit' button: Loads professor data into form
 * 2. POST with 'submit' button: Processes update
 * 
 * Editable Fields:
 * - Email (with uniqueness check)
 * - Phone (optional)
 * - Status (active/inactive)
 * - Fixed employment flag
 * 
 * Non-Editable:
 * - Doctor ID (readonly)
 * - First Name (not shown in form)
 * - Last Name (not shown in form)
 * 
 * Validation:
 * - Email uniqueness (checks against other doctors)
 * - Required field validation
 * - Status options (dropdown)
 * 
 * Error Handling:
 * - Displays email duplicate errors
 * - Shows SQL update errors
 * - Success message on completion
 * - Redirects if no valid POST data
 * 
 * Security:
 * - Session validation
 * - Faculty access control
 * - Should use prepared statements (currently vulnerable)
 * 
 * Note: First/Last name are readonly - cannot be changed
 */

session_start();
include '../../../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
$page_title = "Edit Professor";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>
<?php 
$errorMsg = "";
$successReq = "";
    if(isset($_POST['submit'])){
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $doctor_id = $_POST['doctor_id'];
        $status = $_POST['status'];
        $fixed = $_POST['fixed'];
        $test_sql = "SELECT * FROM doctors WHERE dr_id!='$doctor_id'AND email='$email' LIMIT 1";
        $result=mysqli_query($con,$test_sql);
        if ($result->num_rows > 0) {  
            $errorMsg = "Email already exists for another professor.";
        }else{
        $sql = "UPDATE doctors SET email='$email',".(($phone!="") ?" phone='$phone',":"")." status='$status' WHERE dr_id='$doctor_id'";
       if ($con->query($sql) === TRUE) {
            $successReq = "Professor edited successfully";
        } else {
            $errorMsg .= "Error updating record: " . $con->error;
        }}

        $con->close();
    }elseif(isset($_POST['edit'])){
        $sql = "SELECT * FROM doctors WHERE dr_id=".$_POST['edit']." LIMIT 1";
        $result=mysqli_query($con,$sql);
        if ($result->num_rows > 0) {  
            $row= $result->fetch_assoc();
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $doctor_id = $row['dr_id'];
        $fixed = $row['fixed'];
        $status = $row['status'];
        } else {
            header('Location: list.php');
            exit;
        }
    }else{
        header('Location: list.php');
        exit;
    }
    ?>
    <?php if($errorMsg): ?><div class="error-box"><?php echo $errorMsg; ?></div><?php endif; ?>
    <?php if($successReq): ?><div class="success-box"><?php echo $successReq; ?></div><?php endif; ?>

        <div class="page-content">
            <div class="page-header">
                <h1>Edit Professor</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a><span>/</span>
                    <a href="list.php">Professors</a><span>/</span>
                    <span>Edit Professor</span>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Personal Information</h3></div>
                <div class="card-body">
                    <form id="editDoctorForm" method="post" data-validate>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Doctor ID <span style="color: #f56565;">*</span></label>
                                <input type="number" class="form-control" name="doctor_id" readonly value="<?php echo $doctor_id; ?>">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>First Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="first_name" readonly placeholder="First Name" value="<?php echo $first_name; ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="last_name" readonly placeholder="Last Name" value="<?php echo $last_name; ?>">
                            </div>
                        </div>
                        
                        
                        

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Email <span style="color: #f56565;">*</span></label>
                                <input type="email" class="form-control" name="email" required placeholder="doctor.email@example.com" value="<?php echo $email; ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="xxx-xxxxxxxx" value="<?php echo $phone; ?>">
                            </div>
                        </div>
                        <h3 style="margin: 30px 0 20px; padding-top: 20px; border-top: 2px solid #e2e8f0;">Additional Information</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="active"<?php if(isset($status) && $status == 'active') echo ' selected'; ?>>Active</option>
                                    <option value="inactive"<?php if(isset($status) && $status == 'inactive') echo ' selected'; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fixed Status</label>
                                <?php echo ($fixed == 1 ? '<span class="badge-status active">Fixed</span>' : '<span class="badge-status inactive">Not Fixed</span>'); ?>
                                <input type="hidden" name="fixed" value="<?php echo $fixed; ?>">
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary" name="submit" value="1">
                                <i class="fas fa-save"></i> Update Professor Data
                            </button>
                            <a href="list.php" class="btn btn-outline">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
 
<?php include '../../includes/footer.php'; ?>
