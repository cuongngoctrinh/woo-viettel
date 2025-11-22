<?php
/**
 * ViettelPost Shipping Method
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Shipping class
 */
class WC_ViettelPost_Shipping extends WC_Shipping_Method {
	/**
	 * Plugin instance
	 *
	 * @var WC_ViettelPost_Shipping
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return WC_ViettelPost_Shipping
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @param int $instance_id Shipping method instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'viettelpost';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'ViettelPost', 'woocommerce-viettelpost' );
		$this->method_description = __( 'Tính phí ship tự động với ViettelPost', 'woocommerce-viettelpost' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
		);

		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_ajax_viettelpost_calculate_shipping', array( $this, 'ajax_calculate_shipping' ) );
		add_action( 'wp_ajax_nopriv_viettelpost_calculate_shipping', array( $this, 'ajax_calculate_shipping' ) );
	}

	/**
	 * Initialize settings
	 */
	public function init() {
		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title', __( 'ViettelPost', 'woocommerce-viettelpost' ) );
		$this->enabled     = $this->get_option( 'enabled', 'yes' );
		$this->tax_status  = $this->get_option( 'tax_status', 'taxable' );
        // Load các setting mới
        $this->service_code = $this->get_option( 'service_code', 'VCN' ); 
        $this->enable_insurance = $this->get_option( 'enable_insurance', 'yes' );
	}

	/**
	 * Initialize form fields - ĐÃ BỔ SUNG CÁC TÙY CHỌN MỚI
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title' => array(
				'title'       => __( 'Tiêu đề', 'woocommerce-viettelpost' ),
				'type'        => 'text',
				'description' => __( 'Tiêu đề hiển thị cho khách hàng', 'woocommerce-viettelpost' ),
				'default'     => __( 'ViettelPost', 'woocommerce-viettelpost' ),
				'desc_tip'    => true,
			),
            'service_code' => array(
				'title'       => __( 'Loại dịch vụ', 'woocommerce-viettelpost' ),
				'type'        => 'select',
				'description' => __( 'Chọn loại dịch vụ ViettelPost mặc định để tính phí.', 'woocommerce-viettelpost' ),
				'default'     => 'VCN',
				'options'     => array(
                    'VCN'   => 'Tài liệu/Hàng hóa nhanh (VCN)',
                    'VTK'   => 'Tài liệu/Hàng hóa tiết kiệm (VTK)',
                    'VHT'   => 'Hỏa tốc (VHT)',
                    'VCBA'  => 'Chuyển phát Bay (VCBA)',
                    'V60'   => 'Dịch vụ 60h (V60)',
                ),
			),
            'enable_insurance' => array(
				'title'       => __( 'Khai giá (Bảo hiểm)', 'woocommerce-viettelpost' ),
				'type'        => 'select',
				'default'     => 'yes',
				'options'     => array(
                    'yes' => 'Có - Khai giá trị hàng hóa (Phí cao hơn)',
                    'no'  => 'Không - Chỉ tính phí vận chuyển',
                ),
                'description' => __( 'Nếu chọn "Có", hệ thống sẽ gửi giá trị đơn hàng lên ViettelPost để tính phí bảo hiểm.', 'woocommerce-viettelpost' ),
			),
			'tax_status' => array(
				'title'       => __( 'Trạng thái thuế', 'woocommerce-viettelpost' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Có thuế', 'woocommerce-viettelpost' ),
					'none'    => _x( 'Không có thuế', 'Tax status', 'woocommerce-viettelpost' ),
				),
			),
		);
	}

	/**
	 * Calculate shipping
	 *
	 * @param array $package Package array.
	 */
	public function calculate_shipping( $package = array() ) {
		// Lấy shipping cost từ session nếu có (để tránh gọi API nhiều lần)
		$shipping_cost = WC()->session->get( 'viettelpost_shipping_cost' );
        
        // Logic check session cũ... (giữ nguyên logic check địa chỉ để cache)
		$session_province = WC()->session->get( 'viettelpost_province_id' );
		$session_district = WC()->session->get( 'viettelpost_district_id' );
		$session_ward     = WC()->session->get( 'viettelpost_ward_id' );
        $session_service  = WC()->session->get( 'viettelpost_service_code' );

		$current_province = isset( $package['destination']['state'] ) ? intval( $package['destination']['state'] ) : 0;
		$current_district = isset( $package['destination']['city'] ) ? intval( $package['destination']['city'] ) : 0;
		$current_ward     = isset( $package['destination']['address_2'] ) ? intval( $package['destination']['address_2'] ) : 0;

		$use_session_cost = false;
		if ( $shipping_cost !== null && $shipping_cost > 0 ) {
			if ( $current_province == $session_province && 
                 $current_district == $session_district && 
                 $current_ward == $session_ward &&
                 $this->service_code == $session_service ) { // Check thêm service code
				$use_session_cost = true;
			}
		}

		if ( ! $use_session_cost ) {
			$shipping_cost = $this->calculate_shipping_cost( $package );
			
			if ( $shipping_cost !== false && $shipping_cost >= 0 ) {
				WC()->session->set( 'viettelpost_shipping_cost', $shipping_cost );
				if ( $current_province ) WC()->session->set( 'viettelpost_province_id', $current_province );
				if ( $current_district ) WC()->session->set( 'viettelpost_district_id', $current_district );
				if ( $current_ward ) WC()->session->set( 'viettelpost_ward_id', $current_ward );
                WC()->session->set( 'viettelpost_service_code', $this->service_code );
			}
		}

		if ( $shipping_cost !== false && $shipping_cost >= 0 ) {
			$rate = array(
				'id'      => $this->get_rate_id(),
				'label'   => $this->title,
				'cost'    => floatval( $shipping_cost ),
				'package' => $package,
			);
			$this->add_rate( $rate );
		}
	}

