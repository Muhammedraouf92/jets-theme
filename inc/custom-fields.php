<?php
/**
 * ACF field group registration (local PHP, not the ACF UI — keeps field
 * definitions version-controlled with the theme).
 *
 * Note vs. PROJECT_CONTEXT.md §10: `full_description` is NOT a separate
 * ACF field — the `service` CPT already supports the native content
 * editor (`the_content()`), so the long-form body lives there instead of
 * duplicating a WYSIWYG field for no benefit.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'jets_register_acf_field_groups' );

function jets_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	jets_register_service_fields();
	jets_register_contact_fields();
	jets_register_options_fields();
	jets_register_homepage_content_fields();
}

/**
 * Fields on the `service` CPT.
 */
function jets_register_service_fields() {
	acf_add_local_field_group( array(
		'key'    => 'group_service_details',
		'title'  => __( 'Service Details', 'jets-theme' ),
		'fields' => array(
			array(
				'key'          => 'field_service_short_description',
				'label'        => __( 'Short Description', 'jets-theme' ),
				'name'         => 'short_description',
				'type'         => 'textarea',
				'instructions' => __( 'One or two sentences used in grid/teaser cards (Services showcase, archive).', 'jets-theme' ),
				'rows'         => 3,
				'required'     => 1,
			),
			array(
				'key'   => 'field_service_hero_image',
				'label' => __( 'Hero / Feature Image', 'jets-theme' ),
				'name'  => 'hero_image',
				'type'  => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
			),
			array(
				'key'          => 'field_service_icon',
				'label'        => __( 'Icon Slug', 'jets-theme' ),
				'name'         => 'icon',
				'type'         => 'text',
				'instructions' => __( 'Key matching an inline SVG icon registered in template-parts/components (e.g. "hotel", "flight", "visa").', 'jets-theme' ),
			),
			array(
				'key'          => 'field_service_value_badges',
				'label'        => __( 'Value Badges', 'jets-theme' ),
				'name'         => 'value_badges',
				'type'         => 'textarea',
				'instructions' => __( 'One badge per line, e.g. Speed / Precision / Flexibility / Reliable Support.', 'jets-theme' ),
				'rows'         => 3,
			),
			array(
				'key'          => 'field_service_process_steps',
				'label'        => __( 'Process Steps', 'jets-theme' ),
				'name'         => 'process_steps',
				'type'         => 'textarea',
				'instructions' => __( 'Optional. One step per line, as "Step Title" or "Step Title | Step Description". Leave empty for simple services; populate for deep-dive solutions (Corporate Travel, Group Travel & Events, Transportation, Airport Services, Visa Services) to render a process list.', 'jets-theme' ),
				'rows'         => 6,
			),
			array(
				'key'          => 'field_service_is_featured',
				'label'        => __( 'Featured on Homepage', 'jets-theme' ),
				'name'         => 'is_featured_on_homepage',
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => __( 'Controls inclusion in the homepage Services showcase and Delivery-spotlight sections.', 'jets-theme' ),
			),
			array(
				'key'               => 'field_service_homepage_order',
				'label'             => __( 'Homepage Order', 'jets-theme' ),
				'name'              => 'homepage_order',
				'type'              => 'number',
				'instructions'      => __( 'Lower numbers appear first. Only relevant when "Featured on Homepage" is enabled.', 'jets-theme' ),
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_service_is_featured',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'service',
				),
			),
		),
	) );
}

/**
 * Fields for the Contact page template. Each is individually optional —
 * templates must conditionally hide unset values rather than printing
 * empty labels (PROJECT_CONTEXT.md §13).
 */
function jets_register_contact_fields() {
	acf_add_local_field_group( array(
		'key'    => 'group_contact_details',
		'title'  => __( 'Contact Details', 'jets-theme' ),
		'fields' => array(
			array(
				'key'   => 'field_contact_phone',
				'label' => __( 'Phone Number', 'jets-theme' ),
				'name'  => 'contact_phone',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_contact_whatsapp',
				'label' => __( 'WhatsApp Number', 'jets-theme' ),
				'name'  => 'contact_whatsapp',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_contact_email',
				'label' => __( 'Email Address', 'jets-theme' ),
				'name'  => 'contact_email',
				'type'  => 'email',
			),
			array(
				'key'   => 'field_contact_address',
				'label' => __( 'Address', 'jets-theme' ),
				'name'  => 'contact_address',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_contact_hours',
				'label' => __( 'Business Hours', 'jets-theme' ),
				'name'  => 'contact_hours',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_contact_map_embed',
				'label'        => __( 'Map Embed URL', 'jets-theme' ),
				'name'         => 'contact_map_embed',
				'type'         => 'url',
				'instructions' => __( 'Google Maps &rarr; Share &rarr; Embed a map &rarr; copy the src="..." URL from the iframe code.', 'jets-theme' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-contact.php',
				),
			),
		),
	) );
}

/**
 * Site-wide Theme Options: default social links, footer legal text,
 * default OG/share image. A single options screen instead of scattering
 * settings across multiple admin pages.
 */
