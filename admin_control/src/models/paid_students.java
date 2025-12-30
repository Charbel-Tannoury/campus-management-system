package models;

/**
 * Paid Students Data Model
 * 
 * Represents a student's payment/registration record for a specific faculty and major.
 * This table tracks tuition payment approvals that allow students to enroll in courses.
 * 
 * Database Table: paid_students
 * Fields:
 * - paid_id: Primary key, unique payment record identifier
 * - faculty_id: Faculty the student is paying to enroll in
 * - student_id: Foreign key to students table
 * - major_id: Major/program the student is enrolling in
 * - year: Academic year of the payment
 * - status: Approval status (0=pending admin approval, 1=approved)
 * - created_at: Timestamp when payment record was created
 * 
 * Workflow:
 * 1. Student submits payment request (status=0)
 * 2. Admin reviews and verifies payment
 * 3. Admin approves (status=1)
 * 4. Student can then enroll in courses for that faculty/major/year
 * 
 * Usage: Used in enrollment eligibility checks and payment verification pages
 */
public class paid_students {
private int paid_id;
private int faculty_id;
private int student_id;
private int major_id;
private int year;
private boolean status;
private int created_at;
public paid_students(int paid_id, int faculty_id, int student_id, int major_id, int year, boolean status, int created_at) {
    this.paid_id = paid_id;
    this.faculty_id = faculty_id;
    this.student_id = student_id;
    this.major_id = major_id;
    this.year = year;
    this.status = status;
    this.created_at = created_at;
}
public int getPaid_id() {
    return paid_id;}
public int getFaculty_id() {
    return faculty_id;}
public int getStudent_id() {
    return student_id;}
public int getMajor_id() {
    return major_id;}
public int getYear() {
    return year;}
public boolean isStatus() {
    return status;}
public int getCreated_at() {
    return created_at;}
}
