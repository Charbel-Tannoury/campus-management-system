package resources;

import connect_to_database.con_db;

import java.sql.ResultSet;
import java.util.ArrayList;

import models.*;

/**
 * Course Resource Manager
 * 
 * Handles all database operations related to courses including:
 * - Fetching course lists for a specific faculty
 * - Adding new courses with major and semester associations
 * - Managing course-major-semester relationships
 * 
 * All queries are faculty-specific based on the current university context.
 */
public class coursesf {
    // Current university context for filtering courses by faculty
    private university thisuniversity;
    // Database connection handler
    private con_db connectdb;
    public coursesf(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }
    public ArrayList<courses> getAllCourses() {
        ArrayList<courses> coursesList = new ArrayList<>();
        String query = "SELECT DISTINCT c.course_id, c.course_name, c.course_details " +
                       "FROM major_course_semester mcs " +
                       "JOIN to_enrol te ON mcs.mcs_id = te.mcs_id " +
                       "JOIN courses c ON mcs.course_id = c.course_id " +
                       "WHERE te.faculty_id = " + this.thisuniversity.getFaculty_id();
    
        connectdb = new con_db(query);
        try {
            connectdb.start();
            connectdb.join();
            ResultSet rs = connectdb.getResult();
            while (rs.next()) {
                String courseId = rs.getString("course_id");
                String courseName = rs.getString("course_name");
                String courseDetails = rs.getString("course_details");
                courses course = new courses(courseId, courseName, courseDetails);
                coursesList.add(course);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return coursesList;
    }
    
    /**
     * Adds a new course to the database with all necessary associations.
     * Process:
     * 1. Insert course into courses table
     * 2. Link course to major and semester in major_course_semester table
     * 3. Create enrollment offering in to_enrol table
     * 
     * @param courseId Unique course identifier
     * @param courseName Course name/title
     * @param courseDetails Course description
     * @param majorId Major/program the course belongs to
     * @param semester Semester when course is offered
     */
    public void addcourses(String courseId, String courseName, String courseDetails, String majorId, String semester){
  
        try {
            // Step 1: Insert new course into courses table
            String courseQuery = "INSERT INTO courses (course_id, course_name, course_details) VALUES ('" + courseId + "', '" + courseName + "', '" + courseDetails + "')";
            System.out.println("Executing course query: " + courseQuery);
            connectdb = new con_db(courseQuery);
            connectdb.start();
            connectdb.join();
            
            // Step 2: Link course to major and semester with default credits
            int credits = 3;  // Default credit hours
            String mcsQuery = "INSERT INTO major_course_semester (major_id, course_id, semester_id, credits) VALUES ('" + majorId + "', '" + courseId + "', '" + semester + "', " + credits + ")";
            System.out.println("Executing major_course_semester query: " + mcsQuery);
            connectdb = new con_db(mcsQuery);
            connectdb.start();
            connectdb.join();
            
            // Step 3: Retrieve the generated mcs_id for creating enrollment offering
            String getMcsIdQuery = "SELECT mcs_id FROM major_course_semester WHERE major_id = '" + majorId + "' AND course_id = '" + courseId + "' AND semester_id = '" + semester + "' ORDER BY mcs_id DESC LIMIT 1";
            System.out.println("Executing get mcs_id query: " + getMcsIdQuery);
            connectdb = new con_db(getMcsIdQuery);
            connectdb.start();
            connectdb.join();
            
            ResultSet rs = connectdb.getResult();
            if (rs != null && rs.next()) {
                int mcsId = rs.getInt("mcs_id");
                System.out.println("Got mcs_id: " + mcsId);
                
                // Step 4: Create enrollment offering for the current year
                String currentYear = String.valueOf(java.time.Year.now().getValue());
                int defaultDrId = 1234;  // Default doctor ID placeholder
                
                String toEnrolQuery = "INSERT INTO to_enrol (faculty_id, dr_id, mcs_id, year) VALUES (" + this.thisuniversity.getFaculty_id() + ", " + defaultDrId + ", " + mcsId + ", '" + currentYear + "')";
                System.out.println("Executing to_enrol query: " + toEnrolQuery);
                connectdb = new con_db(toEnrolQuery);
                connectdb.start();
                connectdb.join();
                
                System.out.println("Course added successfully!");
            } else {
                System.out.println("ERROR: Could not retrieve mcs_id after insertion!");
            }
            
        } catch (Exception e) {
            System.out.println("ERROR in addcourses: " + e.getMessage());
            e.printStackTrace();
        }

    }
}
