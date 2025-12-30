package pages_ui;

import util.*;
import javax.swing.*;
import java.awt.*;
import resources.professorsf;
import models.university;

/**
 * Add Professor Page UI Component
 * 
 * Provides a form interface for administrators to add new professors (doctors) to the system.
 * Features:
 * - Complete professor information input fields
 * - ID, name, email, phone, password entry
 * - Status selection (active/inactive)
 * - Fixed employment checkbox (permanent vs temporary)
 * - Password confirmation field
 * - Form validation for required fields
 * - Save and cancel buttons
 * - Breadcrumb navigation
 * - Integration with professorsf resource manager (BCrypt password hashing)
 * 
 * Layout Structure:
 * - Page header with breadcrumb
 * - White card container with form
 * - Multi-row grid layout for input fields
 * - Password fields with confirmation
 * - Action buttons (Save/Cancel)
 * 
 * Required Fields:
 * - Professor ID
 * - First Name
 * - Last Name
 * - Email
 * - Password (with confirmation)
 * 
 * Optional Fields:
 * - Phone number
 * - Status (defaults to active)
 * - Fixed employment flag
 */
public class AddProfessorPage extends JPanel {

    private university thisUniversity;
    private JTextField professorIdField;
    private JTextField firstNameField;
    private JTextField lastNameField;
    private JTextField emailField;
    private JTextField phoneField;
    private JPasswordField passwordField;
    private JPasswordField confirmPasswordField;
    private JComboBox<String> statusComboBox;
    private JCheckBox fixedCheckBox;
    
