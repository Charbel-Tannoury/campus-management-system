package components;

import javax.swing.*;
import java.awt.*;

/**
 * Main Statistics Panel with Cards and News
 * 
 * A reusable dashboard component that displays statistics and news.
 * Features:
 * - 2x3 grid of statistic cards (6 total)
 * - Hardcoded sample data for demo purposes
 * - Latest news section with "View All" button
 * - Scrollable content area
 * - Light gray background (#f5f7fa)
 * 
 * Cards Displayed:
 * - Students (blue icon)
 * - Professors (green icon)
 * - Courses (purple icon)
 * - Majors (yellow icon)
 * - Faculties (orange icon)
 * - Messages (red icon)
 * 
 * Layout:
 * - Statistics grid at top (2 rows, 3 columns)
 * - 30px spacing between grid and news
 * - News section at bottom with header and items
 * - 20px padding around all content
 * 
 * Note: This appears to be a demo/template component.
 * The live StatisticsPage.java fetches real data from database.
 */
public class StatisticsPanel extends JPanel {
    
    public StatisticsPanel() {
        setLayout(new BorderLayout());
        setBackground(new Color(245, 247, 250));
        setBorder(BorderFactory.createEmptyBorder(20, 30, 20, 30));
        
        // Main content wrapper
        JPanel contentWrapper = new JPanel();
        contentWrapper.setLayout(new BoxLayout(contentWrapper, BoxLayout.Y_AXIS));
        contentWrapper.setBackground(new Color(245, 247, 250));
        
        // Statistics cards grid
        JPanel statsGrid = createStatsGrid();
        statsGrid.setMaximumSize(new Dimension(Integer.MAX_VALUE, 280));
        contentWrapper.add(statsGrid);
        
        contentWrapper.add(Box.createRigidArea(new Dimension(0, 30)));
        
        // News section
        JPanel newsSection = createNewsSection();
        contentWrapper.add(newsSection);
        
        // Wrap in scroll pane
        JScrollPane scrollPane = new JScrollPane(contentWrapper);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);
        
        add(scrollPane, BorderLayout.CENTER);
    }
    
    private JPanel createStatsGrid() {
        JPanel grid = new JPanel(new GridLayout(2, 3, 20, 20));
        grid.setBackground(new Color(245, 247, 250));
        
        // Create 6 stat cards
        grid.add(new StatCard("Students", "1", "👥", new Color(52, 152, 219)));
        grid.add(new StatCard("Professors", "3", "👨‍🏫", new Color(46, 204, 113)));
        grid.add(new StatCard("Courses", "4", "📚", new Color(155, 89, 182)));
        grid.add(new StatCard("Majors", "2", "🎓", new Color(241, 196, 15)));
        grid.add(new StatCard("Faculties", "10", "🏛", new Color(230, 126, 34)));
        grid.add(new StatCard("Messages", "10", "✉", new Color(231, 76, 60)));
        
        return grid;
    }
    
    private JPanel createNewsSection() {
        JPanel newsPanel = new JPanel();
        newsPanel.setLayout(new BoxLayout(newsPanel, BoxLayout.Y_AXIS));
        newsPanel.setBackground(new Color(245, 247, 250));
        
        // Header
        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(new Color(245, 247, 250));
        headerPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 40));
        
        JLabel titleLabel = new JLabel("Latest News");
        titleLabel.setFont(new Font("Segoe UI", Font.BOLD, 18));
        titleLabel.setForeground(new Color(44, 62, 80));
        
        JButton viewAllBtn = new JButton("View All");
        viewAllBtn.setFont(new Font("Segoe UI", Font.PLAIN, 13));
        viewAllBtn.setForeground(new Color(52, 152, 219));
        viewAllBtn.setBackground(Color.WHITE);
        viewAllBtn.setBorderPainted(false);
        viewAllBtn.setFocusPainted(false);
        viewAllBtn.setCursor(new Cursor(Cursor.HAND_CURSOR));
        
        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(viewAllBtn, BorderLayout.EAST);
        
        newsPanel.add(headerPanel);
        newsPanel.add(Box.createRigidArea(new Dimension(0, 15)));
        
        // News items
        newsPanel.add(createNewsItem("New Course Registration Opens", "2 hours ago"));
        newsPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        newsPanel.add(createNewsItem("Faculty Meeting Scheduled", "5 hours ago"));
        newsPanel.add(Box.createRigidArea(new Dimension(0, 10)));
        newsPanel.add(createNewsItem("Exam Results Published", "1 day ago"));
        
        return newsPanel;
    }
    
    private JPanel createNewsItem(String title, String time) {
        JPanel item = new JPanel(new BorderLayout());
        item.setBackground(Color.WHITE);
        item.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 3, 0, 0, new Color(52, 152, 219)),
            BorderFactory.createEmptyBorder(15, 20, 15, 20)
        ));
        item.setMaximumSize(new Dimension(Integer.MAX_VALUE, 60));
        
        JLabel titleLabel = new JLabel(title);
        titleLabel.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        titleLabel.setForeground(new Color(44, 62, 80));
        
        JLabel timeLabel = new JLabel(time);
        timeLabel.setFont(new Font("Segoe UI", Font.PLAIN, 12));
        timeLabel.setForeground(Color.GRAY);
        
        item.add(titleLabel, BorderLayout.WEST);
        item.add(timeLabel, BorderLayout.EAST);
        
        return item;
    }
}
