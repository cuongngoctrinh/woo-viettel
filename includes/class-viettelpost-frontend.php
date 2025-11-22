<?php
/**
 * Frontend Scripts and Styles
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Frontend class
 */
class WC_ViettelPost_Frontend {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost_Frontend
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return WC_ViettelPost_Frontend
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_scripts() {
		if ( ! is_checkout() && ! is_account_page() ) {
			return;
		}

		wp_enqueue_style(
			'viettelpost-address',
			WC_VIETTELPOST_PLUGIN_URL . 'assets/css/viettelpost-address.css',
			array(),
			WC_VIETTELPOST_VERSION
		);

		wp_enqueue_script(
			'viettelpost-checkout',
			WC_VIETTELPOST_PLUGIN_URL . 'assets/js/viettelpost-checkout.js',
			array( 'jquery', 'wc-checkout' ),
			WC_VIETTELPOST_VERSION,
			true
		);

		wp_enqueue_script(
			'viettelpost-address',
			WC_VIETTELPOST_PLUGIN_URL . 'assets/js/viettelpost-address.js',
			array( 'jquery', 'wc-checkout' ),
			WC_VIETTELPOST_VERSION,
			true
		);

		wp_localize_script(
			'viettelpost-checkout',
			'wc_viettelpost_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'viettelpost-calculate' ),
			)
		);

		wp_localize_script(
			'viettelpost-address',
			'wc_viettelpost_address',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'viettelpost-address' ),
				'select_province' => __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' ),
				'select_district' => __( 'Chọn quận/huyện', 'woocommerce-viettelpost' ),
				'select_ward'     => __( 'Chọn phường/xã', 'woocommerce-viettelpost' ),
			)
		);
	}
}

