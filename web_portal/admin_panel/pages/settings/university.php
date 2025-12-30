<?php
/**
 * University Information Settings Page
 * 
 * Admin interface for managing university/faculty information.
 * Features:
 * - Basic information form (name, email, phone, address)
 * - Social media links form
 * - Hardcoded default values (CCTJ - University)
 * - Client-side form submission (JavaScript prevents default)
 * - Visual success message (no actual database update)
 * 
 * Form Sections:
 * 
 * 1. Basic Information:
 *    - University Name
 *    - College Name (Faculty)
 *    - Branch Name
 *    - Main Email
 *    - Phone Number
 *    - Full Address
 *    - Website
 *    - Postal Code
 *    - University Description
 * 
 * 2. Social Media Information:
 *    - Facebook URL
 *    - Twitter URL
 *    - Instagram URL
 *    - LinkedIn URL
 * 
 * Current Implementation:
 * - Forms have no backend processing
 * - JavaScript prevents submission and shows success message
 * - All values are hardcoded defaults
 * - No database integration
 * 
 * Recommended Improvements:
 * - Add backend processing to save to database
 * - Load actual values from university table
 * - Add proper form validation
 * - Implement actual save functionality
 * 
 * Security:
 * - Session validation present
 * - Faculty-based access control
 * 
 * Note: This is a template/demo page without actual functionality
 */

session_start();
require_once '../../../connection.php';
if(!isset($_SESSION['Faculty']) || !isset($_SESSION['emp_id'])) {
    header('Location: ../../../login_logout/login.php');
    exit;
}
$page_title = "University Information";
$insettings = true;
include '../../includes/head.php';
?>
<?php include '../../includes/sidebar.php'; ?>
<main class="main-content">
<?php include '../../includes/header.php'; ?>
        <div class="page-content">
            <div class="page-header"><h1>University Information</h1><div class="breadcrumb"><a href="../../index.php">Home</a><span>/</span><span>Settings</span><span>/</span><span>University Information</span></div></div>
            
            <div class="card">
                <div class="card-header"><h3>Basic Information</h3></div>
                <div class="card-body">
                    <form id="universityForm" data-validate>
                        <div class="form-group"><label>University Name <span style="color: #f56565;">*</span></label><input type="text" class="form-control" name="university_name" required value="CCTJ - University"></div>
                        <div class="form-group"><label>College Name <span style="color: #f56565;">*</span></label><input type="text" class="form-control" name="college_name" required value="Faculty name"></div>
                        <div class="form-group"><label>Branch Name <span style="color: #f56565;">*</span></label><input type="text" class="form-control" name="branch_name" required value="Branch name"></div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group"><label>Main Email</label><input type="email" class="form-control" name="email" value="info@example.com"></div>
                            <div class="form-group"><label>Phone Number</label><input type="text" class="form-control" name="phone" value="+000-00-000000"></div>
                        </div>
                        <div class="form-group"><label>Full Address</label><textarea class="form-control" name="address" rows="3">City - Country</textarea></div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group"><label>Website</label><input type="url" class="form-control" name="website" value="https://www.example.com"></div>
                            <div class="form-group"><label>Postal Code</label><input type="text" class="form-control" name="postal_code" value="xx-xxxx"></div>
                        </div>
                        <div class="form-group"><label>University Description</label><textarea class="form-control" name="description" rows="4"></textarea></div>
                        <div style="display: flex; gap: 10px; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                            <button type="reset" class="btn btn-outline"><i class="fas fa-redo"></i> Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Social Media Information</h3></div>
                <div class="card-body">
                    <form>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group"><label><i class="fab fa-facebook"></i> Facebook</label><input type="url" class="form-control" name="facebook" placeholder="https://facebook.com/..."></div>
                            <div class="form-group"><label><i class="fab fa-twitter"></i> Twitter</label><input type="url" class="form-control" name="twitter" placeholder="https://twitter.com/..."></div>
                            <div class="form-group"><label><i class="fab fa-instagram"></i> Instagram</label><input type="url" class="form-control" name="instagram" placeholder="https://instagram.com/..."></div>
                            <div class="form-group"><label><i class="fab fa-linkedin"></i> LinkedIn</label><input type="url" class="form-control" name="linkedin" placeholder="https://linkedin.com/..."></div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;"><i class="fas fa-save"></i> Save Social Links</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script src="../../assets/js/main.js"></script>
    <script>document.getElementById('universityForm').addEventListener('submit', function(e) { e.preventDefault(); showMessage('Information saved successfully', 'success'); });</script>
<?php include '../../includes/footer.php'; ?>
