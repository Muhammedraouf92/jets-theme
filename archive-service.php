<?php
/**
 * Services archive — every `service` post (simple services and deep-dive
 * solutions share one CPT/template, see PROJECT_CONTEXT.md §10) in a
 * responsive card grid, reusing the same card partial as the homepage
 * Services carousel (template-parts/components/card-service.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section" aria-labelledby="services-archive-heading">
	<div class="container">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'What We Offer' ) ); ?></span>
			<h1 class="section-heading__title" id="services-archive-heading"><?php echo esc_html( jets_translate( 'All Services' ) ); ?></h1>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3 services-grid">
				<?php
				$index = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<div class="reveal" data-reveal style="--reveal-i: <?php echo (int) ( $index % 3 ); ?>">
						<?php get_template_part( 'template-parts/components/card-service', null, array( 'service' => get_post(), 'index' => $index ) ); ?>
					</div>
					<?php
					$index++;
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination( array(
				'prev_text' => esc_html( jets_translate( 'Previous' ) ),
				'next_text' => esc_html( jets_translate( 'Next' ) ),
			) );
			?>
		<?php else : ?>
			<p><?php echo esc_html( jets_translate( 'No services found.' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
