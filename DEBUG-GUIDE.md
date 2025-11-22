
# Hướng Dẫn Debug ViettelPost API

## Cách Xem Debug Logs

### 1. Tìm File Debug Log

Debug logs được ghi vào WordPress debug log. Để xem:

**Cách 1: Kiểm tra file `debug.log`**

- File thường nằm tại: `wp-content/debug.log`
- Hoặc kiểm tra trong `wp-config.php` xem `WP_DEBUG_LOG` được set ở đâu

**Cách 2: Kiểm tra PHP Error Log**

- Xem trong cPanel → Error Logs
- Hoặc file: `/var/log/php/error.log` (tùy server)

**Cách 3: Kiểm tra trong WordPress**

- Cài plugin "Query Monitor" hoặc "Debug Bar"
- Xem trong phần Logs

### 2. Bật Debug Mode (nếu chưa bật)

Thêm vào file `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### 3. Các Điểm Debug Đã Thêm

#### A. **API Call (`class-viettelpost-api.php`)**

```
=== ViettelPost API Debug: calculate_shipping START ===
API URL: ...
Params sent: ...
Token: ...
=== Raw Response ===
Response code: ...
Response message: ...
Response body: ...
=== Parsed Data ===
...
SUCCESS/ERROR: ...
=== ViettelPost API Debug: END ===
```

**Kiểm tra:**

- ✅ Params có đúng format không?
- ✅ Token có hợp lệ không?
- ✅ Response code là 200?
- ✅ Response body có data không?
- ✅ Parsed data có `status: 200` không?

#### B. **Shipping Cost Calculation (`class-viettelpost-shipping.php`)**

```
=== calculate_shipping_cost: Before API call ===
Params: ...
=== calculate_shipping_cost: After API call ===
Result type: ...
Result: ...
SUCCESS: Found MONEY_TOTAL ... / ERROR: ...
```

**Kiểm tra:**

- ✅ Params có đủ thông tin không?
- ✅ Result có phải là array không?
- ✅ Có key `MONEY_TOTAL` trong result không?

#### C. **AJAX Handler (`ajax_calculate_shipping`)**

```
=== ajax_calculate_shipping: Before calculate_shipping_cost ===
Province: ...
District: ...
Ward: ...
Package: ...
=== ajax_calculate_shipping: After calculate_shipping_cost ===
Shipping cost: ...
```

**Kiểm tra:**

- ✅ Province, District, Ward có giá trị không?
- ✅ Package có contents không?
- ✅ Shipping cost có được tính ra không?

### 4. Các Lỗi Thường Gặp và Cách Xử Lý

#### Lỗi 1: "Token failed"

**Nguyên nhân:** Không lấy được token từ API
**Giải pháp:**

- Kiểm tra username/password trong settings
- Kiểm tra API base URL

#### Lỗi 2: "wp_remote_post failed"

**Nguyên nhân:** Không kết nối được đến API
**Giải pháp:**

- Kiểm tra kết nối mạng
- Kiểm tra firewall
- Kiểm tra SSL certificate

#### Lỗi 3: "API returned error - Status: XXX"

**Nguyên nhân:** API trả về lỗi
**Giải pháp:**

- Xem message trong log
- Kiểm tra params có đúng không
- Kiểm tra API documentation

#### Lỗi 4: "MONEY_TOTAL not found in result"

**Nguyên nhân:** Response format không đúng
**Giải pháp:**

- Xem "Result keys" trong log
- Kiểm tra xem API có thay đổi format không
- Cập nhật code để đọc đúng key

#### Lỗi 5: "calculate_shipping_cost returned false"

**Nguyên nhân:** Không tính được phí ship
**Giải pháp:**

- Xem logs ở trên để tìm nguyên nhân
- Kiểm tra sender address trong settings
- Kiểm tra receiver address

### 5. Test Case để Debug

1. **Test với sản phẩm có weight:**

   - Thêm sản phẩm có weight vào giỏ hàng
   - Chọn địa chỉ đầy đủ
   - Xem log để kiểm tra PRODUCT_WEIGHT

2. **Test với sản phẩm không có weight:**

   - Thêm sản phẩm không có weight
   - Chọn địa chỉ đầy đủ
   - Xem log để kiểm tra có dùng 0.5kg (500g) không

3. **Test với địa chỉ không hợp lệ:**
   - Chọn địa chỉ không tồn tại
   - Xem log để kiểm tra error message

### 6. Tắt Debug Logs (Sau khi fix xong)

Sau khi đã tìm ra và fix lỗi, có thể tắt debug logs bằng cách:

1. Xóa hoặc comment các dòng `error_log()` trong code
2. Hoặc thêm điều kiện:

```php
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( '...' );
}
```

### 7. Quick Debug Commands

**Xem log real-time (Linux):**

```bash
tail -f wp-content/debug.log
```

**Xem log với filter ViettelPost:**

```bash
grep "ViettelPost" wp-content/debug.log
```

**Xem log cuối cùng:**

```bash
tail -n 100 wp-content/debug.log
```

### 8. Checklist Debug

Khi gặp lỗi, kiểm tra theo thứ tự:

- [ ] Debug logs có được ghi không?
- [ ] Params có đúng format không?
- [ ] Token có được lấy thành công không?
- [ ] API có trả về response không?
- [ ] Response code là 200?
- [ ] Response body có data không?
- [ ] Parsed data có đúng format không?
- [ ] Có key `MONEY_TOTAL` trong result không?
- [ ] Shipping cost có được tính ra không?

### 9. Gửi Log để Hỗ Trợ

Khi cần hỗ trợ, gửi kèm:

1. Toàn bộ debug logs từ khi bắt đầu đến khi lỗi
2. Screenshot của error message (nếu có)
3. Thông tin về:
   - Sản phẩm trong giỏ hàng (có weight không?)
   - Địa chỉ đã chọn
   - Settings của plugin
