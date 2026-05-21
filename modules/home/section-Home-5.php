<?php
/**
 * Section: Home 5 — Tabs lọc sản phẩm theo Màu sắc
 * ACF: home_5_heading, home_5_parent_category, home_5_color_attribute, home_5_limit
 *
 * Flow:
 * - Admin chọn 1 danh mục (home_5_parent_category)
 * - Admin nhập slug attribute màu (home_5_color_attribute, mặc định: pa_color)
 * - PHP tự lấy tất cả terms màu từ sản phẩm trong danh mục đó
 * - Màu HEX lấy từ term meta 'color' → fallback 'product_attribute_color' → fallback '#cccccc'
 * - Mỗi tab = 1 màu, query sản phẩm theo (danh mục AND màu)
 */

$home_5_heading         = get_field('home_5_heading');
$home_5_parent_cat_id   = get_field('home_5_parent_category'); // term ID
$home_5_color_attribute = get_field('home_5_color_attribute') ?: 'pa_color';
$home_5_limit           = get_field('home_5_limit') ?: 8;

if (!$home_5_parent_cat_id) return;

// Lấy tất cả product ID trong danh mục (bao gồm danh mục con)
$product_ids = get_posts(array(
	'post_type'      => 'product',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'fields'         => 'ids',
	'tax_query'      => array(
		array(
			'taxonomy'         => 'product_cat',
			'field'            => 'term_id',
			'terms'            => intval($home_5_parent_cat_id),
			'include_children' => true,
		),
	),
));

if (empty($product_ids)) return;

// Lấy các terms màu sắc từ attribute taxonomy được dùng bởi các sản phẩm trên
$color_terms = get_terms(array(
	'taxonomy'   => $home_5_color_attribute,
	'object_ids' => $product_ids,
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
));

if (empty($color_terms) || is_wp_error($color_terms)) return;

// Lấy HEX cho mỗi term (thử nhiều meta key phổ biến)
$color_data = array();
foreach ($color_terms as $term) {
	$hex = get_term_meta($term->term_id, 'color', true);
	if (empty($hex)) {
		$hex = get_term_meta($term->term_id, 'product_attribute_color', true);
	}
	if (empty($hex)) {
		$hex = get_term_meta($term->term_id, 'color_code', true); // RadiusTheme swatches
	}
	if (empty($hex)) {
		$hex = '#cccccc'; // fallback
	}
	$color_data[] = array(
		'term' => $term,
		'hex'  => $hex,
	);
}

$first_hex   = $color_data[0]['hex'];
$first_label = $color_data[0]['term']->name;
?>

<section class="section-Home-5">
	<div class="container-fluid">
		<div class="section-py">
			<div class="gsap-tabs-wrapper"
				data-gsap-tabs-options="{'effect': 'fade-up', 'event': 'click', 'mobileEvent': 'click', 'triggerScale': 1}">

				<!-- Header: Tiêu đề + Filter màu -->
				<div class="main-content">
					<?php if ($home_5_heading) : ?>
					<h2 class="heading-2 uppercase mb-3"><?php echo esc_html($home_5_heading); ?></h2>
					<?php endif; ?>

					<div class="filter-color">
						<div class="filter-dropdown">
							<!-- Toggle hiển thị màu đang chọn -->
							<div class="filter-toggle">
								<span class="selected-text">
									<div class="icon-bg" style="background-color: <?php echo esc_attr($first_hex); ?>">
									</div>
								</span>
								<i class="fa-regular fa-chevron-down"></i>
							</div>

							<!-- Danh sách dots màu (tự động từ attribute terms) -->
							<ul class="tab-triggers filter-menu">
								<?php foreach ($color_data as $index => $item) : ?>
								<li <?php echo $index === 0 ? 'class="active"' : ''; ?>
									data-tab-trigger="<?php echo $index; ?>"
									data-color-label="<?php echo esc_attr($item['term']->name); ?>">
									<a class="nav-link" href="javascript:void(0)"
										style="background-color: <?php echo esc_attr($item['hex']); ?>">
										<span>
											<div class="icon-bg"
												style="background-color: <?php echo esc_attr($item['hex']); ?>"
												title="<?php echo esc_attr($item['term']->name); ?>"></div>
										</span>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>

						<!-- Hiển thị tên màu đang chọn -->
						<a class="button-black" href="javascript:void(0)">
							<span class="active-color-name"><?php echo esc_html($first_label); ?></span>
							<i class="fa-light fa-arrow-rotate-left"></i>
						</a>
					</div>
				</div><!-- /.main-content -->

				<!-- Tab Contents: mỗi màu = 1 swiper product -->
				<div class="tab-contents relative">
					<?php foreach ($color_data as $index => $item) :
						$color_term = $item['term'];

						// Query: sản phẩm trong danh mục VÀ có attribute màu này
						$args = array(
							'post_type'      => 'product',
							'posts_per_page' => intval($home_5_limit),
							'post_status'    => 'publish',
							'orderby'        => 'date',
							'order'          => 'DESC',
							'tax_query'      => array(
								'relation' => 'AND',
								array(
									'taxonomy'         => 'product_cat',
									'field'            => 'term_id',
									'terms'            => intval($home_5_parent_cat_id),
									'include_children' => true,
								),
								array(
									'taxonomy' => $home_5_color_attribute,
									'field'    => 'term_id',
									'terms'    => $color_term->term_id,
								),
							),
						);
						$products_query = new WP_Query($args);
					?>
					<div class="tab-pane w-full" data-tab-content="<?php echo $index; ?>">
						<div class="swiper-products-progress"
							data-stick-options='{"position": "right", "stickAbove": 1200}'>
							<div class="swiper">
								<div class="swiper-wrapper">
									<?php if ($products_query->have_posts()) : ?>
									<?php while ($products_query->have_posts()) : $products_query->the_post();
											global $product;
											$product = wc_get_product(get_the_ID());
											if (!$product || !$product->is_visible()) continue;
											$thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
										?>
									<div class="swiper-slide">
										<?php get_template_part( 'components/card-product', null, array( 'thumb_url' => $thumb_url, 'product' => $product ) ); ?>
									</div>
									<?php endwhile; wp_reset_postdata(); ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="swiper-pagination"></div>
						</div>
					</div>
					<?php endforeach; ?>
				</div><!-- /.tab-contents -->

			</div><!-- /.gsap-tabs-wrapper -->
		</div>
	</div>
</section>