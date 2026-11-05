<?php
/**
 * Small template-helper functions shared across templates/template-parts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True when the current request should render right-to-left (Arabic).
 * Wraps core is_rtl() so templates have one consistent call regardless
 * of whether Polylang or WP's own language setting is driving direction.
 *
 * @return bool
 */
function jets_is_rtl() {
	return (bool) is_rtl();
}

/**
 * Fetch a `service` post's structured meta in one call, so templates
 * don't scatter get_field() calls (and so the field keys in
 * inc/custom-fields.php stay the single source of truth).
 *
 * @param int|WP_Post $post
 * @return array{
 *   short_description: string,
 *   hero_image_id: int,
 *   icon: string,
 *   value_badges: array<int, string>,
 *   process_steps: array<int, array{title: string, description: string}>,
 *   is_featured: bool,
 *   homepage_order: int
 * }
 */
function jets_get_service_meta( $post ) {
	$post_id = is_object( $post ) ? $post->ID : (int) $post;

	// value_badges / process_steps are plain textareas (one item per line),
	// not ACF repeaters — repeater fields require ACF PRO, which this site
	// doesn't have. Parsed here so every caller still gets the same array
	// shape a repeater would have returned.
	$value_badges = array();
	if ( function_exists( 'get_field' ) ) {
		$badges_raw = (string) get_field( 'value_badges', $post_id );
		foreach ( preg_split( '/\r\n|\r|\n/', $badges_raw ) as $line ) {
			$line = trim( $line );
			if ( '' !== $line ) {
				$value_badges[] = $line;
			}
		}
	}

	$process_steps = array();
	if ( function_exists( 'get_field' ) ) {
		$steps_raw = (string) get_field( 'process_steps', $post_id );
		foreach ( preg_split( '/\r\n|\r|\n/', $steps_raw ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts = explode( '|', $line, 2 );
			$title = trim( $parts[0] );
			if ( '' === $title ) {
				continue;
			}
			$process_steps[] = array(
				'title'       => $title,
				'description' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
			);
		}
	}

	return array(
		'short_description' => function_exists( 'get_field' ) ? (string) get_field( 'short_description', $post_id ) : '',
		'hero_image_id'     => function_exists( 'get_field' ) ? (int) get_field( 'hero_image', $post_id ) : 0,
		'icon'               => function_exists( 'get_field' ) ? (string) get_field( 'icon', $post_id ) : '',
		'value_badges'       => $value_badges,
		'process_steps'      => $process_steps,
		'is_featured'        => function_exists( 'get_field' ) ? (bool) get_field( 'is_featured_on_homepage', $post_id ) : false,
		'homepage_order'     => function_exists( 'get_field' ) ? (int) get_field( 'homepage_order', $post_id ) : 0,
	);
}

/**
 * `service` posts flagged "Featured on Homepage", ordered by
 * homepage_order ascending. Returns an empty array (never a fabricated
 * fallback list) when nothing is flagged yet.
 *
 * @return WP_Post[]
 */
function jets_get_featured_services() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$query = new WP_Query( array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'meta_key'       => 'homepage_order',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'   => 'is_featured_on_homepage',
				'value' => '1',
			),
		),
	) );

	return $query->posts;
}

/**
 * Build a wa.me deep-link for the site's WhatsApp number, per
 * PROJECT_CONTEXT.md §4's "exactly two CTA mechanisms" rule. Digits-only
 * (strips spaces/dashes/parens/leading +) so `wa.me` accepts it. Returns
 * an empty string — never a placeholder link — when no number is set, so
 * callers can hide the button entirely rather than link to nothing.
 *
 * @param string $number Explicit number to use (e.g. a page-level ACF field); falls back to the global Theme Options number when empty.
 * @return string
 */
function jets_get_whatsapp_url( $number = '' ) {
	if ( '' === $number && function_exists( 'get_field' ) ) {
		$number = (string) get_field( 'whatsapp_number', 'option' );
	}

	$digits = preg_replace( '/[^0-9]/', '', (string) $number );

	if ( '' === $digits ) {
		return '';
	}

	return 'https://wa.me/' . $digits;
}

