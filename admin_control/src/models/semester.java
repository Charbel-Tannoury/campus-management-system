package models;

/**
 * Semester Data Model
 * 
 * Represents an academic semester/term in the university system.
 * Semester IDs typically represent sequential terms (e.g., semester1,semester2,semester3).
 * 
 * Database Table: semester
 * Fields:
 * - semester_id: Primary key, unique identifier for the semester
 * 
 * Usage:
 * - Used in major_course_semester associations
 * - Referenced in course scheduling
 * - Displayed in semester selection dropdowns
 * 
 * Note: This is a minimal model with only the ID field.
 */
public class semester {
private int semester_id;

public semester(int semester_id) {
    this.semester_id = semester_id;
}
public int getSemester_id() {
    return semester_id;
}
}
