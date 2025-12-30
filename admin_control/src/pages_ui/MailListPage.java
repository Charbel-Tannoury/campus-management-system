package pages_ui;

import util.*;
import resources.getstats;
import resources.deleteMail;
import models.mails;
import models.university;
import javax.swing.*;
import javax.swing.table.*;
import java.awt.*;
import java.util.ArrayList;

/**
 * Mail Messages List Page UI Component
 * 
 * Displays a table of all mail/announcement messages for the faculty.
 * Features:
 * - Paginated table of messages
 * - Shows title, priority, receivers, date, and status
 * - View button to see full message details
 * - Delete button to remove messages
 * - Add new message button in header
 * - Faculty-based filtering (shows only relevant messages)
 * - Integration with showMails resource manager
 * - Real-time message count debugging
 * 
 * Table Columns:
 * - Mail ID
 * - Title
 * - Priority (low/medium/high/urgent)
 * - Receivers (All/Students/Professors)
 * - Date Created
 * - Actions (View/Delete buttons)
 * 
 * Data Source:
 * - Loads messages via getstats.getMails()
 * - Filters by related_faculties (0 = all, or specific faculty)
 * - Ordered by created_at DESC (newest first)
 * 
 * Layout:
 * - Page header with breadcrumb
 * - White card containing the table
 * - Custom styled table with alternating row colors
 */
public class MailListPage extends JPanel {
    
    private university thisuniversity;
    private ArrayList<mails> mailsList;
    private JTable table;
    private DefaultTableModel tableModel;
    private Object dashboardFrame; 

    public MailListPage() {
        this(null, null);
    }
    
