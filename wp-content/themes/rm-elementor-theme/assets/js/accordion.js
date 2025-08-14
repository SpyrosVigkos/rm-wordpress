/**
 * ReelMetrics Accordion Widget JavaScript
 * Handles accordion functionality with smooth animations
 */

(function ($) {
  "use strict";

  class RMAccordion {
    constructor(element) {
      this.accordion = $(element);
      this.items = this.accordion.find(".rm-accordion-item");
      this.animationDuration =
        parseInt(this.accordion.data("animation-duration")) || 400;

      this.init();
    }

    init() {
      this.bindEvents();
      this.setupAccessibility();
    }

    bindEvents() {
      const self = this;

      // Click event for accordion headers (both closed and open state)
      this.accordion.on(
        "click",
        ".rm-accordion-header, .rm-accordion-content-icon",
        function (e) {
          e.preventDefault();
          const item = $(this).closest(".rm-accordion-item");
          self.toggleItem(item);
        }
      );

      // Keyboard navigation
      this.accordion.on("keydown", ".rm-accordion-header", function (e) {
        self.handleKeyboard(e, $(this));
      });

      // Handle window resize for responsive behavior
      $(window).on(
        "resize",
        debounce(function () {
          self.handleResize();
        }, 250)
      );
    }

    setupAccessibility() {
      this.items.each(function (index) {
        const $item = $(this);
        const $header = $item.find(".rm-accordion-header");
        const $content = $item.find(".rm-accordion-content");
        const $title = $item.find(".rm-accordion-title");

        // Set up ARIA attributes
        const headerId = "rm-accordion-header-" + index;
        const contentId = "rm-accordion-content-" + index;

        $header.attr({
          id: headerId,
          role: "button",
          "aria-expanded": "false",
          "aria-controls": contentId,
          tabindex: "0",
        });

        $content.attr({
          id: contentId,
          role: "region",
          "aria-labelledby": headerId,
          "aria-hidden": "true",
        });
      });
    }

    toggleItem(item) {
      const isOpen = item.hasClass("is-open");

      if (isOpen) {
        this.closeItem(item);
      } else {
        // Close all other items first
        this.closeAllItems();
        this.openItem(item);
      }
    }

    openItem(item) {
      const self = this;
      const header = item.find(".rm-accordion-header");
      const content = item.find(".rm-accordion-content");

      // Store original width for closing
      const closedWidth = item.data("closed-width") || "287px";
      item.data("original-width", closedWidth);

      // Update ARIA attributes
      header.attr("aria-expanded", "true");
      content.attr("aria-hidden", "false");

      // Add opening class for animation
      item.addClass("is-opening");

      // Trigger the opening animation
      setTimeout(function () {
        item.addClass("is-open").removeClass("is-opening");

        // Fire custom event
        self.accordion.trigger("rm-accordion:opened", [item]);
      }, 10);

      // Clean up animation classes after animation completes
      setTimeout(function () {
        item.removeClass("is-opening");
      }, this.animationDuration + 50);
    }

    closeItem(item) {
      const self = this;
      const header = item.find(".rm-accordion-header");
      const content = item.find(".rm-accordion-content");

      // Restore original width
      const originalWidth =
        item.data("original-width") || item.data("closed-width") || "287px";

      // Update ARIA attributes
      header.attr("aria-expanded", "false");
      content.attr("aria-hidden", "true");

      // Add closing class for animation
      item.addClass("is-closing");

      // Trigger the closing animation
      setTimeout(function () {
        item.removeClass("is-open is-closing");

        // Restore original widths
        header.css("width", originalWidth);
        content.css("width", originalWidth);

        // Fire custom event
        self.accordion.trigger("rm-accordion:closed", [item]);
      }, this.animationDuration);
    }

    closeAllItems() {
      const self = this;
      this.items.each(function () {
        const $item = $(this);
        if ($item.hasClass("is-open")) {
          self.closeItem($item);
        }
      });
    }

    handleKeyboard(e, header) {
      const item = header.closest(".rm-accordion-item");
      const currentIndex = this.items.index(item);

      switch (e.which) {
        case 13: // Enter
        case 32: // Space
          e.preventDefault();
          this.toggleItem(item);
          break;

        case 38: // Up arrow
          e.preventDefault();
          this.focusPrevious(currentIndex);
          break;

        case 40: // Down arrow
          e.preventDefault();
          this.focusNext(currentIndex);
          break;

        case 36: // Home
          e.preventDefault();
          this.focusFirst();
          break;

        case 35: // End
          e.preventDefault();
          this.focusLast();
          break;
      }
    }

    focusNext(currentIndex) {
      const nextIndex = currentIndex + 1;
      if (nextIndex < this.items.length) {
        this.items.eq(nextIndex).find(".rm-accordion-header").focus();
      } else {
        this.focusFirst();
      }
    }

    focusPrevious(currentIndex) {
      const prevIndex = currentIndex - 1;
      if (prevIndex >= 0) {
        this.items.eq(prevIndex).find(".rm-accordion-header").focus();
      } else {
        this.focusLast();
      }
    }

    focusFirst() {
      this.items.first().find(".rm-accordion-header").focus();
    }

    focusLast() {
      this.items.last().find(".rm-accordion-header").focus();
    }

    handleResize() {
      // Handle any responsive adjustments if needed
      // Currently handled via CSS, but can be extended here
    }

    // Public API methods
    open(index) {
      if (index >= 0 && index < this.items.length) {
        this.openItem(this.items.eq(index));
      }
    }

    close(index) {
      if (index >= 0 && index < this.items.length) {
        this.closeItem(this.items.eq(index));
      }
    }

    closeAll() {
      this.closeAllItems();
    }

    destroy() {
      this.accordion.off("click keydown");
      $(window).off("resize");
      this.items.removeClass("is-open is-opening is-closing");
      this.items
        .find(".rm-accordion-header")
        .removeAttr("id role aria-expanded aria-controls tabindex");
      this.items
        .find(".rm-accordion-content")
        .removeAttr("id role aria-labelledby aria-hidden");
    }
  }

  // Utility function for debouncing
  function debounce(func, wait, immediate) {
    let timeout;
    return function () {
      const context = this,
        args = arguments;
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

  // Initialize accordions when DOM is ready
  $(document).ready(function () {
    initializeAccordions();
  });

  // Initialize accordions (useful for dynamic content)
  function initializeAccordions() {
    $(".rm-accordion").each(function () {
      if (!$(this).data("rm-accordion-initialized")) {
        new RMAccordion(this);
        $(this).data("rm-accordion-initialized", true);
      }
    });
  }

  // Elementor frontend compatibility
  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/rm-accordion.default",
      function ($scope) {
        const accordion = $scope.find(".rm-accordion");
        if (accordion.length && !accordion.data("rm-accordion-initialized")) {
          new RMAccordion(accordion[0]);
          accordion.data("rm-accordion-initialized", true);
        }
      }
    );
  });

  // Make RMAccordion globally available
  window.RMAccordion = RMAccordion;
  window.initializeRMAccordions = initializeAccordions;
})(jQuery);
