package util;

import javax.swing.*;
import javax.swing.border.Border;
import javax.swing.table.*;
import java.awt.*;

/**
 * UI Helper utility class for creating consistent styled Swing components.
 * Provides factory methods for buttons, text fields, tables, and other UI elements
 * with predefined styling based on the application's design system.
 */
public class UIHelper {

    /**
     * Creates a styled button with custom background and text colors.
     * 
     * @param text Button label text
     * @param bgColor Background color
     * @param textColor Text color
     * @return Configured JButton instance
     */
    public static JButton createButton(String text, Color bgColor, Color textColor) {
        JButton button = new JButton(text);
        button.setBackground(bgColor);
        button.setForeground(textColor);
        button.setFont(AppFonts.BODY);
        button.setFocusPainted(false);                                      // Remove focus border
        button.setBorderPainted(false);                                     // Remove button border
        button.setCursor(new Cursor(Cursor.HAND_CURSOR));                  // Hand cursor on hover
        button.setBorder(BorderFactory.createEmptyBorder(10, 20, 10, 20)); // Internal padding
        return button;
    }

    /**
     * Creates a primary action button with secondary color background.
     * Used for main actions like "Save", "Submit", etc.
     * 
     * @param text Button label text
     * @return Styled primary button
     */
    public static JButton createPrimaryButton(String text) {
        return createButton(text, AppColors.SECONDARY, Color.WHITE);
    }

    /**
     * Creates an outline button with border and transparent background.
     * Used for secondary actions like "Cancel", "Back", etc.
     * 
     * @param text Button label text
     * @return Styled outline button
     */
    public static JButton createOutlineButton(String text) {
        JButton button = new JButton(text);
        button.setBackground(Color.WHITE);
        button.setForeground(AppColors.PRIMARY);
        button.setFont(AppFonts.BODY);
        button.setFocusPainted(false);
        button.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(AppColors.BORDER),
                BorderFactory.createEmptyBorder(8, 18, 8, 18)));
        button.setCursor(new Cursor(Cursor.HAND_CURSOR));
        return button;
    }

    /**
     * Creates a styled text field with border and padding.
     * 
     * @param placeholder Tooltip text to display as placeholder
     * @return Configured JTextField instance
     */
    public static JTextField createTextField(String placeholder) {
        JTextField field = new JTextField();
        field.setFont(AppFonts.BODY);
        field.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(AppColors.BORDER),
                BorderFactory.createEmptyBorder(10, 15, 10, 15)));
        field.setToolTipText(placeholder);
        return field;
    }

    /**
     * Creates a multi-line text area with word wrapping.
     * 
     * @param placeholder Tooltip text
     * @param rows Number of visible rows
     * @return Configured JTextArea instance
     */
    public static JTextArea createTextArea(String placeholder, int rows) {
        JTextArea area = new JTextArea(rows, 0);
        area.setFont(AppFonts.BODY);
        area.setBorder(BorderFactory.createEmptyBorder(10, 15, 10, 15));
        area.setLineWrap(true);         // Wrap text to fit width
        area.setWrapStyleWord(true);    // Wrap at word boundaries
        return area;
    }

    /**
     * Creates a styled dropdown/combo box.
     * 
     * @param items Array of items to display in dropdown
     * @return Configured JComboBox instance
     */
    public static JComboBox<String> createComboBox(String[] items) {
        JComboBox<String> combo = new JComboBox<>(items);
        combo.setFont(AppFonts.BODY);
        combo.setBackground(Color.WHITE);
        return combo;
    }

    /**
     * Creates a card panel with white background and subtle border.
     * Used for grouping related content.
     * 
     * @return Styled card JPanel
     */
    public static JPanel createCard() {
        JPanel card = new JPanel();
        card.setBackground(Color.WHITE);
        card.setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createLineBorder(new Color(0, 0, 0, 20)),  // Subtle border
                BorderFactory.createEmptyBorder(0, 0, 0, 0)));
        return card;
    }

    /**
     * Creates a styled table with custom formatting.
     * Features:
     * - Read-only cells (not editable)
     * - Custom row height and colors
     * - Styled header
     * - Hover selection highlighting
     * 
     * @param data 2D array of table data
     * @param columns Array of column names
     * @return Configured JTable instance
     */
    public static JTable createStyledTable(Object[][] data, String[] columns) {
        // Create table model with non-editable cells
        DefaultTableModel model = new DefaultTableModel(data, columns) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;  // Make all cells read-only
            }
        };

        JTable table = new JTable(model);
        table.setFont(AppFonts.BODY);
        table.setRowHeight(45);                                                      // Taller rows for readability
        table.setShowGrid(false);                                                    // Remove grid lines
        table.setIntercellSpacing(new Dimension(0, 0));                             // No spacing between cells
        table.setBackground(Color.WHITE);
        table.setSelectionBackground(new Color(102, 126, 234, 30));                 // Light purple selection
        table.setSelectionForeground(AppColors.TEXT_DARK);                          // Dark text on selection

        // Style table header
        JTableHeader header = table.getTableHeader();
        header.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 14));
        header.setBackground(AppColors.LIGHT);
        header.setForeground(AppColors.TEXT_DARK);
        header.setBorder(BorderFactory.createMatteBorder(0, 0, 2, 0, AppColors.BORDER));
        header.setPreferredSize(new Dimension(0, 50));

        
        table.setDefaultRenderer(Object.class, new DefaultTableCellRenderer() {
            @Override
            public Component getTableCellRendererComponent(JTable table, Object value,
                    boolean isSelected, boolean hasFocus, int row, int column) {
                Component c = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
                if (!isSelected) {
                    c.setBackground(row % 2 == 0 ? Color.WHITE : new Color(247, 250, 252));
                }
                setBorder(BorderFactory.createEmptyBorder(0, 15, 0, 15));
                return c;
            }
        });

        return table;
    }


    public static JLabel createBadge(String text, boolean isActive) {
        JLabel badge = new JLabel(text);
        badge.setFont(new Font(AppFonts.FAMILY, Font.BOLD, 11));
        badge.setOpaque(true);
        badge.setHorizontalAlignment(SwingConstants.CENTER);
        badge.setBorder(BorderFactory.createEmptyBorder(5, 12, 5, 12));

        if (isActive) {
            badge.setBackground(new Color(198, 246, 213)); 
            badge.setForeground(new Color(39, 103, 73)); 
        } else {
            badge.setBackground(new Color(254, 215, 215)); 
            badge.setForeground(new Color(155, 28, 28)); 
        }

        return badge;
    }
}
