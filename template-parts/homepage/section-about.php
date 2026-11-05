<?php
/**
 * §3 Who We Are (About). Content: PROJECT_CONTEXT.md §6.3, ACF Homepage
 * Content ("About" tab), sourced verbatim from jets.pdf page 2.
 *
 * Interaction (Phase 7): image wipes in via clip-path, copy settles in
 * just after, and a thin vertical line — carrying the connecting-line
 * motif from Philosophy (§2) — draws down the gap between them. The
 * `.split` grid is already direction-aware (PROJECT_CONTEXT.md §7), so
 * the image naturally leads (right-hand side under RTL) without a
 * `.split--reverse` override.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$description    = jets_home_option( 'about_description' );
$brand_statement = jets_home_option( 'about_brand_statement' );

if ( ! $description && ! $brand_statement ) {
	return;
}
?>
<section class="section" aria-labelledby="about-heading">
	<div class="container">
		<div class="split split--connected" data-reveal style="--reveal-i: 0">
			<div class="about-media reveal-media" data-reveal>
				<img src="<?php echo esc_url( jets_dev_image( 'about' ) ); ?>" alt="" fetchpriority="low">
			</div>
			<div class="about-copy reveal" data-reveal style="--reveal-i: 3">
				<div class="section-heading">
					<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'About JETS' ) ); ?></span>
					<h2 class="section-heading__title" id="about-heading"><?php echo esc_html( jets_translate( 'Who We Are' ) ); ?></h2>
				</div>
				<?php if ( $description ) : ?>
					<p><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<?php if ( $brand_statement ) : ?>
					<p><strong><?php echo esc_html( $brand_statement ); ?></strong></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
