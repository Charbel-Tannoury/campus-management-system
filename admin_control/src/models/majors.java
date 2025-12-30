package models;

/**
 * Majors Data Model
 * 
 * Represents an academic major/program of study in the university system.
 * Each major is identified by a unique ID and has a descriptive name.
 * 
 * Database Table: majors
 * Fields:
 * - major_id: Primary key, unique identifier for the major
 * - major_name: Display name of the major (e.g., "Computer Science", "Business Administration")
 * 
 * Usage:
 * - Used in course-major-semester associations
 * - Referenced in student enrollment records
 * - Displayed in major selection dropdowns
 */
public class majors {

    // Unique identifier for the major (primary key)
    private int major_id;
    
    // Display name of the major program
    private String major_name;
    
    /**
     * Constructor for majors object
     * @param major_id Unique identifier for the major
     * @param major_name Name of the major program
     */
    public majors(int major_id, String major_name) {
    this.major_id = major_id;
    this.major_name = major_name;
}
public int getMajor_id() {
    return major_id;
}
public String getMajor_name() {
    return major_name;
}
public void setMajor_name(String major_name) {
    this.major_name = major_name;
}
}
