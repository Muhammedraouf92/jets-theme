<?php
/**
 * Generic page fallback — used by any page.php-eligible content that
 * doesn't have its own dedicated template (page-about.php,
 * page-partners.php, page-contact.php). Currently that's Privacy Policy
 * and Terms of Use, but this covers any future plain content page too.
 *
 * Same light intro-panel pattern as the other page-*.php templates
 * (centered eyebrow + H1), then the post content through the shared
 * `.prose` typography (components.css) that restores readable
 * margins/list-styles for raw the_content() output.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<section class="section" aria-labelledby="page-heading">
		<div class="container container--narrow">
			<div class="section-heading section-heading--center reveal" data-reveal>
				<h1 class="section-heading__title" id="page-heading"><?php the_title(); ?></h1>
			</div>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container">
			<?php if ( get_the_content() ) : ?>
				<div class="prose reveal" data-reveal>
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php
endwhile;

get_footer();
