<?php
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

function theme_remove_woocommerce_hooks()
{
    // Global
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

    // Single Product
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_after_single_product', 'woocommerce_output_related_products', 20);
    remove_action('woocommerce_after_single_product', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);


    // Archive Product
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
    // remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);


    add_action('woocommerce_before_shop_top_filter', 'woocommerce_catalog_ordering', 30);
}
add_action('init', 'theme_remove_woocommerce_hooks');



// Remove default variation select
function remove_variation_dropdown_style()
{
?>
<style>
.variations select,
.variations .label {
	display: none !important;
}

.reset_variations {
	display: none !important;
}

.woocommerce-variation-price {
	display: none !important;
}
</style>
<?php
}
add_action('wp_head', 'remove_variation_dropdown_style');


add_filter('woocommerce_dropdown_variation_attribute_options_args', 'auto_select_first_variation', 10, 1);
function auto_select_first_variation($args)
{
    $params = 'attribute_' . $args['attribute'];
    $currentUrl = $_SERVER['REQUEST_URI'];
    $active_first_variant = strpos($currentUrl, $params);
    if (count($args['options']) > 0)
        if ($active_first_variant === false)
            $args['selected'] = $args['options'][0];
    return $args;
}



add_filter('woocommerce_available_variation', 'add_parent_gallery_to_variation', 10, 3);
function add_parent_gallery_to_variation($variation_data, $product, $variation)
{
    $thumbnail_id = $variation->get_image_id();

    $parent_id = $variation->get_parent_id();
    $parent_product = wc_get_product($parent_id);

    $gallery_image_ids = $parent_product->get_gallery_image_ids();
    $gallery_image_ids = array_filter(array_merge([$thumbnail_id], $gallery_image_ids));
    $gallery = [];
    foreach ($gallery_image_ids as $image_id) {
        $gallery[] = [
            'url' => wp_get_attachment_url($image_id),
            'thumbnail' => wp_get_attachment_image_url($image_id, 'woocommerce_gallery_thumbnail'),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
        ];
    }

    $variation_data['parent_gallery'] = $gallery;

    return $variation_data;
}


// Hiển thị field trong từng variation
// add_action('woocommerce_variation_options_pricing', 'add_custom_variation_fields', 10, 3);

function add_custom_variation_fields($loop, $variation_data, $variation)
{
    $custom_variation_name = get_post_meta($variation->ID, 'custom_variation_name', true);
    echo '<div class="custom_variation_fields">';
    woocommerce_wp_text_input(
        array(
            'id' => "custom_variation_name_{$loop}",
            'name' => "custom_variation_name[{$variation->ID}]",
            'value' => $custom_variation_name,
            'label' => __('Tên riêng:', 'woocommerce'),
            'wrapper_class' => 'form-row form-row-full',
        )
    );
    echo '</div>';
}

// Lưu dữ liệu khi cập nhật variation
add_action('woocommerce_save_product_variation', 'save_custom_variation_fields', 12, 2);

function save_custom_variation_fields($variation_id, $i)
{
    if (isset($_POST['custom_variation_name'][$variation_id])) {
        update_post_meta(
            $variation_id,
            'custom_variation_name',
            sanitize_text_field($_POST['custom_variation_name'][$variation_id])
        );
    }
}

add_action('woocommerce_admin_process_variation_object', function ($variation, $i) {
    if ($variation->get_regular_price() === '') {
        $variation->set_regular_price(0);
    }

    if ($variation->get_sale_price() === '') {
        $variation->set_sale_price('');
    }
}, 10, 2);

add_action('woocommerce_update_product_variation', 'save_custom_variation_fields_fallback', 10, 1);

function save_custom_variation_fields_fallback($variation_id)
{
    // Tìm index của variation trong form
    $variations = $_POST['variable_post_id'] ?? array();
    $i = array_search($variation_id, $variations);

    if ($i !== false) {
        error_log('Fallback save for variation ID: ' . $variation_id . ', Index: ' . $i);
        save_custom_variation_fields($variation_id, $i);
    }
}

add_filter('woocommerce_show_variation_price', '__return_true');


