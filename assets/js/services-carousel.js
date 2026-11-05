/**
 * Services showcase carousel. Swiper handles touch/drag, snapping,
 * pagination and responsive slides-per-view — see enqueue.php for why
 * this is one of the theme's two deliberate non-vanilla-JS exceptions.
 * Markup falls back to a plain scroll-snap row (styled in components.css)
 * until this runs, so the section still works with JS disabled.
 */
(function () {
	"use strict";

	if ( typeof Swiper === "undefined" ) {
		return;
	}

	var root = document.querySelector( "[data-services-carousel]" );
	if ( ! root ) {
		return;
	}

	var track = root.querySelector( "[data-services-carousel-track]" );
	if ( ! track ) {
		return;
	}

	new Swiper( track, {
		slidesPerView: 1.15,
		spaceBetween: 20,
		speed: 550,
		grabCursor: true,
		rtl: document.documentElement.dir === "rtl",
		keyboard: { enabled: true },
		a11y: { enabled: true },
		pagination: {
			el: root.querySelector( "[data-services-carousel-pagination]" ),
			clickable: true,
		},
		navigation: {
			nextEl: root.querySelector( "[data-services-carousel-next]" ),
			prevEl: root.querySelector( "[data-services-carousel-prev]" ),
		},
		breakpoints: {
			600: { slidesPerView: 1.6, spaceBetween: 24 },
			900: { slidesPerView: 2.4, spaceBetween: 28 },
			1200: { slidesPerView: 3.2, spaceBetween: 32 },
		},
	} );
} )();
