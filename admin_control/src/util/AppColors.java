package util;

import java.awt.Color;

/**
 * Centralized color palette for the Campus Management System.
 * Provides consistent theming across all UI components.
 * All colors use RGB values for consistent rendering.
 */
public class AppColors {
    
    // ===== Primary UI Colors =====
    /** Primary brand color - used for main UI elements */
    public static final Color PRIMARY = new Color(74, 85, 104);       // Dark gray-blue
    
    /** Secondary accent color - used for highlights and interactive elements */
    public static final Color SECONDARY = new Color(102, 126, 234);   // Purple-blue
    
    /** Success color - used for positive actions and confirmations */
    public static final Color SUCCESS = new Color(72, 187, 120);      // Green
    
    /** Danger/Error color - used for warnings and destructive actions */
    public static final Color DANGER = new Color(245, 101, 101);      // Red
    
    /** Warning color - used for caution messages */
    public static final Color WARNING = new Color(237, 137, 54);      // Orange
    
    /** Info color - used for informational messages */
    public static final Color INFO = new Color(66, 153, 225);         // Blue
    
    /** Dark color - used for text and strong contrast elements */
    public static final Color DARK = new Color(45, 55, 72);           // Very dark gray
    
    /** Light color - used for backgrounds and subtle elements */
    public static final Color LIGHT = new Color(247, 250, 252);       // Very light gray
    
    // ===== Sidebar Colors =====
    /** Sidebar background color - red gradient start */
    public static final Color SIDEBAR_BG = new Color(185, 28, 28);     // #b91c1c - Red gradient start
    
    /** Sidebar dark color - red gradient end for depth effect */
    public static final Color SIDEBAR_DARK = new Color(127, 29, 29);   // #7f1d1d - Red gradient end
    
    /** Sidebar hover state - darker red for interactive feedback */
    public static final Color SIDEBAR_HOVER = new Color(153, 27, 27);  // Slightly darker red for hover 
    
    // ===== Text Colors =====
    /** Primary text color - used for headings and important text */
    public static final Color TEXT_DARK = new Color(45, 55, 72);       // Dark gray
    
    /** Muted text color - used for secondary information */
    public static final Color TEXT_MUTED = new Color(113, 128, 150);   // Medium gray
    
    /** Light text color - used for subtle text and placeholders */
    public static final Color TEXT_LIGHT = new Color(160, 174, 192);   // Light gray
    
    // ===== Background Colors =====
    /** Page background color - main content area background */
    public static final Color BG_PAGE = new Color(245, 247, 250);      // Very light blue-gray
    
    /** White background - used for cards and containers */
    public static final Color BG_WHITE = Color.WHITE;
    
    // ===== Border Colors =====
    /** Standard border color - used for dividers and outlines */
    public static final Color BORDER = new Color(226, 232, 240);      
}
