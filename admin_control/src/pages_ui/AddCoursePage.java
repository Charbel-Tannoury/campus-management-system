package pages_ui;

import util.*;
import javax.swing.*;
import java.awt.*;
import resources.coursesf;
import models.university;

/**
 * Add Course Page UI Component
 * 
 * Provides a form interface for administrators to add new courses to the system.
 * Features:
 * - Course code and name input
 * - Multi-line description text area
 * - Major selection dropdown
 * - Semester assignment
 * - Form validation for required fields
 * - Save and cancel buttons
 * - Breadcrumb navigation
 * - Integration with coursesf resource manager
 * 
 * Layout Structure:
 * - Page header with breadcrumb navigation
 * - White card container with form
 * - Two-column grid for code/name fields
 * - Full-width text area for description
 * - Dropdowns for major and semester selection
 * - Action buttons (Save/Cancel)
 * 
 * Required Fields:
 * - Course Code (e.g., CS101)
 * - Course Name (e.g., Introduction to Computer Science)
 * - Major (dropdown selection)
 * - Semester (dropdown selection)
 * 
 * Optional Fields:
 * - Description (multi-line text)
 */
public class AddCoursePage extends JPanel {

    private university thisUniversity;
    private JTextField courseCodeField;
    private JTextField courseNameField;
    private JTextArea courseDescriptionArea;
    private JComboBox<String> majorComboBox;
    private JComboBox<String> semesterComboBox;
    
    public AddCoursePage(university thisUniversity) {
        this.thisUniversity = thisUniversity;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Add New Course", new String[] { "Home", "Courses", "Add Course" }));
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
        card.setMaximumSize(new Dimension(Integer.MAX_VALUE, 600));

        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(Color.WHITE);
        headerPanel.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER),
                BorderFactory.createEmptyBorder(20, 25, 20, 25)));

        JLabel titleLabel = new JLabel("Course Information");
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
        JPanel codePanel = createFormField("Course Code *", "Example: CS101");
        JPanel namePanel = createFormField("Course Name *", "Example: Introduction to Computer Science");
        row1.add(codePanel);
        row1.add(namePanel);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        JPanel row2 = new JPanel(new BorderLayout());
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 120));
        JPanel descPanel = createTextAreaField("Description", "Course description...", 4);
        row2.add(descPanel, BorderLayout.CENTER);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel majorPanel = createDropdownField("Major *",
                new String[] { "CS", "CE", "EE", "ME", "BA", "FIN", "MKT", "MED", "LAW", "MATH", "PHY", "BIO", "CHEM", "NURS", "CE_CIVIL" });
        row3.add(majorPanel);
        row3.add(createDropdownField("Responsible Professor", new String[] { "-- Select Professor --",
                "Dr. Ahmad Mansour", "Dr. Layla Ibrahim", "Dr. Omar Hassan" }));
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row4 = new JPanel(new GridLayout(1, 2, 20, 0));
        row4.setBackground(Color.WHITE);
        row4.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        row4.add(createFormField("Credits *", "3"));
        JPanel semesterPanel = createDropdownField("Semester *", new String[] { "semester1", "semester2", "semester3", "semester4", "semester5", "semester6", "semester7", "semester8" });
        row4.add(semesterPanel);
        formBody.add(row4);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Save Course");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        saveBtn.addActionListener(e -> {
            String courseCode = courseCodeField.getText().trim();
            String courseName = courseNameField.getText().trim();
            String courseDescription = courseDescriptionArea.getText().trim();
            String selectedMajor = (String) majorComboBox.getSelectedItem();
            String selectedSemester = (String) semesterComboBox.getSelectedItem();
            
            if (courseCode.isEmpty() || courseName.isEmpty()) {
                JOptionPane.showMessageDialog(this, 
                    "Course Code and Course Name are required!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            if (selectedMajor == null || selectedSemester == null) {
                JOptionPane.showMessageDialog(this, 
                    "Please select Major and Semester!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            try {
                coursesf courseManager = new coursesf(thisUniversity);
                courseManager.addcourses(courseCode, courseName, courseDescription, selectedMajor, selectedSemester);
                
                JOptionPane.showMessageDialog(this, 
                    "Course '" + courseName + "' added successfully to " + selectedMajor + " " + selectedSemester + "!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                courseCodeField.setText("");
                courseNameField.setText("");
                courseDescriptionArea.setText("");
                majorComboBox.setSelectedIndex(0);
                semesterComboBox.setSelectedIndex(0);
                
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error adding course: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });
        
        cancelBtn.addActionListener(e -> {
            courseCodeField.setText("");
            courseNameField.setText("");
            courseDescriptionArea.setText("");
            majorComboBox.setSelectedIndex(0);
            semesterComboBox.setSelectedIndex(0);
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
        
        if (label.contains("Course Code")) {
            courseCodeField = field;
        } else if (label.contains("Course Name")) {
            courseNameField = field;
        }

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }

    private JPanel createTextAreaField(String label, String placeholder, int rows) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JTextArea area = UIHelper.createTextArea(placeholder, rows);
        area.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        if (label.contains("Description")) {
            courseDescriptionArea = area;
        }

        JScrollPane scrollPane = new JScrollPane(area);
        scrollPane.setBorder(BorderFactory.createLineBorder(AppColors.BORDER));
        scrollPane.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(scrollPane);

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
        
        if (label.contains("Major")) {
            majorComboBox = combo;
        } else if (label.contains("Semester")) {
            semesterComboBox = combo;
        }

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(combo);

        return panel;
    }
}
