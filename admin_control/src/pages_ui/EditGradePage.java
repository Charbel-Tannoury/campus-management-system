package pages_ui;

import util.*;
import resources.gradesf;
import models.*;
import javax.swing.*;
import java.awt.*;
import java.lang.reflect.Method;

/**
 * Edit Grade Page UI Component
 * 
 * Provides an interface for administrators to modify existing grade records.
 * Features:
 * - Pre-populated form with current grade values
 * - Read-only fields: Grade ID, Student ID, Student Name, Course Name
 * - Editable grade components: Project, Midterm, First Final, Second Final
 * - Real-time total calculation as grades are entered
 * - Input validation (numeric only)
 * - Update and cancel buttons
 * - Breadcrumb navigation
 * - Integration with gradesf resource manager
 * - Reflection-based navigation back to grades list
 * 
 * Grade Components:
 * - Project Grade (0-100)
 * - Midterm Grade (0-100)
 * - First Final Exam (0-100)
 * - Second Final Exam (0-100, for retakes)
 * - Total: Automatic sum of all components
 * 
 * Workflow:
 * 1. loadGradeData() called with existing grade information
 * 2. Form fields populated with current values
 * 3. Admin modifies grade values
 * 4. Total recalculates automatically on each keystroke
 * 5. Update button triggers database update
 * 6. Optional navigation back to grades list
 * 
 * Layout:
 * - Page header with breadcrumb
 * - White card with form
 * - Two-column grid for grade fields
 * - Live total display at bottom
 * - Action buttons (Update/Cancel)
 */
public class EditGradePage extends JPanel {
    
    private JTextField gradeIdField;
    private JTextField studentIdField;
    private JTextField studentNameField;
    private JTextField courseNameField;
    private JTextField projectGradeField;
    private JTextField midGradeField;
    private JTextField firstFinalField;
    private JTextField secondFinalField;
    private JLabel totalLabel;
    private university thisUniversity;
    private int currentGradeId;
    private Object dashboardFrame;

    public EditGradePage(university thisUniversity) {
        this(thisUniversity, null);
    }

    public EditGradePage(university thisUniversity, Object dashboardFrame) {
        this.thisUniversity = thisUniversity;
        this.dashboardFrame = dashboardFrame;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader("Edit Grade", new String[] { "Home", "Grades", "Edit Grade" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));

        
        content.add(createFormCard());

        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
    }

    public void loadGradeData(int gradeId, String studentId, String studentName, String courseName, 
                              String projectGrade, String midGrade, String firstFinal, String secondFinal, String total) {
        this.currentGradeId = gradeId;
        gradeIdField.setText(String.valueOf(gradeId));
        gradeIdField.setEditable(false);
        studentIdField.setText(studentId);
        studentIdField.setEditable(false);
        studentNameField.setText(studentName);
        studentNameField.setEditable(false);
        courseNameField.setText(courseName);
        courseNameField.setEditable(false);
        projectGradeField.setText(projectGrade);
        midGradeField.setText(midGrade);
        firstFinalField.setText(firstFinal);
        secondFinalField.setText(secondFinal);
        totalLabel.setText("Total: " + total);
        
        
        addCalculationListeners();
    }

    private void addCalculationListeners() {
        java.awt.event.KeyAdapter keyAdapter = new java.awt.event.KeyAdapter() {
            @Override
            public void keyReleased(java.awt.event.KeyEvent e) {
                calculateTotal();
            }
        };
        
        projectGradeField.addKeyListener(keyAdapter);
        midGradeField.addKeyListener(keyAdapter);
        firstFinalField.addKeyListener(keyAdapter);
        secondFinalField.addKeyListener(keyAdapter);
    }

