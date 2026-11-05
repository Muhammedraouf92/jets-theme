<?php
/**
 * §7 How We Deliver (Corporate/Group/VIP spotlight). Content pulled live
 * from every featured `service` post that has process_steps populated
 * (jets_get_services_with_process_steps()) — purely data-driven, not a
 * hardcoded list of titles. Today that's Corporate Travel, Group Travel &
 * Events, Ground Transportation, Airport Services, and Visa Assistance.
 *
 * Each flow gets its own full-width section (image + a plain inline chain
 * of step titles, reusing the .split layout from section-about.php),
 * alternating light/dark background and media side for rhythm across the
 * run. Steps render as "A → B → C" (.delivery-chain) rather than a
 * numbered/connected-line timeline — these are a short ordered list, not
 * a roadmap diagram, and the arrow-chain phrasing matches how
 * PROJECT_CONTEXT.md itself describes each flow. Real hero_image falls
 * back to a curated per-flow photo (jets_get_delivery_image()) until
 * Media Library images are uploaded, per §12 Asset Strategy.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = jets_get_services_with_process_steps();

if ( empty( $services ) ) {
	return;
}
?>
<section class="section section--tight" aria-labelledby="delivery-heading">
	<div class="container">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'How We Deliver' ) ); ?></span>
			<h2 class="section-heading__title" id="delivery-heading"><?php echo esc_html( jets_translate( 'Operational rigor behind every journey.' ) ); ?></h2>
		</div>
	</div>
</section>

<?php foreach ( $services as $flow_index => $service ) :
	$meta       = jets_get_service_meta( $service );
	$step_count = count( $meta['process_steps'] );
	$is_dark    = 0 === $flow_index % 2;
	$is_reverse = 1 === $flow_index % 2;
	$title      = get_the_title( $service );
	?>
	<section class="section section--tight delivery-flow<?php echo $is_dark ? ' section--dark' : ''; ?>">
		<div class="container">
			<div class="split<?php echo $is_reverse ? ' split--reverse' : ''; ?>">
				<div class="delivery-flow__media reveal-media" data-reveal>
					<?php if ( $meta['hero_image_id'] ) : ?>
						<?php echo wp_get_attachment_image( $meta['hero_image_id'], 'large', false, array( 'loading' => false, 'fetchpriority' => 'low', 'alt' => $title ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( jets_get_delivery_image( $title, $flow_index ) ); ?>" alt="" fetchpriority="low">
					<?php endif; ?>
				</div>

				<div class="delivery-flow__copy reveal" data-reveal style="--reveal-i: 3">
					<span class="delivery-flow__meta"><?php echo esc_html( sprintf( jets_translate( '%d-step process' ), $step_count ) ); ?></span>
					<h3 class="delivery-flow__title"><?php echo esc_html( $title ); ?></h3>

					<ul class="delivery-chain">
						<?php foreach ( $meta['process_steps'] as $step ) : ?>
							<li<?php echo ! empty( $step['description'] ) ? ' title="' . esc_attr( $step['description'] ) . '"' : ''; ?>><?php echo esc_html( $step['title'] ); ?></li>
						<?php endforeach; ?>
					</ul>

					<?php if ( ! empty( $meta['value_badges'] ) ) : ?>
						<div class="badge-row delivery-flow__badges">
							<?php foreach ( $meta['value_badges'] as $badge ) : ?>
								<span class="badge badge--sm"><?php echo esc_html( $badge ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
<?php endforeach; ?>

