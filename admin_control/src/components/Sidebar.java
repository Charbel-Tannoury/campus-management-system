package components;

import util.*;
import javax.swing.*;

import models.*;
import pages_ui.*;

import java.awt.*;
import java.awt.event.*;
import java.util.HashMap;
import java.util.Map;

/**
 * Sidebar Navigation Component
 * 
 * A custom JPanel that provides the main navigation sidebar for the admin dashboard.
 * Features:
 * - Expandable/collapsible menu items with submenus
 * - Active state tracking and visual feedback
 * - University logo and information display
 * - Hierarchical menu structure (Mail, Courses, Students, Professors, Grades)
 * - Logout functionality
 * - Navigation callback system using NavigationListener interface
 * 
 * Visual Design:
 * - Dark red gradient background matching admin panel theme
 * - White text with hover effects
 * - Darker submenu backgrounds for visual hierarchy
 * - Smooth transitions and interactive feedback
 */
public class Sidebar extends JPanel {
    
    // Navigation callback listener for handling menu clicks
    private NavigationListener navListener;
    
    // Maps to track submenu panels and menu item panels by their IDs
    private Map<String, JPanel> submenuPanels = new HashMap<>();
    private Map<String, JPanel> menuItemPanels = new HashMap<>();
    
    // Currently active menu item ID (used for highlighting)
    private String activeMenu = "statistics";
    
    // University and employee data for header display
    private university thisuniversity;
    private employee thisemployee;
    private ImageIcon unilogo;
    
    // Color constants for visual hierarchy
    private static final Color SUBMENU_BG = new Color(127, 29, 29); // Darker red - matches gradient end
    private static final Color SUBMENU_ITEM_BG = new Color(100, 23, 23); // Even darker red for submenu items
    
    public interface NavigationListener {
        void onNavigate(String page);
    }
    
    public Sidebar(NavigationListener listener, university thisuniversity, ImageIcon unilogo) {
        this.navListener = listener;
        this.thisuniversity = thisuniversity;
        this.thisemployee = thisemployee;
        this.unilogo = unilogo;
        setBackground(AppColors.SIDEBAR_BG);
        setLayout(new BoxLayout(this, BoxLayout.Y_AXIS));
        addHeader();
        addMenuWithSubmenu("mail", "📧", "Mail", new String[][]{
            {"mail_list", "Messages List"},
            {"mail_add", "Add Message"}
        });
        addMenuWithSubmenu("courses", "📚", "Courses", new String[][]{
            {"courses_list", "Courses List"},
            {"courses_add", "Add Course"}
        });
        addMenuWithSubmenu("students", "👥", "Students", new String[][]{
            {"students_list", "Students List"},
            {"students_edit", "Edit Student"}
        });
        addMenuWithSubmenu("professors", "👨‍🏫", "Professors", new String[][]{
            {"professors_list", "Professors List"},
            {"professors_add", "Add Professor"}
        });
        addMenuWithSubmenu("grades", "📊", "Grades", new String[][]{
            {"grades_list", "Grades List"},
            {"grades_add", "Add Grades"}
        });
        
        addLogoutButton();
    }