	/**
	 * Calculate shipping cost from package
	 *
	 * @param array $package Package array.
	 * @return float|false
	 */
	private function calculate_shipping_cost( $package ) {
		if ( empty( $package['destination'] ) ) return false;

		$destination = $package['destination'];
		$province = $destination['state'];
		$district = isset( $destination['city'] ) ? $destination['city'] : '';
		$ward     = isset( $destination['address_2'] ) ? $destination['address_2'] : '';

		if ( empty( $province ) || empty( $district ) ) return false; // Ít nhất phải có Tỉnh/Huyện

		// Tính weight và value
		$weight = 0;
		$value  = 0;
		foreach ( $package['contents'] as $item ) {
			if ( $item['data']->needs_shipping() ) {
				$item_weight = $item['data']->get_weight();
				if ( empty( $item_weight ) || $item_weight <= 0 ) {
					$item_weight = 0.5; // Mặc định 0.5kg nếu không set
				}
				$weight += $item_weight * $item['quantity'];
				$value  += $item['line_total'];
			}
		}
		if ( empty( $weight ) || $weight <= 0 ) $weight = 0.5;

		// Convert sang gram
		$weight_grams = wc_get_weight( $weight, 'g', get_option( 'woocommerce_weight_unit' ) );

		$api = new WC_ViettelPost_API();

		// Lấy thông tin người gửi
		$sender_province = get_option( 'wc_viettelpost_sender_province', '' );
		$sender_district = get_option( 'wc_viettelpost_sender_district', '' );
		
		if ( empty( $sender_province ) || empty( $sender_district ) ) return false;

        // Xử lý khai giá (Bảo hiểm)
        $product_price = ($this->enable_insurance === 'yes') ? round($value) : 0;

		$params = array(
			'PRODUCT_WEIGHT'    => max( 1, round( $weight_grams ) ),
			'PRODUCT_PRICE'     => $product_price,
			'MONEY_COLLECTION'  => 0, // Mặc định tính phí ship không kèm thu hộ (COD xử lý bởi cổng thanh toán khác hoặc logic riêng)
			'ORDER_SERVICE_ADD' => '',
			'ORDER_SERVICE'     => $this->service_code, // Dùng mã dịch vụ từ setting
			'SENDER_PROVINCE'   => $sender_province,
			'SENDER_DISTRICT'   => $sender_district,
			'RECEIVER_PROVINCE' => $province,
			'RECEIVER_DISTRICT' => $district,
			'PRODUCT_TYPE'      => 'HH',
			'NATIONAL_TYPE'     => 1,
		);

		$result = $api->calculate_shipping( $params );

		if ( is_wp_error( $result ) ) {
			// Log lỗi nhưng không hiển thị ra frontend để tránh làm khách sợ, chỉ return false để ẩn phương thức
			error_log( 'VTP Error: ' . $result->get_error_message() );
			return false;
		}

		// Trích xuất giá tiền
		if ( is_array( $result ) && isset( $result['MONEY_TOTAL'] ) ) {
			return floatval( $result['MONEY_TOTAL'] );
		}
        // Trường hợp API trả về array các dịch vụ (nếu không chỉ định service)
		if ( isset( $result[0]['MONEY_TOTAL'] ) ) {
			return floatval( $result[0]['MONEY_TOTAL'] );
		}

		return false;
	}

	/**
	 * AJAX handler (Giữ nguyên logic cũ nhưng gọi hàm calculate mới)
	 */
	public function ajax_calculate_shipping() {
        // Logic AJAX giữ nguyên như file gốc của bạn
        // ...
        if ( ! check_ajax_referer( 'viettelpost-calculate', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật', 'woocommerce-viettelpost' ) ) );
			return;
		}
        // (Phần còn lại của hàm ajax giữ nguyên, nó sẽ gọi calculate_shipping_cost ở trên)
        // ... (Bạn hãy giữ lại phần code xử lý _POST trong file gốc)
	}
}