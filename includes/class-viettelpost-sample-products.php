<?php
/**
 * Sample Products Generator for Testing
 *
 * @package WooCommerce_ViettelPost
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_ViettelPost_Sample_Products class
 */
class WC_ViettelPost_Sample_Products {
	/**
	 * Sample products data
	 *
	 * @var array
	 */
	private $sample_products = array(
		array(
			'name'        => 'Áo thun nam cổ tròn',
			'price'       => 150000,
			'sale_price'  => 120000,
			'weight'      => 0.2, // kg
			'description' => 'Áo thun nam chất liệu cotton 100%, thoáng mát, thấm hút mồ hôi tốt. Phù hợp cho mùa hè.',
			'short_description' => 'Áo thun nam cotton 100%',
			'category'    => 'Thời trang nam',
			'image_url'   => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500',
		),
		array(
			'name'        => 'Quần jean nam',
			'price'       => 450000,
			'sale_price'  => 380000,
			'weight'      => 0.8,
			'description' => 'Quần jean nam form slim, chất liệu denim cao cấp, bền đẹp theo thời gian.',
			'short_description' => 'Quần jean nam form slim',
			'category'    => 'Thời trang nam',
			'image_url'   => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=500',
		),
		array(
			'name'        => 'Áo sơ mi nữ',
			'price'       => 280000,
			'sale_price'  => 250000,
			'weight'      => 0.3,
			'description' => 'Áo sơ mi nữ công sở, chất liệu vải cao cấp, form đẹp, dễ phối đồ.',
			'short_description' => 'Áo sơ mi nữ công sở',
			'category'    => 'Thời trang nữ',
			'image_url'   => 'https://images.unsplash.com/photo-1594633313593-bab3825d0caf?w=500',
		),
		array(
			'name'        => 'Giày thể thao',
			'price'       => 850000,
			'sale_price'  => 750000,
			'weight'      => 0.5,
			'description' => 'Giày thể thao chạy bộ, đế cao su chống trượt, êm ái, thoáng khí.',
			'short_description' => 'Giày thể thao chạy bộ',
			'category'    => 'Giày dép',
			'image_url'   => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500',
		),
		array(
			'name'        => 'Túi xách nữ',
			'price'       => 320000,
			'sale_price'  => 280000,
			'weight'      => 0.4,
			'description' => 'Túi xách nữ da thật, thiết kế sang trọng, nhiều ngăn tiện lợi.',
			'short_description' => 'Túi xách nữ da thật',
			'category'    => 'Phụ kiện',
			'image_url'   => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500',
		),
		array(
			'name'        => 'Đồng hồ nam',
			'price'       => 1200000,
			'sale_price'  => 980000,
			'weight'      => 0.15,
			'description' => 'Đồng hồ nam dây da, mặt kính sapphire, chống nước 5ATM.',
			'short_description' => 'Đồng hồ nam dây da',
			'category'    => 'Phụ kiện',
			'image_url'   => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500',
		),
		array(
			'name'        => 'Balo du lịch',
			'price'       => 550000,
			'sale_price'  => 480000,
			'weight'      => 0.6,
			'description' => 'Balo du lịch chống nước, nhiều ngăn, đệm lưng êm ái, phù hợp đi du lịch.',
			'short_description' => 'Balo du lịch chống nước',
			'category'    => 'Túi xách',
			'image_url'   => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500',
		),
		array(
			'name'        => 'Máy tính bảng',
			'price'       => 5500000,
			'sale_price'  => 4800000,
			'weight'      => 0.5,
			'description' => 'Máy tính bảng 10 inch, màn hình Full HD, RAM 4GB, bộ nhớ 64GB.',
			'short_description' => 'Máy tính bảng 10 inch',
			'category'    => 'Điện tử',
			'image_url'   => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500',
		),
		array(
			'name'        => 'Tai nghe không dây',
			'price'       => 890000,
			'sale_price'  => 750000,
			'weight'      => 0.05,
			'description' => 'Tai nghe không dây Bluetooth, chống ồn chủ động, pin 30 giờ.',
			'short_description' => 'Tai nghe không dây Bluetooth',
			'category'    => 'Điện tử',
			'image_url'   => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500',
		),
		array(
			'name'        => 'Sách tiểu thuyết',
			'price'       => 120000,
			'sale_price'  => 99000,
			'weight'      => 0.3,
			'description' => 'Sách tiểu thuyết bestseller, bìa cứng, in đẹp, nội dung hấp dẫn.',
			'short_description' => 'Sách tiểu thuyết bestseller',
			'category'    => 'Sách',
			'image_url'   => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500',
		),
	);

