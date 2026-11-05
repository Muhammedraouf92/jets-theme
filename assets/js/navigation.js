/**
 * Header scroll transition + mobile overlay menu.
 * Dependency-free (no build step), scoped to avoid leaking globals.
 */
(function () {
	"use strict";

	var header = document.getElementById( "site-header" );
	if ( ! header ) {
		return;
	}

	/* ---- Sticky solid-on-scroll ---- */

	var SCROLL_THRESHOLD = 40;
	var ticking = false;
	var isTransparentHeader = header.classList.contains( "site-header--transparent" );
	var heroEl = document.getElementById( "hero" );

	function updateHeaderScrollState() {
		var scrolled;

		if ( isTransparentHeader && heroEl ) {
			// The hero may be GSAP-pinned for several viewport-heights of
			// scroll (Phase 6) — a plain scrollY threshold would flip the
			// header solid mid-sequence, well before the hero visually
			// finishes. Stay transparent until the hero (pin-spacer
			// included) has actually scrolled past.
			scrolled = heroEl.getBoundingClientRect().bottom <= 0;
		} else {
			scrolled = window.scrollY > SCROLL_THRESHOLD;
		}

		header.classList.toggle( "site-header--scrolled", scrolled );
		ticking = false;
	}

	function onScroll() {
		if ( ! ticking ) {
			window.requestAnimationFrame( updateHeaderScrollState );
			ticking = true;
		}
	}

	updateHeaderScrollState();
	window.addEventListener( "scroll", onScroll, { passive: true } );

	/* ---- Mobile overlay menu ---- */

	var toggle = document.getElementById( "nav-toggle" );
	var overlay = document.getElementById( "nav-mobile" );

	if ( ! toggle || ! overlay ) {
		return;
	}

	var closeBtn = document.getElementById( "nav-mobile-close" );
	var scrim = overlay.querySelector( ".nav-mobile__scrim" );
	var panel = overlay.querySelector( ".nav-mobile__panel" );
	var FOCUSABLE_SELECTOR =
		'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	function getFocusable() {
		return Array.prototype.slice.call( panel.querySelectorAll( FOCUSABLE_SELECTOR ) );
	}

	function openMenu() {
		overlay.classList.add( "is-open" );
		overlay.setAttribute( "aria-hidden", "false" );
		toggle.setAttribute( "aria-expanded", "true" );
		document.documentElement.classList.add( "nav-open" );

		var focusable = getFocusable();
		if ( focusable.length ) {
			focusable[ 0 ].focus();
		}

		document.addEventListener( "keydown", onKeydown );
	}

	function closeMenu() {
		overlay.classList.remove( "is-open" );
		overlay.setAttribute( "aria-hidden", "true" );
		toggle.setAttribute( "aria-expanded", "false" );
		document.documentElement.classList.remove( "nav-open" );
		document.removeEventListener( "keydown", onKeydown );
		toggle.focus();
	}

	function onKeydown( event ) {
		if ( event.key === "Escape" ) {
			closeMenu();
			return;
		}

		if ( event.key !== "Tab" ) {
			return;
		}

		var focusable = getFocusable();
		if ( ! focusable.length ) {
			return;
		}

		var first = focusable[ 0 ];
		var last = focusable[ focusable.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	toggle.addEventListener( "click", function () {
		if ( overlay.classList.contains( "is-open" ) ) {
			closeMenu();
		} else {
			openMenu();
		}
	} );

	if ( closeBtn ) {
		closeBtn.addEventListener( "click", closeMenu );
	}

	if ( scrim ) {
		scrim.addEventListener( "click", closeMenu );
	}

	overlay.querySelectorAll( ".nav-mobile__menu a" ).forEach( function ( link ) {
		link.addEventListener( "click", closeMenu );
	} );
} )();
