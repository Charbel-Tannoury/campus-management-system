<?php
/**
 * Database Connection Configuration
 * 
 * This file establishes the MySQL database connection for the Campus Management System.
 * It's included by all PHP files that need database access.
 * 
 * Connection Parameters:
 * - Host: localhost (MySQL server location)
 * - User: root (database user)
 * - Database: campus-management-system (campus management database)
 * - Port: 3306 (default MySQL port)
 */

// Database connection parameters
$host = 'localhost';          // MySQL server hostname
$user = 'root';               // Database username
$password = '';               // Database password (empty for local development)
$database = 'campus-management-system';         // Database name
$port = 3306;                 // MySQL port

// Establish database connection
$con = mysqli_connect($host, $user, $password, $database, $port);

// Check if connection was successful (optional - uncomment for debugging)
// if (!$con) {
//     die("Connection failed: " . mysqli_connect_error());
// }
?>
