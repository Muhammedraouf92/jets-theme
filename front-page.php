<?php
/**
 * Homepage — one continuous narrative, assembled from the 10 sections in
 * PROJECT_CONTEXT.md §6. Phase 5 scaffold: real content from CPT/ACF in
 * the correct order and structure, minimal styling. Final visual +
 * animation passes land in Phases 6-9.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/homepage/section-hero' );
get_template_part( 'template-parts/homepage/section-philosophy' );
get_template_part( 'template-parts/homepage/section-about' );
get_template_part( 'template-parts/homepage/section-why' );
get_template_part( 'template-parts/homepage/section-services' );
get_template_part( 'template-parts/homepage/section-experiences' );
get_template_part( 'template-parts/homepage/section-delivery' );
get_template_part( 'template-parts/homepage/section-technology' );
get_template_part( 'template-parts/homepage/section-network' );
get_template_part( 'template-parts/homepage/section-cta' );

get_footer();
