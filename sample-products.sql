-- SQL để tạo sản phẩm mẫu cho WooCommerce ViettelPost
-- Lưu ý: Thay thế {PREFIX} bằng table prefix của bạn (thường là wp_)
-- Thay thế {SITE_URL} bằng URL website của bạn

-- 1. Áo thun nam cổ tròn
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Áo thun nam chất liệu cotton 100%, thoáng mát, thấm hút mồ hôi tốt. Phù hợp cho mùa hè.', 'Áo thun nam cổ tròn', 'Áo thun nam cotton 100%', 'publish', 'open', 'open', '', 'ao-thun-nam-co-tron', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product1_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product1_id, '_visibility', 'visible'),
(@product1_id, '_stock_status', 'instock'),
(@product1_id, '_manage_stock', 'yes'),
(@product1_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product1_id, '_weight', '0.2'),
(@product1_id, '_regular_price', '150000'),
(@product1_id, '_sale_price', '120000'),
(@product1_id, '_price', '120000'),
(@product1_id, '_sku', 'VTP-ao-thun-nam-co-tron'),
(@product1_id, '_product_version', '8.0.0'),
(@product1_id, '_product_type', 'simple');

-- 2. Quần jean nam
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Quần jean nam form slim, chất liệu denim cao cấp, bền đẹp theo thời gian.', 'Quần jean nam', 'Quần jean nam form slim', 'publish', 'open', 'open', '', 'quan-jean-nam', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product2_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product2_id, '_visibility', 'visible'),
(@product2_id, '_stock_status', 'instock'),
(@product2_id, '_manage_stock', 'yes'),
(@product2_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product2_id, '_weight', '0.8'),
(@product2_id, '_regular_price', '450000'),
(@product2_id, '_sale_price', '380000'),
(@product2_id, '_price', '380000'),
(@product2_id, '_sku', 'VTP-quan-jean-nam'),
(@product2_id, '_product_version', '8.0.0'),
(@product2_id, '_product_type', 'simple');

-- 3. Áo sơ mi nữ
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Áo sơ mi nữ công sở, chất liệu vải cao cấp, form đẹp, dễ phối đồ.', 'Áo sơ mi nữ', 'Áo sơ mi nữ công sở', 'publish', 'open', 'open', '', 'ao-so-mi-nu', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product3_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product3_id, '_visibility', 'visible'),
(@product3_id, '_stock_status', 'instock'),
(@product3_id, '_manage_stock', 'yes'),
(@product3_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product3_id, '_weight', '0.3'),
(@product3_id, '_regular_price', '280000'),
(@product3_id, '_sale_price', '250000'),
(@product3_id, '_price', '250000'),
(@product3_id, '_sku', 'VTP-ao-so-mi-nu'),
(@product3_id, '_product_version', '8.0.0'),
(@product3_id, '_product_type', 'simple');

-- 4. Giày thể thao
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Giày thể thao chạy bộ, đế cao su chống trượt, êm ái, thoáng khí.', 'Giày thể thao', 'Giày thể thao chạy bộ', 'publish', 'open', 'open', '', 'giay-the-thao', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product4_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product4_id, '_visibility', 'visible'),
(@product4_id, '_stock_status', 'instock'),
(@product4_id, '_manage_stock', 'yes'),
(@product4_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product4_id, '_weight', '0.5'),
(@product4_id, '_regular_price', '850000'),
(@product4_id, '_sale_price', '750000'),
(@product4_id, '_price', '750000'),
(@product4_id, '_sku', 'VTP-giay-the-thao'),
(@product4_id, '_product_version', '8.0.0'),
(@product4_id, '_product_type', 'simple');

