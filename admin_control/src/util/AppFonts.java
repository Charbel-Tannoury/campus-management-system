package util;

import java.awt.Font;

/**
 * Centralized font definitions for consistent typography across the application.
 * All fonts use the "Segoe UI" font family with varying sizes and weights.
 * 
 * Font Hierarchy:
 * - TITLE (28px, Bold): Main page titles
 * - HEADING (22px, Bold): Section headings
 * - SUBHEADING (18px, Bold): Subsection headings
 * - CARD_TITLE (16px, Bold): Card/panel titles
 * - BODY (14px, Regular): Body text, form inputs
 * - SMALL (12px, Regular): Helper text, captions
 * - STAT_VALUE (30px, Bold): Large numbers in statistics
 * - MENU_ITEM (15px, Regular): Sidebar menu items
 * - SUBMENU_ITEM (14px, Regular): Sidebar submenu items
 * - TABLE_HEADER (13px, Bold): Table column headers
 */
public class AppFonts {
    /** Font family used throughout the application */
    public static final String FAMILY = "Segoe UI";
    
    /** Main page title font (28px, Bold) */
    public static final Font TITLE = new Font(FAMILY, Font.BOLD, 28);
    
    /** Section heading font (22px, Bold) */
    public static final Font HEADING = new Font(FAMILY, Font.BOLD, 22);
    
    /** Subsection heading font (18px, Bold) */
    public static final Font SUBHEADING = new Font(FAMILY, Font.BOLD, 18);
    
    /** Card title font (16px, Bold) */
    public static final Font CARD_TITLE = new Font(FAMILY, Font.BOLD, 16);
    
    /** Body text and form input font (14px, Regular) */
    public static final Font BODY = new Font(FAMILY, Font.PLAIN, 14);
    
    /** Small text and caption font (12px, Regular) */
    public static final Font SMALL = new Font(FAMILY, Font.PLAIN, 12);
    
    /** Large statistic value font (30px, Bold) */
    public static final Font STAT_VALUE = new Font(FAMILY, Font.BOLD, 30);
    
    /** Sidebar menu item font (15px, Regular) */
    public static final Font MENU_ITEM = new Font(FAMILY, Font.PLAIN, 15);
    
    /** Sidebar submenu item font (14px, Regular) */
    public static final Font SUBMENU_ITEM = new Font(FAMILY, Font.PLAIN, 14);
    
    /** Table header font (13px, Bold) */
    public static final Font TABLE_HEADER = new Font(FAMILY, Font.BOLD, 13);
}
