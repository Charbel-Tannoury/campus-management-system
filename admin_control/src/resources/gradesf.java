package resources;

import connect_to_database.con_db;
import java.sql.ResultSet;
import java.util.ArrayList;
import models.*;

/**
 * Grade Resource Manager
 * 
 * Manages all database operations for student grades including:
 * - Fetching grades with student and course information
 * - Calculating total scores from different assessment components
 * - Updating individual grade records
 * 
 * All queries are faculty-specific based on the current university context.
 */
public class gradesf {
    // Current university context for filtering grades by faculty
    private university thisuniversity;
    // Database connection handler
    private con_db connectdb;

    /**
     * Constructor initializes the grade resource manager.
     * @param thisuniversity The current university context
     */
    public gradesf(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }

    /**
     * Retrieves all grades for students in the current faculty.
     * Returns comprehensive grade information including:
     * - Student ID and name
     * - Course name
     * - Individual assessment scores (project, midterm, finals)
     * - Calculated total score
     * 
     * @return ArrayList of Object arrays containing grade data
     */
    public ArrayList<Object[]> getAllGrades() {
        ArrayList<Object[]> gradesList = new ArrayList<>();
        // Complex join query to get complete grade information
        String query = "SELECT g.grade_id, s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, " +
                       "c.course_name, g.project_grade, g.mid_grade, g.first_final, g.second_final, " +
                       "(COALESCE(g.project_grade, 0) + COALESCE(g.mid_grade, 0) + COALESCE(g.first_final, 0) + COALESCE(g.second_final, 0)) as total " +
                       "FROM grades g " +
                       "JOIN enrollment e ON g.enrol_id = e.enrol_id " +
                       "JOIN students s ON e.student_id = s.student_id " +
                       "JOIN to_enrol te ON e.to_enrol_id = te.to_enrol_id " +
                       "JOIN major_course_semester mcs ON te.mcs_id = mcs.mcs_id " +
                       "JOIN courses c ON mcs.course_id = c.course_id " +
                       "WHERE te.faculty_id = " + this.thisuniversity.getFaculty_id() + " " +
                       "ORDER BY s.student_id, c.course_name";
    
        System.out.println("Executing grades query: " + query);
        connectdb = new con_db(query);
        try {
            connectdb.start();
            connectdb.join();
            ResultSet rs = connectdb.getResult();
            while (rs.next()) {
                int gradeId = rs.getInt("grade_id");
                int studentId = rs.getInt("student_id");
                String studentName = rs.getString("student_name");
                String courseName = rs.getString("course_name");
                int projectGrade = rs.getInt("project_grade");
                int midGrade = rs.getInt("mid_grade");
                int finalGrade = rs.getInt("first_final");
                int secondFinal = rs.getInt("second_final");
                int total = rs.getInt("total");
                
                Object[] row = {
                    gradeId,
                    studentId,
                    studentName,
                    courseName,
                    projectGrade,
                    midGrade,
                    finalGrade,
                    secondFinal,
                    total
                };
                gradesList.add(row);
            }
            System.out.println("Loaded " + gradesList.size() + " grades");
        } catch (Exception e) {
            System.out.println("Error loading grades: " + e.getMessage());
            e.printStackTrace();
        }
        return gradesList;
    }

    /**
     * Updates grade scores for a specific grade record.
     * 
     * @param gradeId Unique identifier for the grade record
     * @param projectGrade Updated project score
     * @param midGrade Updated midterm exam score
     * @param finalGrade Updated first final exam score
     * @param secondFinal Updated second final exam score (retake)
     */
    public void updateGrade(int gradeId, int projectGrade, int midGrade, int finalGrade, int secondFinal) {
        try {
            String updateQuery = "UPDATE grades SET " +
                                 "project_grade = " + projectGrade + ", " +
                                 "mid_grade = " + midGrade + ", " +
                                 "final_final = " + finalGrade + ", " +
                                 "second_final = " + secondFinal + " " +
                                 "WHERE grade_id = " + gradeId;
            System.out.println("Executing update query: " + updateQuery);
            connectdb = new con_db(updateQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Grade updated successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in updateGrade: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
