package pages_ui;
import javax.swing.*;
import connect_to_database.con_db;
import java.sql.*;
import java.awt.*;
import java.io.*;
import java.nio.file.*;
import org.mindrot.jbcrypt.BCrypt;
import models.*;
import java.util.*;

/**
 * Login Page Component
 * 
 * Provides the authentication interface for administrators to access the system.
 * Features:
 * - Employee ID and password authentication
 * - Faculty selection dropdown
 * - Saved credentials loading from ud.txt file (auto-login)
 * - BCrypt password verification for security
 * - Modern UI design with custom styling
 * - Session persistence option
 * 
 * Authentication Flow:
 * 1. Check for saved credentials in admin_control/src/userdata/ud.txt
 * 2. If found, auto-login with stored ID, faculty, and password
 * 3. If not found, display login form
 * 4. On login, validate credentials against database
 * 5. Use BCrypt to verify hashed passwords
 * 
 * Saved Credentials Format: id;faculty_id;password_hash
 */
public class login extends JPanel {
    // UI Components for login form
    private JLabel id_label = new JLabel("Enter Admin Id");
    private JLabel F_id = new JLabel("Choose Faculty");
    private JLabel pass_label = new JLabel("Enter Password");
    private JTextField id_text = new JTextField();
    private JPasswordField pass_text = new JPasswordField();
    private JLabel error = new JLabel("");  // Error message display
    
    // Data components
    private ArrayList<university> faculty_list = new ArrayList<university>();  // All available faculties
    private JButton login_button = new JButton("Login");
    private JFrame frame;  // Parent frame reference
    private JComboBox<String> faculty_dropdown = new JComboBox<String>();
    
    // Authentication state
    private university thisuniversity;  // Selected university/faculty
    private employee thisemployee;  // Authenticated employee
    private String pwd ="";  // Stored password (for auto-login)
    private university faculty=null;  // Selected faculty
    private String id = "0";  // Employee ID
    private Boolean loggedin = false;  // Auto-login flag
    /**
     * Constructor for login panel
     * Loads all faculties from database and checks for saved credentials
     * @param frame Parent JFrame reference
     */
    public login( JFrame frame) {
        this.frame = frame;
        
        // Load all available faculties from database
        con_db db = new con_db("SELECT * FROM university");
        try {
            db.start();
            db.join();
            ResultSet rs = db.getResult();
            while (rs != null && rs.next()) {
                faculty_list.add(new university(rs.getInt("faculty_id"), rs.getString("faculty_name"), rs.getInt("faculty_number")));
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        // Attempt to load saved credentials from ud.txt file
        // This enables auto-login functionality for returning users
        String line = null;
        try {
            System.out.println("trying to read saved user");
            System.out.println("Working dir: " + System.getProperty("user.dir"));
            
            // Construct path to saved credentials file
            Path userFile = Paths.get("admin_control","src", "userdata", "ud.txt");
            System.out.println("Looking for: " + userFile.toAbsolutePath());
            
            // Read credentials if file exists
            if (Files.exists(userFile)) {
                try (BufferedReader reader = Files.newBufferedReader(userFile)) {
                    line = reader.readLine();  // Format: id;faculty_id;password_hash
                }
            } else {
                System.out.println("File not found at: " + userFile.toAbsolutePath());
            }
        } catch(Exception e) {
            e.printStackTrace();
            System.out.println("user not saved");
            line = null;
        }
    if (line != null && !line.trim().isEmpty()) {
        String[] parts = line.split(";");
        id = parts[0].trim();
        
        for (university facultyi : faculty_list) {
            if (facultyi.getFaculty_id() == Integer.parseInt(parts[1].trim())) {
                this.faculty = facultyi;
                break;
            }
        }
        pwd = parts[2].trim();
        
        System.out.println(id+" "+faculty.getFaculty_id()+" "+pwd);
        loggedin = true;
    }else{

        
        Font labelFont = new Font("SansSerif", Font.BOLD, 18);
        Font fieldFont = new Font("SansSerif", Font.PLAIN, 16);
        Font btnFont = new Font("SansSerif", Font.BOLD, 16);
        Font errFont = new Font("SansSerif", Font.BOLD, 14);
        this.setBackground(new Color(0xF5F7FA));
        this.id_label.setFont(labelFont);
        this.F_id.setFont(labelFont);
        this.pass_label.setFont(labelFont);
        this.id_text.setFont(fieldFont);
        this.pass_text.setFont(fieldFont);
        this.faculty_dropdown.setFont(fieldFont);
        this.login_button.setFont(btnFont);
        
        this.login_button.setBackground(new Color(0x667eea));
        this.login_button.setForeground(Color.WHITE);
        this.login_button.setFocusPainted(false);
        this.login_button.setOpaque(true);
        this.login_button.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(new Color(0x667eea)),
            BorderFactory.createEmptyBorder(10, 14, 10, 14)));
        
        this.login_button.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseEntered(java.awt.event.MouseEvent evt) {
                login_button.setBackground(new Color(0x5a6fd6));
            }
            public void mouseExited(java.awt.event.MouseEvent evt) {
                login_button.setBackground(new Color(0x667eea));
            }
        });
        this.error.setFont(errFont);

        
        Dimension fieldSize = new Dimension(360, 36);
        id_text.setPreferredSize(fieldSize);
        pass_text.setPreferredSize(fieldSize);
        faculty_dropdown.setPreferredSize(fieldSize);

        id_label.setHorizontalAlignment(SwingConstants.LEFT);
        F_id.setHorizontalAlignment(SwingConstants.LEFT);
        pass_label.setHorizontalAlignment(SwingConstants.LEFT);

