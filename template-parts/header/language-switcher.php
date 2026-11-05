<?php
/**
 * Language switcher. Renders nothing pre-Polylang or while only one
 * language is configured (jets_get_language_switcher_items() degrades
 * gracefully — see inc/i18n.php) rather than showing a useless single-item
 * switcher. Used both in the desktop header actions and pinned near the
 * top of the mobile overlay ($args['context'] toggles the modifier class
 * only; markup is identical).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jets_languages = function_exists( 'jets_get_language_switcher_items' ) ? jets_get_language_switcher_items() : array();

if ( count( $jets_languages ) < 2 ) {
	return;
}

$jets_lang_context = isset( $args['context'] ) ? $args['context'] : 'desktop';
?>
<div class="lang-switcher lang-switcher--<?php echo esc_attr( $jets_lang_context ); ?>">
	<?php foreach ( $jets_languages as $jets_lang ) : ?>
		<a
			href="<?php echo esc_url( $jets_lang['url'] ); ?>"
			class="lang-switcher__item<?php echo $jets_lang['active'] ? ' is-active' : ''; ?>"
			<?php echo $jets_lang['active'] ? 'aria-current="true"' : ''; ?>
		><?php echo esc_html( strtoupper( $jets_lang['slug'] ) ); ?></a>
	<?php endforeach; ?>
</div>
