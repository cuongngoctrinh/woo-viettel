# WooCommerce ViettelPost Shipping Plugin

Plugin tích hợp ViettelPost với WooCommerce để tính phí ship tự động và quản lý đơn hàng.

## Tính năng

- ✅ Tính phí ship tự động từ API ViettelPost
- ✅ Tự động tính phí khi khách hàng chọn địa chỉ trong checkout
- ✅ Custom order status "Chờ duyệt" cho đơn hàng
- ✅ Admin có thể duyệt đơn hàng trước khi gửi lên ViettelPost
- ✅ Tự động gửi đơn hàng lên ViettelPost khi được duyệt
- ✅ Quản lý thông tin người gửi trong admin
- ✅ Hiển thị mã đơn ViettelPost trong order details

## Cài đặt

1. Copy thư mục `woocommerce-viettelpost` vào `wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin
3. Vào **WooCommerce > ViettelPost** để cấu hình

## Cấu hình

### 1. Thông tin API

- **Username**: Tài khoản API ViettelPost
- **Password**: Mật khẩu API ViettelPost
- **API URL**: URL API (mặc định: https://partner.viettelpost.vn/v2/)

### 2. Thông tin người gửi

Điền đầy đủ thông tin người gửi:
- Tên người gửi
- Số điện thoại
- Email
- Địa chỉ
- Tỉnh/Thành phố
- Quận/Huyện
- Phường/Xã

### 3. Cấu hình Shipping Method

1. Vào **WooCommerce > Settings > Shipping**
2. Tạo hoặc chỉnh sửa Shipping Zone
3. Thêm **ViettelPost** vào zone
4. Cấu hình tiêu đề và trạng thái thuế

## Quy trình hoạt động

1. **Khách hàng thêm giỏ hàng** → Chọn sản phẩm
2. **Thanh toán** → Điền thông tin địa chỉ
3. **Chọn tỉnh/thành** → Plugin tự động tính phí ship từ ViettelPost
4. **Hoàn tất đơn hàng** → Đơn hàng ở trạng thái "Chờ duyệt"
5. **Admin duyệt đơn hàng** → Chuyển sang "Processing"
6. **Tự động gửi lên ViettelPost** → Lưu mã đơn ViettelPost

## Yêu cầu

- WordPress 5.0+
- WooCommerce 5.0+
- PHP 7.2+
- Tài khoản API ViettelPost

## Cấu trúc Plugin

```
woocommerce-viettelpost/
├── woocommerce-viettelpost.php    # Main plugin file
├── includes/
│   ├── class-viettelpost-api.php          # API handler
│   ├── class-viettelpost-shipping.php     # Shipping method
│   ├── class-viettelpost-order-status.php # Custom order status
│   ├── class-viettelpost-admin.php        # Admin interface
│   └── class-viettelpost-frontend.php     # Frontend scripts
├── assets/
│   └── js/
│       └── viettelpost-checkout.js        # Checkout script
└── README.md
```

## Hỗ trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. WooCommerce đã được kích hoạt
2. Thông tin API đã được cấu hình đúng
3. Thông tin người gửi đã được điền đầy đủ
4. Shipping method đã được thêm vào shipping zone

## License

GPL v2 or later