add_filter('woocommerce_available_variation', function ($variation) {
    $variation_id = $variation['variation_id'];
    $product_id = wp_get_post_parent_id($variation_id);
    $product = wc_get_product($product_id);
    $product_name = $product->get_name();

    $variation['title_product'] = !empty($product_name) ? $product_name : '';
    return $variation;
});


// Add product variations ACF rule
// add_filter('acf/location/rule_values/post_type', 'acf_location_rule_values_Post');
// function acf_location_rule_values_Post($choices)
// {
//     $choices['product_variation'] = 'Product Variation';
//     return $choices;
// }

// $GLOBALS['wc_loop_variation_id'] = null;

// function is_field_group_for_variation($field_group, $variation_data, $variation_post)
// {
//     return (preg_match('/Variation/i', $field_group['title']) == true);
// }

// add_action('woocommerce_product_after_variable_attributes', function ($loop_index, $variation_data, $variation_post) {
//     $GLOBALS['wc_loop_variation_id'] = $variation_post->ID;

//     foreach (acf_get_field_groups() as $field_group) {
//         if (is_field_group_for_variation($field_group, $variation_data, $variation_post)) {
//             acf_render_fields($variation_post->ID, acf_get_fields($field_group));
//         }
//     }

//     $GLOBALS['wc_loop_variation_id'] = null;
// }, 10, 3);

// add_action('woocommerce_save_product_variation', function ($variation_id, $loop_index) {
//     if (!isset($_POST['acf_variation'][$variation_id])) {
//         return;
//     }
//     if (!empty($_POST['acf_variation'][$variation_id]) && is_array($fields = $_POST['acf_variation'][$variation_id])) {
//         foreach ($fields as $key => $val) {
//             update_field($key, $val, $variation_id);
//         }
//     }
// }, 10, 2);

// add_filter('acf/prepare_field', function ($field) {
//     if (!$GLOBALS['wc_loop_variation_id']) {
//         return $field;
//     }

//     $field['name'] = preg_replace('/^acf\[/', 'acf_variation[' . $GLOBALS['wc_loop_variation_id'] . '][', $field['name']);

//     return $field;
// }, 10, 1);



function ajax_get_variation_acf_fields_color()
{
    $parent_id = isset($_POST['parent_id']) ? absint($_POST['parent_id']) : 0;
    $color = isset($_POST['color']) ? sanitize_text_field($_POST['color']) : '';

    if (!$parent_id || !$color) {
        wp_send_json_error(['message' => 'Thiếu parent_id hoặc color']);
    }

    $args = array(
        'post_type'      => 'product_variation',
        'posts_per_page' => -1,
        'post_parent'    => $parent_id,
        'meta_query'     => array(
            array(
                'key'   => 'attribute_pa_mau-sac',
                'value' => $color,
            ),
        ),
    );

    $query = new WP_Query($args);
    $variation_albums = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $variation_id = get_the_ID();
            $album = get_field('variation_album', $variation_id);
            if (!empty($album)) {
                $variation_albums[] = $album;
            }
        }
        wp_reset_postdata();
    }

    wp_send_json_success($variation_albums);
}
add_action('wp_ajax_get_variation_acf_fields_color', 'ajax_get_variation_acf_fields_color');
add_action('wp_ajax_nopriv_get_variation_acf_fields_color', 'ajax_get_variation_acf_fields_color');



// add_action('admin_init', 'transfer_variation_images_to_acf_gallery');
function transfer_variation_images_to_acf_gallery()
{
    // Lấy tất cả sản phẩm biến thể
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_type',
                'field' => 'slug',
                'terms' => 'variable',
            ),
        ),
    );
    $products = new WP_Query($args);

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);

            // Lấy tất cả variations của sản phẩm
            $variations = $product->get_available_variations();
            foreach ($variations as $variation) {
                $variation_id = $variation['variation_id'];
                // Lấy dữ liệu từ rtwpvg_images
                $image_ids_data = get_post_meta($variation_id, 'rtwpvg_images', true);

                $image_ids = array();
                if (is_array($image_ids_data)) {
                    $image_ids = array_map('absint', $image_ids_data);
                } elseif (is_string($image_ids_data) && !empty($image_ids_data)) {
                    $image_ids = array_map('absint', explode(',', $image_ids_data));
                }

                // Nếu có ID hình ảnh, gán vào trường ACF Gallery
                if (!empty($image_ids)) {
                    update_field('variation_album', $image_ids, $variation_id);
                }
            }
        }
        wp_reset_postdata();
    }
}



