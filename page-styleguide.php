<?php
/**
 * Template Name: Style Guide (internal)
 *
 * Internal-only component preview — not linked in any nav menu. Used to
 * visually verify the Phase 3 design system in isolation before it's
 * wired into real templates. Safe to keep (admin-only value) or delete
 * once Phase 4+ templates make it redundant — flagged as a "Future
 * improvements" candidate for removal in PROJECT_CONTEXT.md.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container section">

	<div class="section-heading">
		<span class="section-heading__eyebrow">Internal / QA</span>
		<h1 class="section-heading__title">Style Guide</h1>
		<p class="section-heading__intro">Phase 3 component preview — buttons, badges, cards, process timeline, forms. Not part of the public site.</p>
	</div>

	<h2>Buttons</h2>
	<div class="badge-row" style="margin-bottom:3rem;">
		<a href="#" class="btn btn--primary">Explore Our Services</a>
		<a href="#" class="btn btn--secondary">Plan Your Journey</a>
		<a href="#" class="btn btn--text">View All Services <span class="icon">&rarr;</span></a>
	</div>

	<h2>Badges</h2>
	<div class="badge-row" style="margin-bottom:3rem;">
		<span class="badge">Speed</span>
		<span class="badge">Precision</span>
		<span class="badge">Flexibility</span>
		<span class="badge">Reliable Support</span>
	</div>

	<h2>Service cards</h2>
	<div class="grid grid--3" style="margin-bottom:3rem;">
		<?php
		$demo_services = array(
			array( 'Hotel Reservations', 'The right stay for every journey — location, quality, value, and service.' ),
			array( 'Airport Services', 'Integrated airport services for a smooth, seamless arrival and departure.' ),
			array( 'Visa Assistance', 'Clearer, simpler, ready to travel.' ),
		);
		foreach ( $demo_services as $i => $s ) :
			?>
			<div class="card card--service">
				<span class="card__icon">
					<?php echo jets_get_service_icon( $s[0], $i ); ?>
				</span>
				<h3 class="card__title"><?php echo esc_html( $s[0] ); ?></h3>
				<p class="card__desc"><?php echo esc_html( $s[1] ); ?></p>
				<span class="btn btn--text card__link">Read more <span class="icon">&rarr;</span></span>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Pillar cards (Why JETS)</h2>
	<div class="grid grid--3" style="margin-bottom:3rem;">
		<?php
		$pillars = array( 'Specialized Expertise', 'Integrated Solutions', 'Flexibility' );
		foreach ( $pillars as $p ) :
			?>
			<div class="card card--pillar">
				<span class="card__icon">
					<?php echo jets_get_pillar_icon( $p ); ?>
				</span>
				<h3 class="card__title"><?php echo esc_html( $p ); ?></h3>
				<p class="card__desc">A deep understanding of the travel industry and the evolving needs of our clients and partners.</p>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Process timeline (Corporate Travel)</h2>
	<div class="process-timeline" style="margin-bottom:3rem;">
		<?php
		$steps = array( 'Flights', 'Accommodation', 'Transportation', 'VIP Services', 'Support' );
		foreach ( $steps as $i => $step ) :
			?>
			<div class="process-step">
				<span class="process-step__index"><?php echo esc_html( $i + 1 ); ?></span>
				<span class="process-step__title"><?php echo esc_html( $step ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<h2>Form</h2>
	<form class="container--narrow" style="padding:0; margin-bottom:3rem;" onsubmit="return false;">
		<div class="form__status form__status--success">Thank you — we'll be in touch shortly.</div>
		<div class="field">
			<label class="field__label" for="sg-name">Full Name</label>
			<input class="field__input" id="sg-name" type="text">
		</div>
		<div class="field field--error">
			<label class="field__label" for="sg-email">Email Address</label>
			<input class="field__input" id="sg-email" type="email">
			<span class="field__message field__message--error">Please enter a valid email address.</span>
		</div>
		<div class="field">
			<label class="field__label" for="sg-message">Message</label>
			<textarea class="field__textarea" id="sg-message"></textarea>
		</div>
		<button class="btn btn--primary" type="submit">Send Inquiry</button>
	</form>

	<h2>Full-bleed dark panel</h2>
	<div class="panel--full-bleed section--dark" style="min-height:40vh; border-radius: var(--radius-lg); overflow:hidden; margin-bottom: 3rem;">
		<div class="panel__scrim"></div>
		<div class="panel__content container">
			<span class="section-heading__eyebrow">Brand statement</span>
			<h2>Small details create extraordinary journeys.</h2>
		</div>
	</div>

</div>

<?php
get_footer();
