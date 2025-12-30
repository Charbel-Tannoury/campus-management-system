<?php
/**
 * HTML Head Include for Admin Panel
 * 
 * Common HTML head section for all admin pages.
 * Includes:
 * - Meta tags (charset, viewport)
 * - Dynamic page title with prefix
 * - Stylesheet link to main CSS
 * - Font Awesome 6.4.0 CDN
 * - Optional Chart.js library
 * - Favicon references (PNG and ICO)
 * - Responsive path handling for nested pages
 * 
 * Features:
 * - Conditional Chart.js loading (set $include_chart_js = true)
 * - Dynamic title from $page_title variable
 * - Path-aware asset loading (detects /pages/ depth)
 * - University branding (CCTJ - University)
 * 
 * Usage:
 * 1. Set $page_title before including (optional)
 * 2. Set $include_chart_js = true for dashboard pages (optional)
 * 3. Include this file at the top of every admin page
 * 
 * Example:
 * $page_title = 'Students List';
 * include '../../includes/head.php';
 * // Outputs: <title>Students List - CCTJ - University</title>
 */
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>CCTJ - University</title>
    <link rel="stylesheet" href="<?php echo strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../../' : ''; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if(isset($include_chart_js) && $include_chart_js): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
    <link rel="icon" href="<?php echo strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../../' : ''; ?>assets/img/logo.png" type="image/png">
    <link rel="shortcut icon" href="<?php echo strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../../' : ''; ?>assets/img/logo.png" type="image/x-icon">
</head>
<body>
