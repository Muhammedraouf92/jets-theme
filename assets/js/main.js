/**
 * Entry point — orchestrates the small, focused modules below.
 * Kept dependency-free (no build step required) per project brief.
 *
 * Scroll-reveal and all other animation logic live in animations.js
 * (consolidated there in Phase 11) — this file only handles the
 * no-js/js class swap that everything else's CSS gates on.
 */
(function () {
  "use strict";

  document.documentElement.classList.remove("no-js");
  document.documentElement.classList.add("js");
})();
