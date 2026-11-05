<?php
/**
 * §9 Network & Who We Serve. Combines Partnerships + Our Network +
 * Clients & Partners (jets.pdf pages 15-17) into one section, per
 * PROJECT_CONTEXT.md §6.9. Content: ACF Homepage Content ("Network" tab).
 *
 * PROJECT_CONTEXT.md's own brief for this section calls for a "stylized
 * world-map/connection-line graphic" — rendered here as a low-opacity
 * decorative node network (.network-section__backdrop) rather than
 * literal cartography, on a dark full-bleed section that bookends the
 * dark Hero and hands off directly into the dark Final CTA panel that
 * follows it (no light/dark/light flicker between the two).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$partnerships_text    = jets_home_option( 'partnerships_text' );
$partnerships_tagline = jets_home_option( 'partnerships_tagline' );
$network_text         = jets_home_option( 'network_text' );
$network_tagline      = jets_home_option( 'network_tagline' );
$network_categories   = jets_home_option( 'network_categories' );
$clients_intro        = jets_home_option( 'clients_intro' );
$clients_tagline      = jets_home_option( 'clients_tagline' );
$client_types         = jets_home_option( 'client_types' );

if ( ! $partnerships_text && ! $network_text && ! $clients_intro ) {
	return;
}
?>
<section class="section section--dark network-section" aria-labelledby="network-heading">
	<div class="network-section__backdrop reveal" data-reveal aria-hidden="true">
		<svg viewBox="0 0 1200 500" preserveAspectRatio="xMidYMid slice" fill="none" stroke="currentColor" stroke-width="1.5">
			<path pathLength="1" d="M80 120 220 300 M220 300 350 80 M350 80 480 220 M480 220 600 350 M600 350 720 100 M720 100 850 280 M850 280 980 150 M980 150 1100 320 M220 300 300 420 M600 350 650 450 M850 280 950 420 M480 220 720 100"/>
			<g fill="currentColor" stroke="none">
				<circle cx="80" cy="120" r="4"/>
				<circle cx="220" cy="300" r="4"/>
				<circle cx="350" cy="80" r="4"/>
				<circle cx="480" cy="220" r="4"/>
				<circle cx="600" cy="350" r="4"/>
				<circle cx="720" cy="100" r="4"/>
				<circle cx="850" cy="280" r="4"/>
				<circle cx="980" cy="150" r="4"/>
				<circle cx="1100" cy="320" r="4"/>
				<circle cx="300" cy="420" r="4"/>
				<circle cx="650" cy="450" r="4"/>
				<circle cx="950" cy="420" r="4"/>
			</g>
		</svg>
	</div>

	<div class="container">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Network & Who We Serve' ) ); ?></span>
			<h2 class="section-heading__title" id="network-heading"><?php echo esc_html( jets_translate( 'A connected world, working for you.' ) ); ?></h2>
		</div>

		<div class="grid grid--3">
			<?php if ( $partnerships_text ) : ?>
				<div class="network-block reveal" data-reveal style="--reveal-i: 0">
					<span class="network-block__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 15l6-6"/><path d="M11 6l1.5-1.5a3.5 3.5 0 0 1 5 5L16 11"/><path d="M13 18l-1.5 1.5a3.5 3.5 0 0 1-5-5L8 13"/></svg>
					</span>
					<h3 class="network-block__title"><?php echo esc_html( jets_translate( 'Partnerships' ) ); ?></h3>
					<p class="network-block__desc"><?php echo esc_html( $partnerships_text ); ?></p>
					<?php if ( $partnerships_tagline ) : ?>
						<p class="network-block__tagline"><?php echo esc_html( $partnerships_tagline ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $network_text ) : ?>
				<div class="network-block reveal" data-reveal style="--reveal-i: 1">
					<span class="network-block__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="17" r="2.5"/><circle cx="19" cy="17" r="2.5"/><path d="M12 7.5 5 14.7M12 7.5l7 7.2M7.3 17h9.4"/></svg>
					</span>
					<h3 class="network-block__title"><?php echo esc_html( jets_translate( 'Our Network' ) ); ?></h3>
					<p class="network-block__desc"><?php echo esc_html( $network_text ); ?></p>
					<?php if ( ! empty( $network_categories ) ) : ?>
						<div class="badge-row">
							<?php foreach ( $network_categories as $category ) : ?>
								<span class="badge"><?php echo esc_html( $category['label'] ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $network_tagline ) : ?>
						<p class="network-block__tagline"><?php echo esc_html( $network_tagline ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $clients_intro ) : ?>
				<div class="network-block reveal" data-reveal style="--reveal-i: 2">
					<span class="network-block__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5"/><circle cx="17" cy="8.5" r="2.4"/><path d="M15.5 14.2c2.5.3 4.3 2.1 4.5 4.8"/></svg>
					</span>
					<h3 class="network-block__title"><?php echo esc_html( jets_translate( 'Clients & Partners' ) ); ?></h3>
					<p class="network-block__desc"><?php echo esc_html( $clients_intro ); ?></p>
					<?php if ( ! empty( $client_types ) ) : ?>
						<div class="badge-row">
							<?php foreach ( $client_types as $type ) : ?>
								<span class="badge"><?php echo esc_html( $type['label'] ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $clients_tagline ) : ?>
						<p class="network-block__tagline"><?php echo esc_html( $clients_tagline ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
