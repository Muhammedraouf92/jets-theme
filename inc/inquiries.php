<?php
/**
 * Inquiry storage + admin review, per PROJECT_CONTEXT.md §10 "Inquiries
 * storage" and the Decisions Log: a plain custom table (not a CPT) so
 * submissions stay out of wp_posts/wp_postmeta and away from REST/search/
 * query exposure, viewed via a native WP_List_Table admin screen. Storage
 * and the admin wp_mail() notification are independent — a failed email
 * must never lose a submission that already made it into the database.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'JETS_INQUIRIES_DB_VERSION', '1.0' );

/**
 * Table name, namespaced by $wpdb->prefix like every other WP table.
 *
 * @return string
 */
function jets_inquiries_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'jets_inquiries';
}

/**
 * Creates (or upgrades) the jets_inquiries table via dbDelta().
 */
function jets_create_inquiries_table() {
	global $wpdb;

	$table_name      = jets_inquiries_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(191) NOT NULL,
		email VARCHAR(191) NOT NULL,
		phone VARCHAR(50) NOT NULL DEFAULT '',
		message TEXT NOT NULL,
		source_page VARCHAR(255) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'jets_inquiries_db_version', JETS_INQUIRIES_DB_VERSION );
}
add_action( 'after_switch_theme', 'jets_create_inquiries_table' );

/**
 * Safety net for a theme that's already active when this file is added —
 * `after_switch_theme` only fires on activation, so this catches the
 * table up on the next admin page load instead of requiring a manual
 * deactivate/reactivate.
 */
function jets_maybe_upgrade_inquiries_table() {
	if ( get_option( 'jets_inquiries_db_version' ) !== JETS_INQUIRIES_DB_VERSION ) {
		jets_create_inquiries_table();
	}
}
add_action( 'admin_init', 'jets_maybe_upgrade_inquiries_table' );

/**
 * AJAX handler for the Contact page's inquiry form (assets/js/forms.js).
 * Registered for both logged-in and logged-out requests since every real
 * submitter is a logged-out site visitor.
 */
function jets_handle_inquiry_submission() {
	if ( ! isset( $_POST['jets_inquiry_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jets_inquiry_nonce'] ) ), 'jets_inquiry_submit' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh and try again.', 'jets-theme' ) ), 403 );
	}

	// Honeypot: a real visitor never sees or fills this field (hidden via
	// CSS, tabindex="-1"). A bot that fills it gets a fake success response
	// so it doesn't retry — the submission is silently dropped, not stored
	// or emailed.
	if ( ! empty( $_POST['jets_hp_field'] ) ) {
		wp_send_json_success( array( 'message' => __( "Thank you — we'll be in touch shortly.", 'jets-theme' ) ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$source  = isset( $_POST['source_page'] ) ? esc_url_raw( wp_unslash( $_POST['source_page'] ) ) : '';

	$errors = array();
	if ( '' === $name ) {
		$errors['name'] = __( 'Please enter your name.', 'jets-theme' );
	}
	if ( '' === $email || ! is_email( $email ) ) {
		$errors['email'] = __( 'Please enter a valid email address.', 'jets-theme' );
	}
	if ( '' === $phone ) {
		$errors['phone'] = __( 'Please enter a valid phone number.', 'jets-theme' );
	}
	if ( '' === $message ) {
		$errors['message'] = __( 'Please enter a message.', 'jets-theme' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error( array(
			'message' => __( 'Please correct the highlighted fields.', 'jets-theme' ),
			'fields'  => $errors,
		), 422 );
	}

	global $wpdb;

	$inserted = $wpdb->insert(
		jets_inquiries_table_name(),
		array(
			'name'        => $name,
			'email'       => $email,
			'phone'       => $phone,
			'message'     => $message,
			'source_page' => $source,
			'created_at'  => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'jets-theme' ) ), 500 );
	}

	$admin_email = 'admin@jetstravels.com';
	if ( $admin_email ) {
		$subject = sprintf(
			/* translators: 1: site name, 2: submitter name. */
			__( '[%1$s] New inquiry from %2$s', 'jets-theme' ),
			get_bloginfo( 'name' ),
			$name
		);
		$body = "Name: $name\nEmail: $email\nPhone: $phone\nSource: $source\n\nMessage:\n$message";
		wp_mail( $admin_email, $subject, $body );
	}

	wp_send_json_success( array( 'message' => __( "Thank you — we'll be in touch shortly.", 'jets-theme' ) ) );
}
add_action( 'wp_ajax_jets_submit_inquiry', 'jets_handle_inquiry_submission' );
add_action( 'wp_ajax_nopriv_jets_submit_inquiry', 'jets_handle_inquiry_submission' );

/**
 * Top-level "Inquiries" admin page, manage_options only.
 */
function jets_register_inquiries_admin_page() {
	add_menu_page(
		__( 'Inquiries', 'jets-theme' ),
		__( 'Inquiries', 'jets-theme' ),
		'manage_options',
		'jets-inquiries',
		'jets_render_inquiries_admin_page',
		'dashicons-email-alt2',
		26
	);
}
add_action( 'admin_menu', 'jets_register_inquiries_admin_page' );

/**
 * Handles the per-row delete link on the Inquiries admin page. Runs on
 * admin_init (before any output) so it can redirect after deleting.
 */
function jets_handle_inquiry_delete() {
	if ( empty( $_GET['page'] ) || 'jets-inquiries' !== $_GET['page'] ) {
		return;
	}
	if ( empty( $_GET['action'] ) || 'delete' !== $_GET['action'] || empty( $_GET['id'] ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$id = absint( $_GET['id'] );
	check_admin_referer( 'jets_delete_inquiry_' . $id );

	global $wpdb;
	$wpdb->delete( jets_inquiries_table_name(), array( 'id' => $id ), array( '%d' ) );

	wp_safe_redirect( remove_query_arg( array( 'action', 'id', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'jets_handle_inquiry_delete' );

/**
 * Renders the Inquiries admin page.
 */
function jets_render_inquiries_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	require_once JETS_THEME_DIR . '/inc/class-jets-inquiries-list-table.php';

	$list_table = new Jets_Inquiries_List_Table();
	$list_table->prepare_items();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Inquiries', 'jets-theme' ); ?></h1>
		<p><?php esc_html_e( "Submissions from the site's inquiry form. Deleting a row here cannot be undone.", 'jets-theme' ); ?></p>
		<form method="get">
			<input type="hidden" name="page" value="jets-inquiries">
			<?php $list_table->display(); ?>
		</form>
	</div>
	<?php
}
