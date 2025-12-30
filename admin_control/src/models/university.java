package models;

/**
 * University Faculty/College Data Model
 * 
 * Represents university faculties/colleges/branches in the system.
 * Maps to database table: university
 * 
 * Database Schema:
 * - faculty_id: int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY
 * - faculty_name: varchar(100) NOT NULL
 * - faculty_number: int(11) NOT NULL (branch/campus number)
 * - UNIQUE KEY: (faculty_name, faculty_number) for uniqueness constraint
 * 
 * Purpose:
 * - Identifies different faculties/colleges within the university
 * - Links to administrators, courses, students, and other entities
 * - Branch number distinguishes multiple campuses of same faculty
 */
public class university {
    private int faculty_id;         // Unique faculty identifier (AUTO_INCREMENT)
    private String faculty_name;    // Faculty/college name (max 100 chars)
    private int faculty_number;     // Branch/campus number for multi-campus faculties

    /**
     * Constructor to create a university faculty instance.
     * @param faculty_id Unique faculty identifier
     * @param faculty_name Name of the faculty
     * @param faculty_number Administrative contact number
     */
    public university(int faculty_id, String faculty_name, int faculty_number) {
        this.faculty_id = faculty_id;
        this.faculty_name = faculty_name;
        this.faculty_number = faculty_number;
    }
    
    public int getFaculty_id() {
        return faculty_id;
    }

    public void setFaculty_id(int faculty_id) {
        this.faculty_id = faculty_id;
    }

    public String getFaculty_name() {
        return faculty_name;
    }

    public void setFaculty_name(String faculty_name) {
        this.faculty_name = faculty_name;
    }

    public int getFaculty_number() {
        return faculty_number;
    }

    public void setFaculty_number(int faculty_number) {
        this.faculty_number = faculty_number;
    }
}
