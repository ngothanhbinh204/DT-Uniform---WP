<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// ===== VARIABLES CONFIGURATION =====
// Product basic information
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_sku = $product->get_sku() ?: 'N/A';

// Image configuration
$featured_image_id = $product->get_image_id();
$featured_image_id_of_product = get_post_thumbnail_id($product_id);
$gallery_image_ids = $product->get_gallery_image_ids();
$all_image_ids = array_merge([$featured_image_id], $gallery_image_ids);

// Product type and variations
$is_variable_product = $product->is_type('variable');
$variations = $is_variable_product ? $product->get_available_variations() : [];

// Price configuration for variable products
$variation_prices = $is_variable_product ? $product->get_variation_prices() : [];
$min_regular_price = !empty($variation_prices['regular_price']) ? min($variation_prices['regular_price']) : 0;
$max_regular_price = !empty($variation_prices['regular_price']) ? max($variation_prices['regular_price']) : 0;
$has_price_range = $min_regular_price !== $max_regular_price;

// Product availability
$is_purchasable = $product->is_purchasable();
$is_in_stock = $product->is_in_stock();


// CSS classes and attributes
$main_image_classes = 'img-ratio ratio:pt-[532_800] rounded-lg overflow-hidden';
$thumb_image_classes = 'img ratio:pt-[106_144] img-ratio rem:rounded-[16px] overflow-hidden [&_img]:rem:rounded-[16px]';
$fancybox_gallery = 'product-image';
$fancybox_main = 'img-main';

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

get_template_part('modules/common/breadcrumb');
?>
<?php
$product_id = get_the_ID();
$product_info_table = get_field('product_info_table', $product_id);
$product_detail_tabs = get_field('product_detail_tabs', $product_id);
$product_suggested = get_field('product_suggested', $product_id);
$product_commerce_platforms_single_product = get_field('product_commerce_platforms_single_product', 'option');
$product_contact_form_desc_single_product = get_field('product_contact_form_desc_single_product', 'option');
$single_product_shop_link = get_field('single_product_shop_link', 'option');
$product_contact_shortcode_single_product = get_field('product_contact_shortcode_single_product', 'option');