/**
 * Fetch a value from the "Homepage Content" ACF options page, degrading
 * to an empty string (never a fabricated fallback) when ACF or the field
 * isn't available.
 *
 * ACF (free) has no native Polylang integration for Options Pages, so
 * Arabic content lives in a second options page ('options_ar', see
 * inc/custom-fields.php) rather than the default 'option'. This picks
 * whichever one matches the current language, falling back to the
 * English options page if an Arabic value hasn't been filled in yet.
 *
 * @param string $field_name
 * @return mixed
 */
function jets_home_option( $field_name ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$option_id = 'option';
	if ( function_exists( 'pll_current_language' ) && 'ar' === pll_current_language() ) {
		$option_id = 'options_ar';
	}

	$value = get_field( $field_name, $option_id );

	if ( '' === $value || null === $value || array() === $value ) {
		if ( 'option' !== $option_id ) {
			$value = get_field( $field_name, 'option' );
		}
	}

	return null === $value ? '' : $value;
}

/**
 * Inline SVG icon for a "Why JETS" pillar. Tries an exact match against
 * the pillar's Icon Slug field (or its slugified label) first; content
 * entered in Arabic, reworded, or spelled differently than the jets.pdf
 * reference text won't match, so falls back to cycling through the icon
 * set by the pillar's position in the repeater — every card still gets a
 * distinct icon even when the text lookup misses.
 *
 * @param string $slug_or_label Icon Slug field value, or the pillar label as a fallback key.
 * @param int    $index         Pillar's position in the repeater (0-based), used when the text lookup misses.
 * @return string Raw <svg> markup (trusted, theme-authored — do not esc_html()).
 */
function jets_get_pillar_icon( $slug_or_label, $index = 0 ) {
	static $icons = null;

	if ( null === $icons ) {
		$icons = array(
			'specialized-expertise' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8.5" r="5"/><path d="M8.7 12.9 7 21l5-2.5 5 2.5-1.7-8.1"/></svg>',
			'integrated-solutions'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="12" r="6"/><circle cx="15" cy="12" r="6"/></svg>',
			'flexibility'           => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><circle cx="9" cy="6" r="2" fill="currentColor" stroke="none"/><line x1="4" y1="12" x2="20" y2="12"/><circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"/><line x1="4" y1="18" x2="20" y2="18"/><circle cx="7" cy="18" r="2" fill="currentColor" stroke="none"/></svg>',
			'strong-network'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="17" r="2.5"/><circle cx="19" cy="17" r="2.5"/><path d="M12 7.5 5 14.7M12 7.5l7 7.2M7.3 17h9.4"/></svg>',
			'advanced-technology'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="1.5"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5.6 5.6l1.8 1.8M16.6 16.6l1.8 1.8M18.4 5.6l-1.8 1.8M7.4 16.6l-1.8 1.8"/></svg>',
			'attention-to-detail'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.4 15.4 21 21"/><path d="M8 10.5l1.75 1.75L13.5 8"/></svg>',
		);
	}

	$key = sanitize_title( $slug_or_label );

	if ( isset( $icons[ $key ] ) ) {
		return $icons[ $key ];
	}

	$ordered = array_values( $icons );

	return $ordered[ $index % count( $ordered ) ];
}

/**
 * Inline SVG icon for a `service` post, keyed by its Icon Slug field (or
 * a slugified title when that's empty) against the 13 services seeded
 * from jets.pdf page 6. Same match-by-text-then-fall-back-to-position
 * strategy as jets_get_pillar_icon() — guarantees visually distinct
 * icons across a carousel even when the text lookup misses.
 *
 * @param string $slug_or_title Icon Slug field value, or the post title as a fallback key.
 * @param int    $index         Post's position in the current listing (0-based), used when the text lookup misses.
 * @return string Raw <svg> markup (trusted, theme-authored — do not esc_html()).
 */
