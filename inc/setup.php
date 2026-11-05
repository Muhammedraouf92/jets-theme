<?php
/**
 * Core theme setup: supports, menus, image sizes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme supports, menu registration, translation loading.
 */
function jets_setup() {
	load_theme_textdomain( 'jets-theme', JETS_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'jets-theme' ),
		'footer'  => __( 'Footer Navigation', 'jets-theme' ),
	) );

	add_image_size( 'jets-hero', 1920, 1080, true );
	add_image_size( 'jets-card', 640, 480, true );
	add_image_size( 'jets-wide', 1600, 900, true );
}
add_action( 'after_setup_theme', 'jets_setup' );

/**
 * The favicon is theme-controlled (header.php's own <link rel="icon">
 * tags, generated from assets/images/logo.jpeg) rather than the wp-admin Customizer's
 * Site Icon — same "hardcode it in the theme" precedent as the footer
 * and header logo. Unhooking core's own site icon output means it can't
 * later duplicate/conflict with ours if someone sets a Site Icon in the
 * Customizer.
 */
remove_action( 'wp_head', 'wp_site_icon' );

/**
 * Output the correct html lang/dir attributes for the active language.
 * Polylang overrides `lang` via its own hooks once active (Phase 2);
 * this filter is the safe default in the meantime.
 */
function jets_language_attributes( $output ) {
	if ( is_rtl() && false === strpos( $output, 'dir=' ) ) {
		$output .= ' dir="rtl"';
	}
	return $output;
}
add_filter( 'language_attributes', 'jets_language_attributes' );
