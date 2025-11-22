# Test Cases - ViettelPost Address Fields & Shipping Calculation

## Mục đích
Kiểm thử các chức năng đã sửa để đảm bảo:
1. Các trường địa chỉ không bị reset khi chọn quận/huyện
2. Chỉ tính phí ship khi đủ 3 trường (Tỉnh, Quận, Phường)
3. Không có vòng lặp vô hạn
4. Tính phí ship hoạt động đúng

---

## Test Case 1: Tỉnh/Thành phố không bị reset khi chọn Quận/Huyện

### Mô tả
Khi người dùng đã chọn Tỉnh/Thành phố, sau đó chọn Quận/Huyện, Tỉnh/Thành phố không được reset về trống.

### Các bước thực hiện:
1. Mở trang checkout
2. Chọn **Tỉnh/Thành phố** (ví dụ: "Hồ Chí Minh")
3. Chọn **Quận/Huyện** (ví dụ: "QUẬN 8")
4. Kiểm tra giá trị **Tỉnh/Thành phố** vẫn là "Hồ Chí Minh"

### Kết quả mong đợi:
- ✅ Tỉnh/Thành phố vẫn giữ nguyên giá trị đã chọn
- ✅ Quận/Huyện hiển thị đúng giá trị đã chọn
- ✅ Không có lỗi trong console

### Cách kiểm tra:
```javascript
// Mở Console (F12) và chạy:
console.log('Tỉnh:', $('#billing_state').val());
console.log('Quận:', $('#billing_city').val());
// Sau khi chọn quận, kiểm tra lại:
console.log('Tỉnh sau khi chọn quận:', $('#billing_state').val());
```

---

## Test Case 2: Chỉ tính phí ship khi đủ 3 trường

### Mô tả
Hệ thống chỉ tính phí ship khi người dùng đã điền đầy đủ Tỉnh/Thành phố, Quận/Huyện, và Phường/Xã.

### Scenario 2.1: Thiếu Tỉnh/Thành phố
1. Chọn **Quận/Huyện** (ví dụ: "QUẬN 8")
2. Chọn **Phường/Xã** (ví dụ: "PHƯỜNG 14")
3. **KHÔNG** chọn Tỉnh/Thành phố

**Kết quả mong đợi:**
- ❌ Không tính phí ship
- ❌ Không có thông báo lỗi "Lỗi khi tính phí ship"
- ✅ Console log: "Vui lòng điền đầy đủ Tỉnh/Thành phố, Quận/Huyện và Phường/Xã để tính phí ship"

### Scenario 2.2: Thiếu Quận/Huyện
1. Chọn **Tỉnh/Thành phố** (ví dụ: "Hồ Chí Minh")
2. Chọn **Phường/Xã** (ví dụ: "PHƯỜNG 14")
3. **KHÔNG** chọn Quận/Huyện

**Kết quả mong đợi:**
- ❌ Không tính phí ship
- ❌ Không có thông báo lỗi "Lỗi khi tính phí ship"

### Scenario 2.3: Thiếu Phường/Xã
1. Chọn **Tỉnh/Thành phố** (ví dụ: "Hồ Chí Minh")
2. Chọn **Quận/Huyện** (ví dụ: "QUẬN 8")
3. **KHÔNG** chọn Phường/Xã

**Kết quả mong đợi:**
- ❌ Không tính phí ship
- ❌ Không có thông báo lỗi "Lỗi khi tính phí ship"

### Scenario 2.4: Đủ cả 3 trường
1. Chọn **Tỉnh/Thành phố** (ví dụ: "Hồ Chí Minh")
2. Chọn **Quận/Huyện** (ví dụ: "QUẬN 8")
3. Chọn **Phường/Xã** (ví dụ: "PHƯỜNG 14")

**Kết quả mong đợi:**
- ✅ Hiển thị "Đang tính phí ship..."
- ✅ Tính phí ship thành công
- ✅ Hiển thị phí ship hoặc thông báo lỗi từ API (nếu có)

---

## Test Case 3: Không có vòng lặp vô hạn

### Mô tả
Kiểm tra xem có vòng lặp vô hạn khi các event handlers trigger lẫn nhau không.

### Các bước thực hiện:
1. Mở trang checkout
2. Mở Console (F12) và theo dõi các log
3. Chọn **Tỉnh/Thành phố**
4. Chọn **Quận/Huyện**
5. Chọn **Phường/Xã**
6. Quan sát Console và Network tab

### Kết quả mong đợi:
- ✅ Không có quá nhiều request AJAX lặp lại
- ✅ Không có quá nhiều event "updated_checkout" được trigger
- ✅ Console không có lỗi lặp lại
- ✅ Các trường không bị reset liên tục

### Cách kiểm tra:
```javascript
// Đếm số lần updated_checkout được trigger
var updateCount = 0;
$(document.body).on('updated_checkout', function() {
  updateCount++;
  console.log('updated_checkout triggered:', updateCount);
});

// Sau khi thao tác, kiểm tra:
console.log('Total updates:', updateCount);
// Nên < 5 lần cho một thao tác bình thường
```

---

## Test Case 4: Tính phí ship không bị gọi nhiều lần

### Mô tả
Khi đủ 3 trường, tính phí ship chỉ được gọi 1 lần, không lặp lại.

### Các bước thực hiện:
1. Mở trang checkout
2. Mở Network tab (F12) và filter "viettelpost_calculate_shipping"
3. Chọn đầy đủ **Tỉnh/Thành phố**, **Quận/Huyện**, **Phường/Xã**
4. Quan sát số lượng request AJAX

