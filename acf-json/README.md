# Hướng dẫn sử dụng ACF Fields cho trang Contact

## Các file JSON ACF đã tạo:

### 1. `group_contact_page.json`

Field group cho trang Contact Template (`template_contact.php`)

**Các trường bao gồm:**

#### Tab 1: Thông tin liên hệ

- **Tên công ty** (`contact_company_name`): Text field
- **Thông tin liên hệ** (`contact_info_repeater`): Repeater field
  - Label (Ví dụ: "Trụ sở chính:", "Chi nhánh:")
  - Danh sách thông tin (Repeater)
    - Nội dung (Textarea)
    - Link (URL)
- **Tiêu đề mạng xã hội** (`contact_social_title`): Text field
- **Liên kết mạng xã hội** (`contact_social_links`): Repeater field
  - Icon (Font Awesome class)
  - URL

#### Tab 2: Form liên hệ

- **Tiêu đề form** (`contact_form_title`): Text field
- **Mô tả form** (`contact_form_description`): Textarea

#### Tab 3: Hệ thống cửa hàng

- **Chọn hệ thống cửa hàng** (`system_shops`): Relationship field
  - Chọn các posts từ post type "shop"
  - Có thể filter theo taxonomy (shop-category, shop-area)
  - Có thể chọn nhiều shops
- **Google Map Embed URL** (`system_map_url`): URL field

### 2. `group_shop_info.json`

Field group cho post type Shop (Hệ thống công ty / cửa hàng)

**Các trường bao gồm:**

- **Địa chỉ** (`shop_address`): Text field
- **Số điện thoại** (`shop_phone`): Text field
- **Email** (`shop_email`): Email field
- **Link Google Maps** (`shop_map_link`): URL field
- **Giờ làm việc** (`shop_working_hours`): Text field

---

## Cách import và sử dụng:

### Bước 1: Import ACF Fields

**Cách 1: Tự động sync (Khuyến nghị)**

1. Vào WordPress Admin
2. Truy cập **Custom Fields > Tools**
3. Tab **Sync** sẽ hiển thị các field groups mới
4. Click **Sync** để đồng bộ các fields

**Cách 2: Import thủ công**

1. Vào WordPress Admin
2. Truy cập **Custom Fields > Tools**
3. Tab **Import Field Groups**
4. Chọn file JSON và import

### Bước 2: Tạo dữ liệu cho Shop

1. Vào **Hệ thống công ty / cửa hàng** (Shop post type)
2. Tạo các shops mới
3. Điền thông tin:
   - Tiêu đề: Tên chi nhánh
   - Địa chỉ
   - Số điện thoại
   - Email
   - Link Google Maps
   - Giờ làm việc
4. Gán taxonomy:
   - **Khu vực** (shop-area): Ví dụ: "Chi nhánh TP. Hồ Chí Minh", "Chi nhánh Hà Nội"
   - **Danh mục Ngành hàng** (shop-category): Nếu cần

### Bước 3: Cấu hình trang Contact

1. Tạo hoặc chỉnh sửa một Page
2. Chọn **Template**: "Contact Page"
3. Điền thông tin vào các tabs:
   - **Thông tin liên hệ**: Tên công ty, địa chỉ trụ sở, mạng xã hội
   - **Form liên hệ**: Tiêu đề và mô tả
   - **Hệ thống cửa hàng**: Chọn các shops từ relationship field
4. Nhập Google Map Embed URL
5. Publish/Update page

### Bước 4: Cập nhật Template

Sử dụng code trong file `template_contact_example.php` để thay thế nội dung hardcode trong `template_contact.php`

**Hoặc:**

Copy code từ `template_contact_example.php` sang `template_contact.php`

---

## Code snippets hữu ích:

### Lấy danh sách shops từ relationship field:

```php
$shops = get_field('system_shops');
if ($shops) {
    foreach ($shops as $shop) {
        echo $shop->post_title; // Tên shop
        $address = get_field('shop_address', $shop->ID);
        $phone = get_field('shop_phone', $shop->ID);
    }
}
```

### Group shops theo khu vực (taxonomy):

```php
$shops = get_field('system_shops');
$shops_by_area = array();

foreach ($shops as $shop) {
    $areas = get_the_terms($shop->ID, 'shop-area');
    $area_name = $areas ? $areas[0]->name : 'Khác';

    if (!isset($shops_by_area[$area_name])) {
        $shops_by_area[$area_name] = array();
    }

    $shops_by_area[$area_name][] = $shop;
}
```

### Lấy thông tin liên hệ:

```php
if (have_rows('contact_info_repeater')) {
    while (have_rows('contact_info_repeater')) {
        the_row();
        $label = get_sub_field('label');

        if (have_rows('items')) {
            while (have_rows('items')) {
                the_row();
                $text = get_sub_field('text');
                $link = get_sub_field('link');
            }
        }
    }
}
```

---

## Lưu ý:

1. **ACF Pro** cần được cài đặt và kích hoạt
2. Post type "shop" và taxonomies "shop-area", "shop-category" đã được đăng ký trong `inc/function-custom.php`
3. Các field names không nên thay đổi để tránh lỗi khi sync
4. Nếu cần thêm fields mới, export lại JSON sau khi thay đổi

---

## Hỗ trợ:

Nếu gặp vấn đề, kiểm tra:

- ACF Pro version >= 5.0
- PHP version >= 7.4
- WordPress version >= 5.0
