<?php
/**
 * Full-screen mobile overlay menu. Markup is always printed (so it can be
 * targeted by CSS/JS regardless of breakpoint) but stays visually and
 * programmatically hidden — `aria-hidden="true"` plus CSS — until
 * navigation.js toggles `is-open` from the header's hamburger button.
 *
 * The language switcher is placed above the menu per PROJECT_CONTEXT.md
 * §9 ("language switcher pinned near the top").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! has_nav_menu( 'primary' ) ) {
	return;
}
?>
<div class="nav-mobile" id="nav-mobile" aria-hidden="true">
	<div class="nav-mobile__scrim"></div>
	<div class="nav-mobile__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site navigation', 'jets-theme' ); ?>">
		<div class="nav-mobile__panel-header">
			<?php get_template_part( 'template-parts/header/language-switcher', null, array( 'context' => 'mobile' ) ); ?>

			<button type="button" class="nav-mobile__close" id="nav-mobile-close">
				<span aria-hidden="true">&times;</span>
				<span class="visually-hidden"><?php echo esc_html( jets_translate( 'Close' ) ); ?></span>
			</button>
		</div>

		<nav aria-label="<?php esc_attr_e( 'Primary', 'jets-theme' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav-mobile__menu',
			) );
			?>
		</nav>

			<?php $jets_mobile_whatsapp_url = jets_get_whatsapp_url(); ?>
			<?php if ( $jets_mobile_whatsapp_url ) : ?>
				<a class="whatsapp-btn whatsapp-btn--labeled" href="<?php echo esc_url( $jets_mobile_whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.06-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18.2c-1.67 0-3.22-.49-4.52-1.34l-.32-.2-3 .79.8-2.92-.21-.3A8.19 8.19 0 1 1 20.2 12 8.2 8.2 0 0 1 12 20.2Zm4.5-6.13c-.25-.12-1.45-.71-1.68-.79-.22-.08-.39-.12-.55.12-.16.25-.63.79-.77.95-.14.16-.28.18-.53.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.28.37-.42.12-.14.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.42.06-.64.31-.22.25-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.45-.59 1.65-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.47-.28Z"/></svg>
					<?php echo esc_html( jets_translate( 'Chat on WhatsApp' ) ); ?>
				</a>
			<?php endif; ?>
	</div>
</div>
