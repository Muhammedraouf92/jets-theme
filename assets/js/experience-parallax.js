/**
 * Experience-category panel parallax (Where We Can Take You, §6, Phase 8).
 * Each panel's image drifts vertically at a slightly different rate than
 * page scroll — desktop only, vanilla (rAF-throttled scroll listener), so
 * this stays outside the GSAP-for-Hero-only policy (PROJECT_CONTEXT.md
 * §8). Mobile/reduced-motion get no parallax — just the plain [data-reveal]
 * fade already on each panel link.
 */
(function () {
	"use strict";

	var group = document.querySelector("[data-parallax-group]");
	if (!group) {
		return;
	}

	var images = Array.prototype.slice.call(group.querySelectorAll(".experience-panel__media"));
	if (!images.length) {
		return;
	}

	var mq = window.matchMedia("(min-width: 900px) and (prefers-reduced-motion: no-preference)");
	var ticking = false;
	var active = false;

	function update() {
		ticking = false;
		var vh = window.innerHeight;
		images.forEach(function (img) {
			var rect = img.parentElement.getBoundingClientRect();
			var center = rect.top + rect.height / 2;
			var offset = (center - vh / 2) * 0.12;
			img.style.transform = "translateY(" + offset.toFixed(1) + "px)";
		});
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	function enable() {
		if (active) {
			return;
		}
		active = true;
		window.addEventListener("scroll", onScroll, { passive: true });
		window.addEventListener("resize", onScroll);
		update();
	}

	function disable() {
		if (!active) {
			return;
		}
		active = false;
		window.removeEventListener("scroll", onScroll);
		window.removeEventListener("resize", onScroll);
		images.forEach(function (img) {
			img.style.transform = "";
		});
	}

	function sync() {
		if (mq.matches) {
			enable();
		} else {
			disable();
		}
	}

	sync();
	mq.addEventListener("change", sync);
})();
