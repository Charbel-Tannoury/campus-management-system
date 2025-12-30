
import pages_ui.login;
import javax.swing.*;

import models.*;

import java.awt.*;
import java.awt.event.*;
import java.awt.image.*;
import java.nio.file.Path;
import java.nio.file.Paths;

/**
 * Main application entry point for the Campus Management System Admin Control Panel.
 * This class handles application initialization, login flow, and dashboard creation.
 * 
 * @author Campus Management Team
 * @version 1.0
 */
public class App {
    // Current university instance - holds university configuration and data
    private static university thisuniversity;
    // Current logged-in employee/admin instance
    private static employee thisemployee;
    
    /**
     * Main entry point for the application.
     * @param args Command line arguments (not used)
     */
    public static void main(String[] args) {
        startApp();
        
    }
    /**
     * Initializes and starts the application.
     * Sets up the UI Look & Feel, enables font anti-aliasing, and displays the login window.
     */
    public static void startApp() {
        try {
            // Use system native look and feel for better OS integration
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
            
            // Enable font anti-aliasing for smoother text rendering
            System.setProperty("awt.useSystemAAFontSettings", "on");
            System.setProperty("swing.aatext", "true");
        } catch (Exception e) {
            e.printStackTrace();
        }
        // Create main login frame
        JFrame frame = new JFrame("CCTJ - University Login");
      
        // Load and scale university logo for window icon
        ImageIcon raw = new ImageIcon(Paths.get("admin_control","src","img","logo.png").toString());
        Image frameIcon = raw.getImage().getScaledInstance(64, 64, Image.SCALE_SMOOTH);
        
        // Initialize login panel
        login loginPanel = new login(frame);
        
        SwingUtilities.invokeLater(() -> {
            // Check if login was successful
            if(loginPanel.checkpass()){
                // Close login window
                frame.dispose();
                
                // Retrieve authenticated university and employee data
                thisuniversity = loginPanel.getThisUniversity();
                thisemployee = loginPanel.getThisEmployee();
                
                // Create and display main dashboard
                DashboardFrame dashboardFrame = new DashboardFrame(thisuniversity, thisemployee, raw);
                dashboardFrame.setIconImage(frameIcon);
                dashboardFrame.setVisible(true);

                dashboardFrame.addWindowListener(new WindowAdapter() {
                    @Override
                    public void windowClosed(WindowEvent e) {
                        
                        startApp();
                    }   
                });

            } else {
                
                frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
                frame.setLayout(new BorderLayout());
                
                loginPanel.setBorder(BorderFactory.createEmptyBorder(12, 12, 12, 12));
                
                loginPanel.setBackground(new Color(0xF5F7FA));
                loginPanel.setOpaque(true);
                
                Image scaledImg = raw.getImage().getScaledInstance(350, 350, Image.SCALE_SMOOTH);
                JLabel imgLabel = new JLabel(new ImageIcon(scaledImg));
                imgLabel.setPreferredSize(new Dimension(350, 350));
                
                GradientPanel gp = new GradientPanel(new BorderLayout());
                gp.add(imgLabel, BorderLayout.NORTH);
                gp.add(loginPanel, BorderLayout.CENTER);
                frame.setContentPane(gp);

                
               
                frame.setIconImage(frameIcon);

                
                frame.setPreferredSize(new Dimension(800, 800));
                frame.pack();
                frame.setResizable(false);
                frame.setLocationRelativeTo(null);
                frame.setVisible(true);
                
                frame.addComponentListener(new ComponentAdapter() {
                    public void componentHidden(ComponentEvent e) {
                        thisuniversity = loginPanel.getThisUniversity();
                        thisemployee = loginPanel.getThisEmployee();
                        frame.dispose();
                        DashboardFrame dashboardFrame = new DashboardFrame(thisuniversity, thisemployee, raw);
                        dashboardFrame.setIconImage(frameIcon);
                        dashboardFrame.setVisible(true);

                        
                    }
                   
                });
            }
        });
    }

    
    static class GradientPanel extends JPanel {
        public GradientPanel(LayoutManager lm) { super(lm); setOpaque(true); }
        @Override
        protected void paintComponent(Graphics g) {
            super.paintComponent(g);
            Graphics2D g2 = (Graphics2D) g.create();
            int w = getWidth();
            int h = getHeight();
            Color c1 = new Color(0xb91c1c); // #b91c1c - Red gradient start (matches PHP)
            Color c2 = new Color(0x7f1d1d); // #7f1d1d - Red gradient end (matches PHP)
            GradientPaint gp = new GradientPaint(0, 0, c1, 0, h, c2);
            g2.setPaint(gp);
            g2.fillRect(0, 0, w, h);
            g2.dispose();
        }
    }
}
