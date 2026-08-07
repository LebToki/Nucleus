/**
 * RestoSign Admin Enhancements JavaScript
 * Enhanced interactivity and user experience improvements
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Initialize all enhancements
    initSearchEnhancements();
    initSidebarEnhancements();
    initThemeToggle();
    initLoadingStates();
    initTooltips();
    initAnimations();
    initResponsiveHelpers();
});

/**
 * Enhanced Search Functionality
 */
function initSearchEnhancements() {
    const searchWrapper = document.querySelector('.navbar-search-wrapper');
    const searchInput = document.querySelector('.search-input');
    const searchClear = document.querySelector('.search-clear');
    const searchSuggestions = document.querySelector('.search-suggestions');

    if (!searchWrapper || !searchInput) return;

    // Show/hide suggestions based on input
    searchInput.addEventListener('input', function () {
        const value = this.value.trim();

        if (value.length > 0) {
            searchSuggestions.classList.remove('d-none');
            searchClear.classList.remove('d-none');

            // Filter suggestions based on input
            filterSuggestions(value);
        } else {
            searchSuggestions.classList.add('d-none');
            searchClear.classList.remove('d-none');
        }
    });

    // Clear search
    if (searchClear) {
        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            searchSuggestions.classList.add('d-none');
            searchClear.classList.add('d-none');
            searchInput.focus();
        });
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchWrapper.contains(e.target)) {
            searchSuggestions.classList.add('d-none');
        }
    });

    // Handle suggestion clicks
    const suggestionItems = document.querySelectorAll('.suggestion-item');
    suggestionItems.forEach(item => {
        item.addEventListener('click', function () {
            const text = this.querySelector('span').textContent;
            searchInput.value = text;
            searchSuggestions.classList.add('d-none');

            // Trigger search (you can implement actual search logic here)
            performSearch(text);
        });
    });

    // Handle Enter key
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch(this.value);
            searchSuggestions.classList.add('d-none');
        }
    });
}

/**
 * Filter search suggestions
 */
function filterSuggestions(query) {
    const suggestions = document.querySelectorAll('.suggestion-item');
    const queryLower = query.toLowerCase();

    suggestions.forEach(suggestion => {
        const text = suggestion.querySelector('span').textContent.toLowerCase();
        if (text.includes(queryLower)) {
            suggestion.style.display = 'flex';
        } else {
            suggestion.style.display = 'none';
        }
    });
}

/**
 * Perform search action
 */
function performSearch(query) {
    if (!query.trim()) return;

    // Add loading state
    const searchInput = document.querySelector('.search-input');
    searchInput.classList.add('loading');

    // Simulate search (replace with actual search implementation)
    setTimeout(() => {
        searchInput.classList.remove('loading');

        // You can implement actual search logic here
        console.log('Searching for:', query);

        // Example: redirect to search results
        // window.location.href = `search.php?q=${encodeURIComponent(query)}`;
    }, 500);
}

/**
 * Enhanced Sidebar Functionality
 */
function initSidebarEnhancements() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarMobileToggle = document.querySelector('.sidebar-mobile-toggle');
    const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');
    const groupToggles = document.querySelectorAll('.group-toggle');

    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            document.querySelector('.dashboard-main').classList.toggle('active');

            // Save state to localStorage
            const isActive = document.querySelector('.dashboard-main').classList.contains('active');
            localStorage.setItem('sidebarCollapsed', isActive);
        });
    }

    // Mobile sidebar toggle
    if (sidebarMobileToggle) {
        sidebarMobileToggle.addEventListener('click', function () {
            sidebar.classList.add('sidebar-open');
            document.body.classList.add('overlay-active');
        });
    }

    // Close sidebar on mobile
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function () {
            sidebar.classList.remove('sidebar-open');
            document.body.classList.remove('overlay-active');
        });
    }

    // Collapsible menu groups
    groupToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            this.setAttribute('aria-expanded', !isExpanded);

            // Save state to localStorage
            const groupId = this.getAttribute('data-bs-target').replace('#', '');
            localStorage.setItem(`sidebarGroup_${groupId}`, !isExpanded);
        });
    });

    // Restore sidebar state from localStorage
    restoreSidebarState();

    // Close sidebar when clicking overlay on mobile
    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768 &&
            !sidebar.contains(e.target) &&
            !sidebarMobileToggle.contains(e.target) &&
            sidebar.classList.contains('sidebar-open')) {
            sidebar.classList.remove('sidebar-open');
            document.body.classList.remove('overlay-active');
        }
    });
}

