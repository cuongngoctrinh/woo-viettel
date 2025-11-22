<?php
/**
 * Admin Interface for ViettelPost
 *
 * @package WooCommerce_ViettelPost
 */
 
defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Admin class
 */
class WC_ViettelPost_Admin {
    /**
     * Plugin instance
     *
     * @var WC_ViettelPost_Admin
     */
    private static $instance = null;
 
    /**
     * Get instance
     *
     * @return WC_ViettelPost_Admin
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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'display_viettelpost_info' ) );
        add_filter( 'woocommerce_order_actions', array( $this, 'add_order_actions' ), 10, 2 );
        add_action( 'woocommerce_order_action_approve_viettelpost', array( $this, 'approve_order' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_viettelpost_get_districts', array( $this, 'ajax_get_districts' ) );
        add_action( 'wp_ajax_viettelpost_get_wards', array( $this, 'ajax_get_wards' ) );
        add_action( 'wp_ajax_viettelpost_generate_products', array( $this, 'ajax_generate_products' ) );
        add_action( 'wp_ajax_viettelpost_delete_sample_products', array( $this, 'ajax_delete_sample_products' ) );

        // BỔ SUNG MỚI: META BOX + TẠO ĐƠN + CHỌN KHO + IN/HỦY
        add_action( 'add_meta_boxes', array( $this, 'add_viettelpost_meta_box' ) );
        add_action( 'wp_ajax_vtp_create_order', array( $this, 'ajax_create_order' ) );
        add_action( 'wp_ajax_vtp_cancel_order', array( $this, 'ajax_cancel_order' ) );
    }
 
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'ViettelPost Settings', 'woocommerce-viettelpost' ),
            __( 'ViettelPost', 'woocommerce-viettelpost' ),
            'manage_woocommerce',
            'wc-viettelpost-settings',
            array( $this, 'settings_page' )
        );
    }
 
    /**
     * Register settings
     */
    public function register_settings() {
        // API Settings
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_username' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_password' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_api_url' );
 
        // Sender Settings
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_name' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_phone' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_email' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_address' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_province' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_district' );
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_sender_ward' );

