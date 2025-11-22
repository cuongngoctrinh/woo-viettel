<?php
/**
 * Custom Checkout Handler
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Checkout class
 */
class WC_ViettelPost_Checkout {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost_Checkout
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return WC_ViettelPost_Checkout
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
		// Override default address fields first
		add_filter( 'woocommerce_default_address_fields', array( $this, 'override_default_address_fields' ), 99999, 1 );
		
		// Override checkout fields completely
		add_filter( 'woocommerce_checkout_fields', array( $this, 'override_checkout_fields' ), 99999, 1 );
		
		// Override locale for Vietnam
		add_filter( 'woocommerce_get_country_locale', array( $this, 'override_vietnam_locale' ), 99999, 1 );
		
		// Set default country
		add_filter( 'default_checkout_country', array( $this, 'set_default_country' ) );
		add_filter( 'default_checkout_billing_country', array( $this, 'set_default_country' ) );
		add_filter( 'default_checkout_shipping_country', array( $this, 'set_default_country' ) );
		
		// Force country to VN
		add_action( 'woocommerce_checkout_init', array( $this, 'force_vietnam_country' ), 1 );
		add_action( 'woocommerce_before_checkout_process', array( $this, 'force_vietnam_country' ), 1 );
		add_action( 'woocommerce_checkout_process', array( $this, 'force_vietnam_country' ), 1 );
		
		// Add custom scripts
		add_action( 'wp_footer', array( $this, 'add_checkout_override_script' ), 999 );
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
	 * Force Vietnam country
	 */
	public function force_vietnam_country() {
		if ( WC()->customer ) {
			WC()->customer->set_billing_country( 'VN' );
			WC()->customer->set_shipping_country( 'VN' );
		}
		if ( isset( $_POST['billing_country'] ) ) {
			$_POST['billing_country'] = 'VN';
		}
		if ( isset( $_POST['shipping_country'] ) ) {
			$_POST['shipping_country'] = 'VN';
		}
	}

