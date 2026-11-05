<?php
/**
 * §5 What We Offer (Services). Content: PROJECT_CONTEXT.md §6.5, pulled
 * live from the `service` CPT (jets_get_featured_services(), ordered by
 * homepage_order) — nothing hardcoded. Swiper-powered carousel (see
 * inc/enqueue.php + assets/js/services-carousel.js); markup falls back to
 * a plain scroll-snap row (components.css) if JS is disabled.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = jets_get_featured_services();

if ( empty( $services ) ) {
	return;
}
?>
<section class="section" id="services" aria-labelledby="services-heading">
	<div class="container">
		<div class="section-heading reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'What We Offer' ) ); ?></span>
			<h2 class="section-heading__title" id="services-heading"><?php echo esc_html( jets_translate( 'One partner. Every part of the journey.' ) ); ?></h2>
			<p class="section-heading__intro"><?php echo esc_html( jets_translate( 'JETS provides an integrated range of travel and tourism solutions covering the complete travel journey — from planning and booking to execution and client support — with flexible services designed for individuals, businesses, and travel partners.' ) ); ?></p>
		</div>

		<div class="carousel reveal" data-reveal style="--reveal-i: 1" data-services-carousel>
			<div class="swiper services-carousel__swiper" data-services-carousel-track>
				<div class="swiper-wrapper">
					<?php foreach ( $services as $index => $service ) : ?>
						<div class="swiper-slide">
							<?php get_template_part( 'template-parts/components/card-service', null, array( 'service' => $service, 'index' => $index ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="carousel__controls">
				<button type="button" class="carousel__arrow" data-services-carousel-prev aria-label="<?php echo esc_attr( jets_translate( 'Previous' ) ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
				</button>
				<div class="carousel__pagination" data-services-carousel-pagination></div>
				<button type="button" class="carousel__arrow" data-services-carousel-next aria-label="<?php echo esc_attr( jets_translate( 'Next' ) ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
				</button>
			</div>
		</div>

		<div class="badge-row reveal" data-reveal style="justify-content: center; margin-top: 2.5rem; --reveal-i: 2">
			<a class="btn btn--secondary" href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>">
				<?php echo esc_html( jets_translate( 'View All Services' ) ); ?>
			</a>
		</div>
	</div>
</section>
