package resources;

import connect_to_database.con_db;
import java.time.Year;
import java.sql.ResultSet;
import java.util.ArrayList;

import models.*;

/**
 * Statistics Resource Manager
 * 
 * Provides methods to retrieve various statistical counts for the dashboard.
 * All statistics are faculty-filtered to show only data relevant to the admin's faculty.
 * Uses threaded con_db for database queries.
 * 
 * Available Statistics:
 * - getTotalStudents(): Count of students in paid_students table
 * - getTotalDoctors(): Count of distinct professors teaching this year
 * - getTotalCourses(): Count of distinct courses offered this year
 * - getTotalMajors(): Count of distinct majors with courses this year
 * - getTotalFaculties(): Count of all faculties (not filtered)
 * 
 * All counts (except faculties) are filtered by:
 * - faculty_id: Only data for the admin's faculty
 * - year: Current year (where applicable)
 * 
 * Used by: StatisticsPage, DashboardFrame for displaying metrics
 */
public class getstats {
    private university thisuniversity;
    private con_db connectdb ;
    public getstats(university thisuniversity) {
        this.thisuniversity = thisuniversity;
    }
    public int getTotalStudents() {
       
        String query = "SELECT COUNT(*) AS total FROM paid_students WHERE faculty_id = " + thisuniversity.getFaculty_id();
        connectdb = new con_db(query);
        int totalStudents = 0;
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalStudents = connectdb.getResult().getInt("total");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalStudents;
    }
    public int getTotalDoctors(){
        String query="SELECT COUNT(DISTINCT dr_id) AS professor_count FROM to_enrol WHERE faculty_id= "+this.thisuniversity.getFaculty_id()+" AND year = "+Year.now().getValue();
        connectdb = new con_db(query);
        int totalDoctors = 0;
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalDoctors = connectdb.getResult().getInt("professor_count");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalDoctors;
    }
    public int getTotalCourses(){
        String query="SELECT COUNT(DISTINCT m.course_id) AS courses_count FROM major_course_semester m JOIN to_enrol t ON m.mcs_id = t.mcs_id WHERE t.faculty_id="+this.thisuniversity.getFaculty_id()+" AND t.year = "+Year.now().getValue();
        connectdb = new con_db(query);
        int totalCourses = 0;   
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalCourses = connectdb.getResult().getInt("courses_count");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalCourses;
    }
    public int getTotalMajors(){
        String query="SELECT COUNT(DISTINCT m.major_id) AS major_count FROM major_course_semester m JOIN to_enrol t ON m.mcs_id = t.mcs_id WHERE t.faculty_id="+this.thisuniversity.getFaculty_id()+" AND t.year = "+Year.now().getValue();
        connectdb = new con_db(query);
        int totalMajors = 0;
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalMajors = connectdb.getResult().getInt("major_count");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalMajors;
    }
    public int getTotalFaculties(){
        String query="SELECT COUNT(faculty_id) AS faculty_count FROM university ";
        connectdb = new con_db(query);
        int totalFaculties = 0;
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalFaculties = connectdb.getResult().getInt("faculty_count");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalFaculties;
    }
    public int getTotalMails(){
        String query="SELECT COUNT(mail_id) AS mail_count FROM mails WHERE related_faculties=0 OR related_faculties="+this.thisuniversity.getFaculty_id();
        connectdb = new con_db(query);
        int totalMails = 0;
        try {
            connectdb.start();
            connectdb.join();
            if (connectdb.getResult().next()) {
                totalMails = connectdb.getResult().getInt("mail_count");
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        return totalMails;
    }
    public ArrayList<mails> getMails(){
        String query="SELECT * FROM mails WHERE related_faculties=0 OR related_faculties="+this.thisuniversity.getFaculty_id()+" ORDER BY created_at DESC";
        System.out.println("Executing mails query: " + query);
        connectdb = new con_db(query);
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
