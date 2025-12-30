package pages_ui;

import util.*;
import resources.insertMail;
import models.university;
import javax.swing.*;
import java.awt.*;

/**
 * Add Mail/Message Page UI Component
 * 
 * Provides a form interface for administrators to create and send announcements/messages.
 * Features:
 * - Message title and content input
 * - Priority selection (low, medium, high, urgent)
 * - Receivers selection (All, Students, Professors, etc.)
 * - Related faculties selection (which faculties should see this message)
 * - Attached document path/URL field
 * - Form validation for required fields
 * - Send and cancel buttons
 * - Breadcrumb navigation
 * - Integration with insertMail resource manager
 * 
 * Layout Structure:
 * - Page header with breadcrumb
 * - White card container with form
 * - Title field (single line)
 * - Content area (multi-line text area)
 * - Dropdowns for priority, receivers, and related faculties
 * - Attached document field
 * - Action buttons (Send/Cancel)
 * 
 * Required Fields:
 * - Mail Title (subject line)
 * - Mail Content (message body)
 * - Priority level
 * - Receivers (target audience)
 * - Related faculties (visibility scope)
 * 
 * Optional Fields:
 * - Attached document path
 */
public class AddMailPage extends JPanel {
    
    private JTextField mailTitleField;
    private JTextArea mailContentArea;
    private JComboBox<String> priorityCombo;
    private JComboBox<String> relatedFacultiesCombo;
    private JComboBox<String> receiversCombo;
    private JTextField attachedDocField;
    private university thisuniversity;

    public AddMailPage() {
        this(null);
    }
    
