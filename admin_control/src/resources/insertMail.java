package resources;
import models.mails;
import connect_to_database.con_db;
import java.sql.*;

/**
 * Mail Insertion Resource Manager
 * 
 * Handles inserting new mail/announcement messages into the database.
 * Provides two insertion methods:
 * 1. insertNewMail() - Insert with individual parameters
 * 2. insertMailFromObject() - Insert from mails object
 * 
 * Features:
 * - Uses PreparedStatement for SQL injection prevention
 * - Automatically sets created_at timestamp to NOW()
 * - Returns boolean success/failure indicator
 * - Properly closes database resources
 * 
 * Database Operations:
 * - Single INSERT statement with 8 columns
 * - No threading (synchronous operation)
 * - Exception handling with console error output
 */
public class insertMail {
    
    /**
     * Inserts a new mail message into the database
     * @param faculty_id ID of faculty creating the message
     * @param priority Message priority (low, medium, high, urgent)
     * @param receivers Target audience identifier
     * @param related_faculties Which faculties should see this message
     * @param mail_title Subject line
     * @param mail_info Message body content
     * @param attached_doc Path to attached document (can be empty string)
     * @return true if insertion successful, false otherwise
     */
    public static boolean insertNewMail(int faculty_id, String priority, int receivers, int related_faculties, 
                                        String mail_title, String mail_info, String attached_doc) {
        
        // SQL INSERT with 8 columns (created_at auto-set to current timestamp)
        String query = "INSERT INTO mails (faculty_id, priority, receivers, related_faculties, mail_title, mail_info, attached_doc, created_at) " +
                      "VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        try {
            con_db connectdb = new con_db("");
            Connection conn = connectdb.getConnection();
            PreparedStatement pstmt = conn.prepareStatement(query);
            
            pstmt.setInt(1, faculty_id);
            pstmt.setString(2, priority);
            pstmt.setInt(3, receivers);
            pstmt.setInt(4, related_faculties);
            pstmt.setString(5, mail_title);
            pstmt.setString(6, mail_info);
            pstmt.setString(7, attached_doc);
            
            int rowsAffected = pstmt.executeUpdate();
            pstmt.close();
            conn.close();
            
            return rowsAffected > 0;
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }
    
    public static boolean insertMailFromObject(mails mail) {
        return insertNewMail(
            mail.getFaculty_id(),
            mail.getPriority(),
            mail.getReceivers(),
            mail.getRelated_faculties(),
            mail.getMail_title(),
            mail.getMail_info(),
            mail.getAttached_doc()
        );
    }
}

