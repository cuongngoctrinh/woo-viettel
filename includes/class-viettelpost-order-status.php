<?php
/**
 * Custom Order Status for ViettelPost
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Order_Status class
 */
class WC_ViettelPost_Order_Status {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost_Order_Status
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return WC_ViettelPost_Order_Status
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
		add_action( 'init', array( $this, 'register_status' ), 10 );
		add_filter( 'wc_order_statuses', array( $this, 'add_order_status' ) );
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( $this, 'register_post_status' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'on_status_change' ), 10, 4 );
	}

	/**
	 * Register custom order status
	 */
	public static function register_status() {
		register_post_status(
			'wc-cho-duyet',
			array(
				'label'                     => _x( 'Chờ duyệt', 'Order status', 'woocommerce-viettelpost' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list'  => true,
				'label_count'               => _n_noop( 'Chờ duyệt <span class="count">(%s)</span>', 'Chờ duyệt <span class="count">(%s)</span>', 'woocommerce-viettelpost' ),
			)
		);
	}

	/**
	 * Add order status to WooCommerce
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array
	 */
	public function add_order_status( $order_statuses ) {
		$order_statuses['wc-cho-duyet'] = _x( 'Chờ duyệt', 'Order status', 'woocommerce-viettelpost' );
		return $order_statuses;
	}

	/**
	 * Register post status
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array
	 */
	public function register_post_status( $order_statuses ) {
		$order_statuses['wc-cho-duyet'] = array(
			'label'                     => _x( 'Chờ duyệt', 'Order status', 'woocommerce-viettelpost' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Chờ duyệt <span class="count">(%s)</span>', 'Chờ duyệt <span class="count">(%s)</span>', 'woocommerce-viettelpost' ),
		);
		return $order_statuses;
	}

	/**
	 * Handle order status change
	 *
	 * @param int    $order_id Order ID.
	 * @param string $old_status Old status.
	 * @param string $new_status New status.
	 * @param object $order Order object.
	 */
	public function on_status_change( $order_id, $old_status, $new_status, $order ) {
		// Set order to "Chờ duyệt" after payment
		if ( ( 'processing' === $new_status || 'completed' === $new_status ) && 'cho-duyet' !== $old_status ) {
			// Check if order uses ViettelPost shipping
			$shipping_methods = $order->get_shipping_methods();
			$uses_viettelpost = false;

			foreach ( $shipping_methods as $shipping_method ) {
				if ( strpos( $shipping_method->get_method_id(), 'viettelpost' ) !== false ) {
					$uses_viettelpost = true;
					break;
				}
			}

			if ( $uses_viettelpost ) {
				// Remove this hook temporarily to avoid infinite loop
				remove_action( 'woocommerce_order_status_changed', array( $this, 'on_status_change' ), 10 );
				$order->update_status( 'cho-duyet', __( 'Đơn hàng chờ duyệt trước khi gửi lên ViettelPost', 'woocommerce-viettelpost' ) );
				add_action( 'woocommerce_order_status_changed', array( $this, 'on_status_change' ), 10, 4 );
				return;
			}
		}

		// When admin approves (changes from cho-duyet to processing), send to ViettelPost
		if ( 'cho-duyet' === $old_status && 'processing' === $new_status ) {
			$this->send_order_to_viettelpost( $order_id, $order );
		}
	}

	/**
	 * Send order to ViettelPost
	 *
	 * @param int    $order_id Order ID.
	 * @param object $order Order object.
	 */
	private function send_order_to_viettelpost( $order_id, $order ) {
		// Check if already sent
		$already_sent = $order->get_meta( '_viettelpost_order_id' );
		if ( $already_sent ) {
			return;
		}

		$api = new WC_ViettelPost_API();

		// Prepare order data
		$shipping_address = $order->get_shipping_address_1();
		$shipping_city = $order->get_shipping_city();
		$shipping_state = $order->get_shipping_state();
		$shipping_postcode = $order->get_shipping_postcode();
		$shipping_country = $order->get_shipping_country();

		// Get sender info
		$sender_name = get_option( 'wc_viettelpost_sender_name', '' );
		$sender_phone = get_option( 'wc_viettelpost_sender_phone', '' );
		$sender_address = get_option( 'wc_viettelpost_sender_address', '' );
		$sender_province = get_option( 'wc_viettelpost_sender_province', '' );
		$sender_district = get_option( 'wc_viettelpost_sender_district', '' );
		$sender_ward = get_option( 'wc_viettelpost_sender_ward', '' );

		// Get receiver info
		$receiver_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
		$receiver_phone = $order->get_billing_phone();
		$receiver_address = $shipping_address;
		$receiver_province = $shipping_state;
		$receiver_district = $shipping_city;
		$receiver_ward = $order->get_meta( '_shipping_address_2' );

		// Get order items
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			$items[] = array(
				'PRODUCT_NAME' => $item->get_name(),
				'PRODUCT_QUANTITY' => $item->get_quantity(),
				'PRODUCT_PRICE' => $item->get_total(),
				'PRODUCT_WEIGHT' => $product ? wc_get_weight( $product->get_weight(), 'g', get_option( 'woocommerce_weight_unit' ) ) : 0,
			);
		}

		$order_data = array(
			'ORDER_NUMBER' => $order->get_order_number(),
			'GROUPADDRESS_ID' => '', // Optional
			'CUS_ID' => '', // Optional
			'SENDER_FULLNAME' => $sender_name,
			'SENDER_ADDRESS' => $sender_address,
			'SENDER_PHONE' => $sender_phone,
			'SENDER_EMAIL' => get_option( 'admin_email' ),
			'SENDER_WARD' => $sender_ward,
			'SENDER_DISTRICT' => $sender_district,
			'SENDER_PROVINCE' => $sender_province,
			'RECEIVER_FULLNAME' => $receiver_name,
			'RECEIVER_ADDRESS' => $receiver_address,
			'RECEIVER_PHONE' => $receiver_phone,
			'RECEIVER_EMAIL' => $order->get_billing_email(),
			'RECEIVER_WARD' => $receiver_ward,
			'RECEIVER_DISTRICT' => $receiver_district,
			'RECEIVER_PROVINCE' => $receiver_province,
			'PRODUCT_TYPE' => 'HH',
			'PRODUCT_WEIGHT' => max( 1, round( wc_get_weight( $order->get_total_weight(), 'g', get_option( 'woocommerce_weight_unit' ) ) ) ),
			'PRODUCT_PRICE' => round( $order->get_total() - $order->get_shipping_total() ),
			'MONEY_COLLECTION' => round( $order->get_total() ),
			'NOTE' => sprintf( __( 'Đơn hàng #%s từ %s', 'woocommerce-viettelpost' ), $order->get_order_number(), get_bloginfo( 'name' ) ),
			'LIST_ITEM' => $items,
		);

		$result = $api->create_order( $order_data );

		if ( is_wp_error( $result ) ) {
			$order->add_order_note( sprintf( __( 'Lỗi khi gửi đơn hàng lên ViettelPost: %s', 'woocommerce-viettelpost' ), $result->get_error_message() ) );
			return;
		}

		// Save ViettelPost order ID
		if ( isset( $result['ORDER_NUMBER'] ) ) {
			$order->update_meta_data( '_viettelpost_order_id', $result['ORDER_NUMBER'] );
			$order->update_meta_data( '_viettelpost_order_data', $result );
			$order->save();

			$order->add_order_note( sprintf( __( 'Đơn hàng đã được gửi lên ViettelPost. Mã đơn: %s', 'woocommerce-viettelpost' ), $result['ORDER_NUMBER'] ) );
		}
	}
}

