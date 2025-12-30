<?php
/**
 * Footer Include for Admin Panel
 * 
 * Common footer section loaded on all admin pages.
 * Includes:
 * - Main JavaScript file (main.js) with cache busting
 * - Google Translate widget initialization
 * - Closing body and html tags
 * 
 * Features:
 * 1. JavaScript Loading:
 *    - Path-aware loading (detects /pages/ depth)
 *    - Cache busting with timestamp (?v=<?php echo time(); ?>)
 *    - Ensures fresh JS on every page load
 * 
 * 2. Google Translate Integration:
 *    - Initializes translation widget
 *    - Default language: English (en)
 *    - Loads from google.com/translate_a/element.js
 *    - Callback: googleTranslateElementInit()
 * 
 * Usage:
 * - Include at the bottom of every admin page
 * - Must be after all HTML content
 * - Closes the <body> and <html> tags
 * 
 * Example:
 * // ... page content ...
 * <?php include '../../includes/footer.php'; ?>
 */
?>
    <script src="<?php echo strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '../../' : ''; ?>assets/js/main.js?v=<?php echo time(); ?>"></script>
    
    
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement(
        {pageLanguage: 'en'},
        'google_translate_element'
      );
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
