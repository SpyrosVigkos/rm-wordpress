/**
 * ReelMetrics Timeline/Stepper Widget JavaScript
 * Simple, stable scroll-triggered functionality
 */

(function ($) {
  "use strict";

  class RMTimeline {
    constructor(element) {
      this.timeline = $(element);
      this.steps = this.timeline.find(".rm-timeline-step");
      this.images = this.timeline.find(".rm-timeline-image");
      this.imageContainer = this.timeline.find(".rm-timeline-image-sticky");
      this.stickyOffset = parseInt(this.timeline.data("sticky-offset")) || 100;
      this.currentStep = 0;
      this.isScrolling = false;

      this.init();
    }

    init() {
      // Step 1 is active by default via CSS step-one class
      // Just set up scroll handler and activate step 1 in JS state
      this.currentStep = 0;
      this.setupScrollHandler();
      this.updateStickyOffset();
      console.log("✅ Timeline initialized - Step 1 active via step-one class");
    }

    setupScrollHandler() {
      const self = this;
      let scrollTimeout = null;

      // Simple scroll-based detection with throttling
      $(window).on("scroll", function () {
        if (scrollTimeout) {
          clearTimeout(scrollTimeout);
        }

        scrollTimeout = setTimeout(() => {
          self.handleScroll();
        }, 100); // Throttle scroll handling
      });
    }

    handleScroll() {
      const windowTop = $(window).scrollTop();
      const windowHeight = $(window).height();
      const viewportCenter = windowTop + windowHeight / 2;

      let activeStep = 0;
      let closestDistance = Infinity;

      // Find the step closest to viewport center
      this.steps.each((index, element) => {
        const stepTop = $(element).offset().top;
        const stepCenter = stepTop + $(element).height() / 2;
        const distance = Math.abs(viewportCenter - stepCenter);

        if (distance < closestDistance) {
          closestDistance = distance;
          activeStep = index;
        }
      });

      // Only activate if different from current
      if (activeStep !== this.currentStep) {
        this.activateStep(activeStep);
      }
    }

    activateStep(stepIndex) {
      if (
        stepIndex === this.currentStep ||
        stepIndex < 0 ||
        stepIndex >= this.steps.length
      ) {
        return;
      }

      console.log(`✅ Activating step: ${stepIndex + 1}`);

      this.currentStep = stepIndex;

      // Clean state management using step-one class
      console.log("🧹 Managing step states with classes");

      // Handle step-one class specifically
      const $stepOne = this.steps.filter(".step-one");
      if (stepIndex === 0) {
        // Activating step 1 - remove deactivated class
        $stepOne.removeClass("deactivated");
        console.log("✅ Step 1 activated via step-one class");
      } else {
        // Activating other step - deactivate step 1
        $stepOne.addClass("deactivated");
        console.log("🔴 Step 1 deactivated via deactivated class");
      }

      // Remove active from all steps
      this.steps.removeClass("active");
      this.images.removeClass("active");

      // Add active to current step (unless it's step 1, which uses step-one class)
      if (stepIndex !== 0) {
        this.steps.eq(stepIndex).addClass("active");
        console.log(`✅ Step ${stepIndex + 1} activated via active class`);
      }

      this.images.eq(stepIndex).addClass("active");
    }

    updateStickyOffset() {
      this.imageContainer.css("top", this.stickyOffset + "px");
    }

    destroy() {
      $(window).off("scroll");
    }
  }

  // Initialize timelines
  $(document).ready(function () {
    $(".rm-timeline").each(function () {
      if (!$(this).data("rm-timeline-initialized")) {
        new RMTimeline(this);
        $(this).data("rm-timeline-initialized", true);
      }
    });
  });

  // Elementor frontend compatibility
  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/rm-timeline.default",
      function ($scope) {
        const timeline = $scope.find(".rm-timeline");
        if (timeline.length && !timeline.data("rm-timeline-initialized")) {
          new RMTimeline(timeline[0]);
          timeline.data("rm-timeline-initialized", true);
        }
      }
    );
  });
})(jQuery);
