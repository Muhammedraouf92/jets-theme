<?php
/**
 * §6 Where We Can Take You (Experience categories). Content pulled from
 * the "Tours & Excursions" `service` post (its value_badges are the 5
 * experience categories, per jets.pdf page 12) rather than duplicated in
 * Homepage Content options — see jets_get_service_by_slug().
 *
 * Full-bleed image panel per category (Phase 8): a curated, color-matched
 * photo per category (jets_get_experience_image()) with an overlay label,
 * each panel fading/translating in via the shared [data-reveal] mechanism.
 * Desktop additionally gets a subtle vertical parallax drift per panel
 * (assets/js/experience-parallax.js, vanilla, gated behind min-width +
 * prefers-reduced-motion); mobile keeps just the plain reveal fade — the
 * "simple crossfade on mobile" from the storyboard, simplified to reuse
 * the same reveal mechanism as every other section rather than a bespoke
 * auto-advancing crossfade.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service = jets_get_service_by_slug( 'tours-excursions' );

if ( ! $service ) {
	return;
}

$meta = jets_get_service_meta( $service );

if ( empty( $meta['value_badges'] ) ) {
	return;
}
?>
<section class="section section--alt" aria-labelledby="experiences-heading">
	<div class="container">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Where We Can Take You' ) ); ?></span>
			<h2 class="section-heading__title" id="experiences-heading"><?php echo esc_html( jets_translate( 'Experience More. Discover Further.' ) ); ?></h2>
			<?php if ( $meta['short_description'] ) : ?>
				<p class="section-heading__intro"><?php echo esc_html( $meta['short_description'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="experience-panels" data-parallax-group>
		<?php foreach ( $meta['value_badges'] as $index => $category ) : ?>
			<a
				class="experience-panel reveal"
				data-reveal
				style="--reveal-i: <?php echo (int) $index; ?>"
				href="<?php echo esc_url( get_permalink( $service ) ); ?>"
			>
				<img
					class="experience-panel__media"
					src="<?php echo esc_url( jets_get_experience_image( $category, $index ) ); ?>"
					alt=""
					fetchpriority="low"
				>
				<span class="experience-panel__label"><?php echo esc_html( $category ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="container">
		<div class="badge-row reveal" data-reveal style="justify-content: center; margin-top: 2.5rem; --reveal-i: 1">
			<a class="btn btn--secondary" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
				<?php echo esc_html( jets_translate( 'Explore Tours & Excursions' ) ); ?>
			</a>
		</div>
	</div>
</section>
