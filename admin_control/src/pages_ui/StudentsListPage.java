package pages_ui;

import util.*;
import resources.studentsf;
import models.*;
import javax.swing.*;
import javax.swing.table.*;
import java.awt.*;
import java.awt.event.*;
import java.util.ArrayList;
import java.lang.reflect.Method;

/**
 * Students List Page UI Component
 * 
 * Displays a searchable, interactive table of all students in the system.
 * Features:
 * - Paginated table display of student records
 * - Edit button for each student (navigates to EditStudentPage)
 * - Faculty-based filtering (shows only students from admin's faculty)
 * - Breadcrumb navigation
 * - Action buttons for editing individual records
 * - Integration with studentsf resource manager
 * - Reflection-based navigation to edit page
 * 
 * Table Columns:
 * - Student ID
 * - First Name
 * - Last Name
 * - Email
 * - Status (active/inactive/suspended)
 * - Actions (Edit button)
 * 
 * Layout:
 * - Page header with breadcrumb
 * - White card containing the table
 * - Custom styled table with alternating row colors
 */
public class StudentsListPage extends JPanel {

    // Table model for managing student data display
    private DefaultTableModel tableModel;
    
    // University context for faculty-based filtering
    private university thisUniversity;
    
    // Reference to parent dashboard for navigation
    private Object dashboardFrame;

    /**
     * Constructor without dashboard reference
     * @param thisUniversity University object for faculty context
     */
    public StudentsListPage(university thisUniversity) {
        this(thisUniversity, null);
    }

    /**
     * Full constructor with dashboard reference
     * @param thisUniversity University object for faculty context
     * @param dashboardFrame Parent dashboard for reflection-based navigation
     */
    public StudentsListPage(university thisUniversity, Object dashboardFrame) {
        this.thisUniversity = thisUniversity;
        this.dashboardFrame = dashboardFrame;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader("Students List", new String[] { "Home", "Students", "Students List" }));
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

