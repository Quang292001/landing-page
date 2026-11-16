# MỘC Studio — WordPress theme

Chuyển từ landing page tĩnh ở thư mục gốc sang custom theme WordPress.

## Cài đặt

1. Nén thư mục `moc-studio/` thành `moc-studio.zip` (hoặc copy vào `wp-content/themes/`).
2. **Giao diện → Giao diện → Thêm mới → Tải lên**, rồi kích hoạt.
3. **Cài đặt → Đọc**: chọn "Trang chủ hiển thị: Một trang tĩnh" (trang nào cũng được). `front-page.php` sẽ tự được dùng.
4. **Cài đặt → Tổng quan**: đặt Tên trang = "Mộc Studio" (logo tự viết hoa), Khẩu hiệu = mô tả SEO.

## Sửa nội dung

| Nội dung | Ở đâu |
| --- | --- |
| Bảng giá 3 gói, link Zalo/TikTok/mạng xã hội | Giao diện → Tùy biến → **MỘC Studio** |
| Bộ ảnh (tên, nhãn, ảnh bìa, danh sách ảnh, thứ tự) | Menu **Bộ ảnh** ở cột trái admin |
| Menu | Giao diện → Menu, vị trí "Menu chính" — dùng Liên kết tự tạo `/#bo-anh`, `/#dich-vu`... |
| Favicon | Giao diện → Tùy biến → Thông tin trang → Biểu tượng trang |

Chưa có bộ ảnh nào thì trang hiển thị 3 bộ ảnh mẫu nằm trong `assets/images/`.

## Cấu trúc

```
moc-studio/
├── style.css            # Header khai báo theme + toàn bộ CSS
├── functions.php        # Theme support, menu, wp_enqueue_*, thẻ <head>, hàm tiện ích
├── header.php           # <head>, wp_head(), thanh menu
├── footer.php           # Footer, popup xem ảnh, nút liên hệ nổi, wp_footer()
├── front-page.php       # Landing page (trang chủ)
├── index.php            # Template dự phòng bắt buộc
├── inc/customizer.php   # Bảng giá + link liên hệ (theme_mod)
├── inc/album-cpt.php    # Custom post type moc_album + meta box
└── assets/
    ├── js/main.js           # Lightbox, form, hiện dần khi cuộn (JS thuần)
    ├── js/scroll-effects.js # Menu/thanh tiến trình/scrollspy/parallax (jQuery)
    ├── js/admin-album.js    # Chọn ảnh từ Thư viện Media trong admin (jQuery + wp.media)
    └── images/
```

Form đặt lịch vẫn chỉ kiểm tra phía trình duyệt như bản tĩnh, chưa gửi dữ liệu đi đâu.
