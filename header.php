<?php
/**
 * Header markup.
 *
 * Phase 4: sticky header with transparent-over-hero → solid-on-scroll
 * behavior (navigation.js), full-screen mobile overlay menu, and the
 * Polylang-backed language switcher. See PROJECT_CONTEXT.md §7 "Navigation"
 * and §9 Responsive Strategy.
 *
 * The transparent starting state only applies on the front page (where a
 * full-bleed hero exists behind it once Phase 6 lands) — every other page
 * has no hero to sit transparently over, so the header is solid from the
 * start there; navigation.js still adds the scrolled/solid class on scroll
 * on every page for a consistent sticky feel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jets_header_classes = array( 'site-header' );
if ( is_front_page() ) {
	$jets_header_classes[] = 'site-header--transparent';
}
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php echo esc_url( JETS_THEME_URI . '/assets/images/favicon.ico' ); ?>" sizes="any">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( JETS_THEME_URI . '/assets/images/favicon-32x32.png' ); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( JETS_THEME_URI . '/assets/images/favicon-16x16.png' ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( JETS_THEME_URI . '/assets/images/apple-touch-icon.png' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'jets-theme' ); ?></a>

<header class="<?php echo esc_attr( implode( ' ', $jets_header_classes ) ); ?>" id="site-header">
	<div class="container site-header__inner">
		<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php
				// No Customizer logo set — the theme's own logo mark
				// (assets/images/logo.jpeg, background-removed + cropped to
				// just the icon via inc/helpers.php-adjacent build step, see
				// assets/images/logo-icon.png) plus a plain-text wordmark,
				// rather than the site's wp-admin Settings > General title
				// (which isn't necessarily "Jets"). Private single-client
				// theme — hardcoded brand name, same precedent as the
				// footer's wordmark.
				?>
				<img class="site-header__logo" src="<?php echo esc_url( JETS_THEME_URI . '/assets/images/logo-icon.png' ); ?>" alt="" width="46" height="40">
				<span class="site-header__brand-name">Jets</span>
			<?php endif; ?>
		</a>

		<?php get_template_part( 'template-parts/header/nav-desktop' ); ?>

		<div class="site-header__actions">
			<?php get_template_part( 'template-parts/header/language-switcher', null, array( 'context' => 'desktop' ) ); ?>

			<?php $jets_header_whatsapp_url = jets_get_whatsapp_url(); ?>
			<?php if ( $jets_header_whatsapp_url ) : ?>
				<a class="whatsapp-btn" href="<?php echo esc_url( $jets_header_whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
					<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.06-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18.2c-1.67 0-3.22-.49-4.52-1.34l-.32-.2-3 .79.8-2.92-.21-.3A8.19 8.19 0 1 1 20.2 12 8.2 8.2 0 0 1 12 20.2Zm4.5-6.13c-.25-.12-1.45-.71-1.68-.79-.22-.08-.39-.12-.55.12-.16.25-.63.79-.77.95-.14.16-.28.18-.53.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.28.37-.42.12-.14.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.42.06-.64.31-.22.25-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.45-.59 1.65-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.47-.28Z"/></svg>
					<span class="visually-hidden"><?php echo esc_html( jets_translate( 'Chat on WhatsApp' ) ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<button
					type="button"
					class="site-header__toggle"
					id="nav-toggle"
					aria-expanded="false"
					aria-controls="nav-mobile"
				>
					<span class="site-header__toggle-bar"></span>
					<span class="site-header__toggle-bar"></span>
					<span class="site-header__toggle-bar"></span>
					<span class="visually-hidden"><?php echo esc_html( jets_translate( 'Menu' ) ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</header>

<?php get_template_part( 'template-parts/header/nav-mobile' ); ?>

<main id="main">
