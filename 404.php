<?php
/**
 * 404 template. Same light intro-panel pattern as page.php, but with a
 * friendly message and two CTAs instead of post content — a real 404 has
 * no post to loop over, so this is static markup, not a query loop.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section" aria-labelledby="error-404-heading">
	<div class="container container--narrow">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Error 404' ) ); ?></span>
			<h1 class="section-heading__title" id="error-404-heading"><?php echo esc_html( jets_translate( 'Page Not Found' ) ); ?></h1>
			<p class="section-heading__intro"><?php echo esc_html( jets_translate( "The page you're looking for doesn't exist or may have moved." ) ); ?></p>
		</div>

		<?php get_template_part( 'template-parts/components/search-form' ); ?>

		<div class="error-404__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo esc_html( jets_translate( 'Back to Homepage' ) ); ?>
			</a>
			<?php if ( post_type_exists( 'service' ) ) : ?>
				<a class="btn btn--text" href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>">
					<?php echo esc_html( jets_translate( 'View All Services' ) ); ?> <span class="icon">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
