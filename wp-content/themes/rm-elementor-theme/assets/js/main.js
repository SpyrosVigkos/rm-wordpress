/**
 * ReelMetrics Theme JavaScript
 */

// Mobile Navigation Toggle
function rmToggleMobileMenu() {
    const overlay = document.getElementById('rm-mobile-overlay');
    const body = document.body;
    
    if (overlay.classList.contains('active')) {
        // Close menu
        overlay.classList.remove('active');
        body.style.overflow = '';
    } else {
        // Open menu
        overlay.classList.add('active');
        body.style.overflow = 'hidden';
    }
}

// Close mobile menu when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('rm-mobile-overlay');
    
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                rmToggleMobileMenu();
            }
        });
    }
    
    // Close mobile menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('active')) {
            rmToggleMobileMenu();
        }
    });
    
    // Handle window resize - close mobile menu if screen becomes large
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && overlay && overlay.classList.contains('active')) {
            rmToggleMobileMenu();
        }
    });
});

// Sticky Navigation Enhancement
document.addEventListener('DOMContentLoaded', function() {
    const navigation = document.querySelector('.rm-navigation');
    
    if (navigation) {
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Add/remove classes based on scroll position
            if (scrollTop > 100) {
                navigation.classList.add('rm-nav-scrolled');
            } else {
                navigation.classList.remove('rm-nav-scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }
});

// Initialize when DOM is loaded
jQuery(document).ready(function($) {
    // Additional jQuery-based functionality can go here
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(event) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100 // Account for sticky nav
            }, 1000);
        }
    });
    
    // Handle active navigation states
    $('.rm-nav-link').on('click', function() {
        $('.rm-nav-link').removeClass('active');
        $(this).addClass('active');
    });
});