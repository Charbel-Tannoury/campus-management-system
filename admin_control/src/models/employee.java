package models;

/**
 * Employee/Administrative Staff Data Model
 * 
 * Represents administrative staff members in the university system.
 * Maps to database table: employee
 * 
 * Database Schema:
 * - emp_id: int(10) UNSIGNED AUTO_INCREMENT starting at 123 (3-digit staff IDs)
 * - first_name: varchar(50) NOT NULL
 * - last_name: varchar(50) NOT NULL
 * - email: varchar(250) NOT NULL
 * - p_number: int(50) NOT NULL (phone number)
 * 
 * Related Tables:
 * - uniadmins: Links employees to faculties with authentication credentials
 */
public class employee {
    private int emp_id;             // Unique employee identifier (AUTO_INCREMENT from 123)
    private String first_name;      // Employee's first name (max 50 chars)
    private String last_name;       // Employee's last name (max 50 chars)
    private String email;           // Email address (max 250 chars)
    private int p_number;           // Phone number (required)

    /**
     * Constructor to create an employee instance.
     * @param emp_id Unique employee identifier
     * @param first_name Employee's first name
     * @param last_name Employee's last name
     * @param email Employee's email address
     * @param p_number Employee's phone number
     */
    public employee(int emp_id, String first_name, String last_name, String email, int p_number) {
        this.emp_id = emp_id;
        this.first_name = first_name;
        this.last_name = last_name;
        this.email = email;
        this.p_number = p_number;
    }

    public int getEmp_id() {
        return emp_id;
    }

    public void setEmp_id(int emp_id) {
        this.emp_id = emp_id;
    }

    public String getFirst_name() {
        return first_name;
    }

    public void setFirst_name(String first_name) {
        this.first_name = first_name;
    }

    public String getLast_name() {
        return last_name;
    }

    public void setLast_name(String last_name) {
        this.last_name = last_name;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
    public int getP_number() {
        return p_number;
    }
    public void setP_number(int p_number) {
        this.p_number = p_number;
    }
    
}
