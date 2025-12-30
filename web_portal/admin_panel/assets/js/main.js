/**
 * Campus Management System - Admin Panel JavaScript
 * 
 * This file handles all interactive features for the admin dashboard:
 * - Mobile menu toggle
 * - Modal dialogs
 * - Form validation
 * - Dropdown menus
 * - Submenu interactions
 * - Dynamic UI updates
 * 
 * All functions are initialized when DOM content is loaded.
 */

// Initialize all interactive features when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();         // Mobile sidebar toggle
    initModals();             // Modal window handlers
    initFormValidation();     // Client-side form validation
    initSubmenu();            // Submenu expand/collapse
    initHeaderDropdowns();    // User profile dropdown
});

// =============================================
// Mobile Menu Toggle
// Handles sidebar visibility on mobile devices
// =============================================
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        // Toggle sidebar visibility when hamburger menu is clicked
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside of it
        document.addEventListener('click', function(e) {
            // Check if click is outside both sidebar and toggle button
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
}

// =============================================
// Header Dropdown Menus
// Manages user profile dropdown in header
// =============================================
function initHeaderDropdowns() {
    const userProfile = document.querySelector('.user-profile');
    
    if (userProfile) {
        // Toggle dropdown visibility when clicking on user profile
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();  // Prevent event from bubbling
            this.classList.toggle('active');
        });
        
        // Close dropdown when clicking anywhere else on page
        document.addEventListener('click', function() {
            userProfile.classList.remove('active');
        });
        
        // Prevent dropdown from closing when clicking inside dropdown menu
        const dropdown = userProfile.querySelector('.dropdown-content');
        if (dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
}


// =============================================
// Modal Dialog Management
// Controls opening, closing, and interaction with modal windows
// =============================================
function initModals() {
    // Open modal when clicking elements with data-modal-target attribute
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');  // Display modal
            }
        });
    });

    // Close modal when clicking close button or elements with data-modal-close
    document.querySelectorAll('.modal-close, [data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');  // Hide modal
            }
        });
    });

    // Close modal when clicking on backdrop (outside modal content)
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {  // Only if clicking backdrop, not modal content
                this.classList.remove('show');
            }
        });
    });
}

/**
 * Open a modal programmatically from JavaScript code
 * @param {string} modalId - The ID of the modal element to open
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

/**
 * Close a modal programmatically from JavaScript code
 * @param {string} modalId - The ID of the modal element to close
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}


// =============================================
// Client-Side Form Validation
// Validates form inputs before submission
// =============================================
function initFormValidation() {
    // Apply validation to forms with data-validate attribute
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all required fields are filled
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#f56565';  // Red border for errors
                } else {
                    field.style.borderColor = '#e2e8f0';  // Reset to normal border
                }
            });

            // Validate email format using regex
            this.querySelectorAll('[type="email"]').forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.style.borderColor = '#f56565';  // Red border for invalid email
                }
            });

            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly');
            }
        });
    });
}

// =============================================
// Submenu Toggle
// Expand and collapse navigation submenus
// =============================================
function initSubmenu() {
    const submenuLinks = document.querySelectorAll('.has-submenu > a');
    
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const parent = this.parentElement;
            const isActive = parent.classList.contains('active');
            
            // Close all other submenus
            document.querySelectorAll('.has-submenu').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current submenu
            if (isActive) {
                parent.classList.remove('active');
            } else {
                parent.classList.add('active');
            }
        });
    });
}

// =============================================
// 3rod risalet naja7
// =============================================
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 30px;
        background: ${type === 'success' ? '#48bb78' : '#f56565'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideDown 0.3s ease;
    `;
    messageDiv.textContent = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.animation = 'slideUp 0.3s ease';
        setTimeout(() => messageDiv.remove(), 300);
    }, 3000);
}

// =============================================
// tar2im lsaf7at
// =============================================
function initPagination(totalItems, itemsPerPage, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    let currentPage = 1;
    
    function renderPagination() {
        container.innerHTML = '';
        
        // zir lsaf7a lsabe2a
        const prevBtn = createPaginationButton('Previous', currentPage > 1, () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
                renderPagination();
            }
        });
        container.appendChild(prevBtn);
        
        // ar2am lsaf7at
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = createPaginationButton(i, true, () => {
                currentPage = i;
                showPage(currentPage);
                renderPagination();
            }, currentPage === i);
            container.appendChild(pageBtn);
        }
        
        // zir lsaf7a ljaye
        const nextBtn = createPaginationButton('Next', currentPage < totalPages, () => {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
                renderPagination();
            }
        });
        container.appendChild(nextBtn);
    }
    
    function createPaginationButton(text, enabled, onClick, active = false) {
        const btn = document.createElement('button');
        btn.textContent = text;
        btn.className = `btn ${active ? 'btn-primary' : 'btn-outline'} btn-sm`;
        btn.disabled = !enabled;
        if (enabled) {
            btn.addEventListener('click', onClick);
        }
        return btn;
    }
    
    function showPage(page) {
        // 7ot logic 3arad lsaf7a hon
        console.log('Showing page:', page);
    }
    
    renderPagination();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            transform: translate(-50%, 0);
            opacity: 1;
        }
        to {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
