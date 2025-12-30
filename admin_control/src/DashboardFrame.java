import components.*;
import pages_ui.*;
import util.*;
import javax.swing.*;
import java.awt.*;
import java.nio.file.Paths;

import models.*;

/**
 * Main Dashboard Frame for the Admin Control Panel
 * 
 * This class creates the primary application window containing:
 * - Fixed sidebar with navigation menu
 * - Top bar with user information
 * - Content area with card-based page switching
 * - Multiple pages: Statistics, Students, Professors, Courses, Grades, Mail
 * 
 * Uses CardLayout for seamless page transitions without closing windows.
 * Implements NavigationListener for sidebar menu interactions.
 */
public class DashboardFrame extends JFrame implements Sidebar.NavigationListener {
    
    // Content panel with CardLayout for page switching
    private JPanel contentPanel;
    private CardLayout cardLayout;
    // Top navigation bar
    private TopBar topBar;
    // Current university/faculty context
    private university thisuniversity;
    // Logged-in employee/admin
    private employee thisemployee;
    // University logo for branding
    private ImageIcon unilogo;
    // Page instances for direct access when needed
    private EditStudentPage editStudentPage;
    private EditProfessorPage editProfessorPage;
    private EditGradePage editGradePage;
    
    /**
     * Constructor creates and initializes the dashboard frame.
     * Sets up the entire UI including sidebar, top bar, and all content pages.
     * 
     * @param uni University/faculty context for the session
     * @param emp Logged-in employee/administrator
     * @param unilogo University logo for display
     */
    public DashboardFrame(university uni, employee emp, ImageIcon unilogo) {
        this.thisuniversity = uni;
        this.thisemployee = emp;
        this.unilogo = unilogo;
        setTitle("CCTJ - University - Admin Dashboard");
        
        // Load and set window icon
        ImageIcon raw = new ImageIcon(Paths.get("admin_control","src", "img","logo.png").toString());
        Image frameIcon = raw.getImage().getScaledInstance(64, 64, Image.SCALE_SMOOTH);
        setIconImage(frameIcon);
        
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(1400, 900);
        setMinimumSize(new Dimension(1200, 700));
        setLocationRelativeTo(null);  // Center on screen
        
        // Return to login screen when dashboard is closed
        addWindowListener(new java.awt.event.WindowAdapter() {
            @Override
            public void windowClosed(java.awt.event.WindowEvent e) {
                App.startApp();
            }
        });
        
        // Use BorderLayout for main window structure
        setLayout(new BorderLayout());
        
        // Create sidebar with navigation menu
        Sidebar sidebar = new Sidebar(this, thisuniversity, unilogo);
        
        // Wrap sidebar in scrollpane for overflow handling
        JScrollPane sidebarScrollPane = new JScrollPane(sidebar);
        sidebarScrollPane.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
        sidebarScrollPane.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED);
        sidebarScrollPane.setBorder(null);
        sidebarScrollPane.getVerticalScrollBar().setUnitIncrement(16);  // Smooth scrolling
        sidebarScrollPane.setPreferredSize(new Dimension(260, 0));  // Fixed width sidebar
        
        add(sidebarScrollPane, BorderLayout.WEST);
        
        
        JPanel mainPanel = new JPanel(new BorderLayout());
        mainPanel.setBackground(AppColors.BG_PAGE);
        
        
        topBar = new TopBar(thisuniversity, thisemployee);
        mainPanel.add(topBar, BorderLayout.NORTH);
        
        
        cardLayout = new CardLayout();
        contentPanel = new JPanel(cardLayout);
        contentPanel.setBackground(AppColors.BG_PAGE);
        
        
        contentPanel.add(new StatisticsPage(thisuniversity), "statistics");
        contentPanel.add(new MailListPage(thisuniversity, this), "mail_list");
        contentPanel.add(new AddMailPage(thisuniversity), "mail_add");
        contentPanel.add(new AddProfessorPage(thisuniversity), "professors_add");
        contentPanel.add(new CoursesListPage(thisuniversity), "courses_list");
        contentPanel.add(new AddCoursePage(thisuniversity), "courses_add");
        contentPanel.add(new StudentsListPage(thisuniversity, this), "students_list");
        editStudentPage = new EditStudentPage(thisuniversity, this);
        contentPanel.add(editStudentPage, "students_edit"); 
        contentPanel.add(new ProfessorsListPage(thisuniversity, this), "professors_list");
        editProfessorPage = new EditProfessorPage(thisuniversity, this);
        contentPanel.add(editProfessorPage, "professors_edit");
        contentPanel.add(new GradesListPage(thisuniversity, this), "grades_list");
        editGradePage = new EditGradePage(thisuniversity, this);
        contentPanel.add(editGradePage, "grades_edit");
        contentPanel.add(new AddCoursePage(thisuniversity), "grades_add"); 
        
        mainPanel.add(contentPanel, BorderLayout.CENTER);
        
        add(mainPanel, BorderLayout.CENTER);
        
        
        cardLayout.show(contentPanel, "statistics");
    }
    
    @Override
    public void onNavigate(String page) {
        
        cardLayout.show(contentPanel, page);
        
        
        String[] breadcrumb;
        switch (page) {
            case "statistics":
                breadcrumb = new String[]{"Home", "Statistics"};
                break;
            case "mail_list":
                breadcrumb = new String[]{"Home", "Mail", "Messages List"};
                break;
            case "mail_add":
                breadcrumb = new String[]{"Home", "Mail", "Add Message"};
                break;
            case "courses_list":
                breadcrumb = new String[]{"Home", "Courses", "Courses List"};
                break;
            case "courses_add":
                breadcrumb = new String[]{"Home", "Courses", "Add Course"};
                break;
            case "students_list":
                breadcrumb = new String[]{"Home", "Students", "Students List"};
                break;
            case "professors_list":
                breadcrumb = new String[]{"Home", "Professors", "Professors List"};
                break;
            case "grades_list":
                breadcrumb = new String[]{"Home", "Grades", "Grades List"};
                break;
            default:
                breadcrumb = new String[]{"Home", page};
        }
        topBar.setPageInfo(page, breadcrumb);
    }
    
    
    public void editStudent(int studentId, String firstName, String lastName, String email, String status) {
        editStudentPage.loadStudentData(studentId, firstName, lastName, email, status);
        onNavigate("students_edit");
    }
    
    
    public void editProfessor(int professorId, String firstName, String lastName, String email, String phone, String status, boolean fixed) {
        editProfessorPage.loadProfessorData(professorId, firstName, lastName, email, phone, status, fixed);
        onNavigate("professors_edit");
    }
    
    
    public void editGrade(int gradeId, String studentId, String studentName, String courseName, 
                         String projectGrade, String midGrade, String firstFinal, String secondFinal, String total) {
        editGradePage.loadGradeData(gradeId, studentId, studentName, courseName, projectGrade, midGrade, firstFinal, secondFinal, total);
        onNavigate("grades_edit");
    }
}