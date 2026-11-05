<?php
/**
 * Desktop primary navigation. Hidden below the tablet breakpoint via CSS
 * in favor of the full-screen overlay in nav-mobile.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! has_nav_menu( 'primary' ) ) {
	return;
}
?>
<nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'jets-theme' ); ?>">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'site-header__menu',
	) );
	?>
</nav>