    private void calculateTotal() {
        try {
            int project = projectGradeField.getText().isEmpty() ? 0 : Integer.parseInt(projectGradeField.getText());
            int mid = midGradeField.getText().isEmpty() ? 0 : Integer.parseInt(midGradeField.getText());
            int firstFinal = firstFinalField.getText().isEmpty() ? 0 : Integer.parseInt(firstFinalField.getText());
            int secondFinal = secondFinalField.getText().isEmpty() ? 0 : Integer.parseInt(secondFinalField.getText());
            int total = project + mid + firstFinal + secondFinal;
            totalLabel.setText("Total: " + total);
        } catch (NumberFormatException e) {
            totalLabel.setText("Total: Invalid input");
        }
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

        
        JLabel formTitle = new JLabel("Grade Information");
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
        JPanel gradeIdPanel = createFormField("Grade ID", "");
        gradeIdField = (JTextField) gradeIdPanel.getComponent(2);
        row1.add(gradeIdPanel);
        JPanel studentIdPanel = createFormField("Student ID", "");
        studentIdField = (JTextField) studentIdPanel.getComponent(2);
        row1.add(studentIdPanel);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row2 = new JPanel(new GridLayout(1, 2, 20, 0));
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel studentNamePanel = createFormField("Student Name", "");
        studentNameField = (JTextField) studentNamePanel.getComponent(2);
        row2.add(studentNamePanel);
        JPanel courseNamePanel = createFormField("Course", "");
        courseNameField = (JTextField) courseNamePanel.getComponent(2);
        row2.add(courseNamePanel);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel projectPanel = createFormField("Project Grade", "0");
        projectGradeField = (JTextField) projectPanel.getComponent(2);
        row3.add(projectPanel);
        JPanel midPanel = createFormField("Midterm Grade", "0");
        midGradeField = (JTextField) midPanel.getComponent(2);
        row3.add(midPanel);
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row4 = new JPanel(new GridLayout(1, 2, 20, 0));
        row4.setBackground(Color.WHITE);
        row4.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        JPanel firstFinalPanel = createFormField("First Final Grade", "0");
        firstFinalField = (JTextField) firstFinalPanel.getComponent(2);
        row4.add(firstFinalPanel);
        JPanel secondFinalPanel = createFormField("Second Final Grade", "0");
        secondFinalField = (JTextField) secondFinalPanel.getComponent(2);
        row4.add(secondFinalPanel);
        formBody.add(row4);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        totalLabel = new JLabel("Total: 0");
        totalLabel.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 16));
        totalLabel.setForeground(AppColors.SECONDARY);
        totalLabel.setAlignmentX(Component.LEFT_ALIGNMENT);
        formBody.add(totalLabel);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Update Grade");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        
        saveBtn.addActionListener(e -> {
            try {
                int projectGrade = projectGradeField.getText().isEmpty() ? 0 : Integer.parseInt(projectGradeField.getText());
                int midGrade = midGradeField.getText().isEmpty() ? 0 : Integer.parseInt(midGradeField.getText());
                int firstFinal = firstFinalField.getText().isEmpty() ? 0 : Integer.parseInt(firstFinalField.getText());
                int secondFinal = secondFinalField.getText().isEmpty() ? 0 : Integer.parseInt(secondFinalField.getText());
                
                
                gradesf gradeManager = new gradesf(thisUniversity);
                gradeManager.updateGrade(currentGradeId, projectGrade, midGrade, firstFinal, secondFinal);
                
                JOptionPane.showMessageDialog(this, 
                    "Grade updated successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                if (dashboardFrame != null) {
                    try {
                        Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                        method.invoke(dashboardFrame, "grades_list");
                    } catch (Exception ex) {
                        ex.printStackTrace();
                    }
                }
                
            } catch (NumberFormatException ex) {
                JOptionPane.showMessageDialog(this, 
                    "Please enter valid numeric values for grades!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error updating grade: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });
        
        
        cancelBtn.addActionListener(e -> {
            if (dashboardFrame != null) {
                try {
                    Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                    method.invoke(dashboardFrame, "grades_list");
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
}