// 

add_filter('woocommerce_get_price_html', 'custom_woocommerce_get_price_html', 10, 2);

function custom_woocommerce_get_price_html($price, $product)
{
    // Variable product
    if ($product->is_type('variable')) {

        $prices = $product->get_variation_prices(true);

        if (!empty($prices['price'])) {

            $min_price = current($prices['price']);
            $max_price = end($prices['price']);

            if ($min_price !== $max_price) {
                $price = wc_price($min_price) . ' - ' . wc_price($max_price);
            } else {
                $price = wc_price($min_price);
            }
        }
    }

    // Sale product
    elseif ($product->is_on_sale()) {

        $regular_price = wc_price($product->get_regular_price());
        $sale_price = wc_price($product->get_sale_price());

        $price = '<del>' . $regular_price . '</del> <ins>' . $sale_price . '</ins>';
    }

    // Normal product
    else {

        if ($product->get_price() > 0) {
            $price = wc_price($product->get_price());
        } else {
            $price = __('Liên hệ', 'canhcamtheme');
        }
    }

    // Không phải trang single product
    if (!is_product()) {
        return $price;
    }

    // Single product
    return '
    <div class="price-product">
        <div class="label">' . __('Price:', 'canhcamtheme') . '</div>
        <div class="price">' . $price . '</div>
    </div>';
}



// ================================================
// Custom Catalog Sorting
// ================================================

/**
 * Đăng ký các tùy chọn sắp xếp trong dropdown của WooCommerce.
 */
add_filter('woocommerce_catalog_orderby', 'custom_translate_woocommerce_orderby');
add_filter('woocommerce_default_catalog_orderby_options', 'custom_translate_woocommerce_orderby');
function custom_translate_woocommerce_orderby($sortby)
{
    $sortby = array();

    $sortby['featured']   = __('Featured Products', 'canhcamtheme');
    $sortby['sales']      = __('Best Selling Products', 'canhcamtheme');
    $sortby['favorite']   = __('Favorite Products', 'canhcamtheme');
    $sortby['price-desc'] = __('Products from High to Low', 'canhcamtheme');

    return $sortby;
}


/**
 * Build WP_Query args chuẩn cho từng kiểu sắp xếp.
 *
 * Lý do dùng WooCommerce native thay vì ACF boolean:
 *  - ACF boolean không tự cập nhật theo dữ liệu bán hàng thật.
 *  - `total_sales` được WooCommerce tự tăng khi order hoàn thành → dữ liệu chính xác.
 *  - `product_visibility` taxonomy là chuẩn WC, tương thích mọi plugin/query.
 *  - Boolean meta không có index hiệu quả → chậm khi catalog lớn.
 *
 * @param string $orderby_value  Giá trị từ $_GET['orderby']
 * @param array  $args           WP_Query args hiện tại (dùng khi merge với facetwp_query_args)
 * @return array                 Modified WP_Query args
 */