?>
<section id="product-<?php the_ID(); ?>" <?php wc_product_class('product-detail-1 section-py', $product); ?>>
	<div class="container-fluid">
		<div class="product-detail-main flex flex-col lg:flex-row xl:gap-16 gap-base">
			<div class="col-left xl:rem:max-w-[805px] lg:w-2/4 w-full">
				<div
					class="wrapper grid md:grid-cols-[calc(118/805*100%)_1fr] grid-cols-[calc(120/805*100%)_1fr] gap-[calc(20/805*100%)]">
					<div class="thumb relative">
						<div class="relative size-full">
							<div class="swiper">
								<div class="swiper-wrapper">
									<?php if (!empty($all_image_ids)) : ?>
									<?php foreach ($all_image_ids as $key => $image_id) :
											$image_url = wp_get_attachment_image_src($image_id, 'full')[0];
										?>
									<div class="swiper-slide">
										<div class="img"><a class="img-ratio">
												<img class="lozad undefined" data-src="<?php echo $image_url; ?>"
													alt="" /></a>
										</div>
									</div>
									<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="button-swiper arrow-vertical">
							<div class="btn-swiper btn-prev btn-swiper-primary">
								<div class="icon"></div>
							</div>
							<div class="btn-swiper btn-next btn-swiper-primary">
								<div class="icon"></div>
							</div>
						</div>
					</div>
					<div class="main">
						<div class="swiper">
							<div class="swiper-wrapper">
								<?php foreach ($all_image_ids as $key => $image_id) :
									$image_url = wp_get_attachment_image_src($image_id, 'full')[0];
									$link_video = get_field('link_video', $image_id);
								?>
								<div class="swiper-slide">
									<div class="img"><a
											class="img-ratio rounded-4 <?php echo !empty($link_video) ? 'is-icon-play' : '' ?>"
											<?php if ($link_video) : ?> data-fancybox
											data-src="<?= esc_url($link_video) ?>" <?php endif; ?>><img
												class="lozad undefined" data-src="<?php echo $image_url; ?>"
												alt="" /></a></div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-right flex-1" style="max-width: 45%;">
				<h2 class="text-Primary-5 rem:text-[32px] font-bold mb-3 title-product"> <?php echo $product_name; ?>
				</h2>
				<div class="code-product">
					<p><?php _e('Mã sản phẩm:', 'canhcamtheme'); ?> <br><span
							class="sku-product"><?php echo $product_sku; ?></span></p>
				</div>
				<div class="format-content mt-4">
					<?php echo wpautop($product->get_short_description()); ?>
					<?php if (!empty($product_info_table)) : ?>
					<div class="info-product">
						<table>
							<tr>
								<td><?php _e('Tình trạng:', 'canhcamtheme'); ?></td>
								<td class="parameter stock-status"
									data-in-stock-text="<?php echo __('Còn hàng', 'canhcamtheme'); ?>"
									data-out-of-stock-text="<?php echo __('Hết hàng', 'canhcamtheme'); ?>">
									<?php echo $is_in_stock ? __('Còn hàng', 'canhcamtheme') : __('Hết hàng', 'canhcamtheme'); ?>
								</td>
							</tr>
							<?php foreach ($product_info_table as $item) : ?>
							<tr>
								<td><?php echo !empty($item['label']) ? $item['label'] : ''; ?></td>
								<td class="parameter"><?php echo !empty($item['value']) ? $item['value'] : ''; ?></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
					<?php endif; ?>
				</div>
				<?php


				?>

				<?php
				global $product;
				if ($product->is_type('variable')) {

					$attributes = $product->get_variation_attributes();
					$default_attributes = $product->get_default_attributes();

					foreach ($attributes as $attribute_name => $options) {
						// Lấy taxonomy name, ví dụ: pa_dung-tich
						$taxonomy = str_replace('attribute_', '', wc_variation_attribute_name($attribute_name));

						// Lấy term theo thứ tự gốc trong admin (menu_order)
						$terms = wc_get_product_terms($product->get_id(), $taxonomy, [
							'fields' => 'all',
							'orderby' => 'menu_order',
							'order' => 'ASC',
						]);

				?>
				<div class="filters" data-attribute="<?php echo esc_attr($attribute_name); ?>">
					<div class="label"><?php echo __('By', 'canhcamtheme'); ?>
						<span><?php echo wc_attribute_label($attribute_name); ?></span>:
					</div>
					<div class="value">
						<ul class="attribute-options <?php echo esc_attr($attribute_name); ?>">
							<?php
									// Duyệt đúng theo thứ tự term
									foreach ($terms as $term) {
										if (!in_array($term->slug, $options)) continue; // bỏ qua nếu term không nằm trong variation
										$active_class = (isset($default_attributes[$attribute_name]) && $default_attributes[$attribute_name] === $term->slug) ? 'active' : '';
									?>
							<li class="<?php echo esc_attr($active_class); ?>"
								data-value="<?php echo esc_attr($term->slug); ?>">
								<span><?php echo esc_html($term->name); ?></span>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php
					}
				}
				?>


				<?php do_action('woocommerce_single_product_summary'); ?>

				<?php echo $product->get_price_html(); ?>
				<div class="button-contact flex items-center gap-6 whitespace-nowrap">

					<a class="btn btn-contact" href="#form-contact-product" data-fancybox>
						<span><?php _e('Liên hệ để nhận tư vấn', 'canhcamtheme'); ?></span>
						<div class="icon">
							<i class="fa-regular fa-pen-to-square"></i>
						</div>
					</a>
					<?php if (!empty($single_product_shop_link)) : ?>
					<a class="btn btn-location"
						href="<?php echo !empty($single_product_shop_link['url']) ? $single_product_shop_link['url'] : ''; ?>"
						target="_blank">
						<span><?php echo !empty($single_product_shop_link['title']) ? $single_product_shop_link['title'] : ''; ?></span>
						<div class="icon">
							<i class="fa-regular fa-store"></i>
						</div>
					</a>
					<?php endif; ?>
				</div>
				<div class="commercial flex gap-4">
					<div class="label"><?php _e('Sàn thương mại:', 'canhcamtheme'); ?></div>
					<div class="commercial-img flex gap-2 flex-wrap">
						<?php foreach ($product_commerce_platforms_single_product as $item) : ?>
						<a <?php echo !empty($item['url']) ? 'href="' . $item['url'] . '"' : ''; ?> target="_blank">
							<?php if (!empty($item['logo']['url'])) : ?>
							<img src="<?php echo !empty($item['logo']['url']) ? $item['logo']['url'] : ''; ?>"
								alt="<?php echo !empty($item['logo']['alt']) ? $item['logo']['alt'] : ''; ?>">

							<?php endif; ?>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="share flex items-center gap-3">
					<div class="label">Share:</div>
					<div class="social"> <a
							href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink()); ?>">
							<i class="fa-brands fa-facebook-f"></i></a>

					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</section>
