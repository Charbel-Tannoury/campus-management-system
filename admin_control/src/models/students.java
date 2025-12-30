package models;

/**
 * Student Data Model
 * 
 * Represents a student in the university system.
 * Maps to database table: students
 * 
 * Database Schema:
 * - student_id: int(11) AUTO_INCREMENT starting at 12345 (5-digit student numbers)
 * - first_name: varchar(50) NOT NULL
 * - last_name: varchar(50) NOT NULL
 * - password: varchar(255) NOT NULL (BCrypt hashed)
 * - email: varchar(100) NOT NULL UNIQUE
 * - phone_num: int(30) DEFAULT NULL
 * - verification: tinyint(1) NOT NULL DEFAULT 0 (email verification status)
 * - verified_at: datetime(6) DEFAULT NULL (timestamp of verification)
 * - created_at: datetime(6) NOT NULL DEFAULT current_timestamp(6)
 */
public class students {
    private int student_id;         // Unique student identifier (AUTO_INCREMENT from 12345)
    private String fisrt_name;      // Student's first name (max 50 chars)
    private String last_name;       // Student's last name (max 50 chars)
    private String password;        // BCrypt hashed password (max 255 chars)
    private String email;           // Email address (max 100 chars, UNIQUE)
    private int phone_num;          // Contact phone number (nullable)
    private boolean verification;   // Email verification status (0=unverified, 1=verified)
    private String verified_at;     // Timestamp when email was verified (datetime(6))
    private String created_at;      // Account creation timestamp (datetime(6))

    /**
     * Constructor to create a student instance with basic information.
     * 
     * @param student_id Unique student identifier
     * @param fisrt_name Student's first name
     * @param last_name Student's last name
     * @param email Student's email address
     * @param verification Email verification status
     * @param verified_at Verification timestamp
     * @param created_at Account creation timestamp
     */
    public students(int student_id, String fisrt_name, String last_name, String email, Boolean verification,String verified_at, String created_at) {
        this.student_id = student_id;
        this.fisrt_name = fisrt_name;
        this.last_name = last_name;
        this.email = email;
        this.verification = verification;
    }

    public int getStudent_id() {
        return student_id;
    }
    public String getFisrt_name() {
        return fisrt_name;
    }

    public void setFisrt_name(String fisrt_name) {
        this.fisrt_name = fisrt_name;
    }
    public String getLast_name() {
        return last_name;
    }

    public void setLast_name(String last_name) {
        this.last_name = last_name;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }
    public String getCreated_at() {
        return created_at;
    }
    public boolean isVerification() {
        return verification;
    }
    public void setVerification(boolean verification) {
        this.verification = verification;
    }
    public int getPhone_num() {
        return phone_num;
    }
    public void setPhone_num(int phone_num) {
        this.phone_num = phone_num;
    }
    public String getVerified_at() {
        return verified_at;
    }
    public void setVerified_at(String verified_at) {
        this.verified_at = verified_at;
    }
    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }
}