function custom_build_sort_args($orderby_value, $args = array())
{
    // Reset các key có thể gây conflict từ query trước
    unset($args['meta_key'], $args['_custom_sort_featured']);

    switch ($orderby_value) {

        /**
         * FEATURED: Taxonomy 'product_visibility', term slug = 'featured'
         *
         * WooCommerce lưu featured tại:
         *   wp_term_relationships (object_id = post_id, term_taxonomy_id = ID của term 'featured')
         *   wp_term_taxonomy (taxonomy = 'product_visibility')
         *   wp_terms (slug = 'featured')
         *
         * posts_clauses sẽ LEFT JOIN để ưu tiên featured lên đầu, vẫn hiển thị toàn bộ sản phẩm.
         */
        case 'featured':
            $args['_custom_sort_featured'] = true;
            $args['orderby'] = 'menu_order';
            $args['order']   = 'ASC';
            break;

        /**
         * BEST SELLING: Meta key 'total_sales' (WooCommerce native)
         *
         * WooCommerce lưu tại:
         *   wp_postmeta: meta_key = 'total_sales', meta_value = số đơn hàng hoàn thành
         *   Tự động cập nhật qua woocommerce_product_set_stock / order completion hooks.
         */
        case 'sales':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        /**
         * FAVORITE: TODO — cần xác định business logic trước khi implement.
         *
         * Logic cũ (ACF boolean `product_favorite`) đã bị loại bỏ vì:
         *   - Không phản ánh dữ liệu tương tác thực tế của user
         *   - Không tự động cập nhật
         *   - Khó scale khi catalog lớn
         *
         * Hướng refactor đề xuất:
         *   Option A: Custom taxonomy 'product_favorite' (tốt nhất cho lọc & indexing)
         *   Option B: Post meta '_favorite_count' tổng hợp từ wishlist
         *   Option C: Tích hợp với plugin wishlist nếu có
         *
         * Tạm fallback về menu_order trong khi chờ quyết định.
         */
        case 'favorite':
            /*
            // TODO: Implement after deciding favorite data source
            $args['meta_key'] = '_favorite_count';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            */
            $args['orderby'] = 'menu_order';
            $args['order']   = 'ASC';
            break;

        /**
         * PRICE DESC: Meta key '_price' (WooCommerce native)
         *
         * WooCommerce lưu tại wp_postmeta: meta_key = '_price'
         * Với variable product, '_price' = giá thấp nhất trong các variation.
         */
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;

        default:
            $args['orderby'] = 'menu_order';
            $args['order']   = 'ASC';
            break;
    }

    return $args;
}


/**
 * pre_get_posts: Áp dụng custom sort cho page load (non-FacetWP AJAX).
 *
 * Khi FacetWP đang xử lý AJAX request, hook này bị bỏ qua để tránh conflict.
 * facetwp_query_args filter sẽ đảm nhiệm thay.
 */
add_action('pre_get_posts', 'custom_orderby_featured_sales_favorite');
function custom_orderby_featured_sales_favorite($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // FacetWP AJAX: nhường quyền cho facetwp_query_args để tránh double-apply
    if (defined('FACETWP_DOING_AJAX') && FACETWP_DOING_AJAX) {
        return;
    }

    if (!(is_shop() || is_product_category() || is_product_tag())) {
        return;
    }

    $orderby_value = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'featured';
    $args = custom_build_sort_args($orderby_value);

    foreach ($args as $key => $value) {
        $query->set($key, $value);
    }
}


/**
 * facetwp_query_args: Áp dụng custom sort khi FacetWP rebuild query qua AJAX.
 *
 * Đây là hook chính thức của FacetWP để modify WP_Query args.
 * Không dùng pre_get_posts cho AJAX request vì FacetWP có thể override.
 *
 * @see https://facetwp.com/help-center/developers/hooks/facetwp_query_args/
 */
add_filter('facetwp_query_args', 'custom_facetwp_sort_args', 10, 2);
function custom_facetwp_sort_args($args, $class)
{
    $orderby_value = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'featured';
    return custom_build_sort_args($orderby_value, $args);
}


/**
 * posts_clauses: Xử lý 'featured first' bằng cách LEFT JOIN taxonomy product_visibility.
 *
 * Tại sao cần posts_clauses thay vì tax_query thông thường?
 *  - tax_query lọc (WHERE), không sort → chỉ hiện featured, không hiện toàn bộ.
 *  - posts_clauses cho phép thêm JOIN + ORDER BY tùy chỉnh vào SQL cuối.
 *  - LEFT JOIN: lấy tất cả sản phẩm; CASE WHEN: đẩy featured lên đầu (0 trước 1).
 *
 * Chỉ kích hoạt khi query có flag '_custom_sort_featured' (set bởi custom_build_sort_args).
 */
