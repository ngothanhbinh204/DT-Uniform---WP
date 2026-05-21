<?php
/**
 * Section: Home 3 — Tabs lọc sản phẩm theo danh mục con
 * ACF: home_3_heading, home_3_button, home_3_parent_category, home_3_limit
 *
 * Flow:
 * - Admin chọn 1 danh mục cha (vd: CASUAL WEAR)
 * - PHP tự lấy các danh mục con → render thành tabs
 * - Tab đầu tiên = "All" (tất cả SP trong danh mục cha)
 * - Mỗi tab tiếp theo = 1 danh mục con
 */

$home_3_heading         = get_field('home_3_heading');
$home_3_button          = get_field('home_3_button');
$home_3_parent_cat_id   = get_field('home_3_parent_category'); // term ID
$home_3_limit           = get_field('home_3_limit') ?: 8;

if (!$home_3_parent_cat_id) return;

// Lấy danh mục cha
$parent_term = get_term(intval($home_3_parent_cat_id), 'product_cat');
if (!$parent_term || is_wp_error($parent_term)) return;

// Lấy danh mục con
$child_terms = get_terms(array(
	'taxonomy'   => 'product_cat',
	'parent'     => $parent_term->term_id,
	'hide_empty' => true,
	'orderby'    => 'menu_order',
	'order'      => 'ASC',
));

// Build tabs: index 0 = All (parent), 1..n = child cats
// Mỗi tab: ['label' => string, 'term_id' => int|null]
$tabs = array();
$tabs[] = array('label' => 'All', 'term_id' => null); // All tab
if (!is_wp_error($child_terms) && !empty($child_terms)) {
	foreach ($child_terms as $child) {
		$tabs[] = array('label' => $child->name, 'term_id' => $child->term_id);
	}
}

// Heading fallback = tên danh mục cha
$section_heading = $home_3_heading ?: $parent_term->name;
?>

<section class="section-Home-3" data-data-sticky-layout>
	<div class="container-fluid">
		<div class="section-py">
			<div class="gsap-tabs-wrapper" data-gsap-tabs-options="{'effect': 'fade-up', 'event': 'click', 'mobileEvent': 'click', 'triggerScale': 1}">

				<!-- Header: Tiêu đề + Dropdown tabs mobile + Button -->
				<div class="main-content">
					<div class="content-left">
						<?php if ($section_heading) : ?>
						<h2 class="heading-2 uppercase mb-3"><?php echo esc_html($section_heading); ?></h2>
						<?php endif; ?>

						<!-- Dropdown lọc (mobile) -->
						<div class="filter-dropdown">
							<div class="filter-toggle">
								<span class="selected-text"><?php echo esc_html($tabs[0]['label']); ?></span>
								<i class="fa-regular fa-chevron-down"></i>
							</div>
							<ul class="tab-triggers filter-menu">
								<?php foreach ($tabs as $index => $tab) : ?>
								<li <?php echo $index === 0 ? 'class="active"' : ''; ?> data-tab-trigger="<?php echo $index; ?>">
									<a class="nav-link" href="javascript:void(0)">
										<span><?php echo esc_html($tab['label']); ?></span>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>

					<div class="content-right">
						<?php if ($home_3_button) : ?>
						<a class="btn btn-secondary"
							href="<?php echo esc_url($home_3_button['url']); ?>"
							<?php echo !empty($home_3_button['target']) ? 'target="' . esc_attr($home_3_button['target']) . '"' : ''; ?>>
							<span><?php echo esc_html($home_3_button['title']); ?></span>
						</a>
						<?php endif; ?>
					</div>
				</div><!-- /.main-content -->

				<!-- Tab Contents -->
				<div class="tab-contents relative">
					<?php foreach ($tabs as $index => $tab) :
						$tab_term_id = $tab['term_id'];

						// All tab → query trong danh mục cha (include_children)
						// Child tab → query chỉ trong danh mục con đó
						$args = array(
							'post_type'      => 'product',
							'posts_per_page' => intval($home_3_limit),
							'post_status'    => 'publish',
							'orderby'        => 'date',
							'order'          => 'DESC',
							'tax_query'      => array(
								array(
									'taxonomy'         => 'product_cat',
									'field'            => 'term_id',
									'terms'            => $tab_term_id ?? $parent_term->term_id,
									'include_children' => is_null($tab_term_id), // All tab mới include children
								),
							),
						);
						$products_query = new WP_Query($args);
					?>
					<div class="tab-pane w-full" data-tab-content="<?php echo $index; ?>">
						<div class="swiper-products-progress" data-stick-options='{"position": "right", "stickAbove": 1200}'>
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