/**
 * RM Logo Carousel JavaScript
 * Handles carousel functionality, hover effects, and responsive behavior
 */
(function () {
  "use strict";

  /**
   * Initialize logo carousel
   * @param {HTMLElement} element - The carousel container element
   */
  function initLogoCarousel(element) {
    if (!element) return;

    const track = element.querySelector(".rm-logo-carousel__track");
    const logos = element.querySelectorAll(".rm-logo-carousel__logo");

    if (!track || logos.length === 0) return;

    // Get configuration from data attributes
    const config = {
      speed: parseInt(element.dataset.speed) || 30,
      mobileSpeed: parseInt(element.dataset.mobileSpeed) || 20,
      tabletSpeed: parseInt(element.dataset.tabletSpeed) || 25,
      pauseOnHover: element.dataset.pauseOnHover === "true",
      autoPlay: element.dataset.autoPlay !== "false",
      visibleLogos: parseInt(element.dataset.visibleLogos) || 5,
      mobileLogos: parseInt(element.dataset.mobileLogos) || 2,
      tabletLogos: parseInt(element.dataset.tabletLogos) || 3,
    };

    // Responsive speed detection
    function getResponsiveSpeed() {
      if (window.innerWidth <= 768) {
        return config.mobileSpeed;
      } else if (window.innerWidth <= 1024) {
        return config.tabletSpeed;
      }
      return config.speed;
    }

    // Update animation speed
    function updateAnimationSpeed() {
      const speed = getResponsiveSpeed();
      track.style.animationDuration = speed + "s";
    }

    // Initialize animation
    if (config.autoPlay) {
      updateAnimationSpeed();
    } else {
      track.classList.add("rm-logo-carousel__track--no-animation");
    }

    // Pause on hover functionality
    if (config.pauseOnHover && config.autoPlay) {
      element.addEventListener("mouseenter", () => {
        track.classList.add("rm-logo-carousel__track--paused");
      });

      element.addEventListener("mouseleave", () => {
        track.classList.remove("rm-logo-carousel__track--paused");
      });
    }

    // Touch/swipe support for mobile
    let startX = 0;
    let startY = 0;
    let isDragging = false;
    let dragStartTime = 0;

    element.addEventListener(
      "touchstart",
      (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = false;
        dragStartTime = Date.now();
      },
      { passive: true }
    );

    element.addEventListener(
      "touchmove",
      (e) => {
        if (!isDragging) {
          const deltaX = Math.abs(e.touches[0].clientX - startX);
          const deltaY = Math.abs(e.touches[0].clientY - startY);

          if (deltaX > deltaY && deltaX > 10) {
            isDragging = true;
            if (config.autoPlay) {
              track.classList.add("rm-logo-carousel__track--paused");
            }
          }
        }
      },
      { passive: true }
    );

    element.addEventListener(
      "touchend",
      () => {
        if (isDragging) {
          const dragDuration = Date.now() - dragStartTime;
          const pauseTime = Math.min(dragDuration * 2, 3000); // Max 3 seconds pause

          setTimeout(() => {
            if (config.autoPlay) {
              track.classList.remove("rm-logo-carousel__track--paused");
            }
          }, pauseTime);
        }
        isDragging = false;
      },
      { passive: true }
    );

    // Logo click handlers
    logos.forEach((logo) => {
      logo.addEventListener("click", (e) => {
        // Only prevent default if there's no link
        const link = logo.querySelector("a");
        if (!link) {
          e.preventDefault();
        }
        console.log(
          "Logo clicked:",
          logo.querySelector("img")?.alt || "Unknown"
        );
      });
    });

    // Handle window resize
    let resizeTimeout;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        updateAnimationSpeed();
      }, 250);
    });

    // Handle visibility change (pause when tab is not visible)
    document.addEventListener("visibilitychange", () => {
      if (config.autoPlay) {
        if (document.hidden) {
          track.classList.add("rm-logo-carousel__track--paused");
        } else {
          track.classList.remove("rm-logo-carousel__track--paused");
        }
      }
    });

    // Keyboard navigation support
    element.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft" || e.key === "ArrowRight") {
        e.preventDefault();
        // Pause animation briefly on keyboard interaction
        if (config.autoPlay) {
          track.classList.add("rm-logo-carousel__track--paused");
          setTimeout(() => {
            track.classList.remove("rm-logo-carousel__track--paused");
          }, 2000);
        }
      }
    });

    // Make logos focusable for keyboard navigation
    logos.forEach((logo) => {
      logo.setAttribute("tabindex", "0");
      logo.setAttribute("role", "button");
    });
  }

  /**
   * Initialize all logo carousels on the page
   */
  function initAllCarousels() {
    const carousels = document.querySelectorAll(".rm-logo-carousel");
    carousels.forEach(initLogoCarousel);
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllCarousels);
  } else {
    initAllCarousels();
  }

  // Elementor integration
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/rm-logo-carousel.default",
      function ($scope) {
        initLogoCarousel($scope[0] || $scope);
      }
    );
  }

  // Expose for manual initialization
  window.RMLogoCarousel = {
    init: initLogoCarousel,
    initAll: initAllCarousels,
  };
})();
