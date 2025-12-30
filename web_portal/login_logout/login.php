<?php
/**
 * University Login Page
 * 
 * Central authentication portal for all user types.
 * Features:
 * - Dual login sections (Admin/Student)
 * - Responsive card-based layout
 * - Red/brown gradient design (CCTJ branding)
 * - University logo display
 * - Registration links for students
 * - Form submission to logincheck.php
 * - Session initialization
 * - Professional modern UI
 * 
 * Login Sections:
 * 1. Employee/Admin Login:
 *    - Email field
 *    - Password field
 *    - Submit to logincheck.php
 * 
 * 2. Student Login:
 *    - Email field
 *    - Password field
 *    - Registration link
 *    - Payment link
 *    - Submit to logincheck.php
 * 
 * Design:
 * - Split card layout (admin left, student right)
 * - Red gradient header with logo
 * - Light gray form areas
 * - Hover effects on inputs and buttons
 * - Responsive design
 * 
 * Processing:
 * - Both forms submit to logincheck.php
 * - logincheck.php determines user type and redirects
 * - Session created on successful authentication
 * 
 * Use Case: Entry point for entire university portal system
 */

session_start();
require_once '..\connection.php';
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Login</title>
        <title>My Website</title>
        
    <link rel="icon" href="../admin_panel/assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
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
            display: flex;
            min-height: 400px;
        }
        .login-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-right: 1px solid #eee;
        }
        .login-section:last-child {
            border-right: none;
        }
        .login-section h2 {
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
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            background: #fff;
        }
        .checkbox-group {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
            cursor: pointer;
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }
        .checkbox-group label {
            margin: 0;
            color: #4a5568;
            font-weight: normal;
            cursor: pointer;
        }
        .error-message {
            background: #fed7e2;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c53030;
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
        }
        .btn:hover {
            background: #5a6fd6;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-password a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .Faculty-type {
            display: none;
            margin-top: 15px;
        }
        .Faculty-type.show {
            display: block;
        }
        .Faculty-type select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .Faculty-type select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            .login-section {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            .login-section:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../admin_panel/assets/img/logo.png" alt="CCTJ - University Logo" class="logo" style="width: 130px; height: 130px; object-fit: contain; margin-bottom: 15px; border-radius: 50%; background: white; padding: 5px;">
            <h1>CCTJ - University</h1>
        </div>
        
        <div class="content">
            <div class="login-section" style="border-right: none; max-width: 450px; margin: 0 auto;">
                <h2>Login</h2>  
                <form method="POST" action="logincheck.php">
                    <div class="form-group">
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" name="user_id" placeholder="Enter your ID" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_employee" name="is_employee" value="1">
                        <label for="is_employee">I am an Employee</label>
                    </div>
                    <div class="form-group Faculty-type" id="Faculty_type">
                        <label for="Faculty_type">Facultie</label>
                       <?php
                            $query = "SELECT * FROM university";
                            $result = mysqli_query($con, $query);
                            $row=mysqli_fetch_assoc($result);
                            echo '<select name="Faculty" id="Faculty" required>';
                            for ($i=0; $i<mysqli_num_rows($result); $i++) {
                                echo '<option value="'.$row['faculty_id'].'">'.$row['faculty_name'].' - Branch '.$row['faculty_number'].'</option>';
                                $row=mysqli_fetch_assoc($result);
                            }
                            echo '</select>';
                            mysqli_close($con);
                        ?>
                    </div>
                    <button type="submit" name="login" class="btn">Login</button>
                    <?php
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'] ;
                        unset($_SESSION['msg']);
                    }
                    ?>
                    <div class="forgot-password">
                        <a  onclick="willbeadded()">Forgot Password?</a>
                    </div>
                    <div class="forgot-password">
                        <a href="../register/register_student.php">New Student? Register Here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Toggle employee type dropdown
        document.getElementById('is_employee').addEventListener('change', function() {
            var facultyType = document.getElementById('Faculty_type');
            if (this.checked) {
                facultyType.classList.add('show');
            } else {
                facultyType.classList.remove('show');
            }
        });
    </script>
    
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement(
        {pageLanguage: 'en'},
        'google_translate_element'
      );
    }
    function willbeadded(){
        alert("This feature will be added in the future versions.");
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
</body>
</html>