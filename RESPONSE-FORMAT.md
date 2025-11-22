# Response Format Documentation

## Flow Xử Lý Response

### 1. API ViettelPost Response (Raw)

API `getPrice` trả về format:
```json
{
  "status": 200,
  "error": false,
  "message": "OK",
  "data": {
    "MONEY_TOTAL_OLD": 212069,
    "MONEY_TOTAL": 184085,
    "MONEY_TOTAL_FEE": 131410,
    "MONEY_FEE": 23190,
    "MONEY_COLLECTION_FEE": 12750,
    "MONEY_OTHER_FEE": 0,
    "MONEY_VAS": 0,
    "MONEY_VAT": 16735,
    "KPI_HT": 24
  }
}
```

### 2. PHP API Class (`class-viettelpost-api.php`)

**Function:** `calculate_shipping()`

**Xử lý:**
- Kiểm tra `$data['status'] === 200`
- Trả về `$data['data']` (chỉ phần data object)
- Nếu lỗi, trả về `WP_Error`

**Return:** 
```php
array(
  'MONEY_TOTAL' => 184085,
  'MONEY_TOTAL_FEE' => 131410,
  // ... các field khác
)
```

### 3. PHP Shipping Class (`class-viettelpost-shipping.php`)

**Function:** `calculate_shipping_cost()`

**Xử lý:**
- Nhận result từ API class (đã là `$data['data']`)
- Extract `MONEY_TOTAL` từ result
- Trả về `floatval($result['MONEY_TOTAL'])`

**Function:** `ajax_calculate_shipping()`

**Xử lý:**
- Gọi `calculate_shipping_cost()` để lấy cost
- Format cost bằng `wc_price()`
- Trả về bằng `wp_send_json_success()`

**Return (WordPress AJAX format):**
```json
{
  "success": true,
  "data": {
    "cost": 184085,
    "formatted": "184,085 ₫",
    "message": "Phí ship: 184,085 ₫",
    "shipping_method_id": "viettelpost"
  }
}
```

### 4. JavaScript (`viettelpost-address.js`)

**Function:** `calculateShipping()`

**Xử lý:**
- Nhận response từ WordPress AJAX
- Kiểm tra `response.success`
- Nếu success:
  - Lấy `response.data.formatted` để hiển thị
  - Lấy `response.data.cost` nếu cần
- Nếu error:
  - Lấy `response.data.message` để hiển thị lỗi

**Code:**
```javascript
success: function (response) {
  if (!response.success) {
    // Error handling
    var errorMsg = response.data && response.data.message 
      ? response.data.message 
      : "Không thể tính phí ship";
    // Show error
    return;
  }
  
  // Success
  if (response.data && response.data.formatted) {
    // Show formatted cost
    console.log('Cost:', response.data.cost);
    console.log('Formatted:', response.data.formatted);
  }
}
```

## Kiểm Tra Response

### Debug Logs

**JavaScript Console:**
```javascript
// Xem full response
console.log('ViettelPost AJAX Response:', response);

// Xem success flag
console.log('Response success:', response.success);

// Xem data
console.log('Response data:', response.data);

// Xem formatted cost
console.log('Formatted:', response.data.formatted);
console.log('Cost:', response.data.cost);
```

**PHP Debug Logs:**
- Xem trong `wp-content/debug.log`
- Tìm các dòng bắt đầu bằng `=== ViettelPost API Debug ===`

### Expected Response Structure

**Success Response:**
```json
{
  "success": true,
  "data": {
    "cost": 184085,
    "formatted": "184,085 ₫",
    "message": "Phí ship: 184,085 ₫",
    "shipping_method_id": "viettelpost"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "data": {
    "message": "Lỗi khi tính phí ship"
  }
}
```

## Các Trường Hợp Cần Kiểm Tra

### ✅ Trường hợp 1: API trả về success
- `response.success === true`
- `response.data.cost` có giá trị
- `response.data.formatted` có giá trị

### ✅ Trường hợp 2: API trả về error
- `response.success === false`
- `response.data.message` có thông báo lỗi

### ✅ Trường hợp 3: Network error
- jQuery AJAX `error` callback được gọi
- Không có response object

### ⚠️ Trường hợp 4: Response không đúng format
- `response.success` không tồn tại
- `response.data` không tồn tại
- `response.data.formatted` không tồn tại

## Checklist Kiểm Tra

Khi test, kiểm tra:

- [ ] API trả về `status: 200`
- [ ] PHP API class trả về `$data['data']` đúng
- [ ] PHP Shipping class extract được `MONEY_TOTAL`
- [ ] PHP AJAX handler trả về `wp_send_json_success()` đúng format
- [ ] JavaScript nhận được `response.success === true`
- [ ] JavaScript có `response.data.formatted`
- [ ] JavaScript hiển thị được phí ship
- [ ] Không có lỗi trong console

## Troubleshooting

### Vấn đề: `response.data.formatted` là undefined

**Nguyên nhân có thể:**
1. PHP AJAX handler không trả về đúng format
2. `wc_price()` không format được
3. Response bị lỗi nhưng không được xử lý đúng

**Giải pháp:**
1. Kiểm tra debug logs trong PHP
2. Kiểm tra console logs trong JavaScript
3. Xem Network tab để xem raw response

### Vấn đề: Response luôn là error

**Nguyên nhân có thể:**
1. API trả về lỗi
2. `calculate_shipping_cost()` trả về `false`
3. Thiếu thông tin địa chỉ

**Giải pháp:**
1. Kiểm tra debug logs
2. Kiểm tra params được gửi
3. Kiểm tra API response

