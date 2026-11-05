<?php
/**
 * §2 Philosophy / Brand Statement. Content: PROJECT_CONTEXT.md §6.2,
 * ACF Homepage Content ("Philosophy" tab), sourced verbatim from jets.pdf
 * page 18.
 *
 * Interaction (Phase 7): eyebrow → a thin connecting line draws in →
 * the statement cascades in word-by-word → supporting text settles last.
 * All four are one IntersectionObserver pass (main.js) staggered purely
 * via CSS `--reveal-i` (animations.css) — no per-section JS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = jets_home_option( 'philosophy_eyebrow' );
$statement = jets_home_option( 'philosophy_statement' );
$text      = jets_home_option( 'philosophy_text' );

if ( ! $statement && ! $text ) {
	return;
}

$statement_words = $statement ? explode( ' ', $statement ) : array();
$word_count       = count( $statement_words );
?>
<section class="panel--full-bleed section--dark" aria-labelledby="philosophy-heading">
	<div class="panel__content container container--narrow">
		<div class="section-heading section-heading--center">
			<?php if ( $eyebrow ) : ?>
				<span class="section-heading__eyebrow reveal" data-reveal><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<span class="philosophy-line reveal-line" data-reveal style="--reveal-i: 1" aria-hidden="true"></span>

			<?php if ( $statement_words ) : ?>
				<h2 class="section-heading__title reveal-words" id="philosophy-heading" data-reveal>
					<?php foreach ( $statement_words as $i => $word ) : ?>
						<span class="reveal-word" style="--reveal-i: <?php echo (int) ( 4 + $i ); ?>"><?php echo esc_html( $word ); ?></span><?php echo $i < $word_count - 1 ? ' ' : ''; ?>
					<?php endforeach; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="section-heading__intro reveal" data-reveal style="--reveal-i: <?php echo (int) ( 4 + $word_count + 2 ); ?>"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
