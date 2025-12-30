package resources;

import connect_to_database.con_db;

import java.sql.ResultSet;
import java.util.ArrayList;

import models.*;

/**
 * Student Resource Manager
 * 
 * Handles all database operations related to students including:
 * - Fetching student lists
 * - Adding new students
 * - Enrolling students in courses
 * - Updating student information
 * 
 * All queries are faculty-specific based on the current university context.
 */
public class studentsf {
    // Current university context for filtering students by faculty
    private university thisuniversity;
    // Database connection handler
    private con_db connectdb;

    /**
     * Constructor initializes the student resource manager with university context.
     * @param thisuniversity The current university instance for filtering students
     */
    public studentsf(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }

    /**
     * Retrieves all students enrolled in courses for the current faculty.
     * Joins students, enrollment, and to_enrol tables to filter by faculty.
     * 
     * @return ArrayList of student objects belonging to this faculty
     */
    public ArrayList<students> getAllStudents() {
        ArrayList<students> studentsList = new ArrayList<>();
        // Query joins students with enrollment data filtered by faculty
        String query = "SELECT DISTINCT s.student_id, s.first_name, s.last_name, s.email, s.verification, s.verified_at, s.created_at " +
                       "FROM students s " +
                       "JOIN enrollment e ON s.student_id = e.student_id " +
                       "JOIN to_enrol te ON e.to_enrol_id = te.to_enrol_id " +
                       "WHERE te.faculty_id = " + this.thisuniversity.getFaculty_id();
    
        System.out.println("Executing students query: " + query);
        connectdb = new con_db(query);
        try {
            // Execute query in separate thread and wait for completion
            connectdb.start();
            connectdb.join();
            
            // Process result set and create student objects
            ResultSet rs = connectdb.getResult();
            while (rs.next()) {
                // Extract student data from result set
                int studentId = rs.getInt("student_id");
                String firstName = rs.getString("first_name");
                String lastName = rs.getString("last_name");
                String email = rs.getString("email");
                boolean verification = rs.getBoolean("verification");
                String verifiedAt = rs.getString("verified_at");
                String createdAt = rs.getString("created_at");
                
                // Create student object and add to list
                students student = new students(studentId, firstName, lastName, email, verification, verifiedAt, createdAt);
                studentsList.add(student);
            }
            System.out.println("Loaded " + studentsList.size() + " students");
        } catch (Exception e) {
            System.out.println("Error loading students: " + e.getMessage());
            e.printStackTrace();
        }
        return studentsList;
    }

    /**
     * Adds a new student to the database.
     * Student is created with unverified status (verification = 0).
     * 
     * @param studentId Unique student identifier
     * @param firstName Student's first name
     * @param lastName Student's last name
     * @param email Student's email address
     * @param password Hashed password for authentication
     */
    public void addStudent(int studentId, String firstName, String lastName, String email, String password) {
        try {
            // Insert new student with current timestamp
            String studentQuery = "INSERT INTO students (student_id, first_name, last_name, email, password, verification, created_at) " +
                                  "VALUES (" + studentId + ", '" + firstName + "', '" + lastName + "', '" + email + "', '" + password + "', 0, NOW())";
            System.out.println("Executing student query: " + studentQuery);
            connectdb = new con_db(studentQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Student added successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in addStudent: " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Enrolls an existing student in a course offering.
     * Sets enrollment as active (active = 1).
     * 
     * @param studentId ID of the student to enroll
     * @param toEnrolId ID of the course offering to enroll in
     */
    public void enrollStudent(int studentId, int toEnrolId) {
        try {
            String enrollQuery = "INSERT INTO enrollment (student_id, to_enrol_id, active) VALUES (" + studentId + ", " + toEnrolId + ", 1)";
            System.out.println("Executing enrollment query: " + enrollQuery);
            connectdb = new con_db(enrollQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Student enrolled successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in enrollStudent: " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Updates an existing student's information in the database.
     * 
     * @param studentId ID of the student to update
     * @param firstName New first name
     * @param lastName New last name
     * @param email New email address
     * @param verification Verification status (0 = unverified, 1 = verified)
     */
    public void updateStudent(int studentId, String firstName, String lastName, String email, int verification) {
        try {
            String updateQuery = "UPDATE students SET " +
                                 "first_name = '" + firstName + "', " +
                                 "last_name = '" + lastName + "', " +
                                 "email = '" + email + "', " +
                                 "verification = " + verification + " " +
                                 "WHERE student_id = " + studentId;
            System.out.println("Executing update query: " + updateQuery);
            connectdb = new con_db(updateQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Student updated successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in updateStudent: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
