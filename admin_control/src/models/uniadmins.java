package models;

/**
 * University Administrators Model
 * 
 * Represents administrative users who manage the system.
 * Links employees to faculties with authentication credentials.
 * 
 * Purpose:
 * - Stores admin user credentials
 * - Associates employees with faculties
 * - Role-based access control
 * 
 * Database Table: uniadmins
 * 
 * Related Tables:
 * - employee: Employee personal information (linked by emp_id)
 * - university: Faculty/branch information (linked by faculty_id)
 * 
 * Authentication:
 * - Password should be hashed (BCrypt recommended)
 * - emp_id used as username/identifier
 * - Role determines permission level
 * 
 * Role System:
 * - Different roles may have different permissions
 * - Role-based access to features and data
 * 
 * Security:
 * - Passwords must be hashed before storage
 * - Faculty-based data isolation
 * - Session management in web interface
 * 
 * Use Cases:
 * - Faculty administrator login
 * - User authentication and authorization
 * - Access control to admin panel
 */
public class uniadmins {
private int emp_id;         // Employee ID (links to employee table)
private int faculty_id;     // Faculty/branch assignment
private String password;    // Hashed password (BCrypt)
private int role;           // Role/permission level

public uniadmins(int emp_id, int faculty_id, String password, int role) {
    this.emp_id = emp_id;
    this.faculty_id = faculty_id;
    this.password = password;
    this.role = role;
}
public int getEmp_id() {
    return emp_id;
}
public void setEmp_id(int emp_id) {
    this.emp_id = emp_id;

}
public int getFaculty_id() {
    return faculty_id;
}
public void setFaculty_id(int faculty_id) {
    this.faculty_id = faculty_id;}
public String getPassword() {
    return password;
}
public void setPassword(String password) {
    this.password = password;}
public int getRole() {
    return role;
}
public void setRole(int role) {
    this.role = role;
}

}