<?php
/**
 * Custom Post Types.
 *
 * `service` powers both the 11 simple integrated services and the
 * deep-dive "solution" pages (Corporate Travel, Group Travel & Events,
 * Transportation, Airport Services, Hotel Accommodation, Tours &
 * Excursions, Visa Services, Digital Travel Solutions) — see
 * PROJECT_CONTEXT.md §10 for why these share one CPT.
 *
 * `testimonial` is registered now but not queried on the front end
 * until real content exists (PROJECT_CONTEXT.md §13 — no fabricated
 * testimonials).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jets_register_post_types() {

	register_post_type( 'service', array(
		'labels' => array(
			'name'               => __( 'Services', 'jets-theme' ),
			'singular_name'      => __( 'Service', 'jets-theme' ),
			'add_new_item'       => __( 'Add New Service', 'jets-theme' ),
			'edit_item'          => __( 'Edit Service', 'jets-theme' ),
			'all_items'          => __( 'All Services', 'jets-theme' ),
			'search_items'       => __( 'Search Services', 'jets-theme' ),
			'not_found'          => __( 'No services found', 'jets-theme' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-airplane',
		'rewrite'       => array( 'slug' => 'services', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'menu_position' => 5,
	) );

	register_post_type( 'testimonial', array(
		'labels' => array(
			'name'          => __( 'Testimonials', 'jets-theme' ),
			'singular_name' => __( 'Testimonial', 'jets-theme' ),
			'add_new_item'  => __( 'Add New Testimonial', 'jets-theme' ),
			'edit_item'     => __( 'Edit Testimonial', 'jets-theme' ),
			'all_items'     => __( 'All Testimonials', 'jets-theme' ),
		),
		// Public so it can be previewed/edited normally, but never queried
		// on the front end (see jets_get_published_testimonials()) until
		// real client testimonials exist.
		'public'       => true,
		'has_archive'  => false,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-format-quote',
		'rewrite'      => false,
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	) );
}
add_action( 'init', 'jets_register_post_types' );

/**
 * Services archive shows every service on one page rather than paginating
 * at WordPress's site-wide "Blog pages show at most" setting (Settings >
 * Reading — Phase 8 shipped with the default of 10, splitting the 13
 * services into a page 2 for just 3 of them). Scoped to the `service`
 * archive's main query only, so it doesn't affect a future blog or any
 * other archive that setting also controls.
 *
 * @param WP_Query $query
 */
function jets_service_archive_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'service' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
}
add_action( 'pre_get_posts', 'jets_service_archive_query' );

/**
 * Returns published testimonials, or an empty array if none exist yet.
 * Front-end templates should treat an empty return as "hide this section
 * entirely" rather than rendering a placeholder — see content rule in
 * PROJECT_CONTEXT.md §13.
 *
 * @return WP_Post[]
 */
function jets_get_published_testimonials() {
	$query = new WP_Query( array(
		'post_type'      => 'testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );

	return $query->posts;
}
