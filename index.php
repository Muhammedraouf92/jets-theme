<?php
/**
 * Required fallback template. Real front-page storytelling lives in
 * front-page.php (Phase 5+); this file only serves as WordPress's
 * generic catch-all for anything not matched by a more specific template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container section">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'jets-theme' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
