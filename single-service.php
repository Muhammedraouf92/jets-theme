<?php
/**
 * Single service — one template for both the 11 simple integrated
 * services and the deep-dive "solution" pages (Corporate Travel, Group
 * Travel & Events, etc.), per PROJECT_CONTEXT.md §10. Gracefully omits
 * the process-steps block when a service has none — 5 of the 13 seeded
 * services are title-only (no PDF paragraph to source steps/badges from).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$service      = get_post();
	$meta         = jets_get_service_meta( $service );
	$contact_url  = jets_get_template_page_url( 'page-contact.php', home_url( '/' ) );
	?>

	<section class="panel--full-bleed section--dark" aria-labelledby="single-service-heading">
		<div class="panel__media" aria-hidden="true">
			<?php if ( $meta['hero_image_id'] ) : ?>
				<?php echo wp_get_attachment_image( $meta['hero_image_id'], 'jets-wide', false, array( 'loading' => 'eager', 'alt' => '' ) ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( jets_dev_image( 'hero' ) ); ?>" alt="" loading="eager">
			<?php endif; ?>
		</div>
		<div class="panel__scrim" aria-hidden="true"></div>

		<div class="panel__content container container--narrow">
			<span class="card__icon single-service__icon" aria-hidden="true">
				<?php echo jets_get_service_icon( ! empty( $meta['icon'] ) ? $meta['icon'] : get_the_title(), 0 ); ?>
			</span>
			<h1 class="section-heading__title" id="single-service-heading"><?php the_title(); ?></h1>
			<?php if ( $meta['short_description'] ) : ?>
				<p class="section-heading__intro"><?php echo esc_html( $meta['short_description'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $meta['value_badges'] ) ) : ?>
				<div class="badge-row">
					<?php foreach ( $meta['value_badges'] as $badge ) : ?>
						<span class="badge"><?php echo esc_html( $badge ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section">
		<div class="container container--narrow">
			<?php if ( get_the_content() ) : ?>
				<div class="prose reveal" data-reveal>
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $meta['process_steps'] ) ) : ?>
				<div class="single-service__process reveal" data-reveal style="--reveal-i: 1">
					<span class="delivery-flow__meta">
						<?php echo esc_html( sprintf( jets_translate( '%d-step process' ), count( $meta['process_steps'] ) ) ); ?>
					</span>
					<ul class="delivery-chain">
						<?php foreach ( $meta['process_steps'] as $step ) : ?>
							<li<?php echo $step['description'] ? ' title="' . esc_attr( $step['description'] ) . '"' : ''; ?>><?php echo esc_html( $step['title'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<div class="single-service__cta reveal" data-reveal style="--reveal-i: 2">
				<a class="btn btn--primary" href="<?php echo esc_url( $contact_url ); ?>">
					<?php echo esc_html( jets_translate( 'Contact Us' ) ); ?>
				</a>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