add_filter('posts_clauses', 'custom_featured_sort_clauses', 10, 2);
function custom_featured_sort_clauses($clauses, $query)
{
    if (!$query->get('_custom_sort_featured')) {
        return $clauses;
    }

    global $wpdb;

    // Tránh JOIN trùng lặp (FacetWP có thể chạy nhiều pass trong một request)
    if (strpos($clauses['join'], 'tr_feat_vis') !== false) {
        return $clauses;
    }

    $featured_term = get_term_by('slug', 'featured', 'product_visibility');

    if (!$featured_term) {
        return $clauses;
    }

    $ttid = (int) $featured_term->term_taxonomy_id;

    $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS tr_feat_vis
                          ON ({$wpdb->posts}.ID = tr_feat_vis.object_id
                          AND tr_feat_vis.term_taxonomy_id = {$ttid})";

    $clauses['orderby'] = "CASE WHEN tr_feat_vis.object_id IS NOT NULL THEN 0 ELSE 1 END ASC,
                           {$wpdb->posts}.menu_order ASC";

    return $clauses;
}



add_filter('loop_shop_per_page', 'custom_products_per_page', 20);
function custom_products_per_page($cols)
{
    if (wp_is_mobile()) {
        return 10; // mobile
    } else {
        return 9; // desktop
    }
}



// Thêm select chọn số sản phẩm / trang
// add_action('woocommerce_before_shop_loop', 'custom_woo_products_per_page', 15);
function custom_woo_products_per_page()
{
    $current_ppp = isset($_GET['ppp']) ? (int) $_GET['ppp'] : get_option('posts_per_page');
    $options = array(12, 24, 36, 48);
    echo '<form method="get" class="woocommerce-per-page view-filter end-item max-lg:mb-5">';
    echo '<label class="text-18 mr-3 whitespace-nowrap">' . __('Hiển thị', 'canhcamtheme') . '</label>';
    echo '<div class="custom-select">';
    echo '<select name="ppp" onchange="this.form.submit()">';
    foreach ($options as $option) {
        $selected = selected($current_ppp, $option, false);
        echo "<option value='{$option}' {$selected}>{$option}</option>";
    }
    echo '</select>';
    echo '</div>';
    // Giữ lại query string khác (ví dụ orderby, filter)
    foreach ($_GET as $key => $val) {
        if ('ppp' === $key) continue;
        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    }

    echo '</form>';
}

// Áp dụng số sản phẩm / trang
add_filter('loop_shop_per_page', function ($cols) {
    return isset($_GET['ppp']) ? (int) $_GET['ppp'] : $cols;
}, 20);


function filter_product_by_category()
{
    $category_id = isset($_POST['categoryId']) ? (int) $_POST['categoryId'] : 0;
    if ($category_id) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 8,
            'tax_query' => array(
                array(
                    'taxoinomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id,
                ),
            ),
        );
        $products = new WP_Query($args);
        $html = '';
        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $html .= get_template_part('components/content-product-3');
            }
        } else {
            $html = '<div class="text-center">' . __('Sản phẩm đang cập nhật', 'canhcamtheme') . '</div>';
        }
        wp_reset_postdata();
        echo $html;
        die();
    }
}
add_action('wp_ajax_filter_product_by_category', 'filter_product_by_category');
add_action('wp_ajax_nopriv_filter_product_by_category', 'filter_product_by_category');


function add_Admin_script()
{
?>
<script>
jQuery(document).ready(function($) {
	$(document).on(
		'change',
		'[data-name="category"]  select',
		function() {
			const select2 = $(this).select2("data");
			const value = select2[0].id;
			const title = select2[0].text;
			console.log(value, title);
			$(this)
				.closest(".acf-fields")
				.find('[data-type="relationship"] select optgroup')
				.each(function() {
					$(this)
						.find("option")
						.each(function() {
							if ($(this).text() === title) {
								$(this).prop("selected", true);
							}
						});
				});
			$(this)
				.closest(".acf-fields")
				.find('[data-type="relationship"] select')
				.trigger("change");
			$(this).closest(".acf-fields").find(".values ul").children().remove();
		}
	);
});
</script>
<?php
}
add_action('admin_footer', 'add_Admin_script');


// Custom Walker cho product_cat
class Custom_Walker_Category extends Walker_Category
{
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"menu-list level-$depth\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
        $current_term = get_queried_object();
        $current_id   = isset($current_term->term_id) ? $current_term->term_id : 0;
        $current_ancestors = $current_id ? get_ancestors($current_id, 'product_cat') : array();

