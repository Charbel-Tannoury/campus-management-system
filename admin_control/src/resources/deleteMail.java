package resources;
import connect_to_database.con_db;
import java.sql.*;

/**
 * Mail Deletion Resource Manager
 * 
 * Handles deletion of mail/announcement messages from the database.
 * Uses direct JDBC connection instead of threaded con_db approach.
 * 
 * Features:
 * - Simple DELETE operation by mail_id
 * - try-with-resources for automatic connection cleanup
 */
public class deleteMail {

    
    /**
     * Deletes a mail message by its ID
     * @param mail_id ID of the mail message to delete
     * @return true if deletion successful (rows affected > 0), false otherwise
     */
    public static boolean deleteMailById(int mail_id) {
        String query = "DELETE FROM mails WHERE mail_id = ?";
        
        try (Connection conn =new con_db(query).getConnection();
             PreparedStatement pstmt = conn.prepareStatement(query)) {
            
            pstmt.setInt(1, mail_id);
            int rowsAffected = pstmt.executeUpdate();
            
            return rowsAffected > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }
}