    public MailListPage(university thisuniversity, Object dashboardFrame) {
        this.dashboardFrame = dashboardFrame;
        this.thisuniversity = thisuniversity;
        if (thisuniversity != null) {
            this.mailsList = new getstats(thisuniversity).getMails();
            System.out.println("MailListPage: mailsList size = " + (mailsList != null ? mailsList.size() : "null"));
        }
        
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Messages List", new String[] { "Home", "Mail", "Messages List" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));
        content.add(createTableCard());

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

    private JPanel createTableCard() {
        JPanel card = new JPanel(new BorderLayout());
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createLineBorder(new Color(0, 0, 0, 10)));
        card.setMaximumSize(new Dimension(Integer.MAX_VALUE, 600));

        
        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(Color.WHITE);
        headerPanel.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER),
                BorderFactory.createEmptyBorder(20, 25, 20, 25)));

        JLabel titleLabel = new JLabel("All Messages");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);

        JButton addBtn = UIHelper.createPrimaryButton("+ New Message");
        addBtn.addActionListener(e -> {
            if (dashboardFrame != null) {
                try {
                    java.lang.reflect.Method method = dashboardFrame.getClass().getMethod("onNavigate", String.class);
                    method.invoke(dashboardFrame, "mail_add");
                } catch (Exception ex) {
                    ex.printStackTrace();
                }
            }
        });
        buttonsPanel.add(addBtn);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(buttonsPanel, BorderLayout.EAST);

        card.add(headerPanel, BorderLayout.NORTH);

        
        String[] columns = { "#", "Subject", "Content", "Date", "Priority", "Actions" };
        Object[][] data;
        
        System.out.println("Creating table, mailsList is " + (mailsList == null ? "null" : "not null, size=" + mailsList.size()));
        
        if (mailsList != null && !mailsList.isEmpty()) {
            data = new Object[mailsList.size()][6];
            for (int i = 0; i < mailsList.size(); i++) {
                mails mail = mailsList.get(i);
                data[i][0] = String.valueOf(mail.getMail_id());
                data[i][1] = mail.getMail_title() != null ? mail.getMail_title() : "No Subject";
                data[i][2] = mail.getMail_info() != null ? mail.getMail_info() : "";
                data[i][3] = mail.getCreated_at() != null ? mail.getCreated_at() : "";
                data[i][4] = mail.getPriority() != null ? mail.getPriority() : "Normal";
                data[i][5] = "";
                System.out.println("Added mail row " + i + ": " + data[i][1]);
            }
        } else {
            System.out.println("No mails found, showing fallback message");
            
            data = new Object[][] {
                { "", "No messages", "No messages available", "", "", "" }
            };
        }

        tableModel = new DefaultTableModel(data, columns) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 5; 
            }
        };
        
        table = new JTable(tableModel);
        this.table = table;
        
        
        table.setFont(AppFonts.BODY);
        table.setRowHeight(40);
        table.setShowGrid(false);
        table.setIntercellSpacing(new Dimension(0, 0));
        table.setSelectionBackground(new Color(102, 126, 234, 30));
        table.setSelectionForeground(AppColors.TEXT_DARK);
        
        
        JTableHeader header = table.getTableHeader();
        header.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 13));
        header.setBackground(AppColors.LIGHT);
        header.setForeground(AppColors.TEXT_MUTED);
        header.setBorder(BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER));
        
        
        table.setDefaultRenderer(Object.class, new DefaultTableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                Component c = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
                if (!isSelected) {
                    c.setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
                }
                setBorder(BorderFactory.createEmptyBorder(0, 10, 0, 10));
                return c;
            }
        });

        
        table.getColumnModel().getColumn(5).setCellRenderer(new TableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT, 5, 5));
                panel.setBackground(isSelected ? new Color(102, 126, 234, 30) : 
                                   (row % 2 == 0 ? Color.WHITE : AppColors.LIGHT));

                JButton viewBtn = new JButton("👁");
                viewBtn.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 12));
                viewBtn.setBackground(AppColors.INFO);
                viewBtn.setForeground(Color.WHITE);
                viewBtn.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));
                viewBtn.setFocusPainted(false);
                viewBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));

                JButton deleteBtn = new JButton("🗑");
                deleteBtn.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 12));
                deleteBtn.setBackground(AppColors.DANGER);
                deleteBtn.setForeground(Color.WHITE);
                deleteBtn.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));
                deleteBtn.setFocusPainted(false);
                deleteBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));

                panel.add(viewBtn);
                panel.add(deleteBtn);
                return panel;
            }
        });
        
        
        table.getColumnModel().getColumn(5).setCellEditor(new ButtonEditor(new JCheckBox()));

        JScrollPane tableScroll = new JScrollPane(table);
        tableScroll.setBorder(BorderFactory.createEmptyBorder());

        JPanel bodyPanel = new JPanel(new BorderLayout());
        bodyPanel.setBackground(Color.WHITE);
        bodyPanel.add(tableScroll, BorderLayout.CENTER);

        card.add(bodyPanel, BorderLayout.CENTER);

        return card;
    }
    
    private void viewMailDetails(int row) {
        if (mailsList == null || row >= mailsList.size()) {
            JOptionPane.showMessageDialog(this, "Unable to view mail details.", "Error", JOptionPane.ERROR_MESSAGE);
            return;
        }
        
        mails mail = mailsList.get(row);
        
        
        JDialog dialog = new JDialog((Frame) SwingUtilities.getWindowAncestor(this), "Mail Details", true);
        dialog.setSize(600, 500);
        dialog.setLocationRelativeTo(this);
        
        JPanel detailsPanel = new JPanel();
        detailsPanel.setLayout(new BoxLayout(detailsPanel, BoxLayout.Y_AXIS));
        detailsPanel.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        detailsPanel.setBackground(Color.WHITE);
        
        
        addDetailField(detailsPanel, "Mail ID:", String.valueOf(mail.getMail_id()));
        addDetailField(detailsPanel, "Subject:", mail.getMail_title());
        addDetailField(detailsPanel, "Priority:", mail.getPriority());
        addDetailField(detailsPanel, "Faculty ID:", String.valueOf(mail.getFaculty_id()));
        addDetailField(detailsPanel, "Related Faculties:", mail.getRelated_faculties() == 0 ? "Global" : String.valueOf(mail.getRelated_faculties()));
        addDetailField(detailsPanel, "Receivers:", 
            mail.getReceivers() == 0 ? "All Users" : 
            mail.getReceivers() == 1 ? "Students Only" : "Professors Only");
        addDetailField(detailsPanel, "Created At:", mail.getCreated_at());
        addDetailField(detailsPanel, "Attached Document:", mail.getAttached_doc() != null ? mail.getAttached_doc() : "None");
        
        
        detailsPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        JLabel contentLabel = new JLabel("Content:");
        contentLabel.setFont(AppFonts.BODY);
        contentLabel.setForeground(AppColors.TEXT_DARK);
        contentLabel.setAlignmentX(Component.LEFT_ALIGNMENT);
        detailsPanel.add(contentLabel);
        
        detailsPanel.add(Box.createRigidArea(new Dimension(0, 5)));
        JTextArea contentArea = new JTextArea(mail.getMail_info());
        contentArea.setEditable(false);
        contentArea.setLineWrap(true);
        contentArea.setWrapStyleWord(true);
        contentArea.setFont(AppFonts.BODY);
        contentArea.setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));
        JScrollPane scrollPane = new JScrollPane(contentArea);
        scrollPane.setPreferredSize(new Dimension(550, 150));
        scrollPane.setAlignmentX(Component.LEFT_ALIGNMENT);
        detailsPanel.add(scrollPane);
        
        
        detailsPanel.add(Box.createRigidArea(new Dimension(0, 20)));
        JButton closeBtn = UIHelper.createPrimaryButton("Close");
        closeBtn.addActionListener(e -> dialog.dispose());
        closeBtn.setAlignmentX(Component.LEFT_ALIGNMENT);
        detailsPanel.add(closeBtn);
        
        dialog.add(new JScrollPane(detailsPanel));
        dialog.setVisible(true);
    }
    
    private void addDetailField(JPanel panel, String label, String value) {
        JPanel fieldPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 0, 5));
        fieldPanel.setBackground(Color.WHITE);
        fieldPanel.setAlignmentX(Component.LEFT_ALIGNMENT);
        fieldPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 30));
        
        JLabel labelComponent = new JLabel(label);
        labelComponent.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 14));
        labelComponent.setForeground(AppColors.TEXT_DARK);
        
        JLabel valueComponent = new JLabel(value != null ? value : "N/A");
        valueComponent.setFont(AppFonts.BODY);
        valueComponent.setForeground(AppColors.TEXT_MUTED);
        
        fieldPanel.add(labelComponent);
        fieldPanel.add(Box.createRigidArea(new Dimension(10, 0)));
        fieldPanel.add(valueComponent);
        
        panel.add(fieldPanel);
    }
    
    private void deleteMailFromDatabase(int row) {
        if (mailsList == null || row >= mailsList.size()) {
            JOptionPane.showMessageDialog(this, "Unable to delete mail.", "Error", JOptionPane.ERROR_MESSAGE);
            return;
        }
        
        mails mail = mailsList.get(row);
        
        int confirm = JOptionPane.showConfirmDialog(this,
            "Are you sure you want to delete this mail?\n\nSubject: " + mail.getMail_title(),
            "Confirm Deletion",
            JOptionPane.YES_NO_OPTION,
            JOptionPane.WARNING_MESSAGE);
        
        if (confirm == JOptionPane.YES_OPTION) {
            boolean success = deleteMail.deleteMailById(mail.getMail_id());
            
            if (success) {
                JOptionPane.showMessageDialog(this, 
                    "Mail deleted successfully!", 
                    "Success", 
                    JOptionPane.INFORMATION_MESSAGE);
                
                
                refreshMailList();
            } else {
                JOptionPane.showMessageDialog(this, 
                    "Failed to delete mail. Please try again.", 
                    "Error", 
                    JOptionPane.ERROR_MESSAGE);
            }
        }
    }
    
    private void refreshMailList() {
        
        if (thisuniversity != null) {
            this.mailsList = new getstats(thisuniversity).getMails();
        }
        
        
        removeAll();
        
        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Messages List", new String[] { "Home", "Mail", "Messages List" }));
        content.add(Box.createRigidArea(new Dimension(0, 25)));
        content.add(createTableCard());

        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
        
        revalidate();
        repaint();
    }
    
    /**
     * Custom cell editor for action buttons
     */
    class ButtonEditor extends DefaultCellEditor {
        private JPanel panel;
        private JButton viewBtn;
        private JButton deleteBtn;
        private int currentRow;

        public ButtonEditor(JCheckBox checkBox) {
            super(checkBox);
            
            panel = new JPanel(new FlowLayout(FlowLayout.LEFT, 5, 5));
            
            viewBtn = new JButton("👁");
            viewBtn.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 12));
            viewBtn.setBackground(AppColors.INFO);
            viewBtn.setForeground(Color.WHITE);
            viewBtn.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));
            viewBtn.setFocusPainted(false);
            viewBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));
            viewBtn.addActionListener(e -> {
                viewMailDetails(currentRow);
                fireEditingStopped();
            });

            deleteBtn = new JButton("🗑");
            deleteBtn.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 12));
            deleteBtn.setBackground(AppColors.DANGER);
            deleteBtn.setForeground(Color.WHITE);
            deleteBtn.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));
            deleteBtn.setFocusPainted(false);
            deleteBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));
            deleteBtn.addActionListener(e -> {
                deleteMailFromDatabase(currentRow);
                fireEditingStopped();
            });

            panel.add(viewBtn);
            panel.add(deleteBtn);
        }

        @Override
        public Component getTableCellEditorComponent(JTable table, Object value,
                boolean isSelected, int row, int column) {
            this.currentRow = row;
            panel.setBackground(isSelected ? new Color(102, 126, 234, 30) : 
                               (row % 2 == 0 ? Color.WHITE : AppColors.LIGHT));
            return panel;
        }

        @Override
        public Object getCellEditorValue() {
            return "";
        }
    }
}
