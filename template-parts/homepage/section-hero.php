<?php
/**
 * §1 Hero — final, scrollytelling, "Flight Path" art direction
 * (PROJECT_CONTEXT.md §6.1 / §8).
 *
 * Art direction: asymmetric, left-anchored editorial composition (not
 * centered-text-over-photo) — an inset hairline frame, aviation/dusk
 * photography treated as one consistent visual world, and a scroll-
 * progress rail as the signature element. See the design rationale in
 * the Phase 6 redesign notes (PROJECT_CONTEXT.md §6.1).
 *
 * Markup serves two behaviors from one source:
 *  - Desktop/tablet (>=1024px, no prefers-reduced-motion): hero-
 *    scrollytelling.js pins this section and scrubs the sequence to
 *    scroll position via GSAP ScrollTrigger.
 *  - Mobile / reduced-motion: plain CSS handles a single on-load staged
 *    entrance, no pin, no JS dependency.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = jets_home_option( 'hero_eyebrow' );
$heading    = jets_home_option( 'hero_heading' );
$subheading = jets_home_option( 'hero_subheading' );
$contact_url = jets_get_template_page_url( 'page-contact.php', home_url( '/' ) );
$whatsapp_url = jets_get_whatsapp_url();
// Falls back to the Contact page if no WhatsApp number is configured,
// rather than a dead/hidden primary CTA — this button is one of the
// site's two conversion mechanisms (PROJECT_CONTEXT.md §4), not an
// optional icon link like the header/footer ones.
$primary_cta_url = $whatsapp_url ? $whatsapp_url : $contact_url;
$words      = $heading ? explode( ' ', $heading ) : array();
?>
<section class="hero" id="hero">
	<div class="hero__pin">
		

		<div class="hero__media hero__media--arrival">
			<img
				src="<?php echo esc_url( jets_dev_image( 'hero-arrival' ) ); ?>"
				alt=""
				fetchpriority="high"
				decoding="async"
			>
		</div>
		<div class="hero__media hero__media--runway">
			<img
				src="<?php echo esc_url( jets_dev_image( 'hero-runway' ) ); ?>"
				alt=""
				loading="lazy"
				decoding="async"
			>
		</div>
		<div class="hero__scrim" aria-hidden="true"></div>

		<div class="hero__grid container">
			<div class="hero__text">
				<?php if ( $eyebrow ) : ?>
					<span class="hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( $words ) : ?>
					<h1 class="hero__heading" id="hero-heading">
						<?php foreach ( $words as $i => $word ) : ?>
							<span class="hero__word"><?php echo esc_html( $word ); ?></span><?php echo $i < count( $words ) - 1 ? ' ' : ''; ?>
						<?php endforeach; ?>
					</h1>
				<?php endif; ?>

				<?php if ( $subheading ) : ?>
					<p class="hero__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>

				<div class="hero__cta">
					<a class="btn btn--primary" href="<?php echo esc_url( $primary_cta_url ); ?>"<?php echo $whatsapp_url ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( jets_translate( 'Start Planning' ) ); ?>
					</a>
					<a class="btn btn--text hero__cta-secondary" href="<?php echo esc_url( $contact_url ); ?>">
						<?php echo esc_html( jets_translate( 'Contact Us' ) ); ?> <span class="icon">&rarr;</span>
					</a>
				</div>
			</div>
		</div>

		<div class="hero__progress" aria-hidden="true">
			<span class="hero__progress-track"></span>
			<span class="hero__progress-fill"></span>
		</div>
	</div>
</section>
