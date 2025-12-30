package pages_ui;

import util.*;
import resources.studentsf;
import models.*;
import javax.swing.*;
import java.awt.*;

/**
 * Add Student Page UI Component
 * 
 * Provides a form interface for administrators to add new students to the system.
 * Features:
 * - Student ID, name, and email input fields
 * - Password creation with confirmation field
 * - Form validation (required fields, password matching)
 * - Save and cancel buttons
 * - Breadcrumb navigation display
 * - Responsive card-based layout
 * - Integration with studentsf resource manager for database operations
 * 
 * Layout Structure:
 * - Page header with title and breadcrumb
 * - White card container with form fields
 * - Two-column grid layout for fields
 * - Action buttons (Save/Cancel) at bottom
 * 
 * Required Fields:
 * - Student ID
 * - First Name
 * - Last Name
 * - Email
 * - Password (with confirmation)
 */
public class AddStudentPage extends JPanel {
    
    // Form input fields for student information
    private JTextField studentIdField;
    private JTextField firstNameField;
    private JTextField lastNameField;
    private JTextField emailField;
    private JPasswordField passwordField;
    private JPasswordField confirmPasswordField;
    
    // University context for faculty filtering
    private university thisUniversity;

    /**
     * Constructor for AddStudentPage
     * @param thisUniversity University object containing faculty information
     */
    public AddStudentPage(university thisUniversity) {
        this.thisUniversity = thisUniversity;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader("Add New Student", new String[] { "Home", "Students", "Add Student" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));

        
        content.add(createFormCard());

        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
    }

    private JPanel createPageHeader(String title, String[] breadcrumbItems) {
        JPanel header = new JPanel(new BorderLayout());
        header.setBackground(AppColors.BG_PAGE);
        header.setMaximumSize(new Dimension(Integer.MAX_VALUE, 60));

        JLabel titleLabel = new JLabel(title);
        titleLabel.setFont(AppFonts.TITLE);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JPanel breadcrumb = new JPanel(new FlowLayout(FlowLayout.LEFT, 8, 0));
        breadcrumb.setBackground(AppColors.BG_PAGE);

        for (int i = 0; i < breadcrumbItems.length; i++) {
            JLabel item = new JLabel(breadcrumbItems[i]);
            item.setFont(AppFonts.BODY);
            item.setForeground(i == breadcrumbItems.length - 1 ? AppColors.TEXT_MUTED : AppColors.SECONDARY);
            breadcrumb.add(item);

            if (i < breadcrumbItems.length - 1) {
                JLabel sep = new JLabel("/");
                sep.setFont(AppFonts.BODY);
                sep.setForeground(AppColors.TEXT_MUTED);
                breadcrumb.add(sep);
            }
        }

        JPanel headerContent = new JPanel();
        headerContent.setLayout(new BoxLayout(headerContent, BoxLayout.Y_AXIS));
        headerContent.setBackground(AppColors.BG_PAGE);
        headerContent.add(titleLabel);
        headerContent.add(Box.createRigidArea(new Dimension(0, 8)));
        headerContent.add(breadcrumb);

        header.add(headerContent, BorderLayout.WEST);
        return header;
    }