/**
 * Restore sidebar state from localStorage
 */
function restoreSidebarState() {
    // Restore main sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.querySelector('.dashboard-main').classList.add('active');
    }

    // Restore group states
    const groupToggles = document.querySelectorAll('.group-toggle');
    groupToggles.forEach(toggle => {
        const groupId = toggle.getAttribute('data-bs-target').replace('#', '');
        const isCollapsed = localStorage.getItem(`sidebarGroup_${groupId}`) === 'true';

        if (isCollapsed) {
            toggle.setAttribute('aria-expanded', 'false');
            const target = document.querySelector(toggle.getAttribute('data-bs-target'));
            if (target) {
                target.classList.remove('show');
            }
        }
    });
}

/**
 * Enhanced Theme Toggle
 * Uses aria-label approach matching the WowDash CSS pseudo-element icon
 */
function initThemeToggle() {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    if (!themeToggle) return;

    // Utility: update button aria-label
    function updateButton(isDark) {
        themeToggle.setAttribute('aria-label', isDark ? 'dark' : 'light');
    }

    // Utility: update theme on html element
    function updateTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }

    // Restore theme from localStorage (default light)
    const savedTheme = localStorage.getItem('theme') || 'light';
    updateTheme(savedTheme);
    updateButton(savedTheme === 'dark');

    // Toggle theme on click
    themeToggle.addEventListener('click', function () {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        updateTheme(newTheme);
        updateButton(newTheme === 'dark');
        localStorage.setItem('theme', newTheme);

        // Smooth transition effect
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    });
}

/**
 * Loading States and Micro-interactions
 */
function initLoadingStates() {
    // Add loading states to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            // Skip if it's a dropdown toggle or similar
            if (this.hasAttribute('data-bs-toggle') || this.hasAttribute('data-bs-target')) {
                return;
            }

            // Add loading state for form submissions
            if (this.type === 'submit' || this.closest('form')) {
                addLoadingState(this);
            }
        });
    });

    // Add loading states to links that might take time
    const links = document.querySelectorAll('a[href$=".php"]:not([href*="logout"]):not([href*="index"])');
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            // Add loading state for navigation
            addLoadingState(this);
        });
    });
}

/**
 * Add loading state to element
 */
function addLoadingState(element) {
    const originalContent = element.innerHTML;
    const loadingSpinner = '<span class="loading-spinner me-2"></span>';

    element.innerHTML = loadingSpinner + 'Loading...';
    element.disabled = true;
    element.classList.add('loading');

    // Remove loading state after a delay (or when the page loads)
    setTimeout(() => {
        element.innerHTML = originalContent;
        element.disabled = false;
        element.classList.remove('loading');
    }, 2000);
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize animations
 */
function initAnimations() {
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    // Add slide-in animation to sidebar
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.add('slide-in');
    }

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe elements that should animate on scroll
    const animateElements = document.querySelectorAll('.kpi-card, .card');
    animateElements.forEach(el => observer.observe(el));
}

/**
 * Responsive helpers
 */
function initResponsiveHelpers() {
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(handleResize, 250);
    });

    // Handle orientation change on mobile
    window.addEventListener('orientationchange', function () {
        setTimeout(handleResize, 100);
    });

    // Initial call
    handleResize();
}

/**
 * Handle window resize
 */
function handleResize() {
    const width = window.innerWidth;
    const sidebar = document.querySelector('.sidebar');
    const dashboardMain = document.querySelector('.dashboard-main');

    // Mobile adjustments
    if (width <= 768) {
        // Close sidebar on mobile
        if (sidebar) {
            sidebar.classList.remove('sidebar-open');
        }
        if (dashboardMain) {
            dashboardMain.classList.remove('active');
        }
        document.body.classList.remove('overlay-active');
    }

    // Tablet adjustments
    if (width <= 992) {
        // Adjust card layouts
        const kpiCards = document.querySelectorAll('.kpi-card');
        kpiCards.forEach(card => {
            card.classList.add('mobile-optimized');
        });
    }

    // Desktop adjustments
    if (width > 992) {
        // Remove mobile optimizations
        const mobileOptimized = document.querySelectorAll('.mobile-optimized');
        mobileOptimized.forEach(el => {
            el.classList.remove('mobile-optimized');
        });
    }
}

/**
 * Utility functions
 */

// Debounce function
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Show notification
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
}

// Export functions for global use
window.RestoSAdmin = {
    showNotification,
    addLoadingState,
    debounce,
    throttle
};
