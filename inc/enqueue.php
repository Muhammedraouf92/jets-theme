<?php
/**
 * Styles and scripts registration.
 *
 * CSS load order matters: variables must load before everything that
 * consumes them; layout/components before responsive overrides.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jets_enqueue_assets() {
	$css_dir = JETS_THEME_DIR . '/assets/css';
	$css_uri = JETS_THEME_URI . '/assets/css';

	$stylesheets = array(
		'jets-variables'   => 'variables.css',
		'jets-base'        => 'base.css',
		'jets-layout'      => 'layout.css',
		'jets-components'  => 'components.css',
		'jets-animations'  => 'animations.css',
		'jets-responsive'  => 'responsive.css',
	);

	$deps = array();

	// Swiper's own stylesheet must print before ours so the overrides in
	// components.css (carousel theming) win the cascade.
	if ( is_front_page() ) {
		$vendor_css_dir = $css_dir . '/vendor';
		$vendor_css_uri = $css_uri . '/vendor';
		$swiper_css     = $vendor_css_dir . '/swiper-bundle.min.css';

		wp_enqueue_style(
			'swiper',
			$vendor_css_uri . '/swiper-bundle.min.css',
			array(),
			file_exists( $swiper_css ) ? filemtime( $swiper_css ) : JETS_THEME_VERSION
		);
		$deps = array( 'swiper' );
	}

	foreach ( $stylesheets as $handle => $file ) {
		$path = $css_dir . '/' . $file;
		wp_enqueue_style(
			$handle,
			$css_uri . '/' . $file,
			$deps,
			file_exists( $path ) ? filemtime( $path ) : JETS_THEME_VERSION
		);
		// Each stylesheet depends on the previous one, preserving cascade order.
		$deps = array( $handle );
	}

	$js_dir = JETS_THEME_DIR . '/assets/js';
	$js_uri = JETS_THEME_URI . '/assets/js';

	$scripts = array(
		'jets-main'       => 'main.js',
		'jets-animations' => 'animations.js',
		'jets-navigation' => 'navigation.js',
	);

	foreach ( $scripts as $handle => $file ) {
		$path = $js_dir . '/' . $file;
		wp_enqueue_script(
			$handle,
			$js_uri . '/' . $file,
			array(),
			file_exists( $path ) ? filemtime( $path ) : JETS_THEME_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	// GSAP + ScrollTrigger (Hero scrollytelling, PROJECT_CONTEXT.md §6.1/§8)
	// and Swiper (Services carousel, §5) are the theme's only deliberate
	// exceptions to its vanilla-JS-by-default approach — both front-page-only,
	// purpose-built interactions that would be a lot of fragile hand-rolled
	// code to reproduce from scratch.
	if ( is_front_page() ) {
		$vendor_dir = $js_dir . '/vendor';
		$vendor_uri = $js_uri . '/vendor';

		wp_enqueue_script(
			'gsap',
			$vendor_uri . '/gsap.min.js',
			array(),
			file_exists( $vendor_dir . '/gsap.min.js' ) ? filemtime( $vendor_dir . '/gsap.min.js' ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		wp_enqueue_script(
			'gsap-scrolltrigger',
			$vendor_uri . '/ScrollTrigger.min.js',
			array( 'gsap' ),
			file_exists( $vendor_dir . '/ScrollTrigger.min.js' ) ? filemtime( $vendor_dir . '/ScrollTrigger.min.js' ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		$hero_path = $js_dir . '/hero-scrollytelling.js';
		wp_enqueue_script(
			'jets-hero-scrollytelling',
			$js_uri . '/hero-scrollytelling.js',
			array( 'gsap-scrolltrigger' ),
			file_exists( $hero_path ) ? filemtime( $hero_path ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		wp_enqueue_script(
			'swiper',
			$vendor_uri . '/swiper-bundle.min.js',
			array(),
			file_exists( $vendor_dir . '/swiper-bundle.min.js' ) ? filemtime( $vendor_dir . '/swiper-bundle.min.js' ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		$services_carousel_path = $js_dir . '/services-carousel.js';
		wp_enqueue_script(
			'jets-services-carousel',
			$js_uri . '/services-carousel.js',
			array( 'swiper' ),
			file_exists( $services_carousel_path ) ? filemtime( $services_carousel_path ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		// Experience-category panel parallax (§6, Phase 8) — plain vanilla
		// scroll listener, not part of the GSAP/Swiper exceptions above;
		// still front-page-only since that's the only place the markup exists.
		$experience_parallax_path = $js_dir . '/experience-parallax.js';
		wp_enqueue_script(
			'jets-experience-parallax',
			$js_uri . '/experience-parallax.js',
			array(),
			file_exists( $experience_parallax_path ) ? filemtime( $experience_parallax_path ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}

	// Contact page's inquiry form (Phase 9) — only needed where the form
	// actually lives.
	if ( is_page_template( 'page-contact.php' ) ) {
		// intl-tel-input (self-hosted, v23.8.0 — the last release with a
		// classic UMD build; v24+ went ESM-only, which would need a
		// different loading strategy than every other vendor script in
		// this theme). Adds a country-code/flag dropdown to the phone
		// field and formats the submitted number to E.164. The "WithUtils"
		// bundle already includes the formatting/validation utils inline,
		// so no separate utilsScript file is needed.
		$iti_dir = JETS_THEME_DIR . '/assets/vendor/intl-tel-input';
		$iti_uri = JETS_THEME_URI . '/assets/vendor/intl-tel-input';

		wp_enqueue_style(
			'intl-tel-input',
			$iti_uri . '/css/intlTelInput.min.css',
			array(),
			file_exists( $iti_dir . '/css/intlTelInput.min.css' ) ? filemtime( $iti_dir . '/css/intlTelInput.min.css' ) : JETS_THEME_VERSION
		);

		wp_enqueue_script(
			'intl-tel-input',
			$iti_uri . '/js/intlTelInputWithUtils.min.js',
			array(),
			file_exists( $iti_dir . '/js/intlTelInputWithUtils.min.js' ) ? filemtime( $iti_dir . '/js/intlTelInputWithUtils.min.js' ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		$forms_path = $js_dir . '/forms.js';
		wp_enqueue_script(
			'jets-forms',
			$js_uri . '/forms.js',
			array( 'intl-tel-input' ),
			file_exists( $forms_path ) ? filemtime( $forms_path ) : JETS_THEME_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
		wp_localize_script( 'jets-forms', 'jetsFormsData', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'jets_enqueue_assets' );
