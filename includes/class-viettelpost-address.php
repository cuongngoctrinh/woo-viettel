<?php
/**
 * Custom Address Fields for Vietnam
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Address class
 */
class WC_ViettelPost_Address {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost_Address
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return WC_ViettelPost_Address
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
		// Set default country to Vietnam
		add_filter( 'default_checkout_country', array( $this, 'set_default_country' ) );
		add_filter( 'default_checkout_billing_country', array( $this, 'set_default_country' ) );
		add_filter( 'default_checkout_shipping_country', array( $this, 'set_default_country' ) );

		// Modify checkout fields directly - priority cao nhất
		add_filter( 'woocommerce_checkout_fields', array( $this, 'modify_checkout_fields' ), 9999, 1 );

		// Update address field labels for Vietnam locale
		add_filter( 'woocommerce_get_country_locale', array( $this, 'update_vietnam_locale' ), 999, 1 );

		// Force set country to VN on checkout
		add_action( 'woocommerce_checkout_process', array( $this, 'force_vietnam_country' ), 1 );
		add_action( 'woocommerce_before_checkout_process', array( $this, 'force_vietnam_country' ), 1 );
		add_action( 'woocommerce_checkout_init', array( $this, 'force_vietnam_country' ), 1 );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'force_vietnam_country' ), 1 );
		
		// Set country before fields are initialized
		add_action( 'woocommerce_checkout_fields', array( $this, 'set_country_before_fields' ), 1 );

		// Save custom fields
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_custom_fields' ), 10, 2 );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_custom_fields_in_admin' ), 10, 1 );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'display_custom_fields_in_admin' ), 10, 1 );

		// AJAX handlers
		add_action( 'wp_ajax_viettelpost_get_provinces', array( $this, 'ajax_get_provinces' ) );
		add_action( 'wp_ajax_nopriv_viettelpost_get_provinces', array( $this, 'ajax_get_provinces' ) );
		add_action( 'wp_ajax_viettelpost_get_districts_frontend', array( $this, 'ajax_get_districts' ) );
		add_action( 'wp_ajax_nopriv_viettelpost_get_districts_frontend', array( $this, 'ajax_get_districts' ) );
		add_action( 'wp_ajax_viettelpost_get_wards_frontend', array( $this, 'ajax_get_wards' ) );
		add_action( 'wp_ajax_nopriv_viettelpost_get_wards_frontend', array( $this, 'ajax_get_wards' ) );
	}

	/**
	 * Set default country to Vietnam
	 *
	 * @param string $country Country code.
	 * @return string
	 */
	public function set_default_country( $country ) {
		return 'VN';
	}

	/**
	 * Set country before fields are initialized
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function set_country_before_fields( $fields ) {
		if ( WC()->customer ) {
			WC()->customer->set_billing_country( 'VN' );
			WC()->customer->set_shipping_country( 'VN' );
		}
		return $fields;
	}

	/**
	 * Modify checkout fields directly
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function modify_checkout_fields( $fields ) {
		// Modify billing fields
		if ( isset( $fields['billing'] ) ) {
			$fields['billing'] = $this->modify_address_fields( $fields['billing'], 'billing' );
		}

		// Modify shipping fields
		if ( isset( $fields['shipping'] ) ) {
			$fields['shipping'] = $this->modify_address_fields( $fields['shipping'], 'shipping' );
		}

		return $fields;
	}

	/**
	 * Modify address fields
	 *
	 * @param array  $fields Address fields.
	 * @param string $prefix Field prefix (billing_ or shipping_).
	 * @return array
	 */
	private function modify_address_fields( $fields, $prefix = '' ) {
		// Hide country field
		if ( isset( $fields[ $prefix . 'country' ] ) ) {
			$fields[ $prefix . 'country' ]['type'] = 'hidden';
			$fields[ $prefix . 'country' ]['default'] = 'VN';
			$fields[ $prefix . 'country' ]['value'] = 'VN';
			$fields[ $prefix . 'country' ]['class'] = array( 'hidden', 'viettelpost-hidden-country' );
			$fields[ $prefix . 'country' ]['required'] = false;
			$fields[ $prefix . 'country' ]['validate'] = array();
		}

		// Convert state to select dropdown for province
		if ( isset( $fields[ $prefix . 'state' ] ) ) {
			$fields[ $prefix . 'state' ]['type'] = 'select';
			$fields[ $prefix . 'state' ]['label'] = __( 'Tỉnh / Thành phố', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'state' ]['placeholder'] = __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'state' ]['options'] = array( '' => __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' ) );
			$fields[ $prefix . 'state' ]['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-province' );
			$fields[ $prefix . 'state' ]['input_class'] = array( 'viettelpost-province-select' );
			$fields[ $prefix . 'state' ]['validate'] = array();
			$fields[ $prefix . 'state' ]['required'] = true;
		}

		// Convert city to select dropdown for district
		if ( isset( $fields[ $prefix . 'city' ] ) ) {
			$fields[ $prefix . 'city' ]['type'] = 'select';
			$fields[ $prefix . 'city' ]['label'] = __( 'Quận / Huyện', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'city' ]['placeholder'] = __( 'Chọn quận/huyện', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'city' ]['options'] = array( '' => __( 'Chọn quận/huyện', 'woocommerce-viettelpost' ) );
			$fields[ $prefix . 'city' ]['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-district' );
			$fields[ $prefix . 'city' ]['input_class'] = array( 'viettelpost-district-select' );
			$fields[ $prefix . 'city' ]['validate'] = array();
			$fields[ $prefix . 'city' ]['required'] = true;
		}

		// Convert address_2 to select dropdown for ward
		if ( isset( $fields[ $prefix . 'address_2' ] ) ) {
			$fields[ $prefix . 'address_2' ]['type'] = 'select';
			$fields[ $prefix . 'address_2' ]['label'] = __( 'Phường / Xã', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'address_2' ]['label_class'] = array();
			$fields[ $prefix . 'address_2' ]['placeholder'] = __( 'Chọn phường/xã', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'address_2' ]['options'] = array( '' => __( 'Chọn phường/xã', 'woocommerce-viettelpost' ) );
			$fields[ $prefix . 'address_2' ]['required'] = true;
			$fields[ $prefix . 'address_2' ]['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-ward' );
			$fields[ $prefix . 'address_2' ]['input_class'] = array( 'viettelpost-ward-select' );
			$fields[ $prefix . 'address_2' ]['validate'] = array();
		}

		// Update address_1 label
		if ( isset( $fields[ $prefix . 'address_1' ] ) ) {
			$fields[ $prefix . 'address_1' ]['label'] = __( 'Địa chỉ chi tiết', 'woocommerce-viettelpost' );
			$fields[ $prefix . 'address_1' ]['placeholder'] = __( 'Số nhà, tên đường', 'woocommerce-viettelpost' );
		}

		return $fields;
	}

	/**
	 * Force Vietnam country on checkout
	 */
	public function force_vietnam_country() {
		if ( isset( $_POST['billing_country'] ) ) {
			$_POST['billing_country'] = 'VN';
		}
		if ( isset( $_POST['shipping_country'] ) ) {
			$_POST['shipping_country'] = 'VN';
		}
		if ( WC()->customer ) {
			WC()->customer->set_billing_country( 'VN' );
			WC()->customer->set_shipping_country( 'VN' );
		}
	}


	/**
	 * Update Vietnam locale
	 *
	 * @param array $locale Country locale.
	 * @return array
	 */
	public function update_vietnam_locale( $locale ) {
		$locale['VN'] = array(
			'country' => array(
				'hidden' => true,
			),
			'state' => array(
				'required' => true,
				'label'    => __( 'Tỉnh / Thành phố', 'woocommerce-viettelpost' ),
				'type'     => 'select',
			),
			'city' => array(
				'required' => true,
				'label'    => __( 'Quận / Huyện', 'woocommerce-viettelpost' ),
				'type'     => 'select',
			),
			'address_2' => array(
				'required' => true,
				'label'    => __( 'Phường / Xã', 'woocommerce-viettelpost' ),
				'type'     => 'select',
				'label_class' => array(),
			),
			'postcode' => array(
				'required' => false,
			),
		);

		return $locale;
	}

	/**
	 * Save custom fields
	 *
	 * @param int   $order_id Order ID.
	 * @param array $posted Posted data.
	 */
	public function save_custom_fields( $order_id, $posted ) {
		// Fields are already saved by WooCommerce, we just need to ensure they're stored correctly
		// The province, district, ward are stored in state, city, address_2 respectively
	}

	/**
	 * Display custom fields in admin
	 *
	 * @param object $order Order object.
	 */
	public function display_custom_fields_in_admin( $order ) {
		$address_type = 'billing';
		if ( did_action( 'woocommerce_admin_order_data_after_shipping_address' ) ) {
			$address_type = 'shipping';
		}

		$province = $order->get_meta( '_' . $address_type . '_state' );
		$district = $order->get_meta( '_' . $address_type . '_city' );
		$ward = $order->get_meta( '_' . $address_type . '_address_2' );

		if ( $province || $district || $ward ) {
			?>
			<div class="address">
				<?php if ( $province ) : ?>
					<p><strong><?php esc_html_e( 'Tỉnh/Thành phố:', 'woocommerce-viettelpost' ); ?></strong> <?php echo esc_html( $province ); ?></p>
				<?php endif; ?>
				<?php if ( $district ) : ?>
					<p><strong><?php esc_html_e( 'Quận/Huyện:', 'woocommerce-viettelpost' ); ?></strong> <?php echo esc_html( $district ); ?></p>
				<?php endif; ?>
				<?php if ( $ward ) : ?>
					<p><strong><?php esc_html_e( 'Phường/Xã:', 'woocommerce-viettelpost' ); ?></strong> <?php echo esc_html( $ward ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * AJAX handler for getting provinces
	 */
	public function ajax_get_provinces() {
		if ( ! check_ajax_referer( 'viettelpost-address', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật', 'woocommerce-viettelpost' ) ) );
			return;
		}

		$api = new WC_ViettelPost_API();
		$provinces = $api->get_provinces();

		if ( is_wp_error( $provinces ) ) {
			wp_send_json_error( array( 'message' => $provinces->get_error_message() ) );
			return;
		}

		$options = array();
		if ( is_array( $provinces ) ) {
			foreach ( $provinces as $province ) {
				if ( isset( $province['PROVINCE_ID'] ) && isset( $province['PROVINCE_NAME'] ) ) {
					$options[ $province['PROVINCE_ID'] ] = $province['PROVINCE_NAME'];
				}
			}
		}

		wp_send_json_success( $options );
	}

	/**
	 * AJAX handler for getting districts
	 */
	public function ajax_get_districts() {
		if ( ! check_ajax_referer( 'viettelpost-address', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật', 'woocommerce-viettelpost' ) ) );
			return;
		}

		$province_id = isset( $_POST['province_id'] ) ? intval( $_POST['province_id'] ) : 0;
		if ( ! $province_id ) {
			wp_send_json_error( array( 'message' => __( 'Thiếu province_id', 'woocommerce-viettelpost' ) ) );
			return;
		}

		$api = new WC_ViettelPost_API();
		$districts = $api->get_districts( $province_id );

		if ( is_wp_error( $districts ) ) {
			wp_send_json_error( array( 'message' => $districts->get_error_message() ) );
			return;
		}

		$options = array();
		if ( is_array( $districts ) ) {
			foreach ( $districts as $district ) {
				if ( isset( $district['DISTRICT_ID'] ) && isset( $district['DISTRICT_NAME'] ) ) {
					$options[ $district['DISTRICT_ID'] ] = $district['DISTRICT_NAME'];
				}
			}
		}

		wp_send_json_success( $options );
	}

	/**
	 * AJAX handler for getting wards
	 */
	public function ajax_get_wards() {
		if ( ! check_ajax_referer( 'viettelpost-address', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật', 'woocommerce-viettelpost' ) ) );
			return;
		}

		$district_id = isset( $_POST['district_id'] ) ? intval( $_POST['district_id'] ) : 0;
		if ( ! $district_id ) {
			wp_send_json_error( array( 'message' => __( 'Thiếu district_id', 'woocommerce-viettelpost' ) ) );
			return;
		}

		$api = new WC_ViettelPost_API();
		$wards = $api->get_wards( $district_id );

		if ( is_wp_error( $wards ) ) {
			wp_send_json_error( array( 'message' => $wards->get_error_message() ) );
			return;
		}

		$options = array();
		if ( is_array( $wards ) ) {
			foreach ( $wards as $ward ) {
				if ( isset( $ward['WARDS_ID'] ) && isset( $ward['WARDS_NAME'] ) ) {
					$options[ $ward['WARDS_ID'] ] = $ward['WARDS_NAME'];
				}
			}
		}

		wp_send_json_success( $options );
	}
}

