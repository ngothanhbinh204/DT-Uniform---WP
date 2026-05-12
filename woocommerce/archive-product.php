<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

// Define all variables at the top
$term = get_queried_object();
$title_category = get_field('category_title', 'product_cat_' . $term->term_id);
$page_title = $title_category ? $title_category : $term->name;

// Shortcode variables
$product_filter_sort_shortcode = '[facetwp facet="product_filter_sort"]';
$pagination_product_shortcode = '[facetwp facet="pagination_product"]';

// Text strings
$filter_by_text = __('Lọc theo', 'canhcamtheme');
$filter_text = __('Bộ lọc', 'canhcamtheme');
$product_filter_title_text = __('Bộ lọc sản phẩm', 'canhcamtheme');

// Template directory path
$template_directory = get_template_directory_uri();

get_header('shop');
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */


$top_term = $term;

while ($top_term->parent != 0) {
	$top_term = get_term($top_term->parent, 'product_cat');
}




do_action('woocommerce_before_main_content');
?>


<section class="product section-py">
	<div class="container">
		<div class="filter-product-mobile lg:hidden sticky top-[calc(var(--header-height)+20px)] z-10">
			<div class="toggle-filter flex items-center justify-between w-fit cursor-pointer">
				<div class="group-left flex items-center gap-2 bg-Primary-1 p-2 text-Utility-white"><i class="fa-light fa-filter subheader-24"></i>
					<div class="label"><?php _e('Bộ lọc', 'canhcamtheme'); ?></div>
				</div>
			</div>
		</div>
		<div class="wrapper grid grid-cols-12 gap-base">
			<div class="col-left lg:col-span-3 col-span-full flex flex-col gap-6">
				<div class="btn-close-filter-product-mobile"><i class="fa-light fa-xmark"></i></div>
				<?php render_category_product_sidebar(); ?>

			</div>
			<div class="col-right lg:col-span-9 col-span-full">
				<div class="heading flex items-center justify-between mb-base flex-wrap">
					<h2 class="heading-title"><?= $term->name ?></h2>
					<div class="filters">
						<?php do_action('woocommerce_before_shop_top_filter');
						?>
					</div>
				</div>
				<div class="facetwp-template">
					<?php if (woocommerce_product_loop()) : ?>
						<?php
						do_action('woocommerce_before_shop_loop');
						woocommerce_product_loop_start();
						if (wc_get_loop_prop('total')) {
							while (have_posts()) {
								the_post();
								/**
								 * Hook: woocommerce_shop_loop.
								 */
								do_action('woocommerce_shop_loop');
								global $layout_product;
								$layout_product = 'layout_2';
								wc_get_template_part('content', 'product');
							}
						}
						woocommerce_product_loop_end();
						do_action('woocommerce_after_shop_loop');
						?>
					<?php else: ?>
						<?php
						do_action('woocommerce_no_products_found');
						?>
					<?php endif; ?>
					<?php echo wp_bootstrap_pagination(); ?>
				</div>
				<?php do_action('woocommerce_after_main_content'); ?>
			</div>
		</div>
	</div>

</section>
<?php
get_footer('shop');
?>