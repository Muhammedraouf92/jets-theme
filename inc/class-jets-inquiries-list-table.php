<?php
/**
 * WP_List_Table subclass for the Inquiries admin page (inc/inquiries.php).
 * Only ever required from inside that page's render callback, so core's
 * WP_List_Table base class (admin-only, not loaded by default) is
 * guaranteed to already be available.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Jets_Inquiries_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => 'inquiry',
			'plural'   => 'inquiries',
			'ajax'     => false,
		) );
	}

	public function get_columns() {
		return array(
			'name'        => __( 'Name', 'jets-theme' ),
			'email'       => __( 'Email', 'jets-theme' ),
			'phone'       => __( 'Phone', 'jets-theme' ),
			'message'     => __( 'Message', 'jets-theme' ),
			'source_page' => __( 'Source', 'jets-theme' ),
			'created_at'  => __( 'Received', 'jets-theme' ),
		);
	}

	protected function get_sortable_columns() {
		return array(
			'name'       => array( 'name', false ),
			'email'      => array( 'email', false ),
			'created_at' => array( 'created_at', true ),
		);
	}

	public function prepare_items() {
		global $wpdb;
		$table = jets_inquiries_table_name();

		$per_page     = 20;
		$current_page = $this->get_pagenum();

		$sortable_keys = array( 'name', 'email', 'created_at' );
		$orderby       = ( ! empty( $_GET['orderby'] ) && in_array( $_GET['orderby'], $sortable_keys, true ) )
			? sanitize_key( $_GET['orderby'] )
			: 'created_at';
		$order = ( ! empty( $_GET['order'] ) && 'asc' === strtolower( sanitize_text_field( $_GET['order'] ) ) ) ? 'ASC' : 'DESC';

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
		$offset      = ( $current_page - 1 ) * $per_page;

		// $orderby/$order are validated against a fixed allow-list above, so
		// this string interpolation can't carry attacker-controlled SQL.
		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table ORDER BY $orderby $order LIMIT %d OFFSET %d",
				$per_page,
				$offset
			),
			ARRAY_A
		);

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total_items / $per_page ),
		) );
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'message':
				$text = wp_strip_all_tags( $item['message'] );
				return esc_html( mb_strlen( $text ) > 140 ? mb_substr( $text, 0, 140 ) . '…' : $text );
			case 'phone':
				return $item['phone'] ? esc_html( $item['phone'] ) : '—';
			case 'source_page':
				return $item['source_page'] ? esc_html( $item['source_page'] ) : '—';
			case 'created_at':
				return esc_html( mysql2date( 'M j, Y g:ia', $item['created_at'] ) );
			default:
				return isset( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : '';
		}
	}

	protected function column_name( $item ) {
		$delete_url = wp_nonce_url(
			add_query_arg(
				array(
					'page'   => 'jets-inquiries',
					'action' => 'delete',
					'id'     => $item['id'],
				),
				admin_url( 'admin.php' )
			),
			'jets_delete_inquiry_' . $item['id']
		);

		$actions = array(
			'delete' => sprintf(
				'<a href="%1$s" onclick="return confirm(%2$s);">%3$s</a>',
				esc_url( $delete_url ),
				"'" . esc_js( __( 'Delete this inquiry? This cannot be undone.', 'jets-theme' ) ) . "'",
				esc_html__( 'Delete', 'jets-theme' )
			),
		);

		return sprintf( '<strong>%s</strong>%s', esc_html( $item['name'] ), $this->row_actions( $actions ) );
	}

	protected function column_email( $item ) {
		return sprintf(
			'<a href="mailto:%1$s">%2$s</a>',
			esc_attr( $item['email'] ),
			esc_html( $item['email'] )
		);
	}

	public function no_items() {
		esc_html_e( 'No inquiries yet.', 'jets-theme' );
	}
}
