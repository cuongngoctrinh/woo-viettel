<?php
/**
 * ViettelPost API Handler
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_API class
 */
class WC_ViettelPost_API {
	/**
	 * API base URL
	 *
	 * @var string
	 */
	private $api_base_url = 'https://partner.viettelpost.vn/v2/';

	/**
	 * API username
	 *
	 * @var string
	 */
	private $username;

	/**
	 * API password
	 *
	 * @var string
	 */
	private $password;

	/**
	 * API token
	 *
	 * @var string
	 */
	private $token = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->username = get_option( 'wc_viettelpost_username', '' );
		$this->password = get_option( 'wc_viettelpost_password', '' );
		$this->api_base_url = get_option( 'wc_viettelpost_api_url', 'https://partner.viettelpost.vn/v2/' );
	}

	/**
	 * Get API token
	 *
	 * @return string|WP_Error
	 */
	public function get_token() {
		if ( ! empty( $this->token ) ) {
			return $this->token;
		}

		$cached_token = get_transient( 'wc_viettelpost_token' );
		if ( $cached_token ) {
			$this->token = $cached_token;
			return $this->token;
		}

		$response = wp_remote_post(
			$this->api_base_url . 'user/Login',
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
				'body' => wp_json_encode(
					array(
						'USERNAME' => $this->username,
						'PASSWORD' => $this->password,
					)
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['status'] ) && 200 === $data['status'] && isset( $data['data']['token'] ) ) {
			$this->token = $data['data']['token'];
			// Cache token for 1 hour
			set_transient( 'wc_viettelpost_token', $this->token, HOUR_IN_SECONDS );
			return $this->token;
		}

		return new WP_Error( 'api_error', __( 'Không thể lấy token từ ViettelPost API', 'woocommerce-viettelpost' ) );
	}

	/**
	 * Calculate shipping fee
	 *
	 * @param array $params Shipping parameters.
	 * @return array|WP_Error
	 */
	public function calculate_shipping( $params ) {
		// Debug: Log params being sent
		error_log( '=== ViettelPost API Debug: calculate_shipping START ===' );
		error_log( 'API URL: ' . $this->api_base_url . 'order/getPrice' );
		error_log( 'Params sent: ' . print_r( $params, true ) );
		
		$token = $this->get_token();
		if ( is_wp_error( $token ) ) {
			error_log( 'ERROR: Token failed - ' . $token->get_error_message() );
			return $token;
		}
		
		error_log( 'Token: ' . substr( $token, 0, 20 ) . '...' );

		$response = wp_remote_post(
			$this->api_base_url . 'order/getPrice',
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'Token' => $token,
				),
				'body' => wp_json_encode( $params ),
				'timeout' => 30,
			)
		);
		
		// Debug: Log raw response
		error_log( '=== Raw Response ===' );
		error_log( 'Response code: ' . wp_remote_retrieve_response_code( $response ) );
		error_log( 'Response message: ' . wp_remote_retrieve_response_message( $response ) );
		
		if ( is_wp_error( $response ) ) {
			error_log( 'ERROR: wp_remote_post failed - ' . $response->get_error_message() );
			error_log( '=== ViettelPost API Debug: END (WP_Error) ===' );
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		error_log( 'Response body: ' . $body );
		
		$data = json_decode( $body, true );
		
		// Debug: Log parsed data
		error_log( '=== Parsed Data ===' );
		error_log( print_r( $data, true ) );
		
		if ( isset( $data['status'] ) && 200 === $data['status'] ) {
			// API getPrice returns data directly in data field
			$result = isset( $data['data'] ) ? $data['data'] : $data;
			error_log( 'SUCCESS: Returning data' );
			error_log( 'Result: ' . print_r( $result, true ) );
			error_log( '=== ViettelPost API Debug: END (Success) ===' );
			return $result;
		}

		// Debug: Log error
		$error_message = isset( $data['message'] ) ? $data['message'] : __( 'Lỗi khi tính phí ship', 'woocommerce-viettelpost' );
		error_log( 'ERROR: API returned error - Status: ' . ( isset( $data['status'] ) ? $data['status'] : 'N/A' ) . ', Message: ' . $error_message );
		error_log( '=== ViettelPost API Debug: END (Error) ===' );
		
		return new WP_Error( 'api_error', $error_message );
	}

	/**
	 * Create order on ViettelPost
	 *
	 * @param array $order_data Order data.
	 * @return array|WP_Error
	 */
	public function create_order( $order_data ) {
		$token = $this->get_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_post(
			$this->api_base_url . 'order/createOrder',
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'Token' => $token,
				),
				'body' => wp_json_encode( $order_data ),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['status'] ) && 200 === $data['status'] ) {
			return $data['data'];
		}

		return new WP_Error( 'api_error', isset( $data['message'] ) ? $data['message'] : __( 'Lỗi khi tạo đơn hàng trên ViettelPost', 'woocommerce-viettelpost' ) );
	}

	/**
	 * Get provinces
	 *
	 * @return array|WP_Error
	 */
	public function get_provinces() {
		$cached = get_transient( 'wc_viettelpost_provinces' );
		if ( $cached ) {
			return $cached;
		}

		$token = $this->get_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_get(
			$this->api_base_url . 'categories/listProvinceById?provinceId=-1',
			array(
				'headers' => array(
					'Token' => $token,
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['status'] ) && 200 === $data['status'] ) {
			// Cache for 24 hours
			set_transient( 'wc_viettelpost_provinces', $data['data'], DAY_IN_SECONDS );
			return $data['data'];
		}

		return new WP_Error( 'api_error', __( 'Không thể lấy danh sách tỉnh thành', 'woocommerce-viettelpost' ) );
	}

	/**
	 * Get districts by province
	 *
	 * @param int $province_id Province ID.
	 * @return array|WP_Error
	 */
	public function get_districts( $province_id ) {
		$cache_key = 'wc_viettelpost_districts_' . $province_id;
		$cached = get_transient( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$token = $this->get_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_get(
			$this->api_base_url . 'categories/listDistrict?provinceId=' . intval( $province_id ),
			array(
				'headers' => array(
					'Token' => $token,
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['status'] ) && 200 === $data['status'] ) {
			// Cache for 24 hours
			set_transient( $cache_key, $data['data'], DAY_IN_SECONDS );
			return $data['data'];
		}

		return new WP_Error( 'api_error', __( 'Không thể lấy danh sách quận/huyện', 'woocommerce-viettelpost' ) );
	}

	/**
	 * Get wards by district
	 *
	 * @param int $district_id District ID.
	 * @return array|WP_Error
	 */
	public function get_wards( $district_id ) {
		$cache_key = 'wc_viettelpost_wards_' . $district_id;
		$cached = get_transient( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$token = $this->get_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_get(
			$this->api_base_url . 'categories/listWards?districtId=' . intval( $district_id ),
			array(
				'headers' => array(
					'Token' => $token,
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['status'] ) && 200 === $data['status'] ) {
			// Cache for 24 hours
			set_transient( $cache_key, $data['data'], DAY_IN_SECONDS );
			return $data['data'];
		}

		return new WP_Error( 'api_error', __( 'Không thể lấy danh sách phường/xã', 'woocommerce-viettelpost' ) );
	}
}