function jets_get_service_icon( $slug_or_title, $index = 0 ) {
	static $icons = null;

	if ( null === $icons ) {
		$plane    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3 11 13"/><path d="M21 3 14.5 21 11 13 3 9.5 21 3Z"/></svg>';
		$giftbox  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="9" width="16" height="11" rx="1.5"/><path d="M4 13.5h16"/><path d="M12 9v11"/><path d="M8.5 9c-1.66 0-3-1.12-3-2.5S6.84 4 8.5 4C10.5 4 12 6.2 12 9c0-2.8 1.5-5 3.5-5 1.66 0 3 1.12 3 2.5S17.16 9 15.5 9"/></svg>';
		$car      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16V11l2-5h12l2 5v5"/><path d="M4 16h16"/><circle cx="7.5" cy="16.5" r="1.5"/><circle cx="16.5" cy="16.5" r="1.5"/></svg>';
		$luggage  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M9 8V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V8"/><path d="M4 13h16"/></svg>';
		$star     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5l2.5 5.4 5.9.6-4.4 4 1.2 5.9L12 16.7l-5.2 2.7 1.2-5.9-4.4-4 5.9-.6L12 3.5Z"/></svg>';
		$passport = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><circle cx="12" cy="9.5" r="2.25"/><path d="M8.5 17c.7-2 2-3 3.5-3s2.8 1 3.5 3"/></svg>';
		$compass  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M15.5 8.5 13.6 13.6 8.5 15.5l1.9-5.1 5.1-1.9Z"/></svg>';
		$brief    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7.5" width="18" height="12" rx="2"/><path d="M8 7.5V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1.5"/><path d="M3 13h18"/></svg>';
		$people   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><circle cx="17" cy="8.5" r="2.4"/><path d="M15.5 14.2c2.5.3 4.3 2.1 4.5 4.8"/></svg>';
		$calendar = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M8 3v4M16 3v4"/><path d="M8.5 14h2M13.5 14h2M8.5 17h2"/></svg>';
		$bed      = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v6"/><path d="M3 18v2M21 18v2"/><path d="M3 14h18"/><circle cx="7.5" cy="11.5" r="1.25"/></svg>';
		$globe    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M4 12h16M12 4c2.2 2.2 3.3 5 3.3 8s-1.1 5.8-3.3 8c-2.2-2.2-3.3-5-3.3-8S9.8 6.2 12 4Z"/></svg>';
		$pencil   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l1-4.2L15.8 5 19 8.2 8.2 19 4 20Z"/><path d="M13.2 6.6 17.4 10.8"/></svg>';

		$icons = array(
			'flight-bookings'              => $plane,
			'flights'                      => $plane,
			'travel-packages-programs'     => $giftbox,
			'travel-packages'              => $giftbox,
			'ground-transportation'        => $car,
			'transportation-services'      => $car,
			'transportation'               => $car,
			'airport-services'             => $luggage,
			'airport'                      => $luggage,
			'vip-services'                 => $star,
			'vip'                          => $star,
			'visa-assistance'              => $passport,
			'visa-services'                => $passport,
			'visa'                         => $passport,
			'tours-excursions'             => $compass,
			'tours'                        => $compass,
			'corporate-travel'             => $brief,
			'corporate'                    => $brief,
			'group-travel-events'          => $people,
			'group-travel'                 => $people,
			'events-travel-management'     => $calendar,
			'events'                       => $calendar,
			'hotel-accommodation'          => $bed,
			'hotel'                        => $bed,
			'digital-travel-solutions'     => $globe,
			'digital'                      => $globe,
			'tailor-made-travel-solutions' => $pencil,
			'tailor-made'                  => $pencil,
		);
	}

	$key = sanitize_title( $slug_or_title );

	if ( isset( $icons[ $key ] ) ) {
		return $icons[ $key ];
	}

	$ordered = array_values( array_unique( $icons ) );

	return $ordered[ $index % count( $ordered ) ];
}

/**
 * A `service` post looked up by slug, for the homepage sections that
 * reuse a single deep-dive solution's data instead of duplicating it in
 * the Homepage Content options (Experience categories ← "Tours &
 * Excursions", Technology ← "Digital Travel Solutions"). Matching by
 * slug (not title) avoids a silent mismatch against titles WordPress has
 * HTML-entity-encoded in the database (e.g. "Tours &amp; Excursions").
 *
 * @param string $slug
 * @return WP_Post|null
 */
