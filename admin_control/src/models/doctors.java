package models;

/**
 * Doctor/Professor Data Model
 * 
 * Represents faculty members (professors/doctors) in the university system.
 * Maps to database table: doctors
 * 
 * Database Schema:
 * - dr_id: int(11) AUTO_INCREMENT starting at 1234 (4-digit faculty IDs)
 * - first_name: varchar(50) NOT NULL
 * - last_name: varchar(50) NOT NULL
 * - phone: varchar(20) DEFAULT NULL
 * - status: varchar(20) DEFAULT 'active' (active/inactive)
 * - email: varchar(100) NOT NULL UNIQUE
 * - password: varchar(255) NOT NULL (BCrypt hashed)
 * - fixed: tinyint(1) NOT NULL DEFAULT 0 (permanent employment status)
 * - created_at: datetime(6) NOT NULL DEFAULT current_timestamp(6)
 * - fixed_at: datetime(6) DEFAULT NULL (timestamp when made permanent)
 */
public class doctors {
    private int dr_id;              // Unique doctor identifier (AUTO_INCREMENT from 1234)
    private String first_name;      // Doctor's first name (max 50 chars)
    private String last_name;       // Doctor's last name (max 50 chars)
    private String phone;           // Contact phone number (max 20 chars, nullable)
    private String status;          // Employment status: 'active' or 'inactive' (default 'active')
    private String email;           // Email address (max 100 chars, UNIQUE)
    private String password;        // BCrypt hashed password (max 255 chars)
    private Boolean fixed;          // Permanent employment flag (0=temporary, 1=fixed)
    private String created_at;      // Account creation timestamp (datetime(6))
    private String fixed_at;        // Timestamp when position became permanent (datetime(6))

    /**
     * Constructor to create a doctor instance with essential information.
     * @param dr_id Unique doctor identifier
     * @param first_name Doctor's first name
     * @param last_name Doctor's last name
     * @param email Email address
     * @param phone Contact phone number
     * @param status Employment status
     * @param fixed Whether position is permanent
     */
    public doctors(int dr_id, String first_name, String last_name, String email ,String phone, String status ,boolean fixed) {
        this.dr_id = dr_id;
        this.first_name = first_name;
        this.last_name = last_name;
        this.email = email;
        this.phone = phone;
        this.status = status;
        this.fixed = fixed;
    }

    public int getDoctor_id() {
        return dr_id;
    }

    public void setDoctor_id(int dr_id) {
        this.dr_id = dr_id;
    }

    public String getFirst_name() {
        return first_name;
    }

    public void setFirst_name(String first_name) {
        this.first_name = first_name;
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
    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }
    public String getFixed_at() {
        return fixed_at;
    }
    public void setFixed_at(String fixed_at) {
        this.fixed_at = fixed_at;
    }
    public Boolean getFixed() {
        return fixed;
    }
    public void setFixed(Boolean fixed) {
        this.fixed = fixed;
    }
    
    /**
     * Gets the encrypted password.
     * Note: For future authentication features.
     * @return Encrypted password string
     */
    public String getPassword() {
        return password;
    }
    public void setPassword(String password) {
        this.password = password;
    }
    public String getPhone() {
        return phone;
    }
    public void setPhone(String phone) {
        this.phone = phone;
    }
    public String getStatus() {
        return status;
    }
    public void setStatus(String status) {
        this.status = status;
    }
}