	/**
	 * Generate sample products
	 *
	 * @return array Results
	 */
	public function generate_products() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array(
				'success' => 0,
				'failed'  => 0,
				'products' => array(),
				'errors' => array( __( 'WooCommerce chưa được kích hoạt', 'woocommerce-viettelpost' ) ),
			);
		}

		$results = array(
			'success' => 0,
			'failed'  => 0,
			'products' => array(),
			'errors' => array(),
		);

		foreach ( $this->sample_products as $product_data ) {
			$result = $this->create_product( $product_data );
			if ( $result['success'] ) {
				$results['success']++;
				$results['products'][] = $result['product_id'];
			} else {
				$results['failed']++;
				if ( isset( $result['message'] ) ) {
					$results['errors'][] = $result['message'];
				}
			}
		}

		return $results;
	}

	/**
	 * Create a product
	 *
	 * @param array $data Product data.
	 * @return array
	 */
	private function create_product( $data ) {
		try {
			// Generate SKU
			$sku = 'VTP-' . sanitize_title( $data['name'] );
			
			// Check if product already exists by SKU
			$existing_id = wc_get_product_id_by_sku( $sku );
			if ( $existing_id ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Sản phẩm "%s" đã tồn tại (ID: %d)', 'woocommerce-viettelpost' ), $data['name'], $existing_id ),
				);
			}

			// Create product
			$product = new WC_Product_Simple();
			
			if ( ! $product ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Không thể khởi tạo sản phẩm "%s"', 'woocommerce-viettelpost' ), $data['name'] ),
				);
			}

			$product->set_name( $data['name'] );
			$product->set_description( $data['description'] );
			$product->set_short_description( $data['short_description'] );
			$product->set_regular_price( $data['price'] );
			
			if ( isset( $data['sale_price'] ) && $data['sale_price'] > 0 ) {
				$product->set_sale_price( $data['sale_price'] );
			}

			$product->set_weight( $data['weight'] );
			$product->set_manage_stock( true );
			$product->set_stock_quantity( rand( 10, 100 ) );
			$product->set_stock_status( 'instock' );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );

			// Set SKU
			$product->set_sku( $sku );

			// Set category
			$category = $this->get_or_create_category( $data['category'] );
			if ( $category ) {
				$product->set_category_ids( array( $category ) );
			}

			// Save product
			$product_id = $product->save();

			if ( is_wp_error( $product_id ) ) {
				$error_message = $product_id->get_error_message();
				return array(
					'success' => false,
					'message' => sprintf( __( 'Lỗi khi lưu sản phẩm "%s": %s', 'woocommerce-viettelpost' ), $data['name'], $error_message ),
				);
			}

			if ( ! $product_id || 0 === absint( $product_id ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Không thể lưu sản phẩm "%s"', 'woocommerce-viettelpost' ), $data['name'] ),
				);
			}

			// Verify product was created
			$saved_product = wc_get_product( $product_id );
			if ( ! $saved_product ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Sản phẩm "%s" đã được tạo nhưng không thể xác minh', 'woocommerce-viettelpost' ), $data['name'] ),
				);
			}

			// Set featured image
			if ( ! empty( $data['image_url'] ) ) {
				$this->set_product_image( $product_id, $data['image_url'] );
			}

			return array(
				'success'   => true,
				'product_id' => $product_id,
				'message'   => sprintf( __( 'Đã tạo sản phẩm "%s" thành công (ID: %d)', 'woocommerce-viettelpost' ), $data['name'], $product_id ),
			);
		} catch ( Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Lỗi exception khi tạo sản phẩm "%s": %s', 'woocommerce-viettelpost' ), $data['name'], $e->getMessage() ),
			);
		}
	}

	/**
	 * Get or create category
	 *
	 * @param string $category_name Category name.
	 * @return int|false
	 */
	private function get_or_create_category( $category_name ) {
		$term = get_term_by( 'name', $category_name, 'product_cat' );
		
		if ( $term ) {
			return $term->term_id;
		}

		$term = wp_insert_term(
			$category_name,
			'product_cat',
			array(
				'description' => sprintf( __( 'Danh mục %s', 'woocommerce-viettelpost' ), $category_name ),
				'slug'        => sanitize_title( $category_name ),
			)
		);

		if ( is_wp_error( $term ) ) {
			return false;
		}

		return $term['term_id'];
	}

	/**
	 * Set product image from URL
	 *
	 * @param int    $product_id Product ID.
	 * @param string $image_url Image URL.
	 */
	private function set_product_image( $product_id, $image_url ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = download_url( $image_url );
		if ( is_wp_error( $tmp ) ) {
			return;
		}

		$file_array = array(
			'name'     => basename( $image_url ),
			'tmp_name' => $tmp,
		);

		$id = media_handle_sideload( $file_array, 0 );
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return;
		}

		set_post_thumbnail( $product_id, $id );
	}

	/**
	 * Delete all sample products
	 *
	 * @return array
	 */
	public function delete_sample_products() {
		$deleted = 0;
		$failed = 0;

		foreach ( $this->sample_products as $product_data ) {
			$sku = 'VTP-' . sanitize_title( $product_data['name'] );
			$product_id = wc_get_product_id_by_sku( $sku );
			
			if ( $product_id ) {
				$result = wp_delete_post( $product_id, true );
				if ( $result ) {
					$deleted++;
				} else {
					$failed++;
				}
			}
		}

		return array(
			'deleted' => $deleted,
			'failed'  => $failed,
		);
	}
}