function jets_get_service_by_slug( $slug ) {
	$posts = get_posts( array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'name'           => $slug,
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	) );

	return $posts ? $posts[0] : null;
}

/**
 * `service` posts that have at least one process step, ordered by
 * homepage_order — powers the homepage "How We Deliver" spotlight
 * (§7). Purely data-driven (whichever services have steps populated)
 * rather than a hardcoded list of titles.
 *
 * @return WP_Post[]
 */
function jets_get_services_with_process_steps() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$featured = jets_get_featured_services();
	$with_steps = array();

	foreach ( $featured as $post ) {
		$steps = get_field( 'process_steps', $post->ID );
		if ( ! empty( $steps ) ) {
			$with_steps[] = $post;
		}
	}

	return $with_steps;
}

/**
 * URL of the first published page assigned a given page template, or a
 * fallback when no such page exists yet (e.g. page-contact.php before
 * Phase 9 creates the real Contact page) — avoids hardcoding/broken links
 * to pages that don't exist yet.
 *
 * @param string $template
 * @param string $fallback
 * @return string
 */
function jets_get_template_page_url( $template, $fallback = '#' ) {
	$pages = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
	) );

	return $pages ? get_permalink( $pages[0] ) : $fallback;
}

/**
 * Central registry of development-only placeholder image URLs (see
 * PROJECT_CONTEXT.md §12 Asset Strategy). Every external/dev image
 * reference in the theme goes through this one function so swapping a
 * placeholder for real Media Library content later means editing one
 * array, not hunting through templates.
 *
 * Populated incrementally as each homepage section is built
 * (Phases 6–9); unset keys fall back to the local placeholder.jpg.
 *
 * @param string $key
 * @return string
 */
function jets_dev_image( $key ) {
	static $registry = null;

	if ( null === $registry ) {
		$registry = array(
			'hero'         => JETS_THEME_URI . '/assets/images/placeholder.jpg',
			// About section (§3, Phase 9 performance fix): a curated,
			// optimized cabin-interior photo — replaces the old shared
			// 'hero' placeholder.jpg fallback there, which at 6MB/5979x3986
			// was the direct cause of the About section's slow image load.
			'about'        => JETS_THEME_URI . '/assets/images/about_1_11zon.webp',
			// Hero scrollytelling (Phase 6, "Flight Path" art direction,
			// §6.1): stage 1 is a wing-at-sunset shot (establishes the
			// world, ascending), stage 3 cross-fades to a runway at dusk
			// (the "path opening forward" — the literal visual metaphor for
			// "Opening the World to You"). Both curated from Unsplash and
			// given a matching cool-shadow grade so they read as one visual
			// world rather than two unrelated stock photos.
			'hero-arrival' => JETS_THEME_URI . '/assets/images/hero-arrival_14_11zon.webp',
			'hero-runway'  => JETS_THEME_URI . '/assets/images/hero-runway_15_11zon.webp',
			// How We Deliver (§7): one curated Unsplash photo per flow,
			// all golden-hour/silhouette shots so the five read as one set
			// despite being unrelated source photos — the same "color-matched
			// curated photography" approach as the hero, chosen over generic
			// stock so the section doesn't feel like clip art once real
			// service photography replaces these.
			'delivery-corporate-travel'     => JETS_THEME_URI . '/assets/images/delivery-corporate-travel_4_11zon.webp',
			'delivery-group-travel-events'  => JETS_THEME_URI . '/assets/images/delivery-group-travel-events_5_11zon.webp',
			'delivery-transportation'       => JETS_THEME_URI . '/assets/images/delivery-transportation_6_11zon.webp',
			'delivery-airport-services'     => JETS_THEME_URI . '/assets/images/delivery-airport-services_3_11zon.webp',
			'delivery-visa-services'        => JETS_THEME_URI . '/assets/images/delivery-visa-services_7_11zon.webp',
			// Where We Can Take You (§6, Phase 8): one curated silhouette/
			// dusk shot per experience category, same "read as one set"
			// curation approach as the delivery photos above.
			'experience-culture'            => JETS_THEME_URI . '/assets/images/experience-culture_10_11zon.webp',
			'experience-adventure'          => JETS_THEME_URI . '/assets/images/experience-adventure_8_11zon.webp',
			'experience-cruises'            => JETS_THEME_URI . '/assets/images/experience-cruises_9_11zon.webp',
			'experience-luxury'             => JETS_THEME_URI . '/assets/images/experience-luxury_12_11zon.webp',
			'experience-leisure'            => JETS_THEME_URI . '/assets/images/experience-leisure_11_11zon.webp',
			// Contact page hero (Phase 9 design pass): an empty departure
			// lounge at sunset — same curated-Unsplash, cool/moody-graded
			// approach as the hero/delivery/experience photos, chosen over a
			// literal "person on the phone" shot to stay consistent with
			// every other curated image in the theme being people-free
			// architectural/silhouette photography.
			'contact-hero'                  => JETS_THEME_URI . '/assets/images/contact-hero_2_11zon.webp',
		);
	}

	return $registry[ $key ] ?? $registry['hero'];
}

