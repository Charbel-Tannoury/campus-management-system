package components;

import util.*;
import javax.swing.*;
import java.awt.*;
import models.*;

/**
 * TopBar Header Component
 * 
 * A custom JPanel that displays the application header bar at the top of the dashboard.
 * Features:
 * - User profile display with avatar icon
 * - Admin name and faculty information
 * - Breadcrumb navigation display (reserved for future use)
 * - Clean white background contrasting with sidebar
 * - Right-aligned user information section
 * 
 * Visual Design:
 * - 70px fixed height
 * - White background with subtle bottom border
 * - Profile section with avatar emoji and user details
 * - Faculty name and branch number display
 */
public class TopBar extends JPanel {
    
    // Current page title (reserved for breadcrumb display)
    private String currentPage = "Statistics";
    
    // Breadcrumb path array (reserved for navigation path display)
    private String[] breadcrumb = {"Home", "Statistics"};
    
    // University and employee objects for displaying user/faculty info
    private university thisuniversity;
    private employee thisemployee;
    
    /**
     * Constructor for TopBar
     * @param thisuniversity University object containing faculty information
     * @param thisemployee Employee object containing logged-in admin details
     */
    public TopBar(university thisuniversity, employee thisemployee) {
        this.thisuniversity = thisuniversity;
        this.thisemployee = thisemployee;
        setLayout(new BorderLayout());
        setBackground(Color.WHITE);
        setPreferredSize(new Dimension(0, 70));
        setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 0, 1, 0, AppColors.BORDER),
            BorderFactory.createEmptyBorder(0, 30, 0, 30)
        ));
        

        JPanel leftPanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 15, 15));
        leftPanel.setBackground(Color.WHITE);
        
        JPanel rightPanel = new JPanel(new FlowLayout(FlowLayout.RIGHT, 10, 10));
        rightPanel.setBackground(Color.WHITE);
        
        JLabel avatar = new JLabel("👤");
        avatar.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 24));
        avatar.setOpaque(true);
        avatar.setBackground(AppColors.SECONDARY);
        avatar.setForeground(Color.WHITE);
        avatar.setBorder(BorderFactory.createEmptyBorder(8, 10, 8, 10));
        

        JPanel userInfo = new JPanel();
        userInfo.setLayout(new BoxLayout(userInfo, BoxLayout.Y_AXIS));
        userInfo.setBackground(Color.WHITE);
        
        JLabel userName = new JLabel("Admin: " + thisemployee.getFirst_name() + " " + thisemployee.getLast_name());
        userName.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 14));
        userName.setForeground(AppColors.TEXT_DARK);
        
        JLabel userRole = new JLabel("CCTJ " + thisuniversity.getFaculty_name()+" - " +thisuniversity.getFaculty_number());
        userRole.setFont(AppFonts.SMALL);
        userRole.setForeground(AppColors.TEXT_MUTED);
        
        userInfo.add(userName);
        userInfo.add(userRole);
        
        JPanel profileWrapper = new JPanel(new FlowLayout(FlowLayout.RIGHT, 12, 0));
        profileWrapper.setBackground(Color.WHITE);
        profileWrapper.setBorder(BorderFactory.createEmptyBorder(8, 15, 8, 15));
        profileWrapper.add(avatar);
        profileWrapper.add(userInfo);
        profileWrapper.setCursor(new Cursor(Cursor.HAND_CURSOR));
        
        rightPanel.add(profileWrapper);
        
        add(leftPanel, BorderLayout.WEST);
        add(rightPanel, BorderLayout.EAST);
    }
    
    public void setPageInfo(String page, String[] breadcrumbPath) {
        this.currentPage = page;
        this.breadcrumb = breadcrumbPath;
        repaint();
    }
}
