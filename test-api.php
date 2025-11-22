<?php
/**
 * Test API với fake data
 * 
 * Cách sử dụng:
 * 1. Truy cập: http://your-site.com/wp-content/plugins/woocommerce-viettelpost/test-api.php
 * 2. Hoặc chạy từ command line: php test-api.php
 * 
 * LƯU Ý: Xóa file này sau khi test xong để bảo mật!
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Kiểm tra quyền (chỉ admin)
if ( ! current_user_can( 'manage_options' ) ) {
    die( 'Access denied. Only administrators can access this file.' );
}

// Fake POST data
$fake_data = array(
    'PRODUCT_WEIGHT' => 7500,
    'PRODUCT_PRICE' => 5000,
    'MONEY_COLLECTION' => 5000,
    'ORDER_SERVICE_ADD' => '',
    'ORDER_SERVICE' => 'VCN',
    'SENDER_PROVINCE' => '1',
    'SENDER_DISTRICT' => '14',
    'RECEIVER_PROVINCE' => '2',
    'RECEIVER_DISTRICT' => '43',
    'PRODUCT_TYPE' => 'HH',
    'NATIONAL_TYPE' => 1,
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>ViettelPost API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #0073aa;
        }
        .section h2 {
            margin-top: 0;
            color: #0073aa;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .success {
            color: #46b450;
            font-weight: bold;
        }
        .error {
            color: #dc3232;
            font-weight: bold;
        }
        .info {
            color: #0073aa;
        }
        button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        button:hover {
            background: #005a87;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 ViettelPost API Test Tool</h1>
        
        <div class="warning">
            <strong>⚠️ CẢNH BÁO:</strong> File này chỉ dùng để test. Xóa file sau khi test xong để bảo mật!
        </div>

        <div class="section">
            <h2>📤 Fake POST Data</h2>
            <pre><?php echo json_encode( $fake_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ); ?></pre>
        </div>

        <?php
        // Kiểm tra nếu có request
        if ( isset( $_GET['test'] ) && $_GET['test'] === '1' ) {
            echo '<div class="section">';
            echo '<h2>📥 API Response</h2>';
            
            // Load API class
            if ( ! class_exists( 'WC_ViettelPost_API' ) ) {
                require_once( dirname( __FILE__ ) . '/includes/class-viettelpost-api.php' );
            }
            
            $api = new WC_ViettelPost_API();
            
            echo '<p class="info">Đang gọi API getPrice...</p>';
            
            // Gọi API
            $start_time = microtime( true );
            $result = $api->calculate_shipping( $fake_data );
            $end_time = microtime( true );
            $execution_time = round( ( $end_time - $start_time ) * 1000, 2 );
            
            echo '<p class="info">⏱️ Thời gian thực thi: ' . $execution_time . 'ms</p>';
            
            if ( is_wp_error( $result ) ) {
                echo '<p class="error">❌ Lỗi: ' . esc_html( $result->get_error_message() ) . '</p>';
                echo '<pre>' . print_r( $result, true ) . '</pre>';
            } else {
                echo '<p class="success">✅ Thành công!</p>';
                echo '<h3>Response Data:</h3>';
                echo '<pre>' . print_r( $result, true ) . '</pre>';
                
                // Extract MONEY_TOTAL
                if ( is_array( $result ) ) {
                    if ( isset( $result['MONEY_TOTAL'] ) ) {
                        echo '<p class="success">💰 MONEY_TOTAL: ' . number_format( $result['MONEY_TOTAL'], 0, ',', '.' ) . ' VNĐ</p>';
                    } elseif ( isset( $result[0]['MONEY_TOTAL'] ) ) {
                        echo '<p class="success">💰 MONEY_TOTAL: ' . number_format( $result[0]['MONEY_TOTAL'], 0, ',', '.' ) . ' VNĐ</p>';
                    } else {
                        echo '<p class="error">⚠️ Không tìm thấy MONEY_TOTAL trong response</p>';
                        echo '<p class="info">Keys có sẵn: ' . implode( ', ', array_keys( $result ) ) . '</p>';
                    }
                } else {
                    echo '<p class="error">⚠️ Response không phải là array</p>';
                    echo '<p class="info">Type: ' . gettype( $result ) . '</p>';
                }
            }
            
            echo '</div>';
        }
        ?>

        <div class="section">
            <h2>🔧 Actions</h2>
            <button onclick="location.href='?test=1'">Test API với Fake Data</button>
            <button onclick="location.href='?'">Refresh</button>
        </div>

        <div class="section">
            <h2>📋 Debug Info</h2>
            <p><strong>API Base URL:</strong> 
                <?php 
                if ( class_exists( 'WC_ViettelPost_API' ) ) {
                    $api = new WC_ViettelPost_API();
                    $reflection = new ReflectionClass( $api );
                    $property = $reflection->getProperty( 'api_base_url' );
                    $property->setAccessible( true );
                    echo esc_html( $property->getValue( $api ) );
                } else {
                    echo 'N/A';
                }
                ?>
            </p>
            <p><strong>WordPress Version:</strong> <?php echo get_bloginfo( 'version' ); ?></p>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Current User:</strong> <?php echo wp_get_current_user()->user_login; ?></p>
        </div>

        <div class="section">
            <h2>📝 Logs</h2>
            <p>Xem debug logs tại: <code>wp-content/debug.log</code></p>
            <p>Hoặc chạy lệnh: <code>tail -f wp-content/debug.log</code></p>
        </div>
    </div>
</body>
</html>