-- 5. Túi xách nữ
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Túi xách nữ da thật, thiết kế sang trọng, nhiều ngăn tiện lợi.', 'Túi xách nữ', 'Túi xách nữ da thật', 'publish', 'open', 'open', '', 'tui-xach-nu', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product5_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product5_id, '_visibility', 'visible'),
(@product5_id, '_stock_status', 'instock'),
(@product5_id, '_manage_stock', 'yes'),
(@product5_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product5_id, '_weight', '0.4'),
(@product5_id, '_regular_price', '320000'),
(@product5_id, '_sale_price', '280000'),
(@product5_id, '_price', '280000'),
(@product5_id, '_sku', 'VTP-tui-xach-nu'),
(@product5_id, '_product_version', '8.0.0'),
(@product5_id, '_product_type', 'simple');

-- 6. Đồng hồ nam
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Đồng hồ nam dây da, mặt kính sapphire, chống nước 5ATM.', 'Đồng hồ nam', 'Đồng hồ nam dây da', 'publish', 'open', 'open', '', 'dong-ho-nam', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product6_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product6_id, '_visibility', 'visible'),
(@product6_id, '_stock_status', 'instock'),
(@product6_id, '_manage_stock', 'yes'),
(@product6_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product6_id, '_weight', '0.15'),
(@product6_id, '_regular_price', '1200000'),
(@product6_id, '_sale_price', '980000'),
(@product6_id, '_price', '980000'),
(@product6_id, '_sku', 'VTP-dong-ho-nam'),
(@product6_id, '_product_version', '8.0.0'),
(@product6_id, '_product_type', 'simple');

-- 7. Balo du lịch
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Balo du lịch chống nước, nhiều ngăn, đệm lưng êm ái, phù hợp đi du lịch.', 'Balo du lịch', 'Balo du lịch chống nước', 'publish', 'open', 'open', '', 'balo-du-lich', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product7_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product7_id, '_visibility', 'visible'),
(@product7_id, '_stock_status', 'instock'),
(@product7_id, '_manage_stock', 'yes'),
(@product7_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product7_id, '_weight', '0.6'),
(@product7_id, '_regular_price', '550000'),
(@product7_id, '_sale_price', '480000'),
(@product7_id, '_price', '480000'),
(@product7_id, '_sku', 'VTP-balo-du-lich'),
(@product7_id, '_product_version', '8.0.0'),
(@product7_id, '_product_type', 'simple');

-- 8. Máy tính bảng
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Máy tính bảng 10 inch, màn hình Full HD, RAM 4GB, bộ nhớ 64GB.', 'Máy tính bảng', 'Máy tính bảng 10 inch', 'publish', 'open', 'open', '', 'may-tinh-bang', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product8_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product8_id, '_visibility', 'visible'),
(@product8_id, '_stock_status', 'instock'),
(@product8_id, '_manage_stock', 'yes'),
(@product8_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product8_id, '_weight', '0.5'),
(@product8_id, '_regular_price', '5500000'),
(@product8_id, '_sale_price', '4800000'),
(@product8_id, '_price', '4800000'),
(@product8_id, '_sku', 'VTP-may-tinh-bang'),
(@product8_id, '_product_version', '8.0.0'),
(@product8_id, '_product_type', 'simple');

-- 9. Tai nghe không dây
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Tai nghe không dây Bluetooth, chống ồn chủ động, pin 30 giờ.', 'Tai nghe không dây', 'Tai nghe không dây Bluetooth', 'publish', 'open', 'open', '', 'tai-nghe-khong-day', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product9_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product9_id, '_visibility', 'visible'),
(@product9_id, '_stock_status', 'instock'),
(@product9_id, '_manage_stock', 'yes'),
(@product9_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product9_id, '_weight', '0.05'),
(@product9_id, '_regular_price', '890000'),
(@product9_id, '_sale_price', '750000'),
(@product9_id, '_price', '750000'),
(@product9_id, '_sku', 'VTP-tai-nghe-khong-day'),
(@product9_id, '_product_version', '8.0.0'),
(@product9_id, '_product_type', 'simple');