    public AddProfessorPage(university thisUniversity) {
        this.thisUniversity = thisUniversity;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Add New Professor", new String[] { "Home", "Professors", "Add Professor" }));
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
        JPanel card = new JPanel(new BorderLayout());
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createLineBorder(new Color(0, 0, 0, 10)));
        card.setMaximumSize(new Dimension(Integer.MAX_VALUE, 700));

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(Color.WHITE);
        headerPanel.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER),
                BorderFactory.createEmptyBorder(20, 25, 20, 25)));

        JLabel titleLabel = new JLabel("Professor Information");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        card.add(headerPanel, BorderLayout.NORTH);

        
        JPanel formBody = new JPanel();
        formBody.setLayout(new BoxLayout(formBody, BoxLayout.Y_AXIS));
        formBody.setBackground(Color.WHITE);
        formBody.setBorder(BorderFactory.createEmptyBorder(25, 25, 25, 25));

        
        JPanel row1 = new JPanel(new GridLayout(1, 2, 20, 0));
        row1.setBackground(Color.WHITE);
        row1.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel idPanel = createFormField("Professor ID *", "Example: 101");
        JPanel emptyPanel = new JPanel();
        emptyPanel.setBackground(Color.WHITE);
        row1.add(idPanel);
        row1.add(emptyPanel);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row2 = new JPanel(new GridLayout(1, 2, 20, 0));
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel firstNamePanel = createFormField("First Name *", "Example: Ahmad");
        JPanel lastNamePanel = createFormField("Last Name *", "Example: Mansour");
        row2.add(firstNamePanel);
        row2.add(lastNamePanel);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel emailPanel = createFormField("Email *", "example@university.edu");
        JPanel phonePanel = createFormField("Phone *", "Example: 03123456");
        row3.add(emailPanel);
        row3.add(phonePanel);
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row4 = new JPanel(new GridLayout(1, 2, 20, 0));
        row4.setBackground(Color.WHITE);
        row4.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel passwordPanel = createPasswordField("Password *", "Enter password");
        JPanel confirmPasswordPanel = createPasswordField("Confirm Password *", "Re-enter password");
        row4.add(passwordPanel);
        row4.add(confirmPasswordPanel);
        formBody.add(row4);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row5 = new JPanel(new GridLayout(1, 2, 20, 0));
        row5.setBackground(Color.WHITE);
        row5.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel statusPanel = createDropdownField("Status *", new String[] { "Active", "Inactive" });
        JPanel fixedPanel = createCheckboxField("Fixed Position");
        row5.add(statusPanel);
        row5.add(fixedPanel);
        formBody.add(row5);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Save Professor");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        saveBtn.addActionListener(e -> {
            String professorId = professorIdField.getText().trim();
            String firstName = firstNameField.getText().trim();
            String lastName = lastNameField.getText().trim();
            String email = emailField.getText().trim();
            String phone = phoneField.getText().trim();
            String password = new String(passwordField.getPassword()).trim();
            String confirmPassword = new String(confirmPasswordField.getPassword()).trim();
            String status = (String) statusComboBox.getSelectedItem();
            boolean isFixed = fixedCheckBox.isSelected();
            
            
            if (professorId.isEmpty() || firstName.isEmpty() || lastName.isEmpty() || 
                email.isEmpty() || phone.isEmpty() || password.isEmpty()) {
                JOptionPane.showMessageDialog(this, 
                    "All fields marked with * are required!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            
            int drId;
            try {
                drId = Integer.parseInt(professorId);
            } catch (NumberFormatException ex) {
                JOptionPane.showMessageDialog(this, 
                    "Professor ID must be a number!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            
            if (!email.contains("@") || !email.contains(".")) {
                JOptionPane.showMessageDialog(this, 
                    "Please enter a valid email address!", 
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
            
            
            if (password.length() < 6) {
                JOptionPane.showMessageDialog(this, 
                    "Password must be at least 6 characters long!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            try {
                professorsf professorManager = new professorsf(thisUniversity);
                professorManager.addProfessor(drId, firstName, lastName, email, phone, password, thisUniversity.getFaculty_id());
                
                JOptionPane.showMessageDialog(this, 
                    "Professor 'Dr. " + firstName + " " + lastName + "' added successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                clearForm();
                
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error adding professor: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });
        
        cancelBtn.addActionListener(e -> {
            clearForm();
        });

        buttonsPanel.add(saveBtn);
        buttonsPanel.add(cancelBtn);
        formBody.add(buttonsPanel);

        card.add(formBody, BorderLayout.CENTER);

        return card;
    }

    private JPanel createFormField(String label, String placeholder) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JTextField field = UIHelper.createTextField(placeholder);
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        field.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        
        if (label.contains("Professor ID")) {
            professorIdField = field;
        } else if (label.contains("First Name")) {
            firstNameField = field;
        } else if (label.contains("Last Name")) {
            lastNameField = field;
        } else if (label.contains("Email")) {
            emailField = field;
        } else if (label.contains("Phone")) {
            phoneField = field;
        }

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }

    private JPanel createPasswordField(String label, String placeholder) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JPasswordField field = new JPasswordField();
        field.setFont(AppFonts.BODY);
        field.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(AppColors.BORDER),
                BorderFactory.createEmptyBorder(8, 12, 8, 12)));
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        field.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        if (label.contains("Confirm")) {
            confirmPasswordField = field;
        } else {
            passwordField = field;
        }

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }

    private JPanel createDropdownField(String label, String[] options) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JComboBox<String> combo = UIHelper.createComboBox(options);
        combo.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        combo.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        if (label.contains("Status")) {
            statusComboBox = combo;
        }

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(combo);

        return panel;
    }

    private JPanel createCheckboxField(String label) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        
        JLabel labelComponent = new JLabel(" ");
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        fixedCheckBox = new JCheckBox(label);
        fixedCheckBox.setFont(AppFonts.BODY);
        fixedCheckBox.setBackground(Color.WHITE);
        fixedCheckBox.setForeground(AppColors.TEXT_DARK);
        fixedCheckBox.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(fixedCheckBox);

        return panel;
    }

    private void clearForm() {
        professorIdField.setText("");
        firstNameField.setText("");
        lastNameField.setText("");
        emailField.setText("");
        phoneField.setText("");
        passwordField.setText("");
        confirmPasswordField.setText("");
        statusComboBox.setSelectedIndex(0);
        fixedCheckBox.setSelected(false);
    }
}
