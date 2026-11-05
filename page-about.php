<?php
/**
 * Template Name: About
 *
 * IA per PROJECT_CONTEXT.md §5: "Who we are, Mission, Vision, Why JETS."
 * Reuses the homepage's "Who We Are" and "Why JETS" template parts
 * verbatim — same ACF Homepage Content fields, same content source — so
 * this page doesn't duplicate a second copy of either. Only the
 * Mission/Vision panel between them is new (its own pair of fields on the
 * same options page, rendered on this page only).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$mission = jets_home_option( 'mission_text' );
$vision  = jets_home_option( 'vision_text' );
?>

<section class="section" aria-labelledby="page-about-heading">
	<div class="container container--narrow">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Our Company' ) ); ?></span>
			<h1 class="section-heading__title" id="page-about-heading"><?php echo esc_html( jets_translate( 'About JETS' ) ); ?></h1>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/homepage/section-about' ); ?>

<?php if ( $mission || $vision ) : ?>
	<section class="section section--alt" aria-labelledby="page-about-mission-heading">
		<div class="container">
			<div class="grid grid--2">
				<?php if ( $mission ) : ?>
					<div class="card reveal" data-reveal>
						<h2 class="card__title" id="page-about-mission-heading"><?php echo esc_html( jets_translate( 'Our Mission' ) ); ?></h2>
						<p class="card__desc"><?php echo esc_html( $mission ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( $vision ) : ?>
					<div class="card reveal" data-reveal style="--reveal-i: 1">
						<h2 class="card__title"><?php echo esc_html( jets_translate( 'Our Vision' ) ); ?></h2>
						<p class="card__desc"><?php echo esc_html( $vision ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/homepage/section-why' ); ?>

<?php
get_footer();
