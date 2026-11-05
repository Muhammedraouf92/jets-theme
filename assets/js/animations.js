/**
 * Site-wide scroll-reveal utility (PROJECT_CONTEXT.md §8/§11 — Phase 11
 * consolidation). Adds `.is-visible` once to any `[data-reveal]` element as
 * it enters the viewport. Vanilla IntersectionObserver, not GSAP — GSAP
 * stays reserved for the Hero's pin+scrub (see hero-scrollytelling.js and
 * the Decisions Log entry on why it wasn't extended to the Services track
 * or the Network/Technology connector-draws in Phase 11: both are already
 * well served by Swiper and a plain CSS stroke-dashoffset transition
 * respectively, so adding a second scroll-animation engine for them would
 * be new complexity without a corresponding gain). The actual per-element
 * animation (fade, line draw, clip-path wipe, word cascade, connector
 * draw) lives entirely in animations.css/components.css; this module only
 * flips the class.
 *
 * If the element contains a lazy-loaded <img> that hasn't finished
 * downloading yet, revealing on intersection alone plays the animation
 * over an empty image box — on a slow connection or a fast scroll this
 * reads as "the text showed up but there's no image here." So the class
 * is only added once every descendant image has actually loaded (or
 * failed — a broken image shouldn't block the section forever), with a
 * short safety timeout in case neither ever fires.
 */
(function () {
  "use strict";

  var targets = document.querySelectorAll("[data-reveal]");

  if (!targets.length) {
    return;
  }

  var IMAGE_WAIT_TIMEOUT = 4000;

  function reveal(el) {
    el.classList.add("is-visible");
  }

  function revealWhenImagesReady(el) {
    var images = Array.prototype.filter.call(
      el.querySelectorAll("img"),
      function (img) {
        return !img.complete;
      },
    );

    if (!images.length) {
      reveal(el);
      return;
    }

    var pending = images.length;
    var done = false;

    var timeoutId = setTimeout(function () {
      done = true;
      reveal(el);
    }, IMAGE_WAIT_TIMEOUT);

    function onSettle() {
      pending--;
      if (done || pending > 0) {
        return;
      }
      done = true;
      clearTimeout(timeoutId);
      reveal(el);
    }

    images.forEach(function (img) {
      img.addEventListener("load", onSettle, { once: true });
      img.addEventListener("error", onSettle, { once: true });
    });
  }

  if (!("IntersectionObserver" in window)) {
    targets.forEach(reveal);
    return;
  }

  // rootMargin's positive bottom value extends the trigger zone below the
  // viewport, so a section starts revealing slightly before it scrolls
  // into view rather than only once it's already there; threshold 0.1
  // means it doesn't wait for much of the element to be visible either.
  // Passive by design — this observer never touches scroll/input handling,
  // so it can't block scroll or delay a tap the way a scroll-jacking
  // technique could.
  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          observer.unobserve(entry.target);
          revealWhenImagesReady(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px 5% 0px" },
  );

  targets.forEach(function (el) {
    observer.observe(el);
  });
})();
