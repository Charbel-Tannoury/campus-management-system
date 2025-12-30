package pages_ui;

import components.*;
import models.university;
import resources.getstats;
import util.*;
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.sql.*;
import java.util.*;
import resources.*;

/**
 * Statistics Dashboard Page
 * 
 * The main dashboard/home page displaying key metrics and latest news.
 * Features:
 * - Six statistical cards showing key metrics:
 *   1. Total Students
 *   2. Total Professors (Doctors)
 *   3. Total Courses
 *   4. Total Majors
 *   5. Total Faculties
 *   6. Total Messages
 * - Latest news/announcements section
 * - Real-time data fetching from database
 * - Color-coded stat cards with icons
 * - Breadcrumb navigation
 * - Scrollable content area
 * 
 * Data Sources:
 * - getstats resource manager for all statistics
 * - showMails (via getstats) for news/announcements
 * - Faculty-filtered where applicable
 * 
 * Layout:
 * - Page header with breadcrumb
 * - 2x3 grid of statistic cards
 * - News section below statistics
 * - All wrapped in scrollable panel
 * 
 * Used as: Default landing page after login
 */
public class StatisticsPage extends JPanel {
    private university thisuniversity;
    private ArrayList<models.mails> mailsList;

    public StatisticsPage(university thisuniversity) {
        this.thisuniversity = thisuniversity;
        this.mailsList = new getstats(thisuniversity).getMails();
        setLayout(new BorderLayout());
        setBackground(AppColors.BG_PAGE);

        
        JPanel content = new JPanel();
        content.setLayout(new BoxLayout(content, BoxLayout.Y_AXIS));
        content.setBackground(AppColors.BG_PAGE);
        content.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));

        
        content.add(createPageHeader());
        content.add(Box.createRigidArea(new Dimension(0, 25)));

        
        content.add(createStatsGrid());
        content.add(Box.createRigidArea(new Dimension(0, 30)));

        
        content.add(createNewsSection());

        
        JScrollPane scrollPane = new JScrollPane(content);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);

        add(scrollPane, BorderLayout.CENTER);
    }

    private JPanel createPageHeader() {
        JPanel header = new JPanel(new BorderLayout());
        header.setBackground(AppColors.BG_PAGE);
        header.setMaximumSize(new Dimension(Integer.MAX_VALUE, 60));

        JLabel title = new JLabel("Statistics Dashboard");
        title.setFont(AppFonts.TITLE);
        title.setForeground(AppColors.TEXT_DARK);

        
        JPanel breadcrumb = new JPanel(new FlowLayout(FlowLayout.LEFT, 8, 0));
        breadcrumb.setBackground(AppColors.BG_PAGE);

        JLabel home = new JLabel("Home");
        home.setFont(AppFonts.BODY);
        home.setForeground(AppColors.SECONDARY);
        home.setCursor(new Cursor(Cursor.HAND_CURSOR));

        JLabel sep = new JLabel("/");
        sep.setFont(AppFonts.BODY);
        sep.setForeground(AppColors.TEXT_MUTED);

        JLabel current = new JLabel("Statistics");
        current.setFont(AppFonts.BODY);
        current.setForeground(AppColors.TEXT_MUTED);

        breadcrumb.add(home);
        breadcrumb.add(sep);
        breadcrumb.add(current);

        JPanel headerContent = new JPanel();
        headerContent.setLayout(new BoxLayout(headerContent, BoxLayout.Y_AXIS));
        headerContent.setBackground(AppColors.BG_PAGE);
        headerContent.add(title);
        headerContent.add(Box.createRigidArea(new Dimension(0, 0)));
        headerContent.add(breadcrumb);

        header.add(headerContent, BorderLayout.WEST);
        return header;
    }

    private JPanel createStatsGrid() {
        JPanel grid = new JPanel(new GridLayout(2, 3, 25, 25));
        grid.setBackground(AppColors.BG_PAGE);
        grid.setMaximumSize(new Dimension(Integer.MAX_VALUE, 400));

        int stdnum = new getstats(thisuniversity).getTotalStudents();
        int drnum = new getstats(thisuniversity).getTotalDoctors();
        int coursenum = new getstats(thisuniversity).getTotalCourses();
        int majornum = new getstats(thisuniversity).getTotalMajors();
        int facultynum = new getstats(thisuniversity).getTotalFaculties(); 
        int mailnum = new getstats(thisuniversity).getTotalMails();
        grid.add(new StatCard("Number of Students", Integer.toString(stdnum), "👥", AppColors.PRIMARY));
        grid.add(new StatCard("Number of Professors", Integer.toString(drnum), "👨‍🏫", AppColors.SUCCESS));
        grid.add(new StatCard("Number of Courses", Integer.toString(coursenum), "📚", AppColors.WARNING));
        grid.add(new StatCard("Number of Majors", Integer.toString(majornum), "📋", AppColors.INFO));
        grid.add(new StatCard("Number of Faculties", Integer.toString(facultynum), "🏛", AppColors.DANGER));
        grid.add(new StatCard("New Messages", Integer.toString(mailnum), "✉", AppColors.PRIMARY));

        return grid;
    }

    private JPanel createNewsSection() {
        JPanel newsPanel = new JPanel();
        newsPanel.setLayout(new BoxLayout(newsPanel, BoxLayout.Y_AXIS));
        newsPanel.setBackground(Color.WHITE);
        newsPanel.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(new Color(0, 0, 0, 10)),
                BorderFactory.createEmptyBorder(0, 0, 0, 0)));
        newsPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 600));

        
        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(Color.WHITE);
        headerPanel.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER),
                BorderFactory.createEmptyBorder(20, 25, 20, 25)));

        JLabel titleLabel = new JLabel("Latest News");
        titleLabel.setFont(AppFonts.SUBHEADING);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JButton viewAllBtn = UIHelper.createPrimaryButton("View All");
        viewAllBtn.setFont(new Font(AppFonts.FAMILY, Font.PLAIN, 13));
        viewAllBtn.setBorder(BorderFactory.createEmptyBorder(8, 15, 8, 15));

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(viewAllBtn, BorderLayout.EAST);

        newsPanel.add(headerPanel);

        
        JPanel newsBody = new JPanel();
        newsBody.setLayout(new BoxLayout(newsBody, BoxLayout.Y_AXIS));
        newsBody.setBackground(Color.WHITE);
        newsBody.setBorder(BorderFactory.createEmptyBorder(15, 25, 25, 25));
        
        
        if (mailsList != null && !mailsList.isEmpty()) {
            int displayCount = mailsList.size(); 
            int mailnum = mailsList.size();
            if (mailnum > 8) {
                mailnum = 8; 
            }
            for (int i = 0; i < mailnum; i++) {
                models.mails mail = mailsList.get(i);
                newsBody.add(createNewsItem(mail.getMail_info(), mail.getCreated_at() != null ? mail.getCreated_at() : "Recently"));
                if (i < displayCount - 1) {
                    newsBody.add(Box.createRigidArea(new Dimension(0, 15)));
                }
            }
        } else {
            
            newsBody.add(createNewsItem("No new messages", "Today"));
        }

        newsPanel.add(newsBody);

        return newsPanel;
    }

    private JPanel createNewsItem(String title, String time) {
        JPanel item = new JPanel(new BorderLayout());
        item.setBackground(Color.WHITE);
        item.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 3, 0, 0, AppColors.SECONDARY),
                BorderFactory.createEmptyBorder(15, 20, 15, 20)));
        item.setMaximumSize(new Dimension(Integer.MAX_VALUE, 55));

        JLabel titleLabel = new JLabel(title);
        titleLabel.setFont(AppFonts.BODY);
        titleLabel.setForeground(AppColors.TEXT_DARK);

        JLabel timeLabel = new JLabel(time);
        timeLabel.setFont(AppFonts.SMALL);
        timeLabel.setForeground(AppColors.TEXT_MUTED);

        item.add(titleLabel, BorderLayout.WEST);
        item.add(timeLabel, BorderLayout.EAST);

        item.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseEntered(java.awt.event.MouseEvent evt) {
                item.setBackground(AppColors.LIGHT);
                item.setCursor(new Cursor(Cursor.HAND_CURSOR));
            }

            public void mouseExited(java.awt.event.MouseEvent evt) {
                item.setBackground(Color.WHITE);
            }
        });

        return item;
    }
}
