package models;

/**
 * Enrollment Data Model
 * 
 * Represents a student's enrollment in a specific course offering.
 * This class links students to course sections through the to_enrol table.
 * 
 * Database Table: enrollment
 * Fields:
 * - enrol_id: Primary key, unique enrollment identifier
 * - student_id: Foreign key to students table
 * - to_enrol_id: Foreign key to to_enrol table (course offering)
 * - active: Boolean flag indicating if enrollment is current/active
 */
public class enrollment {
    // Unique enrollment identifier (primary key)
    private int enrol_id;
    
    // Student who is enrolled (foreign key to students table)
    private int student_id;
    
    // Course offering reference (foreign key to to_enrol table)
    private int to_enrol_id;
    
    // Active status flag (true if currently enrolled, false if dropped/completed)
    private boolean active;
    
    /**
     * Constructor for enrollment object
     * @param enrol_id Unique enrollment identifier
     * @param student_id ID of enrolled student
     * @param to_enrol_id ID of course offering
     * @param active Whether enrollment is currently active
     */
    public enrollment(int enrol_id, int student_id, int to_enrol_id, boolean active) {
    this.enrol_id = enrol_id;
    this.student_id = student_id;
    this.to_enrol_id = to_enrol_id;
    this.active = active;
}
public int getEnrol_id() {
    return enrol_id;}
public int getStudent_id() {
    return student_id;}
public int getTo_enrol_id() {
    return to_enrol_id;}
public boolean isActive() {
    return active;}
public void setActive(boolean active) {
    this.active = active;}
}