    private void addHeader() {
        JPanel headerPanel = new JPanel();
        headerPanel.setBackground(AppColors.SIDEBAR_BG);
        headerPanel.setLayout(new BoxLayout(headerPanel, BoxLayout.Y_AXIS));
        headerPanel.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(0, 0, 1, 0, new Color(255, 255, 255, 25)),
            BorderFactory.createEmptyBorder(25, 20, 25, 20)
        ));
        headerPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 300));
        
        JLabel logoLabel = new JLabel();
        logoLabel.setIcon(new ImageIcon(unilogo.getImage().getScaledInstance(180, 180, Image.SCALE_SMOOTH)));
        logoLabel.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 80));
        logoLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        logoLabel.setOpaque(true);
        logoLabel.setBackground(new Color(0,0,0,0));
        logoLabel.setBorder(BorderFactory.createEmptyBorder(10, 15, 10, 15));
        
       JPanel logoWrapper = new JPanel(new FlowLayout(FlowLayout.CENTER));
        logoWrapper.setBackground(AppColors.SIDEBAR_BG);
        logoWrapper.add(logoLabel);
        

        JLabel nameLabel = new JLabel("CCTJ - University");
        nameLabel.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 17));
        nameLabel.setForeground(Color.WHITE);
        nameLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        
        JLabel subLabel = new JLabel(thisuniversity.getFaculty_name()+" - Branch "+thisuniversity.getFaculty_number());
        subLabel.setFont(new Font(AppFonts.FAMILY, Font.PLAIN, 11));
        subLabel.setForeground(new Color(255, 255, 255, 180));
        subLabel.setAlignmentX(Component.CENTER_ALIGNMENT);
        
        headerPanel.add(logoWrapper);
        headerPanel.add(Box.createRigidArea(new Dimension(0, 12)));
        headerPanel.add(nameLabel);
        headerPanel.add(Box.createRigidArea(new Dimension(0, 3)));
        headerPanel.add(subLabel);
        
        add(headerPanel);
        logoLabel.addMouseListener(new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                setActiveMenu("statistics");
                if (navListener != null) {
                    navListener.onNavigate("statistics");
                }
            }
        });
    }
    
    private void addMenuItem(String id, String text) {
        JPanel menuItem = createMenuItemPanel(id, text, false);
        
        menuItem.addMouseListener(new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                setActiveMenu(id);
                if (navListener != null) {
                    navListener.onNavigate(id);
                }
            }
        });
        
        menuItemPanels.put(id, menuItem);
        add(menuItem);
    }
    
    private void addMenuWithSubmenu(String id, String icon, String text, String[][] subItems) {
        JPanel menuItem = createMenuItemPanel(id,  text + "  ▼", true);
        
        JPanel submenu = new JPanel();
        submenu.setLayout(new BoxLayout(submenu, BoxLayout.Y_AXIS));
        submenu.setBackground(SUBMENU_BG);
        submenu.setOpaque(true);
        submenu.setVisible(false);
        
        int submenuHeight = subItems.length * 40;
        submenu.setPreferredSize(new Dimension(260, submenuHeight));
        submenu.setMaximumSize(new Dimension(Integer.MAX_VALUE, submenuHeight));
        submenu.setMinimumSize(new Dimension(260, submenuHeight));
        
        for (String[] item : subItems) {
            JPanel subItem = createSubmenuItem(item[0], item[1]);
            submenu.add(subItem);
        }
        
        submenuPanels.put(id, submenu);
        menuItemPanels.put(id, menuItem);
        
        menuItem.addMouseListener(new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                toggleSubmenu(id);
            }
        });
        
        add(menuItem);
        add(submenu);
    }
    
    private JPanel createMenuItemPanel(String id, String text, boolean hasSubmenu) {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(true);
        panel.setBackground(id.equals(activeMenu) ? AppColors.SECONDARY : AppColors.SIDEBAR_BG);
        panel.setPreferredSize(new Dimension(260, 48));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 48));
        panel.setMinimumSize(new Dimension(260, 48));
        
        if (id.equals(activeMenu)) {
            panel.setBorder(BorderFactory.createMatteBorder(0, 4, 0, 0, Color.WHITE));
        } else {
            panel.setBorder(BorderFactory.createEmptyBorder(0, 0, 0, 0));
        }
        
        JLabel label = new JLabel(text);
        label.setFont(AppFonts.MENU_ITEM);
        label.setForeground(new Color(255, 255, 255, 230));
        label.setBorder(BorderFactory.createEmptyBorder(0, 20, 0, 20));
        
        panel.add(label, BorderLayout.CENTER);
        
        final String menuId = id;
        panel.addMouseListener(new MouseAdapter() {
            public void mouseEntered(MouseEvent e) {
                if (!menuId.equals(activeMenu)) {
                    panel.setBackground(AppColors.SIDEBAR_HOVER);
                }
                panel.setCursor(new Cursor(Cursor.HAND_CURSOR));
            }
            public void mouseExited(MouseEvent e) {
                if (!menuId.equals(activeMenu)) {
                    panel.setBackground(AppColors.SIDEBAR_BG);
                }
            }
        });
        
        return panel;
    }
    
    private JPanel createSubmenuItem(String id, String text) {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(true);
        panel.setBackground(SUBMENU_ITEM_BG);
        panel.setPreferredSize(new Dimension(260, 40));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 40));
        panel.setMinimumSize(new Dimension(260, 40));
        
        JLabel label = new JLabel(text);
        label.setFont(AppFonts.SUBMENU_ITEM);
        label.setForeground(new Color(255, 255, 255, 200));
        label.setBorder(BorderFactory.createEmptyBorder(0, 50, 0, 20));
        
        panel.add(label, BorderLayout.CENTER);
        
        panel.addMouseListener(new MouseAdapter() {
            public void mouseEntered(MouseEvent e) {
                panel.setBackground(AppColors.SIDEBAR_HOVER);
                panel.setCursor(new Cursor(Cursor.HAND_CURSOR));
            }
            public void mouseExited(MouseEvent e) {
                panel.setBackground(SUBMENU_ITEM_BG);
            }
            public void mouseClicked(MouseEvent e) {
                setActiveMenu(id);
                if (navListener != null) {
                    navListener.onNavigate(id);
                }
            }
        });
        
        return panel;
    }
    
    private void toggleSubmenu(String menuId) {
        JPanel submenu = submenuPanels.get(menuId);
        if (submenu != null) {
            submenu.setVisible(!submenu.isVisible());
            revalidate();
            repaint();
        }
    }
    
    public void setActiveMenu(String menuId) {
        String parentMenu = menuId;
        if (menuId.contains("_")) {
            String prefix = menuId.substring(0, menuId.indexOf("_"));
            if (menuItemPanels.containsKey(prefix)) {
                parentMenu = prefix;
            }
        }
        
        // Reset all menu items
        for (Map.Entry<String, JPanel> entry : menuItemPanels.entrySet()) {
            entry.getValue().setBackground(AppColors.SIDEBAR_BG);
            entry.getValue().setBorder(BorderFactory.createEmptyBorder());
        }
        
        activeMenu = parentMenu;
        JPanel activePanel = menuItemPanels.get(parentMenu);
        if (activePanel != null) {
            activePanel.setBackground(AppColors.SECONDARY);
            activePanel.setBorder(BorderFactory.createMatteBorder(0, 4, 0, 0, Color.WHITE));
        }
        
        repaint();
    }
    
    private void addLogoutButton() {
        JPanel logoutPanel = new JPanel(new BorderLayout());
        logoutPanel.setOpaque(true);
        logoutPanel.setBackground(new Color(220, 38, 38)); // Red background
        logoutPanel.setPreferredSize(new Dimension(260, 55));
        logoutPanel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 55));
        logoutPanel.setMinimumSize(new Dimension(260, 55));
        logoutPanel.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createMatteBorder(1, 0, 0, 0, new Color(255, 255, 255, 25)),
            BorderFactory.createEmptyBorder(5, 0, 5, 0)
        ));
        
        JLabel label = new JLabel("Logout");
        label.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 14));
        label.setForeground(Color.WHITE);
        label.setBorder(BorderFactory.createEmptyBorder(0, 20, 0, 20));
        
        logoutPanel.add(label, BorderLayout.CENTER);
        logoutPanel.addMouseListener(new MouseAdapter() {
            public void mouseEntered(MouseEvent e) {
                logoutPanel.setBackground(new Color(185, 28, 28)); // Darker red on hover
                logoutPanel.setCursor(new Cursor(Cursor.HAND_CURSOR));
            }
            public void mouseExited(MouseEvent e) {
                logoutPanel.setBackground(new Color(220, 38, 38));
            }
            public void mouseClicked(MouseEvent e) {
                handleLogout();
            }
        });
        
        add(logoutPanel);
    }
    
    private void handleLogout() {
        int confirm = JOptionPane.showConfirmDialog(
            this,
            "Are you sure you want to logout?",
            "Confirm Logout",
            JOptionPane.YES_NO_OPTION,
            JOptionPane.QUESTION_MESSAGE
        );
        
        if (confirm == JOptionPane.YES_OPTION) {
            try {
                java.nio.file.Path userFile = java.nio.file.Paths.get("admin_control","src", "userdata", "ud.txt");
                if (java.nio.file.Files.exists(userFile)) {
                    java.nio.file.Files.delete(userFile);
                }
            } catch (Exception e) {
                System.out.println("Could not delete user data file: " + e.getMessage());
            }
            
            Window window = SwingUtilities.getWindowAncestor(this);
            if (window != null) {
                window.dispose();
            }
        }
    }
}