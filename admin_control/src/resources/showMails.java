package resources;

import connect_to_database.con_db;

import java.sql.ResultSet;
import java.util.ArrayList;

import models.*;

/**
 * Mail Retrieval Resource Manager
 * 
 * Fetches mail/announcement messages relevant to a specific faculty.
 * Uses threaded con_db for asynchronous database access.
 * 
 * Filtering Logic:
 * - Shows mails where related_faculties = 0 (broadcast to all faculties)
 * - OR mails specifically sent to this faculty (related_faculties = faculty_id)
 * - Results ordered by created_at DESC (newest first)
 * 
 * Features:
 * - Faculty-based filtering
 * - Thread-based query execution
 * - Returns ArrayList of mails objects
 * - Debug logging to console
 * - Exception handling with error messages
 */
public class showMails {
    // University/faculty context for filtering mails
    private university thisuniversity;
    private con_db connectdb;
    
    /**
     * Constructor
     * @param thisuniversity University object containing faculty_id for filtering
     */
    public showMails(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }
    /**
     * Retrieves all mails relevant to this faculty
     * @return ArrayList of mails objects, ordered by creation date (newest first)
     *         Returns empty list if error occurs or no mails found
     */
    public ArrayList<mails> getMails(){
        // Query filters for: broadcast messages (0) OR faculty-specific messages
        String query="SELECT * FROM mails WHERE related_faculties=0 OR related_faculties="+this.thisuniversity.getFaculty_id()+" ORDER BY created_at DESC";
        System.out.println("Executing mails query: " + query);
        con_db connectdb = new con_db(query);
        ArrayList<mails> mailList = new ArrayList<>();
        try {
            connectdb.start();
            connectdb.join();
            ResultSet rs = connectdb.getResult();
            while (rs.next()) {
                mails mail = new mails(
                    rs.getInt("mail_id"),
                    rs.getInt("faculty_id"),
                    rs.getInt("receivers"),
                    rs.getString("priority"),
                    rs.getInt("related_faculties"),
                    rs.getString("mail_title"),
                    rs.getString("mail_info"),
                    rs.getString("attached_doc"),
                    rs.getString("created_at")
                );
                mailList.add(mail);
            }
            System.out.println("Loaded " + mailList.size() + " mails");
        } catch (Exception e) {
            System.out.println("Error loading mails: " + e.getMessage());
            e.printStackTrace();
        }
        return mailList;
    }
}
