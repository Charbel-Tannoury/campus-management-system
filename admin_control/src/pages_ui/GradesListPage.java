package pages_ui;

import util.*;
import javax.swing.*;
import javax.swing.table.*;
import java.awt.*;
import java.awt.event.*;
import resources.gradesf;
import models.university;
import java.util.ArrayList;

/**
 * Grades List Page UI Component
 * 
 * Displays a comprehensive table of all student grades in the faculty.
 * Features:
 * - Paginated table of grade records
 * - Student information (ID, name)
 * - Course details
 * - All grade components (project, midterm, finals)
 * - Total grade calculation
 * - Pass/fail status indicators
 * - Edit button for each grade record
 * - Add grades button in header
 * - Faculty-based filtering
 * - Integration with gradesf resource manager
 * - Reflection-based navigation to edit page
 * 
 * Table Columns:
 * - Student ID
 * - Student Name
 * - Course Name
 * - Project Grade
 * - Midterm Grade
 * - First Final
 * - Second Final (retake)
 * - Total (sum of all components)
 * - Status (Pass/Fail)
 * - Actions (Edit button)
 * 
 * Data Source:
 * - Loads grades via gradesf.getGrades()
 * - Complex join: enrollment -> students, courses, grades
 * - Filtered by faculty_id
 * - Ordered by student name
 * 
 * Layout:
 * - Page header with breadcrumb and Add button
 * - White card containing the table
 * - Color-coded status indicators (green=pass, red=fail)
 */
public class GradesListPage extends JPanel {
    private university thisuniversity;
    private DefaultTableModel tableModel;
    private Object dashboardFrame;

    public GradesListPage(university university) {
        this(university, null);
    }

