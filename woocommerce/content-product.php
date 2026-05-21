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

<a href="<?php the_permalink(); ?>" <?php wc_product_class('product-item card-products group', $product); ?>>
	<div class="image-product">
		<div class="img img-ratio ratio:pt-[527_393] zoom-img "><img class="lozad" data-src="<?php echo $thumb; ?>"
				alt="<?php the_title(); ?>" src="<?php echo $thumb; ?>" data-loaded="true">
		</div>
	</div>
	<div class="main-content">
		<div class="product-name"><?php the_title(); ?></div>
		<div class="product-price"><?php echo $product->get_price_html(); ?></div>
	</div>
</a>