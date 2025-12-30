package pages_ui;

import util.*;
import resources.professorsf;
import models.*;
import javax.swing.*;
import java.awt.*;
import java.lang.reflect.Method;

/**
 * Edit Professor Page UI Component
 * 
 * Provides an interface for administrators to modify existing professor records.
 * Features:
 * - Pre-populated form with current professor data
 * - Professor ID (read-only, cannot be changed)
 * - Editable fields: name, email, phone, status, fixed employment
 * - Status dropdown (active/inactive)
 * - Fixed employment checkbox (permanent vs temporary)
 * - Update and cancel buttons
 * - Form validation
 * - Breadcrumb navigation
 * - Integration with professorsf resource manager
 * - Reflection-based navigation back to professors list
 * 
 * Workflow:
 * 1. loadProfessorData() called with existing professor information
 * 2. Form fields populated with current values
 * 3. Admin modifies desired fields
 * 4. Update button triggers database update
 * 5. Optional navigation back to professors list
 * 
 * Editable Fields:
 * - First Name
 * - Last Name
 * - Email
 * - Phone
 * - Status (active/inactive dropdown)
 * - Fixed Employment (checkbox)
 * 
 * Read-Only Fields:
 * - Professor ID (primary key, immutable)
 * 
 * Layout:
 * - Page header with breadcrumb
 * - White card with form
 * - Two-column grid for input fields
 * - Action buttons (Update/Cancel)
 */
public class EditProfessorPage extends JPanel {
    
    private JTextField professorIdField;
    private JTextField firstNameField;
    private JTextField lastNameField;
    private JTextField emailField;
    private JTextField phoneField;
    private JComboBox<String> statusComboBox;
    private JCheckBox fixedCheckBox;
    private university thisUniversity;
    private int currentProfessorId;
    private Object dashboardFrame;

    public EditProfessorPage(university thisUniversity) {
        this(thisUniversity, null);
    }

    public EditProfessorPage(university thisUniversity, Object dashboardFrame) {
        this.thisUniversity = thisUniversity;
        this.dashboardFrame = dashboardFrame;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader("Edit Professor", new String[] { "Home", "Professors", "Edit Professor" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));

        
        content.add(createFormCard());

        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
    }

    public void loadProfessorData(int professorId, String firstName, String lastName, String email, String phone, String status, boolean fixed) {
        this.currentProfessorId = professorId;
        professorIdField.setText(String.valueOf(professorId));
        professorIdField.setEditable(false);
        firstNameField.setText(firstName);
        lastNameField.setText(lastName);
        emailField.setText(email);
        phoneField.setText(phone);
        statusComboBox.setSelectedItem(status);
        fixedCheckBox.setSelected(fixed);
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

        
        JLabel formTitle = new JLabel("Professor Information");
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
        JPanel professorIdPanel = createFormField("Professor ID *", "");
        professorIdField = (JTextField) professorIdPanel.getComponent(2);
        row1.add(professorIdPanel);
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
        JPanel phonePanel = createFormField("Phone", "");
        phoneField = (JTextField) phonePanel.getComponent(2);
        row3.add(phonePanel);
        JPanel statusPanel = createDropdownField("Status *", new String[] { "Active", "Inactive", "On Leave" });
        statusComboBox = (JComboBox<String>) statusPanel.getComponent(2);
        row3.add(statusPanel);
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row4 = new JPanel(new FlowLayout(FlowLayout.LEFT));
        row4.setBackground(Color.WHITE);
        row4.setMaximumSize(new Dimension(Integer.MAX_VALUE, 40));
        fixedCheckBox = new JCheckBox("Fixed Position");
        fixedCheckBox.setFont(AppFonts.BODY);
        fixedCheckBox.setBackground(Color.WHITE);
        row4.add(fixedCheckBox);
        formBody.add(row4);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("💾 Update Professor");
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        
        
        saveBtn.addActionListener(e -> {
            String firstName = firstNameField.getText().trim();
            String lastName = lastNameField.getText().trim();
            String email = emailField.getText().trim();
            String phone = phoneField.getText().trim();
            String status = (String) statusComboBox.getSelectedItem();
            boolean fixed = fixedCheckBox.isSelected();
            
            if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty()) {
                JOptionPane.showMessageDialog(this, 
                    "First Name, Last Name, and Email are required!", 
                    "Validation Error", 
                    JOptionPane.ERROR_MESSAGE);
                return;
            }
            
            try {
                
                professorsf professorManager = new professorsf(thisUniversity);
                professorManager.updateProfessor(currentProfessorId, firstName, lastName, email, phone, status, fixed);
                
                JOptionPane.showMessageDialog(this, 
                    "Professor '" + firstName + " " + lastName + "' updated successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                if (dashboardFrame != null) {
                    try {
                        Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                        method.invoke(dashboardFrame, "professors_list");
                    } catch (Exception ex) {
                        ex.printStackTrace();
                    }
                }
                
            } catch (Exception ex) {
                ex.printStackTrace();
                JOptionPane.showMessageDialog(this, 
                    "Error updating professor: " + ex.getMessage(), 
                    "Database Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        });
        
        
        cancelBtn.addActionListener(e -> {
            if (dashboardFrame != null) {
                try {
                    Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                    method.invoke(dashboardFrame, "professors_list");
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