    public GradesListPage(university university, Object dashboardFrame) {
        this.thisuniversity = university;
        this.dashboardFrame = dashboardFrame;
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        content.add(createPageHeader("Grades List", new String[] { "Home", "Grades", "Grades List" }));
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

        JLabel titleLabel = new JLabel("Student Grades");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JPanel buttonsPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 0));
        buttonsPanel.setBackground(Color.WHITE);

        JButton addBtn = UIHelper.createPrimaryButton("+ Add Grades");
        buttonsPanel.add(addBtn);

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(buttonsPanel, BorderLayout.EAST);

        card.add(headerPanel, BorderLayout.NORTH);

        
        String[] columns = { "Grade ID", "Student ID", "Student Name", "Course", "Project", "Midterm", "Final", "Second Final", "Total", "Actions" };
        tableModel = new DefaultTableModel(columns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 9; 
            }
        };

        JTable table = UIHelper.createStyledTable(new Object[0][0], columns);
        table.setModel(tableModel);
        
        loadGradesFromDatabase();

        
        table.getColumnModel().getColumn(0).setPreferredWidth(80);  
        table.getColumnModel().getColumn(1).setPreferredWidth(100); 
        table.getColumnModel().getColumn(2).setPreferredWidth(150); 
        table.getColumnModel().getColumn(3).setPreferredWidth(120); 
        table.getColumnModel().getColumn(4).setPreferredWidth(80);  
        table.getColumnModel().getColumn(5).setPreferredWidth(80);  
        table.getColumnModel().getColumn(6).setPreferredWidth(80);  
        table.getColumnModel().getColumn(7).setPreferredWidth(100); 
        table.getColumnModel().getColumn(8).setPreferredWidth(80);  
        table.getColumnModel().getColumn(9).setPreferredWidth(100); 

        
        table.getColumnModel().getColumn(8).setCellRenderer(new DefaultTableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                String totalStr = value != null ? value.toString() : "0";
                JLabel label = new JLabel(totalStr);
                label.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 14));
                label.setHorizontalAlignment(SwingConstants.CENTER);
                label.setOpaque(true);

                
                Color bgColor;
                try {
                    double total = Double.parseDouble(totalStr);
                    if (total >= 90) {
                        bgColor = new Color(198, 246, 213);
                        label.setForeground(new Color(39, 103, 73));
                    } else if (total >= 80) {
                        bgColor = new Color(198, 246, 246);
                        label.setForeground(new Color(39, 73, 103));
                    } else if (total >= 70) {
                        bgColor = new Color(254, 243, 199);
                        label.setForeground(new Color(146, 64, 14));
                    } else {
                        bgColor = new Color(254, 215, 215);
                        label.setForeground(new Color(155, 28, 28));
                    }
                } catch (NumberFormatException e) {
                    bgColor = Color.LIGHT_GRAY;
                    label.setForeground(Color.BLACK);
                }

                label.setBackground(bgColor);
                label.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));

                JPanel panel = new JPanel(new FlowLayout(FlowLayout.CENTER));
                panel.setBackground(row % 2 == 0 ? Color.WHITE : AppColors.LIGHT);
                if (isSelected)
                    panel.setBackground(new Color(102, 126, 234, 30));
                panel.add(label);
                return panel;
            }
        });

        
        table.getColumnModel().getColumn(9).setCellRenderer(new ActionButtonRenderer());
        table.getColumnModel().getColumn(9).setCellEditor(new ActionButtonEditor(table, tableModel));
        table.getColumnModel().getColumn(9).setPreferredWidth(120);

        JScrollPane tableScroll = new JScrollPane(table);
        tableScroll.setBorder(BorderFactory.createEmptyBorder());

        JPanel bodyPanel = new JPanel(new BorderLayout());
        bodyPanel.setBackground(Color.WHITE);
        bodyPanel.add(tableScroll, BorderLayout.CENTER);

        card.add(bodyPanel, BorderLayout.CENTER);

        return card;
    }

    
    class ActionButtonRenderer extends JPanel implements TableCellRenderer {
        private JButton viewBtn, editBtn;

        public ActionButtonRenderer() {
            setLayout(new FlowLayout(FlowLayout.CENTER, 5, 5));
            setOpaque(true);

            viewBtn = createActionButton("View", new Color(0, 102, 204));
            editBtn = createActionButton("Edit", new Color(204, 102, 0));

            add(viewBtn);
            add(editBtn);
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
        private JButton viewBtn, editBtn;
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

            viewBtn.addActionListener(e -> {
                String gradeId = table.getValueAt(currentRow, 0).toString();
                String studentId = table.getValueAt(currentRow, 1).toString();
                String studentName = (String) table.getValueAt(currentRow, 2);
                String course = (String) table.getValueAt(currentRow, 3);
                String project = table.getValueAt(currentRow, 4).toString();
                String midterm = table.getValueAt(currentRow, 5).toString();
                String firstFinal = table.getValueAt(currentRow, 6).toString();
                String secondFinal = table.getValueAt(currentRow, 7).toString();
                String total = table.getValueAt(currentRow, 8).toString();
                
                String details = "Grade ID: " + gradeId + "\n" +
                                "Student ID: " + studentId + "\n" +
                                "Student Name: " + studentName + "\n" +
                                "Course: " + course + "\n" +
                                "Project: " + project + "\n" +
                                "Midterm: " + midterm + "\n" +
                                "First Final: " + firstFinal + "\n" +
                                "Second Final: " + secondFinal + "\n" +
                                "Total: " + total;
                
                JOptionPane.showMessageDialog(panel, details, "Grade Details",
                        JOptionPane.INFORMATION_MESSAGE);
                fireEditingStopped();
            });

            editBtn.addActionListener(e -> {
                String gradeId = table.getValueAt(currentRow, 0).toString();
                String studentId = table.getValueAt(currentRow, 1).toString();
                String studentName = (String) table.getValueAt(currentRow, 2);
                String course = (String) table.getValueAt(currentRow, 3);
                String project = table.getValueAt(currentRow, 4).toString();
                String midterm = table.getValueAt(currentRow, 5).toString();
                String firstFinal = table.getValueAt(currentRow, 6).toString();
                String secondFinal = table.getValueAt(currentRow, 7).toString();
                String total = table.getValueAt(currentRow, 8).toString();
                
                
                if (dashboardFrame != null) {
                    try {
                        
                        java.lang.reflect.Method method = dashboardFrame.getClass().getMethod("editGrade", 
                            int.class, String.class, String.class, String.class, 
                            String.class, String.class, String.class, String.class, String.class);
                        method.invoke(dashboardFrame, Integer.parseInt(gradeId), studentId, studentName, course, 
                            project, midterm, firstFinal, secondFinal, total);
                    } catch (Exception ex) {
                        ex.printStackTrace();
                        JOptionPane.showMessageDialog(panel, 
                            "Error navigating to edit page: " + ex.getMessage(), 
                            "Error", 
                            JOptionPane.ERROR_MESSAGE);
                    }
                } else {
                    JOptionPane.showMessageDialog(panel, 
                        "Edit functionality for grade: " + studentName + " - " + course + " (ID: " + gradeId + ")\n\nEdit page to be implemented.", 
                        "Edit Grade", 
                        JOptionPane.INFORMATION_MESSAGE);
                }
                fireEditingStopped();
            });

            panel.add(viewBtn);
            panel.add(editBtn);
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

    private void loadGradesFromDatabase() {
        System.out.println("Loading grades from database for faculty_id: " + thisuniversity.getFaculty_id());
        
        gradesf gradesResource = new gradesf(thisuniversity);
        ArrayList<Object[]> gradesList = gradesResource.getAllGrades();
        
        System.out.println("Retrieved " + gradesList.size() + " grades");
        
        
        tableModel.setRowCount(0);
        
        
        for (Object[] grade : gradesList) {
            
            
            
            
            
            
            
            
            
            
            Object[] rowData = {
                grade[0],  
                grade[1],  
                grade[2],  
                grade[3],  
                grade[4] != null ? grade[4] : "0",  
                grade[5] != null ? grade[5] : "0",  
                grade[6] != null ? grade[6] : "0",  
                grade[7] != null ? grade[7] : "0",  
                grade[8] != null ? grade[8].toString() : "0",  
                ""  
            };
            
            tableModel.addRow(rowData);
            System.out.println("Added grade: " + grade[0] + " - " + grade[2] + " - " + grade[3] + " - Total: " + grade[8]);
        }
    }
}