        $cat_name = esc_html($category->name);
        $cat_link = esc_url(get_term_link($category));
        $has_child = get_terms(array(
            'taxonomy'   => 'product_cat',
            'parent'     => $category->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
            'number'     => 1,
        ));

        $active = ($category->term_id == $current_id || in_array($category->term_id, $current_ancestors)) ? ' active' : '';

        // Chỉ in từ cấp 1 trở xuống (bỏ parent=0)
        if ($category->parent == 0) {
            return;
        }

        // Nếu là cấp 1 (tức parent != 0 nhưng depth == 1) => in block heading
        if ($depth == 1) {
            $output .= '<div class="category-product-heading">';
            $output .= '<div class="wrap-title flex items-center justify-between' . $active . '">';
            $output .= '<div class="title"><a href="' . $cat_link . '">' . $cat_name . '</a></div>';
            $output .= '<div class="icon"><i class="fa-light fa-angle-down"></i></div>';
            $output .= '</div>';
            // Nếu có con thì chuẩn bị ul
            if (!empty($has_child)) {
                $output .= '<ul class="menu-list level-' . $depth . '">';
            }
        } else {
            // Các cấp sâu hơn thì dùng <li>
            $output .= '<li class="' . $active . '"><a href="' . $cat_link . '">' . $cat_name . '</a>';
            if (!empty($has_child)) {
                $output .= '<ul class="menu-list level-' . $depth . '">';
            }
        }
    }

    function end_el(&$output, $category, $depth = 0, $args = array())
    {
        if ($category->parent == 0) return;

        $has_child = get_terms(array(
            'taxonomy'   => 'product_cat',
            'parent'     => $category->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
            'number'     => 1,
        ));

        if (!empty($has_child)) {
            $output .= '</ul>';
        }

        if ($depth == 1) {
            $output .= '</div>'; // đóng category-product-heading
        } else {
            $output .= '</li>';
        }
    }
}


function render_category_product_sidebar()
{
    if (!is_tax('product_cat') && !is_shop()) return;

    $current_term    = is_tax('product_cat') ? get_queried_object() : null;
    $current_term_id = $current_term ? $current_term->term_id : 0;

    // Lấy tất cả category cấp 1 (root)
    $root_terms = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => false,
    ]);

    foreach ($root_terms as $root_term) {
        $is_active = $current_term_id && (
            $current_term_id == $root_term->term_id ||
            term_is_ancestor_of($root_term->term_id, $current_term_id, 'product_cat')
        );

        $has_children = !empty(get_terms([
            'taxonomy'   => 'product_cat',
            'parent'     => $root_term->term_id,
            'hide_empty' => false,
            'fields'     => 'ids',
            'number'     => 1,
        ]));

        echo '<div class="category-product-heading">';
        echo '<div class="wrap-title flex items-center justify-between' . ($is_active ? ' active' : '') . '">';
        echo '<div class="title"><a href="' . esc_url(get_term_link($root_term)) . '">' . esc_html($root_term->name) . '</a></div>';
        if ($has_children) {
            echo '<div class="icon"><i class="fa-light fa-angle-down"></i></div>';
        }
        echo '</div>';

        if ($has_children) {
            $children_html = render_category_children_recursive($root_term->term_id, $current_term);
            if ($children_html) {
                echo '<ul class="menu-list level-0"' . ($is_active ? ' style="display:block;"' : '') . '>';
                echo $children_html;
                echo '</ul>';
            }
        }

        echo '</div>';
    }
}

function render_category_children_recursive($parent_id, $current_term)
{
    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'parent'     => $parent_id,
        'hide_empty' => false,
    ]);
    if (empty($terms)) return '';

    $html = '';
    foreach ($terms as $term) {
        $is_active = $current_term && (
            $current_term->term_id == $term->term_id ||
            term_is_ancestor_of($term->term_id, $current_term->term_id, 'product_cat')
        );

        $html .= '<li class="term-item' . ($is_active ? ' active' : '') . '">';
        $html .= '<div class="menu-item">';
        $html .= '<a href="' . get_term_link($term) . '">' . esc_html($term->name) . '</a>';

        $has_children = get_terms(['taxonomy' => 'product_cat', 'parent' => $term->term_id, 'hide_empty' => false]);
        if ($has_children) {
            $html .= '<div class="icon-arrow"><i class="fa-light fa-angle-down"></i></div>';
        }
        $html .= '</div>';

        if ($has_children) {
            $html .= '<ul class="sub-menu" ' . ($is_active ? 'style="display:block;"' : '') . '>';
            $html .= render_category_children_recursive($term->term_id, $current_term);
            $html .= '</ul>';
        }

        $html .= '</li>';
    }
    return $html;
}