    private JPanel createFormCard() {
        JPanel card = new JPanel();
        card.setLayout(new BoxLayout(card, BoxLayout.Y_AXIS));
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(new Color(0, 0, 0, 10)),
                BorderFactory.createEmptyBorder(30, 30, 30, 30)));
        card.setMaximumSize(new Dimension(900, Integer.MAX_VALUE));

        
        JLabel formTitle = new JLabel("Student Information");
        formTitle.setFont(AppFonts.SUBHEADING);
        formTitle.setForeground(AppColors.TEXT_DARK);
        formTitle.setAlignmentX(Component.LEFT_ALIGNMENT);
        card.add(formTitle);
        card.add(Box.createRigidArea(new Dimension(0, 25)));

        
        JPanel formBody = new JPanel();
        formBody.setLayout(new BoxLayout(formBody, BoxLayout.Y_AXIS));
        formBody.setBackground(Color.WHITE);
        formBody.setAlignmentX(Component.LEFT_ALIGNMENT);

        
        JPanel row1 = new JPanel(new GridLayout(1, 2, 20, 0));
        row1.setBackground(Color.WHITE);
        row1.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel studentIdPanel = createFormField("Student ID *", "12345");
        studentIdField = (JTextField) studentIdPanel.getComponent(1);
        row1.add(studentIdPanel);
        JPanel firstNamePanel = createFormField("First Name *", "John");
        firstNameField = (JTextField) firstNamePanel.getComponent(1);
        row1.add(firstNamePanel);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row2 = new JPanel(new GridLayout(1, 2, 20, 0));
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel lastNamePanel = createFormField("Last Name *", "Doe");
        lastNameField = (JTextField) lastNamePanel.getComponent(1);
        row2.add(lastNamePanel);
        JPanel emailPanel = createFormField("Email *", "student@email.com");
        emailField = (JTextField) emailPanel.getComponent(1);
        row2.add(emailPanel);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel passwordPanel = createPasswordField("Password *");
        passwordField = (JPasswordField) passwordPanel.getComponent(1);
        row3.add(passwordPanel);
        JPanel confirmPasswordPanel = createPasswordField("Confirm Password *");
        confirmPasswordField = (JPasswordField) confirmPasswordPanel.getComponent(1);
        row3.add(confirmPasswordPanel);
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Save Student");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        
        saveBtn.addActionListener(e -> {
            String studentIdStr = studentIdField.getText().trim();
            String firstName = firstNameField.getText().trim();
            String lastName = lastNameField.getText().trim();
            String email = emailField.getText().trim();
            String password = new String(passwordField.getPassword());
            String confirmPassword = new String(confirmPasswordField.getPassword());
            
            if (studentIdStr.isEmpty() || firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || password.isEmpty()) {
                JOptionPane.showMessageDialog(this, 
                    "All fields are required!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            if (!password.equals(confirmPassword)) {
                JOptionPane.showMessageDialog(this, 
                    "Passwords do not match!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            try {
                int studentId = Integer.parseInt(studentIdStr);
                studentsf studentManager = new studentsf(thisUniversity);
                studentManager.addStudent(studentId, firstName, lastName, email, password);
                
                JOptionPane.showMessageDialog(this, 
                    "Student '" + firstName + " " + lastName + "' added successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                studentIdField.setText("");
                firstNameField.setText("");
                lastNameField.setText("");
                emailField.setText("");
                passwordField.setText("");
                confirmPasswordField.setText("");
                
            } catch (NumberFormatException ex) {
                JOptionPane.showMessageDialog(this, 
                    "Student ID must be a number!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error adding student: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });

        buttonsPanel.add(saveBtn);
        buttonsPanel.add(cancelBtn);
        formBody.add(buttonsPanel);

        card.add(formBody);
        return card;
    }

    private JPanel createFormField(String label, String placeholder) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel lbl = new JLabel(label);
        lbl.setFont(AppFonts.BODY);
        lbl.setForeground(AppColors.TEXT_DARK);
        lbl.setAlignmentX(Component.LEFT_ALIGNMENT);

        JTextField field = UIHelper.createTextField(placeholder);
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        field.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(lbl);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }

    private JPanel createPasswordField(String label) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel lbl = new JLabel(label);
        lbl.setFont(AppFonts.BODY);
        lbl.setForeground(AppColors.TEXT_DARK);
        lbl.setAlignmentX(Component.LEFT_ALIGNMENT);

        JPasswordField field = new JPasswordField();
        field.setFont(AppFonts.BODY);
        field.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(AppColors.BORDER),
                BorderFactory.createEmptyBorder(12, 15, 12, 15)));
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        field.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(lbl);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }
}
