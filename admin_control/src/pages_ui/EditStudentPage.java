package pages_ui;

import util.*;
import resources.studentsf;
import models.*;
import javax.swing.*;
import java.awt.*;
import java.lang.reflect.Method;

/**
 * Edit Student Page UI Component
 * 
 * Provides an interface for administrators to modify existing student records.
 * Features:
 * - Pre-populated form fields with current student data
 * - Student ID (read-only, cannot be changed)
 * - Editable name, email, and status fields
 * - Status dropdown (active/inactive/suspended)
 * - Update and cancel buttons
 * - Integration with studentsf resource manager for database updates
 * - Reflection-based navigation back to student list
 * 
 * Workflow:
 * 1. loadStudentData() is called with existing student information
 * 2. Form fields are populated with current values
 * 3. Admin modifies desired fields
 * 4. Update button triggers database update
 * 5. Optional navigation back to student list page
 * 
 * Layout:
 * - Page header with breadcrumb navigation
 * - White card container with form
 * - Two-column grid for input fields
 * - Action buttons (Update/Cancel)
 */
public class EditStudentPage extends JPanel {
    
    // Form input fields for student information
    private JTextField studentIdField;  // Read-only, displays student ID
    private JTextField firstNameField;
    private JTextField lastNameField;
    private JTextField emailField;
    private JComboBox<String> statusComboBox;  // Dropdown: active, inactive, suspended
    
    // Context and navigation
    private university thisUniversity;  // Current university/faculty context
    private int currentStudentId;  // ID of student being edited
    private Object dashboardFrame;  // Reference to parent dashboard for navigation

    /**
     * Constructor without dashboard reference
     * @param thisUniversity University object for faculty context
     */
    public EditStudentPage(university thisUniversity) {
        this(thisUniversity, null);
    }

    /**
     * Full constructor with dashboard reference
     * @param thisUniversity University object for faculty context
     * @param dashboardFrame Parent dashboard frame for navigation callbacks
     */
    public EditStudentPage(university thisUniversity, Object dashboardFrame) {
        this.thisUniversity = thisUniversity;
        this.dashboardFrame = dashboardFrame;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader("Edit Student", new String[] { "Home", "Students", "Edit Student" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));

        
        content.add(createFormCard());

        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
    }

    /**
     * Loads existing student data into the form fields
     * Called when editing a student record from the students list
     * @param studentId Student ID (will be displayed but not editable)
     * @param firstName Current first name
     * @param lastName Current last name
     * @param email Current email address
     * @param status Current status (active, inactive, suspended)
     */
    public void loadStudentData(int studentId, String firstName, String lastName, String email, String status) {
        this.currentStudentId = studentId;
        studentIdField.setText(String.valueOf(studentId));
        studentIdField.setEditable(false);  // Student ID cannot be changed
        firstNameField.setText(firstName);
        lastNameField.setText(lastName);
        emailField.setText(email);
        statusComboBox.setSelectedItem(status);
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
        JPanel studentIdPanel = createFormField("Student ID *", "");
        studentIdField = (JTextField) studentIdPanel.getComponent(2);
        row1.add(studentIdPanel);
        JPanel firstNamePanel = createFormField("First Name *", "");
        firstNameField = (JTextField) firstNamePanel.getComponent(2);
        row1.add(firstNamePanel);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row2 = new JPanel(new GridLayout(1, 2, 20, 0));
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel lastNamePanel = createFormField("Last Name *", "");
        lastNameField = (JTextField) lastNamePanel.getComponent(2);
        row2.add(lastNamePanel);
        JPanel emailPanel = createFormField("Email *", "");
        emailField = (JTextField) emailPanel.getComponent(2);
        row2.add(emailPanel);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel statusPanel = createDropdownField("Status *", new String[] { "Active", "Pending" });
        statusComboBox = (JComboBox<String>) statusPanel.getComponent(2);
        row3.add(statusPanel);
        row3.add(new JPanel()); 
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Update Student");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        
        saveBtn.addActionListener(e -> {
            String firstName = firstNameField.getText().trim();
            String lastName = lastNameField.getText().trim();
            String email = emailField.getText().trim();
            String status = (String) statusComboBox.getSelectedItem();
            
            if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty()) {
                JOptionPane.showMessageDialog(this, 
                    "All fields are required!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            try {
                
                int verification = status.equals("Active") ? 1 : 0;
                studentsf studentManager = new studentsf(thisUniversity);
                studentManager.updateStudent(currentStudentId, firstName, lastName, email, verification);
                
                JOptionPane.showMessageDialog(this, 
                    "Student '" + firstName + " " + lastName + "' updated successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                if (dashboardFrame != null) {
                    try {
                        Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                        method.invoke(dashboardFrame, "students_list");
                    } catch (Exception ex) {
                        ex.printStackTrace();
                    }
                }
                
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error updating student: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });
        
        
        cancelBtn.addActionListener(e -> {
            if (dashboardFrame != null) {
                try {
                    Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                    method.invoke(dashboardFrame, "students_list");
                } catch (Exception ex) {
                    ex.printStackTrace();
                }
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

    private JPanel createDropdownField(String label, String[] options) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel lbl = new JLabel(label);
        lbl.setFont(AppFonts.BODY);
        lbl.setForeground(AppColors.TEXT_DARK);
        lbl.setAlignmentX(Component.LEFT_ALIGNMENT);

        JComboBox<String> comboBox = new JComboBox<>(options);
        comboBox.setFont(AppFonts.BODY);
        comboBox.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        comboBox.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(lbl);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(comboBox);

        return panel;
    }
}
