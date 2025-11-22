<?php
/**
 * Plugin Name: WooCommerce ViettelPost Shipping
 * Plugin URI: https://example.com/woocommerce-viettelpost
 * Description: Tích hợp ViettelPost với WooCommerce để tính phí ship và quản lý đơn hàng tự động
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: woocommerce-viettelpost
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants
define( 'WC_VIETTELPOST_VERSION', '1.0.0' );
define( 'WC_VIETTELPOST_PLUGIN_FILE', __FILE__ );
define( 'WC_VIETTELPOST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_VIETTELPOST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class
 */
class WC_ViettelPost {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost
	 */
	private static $instance = null;

	/**
	 * Get plugin instance
	 *
	 * @return WC_ViettelPost
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
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Initialize plugin
	 */
	public function init() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		// Load plugin files
		$this->includes();

		// Register shipping method
		add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );

		// Initialize components
		WC_ViettelPost_Order_Status::instance();
		WC_ViettelPost_Shipping::instance();
		WC_ViettelPost_Admin::instance();
		WC_ViettelPost_Frontend::instance();
		WC_ViettelPost_Address::instance();
		WC_ViettelPost_Checkout::instance();
	}

	/**
	 * Register shipping method
	 *
	 * @param array $methods Shipping methods.
	 * @return array
	 */
	public function register_shipping_method( $methods ) {
		$methods['viettelpost'] = 'WC_ViettelPost_Shipping';
		return $methods;
	}

	/**
	 * Include required files
	 */
	private function includes() {
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-api.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-shipping.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-order-status.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-admin.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-frontend.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-address.php';
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-checkout.php';
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		// Load required files first
		require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-order-status.php';
		
		// Register custom order status
		WC_ViettelPost_Order_Status::register_status();
		
		// Set default country to Vietnam
		if ( ! get_option( 'woocommerce_default_country' ) ) {
			update_option( 'woocommerce_default_country', 'VN' );
		}
		
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * WooCommerce missing notice
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="error">
			<p>
				<strong><?php esc_html_e( 'WooCommerce ViettelPost', 'woocommerce-viettelpost' ); ?></strong>
				<?php esc_html_e( 'cần WooCommerce để hoạt động. Vui lòng cài đặt và kích hoạt WooCommerce.', 'woocommerce-viettelpost' ); ?>
			</p>
		</div>
		<?php
	}
}

/**
 * Initialize plugin
 */
function wc_viettelpost() {
	return WC_ViettelPost::instance();
}

// Start plugin
wc_viettelpost();

