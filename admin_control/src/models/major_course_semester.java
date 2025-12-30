package models;

/**
 * Major-Course-Semester Association Model
 * 
 * Represents the many-to-many relationship between majors, courses, and semesters.
 * Defines which courses are offered in which semesters for which majors,
 * along with credit hour information.
 * 
 * Purpose:
 * - Links courses to majors and semesters
 * - Defines credit hours for each course offering
 * - Central to course enrollment and curriculum management
 * 
 * Database Table: major_course_semester
 * 
 * Related Tables:
 * - majors: The academic major/program
 * - courses: The course being offered
 * - semester: The semester when course is offered
 * - to_enrol: Links this course offering to professors and enrollment
 * 
 * Business Logic:
 * - A course can be offered in multiple semesters
 * - A course can be required for multiple majors
 * - Credit hours can vary by major/semester
 * 
 * Use Cases:
 * - Course catalog generation
 * - Enrollment eligibility checking
 * - Degree requirement tracking
 * - Credit hour calculations
 */
public class major_course_semester {
private int mcs_id;         // MCS association ID (primary key)
private int major_id;       // Academic major/program
private int course_id;      // Course identifier
private int semester_id;    // Semester identifier
private int credits;        // Credit hours for this course
public major_course_semester(int mcs_id, int major_id, int course_id, int semester_id, int credits) {
    this.mcs_id = mcs_id;
    this.major_id = major_id;
    this.course_id = course_id;
    this.semester_id = semester_id;
    this.credits = credits;
}
public int getMcs_id() {
    return mcs_id;
}
public int getMajor_id() {
    return major_id;
}
public int getCourse_id() {
    return course_id;
}
public int getSemester_id() {
    return semester_id;
}
public int getCredits() {
    return credits;
}
public void setCredits(int credits) {
    this.credits = credits;
}
public void setSemester_id(int semester_id) {
    this.semester_id = semester_id;
}
public void setCourse_id(int course_id) {
    this.course_id = course_id;
}
public void setMajor_id(int major_id) {
    this.major_id = major_id;
}
}