        faculty_dropdown.setEditable(false);

        for (university facultyi : faculty_list) {
            faculty_dropdown.addItem(facultyi.getFaculty_id() + " " + facultyi.getFaculty_name()+" - "+facultyi.getFaculty_number());
        }

        this.setLayout(new GridBagLayout());
        GridBagConstraints c = new GridBagConstraints();
        c.insets = new Insets(7, 7, 7, 7);

        
        c.gridx = 0; c.gridy = 0; c.anchor = GridBagConstraints.WEST; c.fill = GridBagConstraints.NONE; c.weightx = 0;
        this.add(id_label, c);

        
        c.gridx = 0; c.gridy = 1; c.anchor = GridBagConstraints.CENTER; c.fill = GridBagConstraints.HORIZONTAL; c.weightx = 1.0;
        this.add(id_text, c);

        
        c.gridx = 0; c.gridy = 2; c.anchor = GridBagConstraints.WEST; c.fill = GridBagConstraints.NONE; c.weightx = 0;
        this.add(F_id, c);

        
        c.gridx = 0; c.gridy = 3; c.anchor = GridBagConstraints.CENTER; c.fill = GridBagConstraints.HORIZONTAL; c.weightx = 1.0;
        this.add(faculty_dropdown, c);

        
        c.gridx = 0; c.gridy = 4; c.anchor = GridBagConstraints.WEST; c.fill = GridBagConstraints.NONE; c.weightx = 0;
        this.add(pass_label, c);

        
        c.gridx = 0; c.gridy = 5; c.anchor = GridBagConstraints.CENTER; c.fill = GridBagConstraints.HORIZONTAL; c.weightx = 1.0;
        this.add(pass_text, c);

        
        c.gridx = 0; c.gridy = 6; c.anchor = GridBagConstraints.CENTER; c.fill = GridBagConstraints.NONE; c.weightx = 0;
        error.setForeground(Color.RED);
        this.add(error, c);

        
        c.gridx = 0; c.gridy = 7; c.anchor = GridBagConstraints.CENTER; c.fill = GridBagConstraints.HORIZONTAL; c.weightx = 1.0;
        this.add(login_button, c);

        login_button.addActionListener(e -> {
            
            
            pwd = new String(pass_text.getPassword());  
            if (id_text.getText().trim().isEmpty() || pwd.length() == 0) {
                error.setForeground(Color.RED);
                error.setText("Please enter real id and password");
                return;
            }
            id = id_text.getText().trim();
            
            String sel = ((String) faculty_dropdown.getSelectedItem()).split(" ")[0];
            for(university facultyi : faculty_list){
                if(facultyi.getFaculty_id() == Integer.parseInt(sel)){
                    this.faculty = facultyi;
                    break;
                }
            }

            checkpass();
                
            
            
            
            
    });}
    }
public JButton getLoginButton() {
        return login_button;
    }
public JTextField getIdTextField() {
        return id_text;
    }
public JPasswordField getPassTextField() {
        return pass_text;
    }
public JComboBox<String> getFacultyDropdown() {
        return faculty_dropdown;
    }
public university getThisUniversity() {
        return thisuniversity;
    }
public employee getThisEmployee() {
        return thisemployee;
    }
public Boolean checkpass() {
        try {if(faculty != null){
            String safeId = String.valueOf(id).replace("'", "''");
            String sql = "SELECT * FROM uniadmins u JOIN employee e ON e.emp_id = u.emp_id WHERE u.emp_id = " + safeId + " AND u.faculty_id = " + faculty.getFaculty_id() + " LIMIT 1";
            con_db db_check = new con_db(sql);
            db_check.start();
            db_check.join();
            ResultSet rs = db_check.getResult();
            if(id.trim().isEmpty() || pwd.length() == 0 || !isIdInt()){
                error.setForeground(Color.RED);
                error.setText("Invalid ID format");
                return false;
            }
            if (rs == null || !rs.next()) {
                error.setForeground(Color.RED);
                    error.setText("wrong id or faculty");
                return false;   
            }
            String stored = rs.getString("password");
            if (stored != null && stored.startsWith("$2y$")) {
                
                stored = "$2a$" + stored.substring(4);
                
            }
            if (stored == null || !BCrypt.checkpw(pwd, stored)) {
                error.setForeground(Color.RED);
                    error.setText("incorrect password");
                return false;
            }
            thisuniversity = faculty;
            thisemployee = new employee(Integer.parseInt(rs.getString("emp_id")),
            rs.getString("first_name"),
            rs.getString("last_name"),
            rs.getString("email"),
            rs.getInt("p_number"));
            error.setForeground(new Color(0, 128, 0));
            error.setText("Login successful");
            if(!loggedin){
                try{
                    Path userFile = Paths.get("admin_control","src", "userdata", "ud.txt");
                    if (userFile.getParent() != null) Files.createDirectories(userFile.getParent());
                    try (BufferedWriter Writer = Files.newBufferedWriter(userFile)) {
                        Writer.write(thisemployee.getEmp_id() + ";" + thisuniversity.getFaculty_id() + ";" + pwd);
                    }
                    System.out.println("Saved user data to: " + userFile.toAbsolutePath());
                } catch(Exception e){
                    e.printStackTrace();
                    System.out.println("could not save user data");
                }

                frame.setVisible(false);
                
            }
            return true;
        }return false;
        } catch (Exception ex) {
            ex.printStackTrace();
            return false;
        }
    

    }
public Boolean isIdInt(){
    try{
        Integer.parseInt(id);
        return true;
    } catch(Exception e){
        return false;
    }
}}