function get_deepest_category($id)
{
    $post_id = $id;
    $categories = get_the_category($post_id);

    if ($categories) {
        $deepest_cat = null;
        $max_depth = -1;

        foreach ($categories as $cat) {
            $ancestors = get_ancestors($cat->term_id, 'category');
            $depth = count($ancestors);

            if ($depth > $max_depth) {
                $max_depth = $depth;
                $deepest_cat = $cat;
            }
        }
    }

    return $deepest_cat;
}


add_filter('rank_math/frontend/breadcrumb/items', function ($crumbs) {
    if (is_singular('product')) {
        global $post;
        $terms = get_the_terms($post->ID, 'product_cat');

        if ($terms && !is_wp_error($terms)) {
            // Tìm danh mục sâu nhất
            $deepest = null;
            $depth = 0;
            foreach ($terms as $term) {
                $ancestors = get_ancestors($term->term_id, 'product_cat');
                if (count($ancestors) > $depth) {
                    $depth = count($ancestors);
                    $deepest = $term;
                }
            }

            if ($deepest) {
                // Lấy cây danh mục cha -> con
                $ancestors = array_reverse(get_ancestors($deepest->term_id, 'product_cat'));
                $categories = [];

                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'product_cat');
                    $categories[] = [$ancestor->name, get_term_link($ancestor)];
                }

                $categories[] = [$deepest->name, get_term_link($deepest)];

                // Xóa "Sản phẩm" bị trùng (Shop page)
                foreach ($crumbs as $key => $crumb) {
                    if (
                        isset($crumb[1])
                        && (
                            strpos($crumb[1], '/san-pham') !== false
                            || $crumb[0] === 'Sản phẩm'
                        )
                    ) {
                        unset($crumbs[$key]);
                    }
                }

                $crumbs = array_values($crumbs);

                // Lọc bỏ các breadcrumb trùng danh mục (nếu Rank Math đã chèn)
                $existing_links = array_column($crumbs, 1);
                $categories = array_filter($categories, function ($cat) use ($existing_links) {
                    return !in_array($cat[1], $existing_links, true);
                });

                // Chèn cây danh mục vào giữa Trang chủ và tên sản phẩm
                array_splice($crumbs, 1, 0, $categories);
            }
        }
    }

    return array_values($crumbs);
});



add_filter('rank_math/frontend/breadcrumb/items', function ($crumbs) {
    if (is_paged()) {
        // Xoá phần tử cuối nếu là "Trang X" hoặc "Page X"
        $last = end($crumbs);
        if (isset($last[0]) && preg_match('/Trang\s+\d+|Page\s+\d+/i', $last[0])) {
            array_pop($crumbs);
        }
    }
    return $crumbs;
});


add_filter('wpseo_canonical', 'devvn_rank_math_canonical_url', 99);
add_filter('rank_math/frontend/canonical', 'devvn_rank_math_canonical_url', 99);
function devvn_rank_math_canonical_url($canonical_url)
{
    if (is_shop()) {
        $canonical_url = get_permalink(wc_get_page_id('shop'));
    } elseif (is_product_taxonomy() || is_category() || is_tag()) {
        $canonical_url = get_term_link(get_queried_object_id());
    } elseif (is_home()) {
        $canonical_url = get_permalink(get_option('page_for_posts'));
    }
    return $canonical_url;
}



add_filter('woocommerce_available_variation', function ($variation) {
    $variation_id = $variation['variation_id'];
    $product_id = wp_get_post_parent_id($variation_id);
    $product = wc_get_product($product_id);
    $parent_sku = $product->get_sku();

    $variation['parent_sku'] = !empty($parent_sku) ? $parent_sku : '';
    return $variation;
});