function jets_register_options_fields() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' => __( 'Theme Options', 'jets-theme' ),
			'menu_title' => __( 'Theme Options', 'jets-theme' ),
			'menu_slug'  => 'jets-theme-options',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-generic',
		) );
	}

	acf_add_local_field_group( array(
		'key'    => 'group_theme_options',
		'title'  => __( 'Theme Options', 'jets-theme' ),
		'fields' => array(
			array(
				'key'   => 'field_options_social_links',
				'label' => __( 'Social Links', 'jets-theme' ),
				'name'  => 'social_links',
				'type'  => 'repeater',
				'layout' => 'table',
				'button_label' => __( 'Add Link', 'jets-theme' ),
				'sub_fields' => array(
					array(
						'key'     => 'field_options_social_platform',
						'label'   => __( 'Platform', 'jets-theme' ),
						'name'    => 'platform',
						'type'    => 'text',
					),
					array(
						'key'   => 'field_options_social_url',
						'label' => __( 'URL', 'jets-theme' ),
						'name'  => 'url',
						'type'  => 'url',
					),
				),
			),
			array(
				'key'   => 'field_options_footer_legal',
				'label' => __( 'Footer Legal Text', 'jets-theme' ),
				'name'  => 'footer_legal_text',
				'type'  => 'text',
				'instructions' => __( 'E.g. tourism license number, once available.', 'jets-theme' ),
			),
			array(
				'key'   => 'field_options_default_share_image',
				'label' => __( 'Default Share (OG) Image', 'jets-theme' ),
				'name'  => 'default_share_image',
				'type'  => 'image',
				'return_format' => 'id',
			),
			array(
				'key'          => 'field_options_whatsapp_number',
				'label'        => __( 'WhatsApp Number', 'jets-theme' ),
				'name'         => 'whatsapp_number',
				'type'         => 'text',
				'instructions' => __( 'Include country code, e.g. +971501234567. Powers the site-wide WhatsApp button (header, footer, Contact page) via a wa.me link — the button renders nothing while this is empty rather than linking to a placeholder number.', 'jets-theme' ),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'jets-theme-options',
				),
			),
		),
	) );
}

/**
 * Homepage static copy that isn't tied to the `service` CPT (Philosophy,
 * About, Why JETS pillars, Network/Partnerships/Clients, Final CTA).
 * A separate options page from Theme Options — this is real PDF-sourced
 * company copy, not a settings screen, and keeping it separate makes the
 * "content" vs. "settings" distinction clear in wp-admin.
 *
 * Sections that already have a natural CPT home are NOT duplicated here:
 * the Experience-categories section (homepage §6) reads the "Tours &
 * Excursions" service's value_badges/short_description, and the
 * Technology section (§8) reads "Digital Travel Solutions" the same way
 * — see jets_get_service_by_title() in inc/helpers.php.
 */
