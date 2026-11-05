<?php
/**
 * Template Name: Contact
 *
 * Contact details (ACF fields scoped to this template — inc/custom-fields.php
 * — each individually optional, hidden gracefully rather than printed empty
 * per PROJECT_CONTEXT.md §13) plus the site's inquiry form. Submissions are
 * both stored in `{$wpdb->prefix}jets_inquiries` and emailed to the admin,
 * independently of each other (inc/inquiries.php) — see PROJECT_CONTEXT.md
 * §10 "Inquiries storage".
 *
 * Design (Phase 9 revision): a full-bleed photo intro panel — like
 * single-service.php's hero — rather than a plain centered heading, so
 * Contact reads as a designed "moment" like every other page-level opener
 * in the theme instead of the odd one out. Content anchors to the bottom
 * of the frame (the source photo's naturally dark empty-seating area),
 * echoing the Hero's own asymmetric-not-centered art direction. See
 * .contact-hero in components.css and jets_dev_image('contact-hero').
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$phone        = get_field( 'contact_phone' );
$whatsapp     = get_field( 'contact_whatsapp' );
$email        = get_field( 'contact_email' );
$address      = get_field( 'contact_address' );
$hours        = get_field( 'contact_hours' );
$map_embed    = get_field( 'contact_map_embed' );
$whatsapp_url = jets_get_whatsapp_url( $whatsapp );
$has_details  = $phone || $whatsapp_url || $email || $address || $hours;
?>

<section class="panel--full-bleed section--dark contact-hero" aria-labelledby="page-contact-heading">
	<div class="panel__media" aria-hidden="true">
		<img src="<?php echo esc_url( jets_dev_image( 'contact-hero' ) ); ?>" alt="" loading="eager" fetchpriority="high">
	</div>
	<div class="panel__scrim" aria-hidden="true"></div>

	<div class="panel__content container container--narrow">
		<div class="section-heading reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Get In Touch' ) ); ?></span>
			<h1 class="section-heading__title" id="page-contact-heading"><?php echo esc_html( jets_translate( 'Contact Us' ) ); ?></h1>
			<p class="section-heading__intro"><?php echo esc_html( jets_translate( "Tell us where you're headed — our team will take it from there." ) ); ?></p>
		</div>
	</div>
</section>

<section class="section section--alt">
	<div class="container">
		<div class="grid grid--2">
			<?php if ( $has_details ) : ?>
				<div class="contact-details reveal" data-reveal>
					<?php if ( $phone ) : ?>
						<p class="contact-details__row">
							<span class="contact-details__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6.6 10.5c1.4 2.8 3.7 5.1 6.5 6.5l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.1 21 3 13.9 3 5c0-.6.4-1 1-1h3.9c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1L6.6 10.5Z"/></svg>
							</span>
							<span class="contact-details__text">
								<span class="contact-details__label"><?php echo esc_html( jets_translate( 'Phone' ) ); ?></span>
								<a class="contact-details__value" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
							</span>
						</p>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<p class="contact-details__row">
							<span class="contact-details__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
							</span>
							<span class="contact-details__text">
								<span class="contact-details__label"><?php echo esc_html( jets_translate( 'Email' ) ); ?></span>
								<a class="contact-details__value" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
							</span>
						</p>
					<?php endif; ?>
					<?php if ( $address ) : ?>
						<p class="contact-details__row">
							<span class="contact-details__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.25"/></svg>
							</span>
							<span class="contact-details__text">
								<span class="contact-details__label"><?php echo esc_html( jets_translate( 'Address' ) ); ?></span>
								<span class="contact-details__value"><?php echo nl2br( esc_html( $address ) ); ?></span>
							</span>
						</p>
					<?php endif; ?>
					<?php if ( $hours ) : ?>
						<p class="contact-details__row">
							<span class="contact-details__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>
							</span>
							<span class="contact-details__text">
								<span class="contact-details__label"><?php echo esc_html( jets_translate( 'Business Hours' ) ); ?></span>
								<span class="contact-details__value"><?php echo esc_html( $hours ); ?></span>
							</span>
						</p>
					<?php endif; ?>
					<?php if ( $whatsapp_url ) : ?>
						<a class="btn btn--secondary contact-details__whatsapp" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( jets_translate( 'Message us on WhatsApp' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="reveal" data-reveal style="--reveal-i: 1">
				<form
					class="inquiry-form"
					data-inquiry-form
					method="post"
					data-error-summary="<?php echo esc_attr( jets_translate( 'Please correct the highlighted fields.' ) ); ?>"
					data-error-network="<?php echo esc_attr( jets_translate( 'Something went wrong. Please try again.' ) ); ?>"
					novalidate
				>
					<h2 class="inquiry-form__heading"><?php echo esc_html( jets_translate( 'Send an Inquiry' ) ); ?></h2>

					<div class="form__status" data-form-status role="status" aria-live="polite" hidden></div>

					<?php wp_nonce_field( 'jets_inquiry_submit', 'jets_inquiry_nonce' ); ?>
					<input type="hidden" name="source_page" value="<?php echo esc_url( home_url( add_query_arg( null, null ) ) ); ?>">

					<div class="field visually-hidden" aria-hidden="true">
						<label class="field__label" for="jets-hp-field">Leave this field empty</label>
						<input class="field__input" type="text" id="jets-hp-field" name="jets_hp_field" tabindex="-1" autocomplete="off">
					</div>

					<div class="field">
						<label class="field__label" for="contact-name"><?php echo esc_html( jets_translate( 'Full Name' ) ); ?></label>
						<input
							class="field__input" type="text" id="contact-name" name="name" required
							data-error-required="<?php echo esc_attr( jets_translate( 'Please enter your name.' ) ); ?>"
							aria-describedby="contact-name-error"
						>
						<span class="field__message field__message--error" id="contact-name-error"></span>
					</div>

					<div class="field">
						<label class="field__label" for="contact-email"><?php echo esc_html( jets_translate( 'Email Address' ) ); ?></label>
						<input
							class="field__input" type="email" id="contact-email" name="email" required
							data-error-invalid="<?php echo esc_attr( jets_translate( 'Please enter a valid email address.' ) ); ?>"
							aria-describedby="contact-email-error"
						>
						<span class="field__message field__message--error" id="contact-email-error"></span>
					</div>

					<div class="field">
						<label class="field__label" for="contact-phone"><?php echo esc_html( jets_translate( 'Phone Number' ) ); ?></label>
						<input class="field__input" type="tel" id="contact-phone" name="phone"
						data-error-required="<?php echo esc_attr( jets_translate( 'Please enter your phone number.' ) ); ?>"
						data-error-invalid="<?php echo esc_attr( jets_translate( 'Please enter a valid phone number.' ) ); ?>"
						aria-describedby="contact-phone-error"
						>
						<span class="field__message field__message--error" id="contact-phone-error"></span>
					</div>

					<div class="field">
						<label class="field__label" for="contact-message"><?php echo esc_html( jets_translate( 'Message' ) ); ?></label>
						<textarea
							class="field__textarea" id="contact-message" name="message" required
							data-error-required="<?php echo esc_attr( jets_translate( 'Please enter a message.' ) ); ?>"
							aria-describedby="contact-message-error"
						></textarea>
						<span class="field__message field__message--error" id="contact-message-error"></span>
					</div>

					<button class="btn btn--primary" type="submit"><?php echo esc_html( jets_translate( 'Send Inquiry' ) ); ?></button>
				</form>
			</div>
		</div>
	</div>
</section>

<?php if ( $map_embed ) : ?>
	<section class="section section--tight" aria-labelledby="contact-map-heading">
		<div class="container">
			<h2 class="visually-hidden" id="contact-map-heading"><?php echo esc_html( jets_translate( 'Our Location' ) ); ?></h2>
			<div class="contact-map reveal" data-reveal>
				<iframe
					class="contact-map__frame"
					src="<?php echo esc_url( $map_embed ); ?>"
					title="<?php echo esc_attr( jets_translate( 'Map showing our location' ) ); ?>"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
				></iframe>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();