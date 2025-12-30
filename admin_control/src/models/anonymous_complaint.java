package models;

/**
 * Anonymous Complaint Model
 * 
 * Represents anonymous complaints submitted by students to faculty administration.
 * Provides a way for students to report issues without revealing their identity.
 * 
 * Purpose:
 * - Allows confidential reporting of problems
 * - Faculty-directed feedback mechanism
 * - No student identification stored
 * 
 * Database Table: anonymous_complaint
 * 
 * Features:
 * - One-way communication (student to faculty)
 * - No conversation thread (unlike student_statment)
 * - Timestamp tracking
 * - Faculty-specific complaints
 * 
 * Related Tables:
 * - university: The faculty receiving the complaint
 * 
 * Use Cases:
 * - Reporting harassment or discrimination
 * - Academic integrity concerns
 * - Facility/service complaints
 * - General grievances
 * 
 * Privacy:
 * - Student identity not stored
 * - Only faculty and message content recorded
 */
public class anonymous_complaint {
private int ac_id;          // Complaint ID (primary key)
private int faculty_id;     // Faculty receiving complaint
private String message;     // Complaint text content
private String created_at;  // Submission timestamp
public anonymous_complaint(int ac_id, int faculty_id, String message, String created_at) {
    this.ac_id = ac_id;
    this.faculty_id = faculty_id;
    this.message = message;
    this.created_at = created_at;
}
public int getAc_id() {
    return ac_id;
}
public int getFaculty_id() {
    return faculty_id;
}
public String getMessage() {
    return message;
}
public String getCreated_at() {
    return created_at;
}

}