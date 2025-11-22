-- SQL đơn giản để tạo sản phẩm mẫu (thay thế wp_ bằng prefix của bạn)
-- Chạy từng câu lệnh một hoặc thay thế {PREFIX} và {SITE_URL} trước khi chạy

-- Áo thun nam cổ tròn
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Áo thun nam chất liệu cotton 100%, thoáng mát, thấm hút mồ hôi tốt. Phù hợp cho mùa hè.', 'Áo thun nam cổ tròn', 'Áo thun nam cotton 100%', 'publish', 'open', 'open', '', 'ao-thun-nam-co-tron', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '50'),
(LAST_INSERT_ID(), '_weight', '0.2'),
(LAST_INSERT_ID(), '_regular_price', '150000'),
(LAST_INSERT_ID(), '_sale_price', '120000'),
(LAST_INSERT_ID(), '_price', '120000'),
(LAST_INSERT_ID(), '_sku', 'VTP-ao-thun-nam-co-tron'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Quần jean nam
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Quần jean nam form slim, chất liệu denim cao cấp, bền đẹp theo thời gian.', 'Quần jean nam', 'Quần jean nam form slim', 'publish', 'open', 'open', '', 'quan-jean-nam', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '30'),
(LAST_INSERT_ID(), '_weight', '0.8'),
(LAST_INSERT_ID(), '_regular_price', '450000'),
(LAST_INSERT_ID(), '_sale_price', '380000'),
(LAST_INSERT_ID(), '_price', '380000'),
(LAST_INSERT_ID(), '_sku', 'VTP-quan-jean-nam'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Áo sơ mi nữ
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Áo sơ mi nữ công sở, chất liệu vải cao cấp, form đẹp, dễ phối đồ.', 'Áo sơ mi nữ', 'Áo sơ mi nữ công sở', 'publish', 'open', 'open', '', 'ao-so-mi-nu', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '40'),
(LAST_INSERT_ID(), '_weight', '0.3'),
(LAST_INSERT_ID(), '_regular_price', '280000'),
(LAST_INSERT_ID(), '_sale_price', '250000'),
(LAST_INSERT_ID(), '_price', '250000'),
(LAST_INSERT_ID(), '_sku', 'VTP-ao-so-mi-nu'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Giày thể thao
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Giày thể thao chạy bộ, đế cao su chống trượt, êm ái, thoáng khí.', 'Giày thể thao', 'Giày thể thao chạy bộ', 'publish', 'open', 'open', '', 'giay-the-thao', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '25'),
(LAST_INSERT_ID(), '_weight', '0.5'),
(LAST_INSERT_ID(), '_regular_price', '850000'),
(LAST_INSERT_ID(), '_sale_price', '750000'),
(LAST_INSERT_ID(), '_price', '750000'),
(LAST_INSERT_ID(), '_sku', 'VTP-giay-the-thao'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Túi xách nữ
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Túi xách nữ da thật, thiết kế sang trọng, nhiều ngăn tiện lợi.', 'Túi xách nữ', 'Túi xách nữ da thật', 'publish', 'open', 'open', '', 'tui-xach-nu', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '35'),
(LAST_INSERT_ID(), '_weight', '0.4'),
(LAST_INSERT_ID(), '_regular_price', '320000'),
(LAST_INSERT_ID(), '_sale_price', '280000'),
(LAST_INSERT_ID(), '_price', '280000'),
(LAST_INSERT_ID(), '_sku', 'VTP-tui-xach-nu'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Đồng hồ nam
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Đồng hồ nam dây da, mặt kính sapphire, chống nước 5ATM.', 'Đồng hồ nam', 'Đồng hồ nam dây da', 'publish', 'open', 'open', '', 'dong-ho-nam', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '20'),
(LAST_INSERT_ID(), '_weight', '0.15'),
(LAST_INSERT_ID(), '_regular_price', '1200000'),
(LAST_INSERT_ID(), '_sale_price', '980000'),
(LAST_INSERT_ID(), '_price', '980000'),
(LAST_INSERT_ID(), '_sku', 'VTP-dong-ho-nam'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Balo du lịch
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Balo du lịch chống nước, nhiều ngăn, đệm lưng êm ái, phù hợp đi du lịch.', 'Balo du lịch', 'Balo du lịch chống nước', 'publish', 'open', 'open', '', 'balo-du-lich', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '45'),
(LAST_INSERT_ID(), '_weight', '0.6'),
(LAST_INSERT_ID(), '_regular_price', '550000'),
(LAST_INSERT_ID(), '_sale_price', '480000'),
(LAST_INSERT_ID(), '_price', '480000'),
(LAST_INSERT_ID(), '_sku', 'VTP-balo-du-lich'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Máy tính bảng
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Máy tính bảng 10 inch, màn hình Full HD, RAM 4GB, bộ nhớ 64GB.', 'Máy tính bảng', 'Máy tính bảng 10 inch', 'publish', 'open', 'open', '', 'may-tinh-bang', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '15'),
(LAST_INSERT_ID(), '_weight', '0.5'),
(LAST_INSERT_ID(), '_regular_price', '5500000'),
(LAST_INSERT_ID(), '_sale_price', '4800000'),
(LAST_INSERT_ID(), '_price', '4800000'),
(LAST_INSERT_ID(), '_sku', 'VTP-may-tinh-bang'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Tai nghe không dây
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Tai nghe không dây Bluetooth, chống ồn chủ động, pin 30 giờ.', 'Tai nghe không dây', 'Tai nghe không dây Bluetooth', 'publish', 'open', 'open', '', 'tai-nghe-khong-day', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '60'),
(LAST_INSERT_ID(), '_weight', '0.05'),
(LAST_INSERT_ID(), '_regular_price', '890000'),
(LAST_INSERT_ID(), '_sale_price', '750000'),
(LAST_INSERT_ID(), '_price', '750000'),
(LAST_INSERT_ID(), '_sku', 'VTP-tai-nghe-khong-day'),
(LAST_INSERT_ID(), '_product_type', 'simple');

-- Sách tiểu thuyết
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
VALUES (1, NOW(), NOW(), 'Sách tiểu thuyết bestseller, bìa cứng, in đẹp, nội dung hấp dẫn.', 'Sách tiểu thuyết', 'Sách tiểu thuyết bestseller', 'publish', 'open', 'open', '', 'sach-tieu-thuyet', '', '', NOW(), NOW(), '', 0, '', 0, 'product', '', 0);

INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES
(LAST_INSERT_ID(), '_visibility', 'visible'),
(LAST_INSERT_ID(), '_stock_status', 'instock'),
(LAST_INSERT_ID(), '_manage_stock', 'yes'),
(LAST_INSERT_ID(), '_stock', '100'),
(LAST_INSERT_ID(), '_weight', '0.3'),
(LAST_INSERT_ID(), '_regular_price', '120000'),
(LAST_INSERT_ID(), '_sale_price', '99000'),
(LAST_INSERT_ID(), '_price', '99000'),
(LAST_INSERT_ID(), '_sku', 'VTP-sach-tieu-thuyet'),
(LAST_INSERT_ID(), '_product_type', 'simple');

