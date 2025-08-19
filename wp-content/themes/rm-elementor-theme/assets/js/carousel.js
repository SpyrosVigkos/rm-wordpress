(function () {
  "use strict";

  function initCarousel(root) {
    const track = root.querySelector(".rm-carousel__track");
    const slides = Array.from(root.querySelectorAll(".rm-carousel__slide"));
    const prevBtn = root.querySelector('[data-dir="prev"]');
    const nextBtn = root.querySelector('[data-dir="next"]');
    const captionDisplay = root.querySelector(".rm-carousel__caption-display");

    if (!track || slides.length === 0) return;

    let currentIndex = 0;
    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;
    let isScrolling = false;

    function getCurrentIndex() {
      const scrollLeft = track.scrollLeft;
      let closest = 0;
      let minDistance = Infinity;

      slides.forEach((slide, index) => {
        const distance = Math.abs(slide.offsetLeft - scrollLeft);
        if (distance < minDistance) {
          minDistance = distance;
          closest = index;
        }
      });

      return closest;
    }

    function updateButtons() {
      if (prevBtn) {
        prevBtn.disabled = currentIndex === 0;
      }
      if (nextBtn) {
        nextBtn.disabled = currentIndex === slides.length - 1;
      }
    }

    function updateCaption() {
      if (captionDisplay) {
        const currentSlide = slides[currentIndex];
        const caption = currentSlide
          ? currentSlide.querySelector(".rm-carousel__caption")
          : null;
        captionDisplay.textContent = caption ? caption.textContent : "";
      }
    }

    function scrollToSlide(index, smooth = true) {
      if (index < 0 || index >= slides.length) return;

      currentIndex = index;
      isScrolling = true;

      const targetSlide = slides[index];
      track.scrollTo({
        left: targetSlide.offsetLeft,
        behavior: smooth ? "smooth" : "auto",
      });

      updateButtons();
      updateCaption();

      // Reset scrolling flag after animation
      setTimeout(() => {
        isScrolling = false;
      }, 300);
    }

    // Button navigation
    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        if (!isDragging && !isScrolling) {
          scrollToSlide(currentIndex - 1);
        }
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        if (!isDragging && !isScrolling) {
          scrollToSlide(currentIndex + 1);
        }
      });
    }

    // Drag functionality
    let dragThreshold = 5; // Minimum pixels to start drag
    let hasDraggedPastThreshold = false;
    let dragDistance = 0;

    track.addEventListener("pointerdown", (e) => {
      // Only handle left mouse button or touch
      if (e.button && e.button !== 0) return;

      isDragging = true;
      hasDraggedPastThreshold = false;
      dragDistance = 0;
      startX = e.clientX;
      startScrollLeft = track.scrollLeft;
      track.setPointerCapture(e.pointerId);
      track.classList.add("is-dragging");
      e.preventDefault();
    });

    track.addEventListener("pointermove", (e) => {
      if (!isDragging) return;
      e.preventDefault();

      const x = e.clientX;
      const deltaX = x - startX;
      dragDistance = deltaX;

      // Check if we've moved past the threshold
      if (!hasDraggedPastThreshold && Math.abs(deltaX) > dragThreshold) {
        hasDraggedPastThreshold = true;
      }

      if (hasDraggedPastThreshold) {
        track.scrollLeft = startScrollLeft - deltaX;
      }
    });

    track.addEventListener("pointerup", (e) => {
      if (!isDragging) return;

      isDragging = false;
      track.releasePointerCapture(e.pointerId);
      track.classList.remove("is-dragging");

      // Only snap if we actually dragged past threshold
      if (hasDraggedPastThreshold) {
        let targetIndex = currentIndex;

        // Determine target slide based on drag direction and distance
        const slideWidth = slides[0].offsetWidth + 24; // slide width + gap
        const dragThresholdForSlideChange = slideWidth * 0.3; // 30% of slide width

        if (Math.abs(dragDistance) > dragThresholdForSlideChange) {
          if (dragDistance > 0) {
            // Dragged right (showing previous slide)
            targetIndex = Math.max(0, currentIndex - 1);
          } else {
            // Dragged left (showing next slide)
            targetIndex = Math.min(slides.length - 1, currentIndex + 1);
          }
        }

        // Update current index and scroll to target
        currentIndex = targetIndex;
        updateButtons();

        const targetSlide = slides[currentIndex];
        track.scrollTo({
          left: targetSlide.offsetLeft,
          behavior: "smooth",
        });
      }
    });

    // Handle pointer cancel (when drag is interrupted)
    track.addEventListener("pointercancel", (e) => {
      if (isDragging) {
        isDragging = false;
        track.classList.remove("is-dragging");
        hasDraggedPastThreshold = false;
      }
    });

    // Handle scroll events (for manual scrolling or after drag)
    let scrollTimeout;
    track.addEventListener("scroll", () => {
      if (isDragging || isScrolling) return;

      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        const newIndex = getCurrentIndex();
        if (newIndex !== currentIndex) {
          currentIndex = newIndex;
          updateButtons();
          updateCaption();
        }
      }, 150);
    });

    // Keyboard navigation
    root.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") {
        e.preventDefault();
        scrollToSlide(currentIndex - 1);
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        scrollToSlide(currentIndex + 1);
      }
    });

    // Initialize
    currentIndex = 0;
    updateButtons();
    updateCaption();
  }

  function initAll(context = document) {
    let root = context;
    if (window.jQuery && context instanceof window.jQuery) {
      root = context[0];
    }
    if (!root || !root.querySelectorAll) root = document;
    root.querySelectorAll(".rm-carousel").forEach(initCarousel);
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  // Elementor integration
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/rm-carousel.default",
      ($scope) => {
        initAll($scope[0] || $scope);
      }
    );
  }
})();
