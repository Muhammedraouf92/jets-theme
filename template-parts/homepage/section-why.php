<?php
/**
 * §4 Why JETS. Content: PROJECT_CONTEXT.md §6.4, ACF Homepage Content
 * ("Why JETS" tab), sourced verbatim from jets.pdf page 5 (all 6 pillars
 * + their one-line descriptions). Icons come from jets_get_pillar_icon(),
 * keyed by the Icon Slug field (falls back to the label when empty).
 *
 * Interaction (Phase 7): cards fade/translate up in a staggered cascade
 * as the grid enters view (`--reveal-i` = grid position); the icon's own
 * "draw-in" is the existing `.card--pillar::before` accent bar, reused
 * here on `.is-visible` instead of only on `:hover`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pillars = jets_home_option( 'why_jets_pillars' );

if ( empty( $pillars ) ) {
	return;
}
?>
<section class="section section--alt" aria-labelledby="why-heading">
	<div class="container">
		<div class="section-heading section-heading--center">
			<h2 class="section-heading__title" id="why-heading"><?php echo esc_html( jets_translate( 'Why JETS?' ) ); ?></h2>
		</div>

		<div class="grid grid--3">
			<?php foreach ( $pillars as $index => $pillar ) : ?>
				<div class="card card--pillar reveal" data-reveal style="--reveal-i: <?php echo (int) $index; ?>">
					<span class="card__icon" aria-hidden="true">
						<?php echo jets_get_pillar_icon( ! empty( $pillar['icon'] ) ? $pillar['icon'] : $pillar['label'], $index ); ?>
					</span>
					<h3 class="card__title"><?php echo esc_html( $pillar['label'] ); ?></h3>
					<?php if ( ! empty( $pillar['description'] ) ) : ?>
						<p class="card__desc"><?php echo esc_html( $pillar['description'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
