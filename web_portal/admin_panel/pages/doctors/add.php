<?php
/**
 * Add Professor Page
 * 
 * This page allows administrators to add new professors (doctors) to the system.
 * Features:
 * - Input validation (email uniqueness, password matching)
 * - Secure password hashing using PASSWORD_DEFAULT algorithm (bcrypt)
 * - Optional phone number field
 * - Professor status control (active/inactive)
 * - Form data persistence on error
 * - Success/error message display
 */

session_start();
include '../../../connection.php';

$page_title = "Add Professor";
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>
<?php 
    // Initialize message variables for user feedback
    $errorMsg = "";
    $successReq = "";
    
    // Process form submission
    if(isset($_POST['submit'])){
        // Sanitize all input to prevent SQL injection and XSS attacks
        $first_name = mysqli_real_escape_string($con, trim($_POST['first_name'] ?? ''));
        $last_name  = mysqli_real_escape_string($con, trim($_POST['last_name'] ?? ''));
        $email      = mysqli_real_escape_string($con, trim($_POST['email'] ?? ''));
        $phone      = mysqli_real_escape_string($con, trim($_POST['phone'] ?? ''));
        $password   = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $status     = mysqli_real_escape_string($con, trim($_POST['status'] ?? 'inactive'));

        // Validation 1: Check if email already exists in database
        // This prevents duplicate professor accounts
        $test_sql = "SELECT * FROM doctors WHERE email='".$email."' LIMIT 1";
        $result = mysqli_query($con, $test_sql);
        if ($result && $result->num_rows > 0) {
            $errorMsg = "Email already exists for another professor.";
        } 
        // Validation 2: Ensure password and confirmation match
        elseif ($password !== $confirm_password) {
            $errorMsg = "Passwords do not match.";
        } else {
            // Hash the password using PHP's password_hash function
            // PASSWORD_DEFAULT uses bcrypt algorithm (currently the most secure)
            // Automatically generates a random salt and includes it in the hash
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // Build SQL query dynamically to handle optional phone field
            // Only includes phone column if value is provided
            $cols = "first_name, last_name, email, password";
            $vals = "'".$first_name."', '".$last_name."', '".$email."', '".$password_hashed."'";
            if($phone !== ''){
                $cols .= ", phone";
                $vals .= ", '".$phone."'";
            }
            $cols .= ", status";
            $vals .= ", '".$status."'";

            $sql = "INSERT INTO doctors (".$cols.") VALUES (".$vals.")";

            if ($con->query($sql) === TRUE) {
                $successReq = "Professor added successfully";
                unset($first_name, $last_name, $email, $phone, $password,$confirm_password, $status);
            } else {
                $errorMsg .= "Error inserting record: " . $con->error;
            }
        }

        $con->close();
    }
    ?>
    <?php if($errorMsg): ?><div class="error-box"><?php echo $errorMsg; ?></div><?php endif; ?>
    <?php if($successReq): ?><div class="success-box"><?php echo $successReq; ?></div><?php endif; ?>
        <div class="page-content">
            <div class="page-header">
                <h1>Add New Professor</h1>
                <div class="breadcrumb">
                    <a href="../../index.php">Home</a><span>/</span>
                    <a href="list.php">Professors</a><span>/</span>
                    <span>Add Professor</span>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Personal Information</h3></div>
                <div class="card-body">
                    <form id="editDoctorForm" method="post" data-validate>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>First Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="first_name" required placeholder="First Name" value="<?php echo isset($first_name) ? $first_name : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name <span style="color: #f56565;">*</span></label>
                                <input type="text" class="form-control" name="last_name" required placeholder="Last Name" value="<?php echo isset($last_name) ? $last_name : ''; ?>">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Password <span style="color: #f56565;">*</span></label>
                                <input type="password" class="form-control" name="password" required placeholder="Password" value="<?php echo isset($password) ? $password : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password <span style="color: #f56565;">*</span></label>
                                <input type="password" class="form-control" name="confirm_password" required placeholder="Confirm Password" value="<?php echo isset($confirm_password) ? $confirm_password : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Email <span style="color: #f56565;">*</span></label>
                                <input type="email" class="form-control" name="email" required placeholder="doctor.email@example.com" value="<?php echo isset($email) ? $email : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="xxx-xxxxxxxx" value="<?php echo isset($phone) ? $phone : ''; ?>">
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
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary" name="submit" value="1">
                                <i class="fas fa-save"></i> Save Professor Data
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
