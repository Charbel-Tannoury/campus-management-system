/**
 * Campus Management System - Student Portal JavaScript
 * 
 * Handles mobile sidebar toggle and overlay functionality.
 * Features:
 * - Automatic mobile menu button creation if missing
 * - Sidebar show/hide toggle
 * - Overlay backdrop for mobile
 * - Click-outside-to-close behavior
 */

// Initialize sidebar toggle when page loads
document.addEventListener('DOMContentLoaded', function(){
  try{
    // Get main UI elements
    var top = document.querySelector('.topbar');
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.querySelector('.sidebar-overlay');
    
    // Exit if required elements not found
    if(!top || !sidebar) {
      console.warn('Topbar or sidebar not found');
      return;
    }

    /**
     * Attaches toggle handler to a menu button.
     * Prevents duplicate handlers using _hasToggleHandler flag.
     * @param {HTMLElement} btn - The button element to attach handler to
     */
    function attachToggle(btn){
      if(btn._hasToggleHandler) return;  // Skip if already has handler
      
      btn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var isOpen = sidebar.classList.contains('show');
        
        if(isOpen){
          // Close sidebar and hide overlay
          sidebar.classList.remove('show');
          if(overlay) overlay.classList.remove('visible');
        } else {
          // Open sidebar and show overlay
          sidebar.classList.add('show');
          if(overlay) overlay.classList.add('visible');
        }
      });
      btn._hasToggleHandler = true;  // Mark as having handler
    }

    // Find existing menu toggle buttons or create one
    var toggles = Array.prototype.slice.call(document.querySelectorAll('.mobile-menu-toggle'));
    
    // If no toggle button exists, create and insert one
    if(toggles.length === 0){
      var left = top.querySelector('.left') || top.querySelector('.top-left');
      if(left){
        // Create hamburger menu button
        var btn = document.createElement('div');
        btn.className = 'mobile-menu-toggle';
        btn.innerHTML = '<i class="fas fa-bars"></i>';  // FontAwesome icon
        left.insertBefore(btn, left.firstChild);
        toggles.push(btn);
      }
    }

    // Attach toggle handler to all toggle buttons
    toggles.forEach(attachToggle);

    // Clicking overlay closes sidebar
    if(overlay){
      overlay.addEventListener('click', function(e){
        e.preventDefault();
        sidebar.classList.remove('show');
        overlay.classList.remove('visible');
      });
    }

    // Close sidebar when clicking outside on mobile devices
    document.addEventListener('click', function(ev){
      if(window.innerWidth <= 768){  // Mobile breakpoint
        var clickedInsideSidebar = sidebar.contains(ev.target);
        var clickedToggle = false;
        
        // Check if clicked on toggle or its children
        var target = ev.target;
        while(target){
          if(target.classList && target.classList.contains('mobile-menu-toggle')){
            clickedToggle = true;
            break;
          }
          target = target.parentElement;
        }
        
        if(!clickedInsideSidebar && !clickedToggle && sidebar.classList.contains('show')){
          sidebar.classList.remove('show');
          if(overlay) overlay.classList.remove('visible');
        }
      }
    });

    // Hide sidebar on resize to avoid stuck state
    window.addEventListener('resize', function(){
      if(window.innerWidth > 768){
        sidebar.classList.remove('show');
        if(overlay) overlay.classList.remove('visible');
      }
    });

  }catch(e){ 
    console.error('Mobile menu error:', e); 
  }
});
