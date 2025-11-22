# Hướng dẫn sử dụng SQL để tạo sản phẩm mẫu

## File SQL có sẵn

1. **sample-products.sql** - File SQL đầy đủ với biến và logic phức tạp
2. **sample-products-simple.sql** - File SQL đơn giản, dễ sử dụng hơn

## Cách sử dụng

### Cách 1: Sử dụng file đơn giản (Khuyến nghị)

1. Mở file `sample-products-simple.sql`
2. Thay thế `wp_` bằng table prefix của bạn (nếu khác)
3. Mở phpMyAdmin hoặc MySQL client
4. Chọn database WordPress của bạn
5. Copy và paste từng câu lệnh INSERT vào SQL tab
6. Chạy từng câu lệnh

### Cách 2: Sử dụng file đầy đủ

1. Mở file `sample-products.sql`
2. Thay thế `{PREFIX}` bằng table prefix của bạn (ví dụ: `wp_`)
3. Thay thế `{SITE_URL}` bằng URL website của bạn (ví dụ: `https://example.com`)
4. Thay thế `{ID}` bằng số ID (sẽ được tự động thay thế khi chạy)
5. Chạy toàn bộ file SQL

## Lưu ý

- **Backup database trước khi chạy SQL**
- Kiểm tra table prefix của bạn (thường là `wp_` nhưng có thể khác)
- Nếu sản phẩm đã tồn tại (theo SKU), sẽ bị lỗi duplicate
- Sau khi chạy SQL, cần vào WordPress Admin > WooCommerce > Products để xem sản phẩm

## Xóa sản phẩm mẫu

Nếu muốn xóa tất cả sản phẩm mẫu:

```sql
DELETE FROM wp_posts WHERE post_type = 'product' AND post_name IN (
    'ao-thun-nam-co-tron',
    'quan-jean-nam',
    'ao-so-mi-nu',
    'giay-the-thao',
    'tui-xach-nu',
    'dong-ho-nam',
    'balo-du-lich',
    'may-tinh-bang',
    'tai-nghe-khong-day',
    'sach-tieu-thuyet'
);

DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID FROM wp_posts);
```

Hoặc xóa theo SKU:

```sql
DELETE p, pm FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_sku' AND pm.meta_value LIKE 'VTP-%';
```

## Thông tin sản phẩm

| STT | Tên sản phẩm        | Giá gốc    | Giá sale   | Trọng lượng | SKU                     |
| --- | ------------------- | ---------- | ---------- | ----------- | ----------------------- |
| 1   | Áo thun nam cổ tròn | 150,000đ   | 120,000đ   | 0.2kg       | VTP-ao-thun-nam-co-tron |
| 2   | Quần jean nam       | 450,000đ   | 380,000đ   | 0.8kg       | VTP-quan-jean-nam       |
| 3   | Áo sơ mi nữ         | 280,000đ   | 250,000đ   | 0.3kg       | VTP-ao-so-mi-nu         |
| 4   | Giày thể thao       | 850,000đ   | 750,000đ   | 0.5kg       | VTP-giay-the-thao       |
| 5   | Túi xách nữ         | 320,000đ   | 280,000đ   | 0.4kg       | VTP-tui-xach-nu         |
| 6   | Đồng hồ nam         | 1,200,000đ | 980,000đ   | 0.15kg      | VTP-dong-ho-nam         |
| 7   | Balo du lịch        | 550,000đ   | 480,000đ   | 0.6kg       | VTP-balo-du-lich        |
| 8   | Máy tính bảng       | 5,500,000đ | 4,800,000đ | 0.5kg       | VTP-may-tinh-bang       |
| 9   | Tai nghe không dây  | 890,000đ   | 750,000đ   | 0.05kg      | VTP-tai-nghe-khong-day  |
| 10  | Sách tiểu thuyết    | 120,000đ   | 99,000đ    | 0.3kg       | VTP-sach-tieu-thuyet    |
