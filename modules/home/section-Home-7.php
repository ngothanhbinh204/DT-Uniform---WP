<?php
/**
 * Section: Home 7 — What's Hot (Swiper sản phẩm nổi bật)
 * ACF: home_7_heading, home_7_button, home_7_category, home_7_limit
 */

$home_7_heading  = get_field('home_7_heading');
$home_7_button   = get_field('home_7_button');
$home_7_category = get_field('home_7_category');
$home_7_limit    = get_field('home_7_limit');
$home_7_limit    = $home_7_limit ? intval($home_7_limit) : 8;

// Query WooCommerce products
$args = array(
	'post_type'      => 'product',
	'posts_per_page' => $home_7_limit,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);
if ($home_7_category) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => intval($home_7_category),
		),
	);
}
$products_query = new WP_Query($args);

if (!$products_query->have_posts()) return;
?>

<section class="section-Home-7" data-sticky-layout>
	<div class="container-fluid">
		<div class="section-py">

			<!-- Header: Tiêu đề + Button -->
			<div class="block-content">
				<?php if ($home_7_heading) : ?>
					<h2 class="title-heading upper-case"><?php echo esc_html($home_7_heading); ?></h2>
				<?php endif; ?>

				<?php if ($home_7_button) : ?>
					<div class="button-More">
						<a class="btn btn-secondary"
							href="<?php echo esc_url($home_7_button['url']); ?>"
							<?php echo !empty($home_7_button['target']) ? 'target="' . esc_attr($home_7_button['target']) . '"' : ''; ?>>
							<span><?php echo esc_html($home_7_button['title']); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Swiper Products Hot -->
			<div class="block-swiper" data-stick-options='{"position": "right", "stickAbove": 1200}'>
				<div class="swiper-dynamic-config"
					data-swiper-options='{"slidesPerView": 3.7, "spaceBetween": 24, "loop": false, "res": {"xs": {"slidesPerView": 1, "spaceBetween": 10}, "md": {"slidesPerView": 2, "spaceBetween": 20}, "lg": {"slidesPerView": 3.7, "spaceBetween": 24}}}'>
					<div class="swiper">
						<div class="swiper-wrapper">
							<?php while ($products_query->have_posts()) : $products_query->the_post();
								global $product;
								$product = wc_get_product(get_the_ID());
								if (!$product || !$product->is_visible()) continue;
								$thumb_url   = get_the_post_thumbnail_url(get_the_ID(), 'full');
								$excerpt     = $product->get_short_description();
							?>
							<div class="swiper-slide">
								<a class="card-ProductHot group" href="<?php the_permalink(); ?>">
									<div class="image-card">
										<div class="img img-ratio ratio:pt-[527_453] zoom-img">
											<?php if ($thumb_url) : ?>
												<img class="lozad" data-src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>" />
											<?php endif; ?>
										</div>
									</div>
									<div class="content-card">
										<div class="wrap-content">
											<div class="name-card"><?php the_title(); ?></div>
											<?php if ($excerpt) : ?>
												<div class="desc-card">
													<?php echo wp_trim_words(wp_strip_all_tags($excerpt), 20, '...'); ?>
												</div>
											<?php endif; ?>
										</div>
										<button class="btn btn-view"><?php esc_html_e('View More', 'canhcamtheme'); ?></button>
									</div>
								</a>
							</div>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
				</div>
			</div><!-- /.block-swiper -->

		</div>
	</div>
</section>
