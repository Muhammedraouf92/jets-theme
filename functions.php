<?php
/**
 * JETS Travel & Tourism theme bootstrap.
 *
 * Keeps this file slim — all real logic lives in inc/*.
 * See PROJECT_CONTEXT.md for the reasoning behind the file structure.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JETS_THEME_VERSION', '0.1.0' );
define( 'JETS_THEME_DIR', get_template_directory() );
define( 'JETS_THEME_URI', get_template_directory_uri() );

require JETS_THEME_DIR . '/inc/setup.php';
require JETS_THEME_DIR . '/inc/enqueue.php';
require JETS_THEME_DIR . '/inc/custom-post-types.php';
require JETS_THEME_DIR . '/inc/custom-fields.php';
require JETS_THEME_DIR . '/inc/i18n.php';
require JETS_THEME_DIR . '/inc/helpers.php';
require JETS_THEME_DIR . '/inc/inquiries.php';
require JETS_THEME_DIR . '/inc/seo.php';
