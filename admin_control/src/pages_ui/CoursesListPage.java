package pages_ui;

import util.*;
import javax.swing.*;
import javax.swing.table.*;
import java.awt.*;
import java.awt.event.*;
import resources.coursesf;
import models.*;
import java.util.ArrayList;

/**
 * Courses List Page UI Component
 * 
 * Displays a table of all courses offered in the faculty.
 * Features:
 * - Paginated table of course records
 * - Course code, name, description, credits display
 * - Major and semester associations
 * - Add new course button in header
 * - Edit button for each course
 * - Faculty-based filtering
 * - Integration with coursesf resource manager
 * - Breadcrumb navigation
 * 
 * Table Columns:
 * - Course Code (e.g., CS101)
 * - Course Name (e.g., Introduction to Computer Science)
 * - Major Name
 * - Semester
 * - Credits
 * - Actions (Edit button)
 * 
 * Data Source:
 * - Loads courses via coursesf.getCourses()
 * - Filtered by faculty_id
 * - Includes major and semester information via joins
 * 
 * Layout:
 * - Page header with breadcrumb and Add button
 * - White card containing the table
 * - Custom styled table with alternating row colors
 */
public class CoursesListPage extends JPanel {

    private DefaultTableModel tableModel;
    private university thisUniversity;

    public CoursesListPage(university thisUniversity) {
        this.thisUniversity = thisUniversity;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Courses List", new String[] { "Home", "Courses", "Courses List" }));
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

        JLabel titleLabel = new JLabel("All Courses");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);

        JButton addBtn = UIHelper.createPrimaryButton("+ Add New Course");
        addBtn.addActionListener(e -> {
            JOptionPane.showMessageDialog(this, "Add New Course clicked!", "Action", JOptionPane.INFORMATION_MESSAGE);
        });
        buttonsPanel.add(addBtn);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(buttonsPanel, BorderLayout.EAST);

        card.add(headerPanel, BorderLayout.NORTH);

        
        String[] columns = { "Course ID", "Course Name", "Course Details", "Actions" };
        Object[][] data = loadCoursesFromDatabase();

        tableModel = new DefaultTableModel(data, columns) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 3; //
            }
        };

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
                else if (column == 1)
                    setForeground(AppColors.INFO);
                else if (column == 2)
                    setForeground(AppColors.TEXT_DARK);
                else
                    setForeground(AppColors.TEXT_DARK);

                return c;
            }
        });

        
        table.getColumnModel().getColumn(3).setCellRenderer(new ActionButtonRenderer());
        table.getColumnModel().getColumn(3).setCellEditor(new ActionButtonEditor(table));
        table.getColumnModel().getColumn(3).setPreferredWidth(120);

        
        table.getColumnModel().getColumn(0).setPreferredWidth(100);
        table.getColumnModel().getColumn(1).setPreferredWidth(200);
        table.getColumnModel().getColumn(2).setPreferredWidth(300);

        JScrollPane tableScroll = new JScrollPane(table);
        tableScroll.setBorder(BorderFactory.createEmptyBorder());

        JPanel bodyPanel = new JPanel(new BorderLayout());
        bodyPanel.setBackground(Color.WHITE);
        bodyPanel.add(tableScroll, BorderLayout.CENTER);

        card.add(bodyPanel, BorderLayout.CENTER);

        return card;
    }

    private Object[][] loadCoursesFromDatabase() {
        try {
            coursesf courseGetter = new coursesf(thisUniversity);
            ArrayList<courses> coursesList = courseGetter.getAllCourses();
            
            Object[][] data = new Object[coursesList.size()][4];
            for (int i = 0; i < coursesList.size(); i++) {
                courses course = coursesList.get(i);
                data[i][0] = course.getCourse_id();
                data[i][1] = course.getCourse_name();
                data[i][2] = course.getCourse_details();
                data[i][3] = "actions";
            }
            return data;
        } catch (Exception e) {
            e.printStackTrace();
            
            return new Object[0][4];
        }
    }

    public void refreshCoursesList() {
        Object[][] newData = loadCoursesFromDatabase();
        tableModel.setRowCount(0); 
        for (Object[] row : newData) {
            tableModel.addRow(row);
        }
        tableModel.fireTableDataChanged();
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
        private int currentRow;

        public ActionButtonEditor(JTable table) {
            this.table = table;
            panel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
            panel.setOpaque(true);

            viewBtn = createActionButton("View", new Color(0, 102, 204));
            editBtn = createActionButton("Edit", new Color(204, 102, 0));
            deleteBtn = createActionButton("Delete", new Color(153, 0, 0));

            viewBtn.addActionListener(e -> {
                Object courseId = table.getValueAt(currentRow, 0);
                Object courseName = table.getValueAt(currentRow, 1);
                JOptionPane.showMessageDialog(panel,
                        "Course ID: " + courseId + "\nCourse Name: " + courseName,
                        "View Course", JOptionPane.INFORMATION_MESSAGE);
                fireEditingStopped();
            });

            editBtn.addActionListener(e -> {
                Object courseId = table.getValueAt(currentRow, 0);
                Object courseName = table.getValueAt(currentRow, 1);
                JOptionPane.showMessageDialog(panel,
                        "Edit functionality will be implemented for:\nCourse ID: " + courseId + "\nCourse Name: "
                                + courseName,
                        "Edit Course", JOptionPane.INFORMATION_MESSAGE);
                fireEditingStopped();
            });

            deleteBtn.addActionListener(e -> {
                Object courseId = table.getValueAt(currentRow, 0);
                Object courseName = table.getValueAt(currentRow, 1);
                int confirm = JOptionPane.showConfirmDialog(panel,
                        "Are you sure you want to delete course:\nID: " + courseId + "\nName: " + courseName + "?",
                        "Confirm Delete", JOptionPane.YES_NO_OPTION, JOptionPane.WARNING_MESSAGE);
                if (confirm == JOptionPane.YES_OPTION) {
                    
                    
                    tableModel.removeRow(currentRow);
                    JOptionPane.showMessageDialog(panel,
                            "Course removed from display.\nNote: Database deletion not yet implemented.",
                            "Delete", JOptionPane.INFORMATION_MESSAGE);
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
}
