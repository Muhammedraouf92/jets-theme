<?php
/**
 * Template Name: Partners & Network
 *
 * Reuses the homepage's "Network & Who We Serve" section verbatim — its
 * content (Partnerships / Our Network / Clients & Partners, ACF Homepage
 * Content "Network" tab) already covers this page's full IA in the
 * "narrative treatment, no card-wall" style PROJECT_CONTEXT.md §5 calls
 * for, so nothing here is a new content source.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section" aria-labelledby="page-partners-heading">
	<div class="container container--narrow">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Partnerships & Network' ) ); ?></span>
			<h1 class="section-heading__title" id="page-partners-heading"><?php echo esc_html( jets_translate( 'Partners & Network' ) ); ?></h1>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/homepage/section-network' ); ?>

<?php
get_footer();
