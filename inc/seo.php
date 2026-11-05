<?php
/**
 * Open Graph / Twitter Card meta tags, so links shared on WhatsApp,
 * Facebook, Twitter/X, etc. show a real title, description, and image
 * instead of a bare URL.
 *
 * The theme otherwise deliberately outputs no SEO meta of its own (no
 * <title>, no meta description, no canonical — see PROJECT_CONTEXT.md
 * §14) to stay out of the way of a future Rank Math/Yoast install. Social
 * preview cards are the one exception: neither plugin is installed today,
 * and without at least an og:image, link previews are effectively broken
 * right now. jets_has_seo_plugin() below makes this stand down
 * automatically the moment a real SEO plugin is active, so it never
 * fights one later.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool True if a plugin that manages its own social meta is active.
 */
function jets_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' )   // Yoast SEO
		|| class_exists( 'RankMath' )   // Rank Math
		|| defined( 'AIOSEO_VERSION' ); // All in One SEO
}

/**
 * Resolves the best available image for the current request, in priority
 * order: the post's own featured image, a `service` single's hero_image
 * field, the Theme Options "Default Share Image", then the site's actual
 * logo (custom Customizer logo if set, else the theme's logo.jpeg) — so a
 * share preview always shows something recognizable, never a blank card.
 *
 * @return array{url: string, width: int, height: int}
 */
function jets_get_social_share_image() {
	$image_id = 0;

	if ( is_singular() ) {
		$image_id = get_post_thumbnail_id();

		if ( ! $image_id && is_singular( 'service' ) && function_exists( 'get_field' ) ) {
			$image_id = (int) get_field( 'hero_image', get_the_ID() );
		}
	}

	if ( ! $image_id && function_exists( 'get_field' ) ) {
		$image_id = (int) get_field( 'default_share_image', 'option' );
	}

	if ( $image_id ) {
		$src = wp_get_attachment_image_src( $image_id, 'large' );
		if ( $src ) {
			return array( 'url' => $src[0], 'width' => (int) $src[1], 'height' => (int) $src[2] );
		}
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$src = wp_get_attachment_image_src( (int) $custom_logo_id, 'large' );
		if ( $src ) {
			return array( 'url' => $src[0], 'width' => (int) $src[1], 'height' => (int) $src[2] );
		}
	}

	// Final fallback: the theme's own logo file (same file header.php/
	// footer.php fall back to when no Customizer logo is set).
	return array(
		'url'    => JETS_THEME_URI . '/assets/images/logo.jpeg',
		'width'  => 1254,
		'height' => 1254,
	);
}

/**
 * @return string Plain-text description for the current request, or ''.
 */
function jets_get_social_share_description() {
	$description = '';

	if ( is_singular( 'service' ) && function_exists( 'get_field' ) ) {
		$description = get_field( 'short_description', get_the_ID() );
	} elseif ( is_singular() ) {
		$description = get_the_excerpt();
	} elseif ( is_front_page() ) {
		$description = jets_home_option( 'hero_subheading' );
	}

	if ( ! $description ) {
		$description = get_bloginfo( 'description' );
	}

	return $description ? wp_strip_all_tags( $description ) : '';
}

function jets_output_social_meta_tags() {
	if ( jets_has_seo_plugin() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = jets_get_social_share_description();
	$image       = jets_get_social_share_image();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );
	?>
	<?php if ( $description ) : ?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image['url'] ); ?>">
	<?php if ( $image['width'] && $image['height'] ) : ?>
	<meta property="og:image:width" content="<?php echo (int) $image['width']; ?>">
	<meta property="og:image:height" content="<?php echo (int) $image['height']; ?>">
	<?php endif; ?>
	<?php if ( function_exists( 'pll_current_language' ) && pll_current_language() ) : ?>
	<meta property="og:locale" content="<?php echo esc_attr( pll_current_language( 'locale' ) ); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image['url'] ); ?>">
	<?php
}
add_action( 'wp_head', 'jets_output_social_meta_tags', 1 );
