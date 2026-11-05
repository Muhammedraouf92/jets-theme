<?php
/**
 * §8 Technology (Digital Travel Solutions). Content pulled from the
 * "Digital Travel Solutions" `service` post (its value_badges are the 4
 * pillars — Access/Booking/Connectivity/Efficiency, per jets.pdf page
 * 14) rather than duplicated in Homepage Content options.
 *
 * Interaction (Phase 9): a device icon connects to each pillar via an SVG
 * connector that draws in on scroll-into-view (stroke-dasharray/dashoffset,
 * per PROJECT_CONTEXT.md §8's animation table), standing in for the
 * brief's "device/dashboard-style graphic with connecting nodes" without
 * needing literal UI chrome. Node x-positions are computed from the
 * actual badge count so this still works cleanly if a future edit adds
 * or removes a pillar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service = jets_get_service_by_slug( 'digital-travel-solutions' );

if ( ! $service ) {
	return;
}

$meta       = jets_get_service_meta( $service );
$pillars    = $meta['value_badges'];
$node_count = count( $pillars );
?>
<section class="section section--alt" aria-labelledby="technology-heading">
	<div class="container">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Digital Travel Solutions' ) ); ?></span>
			<h2 class="section-heading__title" id="technology-heading"><?php echo esc_html( jets_translate( 'Connected Travel. Smarter Operations.' ) ); ?></h2>
			<?php if ( $meta['short_description'] ) : ?>
				<p class="section-heading__intro"><?php echo esc_html( $meta['short_description'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $node_count > 0 ) : ?>
			<div class="tech-diagram reveal" data-reveal style="--reveal-i: 1">
				<div class="tech-diagram__device" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="1.5"/><path d="M8 20h8M12 16v4"/></svg>
				</div>
				<svg class="tech-diagram__connectors" viewBox="0 0 400 70" preserveAspectRatio="none" aria-hidden="true">
					<?php foreach ( $pillars as $index => $pillar ) : ?>
						<?php $x = round( ( $index + 0.5 ) / $node_count * 400, 1 ); ?>
						<path pathLength="1" d="M200,0 C200,35 <?php echo esc_attr( $x ); ?>,35 <?php echo esc_attr( $x ); ?>,70"/>
					<?php endforeach; ?>
				</svg>
				<div class="tech-diagram__nodes grid grid--<?php echo (int) min( 4, max( 2, $node_count ) ); ?>">
					<?php foreach ( $pillars as $pillar ) : ?>
						<div class="tech-diagram__node">
							<span class="tech-diagram__node-dot" aria-hidden="true"></span>
							<span class="tech-diagram__node-label"><?php echo esc_html( $pillar ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
