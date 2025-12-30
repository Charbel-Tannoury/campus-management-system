package components;

import util.*;
import javax.swing.*;
import java.awt.*;

/**
 * Statistics Card Component
 * 
 * A reusable dashboard card widget that displays a statistical metric.
 * Used in the statistics/dashboard page to show key metrics like:
 * - Total students count
 * - Total professors count
 * - Total courses count
 * - Total majors count
 * 
 * Visual Design:
 * - White card with colored left border accent
 * - Icon badge with background color matching accent
 * - Large numeric value display
 * - Descriptive title label
 * - Hover effect (hand cursor)
 * - 250x400 preferred size
 * 
 * Layout:
 * - BorderLayout structure
 * - Header: Title (left) + Icon badge (right)
 * - Center: Large count value
 * - Colored left border (4px) matching accent color
 * 
 * Parameters:
 * @param title: Display title (e.g., "Total Students")
 * @param count: Numeric value to display (e.g., "1,234")
 * @param icon: Emoji or icon character (e.g., "👥")
 * @param accentColor: Color for border and icon background
 */
public class StatCard extends JPanel {

    public StatCard(String title, String count, String icon, Color accentColor) {
        setLayout(new BorderLayout());
        setBackground(Color.WHITE);
        setPreferredSize(new Dimension(250, 400));

        setBorder(BorderFactory.createCompoundBorder(
                BorderFactory.createMatteBorder(0, 0, 0, 4, accentColor),
                BorderFactory.createCompoundBorder(
                        BorderFactory.createLineBorder(new Color(0, 0, 0, 10)),
                        BorderFactory.createEmptyBorder(20, 25, 20, 20))));


        JPanel headerPanel = new JPanel(new BorderLayout());
        headerPanel.setBackground(Color.WHITE);

        JLabel titleLabel = new JLabel(title);
        titleLabel.setFont(new Font(AppFonts.FAMILY, Font.PLAIN, 14));
        titleLabel.setForeground(AppColors.TEXT_MUTED);

        JLabel iconLabel = new JLabel(icon);
        iconLabel.setFont(new Font("Segoe UI Emoji", Font.PLAIN, 24));
        iconLabel.setForeground(Color.WHITE);
        iconLabel.setOpaque(true);
        iconLabel.setBackground(accentColor);
        iconLabel.setBorder(BorderFactory.createEmptyBorder(12, 15, 12, 15));

        headerPanel.add(titleLabel, BorderLayout.WEST);
        headerPanel.add(iconLabel, BorderLayout.EAST);

        JPanel valuePanel = new JPanel(new FlowLayout(FlowLayout.LEFT, 0, 15));
        valuePanel.setBackground(Color.WHITE);

        JLabel countLabel = new JLabel(count);
        countLabel.setFont(AppFonts.STAT_VALUE);
        countLabel.setForeground(AppColors.TEXT_DARK);

        valuePanel.add(countLabel);

        add(headerPanel, BorderLayout.NORTH);
        add(valuePanel, BorderLayout.CENTER);
        addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseEntered(java.awt.event.MouseEvent evt) {
                setCursor(new Cursor(Cursor.HAND_CURSOR));
            }
        });
    }
}