### Kết quả mong đợi:
- ✅ Chỉ có 1 request "viettelpost_calculate_shipping" được gửi
- ✅ Không có nhiều request lặp lại
- ✅ Không có nhiều thông báo "Lỗi khi tính phí ship"

### Cách kiểm tra:
```javascript
// Đếm số lần calculateShipping được gọi
var calculateCount = 0;
// Thêm vào đầu hàm calculateShipping:
calculateCount++;
console.log('calculateShipping called:', calculateCount);
```

---

## Test Case 5: Restore giá trị sau khi checkout update

### Mô tả
Sau khi checkout được update, các giá trị đã chọn được restore đúng.

### Các bước thực hiện:
1. Chọn đầy đủ **Tỉnh/Thành phố**, **Quận/Huyện**, **Phường/Xã**
2. Thêm sản phẩm vào giỏ hàng (nếu chưa có)
3. Chờ checkout update tự động
4. Kiểm tra các giá trị vẫn giữ nguyên

### Kết quả mong đợi:
- ✅ Tỉnh/Thành phố vẫn giữ nguyên
- ✅ Quận/Huyện vẫn giữ nguyên
- ✅ Phường/Xã vẫn giữ nguyên
- ✅ Không bị reset về trống

---

## Test Case 6: Xử lý lỗi khi tính phí ship

### Mô tả
Khi API trả về lỗi, hệ thống xử lý đúng và không lặp lại request.

### Scenario 6.1: API trả về lỗi
1. Chọn đầy đủ 3 trường
2. Giả lập API trả về lỗi (hoặc dùng địa chỉ không hợp lệ)
3. Quan sát thông báo lỗi

**Kết quả mong đợi:**
- ✅ Hiển thị thông báo lỗi rõ ràng
- ✅ Không lặp lại request lỗi
- ✅ Không có nhiều thông báo lỗi giống nhau

### Scenario 6.2: Network error
1. Tắt mạng hoặc chặn request
2. Chọn đầy đủ 3 trường
3. Quan sát xử lý lỗi

**Kết quả mong đợi:**
- ✅ Hiển thị thông báo lỗi network
- ✅ Không lặp lại request
- ✅ Console có log lỗi

---

## Test Case 7: Kiểm tra flag isRestoringValues

### Mô tả
Flag `isRestoringValues` hoạt động đúng để tránh vòng lặp.

### Các bước thực hiện:
1. Mở Console
2. Thêm log vào code:
```javascript
// Thêm vào đầu updated_checkout handler:
console.log('isRestoringValues:', isRestoringValues);
```
3. Thực hiện các thao tác chọn địa chỉ
4. Quan sát log

### Kết quả mong đợi:
- ✅ Flag được set `true` khi đang restore
- ✅ Flag được set `false` sau khi restore xong
- ✅ Event handlers bỏ qua khi flag = true

---

## Test Case 8: Kiểm tra lastRestoredValues

### Mô tả
Object `lastRestoredValues` lưu trữ đúng giá trị đã restore.

### Các bước thực hiện:
1. Chọn đầy đủ 3 trường
2. Trong Console, kiểm tra:
```javascript
// Thêm vào cuối updated_checkout handler:
console.log('lastRestoredValues:', lastRestoredValues);
```
3. Thay đổi một trường
4. Kiểm tra lại

### Kết quả mong đợi:
- ✅ `lastRestoredValues` lưu đúng giá trị
- ✅ Chỉ restore khi giá trị thay đổi
- ✅ Không restore khi giá trị giống nhau

---

## Checklist Tổng Hợp

Trước khi kết thúc, đảm bảo tất cả các test case sau đều PASS:

- [ ] Test Case 1: Tỉnh không bị reset khi chọn Quận
- [ ] Test Case 2.1: Không tính ship khi thiếu Tỉnh
- [ ] Test Case 2.2: Không tính ship khi thiếu Quận
- [ ] Test Case 2.3: Không tính ship khi thiếu Phường
- [ ] Test Case 2.4: Tính ship khi đủ 3 trường
- [ ] Test Case 3: Không có vòng lặp vô hạn
- [ ] Test Case 4: Tính ship chỉ gọi 1 lần
- [ ] Test Case 5: Restore giá trị đúng
- [ ] Test Case 6: Xử lý lỗi đúng
- [ ] Test Case 7: Flag isRestoringValues hoạt động
- [ ] Test Case 8: lastRestoredValues lưu đúng

---

## Các Lỗi Thường Gặp và Cách Sửa

### Lỗi: "Lỗi khi tính phí ship" lặp lại nhiều lần
**Nguyên nhân:** `checkAndCalculateShipping()` được gọi nhiều lần
**Giải pháp:** Thêm debounce/throttle hoặc flag để tránh gọi nhiều lần

### Lỗi: Tỉnh bị reset khi chọn Quận
**Nguyên nhân:** `loadProvinces()` được gọi và reset HTML
**Giải pháp:** Chỉ load provinces khi chưa có options, và restore giá trị sau khi load

### Lỗi: Vòng lặp vô hạn
**Nguyên nhân:** Event handlers trigger lẫn nhau
**Giải pháp:** Sử dụng flag `isRestoringValues` và native JavaScript để set value

---

## Ghi Chú

- Tất cả các test case nên được chạy trên trình duyệt Chrome/Firefox với DevTools mở
- Kiểm tra cả Console và Network tab
- Test trên cả billing và shipping address
- Test với các trình duyệt khác nhau nếu có thể