        // Bổ sung: Gửi tại bưu cục giảm phí
        register_setting( 'wc_viettelpost_settings', 'wc_viettelpost_send_at_post_office' );
    }
 
    /**
     * Settings page
     */
    public function settings_page() {
        if ( isset( $_POST['submit'] ) && check_admin_referer( 'wc_viettelpost_settings' ) ) {
            update_option( 'wc_viettelpost_username', sanitize_text_field( $_POST['wc_viettelpost_username'] ?? '' ) );
            update_option( 'wc_viettelpost_password', sanitize_text_field( $_POST['wc_viettelpost_password'] ?? '' ) );
            update_option( 'wc_viettelpost_api_url', esc_url_raw( $_POST['wc_viettelpost_api_url'] ?? 'https://partner.viettelpost.vn/v2/' ) );
 
            update_option( 'wc_viettelpost_sender_name', sanitize_text_field( $_POST['wc_viettelpost_sender_name'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_phone', sanitize_text_field( $_POST['wc_viettelpost_sender_phone'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_email', sanitize_email( $_POST['wc_viettelpost_sender_email'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_address', sanitize_text_field( $_POST['wc_viettelpost_sender_address'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_province', sanitize_text_field( $_POST['wc_viettelpost_sender_province'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_district', sanitize_text_field( $_POST['wc_viettelpost_sender_district'] ?? '' ) );
            update_option( 'wc_viettelpost_sender_ward', sanitize_text_field( $_POST['wc_viettelpost_sender_ward'] ?? '' ) );
            update_option( 'wc_viettelpost_send_at_post_office', isset($_POST['wc_viettelpost_send_at_post_office']) ? 'yes' : 'no' );

            echo '<div class="notice notice-success"><p>' . esc_html__( 'Cài đặt đã được lưu!', 'woocommerce-viettelpost' ) . '</p></div>';
        }
 
        $api = new WC_ViettelPost_API();
        $provinces = $api->get_provinces();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Cài đặt ViettelPost', 'woocommerce-viettelpost' ); ?></h1>
            <form method="post" action="">
                <?php wp_nonce_field( 'wc_viettelpost_settings' ); ?>
 
                <h2><?php esc_html_e( 'Thông tin API', 'woocommerce-viettelpost' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_username"><?php esc_html_e( 'Username', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wc_viettelpost_username" name="wc_viettelpost_username" value="<?php echo esc_attr( get_option( 'wc_viettelpost_username', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_password"><?php esc_html_e( 'Password', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="password" id="wc_viettelpost_password" name="wc_viettelpost_password" value="<?php echo esc_attr( get_option( 'wc_viettelpost_password', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_api_url"><?php esc_html_e( 'API URL', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="wc_viettelpost_api_url" name="wc_viettelpost_api_url" value="<?php echo esc_attr( get_option( 'wc_viettelpost_api_url', 'https://partner.viettelpost.vn/v2/' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'URL API của ViettelPost', 'woocommerce-viettelpost' ); ?></p>
                        </td>
                    </tr>
                </table>
 
                <h2><?php esc_html_e( 'Thông tin người gửi', 'woocommerce-viettelpost' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_name"><?php esc_html_e( 'Tên người gửi', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wc_viettelpost_sender_name" name="wc_viettelpost_sender_name" value="<?php echo esc_attr( get_option( 'wc_viettelpost_sender_name', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_phone"><?php esc_html_e( 'Số điện thoại', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wc_viettelpost_sender_phone" name="wc_viettelpost_sender_phone" value="<?php echo esc_attr( get_option( 'wc_viettelpost_sender_phone', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_email"><?php esc_html_e( 'Email', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="email" id="wc_viettelpost_sender_email" name="wc_viettelpost_sender_email" value="<?php echo esc_attr( get_option( 'wc_viettelpost_sender_email', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_address"><?php esc_html_e( 'Địa chỉ', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="wc_viettelpost_sender_address" name="wc_viettelpost_sender_address" value="<?php echo esc_attr( get_option( 'wc_viettelpost_sender_address', '' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_province"><?php esc_html_e( 'Tỉnh/Thành phố', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <select id="wc_viettelpost_sender_province" name="wc_viettelpost_sender_province" class="regular-text">
                                <option value=""><?php esc_html_e( '-- Chọn tỉnh/thành phố --', 'woocommerce-viettelpost' ); ?></option>
                                <?php
                                if ( ! is_wp_error( $provinces ) && is_array( $provinces ) ) {
                                    $selected_province = get_option( 'wc_viettelpost_sender_province', '' );
                                    foreach ( $provinces as $province ) {
                                        $province_id = isset( $province['PROVINCE_ID'] ) ? $province['PROVINCE_ID'] : '';
                                        $province_name = isset( $province['PROVINCE_NAME'] ) ? $province['PROVINCE_NAME'] : '';
                                        ?>
                                        <option value="<?php echo esc_attr( $province_id ); ?>" <?php selected( $selected_province, $province_id ); ?>>
                                            <?php echo esc_html( $province_name ); ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_district"><?php esc_html_e( 'Quận/Huyện', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <select id="wc_viettelpost_sender_district" name="wc_viettelpost_sender_district" class="regular-text">
                                <option value=""><?php esc_html_e( '-- Chọn quận/huyện --', 'woocommerce-viettelpost' ); ?></option>
                                <?php
                                $selected_province = get_option( 'wc_viettelpost_sender_province', '' );
                                if ( $selected_province ) {
                                    $districts = $api->get_districts( $selected_province );
                                    if ( ! is_wp_error( $districts ) && is_array( $districts ) ) {
                                        $selected_district = get_option( 'wc_viettelpost_sender_district', '' );
                                        foreach ( $districts as $district ) {
                                            $district_id = isset( $district['DISTRICT_ID'] ) ? $district['DISTRICT_ID'] : '';
                                            $district_name = isset( $district['DISTRICT_NAME'] ) ? $district['DISTRICT_NAME'] : '';
                                            ?>
                                            <option value="<?php echo esc_attr( $district_id ); ?>" <?php selected( $selected_district, $district_id ); ?>>
                                                <?php echo esc_html( $district_name ); ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_sender_ward"><?php esc_html_e( 'Phường/Xã', 'woocommerce-viettelpost' ); ?></label>
                        </th>
                        <td>
                            <select id="wc_viettelpost_sender_ward" name="wc_viettelpost_sender_ward" class="regular-text">
                                <option value=""><?php esc_html_e( '-- Chọn phường/xã --', 'woocommerce-viettelpost' ); ?></option>
                                <?php
                                $selected_district = get_option( 'wc_viettelpost_sender_district', '' );
                                if ( $selected_district ) {
                                    $wards = $api->get_wards( $selected_district );
                                    if ( ! is_wp_error( $wards ) && is_array( $wards ) ) {
                                        $selected_ward = get_option( 'wc_viettelpost_sender_ward', '' );
                                        foreach ( $wards as $ward ) {
                                            $ward_id = isset( $ward['WARDS_ID'] ) ? $ward['WARDS_ID'] : '';
                                            $ward_name = isset( $ward['WARDS_NAME'] ) ? $ward['WARDS_NAME'] : '';
                                            ?>
                                            <option value="<?php echo esc_attr( $ward_id ); ?>" <?php selected( $selected_ward, $ward_id ); ?>>
                                                <?php echo esc_html( $ward_name ); ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="wc_viettelpost_send_at_post_office">Gửi tại bưu cục (giảm phí)</label>
                        </th>
                        <td>
                            <input type="checkbox" id="wc_viettelpost_send_at_post_office" name="wc_viettelpost_send_at_post_office" value="1" <?php checked(get_option('wc_viettelpost_send_at_post_office'), 'yes'); ?> />
                            <p class="description">Khách được gửi tại bưu cục → giảm phí ship</p>
                        </td>
                    </tr>
                </table>
 
                <?php submit_button(); ?>
            </form>
 
            <hr>
 
            <h2><?php esc_html_e( 'Sản phẩm mẫu', 'woocommerce-viettelpost' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Tạo sản phẩm mẫu để test plugin ViettelPost. Các sản phẩm sẽ có đầy đủ thông tin: tên, giá, mô tả, trọng lượng, hình ảnh.', 'woocommerce-viettelpost' ); ?>
            </p>
            <p>
                <button type="button" class="button button-primary" id="generate-sample-products">
                    <?php esc_html_e( 'Tạo 10 sản phẩm mẫu', 'woocommerce-viettelpost' ); ?>
                </button>
                <button type="button" class="button button-secondary" id="delete-sample-products">
                    <?php esc_html_e( 'Xóa sản phẩm mẫu', 'woocommerce-viettelpost' ); ?>
                </button>
            </p>
            <div id="sample-products-result" style="margin-top: 10px;"></div>
        </div>
        <?php
    }
 
    /**
     * Display ViettelPost info in order
     *
     * @param object $order Order object.
     */
    public function display_viettelpost_info( $order ) {
        $viettelpost_order_id = $order->get_meta( '_vtp_order_number' );
        if ( $viettelpost_order_id ) {
            ?>
            <div class="address">
                <p>
                    <strong><?php esc_html_e( 'Mã đơn ViettelPost:', 'woocommerce-viettelpost' ); ?></strong>
                    <?php echo esc_html( $viettelpost_order_id ); ?>
                </p>
            </div>
            <?php
        }
    }
 
    /**
     * Add order actions
     *
     * @param array    $actions Order actions.
     * @param WC_Order $order Order object.
     * @return array
     */
    public function add_order_actions( $actions, $order ) {
        if ( 'cho-duyet' === $order->get_status() ) {
            $actions['approve_viettelpost'] = __( 'Duyệt và gửi lên ViettelPost', 'woocommerce-viettelpost' );
        }
        return $actions;
    }
 
    /**
     * Approve order and send to ViettelPost
     *
     * @param WC_Order $order Order object.
     */
    public function approve_order( $order ) {
        $order->update_status( 'processing', __( 'Đơn hàng đã được duyệt và gửi lên ViettelPost', 'woocommerce-viettelpost' ) );
    }
 
    /**
     * Enqueue admin scripts
     *
     * @param string $hook Current page hook.
     */
    public function enqueue_scripts( $hook ) {
        if ( 'woocommerce_page_wc-viettelpost-settings' !== $hook ) {
            return;
        }
 
        wp_enqueue_script( 'jquery' );
        wp_add_inline_script(
            'jquery',
            "
            jQuery(document).ready(function($) {
                $('#wc_viettelpost_sender_province').on('change', function() {
                    var provinceId = $(this).val();
                    if (provinceId) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'viettelpost_get_districts',
                                province_id: provinceId,
                                nonce: '" . wp_create_nonce( 'viettelpost-ajax' ) . "'
                            },
                            success: function(response) {
                                if (response.success) {
                                    var options = '<option value=\"\">-- Chọn quận/huyện --</option>';
                                    $.each(response.data, function(i, district) {
                                        options += '<option value=\"' + district.DISTRICT_ID + '\">' + district.DISTRICT_NAME + '</option>';
                                    });
                                    $('#wc_viettelpost_sender_district').html(options);
                                    $('#wc_viettelpost_sender_ward').html('<option value=\"\">-- Chọn phường/xã --</option>');
                                }
                            }
                        });
                    }
                });
 
                $('#wc_viettelpost_sender_district').on('change', function() {
                    var districtId = $(this).val();
                    if (districtId) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'viettelpost_get_wards',
                                district_id: districtId,
                                nonce: '" . wp_create_nonce( 'viettelpost-ajax' ) . "'
                            },
                            success: function(response) {
                                if (response.success) {
                                    var options = '<option value=\"\">-- Chọn phường/xã --</option>';
                                    $.each(response.data, function(i, ward) {
                                        options += '<option value=\"' + ward.WARDS_ID + '\">' + ward.WARDS_NAME + '</option>';
                                    });
                                    $('#wc_viettelpost_sender_ward').html(options);
                                }
                            }
                        });
                    }
                });
 
                jQuery('#generate-sample-products').on('click', function() {
                    var buttonEl = jQuery(this);
                    var resultEl = jQuery('#sample-products-result');
 
                    buttonEl.prop('disabled', true).text('Đang tạo...');
                    resultEl.html('<div class=\"notice notice-info\"><p>Đang tạo sản phẩm mẫu...</p></div>');
 
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'viettelpost_generate_products',
                            nonce: '" . esc_js( wp_create_nonce( 'viettelpost-ajax' ) ) . "'
                        },
                        success: function(response) {
                            buttonEl.prop('disabled', false).text('Tạo 10 sản phẩm mẫu');
 
                            if (response.success) {
                                var message = 'Đã tạo thành công ' + response.data.success + ' sản phẩm';
                                if (response.data.failed > 0) {
                                    message += ', thất bại ' + response.data.failed + ' sản phẩm';
                                }
 
                                var noticeClass = response.data.success > 0 ? 'notice-success' : 'notice-warning';
                                var html = '<div class=\"notice ' + noticeClass + '\"><p><strong>' + message + '</strong></p>';
 
                                if (response.data.errors && response.data.errors.length > 0) {
                                    html += '<ul style=\"margin: 5px 0 0 20px;\">';
                                    for (var i = 0; i < response.data.errors.length; i++) {
                                        html += '<li>' + response.data.errors[i] + '</li>';
                                    }
                                    html += '</ul>';
                                }
 
                                html += '</div>';
                                resultEl.html(html);
                            } else {
                                resultEl.html('<div class 

                                notice notice-error\"><p>' + (response.data.message || 'Có lỗi xảy ra') + '</p></div>');
                            }
                        },
                        error: function() {
                            buttonEl.prop('disabled', false).text('Tạo 10 sản phẩm mẫu');
                            resultEl.html('<div class=\"notice notice-error\"><p>Lỗi khi tạo sản phẩm</p></div>');
                        }
                    });
                });
 
                jQuery('#delete-sample-products').on('click', function() {
                    if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm mẫu?')) {
                        return;
                    }
 
                    var buttonEl = jQuery(this);
                    var resultEl = jQuery('#sample-products-result');
 
                    buttonEl.prop('disabled', true).text('Đang xóa...');
                    resultEl.html('<div class=\"notice notice-info\"><p>Đang xóa sản phẩm mẫu...</p></div>');
 
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'viettelpost_delete_sample_products',
                            nonce: '" . esc_js( wp_create_nonce( 'viettelpost-ajax' ) ) . "'
                        },
                        success: function(response) {
                            buttonEl.prop('disabled', false).text('Xóa sản phẩm mẫu');
 
                            if (response.success) {
                                resultEl.html('<div class=\"notice notice-success\"><p>Đã xóa ' + response.data.deleted + ' sản phẩm</p></div>');
                            } else {
                                resultEl.html('<div class=\"notice notice-error\"><p>' + (response.data.message || 'Có lỗi xảy ra') + '</p></div>');
                            }
                        },
                        error: function() {
                            buttonEl.prop('disabled', false).text('Xóa sản phẩm mẫu');
                            resultEl.html('<div class=\"notice notice-error\"><p>Lỗi khi xóa sản phẩm</p></div>');
                        }
                    });
                });
            });
            "
        );
    }
 
    /**
     * AJAX handler for getting districts
     */
    public function ajax_get_districts() {
        check_ajax_referer( 'viettelpost-ajax', 'nonce' );
 
        $province_id = isset( $_POST['province_id'] ) ? intval( $_POST['province_id'] ) : 0;
        if ( ! $province_id ) {
            wp_send_json_error( array( 'message' => __( 'Thiếu province_id', 'woocommerce-viettelpost' ) ) );
        }
 
        $api = new WC_ViettelPost_API();
        $districts = $api->get_districts( $province_id );
 
        if ( is_wp_error( $districts ) ) {
            wp_send_json_error( array( 'message' => $districts->get_error_message() ) );
        }
 
        wp_send_json_success( $districts );
    }
 
    /**
     * AJAX handler for getting wards
     */
    public function ajax_get_wards() {
        check_ajax_referer( 'viettelpost-ajax', 'nonce' );
 
        $district_id = isset( $_POST['district_id'] ) ? intval( $_POST['district_id'] ) : 0;
        if ( ! $district_id ) {
            wp_send_json_error( array( 'message' => __( 'Thiếu district_id', 'woocommerce-viettelpost' ) ) );
        }
 
        $api = new WC_ViettelPost_API();
        $wards = $api->get_wards( $district_id );
 
        if ( is_wp_error( $wards ) ) {
            wp_send_json_error( array( 'message' => $wards->get_error_message() ) );
        }
 
        wp_send_json_success( $wards );
    }
 
    /**
     * AJAX handler for generating sample products
     */
    public function ajax_generate_products() {
        check_ajax_referer( 'viettelpost-ajax', 'nonce' );
 
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Bạn không có quyền thực hiện thao tác này', 'woocommerce-viettelpost' ) ) );
        }
 
        require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-sample-products.php';
 
        $generator = new WC_ViettelPost_Sample_Products();
        $results = $generator->generate_products();
 
        wp_send_json_success( $results );
    }
 
    /**
     * AJAX handler for deleting sample products
     */
    public function ajax_delete_sample_products() {
        check_ajax_referer( 'viettelpost-ajax', 'nonce' );
 
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Bạn không có quyền thực hiện thao tác này', 'woocommerce-viettelpost' ) ) );
        }
 
        require_once WC_VIETTELPOST_PLUGIN_DIR . 'includes/class-viettelpost-sample-products.php';
 
        $generator = new WC_ViettelPost_Sample_Products();
        $results = $generator->delete_sample_products();
 
        wp_send_json_success( $results );
    }

    // BỔ SUNG MỚI: META BOX + TẠO ĐƠN + CHỌN KHO + IN/HỦY
    public function add_viettelpost_meta_box() {
        add_meta_box(
            'viettelpost_management',
            'ViettelPost - Quản lý vận đơn & Kho hàng',
            array( $this, 'render_viettelpost_meta_box' ),
            'shop_order',
            'side',
            'high'
        );
    }

    public function render_viettelpost_meta_box( $post ) {
        $order = wc_get_order( $post->ID );
        $vtp_order_number = $order->get_meta( '_vtp_order_number' );

        $api = new WC_ViettelPost_API();
        $token = $api->get_token(); // boss có method get_token

        $inventories = [];
        if ($token) {
            $response = wp_remote_get( 'https://partner.viettelpost.vn/v2/user/listInventory', [
                'headers' => [
                    'Token' => $token,
                    'Content-Type' => 'application/json'
                ],
                'timeout' => 30
            ]);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['data']) && is_array($body['data'])) {
                    $inventories = $body['data'];
                }
            }
        }

        if ( $vtp_order_number ) {
            echo '<div style="background:#d4edda;padding:15px;border-radius:8px;text-align:center;margin-bottom:15px;">';
            echo '<strong style="color:#155724;font-size:1.3em;">ĐÃ TẠO VẬN ĐƠN</strong><br>';
            echo '<span style="font-size:1.6em;font-weight:bold;color:#0d4f0d;">' . esc_html($vtp_order_number) . '</span>';
            echo '</div>';
            echo '<p style="text-align:center;"><a href="https://partner.viettelpost.vn/printing-code?code=' . esc_attr($vtp_order_number) . '" target="_blank" class="button button-primary button-large">In vận đơn A5</a></p>';
            echo '<p style="text-align:center;"><button type="button" class="button button-secondary" id="vtp-cancel-order">Hủy vận đơn</button></p>';
        } else {
            if ( !empty($inventories) ) {
                echo '<p><strong>Chọn kho gửi:</strong></p>';
                echo '<select id="vtp-inventory-select" style="width:100%;margin-bottom:10px;">';
                echo '<option value="">→ Tự động chọn kho gần khách</option>';
                foreach ( $inventories as $inv ) {
                    $name = $inv['NAME'] ?? 'Kho không tên';
                    $province = $inv['PROVINCE_NAME'] ?? '';
                    echo '<option value="' . esc_attr($inv['GROUPADDRESS_ID']) . '">' . esc_html("$name - $province") . '</option>';
                }
                echo '</select>';
            } else {
                echo '<p style="color:red;">Không lấy được danh sách kho (kiểm tra username/password)</p>';
            }
            echo '<button type="button" class="button button-primary button-large" style="width:100%;margin-top:10px;" id="vtp-create-order">Tạo vận đơn ViettelPost</button>';
            echo '<p style="font-size:11px;color:#666;margin-top:8px;text-align:center;">Tự động chọn kho cùng tỉnh khách</p>';
        }
        ?>
        <script>
        jQuery(function($) {
            $('#vtp-create-order').on('click', function() {
                var btn = $(this);
                btn.prop('disabled', true).text('Đang tạo...');
                $.post(ajaxurl, {
                    action: 'vtp_create_order',
                    order_id: <?php echo $order->get_id(); ?>,
                    inventory_id: $('#vtp-inventory-select').val() || '',
                    nonce: '<?php echo wp_create_nonce('vtp_create'); ?>'
                }, function(res) {
                    if (res.success) {
                        alert('Tạo vận đơn thành công! Mã: ' + res.data.order_number);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + (res.data || 'Không thể tạo đơn'));
                        btn.prop('disabled', false).text('Tạo vận đơn ViettelPost');
                    }
                });
            });

            $('#vtp-cancel-order').on('click', function() {
                if (!confirm('Hủy vận đơn này? Không thể hoàn tác!')) return;
                $.post(ajaxurl, {
                    action: 'vtp_cancel_order',
                    order_id: <?php echo $order->get_id(); ?>,
                    nonce: '<?php echo wp_create_nonce('vtp_cancel'); ?>'
                }, function(res) {
                    if (res.success) {
                        alert('Hủy thành công!');
                        location.reload();
                    } else {
                        alert('Lỗi hủy: ' + (res.data || 'Unknown'));
                    }
                });
            });
        });
        </script>
        <?php
    }

    public function ajax_create_order() {
        check_ajax_referer('vtp_create', 'nonce');
        $order_id = intval($_POST['order_id']);
        $manual_inventory_id = sanitize_text_field($_POST['inventory_id'] ?? '');

        $order = wc_get_order($order_id);
        if (!$order) wp_send_json_error('Không tìm thấy đơn');

        $api = new WC_ViettelPost_API();
        $token = $api->get_token();
        if (!$token) wp_send_json_error('Không lấy được token');

        $customer_province_id = $order->get_meta('_shipping_state') ?: $order->get_meta('_billing_state');

        $inventory = null;
        $response = wp_remote_get( 'https://partner.viettelpost.vn/v2/user/listInventory', [
            'headers' => [
                'Token' => $token,
                'Content-Type' => 'application/json'
            ],
            'timeout' => 30
        ]);
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $inventories = $body['data'] ?? [];
            if ($manual_inventory_id) {
                foreach ($inventories as $inv) {
                    if ($inv['GROUPADDRESS_ID'] == $manual_inventory_id) {
                        $inventory = $inv;
                        break;
                    }
                }
            } else if ($customer_province_id) {
                $province_name = $this->get_province_name_by_id($customer_province_id);
                foreach ($inventories as $inv) {
                    if (stripos($inv['PROVINCE_NAME'] ?? '', $province_name) !== false) {
                        $inventory = $inv;
                        break;
                    }
                }
            }
            if (!$inventory && !empty($inventories)) $inventory = $inventories[0];
        }

        if (!$inventory) wp_send_json_error('Không có kho hàng');

        $payload = $this->build_create_payload($order, $inventory);

        $response = wp_remote_post( 'https://partner.viettelpost.vn/v2/order/createOrder', [
            'headers' => [
                'Token' => $token,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($payload),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) wp_send_json_error('Lỗi kết nối API');

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == 200 && !empty($body['data']['ORDER_NUMBER'])) {
            $vtp_number = $body['data']['ORDER_NUMBER'];
            $order->update_meta_data('_vtp_order_number', $vtp_number);
            $order->add_order_note("Tạo vận đơn ViettelPost thành công: <strong>$vtp_number</strong>");
            $order->save();
            wp_send_json_success(['order_number' => $vtp_number]);
        } else {
            wp_send_json_error($body['message'] ?? 'Tạo đơn thất bại');
        }
    }

    public function ajax_cancel_order() {
        check_ajax_referer('vtp_cancel', 'nonce');
        $order_id = intval($_POST['order_id']);
        $order = wc_get_order($order_id);
        $vtp_number = $order->get_meta('_vtp_order_number');

        $api = new WC_ViettelPost_API();
        $token = $api->get_token();
        if (!$token) wp_send_json_error('Không có token');

        $response = wp_remote_post( 'https://partner.viettelpost.vn/v2/order/cancelOrder', [
            'headers' => [
                'Token' => $token,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode(['ORDER_NUMBER' => $vtp_number, 'TYPE' => 1]),
            'timeout' => 30
        ]);

        if (is_wp_error($response)) wp_send_json_error('Lỗi kết nối API');

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == 200) {
            $order->delete_meta_data('_vtp_order_number');
            $order->add_order_note("Hủy vận đơn ViettelPost: $vtp_number");
            $order->save();
            wp_send_json_success();
        } else {
            wp_send_json_error($body['message'] ?? 'Hủy thất bại');
        }
    }

    private function build_create_payload($order, $inventory) {
        $is_cod = in_array($order->get_payment_method(), ['cod', 'cash_on_delivery']);
        $weight = $this->calculate_weight_gram($order);

        $items = [];
        foreach ($order->get_items() as $item) {
            $items[] = $item->get_name() . ' x' . $item->get_quantity();
        }

        return [
            "ORDER_NUMBER" => "WC" . $order->get_id(),
            "GROUPADDRESS_ID" => (int)$inventory['GROUPADDRESS_ID'],
            "SENDER_FULLNAME" => get_option('wc_viettelpost_sender_name'),
            "SENDER_PHONE" => get_option('wc_viettelpost_sender_phone'),
            "SENDER_ADDRESS" => $inventory['ADDRESS'] ?? '',
            "RECEIVER_FULLNAME" => trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_lasttest_name()),
            "RECEIVER_PHONE" => $order->get_billing_phone(),
            "RECEIVER_ADDRESS" => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
            "RECEIVER_PROVINCE" => (int)$order->get_meta('_shipping_state'),
            "RECEIVER_DISTRICT" => (int)$order->get_meta('_shipping_city'),
            "RECEIVER_WARD" => (int)$order->get_meta('_shipping_address_2'),
            "PRODUCT_NAME" => substr(implode(', ', $items), 0, 255),
            "PRODUCT_WEIGHT" => $weight,
            "PRODUCT_PRICE" => (int)$order->get_subtotal(),
            "ORDER_PAYMENT" => $is_cod ? 3 : 1,
            "MONEY_COLLECTION" => $is_cod ? (int)$order->get_total() : 0,
            "ORDER_SERVICE" => "VCN",
            "ORDER_SERVICE_ADD" => get_option('wc_viettelpost_send_at_post_office') === 'yes' ? "NOP" : "",
            "ORDER_NOTE" => $order->get_customer_note()
        ];
    }

    private function calculate_weight_gram($order) {
        $weight = 0;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product && $product->get_weight()) {
                $weight += $product->get_weight() * $item->get_quantity();
            }
        }
        return max(100, intval($weight * 1000));
    }

    private function get_province_name_by_id($id) {
        $api = new WC_ViettelPost_API();
        $provinces = $api->get_provinces();
        foreach ($provinces as $p) {
            if ($p['PROVINCE_ID'] == $id) return $p['PROVINCE_NAME'];
        }
        return '';
    }
}

WC_ViettelPost_Admin::instance();