	/**
	 * Override default address fields
	 *
	 * @param array $fields Default address fields.
	 * @return array
	 */
	public function override_default_address_fields( $fields ) {
		// Hide country field
		if ( isset( $fields['country'] ) ) {
			$fields['country']['type'] = 'hidden';
			$fields['country']['default'] = 'VN';
			$fields['country']['value'] = 'VN';
			$fields['country']['required'] = false;
			$fields['country']['validate'] = array();
		}

		// State field - Province (Select) - Priority 70 (First)
		if ( isset( $fields['state'] ) ) {
			$fields['state']['type'] = 'select';
			$fields['state']['label'] = __( 'Tỉnh / Thành phố', 'woocommerce-viettelpost' );
			$fields['state']['placeholder'] = __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' );
			$fields['state']['options'] = array( '' => __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' ) );
			$fields['state']['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-province' );
			$fields['state']['input_class'] = array( 'viettelpost-province-select' );
			$fields['state']['validate'] = array();
			$fields['state']['required'] = true;
			$fields['state']['priority'] = 70;
		}

		// City field - District (Select) - Priority 80 (Second)
		if ( isset( $fields['city'] ) ) {
			$fields['city']['type'] = 'select';
			$fields['city']['label'] = __( 'Quận / Huyện', 'woocommerce-viettelpost' );
			$fields['city']['placeholder'] = __( 'Chọn quận/huyện', 'woocommerce-viettelpost' );
			$fields['city']['options'] = array( '' => __( 'Chọn quận/huyện', 'woocommerce-viettelpost' ) );
			$fields['city']['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-district' );
			$fields['city']['input_class'] = array( 'viettelpost-district-select' );
			$fields['city']['validate'] = array();
			$fields['city']['required'] = true;
			$fields['city']['priority'] = 80;
		}

		// Address 2 field - Ward (Select) - Priority 90 (Third)
		if ( isset( $fields['address_2'] ) ) {
			$fields['address_2']['type'] = 'select';
			$fields['address_2']['label'] = __( 'Phường / Xã', 'woocommerce-viettelpost' );
			$fields['address_2']['label_class'] = array();
			$fields['address_2']['placeholder'] = __( 'Chọn phường/xã', 'woocommerce-viettelpost' );
			$fields['address_2']['options'] = array( '' => __( 'Chọn phường/xã', 'woocommerce-viettelpost' ) );
			$fields['address_2']['required'] = true;
			$fields['address_2']['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change', 'viettelpost-ward' );
			$fields['address_2']['input_class'] = array( 'viettelpost-ward-select' );
			$fields['address_2']['validate'] = array();
			$fields['address_2']['priority'] = 90;
		}

		// Address 1 label - Priority 100 (Fourth - After ward)
		if ( isset( $fields['address_1'] ) ) {
			$fields['address_1']['label'] = __( 'Địa chỉ chi tiết', 'woocommerce-viettelpost' );
			$fields['address_1']['placeholder'] = __( 'Số nhà, tên đường', 'woocommerce-viettelpost' );
			$fields['address_1']['priority'] = 100;
		}

		// Hide postcode field
		if ( isset( $fields['postcode'] ) ) {
			$fields['postcode']['required'] = false;
			$fields['postcode']['class'] = array( 'form-row-wide', 'address-field', 'hidden' );
			$fields['postcode']['type'] = 'hidden';
		}

		return $fields;
	}

	/**
	 * Override checkout fields completely
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function override_checkout_fields( $fields ) {
		// Ensure country is hidden and set to VN
		if ( isset( $fields['billing']['billing_country'] ) ) {
			$fields['billing']['billing_country']['type'] = 'hidden';
			$fields['billing']['billing_country']['default'] = 'VN';
			$fields['billing']['billing_country']['value'] = 'VN';
			$fields['billing']['billing_country']['required'] = false;
		}

		if ( isset( $fields['shipping']['shipping_country'] ) ) {
			$fields['shipping']['shipping_country']['type'] = 'hidden';
			$fields['shipping']['shipping_country']['default'] = 'VN';
			$fields['shipping']['shipping_country']['value'] = 'VN';
			$fields['shipping']['shipping_country']['required'] = false;
		}

		// Ensure state, city, address_2 are select
		if ( isset( $fields['billing']['billing_state'] ) ) {
			$fields['billing']['billing_state']['type'] = 'select';
			if ( ! isset( $fields['billing']['billing_state']['options'] ) || empty( $fields['billing']['billing_state']['options'] ) ) {
				$fields['billing']['billing_state']['options'] = array( '' => __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' ) );
			}
		}

		if ( isset( $fields['billing']['billing_city'] ) ) {
			$fields['billing']['billing_city']['type'] = 'select';
			if ( ! isset( $fields['billing']['billing_city']['options'] ) || empty( $fields['billing']['billing_city']['options'] ) ) {
				$fields['billing']['billing_city']['options'] = array( '' => __( 'Chọn quận/huyện', 'woocommerce-viettelpost' ) );
			}
		}

		if ( isset( $fields['billing']['billing_address_2'] ) ) {
			$fields['billing']['billing_address_2']['type'] = 'select';
			if ( ! isset( $fields['billing']['billing_address_2']['options'] ) || empty( $fields['billing']['billing_address_2']['options'] ) ) {
				$fields['billing']['billing_address_2']['options'] = array( '' => __( 'Chọn phường/xã', 'woocommerce-viettelpost' ) );
			}
		}

		// Same for shipping
		if ( isset( $fields['shipping']['shipping_state'] ) ) {
			$fields['shipping']['shipping_state']['type'] = 'select';
			if ( ! isset( $fields['shipping']['shipping_state']['options'] ) || empty( $fields['shipping']['shipping_state']['options'] ) ) {
				$fields['shipping']['shipping_state']['options'] = array( '' => __( 'Chọn tỉnh/thành phố', 'woocommerce-viettelpost' ) );
			}
		}

		if ( isset( $fields['shipping']['shipping_city'] ) ) {
			$fields['shipping']['shipping_city']['type'] = 'select';
			if ( ! isset( $fields['shipping']['shipping_city']['options'] ) || empty( $fields['shipping']['shipping_city']['options'] ) ) {
				$fields['shipping']['shipping_city']['options'] = array( '' => __( 'Chọn quận/huyện', 'woocommerce-viettelpost' ) );
			}
		}

		if ( isset( $fields['shipping']['shipping_address_2'] ) ) {
			$fields['shipping']['shipping_address_2']['type'] = 'select';
			if ( ! isset( $fields['shipping']['shipping_address_2']['options'] ) || empty( $fields['shipping']['shipping_address_2']['options'] ) ) {
				$fields['shipping']['shipping_address_2']['options'] = array( '' => __( 'Chọn phường/xã', 'woocommerce-viettelpost' ) );
			}
		}

		// Hide postcode for shipping
		if ( isset( $fields['shipping']['shipping_postcode'] ) ) {
			$fields['shipping']['shipping_postcode']['required'] = false;
			$fields['shipping']['shipping_postcode']['class'] = array( 'form-row-wide', 'address-field', 'hidden' );
			$fields['shipping']['shipping_postcode']['type'] = 'hidden';
		}

		// Hide postcode for billing
		if ( isset( $fields['billing']['billing_postcode'] ) ) {
			$fields['billing']['billing_postcode']['required'] = false;
			$fields['billing']['billing_postcode']['class'] = array( 'form-row-wide', 'address-field', 'hidden' );
			$fields['billing']['billing_postcode']['type'] = 'hidden';
		}

		return $fields;
	}

	/**
	 * Override Vietnam locale
	 *
	 * @param array $locale Country locale.
	 * @return array
	 */
	public function override_vietnam_locale( $locale ) {
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
	 * Add checkout override script
	 */
	public function add_checkout_override_script() {
		if ( ! is_checkout() ) {
			return;
		}
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Force hide country fields
			function hideCountryFields() {
				$('#billing_country_field, #shipping_country_field').hide();
				$('#billing_country, #shipping_country').val('VN').hide();
				$('.country-field').hide();
			}

			// Hide immediately
			hideCountryFields();

			// Hide after checkout update
			$(document.body).on('updated_checkout', function() {
				hideCountryFields();
				$('#billing_country, #shipping_country').val('VN');
			});

			// Ensure country is VN
			setInterval(function() {
				$('#billing_country, #shipping_country').val('VN');
			}, 500);
		});
		</script>
		<?php
	}
}