    public AddMailPage(university thisuniversity) {
        this.thisuniversity = thisuniversity;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Add New Message", new String[] { "Home", "Mail", "Add Message" }));
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

        JLabel titleLabel = new JLabel("Message Information");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        card.add(headerPanel, BorderLayout.NORTH);

        
        JPanel formBody = new JPanel();
        formBody.setLayout(new BoxLayout(formBody, BoxLayout.Y_AXIS));
        formBody.setBackground(Color.WHITE);
        formBody.setBorder(BorderFactory.createEmptyBorder(25, 25, 25, 25));

        
        JPanel row1 = new JPanel(new BorderLayout());
        row1.setBackground(Color.WHITE);
        row1.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        mailTitleField = new JTextField();
        JPanel mailTitlePanel = createFormFieldWithComponent("Mail Title *", mailTitleField);
        row1.add(mailTitlePanel, BorderLayout.CENTER);
        formBody.add(row1);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row2 = new JPanel(new BorderLayout());
        row2.setBackground(Color.WHITE);
        row2.setMaximumSize(new Dimension(Integer.MAX_VALUE, 150));
        mailContentArea = new JTextArea(6, 20);
        JPanel mailContentPanel = createTextAreaFieldWithComponent("Mail Content *", mailContentArea);
        row2.add(mailContentPanel, BorderLayout.CENTER);
        formBody.add(row2);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row3 = new JPanel(new GridLayout(1, 2, 20, 0));
        row3.setBackground(Color.WHITE);
        row3.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        priorityCombo = new JComboBox<>(new String[] { "normal", "high" });
        JPanel priorityPanel = createDropdownFieldWithComponent("Priority *", priorityCombo);
        row3.add(priorityPanel);
        relatedFacultiesCombo = new JComboBox<>(new String[] { "Global", "Local" });
        JPanel facultiesPanel = createDropdownFieldWithComponent("Related Faculties *", relatedFacultiesCombo);
        row3.add(facultiesPanel);
        formBody.add(row3);
        formBody.add(Box.createRigidArea(new Dimension(0, 20)));

        
        JPanel row4 = new JPanel(new GridLayout(1, 2, 20, 0));
        row4.setBackground(Color.WHITE);
        row4.setMaximumSize(new Dimension(Integer.MAX_VALUE, 80));
        receiversCombo = new JComboBox<>(new String[] { "All Users", "Students Only", "Professors Only" });
        JPanel receiversPanel = createDropdownFieldWithComponent("Receivers *", receiversCombo);
        row4.add(receiversPanel);
        attachedDocField = new JTextField();
        JPanel attachedDocPanel = createFormFieldWithComponent("Attached Document", attachedDocField);
        row4.add(attachedDocPanel);
        formBody.add(row4);
        formBody.add(Box.createRigidArea(new Dimension(0, 30)));

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);
        buttonsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 50));

        JButton saveBtn = UIHelper.createPrimaryButton("📧 Send Message");
        saveBtn.addActionListener(e -> handleSendMessage());
        
        JButton cancelBtn = UIHelper.createOutlineButton("✕ Cancel");
        cancelBtn.addActionListener(e -> clearForm());

        buttonsPanel.add(saveBtn);
        buttonsPanel.add(cancelBtn);
        formBody.add(buttonsPanel);

        card.add(formBody, BorderLayout.CENTER);

        return card;
    }
    
    private void handleSendMessage() {
        
        String mailTitle = mailTitleField.getText().trim();
        String mailContent = mailContentArea.getText().trim();
        String priority = (String) priorityCombo.getSelectedItem();
        String relatedFaculties = (String) relatedFacultiesCombo.getSelectedItem();
        String receivers = (String) receiversCombo.getSelectedItem();
        String attachedDoc = attachedDocField.getText().trim();
        
        if (mailTitle.isEmpty() || mailContent.isEmpty()) {
            JOptionPane.showMessageDialog(this, 
                "Please fill in all required fields (Mail Title and Content)", 
                "Validation Error", 
                JOptionPane.ERROR_MESSAGE);
            return;
        }
        
        
        int relatedFacultiesValue = relatedFaculties.equals("Global") ? 0 : 
                                     (thisuniversity != null ? thisuniversity.getFaculty_id() : 1);
        
        int receiversValue = receivers.equals("All Users") ? 0 : 
                            receivers.equals("Students Only") ? 1 : 2;
        
        int facultyId = thisuniversity != null ? thisuniversity.getFaculty_id() : 1;
        
        
        boolean success = insertMail.insertNewMail(
            facultyId,
            priority,
            receiversValue,
            relatedFacultiesValue,
            mailTitle,
            mailContent,
            attachedDoc.isEmpty() ? null : attachedDoc
        );
        
        if (success) {
            JOptionPane.showMessageDialog(this, 
                "Message sent successfully!", 
                "Success", 
                JOptionPane.INFORMATION_MESSAGE);
            clearForm();
        } else {
            JOptionPane.showMessageDialog(this, 
                "Failed to send message. Please try again.", 
                "Error", 
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void clearForm() {
        mailTitleField.setText("");
        mailContentArea.setText("");
        priorityCombo.setSelectedIndex(0);
        relatedFacultiesCombo.setSelectedIndex(0);
        receiversCombo.setSelectedIndex(0);
        attachedDocField.setText("");
    }
    
    private JPanel createFormFieldWithComponent(String label, JTextField field) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        field.setFont(AppFonts.BODY);
        field.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(AppColors.BORDER),
            BorderFactory.createEmptyBorder(10, 15, 10, 15)
        ));
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        field.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }
    
    private JPanel createTextAreaFieldWithComponent(String label, JTextArea area) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        area.setFont(AppFonts.BODY);
        area.setLineWrap(true);
        area.setWrapStyleWord(true);
        area.setBorder(BorderFactory.createEmptyBorder(10, 15, 10, 15));
        area.setAlignmentX(Component.LEFT_ALIGNMENT);

        JScrollPane scrollPane = new JScrollPane(area);
        scrollPane.setBorder(BorderFactory.createLineBorder(AppColors.BORDER));
        scrollPane.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(scrollPane);

        return panel;
    }
    
    private JPanel createDropdownFieldWithComponent(String label, JComboBox<String> combo) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        combo.setFont(AppFonts.BODY);
        combo.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        combo.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(combo);

        return panel;
    }
    
    private JPanel createFormFieldWithReference(String label, String placeholder) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JPanel fieldWrapper = new JPanel(new BorderLayout());
        fieldWrapper.setBackground(Color.WHITE);
        JTextField field = UIHelper.createTextField(placeholder);
        field.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        fieldWrapper.add(field);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(fieldWrapper);

        return panel;
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

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(field);

        return panel;
    }
    
    private JPanel createTextAreaFieldWithReference(String label, String placeholder, int rows) {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);

        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(AppFonts.BODY);
        labelComponent.setForeground(AppColors.TEXT_DARK);
        labelComponent.setAlignmentX(Component.LEFT_ALIGNMENT);

        JTextArea area = UIHelper.createTextArea(placeholder, rows);
        area.setAlignmentX(Component.LEFT_ALIGNMENT);

        JScrollPane scrollPane = new JScrollPane(area);
        scrollPane.setBorder(BorderFactory.createLineBorder(AppColors.BORDER));
        scrollPane.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(scrollPane);

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

        JScrollPane scrollPane = new JScrollPane(area);
        scrollPane.setBorder(BorderFactory.createLineBorder(AppColors.BORDER));
        scrollPane.setAlignmentX(Component.LEFT_ALIGNMENT);

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(scrollPane);

        return panel;
    }
    
    private JPanel createDropdownFieldWithReference(String label, String[] options) {
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

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(combo);

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

        panel.add(labelComponent);
        panel.add(Box.createRigidArea(new Dimension(0, 8)));
        panel.add(combo);

        return panel;
    }
}