/**
 * Curated dev image for a delivery flow, keyed by its slugified title
 * against the 5 known flows (Corporate Travel, Group Travel & Events,
 * Transportation, Airport Services, Visa Services). Same match-by-text-
 * then-fall-back-to-position strategy as jets_get_pillar_icon()/
 * jets_get_service_icon() — real content wording may differ from the
 * PROJECT_CONTEXT.md reference titles, so the fallback guarantees five
 * *different* photos rather than risking the same one repeating.
 *
 * @param string $title Post title.
 * @param int    $index Post's position in the current listing (0-based), used when the text lookup misses.
 * @return string Image URL (jets_dev_image() curated photo, or the real hero_image once uploaded — callers check hero_image_id first).
 */
function jets_get_delivery_image( $title, $index = 0 ) {
	$keys = array(
		'corporate-travel'             => 'delivery-corporate-travel',
		'group-travel-events'          => 'delivery-group-travel-events',
		'group-travel'                 => 'delivery-group-travel-events',
		'ground-transportation'        => 'delivery-transportation',
		'transportation-services'      => 'delivery-transportation',
		'transportation'               => 'delivery-transportation',
		'airport-services'             => 'delivery-airport-services',
		'airport'                      => 'delivery-airport-services',
		'visa-assistance'              => 'delivery-visa-services',
		'visa-services'                => 'delivery-visa-services',
		'visa'                         => 'delivery-visa-services',
	);

	$slug = sanitize_title( $title );

	if ( isset( $keys[ $slug ] ) ) {
		return jets_dev_image( $keys[ $slug ] );
	}

	$ordered = array_values( array_unique( $keys ) );

	return jets_dev_image( $ordered[ $index % count( $ordered ) ] );
}

/**
 * Curated dev image for a "Where We Can Take You" experience category
 * (Culture, Adventure, Cruises, Luxury, Leisure — Tours & Excursions'
 * value_badges, per jets.pdf page 12). Same match-by-text-then-fall-back-
 * to-position strategy as jets_get_delivery_image().
 *
 * @param string $label Category label (repeater/textarea line text).
 * @param int    $index Category's position in the list, used when the text lookup misses.
 * @return string Image URL (jets_dev_image() curated photo).
 */
function jets_get_experience_image( $label, $index = 0 ) {
	$keys = array(
		'culture'   => 'experience-culture',
		'adventure' => 'experience-adventure',
		'cruises'   => 'experience-cruises',
		'luxury'    => 'experience-luxury',
		'leisure'   => 'experience-leisure',
	);

	$slug = sanitize_title( $label );

	if ( isset( $keys[ $slug ] ) ) {
		return jets_dev_image( $keys[ $slug ] );
	}

	$ordered = array_values( $keys );

	return jets_dev_image( $ordered[ $index % count( $ordered ) ] );
}
