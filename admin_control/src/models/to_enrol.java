package models;

/**
 * To Enrol Data Model
 * 
 * Represents a course offering for a specific academic year.
 * This is the central linking table that connects:
 * - Faculties (university)
 * - Professors (doctors)
 * - Course-major-semester combinations (major_course_semester)
 * - Academic year
 * 
 * Database Table: to_enrol
 * Fields:
 * - enrol_id: Primary key (note: this is to_enrol_id in the table)
 * - faculty_id: Faculty offering the course
 * - dr_id: Professor teaching the course
 * - mcs_id: Foreign key to major_course_semester (course details)
 * - year: Academic year this course is offered
 * 
 * Purpose:
 * - Defines which courses are available each year
 * - Associates professors with specific course offerings
 * - Enables faculty-based course filtering
 * - Links to enrollment table when students register
 * 
 * Example: Computer Science 101 taught by Dr. Smith in Faculty 1 for year 2025
 */
public class to_enrol {
private int enrol_id;
private int faculty_id;
private int dr_id;
private int mcs_id;
private int year;
public to_enrol(int enrol_id, int faculty_id, int dr_id, int mcs_id, int year) {
    this.enrol_id = enrol_id;
    this.faculty_id = faculty_id;
    this.dr_id = dr_id;
    this.mcs_id = mcs_id;
    this.year = year;}
public int getEnrol_id() {
    return enrol_id;}
public int getFaculty_id() {
    return faculty_id;}
public int getDr_id() {
    return dr_id;}
public int getMcs_id() {
    return mcs_id;}
public int getYear() {
    return year;}
}