-- 10. Sách tiểu thuyết
INSERT INTO {PREFIX}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Sách tiểu thuyết bestseller, bìa cứng, in đẹp, nội dung hấp dẫn.', 'Sách tiểu thuyết', 'Sách tiểu thuyết bestseller', 'publish', 'open', 'open', '', 'sach-tieu-thuyet', '', '', NOW(), NOW(), '', 0, '{SITE_URL}/?p={ID}', 0, 'product', '', 0);

SET @product10_id = LAST_INSERT_ID();

INSERT INTO {PREFIX}postmeta (post_id, meta_key, meta_value) VALUES
(@product10_id, '_visibility', 'visible'),
(@product10_id, '_stock_status', 'instock'),
(@product10_id, '_manage_stock', 'yes'),
(@product10_id, '_stock', FLOOR(10 + RAND() * 90)),
(@product10_id, '_weight', '0.3'),
(@product10_id, '_regular_price', '120000'),
(@product10_id, '_sale_price', '99000'),
(@product10_id, '_price', '99000'),
(@product10_id, '_sku', 'VTP-sach-tieu-thuyet'),
(@product10_id, '_product_version', '8.0.0'),
(@product10_id, '_product_type', 'simple');

-- Tạo danh mục sản phẩm (nếu chưa có)
INSERT IGNORE INTO {PREFIX}terms (name, slug) VALUES
('Thời trang nam', 'thoi-trang-nam'),
('Thời trang nữ', 'thoi-trang-nu'),
('Giày dép', 'giay-dep'),
('Phụ kiện', 'phu-kien'),
('Túi xách', 'tui-xach'),
('Điện tử', 'dien-tu'),
('Sách', 'sach');

SET @term1 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'thoi-trang-nam');
SET @term2 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'thoi-trang-nu');
SET @term3 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'giay-dep');
SET @term4 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'phu-kien');
SET @term5 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'tui-xach');
SET @term6 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'dien-tu');
SET @term7 = (SELECT term_id FROM {PREFIX}terms WHERE slug = 'sach');

INSERT IGNORE INTO {PREFIX}term_taxonomy (term_id, taxonomy, description, parent, count) VALUES
(@term1, 'product_cat', 'Danh mục thời trang nam', 0, 0),
(@term2, 'product_cat', 'Danh mục thời trang nữ', 0, 0),
(@term3, 'product_cat', 'Danh mục giày dép', 0, 0),
(@term4, 'product_cat', 'Danh mục phụ kiện', 0, 0),
(@term5, 'product_cat', 'Danh mục túi xách', 0, 0),
(@term6, 'product_cat', 'Danh mục điện tử', 0, 0),
(@term7, 'product_cat', 'Danh mục sách', 0, 0);

-- Gán sản phẩm vào danh mục
INSERT INTO {PREFIX}term_relationships (object_id, term_taxonomy_id, term_order) VALUES
(@product1_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term1 AND taxonomy = 'product_cat'), 0),
(@product2_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term1 AND taxonomy = 'product_cat'), 0),
(@product3_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term2 AND taxonomy = 'product_cat'), 0),
(@product4_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term3 AND taxonomy = 'product_cat'), 0),
(@product5_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term4 AND taxonomy = 'product_cat'), 0),
(@product6_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term4 AND taxonomy = 'product_cat'), 0),
(@product7_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term5 AND taxonomy = 'product_cat'), 0),
(@product8_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term6 AND taxonomy = 'product_cat'), 0),
(@product9_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term6 AND taxonomy = 'product_cat'), 0),
(@product10_id, (SELECT term_taxonomy_id FROM {PREFIX}term_taxonomy WHERE term_id = @term7 AND taxonomy = 'product_cat'), 0);

-- Cập nhật số lượng sản phẩm trong danh mục
UPDATE {PREFIX}term_taxonomy SET count = (
    SELECT COUNT(*) FROM {PREFIX}term_relationships 
    WHERE term_taxonomy_id = {PREFIX}term_taxonomy.term_taxonomy_id
) WHERE taxonomy = 'product_cat';