<section class="product-detail-2">
	<div class="wrapper-main" data-toggle="tabslet">
		<div class="wrap">
			<div class="container">
				<ul class="tabslet-tab">
					<li class="active"><a href="#tab1"><?php _e('Thông tin sản phẩm', 'canhcamtheme'); ?></a></li>
					<?php if (!empty($product_detail_tabs)) : ?>
					<?php foreach ($product_detail_tabs as $key => $item) : ?>
					<li><a
							href="#tab<?php echo $key + 2; ?>"><?php echo !empty($item['tab_title']) ? $item['tab_title'] : ''; ?></a>
					</li>
					<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<div class="wrap-content">
			<div class="container">
				<div class="tabslet-content active" id="tab1">
					<h2 class="rem:text-[32px] font-bold text-Primary-5 mb-base text-center">
						<?php _e('Thông tin sản phẩm', 'canhcamtheme'); ?></h2>
					<div class="expand-content overflow-hidden">
						<div class="format-content">
							<?php echo wpautop($product->get_description()); ?>
						</div>
					</div>
					<div class="btn-view-more">
						<button> <span><?php _e('Xem thêm', 'canhcamtheme'); ?></span>
							<div class="icon">
								<i class="fa-regular fa-angle-down"></i>
							</div>
						</button>
					</div>
				</div>
				<?php if (!empty($product_detail_tabs)) : ?>
				<?php foreach ($product_detail_tabs as $key => $item) : ?>
				<div class="tabslet-content" id="tab<?php echo $key + 2; ?>">
					<h2 class="rem:text-[32px] font-bold text-Primary-5 mb-base text-center">
						<?php echo !empty($item['content_title']) ? $item['content_title'] : ''; ?></h2>
					<div class="expand-content overflow-hidden">
						<div class="format-content">
							<?php echo !empty($item['content']) ? $item['content'] : ''; ?>
						</div>
					</div>
					<div class="btn-view-more">
						<button> <span><?php _e('Xem thêm', 'canhcamtheme'); ?></span>
							<div class="icon">
								<i class="fa-regular fa-angle-down"></i>
							</div>
						</button>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if (!empty($product_suggested)) :
	$arg_product_suggested = array(
		'post_type' => 'product',
		'post__in' => !empty($product_suggested) ? $product_suggested : [],
		'orderby' => 'post__in',
		'order' => 'DESC',
	);
	$product_suggested = new WP_Query($arg_product_suggested);
	if ($product_suggested->have_posts()) :
?>
<section class="product-detail-3 section-py">
	<div class="container">
		<h2 class="heading-4 text-center text-Primary-5 mb-base"><?php _e('Sản phẩm gợi ý', 'canhcamtheme'); ?></h2>
		<div class="swiper-column-auto relative autoplay swiper-loop">
			<div class="swiper">
				<div class="swiper-wrapper">
					<?php while ($product_suggested->have_posts()) : $product_suggested->the_post(); ?>
					<?php
								$product = wc_get_product(get_the_ID());
								?>
					<div class="swiper-slide">
						<?php wc_get_template_part('content', 'product'); ?>
					</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
			<div class="button-swiper-products">
				<div class="btn-swiper btn-prev btn-swiper-primary">
					<div class="icon"></div>
				</div>
				<div class="btn-swiper btn-next btn-swiper-primary">
					<div class="icon"></div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
<?php endif; ?>
<?php
$category_id = get_the_terms($product_id, 'product_cat');
$arg_product_related = array(
	'post_type' => 'product',
	'post__not_in' => array($product_id),
	'posts_per_page' => 5,
	'orderby' => 'rand',
	'tax_query' => array(
		array(
			'taxonomy' => 'product_cat',
			'field' => 'term_id',
			'terms' => !empty($category_id) ? $category_id[0]->term_id : [],
		),
	),
);
$product_related = new WP_Query($arg_product_related);
if ($product_related->have_posts()) :
?>
<section class="product-detail-4 section-py bg-Utility-gray-50">
	<div class="container">
		<h2 class="heading-4 text-center text-Primary-5 mb-base"><?php _e('Sản phẩm liên quan', 'canhcamtheme'); ?></h2>
		<div class="swiper-column-auto relative autoplay swiper-loop">
			<div class="swiper">
				<div class="swiper-wrapper">
					<?php while ($product_related->have_posts()) : $product_related->the_post(); ?>
					<div class="swiper-slide">
						<?php wc_get_template_part('content', 'product'); ?>
					</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>

				</div>
			</div>
			<div class="button-swiper-products">
				<div class="btn-swiper btn-prev btn-swiper-primary">
					<div class="icon"></div>
				</div>
				<div class="btn-swiper btn-next btn-swiper-primary">
					<div class="icon"></div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>
<div id="form-contact-product" style="display: none;" data-fancybox-modal>
	<div class="popup-content w-full relative z-50">
		<div class="heading text-center mb-6">
			<h2 class="heading-4 text-Neutral-Black font-bold mb-4 title-product"><?php echo $product_name; ?></h2>
			<div class="desc">
				<p><?php echo $product_contact_form_desc_single_product; ?></p>
			</div>
		</div>
		<?php if (!empty($product_contact_shortcode_single_product)) : ?>
		<?php echo do_shortcode($product_contact_shortcode_single_product); ?>
		<?php endif; ?>
	</div>
</div>
<?php
/**
 * Hook: woocommerce_after_single_product_summary. 
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 */
// do_action('woocommerce_after_single_product_summary');
?>
<?php do_action('woocommerce_after_single_product'); ?>
<style>
.woocommerce-variation-description {
	display: none !important;
}

.added_to_cart {
	display: none;
}

.section-product-detail .product-col-right .variations_form label {
	text-align: left;
}
</style>