/**
 * ReelMetrics Timeline/Stepper - Vanilla Controller
 * - Semantic, class-based state (.is-active)
 * - Desktop: choose step closest to viewport center
 * - Mobile: horizontal scroll-snap, choose centered card
 * - Keyboard accessible
 * - Reduced motion aware
 */
(function () {
  "use strict";

  const SELECTORS = {
    root: ".rm-timeline",
    list: ".rm-timeline__list",
    step: ".rm-timeline-step",
    image: ".rm-timeline-image",
  };

  class TimelineController {
    constructor(root) {
      this.root = root;
      this.list = root.querySelector(SELECTORS.list);
      this.steps = Array.from(root.querySelectorAll(SELECTORS.step));
      this.images = Array.from(root.querySelectorAll(SELECTORS.image));
      this.currentIndex = this.getInitialIndex();
      this.isMobile = () => window.matchMedia("(max-width: 768px)").matches;
      this.prefersReduced = window.matchMedia(
        "(prefers-reduced-motion: reduce)"
      ).matches;
      this.scrollHandler = null;
      this.resizeHandler = null;
      this.io = null;
      this.wheelHandler = null;
      this.wheelStepLock = false;
      this.wheelUnlockTimer = null;
    }

    // Engage interactions only when the section is near viewport center
    isInActivationBand() {
      const rect = this.root.getBoundingClientRect();
      const viewportCenter = window.innerHeight / 2;
      const sectionCenter = rect.top + rect.height / 2;
      const band = Math.max(80, window.innerHeight * 0.2); // 20% vh or 80px
      return Math.abs(sectionCenter - viewportCenter) <= band;
    }

    getInitialIndex() {
      const explicit = this.steps.findIndex((s) =>
        s.classList.contains("is-active")
      );
      return explicit >= 0 ? explicit : 0;
    }

    init() {
      // Ensure only one active on init
      this.steps.forEach((step, i) =>
        step.classList.toggle("is-active", i === this.currentIndex)
      );
      this.images.forEach((img, i) =>
        img.classList.toggle("is-active", i === this.currentIndex)
      );
      this.syncAria();
      this.bindKeyboard();

      if (this.isMobile()) {
        this.bindMobileScroll();
      } else {
        this.bindDesktopScroll();
      }

      this.resizeHandler = () => {
        this.unbindScroll();
        if (this.isMobile()) this.bindMobileScroll();
        else this.bindDesktopScroll();
      };
      window.addEventListener("resize", this.resizeHandler, { passive: true });
    }

    destroy() {
      this.unbindScroll();
      window.removeEventListener("resize", this.resizeHandler);
      this.unbindKeyboard();
    }

    // Desktop: pick step closest to viewport center
    bindDesktopScroll() {
      const onScroll = () => {
        if (!this.isInActivationBand()) return;
        const winMid = window.scrollY + window.innerHeight / 2;
        let best = 0,
          bestDist = Infinity;
        this.steps.forEach((el, i) => {
          const rect = el.getBoundingClientRect();
          const mid = rect.top + window.scrollY + rect.height / 2;
          const dist = Math.abs(winMid - mid);
          if (dist < bestDist) {
            bestDist = dist;
            best = i;
          }
        });
        this.activate(best);
      };
      this.scrollHandler = throttle(onScroll, 100);
      window.addEventListener("scroll", this.scrollHandler, { passive: true });
    }

    // Mobile: use horizontal scroll container and choose centered card
    bindMobileScroll() {
      if (!this.list) return;
      const onScroll = () => {
        const box = this.list.getBoundingClientRect();
        const center = box.left + box.width / 2;
        let best = 0,
          bestDist = Infinity;
        this.steps.forEach((el, i) => {
          const r = el.getBoundingClientRect();
          const mid = r.left + r.width / 2;
          const dist = Math.abs(center - mid);
          if (dist < bestDist) {
            bestDist = dist;
            best = i;
          }
        });
        this.activate(best);
      };
      this.scrollHandler = throttle(onScroll, 120);
      this.list.addEventListener("scroll", this.scrollHandler, {
        passive: true,
      });

      // Route vertical wheel to horizontal while inside the timeline section.
      this.wheelHandler = (e) => this.routeWheelToHorizontal(e);
      this.root.addEventListener("wheel", this.wheelHandler, {
        passive: false,
      });
    }

    unbindScroll() {
      if (this.scrollHandler) {
        if (this.isMobile() && this.list)
          this.list.removeEventListener("scroll", this.scrollHandler);
        else window.removeEventListener("scroll", this.scrollHandler);
      }
      this.scrollHandler = null;
      if (this.wheelHandler) {
        this.root.removeEventListener("wheel", this.wheelHandler);
      }
      this.wheelHandler = null;
    }

    activate(index) {
      if (
        index === this.currentIndex ||
        index < 0 ||
        index >= this.steps.length
      )
        return;
      const prevIndex = this.currentIndex;
      this.currentIndex = index;

      // Animation classes for smooth transitions
      if (this.steps[prevIndex]) {
        this.steps[prevIndex].classList.remove("is-active", "is-animating-in");
        this.steps[prevIndex].classList.add("is-animating-out");
        setTimeout(() => {
          this.steps[prevIndex].classList.remove("is-animating-out");
        }, 220);
      }

      this.steps.forEach((s, i) => {
        const makeActive = i === index;
        s.classList.toggle("is-active", makeActive);
        if (makeActive) {
          s.classList.remove("is-animating-out");
          // Trigger reflow to restart animation reliably
          // eslint-disable-next-line no-unused-expressions
          s.offsetHeight;
          s.classList.add("is-animating-in");
          setTimeout(() => s.classList.remove("is-animating-in"), 420);
        }
      });
      this.images.forEach((img, i) => {
        img.classList.toggle("is-active", i === index);
      });
      // step-one deactivation logic
      const stepOne = this.steps.find((s) => s.classList.contains("step-one"));
      if (stepOne) stepOne.classList.toggle("deactivated", index !== 0);
      this.syncAria();
    }

    syncAria() {
      this.steps.forEach((li, i) => {
        if (i === this.currentIndex) li.setAttribute("aria-current", "step");
        else li.removeAttribute("aria-current");
        // Make active step tabbable
        li.tabIndex = i === this.currentIndex ? 0 : -1;
      });
    }

    bindKeyboard() {
      this.keyHandler = (e) => {
        const key = e.key;
        if (
          ![
            "ArrowRight",
            "ArrowDown",
            "ArrowLeft",
            "ArrowUp",
            "Enter",
            " ",
          ].includes(key)
        )
          return;
        e.preventDefault();
        let next = this.currentIndex;
        if (key === "ArrowRight" || key === "ArrowDown")
          next = Math.min(this.steps.length - 1, this.currentIndex + 1);
        if (key === "ArrowLeft" || key === "ArrowUp")
          next = Math.max(0, this.currentIndex - 1);
        if (key === "Enter" || key === " ") next = this.currentIndex; // ensure focus stays on active
        this.activate(next);
        // Scroll into view for mobile horizontal
        if (this.isMobile() && this.list)
          this.steps[next].scrollIntoView({
            behavior: this.prefersReduced ? "auto" : "smooth",
            inline: "center",
            block: "nearest",
          });
      };
      this.root.addEventListener("keydown", this.keyHandler);
    }

    unbindKeyboard() {
      if (this.keyHandler)
        this.root.removeEventListener("keydown", this.keyHandler);
      this.keyHandler = null;
      if (this.wheelUnlockTimer) {
        clearTimeout(this.wheelUnlockTimer);
        this.wheelUnlockTimer = null;
      }
    }

    // Convert vertical wheel into step-by-step horizontal navigation on mobile.
    routeWheelToHorizontal(e) {
      if (!this.isMobile() || !this.list) return;
      if (!this.isInActivationBand()) return; // only engage when centered

      // Ignore pure horizontal wheels; only act on vertical intent
      if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;

      const goingDown = e.deltaY > 0;
      const goingUp = e.deltaY < 0;
      const atFirst = this.currentIndex === 0;
      const atLast = this.currentIndex === this.steps.length - 1;

      // Release control at edges so page can scroll
      if ((goingUp && atFirst) || (goingDown && atLast)) return;

      // Step-by-step navigation
      e.preventDefault();
      if (this.wheelStepLock) return;
      this.wheelStepLock = true;
      const target = Math.max(
        0,
        Math.min(
          this.steps.length - 1,
          this.currentIndex + (goingDown ? 1 : -1)
        )
      );
      this.activate(target);
      this.scrollToIndex(target);
      if (this.wheelUnlockTimer) clearTimeout(this.wheelUnlockTimer);
      this.wheelUnlockTimer = setTimeout(
        () => {
          this.wheelStepLock = false;
        },
        this.prefersReduced ? 60 : 260
      );
    }

    scrollToIndex(index) {
      if (!this.list || index < 0 || index >= this.steps.length) return;
      this.steps[index].scrollIntoView({
        behavior: this.prefersReduced ? "auto" : "smooth",
        inline: "center",
        block: "nearest",
      });
    }
  }

  // Small utilities
  function throttle(fn, wait) {
    let t = 0;
    return (...args) => {
      const now = Date.now();
      if (now - t >= wait) {
        t = now;
        fn.apply(null, args);
      }
    };
  }

  // Bootstrap for WP and Elementor
  function initAllTimelines(context = document) {
    context.querySelectorAll(SELECTORS.root).forEach((root) => {
      if (root.__rmTimeline) return;
      const ctl = new TimelineController(root);
      ctl.init();
      root.__rmTimeline = ctl;
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => initAllTimelines());
  } else {
    initAllTimelines();
  }

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/rm-timeline.default",
      ($scope) => {
        initAllTimelines($scope[0] || $scope);
      }
    );
  }
})();
