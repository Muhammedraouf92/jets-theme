/**
 * Hero scrollytelling — "Flight Path" art direction (PROJECT_CONTEXT.md
 * §6.1). Pins the hero for ~2.2 viewport-heights of scroll and scrubs the
 * background imagery + progress rail to scroll position. Desktop/tablet
 * + no-preference motion only; gsap.matchMedia() both gates this (mobile
 * / reduced-motion never run it — components.css already renders the
 * correct static fallback) and auto-reverts cleanly if the viewport
 * crosses the breakpoint live.
 *
 * The text (eyebrow, headline, sub-headline, CTA) is deliberately NOT
 * part of this timeline — it fades in once on load via a plain CSS
 * animation (components.css), unaffected by scroll. Only the imagery
 * (a continuous Ken-Burns drift, then a cross-fade to the runway shot)
 * and the progress rail are tied to scroll position.
 */
(function () {
	"use strict";

	if ( typeof gsap === "undefined" || typeof ScrollTrigger === "undefined" ) {
		return;
	}

	var heroSection = document.getElementById( "hero" );
	if ( ! heroSection ) {
		return;
	}

	gsap.registerPlugin( ScrollTrigger );

	var mm = gsap.matchMedia();

	mm.add( "(min-width: 1024px) and (prefers-reduced-motion: no-preference)", function () {
		var progressFill = document.querySelector( ".hero__progress-fill" );

		var tl = gsap.timeline( {
			scrollTrigger: {
				trigger: "#hero",
				start: "top top",
				end: "+=" + Math.round( window.innerHeight * 2.2 ),
				scrub: 1,
				pin: ".hero__pin",
				anticipatePin: 1,
				onUpdate: function ( self ) {
					if ( progressFill ) {
						gsap.set( progressFill, { scaleY: self.progress } );
					}
				},
			},
		} );

		// A continuous, restrained scrubbed drift spans the whole sequence
		// rather than a fixed autoplay.
		tl.to( ".hero__media--arrival img", { scale: 1.06, ease: "none", duration: 10 }, 0 );

		// Midway through the pin, cross-fade to the runway — the path
		// opening forward.
		tl.to( ".hero__media--runway", { opacity: 1, scale: 1, ease: "none", duration: 2.5 }, 4 );
		tl.to( ".hero__media--arrival", { opacity: 0.3, ease: "none", duration: 2.5 }, 4 );

		// No other tweens — the pin simply releases into Philosophy at the end.

		return function () {
			tl.scrollTrigger && tl.scrollTrigger.kill();
			tl.kill();
		};
	} );
} )();
