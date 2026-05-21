<?php
/**
 * Component: card-products
 * Tái sử dụng item sản phẩm dạng card cho swiper
 *
 * @param string     $args['thumb_url']  URL ảnh thumbnail
 * @param WC_Product $args['product']    Đối tượng sản phẩm WooCommerce
 */

if ( ! isset( $args ) ) $args = array();
$thumb_url = $args['thumb_url'] ?? '';
$product   = $args['product']   ?? null;

if ( ! $product ) return;
?>
<a class="card-products group" href="<?php the_permalink(); ?>">
	<div class="image-product">
		<div class="img img-ratio ratio:pt-[527_393] zoom-img">
			<?php if ( $thumb_url ) : ?>
			<img class="lozad" data-src="<?php echo esc_url( $thumb_url ); ?>"
				alt="<?php the_title_attribute(); ?>" />
			<?php endif; ?>
		</div>
	</div>
	<div class="main-content">
		<div class="product-name"><?php the_title(); ?></div>
		<div class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
	</div>
</a>
