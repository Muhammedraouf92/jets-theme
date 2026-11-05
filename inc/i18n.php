<?php
/**
 * Polylang integration.
 *
 * Polylang (once activated — see PROJECT_CONTEXT.md §11 for why it was
 * chosen over WPML/TranslatePress) handles post/menu/CPT translation and
 * its own `<html lang/dir>` output natively. This file only covers what
 * Polylang doesn't do for us out of the box: registering hardcoded theme
 * UI strings for translation, and a template-friendly language-switcher
 * helper.
 *
 * Every function here degrades gracefully (no-ops / empty arrays) when
 * Polylang isn't active yet, so the theme never fatals without it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme-hardcoded UI strings with Polylang's String Translation
 * screen, so copy like button labels can be translated without touching
 * PHP. Grouped under "JETS Theme" in Languages > Translations.
 */
function jets_register_translatable_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$strings = array(
		'nav_menu_toggle'        => __( 'Menu', 'jets-theme' ),
		'nav_menu_close'         => __( 'Close', 'jets-theme' ),
		'cta_explore_services'   => __( 'Explore Our Services', 'jets-theme' ),
		'cta_plan_journey'       => __( 'Plan Your Journey', 'jets-theme' ),
		'cta_view_all_services'  => __( 'View All Services', 'jets-theme' ),
		'cta_contact_us'         => __( 'Contact Us', 'jets-theme' ),
		'cta_start_planning'     => __( 'Start Planning', 'jets-theme' ),
		'cta_enquire'            => __( 'Enquire About This Service', 'jets-theme' ),
		'cta_explore_tours'      => __( 'Explore Tours & Excursions', 'jets-theme' ),
		'cta_support_business'   => __( 'See how we support your business', 'jets-theme' ),
		'label_read_more'        => __( 'Read more', 'jets-theme' ),
		'label_all_rights'       => __( 'All rights reserved.', 'jets-theme' ),
		'label_skip_to_content'  => __( 'Skip to content', 'jets-theme' ),
		'form_name'              => __( 'Full Name', 'jets-theme' ),
		'form_email'             => __( 'Email Address', 'jets-theme' ),
		'form_phone'             => __( 'Phone Number', 'jets-theme' ),
		'form_message'           => __( 'Message', 'jets-theme' ),
		'form_submit'            => __( 'Send Inquiry', 'jets-theme' ),
		'form_success'           => __( 'Thank you — we\'ll be in touch shortly.', 'jets-theme' ),
		'form_error'             => __( 'Something went wrong. Please try again.', 'jets-theme' ),
		'home_about_eyebrow'     => __( 'About JETS', 'jets-theme' ),
		'home_about_heading'     => __( 'Who We Are', 'jets-theme' ),
		'home_why_heading'       => __( 'Why JETS?', 'jets-theme' ),
		'home_services_eyebrow'  => __( 'What We Offer', 'jets-theme' ),
		'home_services_heading'  => __( 'One partner. Every part of the journey.', 'jets-theme' ),
		'home_services_intro'    => __( 'JETS provides an integrated range of travel and tourism solutions covering the complete travel journey — from planning and booking to execution and client support — with flexible services designed for individuals, businesses, and travel partners.', 'jets-theme' ),
		'home_experiences_eyebrow' => __( 'Where We Can Take You', 'jets-theme' ),
		'home_experiences_heading' => __( 'Experience More. Discover Further.', 'jets-theme' ),
		'home_delivery_eyebrow'  => __( 'How We Deliver', 'jets-theme' ),
		'home_delivery_heading'  => __( 'Operational rigor behind every journey.', 'jets-theme' ),
		'home_delivery_step_count' => __( '%d-step process', 'jets-theme' ),
		'home_technology_eyebrow' => __( 'Digital Travel Solutions', 'jets-theme' ),
		'home_technology_heading' => __( 'Connected Travel. Smarter Operations.', 'jets-theme' ),
		'home_network_eyebrow'   => __( 'Network & Who We Serve', 'jets-theme' ),
		'home_network_heading'   => __( 'A connected world, working for you.', 'jets-theme' ),
		'home_network_partnerships' => __( 'Partnerships', 'jets-theme' ),
		'home_network_our_network'  => __( 'Our Network', 'jets-theme' ),
		'home_network_clients'   => __( 'Clients & Partners', 'jets-theme' ),
		'footer_nav_main'        => __( 'Main Page', 'jets-theme' ),
		'footer_nav_social'      => __( 'Social Media', 'jets-theme' ),
		'footer_privacy_policy'  => __( 'Privacy Policy', 'jets-theme' ),
		'footer_terms_of_use'    => __( 'Terms of Use', 'jets-theme' ),
		'footer_credit'          => __( 'Made by %s', 'jets-theme' ),
		'footer_tagline'         => __( 'Integrated Travel Solutions. Global Standards.', 'jets-theme' ),
		'services_archive_title' => __( 'All Services', 'jets-theme' ),
		'services_archive_empty' => __( 'No services found.', 'jets-theme' ),
			'nav_whatsapp'           => __( 'Chat on WhatsApp', 'jets-theme' ),
			'page_about_eyebrow'     => __( 'Our Company', 'jets-theme' ),
			'page_about_title'       => __( 'About JETS', 'jets-theme' ),
			'page_about_mission'     => __( 'Our Mission', 'jets-theme' ),
			'page_about_vision'      => __( 'Our Vision', 'jets-theme' ),
			'page_partners_eyebrow'  => __( 'Partnerships & Network', 'jets-theme' ),
			'page_partners_title'    => __( 'Partners & Network', 'jets-theme' ),
			'page_contact_eyebrow'   => __( 'Get In Touch', 'jets-theme' ),
			'page_contact_title'     => __( 'Contact Us', 'jets-theme' ),
			'page_contact_intro'     => __( "Tell us where you're headed — our team will take it from there.", 'jets-theme' ),
			'contact_form_heading'   => __( 'Send an Inquiry', 'jets-theme' ),
			'contact_label_phone'    => __( 'Phone', 'jets-theme' ),
			'contact_label_email'    => __( 'Email', 'jets-theme' ),
			'contact_label_address'  => __( 'Address', 'jets-theme' ),
			'contact_label_hours'    => __( 'Business Hours', 'jets-theme' ),
			'contact_whatsapp_cta'   => __( 'Message us on WhatsApp', 'jets-theme' ),
			'contact_map_heading'    => __( 'Our Location', 'jets-theme' ),
			'contact_map_title'      => __( 'Map showing our location', 'jets-theme' ),
			'contact_form_error_name'    => __( 'Please enter your name.', 'jets-theme' ),
			'contact_form_error_email'   => __( 'Please enter a valid email address.', 'jets-theme' ),
			'contact_form_error_message' => __( 'Please enter a message.', 'jets-theme' ),
			'contact_form_error_phone_required' => __( 'Please enter your phone number.', 'jets-theme' ),
			'contact_form_error_phone_invalid'  => __( 'Please enter a valid phone number.', 'jets-theme' ),
			'contact_form_error_summary' => __( 'Please correct the highlighted fields.', 'jets-theme' ),
			'error_404_eyebrow'      => __( 'Error 404', 'jets-theme' ),
			'error_404_title'        => __( 'Page Not Found', 'jets-theme' ),
			'error_404_text'         => __( "The page you're looking for doesn't exist or may have moved.", 'jets-theme' ),
			'error_404_home_cta'     => __( 'Back to Homepage', 'jets-theme' ),
			'search_results_for'     => __( 'Search Results for:', 'jets-theme' ),
			'search_no_results_text' => __( "We couldn't find anything matching your search. Try different keywords, or browse our services below.", 'jets-theme' ),
			'search_field_label'     => __( 'Search', 'jets-theme' ),
			'search_placeholder'     => __( 'Search the site…', 'jets-theme' ),
			'pagination_previous'    => __( 'Previous', 'jets-theme' ),
			'pagination_next'        => __( 'Next', 'jets-theme' ),
	);

	foreach ( $strings as $name => $string ) {
		pll_register_string( $name, $string, 'JETS Theme' );
	}
}
add_action( 'init', 'jets_register_translatable_strings' );

/**
 * Fetch a registered UI string in the current language, falling back to
 * the raw English default when Polylang (or the string) isn't available.
 *
 * @param string $default_text
 * @return string
 */
function jets_translate( $default_text ) {
	if ( function_exists( 'pll__' ) ) {
		return pll__( $default_text );
	}
	return $default_text;
}

/**
 * Returns a simple list of available languages for the header language
 * switcher template part, or an empty array pre-Polylang / single-language.
 *
 * @return array<int, array{slug: string, name: string, url: string, active: bool}>
 */
function jets_get_language_switcher_items() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}

	$raw = pll_the_languages( array(
		'raw'         => 1,
		'hide_if_empty' => 0,
	) );

	if ( empty( $raw ) ) {
		return array();
	}

	$items = array();
	foreach ( $raw as $lang ) {
		$items[] = array(
			'slug'   => $lang['slug'],
			'name'   => $lang['name'],
			'url'    => $lang['url'],
			'active' => ! empty( $lang['current_lang'] ),
		);
	}

	return $items;
}
