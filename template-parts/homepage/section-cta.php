<?php
/**
 * §10 Final CTA. Content: PROJECT_CONTEXT.md §6.10, ACF Homepage Content
 * ("Final CTA" tab), sourced verbatim from jets.pdf page 19 (closing
 * statement).
 *
 * Interaction (Phase 9): the headline cascades in word-by-word on
 * scroll-into-view — the same `.reveal-words`/`.reveal-word` technique
 * Philosophy's brand statement already uses (PROJECT_CONTEXT.md §8's
 * animation table calls for this on both), closing the emotional loop
 * the Hero opened with matching treatment rather than a plain fade.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$headline     = jets_home_option( 'cta_headline' );
$text         = jets_home_option( 'cta_text' );
$closing_line = jets_home_option( 'cta_closing_line' );
$contact_url  = jets_get_template_page_url( 'page-contact.php', home_url( '/' ) );
$whatsapp_url = jets_get_whatsapp_url();
// Falls back to the Contact page if no WhatsApp number is configured —
// same reasoning as the Hero's matching "Start Planning" button.
$primary_cta_url = $whatsapp_url ? $whatsapp_url : $contact_url;

if ( ! $headline && ! $text ) {
	return;
}

$headline_words = $headline ? explode( ' ', $headline ) : array();
$word_count      = count( $headline_words );
?>
<section class="panel--full-bleed section--alt" aria-labelledby="cta-heading">
	<div class="panel__content container container--narrow">
		<div class="section-heading section-heading--center">
			<?php if ( $headline_words ) : ?>
				<h2 class="section-heading__title reveal-words" id="cta-heading" data-reveal>
					<?php foreach ( $headline_words as $i => $word ) : ?>
						<span class="reveal-word" style="--reveal-i: <?php echo (int) $i; ?>"><?php echo esc_html( $word ); ?></span><?php echo $i < $word_count - 1 ? ' ' : ''; ?>
					<?php endforeach; ?>
				</h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p class="section-heading__intro reveal" data-reveal style="--reveal-i: <?php echo (int) ( $word_count + 2 ); ?>"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<?php if ( $closing_line ) : ?>
				<p class="reveal" data-reveal style="--reveal-i: <?php echo (int) ( $word_count + 3 ); ?>"><strong><?php echo esc_html( $closing_line ); ?></strong></p>
			<?php endif; ?>
		</div>

		<div class="badge-row reveal" data-reveal style="justify-content: center; --reveal-i: <?php echo (int) ( $word_count + 4 ); ?>">
			<a class="btn btn--primary" href="<?php echo esc_url( $primary_cta_url ); ?>"<?php echo $whatsapp_url ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( jets_translate( 'Start Planning' ) ); ?>
			</a>
			<a class="btn btn--secondary" href="<?php echo esc_url( $contact_url ); ?>">
				<?php echo esc_html( jets_translate( 'Contact Us' ) ); ?>
			</a>
		</div>
	</div>
</section>
