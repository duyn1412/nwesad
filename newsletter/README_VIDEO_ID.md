# Thêm cột VIDEO_ID vào Newsletter System

## Vấn đề
Lỗi "Unknown column 'VIDEO_ID' in 'field list'" xảy ra vì cột `VIDEO_ID` chưa được thêm vào bảng `new_settings` trong database.

## Giải pháp

### Bước 1: Thêm cột vào database
Chạy script `add_video_id_column.php` để thêm cột `VIDEO_ID` vào bảng `new_settings`:

```
http://yourdomain.com/nwesadmin/newsletter/add_video_id_column.php
```

Script này sẽ:
- Kết nối database
- Kiểm tra xem cột `VIDEO_ID` đã tồn tại chưa
- Thêm cột nếu chưa có
- Hiển thị cấu trúc bảng hiện tại

### Bước 2: Khôi phục chức năng VIDEO_ID
Sau khi cột được thêm vào database, chạy script `restore_video_id_functionality.php`:

```
http://yourdomain.com/nwesadmin/newsletter/restore_video_id_functionality.php
```

Script này sẽ:
- Cập nhật `get-newsletter-settings.php` để lấy `VIDEO_ID`
- Cập nhật `newsletter-settings.php` để lưu `VIDEO_ID`
- Khôi phục đầy đủ chức năng cho trường Video ID

### Bước 3: Kiểm tra
Sau khi hoàn thành, form newsletter sẽ:
- Hiển thị trường Video ID
- Lưu giá trị vào database
- Lấy giá trị từ database để hiển thị

## Cấu trúc cột mới
- **Tên cột**: `VIDEO_ID`
- **Kiểu dữ liệu**: `VARCHAR(255)`
- **Giá trị mặc định**: `NULL`
- **Vị trí**: Sau cột `LINK_OG_IMG_3`

## Lưu ý
- Chạy script theo đúng thứ tự
- Backup database trước khi thực hiện
- Kiểm tra quyền ghi file nếu có lỗi khi cập nhật code

## Troubleshooting
Nếu gặp lỗi:
1. Kiểm tra kết nối database
2. Kiểm tra quyền truy cập bảng `new_settings`
3. Kiểm tra quyền ghi file trong thư mục newsletter
