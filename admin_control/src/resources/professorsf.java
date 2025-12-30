package resources;

import connect_to_database.con_db;
import java.sql.ResultSet;
import java.util.ArrayList;

import org.mindrot.jbcrypt.BCrypt;

import models.*;

/**
 * Professor Resource Manager
 * 
 * Manages all database operations for professors/doctors including:
 * - Fetching professor lists
 * - Adding new professors with encrypted passwords
 * - Updating professor information and status
 * 
 * Uses BCrypt for password hashing with PHP compatibility.
 */
public class professorsf {
    // Current university context
    private university thisuniversity;
    // Database connection handler
    private con_db connectdb;

    /**
     * Constructor initializes the professor resource manager.
     * @param thisuniversity The current university context
     */
    public professorsf(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }

    /**
     * Retrieves all professors from the database.
     * Note: Returns all professors as doctors table doesn't have faculty_id.
     * @return ArrayList of doctor objects representing professors
     */
    public ArrayList<doctors> getAllProfessors() {
        ArrayList<doctors> professorsList = new ArrayList<>();
        // Query fetches all professors ordered by name
        String query = "SELECT dr_id, first_name, last_name, email, phone, status, fixed " +
                       "FROM doctors " +
                       "ORDER BY last_name, first_name";
    
        System.out.println("Executing professors query: " + query);
        connectdb = new con_db(query);
        try {
            connectdb.start();
            connectdb.join();
            ResultSet rs = connectdb.getResult();
            while (rs.next()) {
                int drId = rs.getInt("dr_id");
                String firstName = rs.getString("first_name");
                String lastName = rs.getString("last_name");
                String email = rs.getString("email");
                String phone = rs.getString("phone");
                String status = rs.getString("status");
                boolean fixed = rs.getBoolean("fixed");
                
                doctors professor = new doctors(drId, firstName, lastName, email, phone, status, fixed);
                professorsList.add(professor);
            }
            System.out.println("Loaded " + professorsList.size() + " professors");
        } catch (Exception e) {
            System.out.println("Error loading professors: " + e.getMessage());
            e.printStackTrace();
        }
        return professorsList;
    }

    /**
     * Adds a new professor to the database with encrypted password.
     * Password is hashed using BCrypt and converted to PHP-compatible format.
     * 
     * @param drId Unique doctor identifier
     * @param firstName Professor's first name
     * @param lastName Professor's last name
     * @param email Email address
     * @param phone Contact phone number
     * @param password Plain text password (will be hashed)
     * @param facultyId Faculty the professor belongs to
     */
    public void addProfessor(int drId, String firstName, String lastName, String email, String phone, String password, int facultyId) {
        // Generate BCrypt hash with cost factor 12
        String salt = BCrypt.gensalt(12);
        String hash = BCrypt.hashpw(password, salt);
        // Convert to PHP-compatible format ($2y$ instead of $2a$)
        String phpCompatibleHash = hash.replaceFirst("^\\$2a\\$", "\\$2y\\$");
        try {
            String professorQuery = "INSERT INTO doctors (dr_id, first_name, last_name, email, phone, password, status, fixed, faculty_id, created_at) " +
                                    "VALUES (" + drId + ", '" + firstName + "', '" + lastName + "', '" + email + "', '" + phone + "', '" + phpCompatibleHash + "', 'Active', 0, " + facultyId + ", NOW())";
            System.out.println("Executing professor query: " + professorQuery);
            connectdb = new con_db(professorQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Professor added successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in addProfessor: " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Updates an existing professor's information.
     * 
     * @param drId Doctor ID to update
     * @param firstName Updated first name
     * @param lastName Updated last name
     * @param email Updated email address
     * @param phone Updated phone number
     * @param status Updated employment status
     * @param fixed Whether the position is permanent
     */
    public void updateProfessor(int drId, String firstName, String lastName, String email, String phone, String status, boolean fixed) {
        try {
            String updateQuery = "UPDATE doctors SET " +
                                 "first_name = '" + firstName + "', " +
                                 "last_name = '" + lastName + "', " +
                                 "email = '" + email + "', " +
                                 "phone = '" + phone + "', " +
                                 "status = '" + status + "', " +
                                 "fixed = " + (fixed ? 1 : 0) + " " +
                                 "WHERE dr_id = " + drId;
            System.out.println("Executing update query: " + updateQuery);
            connectdb = new con_db(updateQuery);
            connectdb.start();
            connectdb.join();
            
            System.out.println("Professor updated successfully!");
            
        } catch (Exception e) {
            System.out.println("ERROR in updateProfessor: " + e.getMessage());
            e.printStackTrace();
        }
    }
}
