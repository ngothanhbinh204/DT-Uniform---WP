<?php
defined('ABSPATH') || exit;

global $product;

if (!$product->is_visible()) {
	return;
}
$product_tag = !empty(get_the_terms($product->get_id(), 'product_tag')) ? get_the_terms($product->get_id(), 'product_tag') : [];

$sale_price = !empty($product->get_sale_price()) ? $product->get_sale_price() : '';
$regular_price = !empty($product->get_regular_price()) ? $product->get_regular_price() : '';

$sale_price_percentage = !empty($regular_price) && !empty($sale_price) ? round(($regular_price - $sale_price) / $regular_price * 100) : '';
if ($product->is_type('variation')) {
	$terms = get_the_terms($product->get_parent_id(), 'product_cat');
} else {
	$terms = get_the_terms($product->get_id(), 'product_cat');
}
global $layout_product;

$layout_product = $layout_product ?? 'layout_1';

// if ($product->is_type('variable')) {

// 	$product_default_variation = get_product_default_variation($product);
// 	if ($product_default_variation) {
// 		$product = $product_default_variation;
// 	} else {
// 		// $product = $product->get_children()[0];
// 	}
// }


if ($product->is_type('variation')) {
	$thumb = !empty(get_the_post_thumbnail_url($product->get_id(), 'full')) ? get_the_post_thumbnail_url($product->get_id(), 'full') : get_the_post_thumbnail_url($product->get_parent_id(), 'full');
} else {
	$thumb = get_the_post_thumbnail_url($product->get_id(), 'full');
}
?>
<div <?php wc_product_class('product-item', $product); ?>>
	<div class="img">
		<a class="img-ratio" href="<?php the_permalink(); ?>">
			<img
				class="lozad undefined"
				data-src="<?php echo $thumb; ?>"
				alt="<?php the_title(); ?>" /></a>
	</div>
	<div class="content">
		<div class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
		<div class="desc">
			<?php echo wp_trim_words($product->get_short_description(), 40, '...'); ?>
		</div>
	</div>
	<a href="<?php the_permalink(); ?>" class="stretched-link"></a>
</div>