function jets_register_homepage_content_fields() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array(
			'page_title' => __( 'Homepage Content', 'jets-theme' ),
			'menu_title' => __( 'Homepage Content', 'jets-theme' ),
			'menu_slug'  => 'jets-homepage-content',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-home',
		) );

		/*
		 * ACF (free) has no built-in Polylang integration for Options Pages —
		 * Polylang only duplicates actual posts/pages per language, not a
		 * single shared 'options' row. Rather than bolt on a third-party
		 * sync layer, this registers a second, separate options page
		 * ('options_ar', a submenu of the English one) that shares the same
		 * field group via the location rule below. jets_home_option() in
		 * inc/helpers.php picks whichever post_id matches the current
		 * Polylang language when reading a field. See PROJECT_CONTEXT.md
		 * §14 (deferred to Phase 10, decided here alongside real Arabic
		 * content).
		 */
		acf_add_options_page( array(
			'page_title'  => __( 'Homepage Content (Arabic)', 'jets-theme' ),
			'menu_title'  => __( 'Homepage Content (AR)', 'jets-theme' ),
			'menu_slug'   => 'jets-homepage-content-ar',
			'parent_slug' => 'jets-homepage-content',
			'post_id'     => 'options_ar',
			'capability'  => 'manage_options',
		) );
	}

	acf_add_local_field_group( array(
		'key'    => 'group_homepage_content',
		'title'  => __( 'Homepage Content', 'jets-theme' ),
		'fields' => array(
			// ---- Hero ----
			array(
				'key'   => 'field_home_tab_hero',
				'label' => __( 'Hero', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_home_hero_eyebrow',
				'label' => __( 'Eyebrow', 'jets-theme' ),
				'name'  => 'hero_eyebrow',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_hero_heading',
				'label' => __( 'Heading', 'jets-theme' ),
				'name'  => 'hero_heading',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_hero_subheading',
				'label' => __( 'Sub-heading', 'jets-theme' ),
				'name'  => 'hero_subheading',
				'type'  => 'text',
			),

			// ---- Philosophy ----
			array(
				'key'   => 'field_home_tab_philosophy',
				'label' => __( 'Philosophy', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_home_philosophy_eyebrow',
				'label' => __( 'Eyebrow', 'jets-theme' ),
				'name'  => 'philosophy_eyebrow',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_philosophy_statement',
				'label' => __( 'Statement', 'jets-theme' ),
				'name'  => 'philosophy_statement',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_philosophy_text',
				'label' => __( 'Supporting text', 'jets-theme' ),
				'name'  => 'philosophy_text',
				'type'  => 'textarea',
			),

			// ---- About ----
			array(
				'key'   => 'field_home_tab_about',
				'label' => __( 'About', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_home_about_description',
				'label' => __( 'Description', 'jets-theme' ),
				'name'  => 'about_description',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'field_home_about_brand_statement',
				'label' => __( 'Brand statement', 'jets-theme' ),
				'name'  => 'about_brand_statement',
				'type'  => 'textarea',
			),
			array(
				'key'          => 'field_home_mission_text',
				'label'        => __( 'Mission', 'jets-theme' ),
				'name'         => 'mission_text',
				'type'         => 'textarea',
				'instructions' => __( 'Rendered on the About page only (not part of the homepage narrative).', 'jets-theme' ),
			),
			array(
				'key'          => 'field_home_vision_text',
				'label'        => __( 'Vision', 'jets-theme' ),
				'name'         => 'vision_text',
				'type'         => 'textarea',
				'instructions' => __( 'Rendered on the About page only (not part of the homepage narrative).', 'jets-theme' ),
			),

			// ---- Why JETS ----
			array(
				'key'   => 'field_home_tab_why',
				'label' => __( 'Why JETS', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_home_why_pillars',
				'label'        => __( 'Pillars', 'jets-theme' ),
				'name'         => 'why_jets_pillars',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => __( 'Add Pillar', 'jets-theme' ),
				'sub_fields'   => array(
					array(
						'key'          => 'field_home_why_pillar_icon',
						'label'        => __( 'Icon Slug', 'jets-theme' ),
						'name'         => 'icon',
						'type'         => 'text',
						'instructions' => __( 'Matches an inline SVG icon registered in template-parts/components.', 'jets-theme' ),
					),
					array(
						'key'   => 'field_home_why_pillar_label',
						'label' => __( 'Label', 'jets-theme' ),
						'name'  => 'label',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_home_why_pillar_description',
						'label' => __( 'Description', 'jets-theme' ),
						'name'  => 'description',
						'type'  => 'textarea',
						'rows'  => 2,
					),
				),
			),

			// ---- Network / Partnerships / Clients ----
			array(
				'key'   => 'field_home_tab_network',
				'label' => __( 'Network', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_home_partnerships_text',
				'label' => __( 'Partnerships — text', 'jets-theme' ),
				'name'  => 'partnerships_text',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'field_home_partnerships_tagline',
				'label' => __( 'Partnerships — tagline', 'jets-theme' ),
				'name'  => 'partnerships_tagline',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_network_text',
				'label' => __( 'Our Network — text', 'jets-theme' ),
				'name'  => 'network_text',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'field_home_network_tagline',
				'label' => __( 'Our Network — tagline', 'jets-theme' ),
				'name'  => 'network_tagline',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_home_network_categories',
				'label'        => __( 'Our Network — categories', 'jets-theme' ),
				'name'         => 'network_categories',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => __( 'Add Category', 'jets-theme' ),
				'sub_fields'   => array(
					array(
						'key'   => 'field_home_network_category_label',
						'label' => __( 'Label', 'jets-theme' ),
						'name'  => 'label',
						'type'  => 'text',
					),
				),
			),
			array(
				'key'   => 'field_home_clients_intro',
				'label' => __( 'Clients & Partners — intro', 'jets-theme' ),
				'name'  => 'clients_intro',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_clients_tagline',
				'label' => __( 'Clients & Partners — tagline', 'jets-theme' ),
				'name'  => 'clients_tagline',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_home_client_types',
				'label'        => __( 'Clients & Partners — types', 'jets-theme' ),
				'name'         => 'client_types',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => __( 'Add Type', 'jets-theme' ),
				'sub_fields'   => array(
					array(
						'key'   => 'field_home_client_type_label',
						'label' => __( 'Label', 'jets-theme' ),
						'name'  => 'label',
						'type'  => 'text',
					),
				),
			),

			// ---- Final CTA ----
			array(
				'key'   => 'field_home_tab_cta',
				'label' => __( 'Final CTA', 'jets-theme' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_home_cta_headline',
				'label' => __( 'Headline', 'jets-theme' ),
				'name'  => 'cta_headline',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_home_cta_text',
				'label' => __( 'Supporting text', 'jets-theme' ),
				'name'  => 'cta_text',
				'type'  => 'textarea',
			),
			array(
				'key'   => 'field_home_cta_closing_line',
				'label' => __( 'Closing line', 'jets-theme' ),
				'name'  => 'cta_closing_line',
				'type'  => 'text',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'jets-homepage-content',
				),
			),
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'jets-homepage-content-ar',
				),
			),
		),
	) );
}