        JLabel titleLabel = new JLabel("All Students");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        
        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);

        JButton filterBtn = UIHelper.createOutlineButton("🔍 Filter");

        buttonsPanel.add(filterBtn);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(buttonsPanel, BorderLayout.EAST);

        card.add(headerPanel, BorderLayout.NORTH);

        
        String[] columns = { "Student ID", "Full Name", "Department", "Year", "Email", "Status", "Actions" };
        
        tableModel = new DefaultTableModel(columns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 6; 
            }
        };

        
        loadStudentsFromDatabase();

        JTable table = new JTable(tableModel);
        table.setRowHeight(50);
        table.setShowGrid(false);
        table.setIntercellSpacing(new Dimension(0, 0));
        table.setSelectionBackground(new Color(102, 126, 234, 30));
        table.setSelectionForeground(AppColors.TEXT_DARK);
        table.setFont(AppFonts.BODY);

        
        JTableHeader header = table.getTableHeader();
        header.setBackground(Color.WHITE);
        header.setForeground(AppColors.TEXT_DARK);
        header.setFont(AppFonts.TABLE_HEADER);
        header.setBorder(BorderFactory.createMatteBorder(0, 0, 2, 0, AppColors.BORDER));
        header.setPreferredSize(new Dimension(header.getWidth(), 45));

        
        table.setDefaultRenderer(Object.class, new DefaultTableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                Component c = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
                if (!isSelected) {
                    c.setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
                }
                setBorder(BorderFactory.createEmptyBorder(0, 15, 0, 15));

                if (column == 0)
                    setForeground(AppColors.SECONDARY);
                else if (column == 4)
                    setForeground(AppColors.INFO);
                else
                    setForeground(AppColors.TEXT_DARK);

                return c;
            }
        });

        
        table.getColumnModel().getColumn(5).setCellRenderer(new DefaultTableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                String status = value.toString();
                JLabel badge = UIHelper.createBadge(status, status.equals("Active"));
                JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT));
                panel.setOpaque(true);
                panel.setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
                if (isSelected)
                    panel.setBackground(new Color(102, 126, 234, 30));
                panel.add(badge);
                return panel;
            }
        });

        
        table.getColumnModel().getColumn(6).setCellRenderer(new ActionButtonRenderer());
        table.getColumnModel().getColumn(6).setCellEditor(new ActionButtonEditor(table, tableModel));
        table.getColumnModel().getColumn(6).setPreferredWidth(120);

        JScrollPane tableScroll = new JScrollPane(table);
        tableScroll.setBorder(BorderFactory.createEmptyBorder());

        JPanel bodyPanel = new JPanel(new BorderLayout());
        bodyPanel.setBackground(Color.WHITE);
        bodyPanel.add(tableScroll, BorderLayout.CENTER);

        card.add(bodyPanel, BorderLayout.CENTER);

        return card;
    }

    
    class ActionButtonRenderer extends JPanel implements TableCellRenderer {
        private JButton viewBtn, editBtn, deleteBtn;

        public ActionButtonRenderer() {
            setLayout(new FlowLayout(FlowLayout.CENTER, 5, 5));
            setOpaque(true);

            viewBtn = createActionButton("View", new Color(0, 102, 204));
            editBtn = createActionButton("Edit", new Color(204, 102, 0));
            deleteBtn = createActionButton("Delete", new Color(153, 0, 0));

            add(viewBtn);
            add(editBtn);
            add(deleteBtn);
        }

        private JButton createActionButton(String text, Color bgColor) {
            JButton btn = new JButton(text);
            btn.setFont(new Font("Segoe UI", Font.BOLD, 10));
            btn.setBackground(bgColor);
            btn.setForeground(Color.WHITE);
            btn.setPreferredSize(new Dimension(50, 28));
            btn.setBorder(BorderFactory.createEmptyBorder(4, 8, 4, 8));
            btn.setFocusPainted(false);
            btn.setOpaque(true);
            btn.setContentAreaFilled(true);
            btn.setBorderPainted(false);
            btn.setCursor(new Cursor(Cursor.HAND_CURSOR));
            return btn;
        }

        @Override
        public Component getTableCellRendererComponent(JTable table, Object value,
                boolean isSelected, boolean hasFocus, int row, int column) {
            if (isSelected) {
                setBackground(new Color(102, 126, 234, 30));
            } else {
                setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
            }
            return this;
        }
    }

    
    class ActionButtonEditor extends AbstractCellEditor implements TableCellEditor {
        private JPanel panel;
        private JButton viewBtn, editBtn, deleteBtn;
        private JTable table;
        private DefaultTableModel model;
        private int currentRow;

        public ActionButtonEditor(JTable table, DefaultTableModel model) {
            this.table = table;
            this.model = model;
            panel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
            panel.setOpaque(true);

            viewBtn = createActionButton("View", new Color(0, 102, 204));
            editBtn = createActionButton("Edit", new Color(204, 102, 0));
            deleteBtn = createActionButton("Delete", new Color(153, 0, 0));

            viewBtn.addActionListener(e -> {
                String id = (String) table.getValueAt(currentRow, 0);
                String name = (String) table.getValueAt(currentRow, 1);
                String department = (String) table.getValueAt(currentRow, 2);
                String year = (String) table.getValueAt(currentRow, 3);
                String email = (String) table.getValueAt(currentRow, 4);
                String status = (String) table.getValueAt(currentRow, 5);
                
                String details = "Student ID: " + id + "\n" +
                                "Name: " + name + "\n" +
                                "Department: " + department + "\n" +
                                "Year: " + year + "\n" +
                                "Email: " + email + "\n" +
                                "Status: " + status;
                
                JOptionPane.showMessageDialog(panel, details, "Student Details",
                        JOptionPane.INFORMATION_MESSAGE);
                fireEditingStopped();
            });

            editBtn.addActionListener(e -> {
                String id = (String) table.getValueAt(currentRow, 0);
                String name = (String) table.getValueAt(currentRow, 1);
                String email = (String) table.getValueAt(currentRow, 4);
                String status = (String) table.getValueAt(currentRow, 5);
                
                
                String[] nameParts = name.split(" ", 2);
                String firstName = nameParts[0];
                String lastName = nameParts.length > 1 ? nameParts[1] : "";
                
                
                if (dashboardFrame != null) {
                    try {
                        
                        Method method = dashboardFrame.getClass().getMethod("editStudent", 
                            int.class, String.class, String.class, String.class, String.class);
                        method.invoke(dashboardFrame, Integer.parseInt(id), firstName, lastName, email, status);
                    } catch (Exception ex) {
                        ex.printStackTrace();
                        JOptionPane.showMessageDialog(panel, 
                            "Error navigating to edit page: " + ex.getMessage(), 
                            "Error", 
                            JOptionPane.ERROR_MESSAGE);
                    }
                } else {
                    JOptionPane.showMessageDialog(panel, 
                        "Edit page for student: " + name + " (" + id + ")\n\nEdit functionality to be implemented.", 
                        "Edit Student", 
                        JOptionPane.INFORMATION_MESSAGE);
                }
                fireEditingStopped();
            });

            deleteBtn.addActionListener(e -> {
                String id = (String) table.getValueAt(currentRow, 0);
                int confirm = JOptionPane.showConfirmDialog(panel,
                        "Are you sure you want to delete student: " + id + "?",
                        "Confirm Delete", JOptionPane.YES_NO_OPTION, JOptionPane.WARNING_MESSAGE);
                if (confirm == JOptionPane.YES_OPTION) {
                    model.removeRow(currentRow);
                }
                fireEditingStopped();
            });

            panel.add(viewBtn);
            panel.add(editBtn);
            panel.add(deleteBtn);
        }

        private JButton createActionButton(String text, Color bgColor) {
            JButton btn = new JButton(text);
            btn.setFont(new Font("Segoe UI", Font.BOLD, 10));
            btn.setBackground(bgColor);
            btn.setForeground(Color.WHITE);
            btn.setPreferredSize(new Dimension(50, 28));
            btn.setBorder(BorderFactory.createEmptyBorder(4, 8, 4, 8));
            btn.setFocusPainted(false);
            btn.setOpaque(true);
            btn.setContentAreaFilled(true);
            btn.setBorderPainted(false);
            btn.setCursor(new Cursor(Cursor.HAND_CURSOR));
            return btn;
        }

        @Override
        public Component getTableCellEditorComponent(JTable table, Object value,
                boolean isSelected, int row, int column) {
            currentRow = row;
            panel.setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
            return panel;
        }

        @Override
        public Object getCellEditorValue() {
            return "actions";
        }
    }

    private void loadStudentsFromDatabase() {
        studentsf studentManager = new studentsf(thisUniversity);
        ArrayList<students> studentsList = studentManager.getAllStudents();
        
        for (students student : studentsList) {
            String fullName = student.getFisrt_name() + " " + student.getLast_name();
            String status = student.isVerification() ? "Active" : "Pending";
            String year = student.getCreated_at() != null ? student.getCreated_at().substring(0, 4) : "N/A";
            
            Object[] row = {
                String.valueOf(student.getStudent_id()),
                fullName,
                "N/A", 
                year,
                student.getEmail(),
                status,
                "actions"
            };
            tableModel.addRow(row);
        }
    }
}
