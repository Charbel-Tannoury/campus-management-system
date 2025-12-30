<?php
/**
 * Student Registration Form
 * 
 * Public-facing student registration interface.
 * Features:
 * - Personal information collection (name, email, phone)
 * - Gender and birth date selection
 * - Major and semester selection from database
 * - Password with confirmation field
 * - Faculty-specific registration
 * - Modern responsive UI with gradient design
 * - CCTJ University branding
 * - Form validation (client & server-side)
 * - Red/brown color scheme
 * 
 * Form Fields:
 * - First Name, Last Name (required)
 * - Email (unique, required)
 * - Phone Number
 * - Gender (Male/Female)
 * - Birth Date
 * - Major (dropdown from majors table)
 * - Semester (dropdown from semester table)
 * - Password + Confirm Password
 * 
 * Processing:
 * - Submits to process_registration.php
 * - Creates student account + payment record
 * - Password hashed with BCrypt
 * - Redirects to login on success
 * 
 * Design:
 * - Centered card layout with logo
 * - Two-column form layout
 * - Red gradient header
 * - Light background (#f5f7fa)
 * - Professional typography
 */

session_start();
require_once '../connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - CCTJ - University</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(180deg, #b91c1c 0%, #7f1d1d 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 900px;
            background: #f5f7fa;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(180deg, #b91c1c 0%, #7f1d1d 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            display: block;
        }
        .header h1 {
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 22px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            background: #fff;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .error-message,
        .success-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .error-message {
            background: #fed7e2;
            color: #c53030;
            border-left: 4px solid #c53030;
        }
        .success-message {
            background: #c6f6d5;
            color: #2f855a;
            border-left: 4px solid #2f855a;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }
        .btn:hover {
            background: #5a6fd6;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-to-login a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="icon" href="../admin_panel/assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../admin_panel/assets/img/logo.png" alt="CCTJ - University Logo" class="logo">
            <h1>CCTJ - University</h1>
        </div>
        
        <div class="content">
            <h2>New Student Registration</h2> 
            
            <?php if(isset($_SESSION['msg'])): ?>
                <div class="<?php echo (strpos($_SESSION['msg'], 'success') !== false) ? 'success-message' : 'error-message'; ?>">
                    <?php 
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="process_registration.php">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                </div>
                
                <div class="form-group">
                    <label for="Faculty">Faculty *</label>
                    <?php
                        $query = "SELECT * FROM university ORDER BY faculty_name ASC";
                        $result = mysqli_query($con, $query);
                        echo '<select name="Faculty" id="Faculty" required onchange="filterMajors()">';
                        echo '<option value="">Select a Faculty</option>';
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="'.$row['faculty_id'].'">'.$row['faculty_name'].'</option>';
                        }
                        echo '</select>';
                    ?>
                </div>
                
                <div class="form-group">
                    <label for="major">Major *</label>
                    <?php
                        $query = "SELECT DISTINCT m.major_id, m.major_name, t.faculty_id 
                                 FROM majors m 
                                 JOIN major_course_semester mm ON m.major_id = mm.major_id 
                                 JOIN to_enrol t ON mm.mcs_id = t.mcs_id 
                                 ORDER BY m.major_name ASC";
                        $result = mysqli_query($con, $query);

                        echo '<select name="major" id="major" required disabled>';
                        echo '<option value="">First select a faculty</option>';
                        while ($dept_row = mysqli_fetch_assoc($result)) {
                            echo '<option value="'.$dept_row['major_id'].'" data-faculty="'.$dept_row['faculty_id'].'">'.$dept_row['major_name'].'</option>';
                        }
                        echo '</select>';
                        mysqli_close($con);
                    ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                </div>
                
                <button type="submit" class="btn">Register</button>
                
                <div class="back-to-login">
                    <a href="../login_logout/login.php">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function filterMajors() {
            const facultySelect = document.getElementById('Faculty');
            const majorSelect = document.getElementById('major');
            const facultyId = facultySelect.value;
            
            const allOptions = majorSelect.querySelectorAll('option[data-faculty]');
            
            if (!facultyId) {
                majorSelect.disabled = true;
                majorSelect.value = '';
                allOptions.forEach(opt => opt.style.display = 'none');
                return;
            }
            
            majorSelect.disabled = false;
            majorSelect.value = '';
            
            allOptions.forEach(function(option) {
                if (option.getAttribute('data-faculty') == facultyId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    </script>
    
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
