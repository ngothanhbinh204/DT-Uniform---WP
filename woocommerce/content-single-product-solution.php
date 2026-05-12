<?php
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

$link_video = get_field('link_video', $product_id);
$group_solution = get_field('group_solution', $product_id);
$socials_product = get_field('socials_product', 'option');
$optimal_solution = get_field('optimal_solution', 'option');
?>

<section id="product-<?php the_ID(); ?>" <?php wc_product_class('solution-info-section section relative z-1 bg-background-50', $product); ?>>
    <div class="container">
        <div class="product-detail-flex">
            <div class="product-detail-slider">
                <div class="product-top relative" data-link-video="<?php echo !empty($link_video) ? $link_video  : '' ?> ">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php
                            // Render variation images for variable products
                            if ($is_variable_product) :
                                foreach ($variations as $key => $variation) :
                                    $variation_image_id = $variation['image_id'];
                                    $variation_id = $variation['variation_id'];
                                    $image_url = get_the_post_thumbnail_url($variation_id, 'full');

                                    if ($variation_image_id && $image_url) :
                            ?>
                                        <div class="swiper-slide" data-variation-id="<?= esc_attr($variation_id) ?>" data-image-id="<?= esc_attr($variation_image_id) ?>" data-image-featured="<?= esc_attr($featured_image_id_of_product) ?>">
                                            <?php if ($key == 0) : ?>
                                                <div class="image img-cover relative <?php echo !empty($link_video) ? 'is-icon-play' : '' ?> " <?php if ($link_video) : ?> data-fancybox data-src="<?= esc_url($link_video) ?>" <?php endif; ?>><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php else : ?>
                                                <div class="image img-cover"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                    endif;
                                endforeach;
                            endif;

                            // Render main product images
                            foreach ($all_image_ids as $key => $image_id) :
                                if ($image_id) :
                                    $image_url = wp_get_attachment_image_src($image_id, 'full')[0];
                                    ?>
                                    <div class="swiper-slide" data-variation-id="<?= esc_attr($variation_id) ?>" data-image-id="<?= esc_attr($variation_image_id) ?>" data-image-featured="<?= esc_attr($featured_image_id_of_product) ?>">
                                        <?php if ($key == 0) : ?>
                                            <div class="image img-cover relative <?php echo !empty($link_video) ? 'is-icon-play' : '' ?> " data-fancybox data-src="<?= esc_url($link_video) ?>"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                        <?php else : ?>
                                            <div class="image img-cover"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                        <?php endif; ?>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="product-thumbs relative">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php
                            if ($is_variable_product) :
                                foreach ($variations as $key => $variation) :
                                    $variation_image_id = $variation['image_id'];
                                    $variation_id = $variation['variation_id'];
                                    $image_url = get_the_post_thumbnail_url($variation_id, 'full');

                                    if ($variation_image_id && $image_url) :
                            ?>
                                        <div class="swiper-slide"
                                            data-variation-id="<?= esc_attr($variation_id) ?>"
                                            data-image-id="<?= esc_attr($variation_image_id) ?>"
                                            data-image-featured="<?= esc_attr($featured_image_id_of_product) ?>">
                                            <?php if ($key == 0) : ?>
                                                <div class="image img-cover relative is-icon-play"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php else : ?>
                                                <div class="image img-cover"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php
                                    endif;
                                endforeach;
                            endif;

                            // Render main product thumbnails
                            foreach ($all_image_ids as $key => $image_id) :
                                if ($image_id) :
                                    $image_url = wp_get_attachment_image_src($image_id, 'full')[0];
                                    ?>
                                    <div class="swiper-slide"
                                        data-variation-id="<?= esc_attr($variation_id) ?>"
                                        data-image-id="<?= esc_attr($image_id) ?>"
                                        data-image-featured="<?= esc_attr($featured_image_id_of_product) ?>">
                                        <div class="<?= esc_attr($thumb_image_classes) ?>">
                                            <?php if ($key == 0) : ?>
                                                <div class="image img-cover relative is-icon-play"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php else : ?>
                                                <div class="image img-cover"><img src="<?= esc_url($image_url) ?>" alt="image"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                        <div class="swiper-button is-abs">
                            <div class="button-prev"><i class="fa-light fa-chevron-left"></i></div>
                            <div class="button-next"><i class="fa-light fa-chevron-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Product Details -->
            <div class="product-detail-caption">
                <div class="solution-info-top">
                    <h1 class="site-title"><?= esc_html($product_name) ?></h1>
                    <?php do_action('woocommerce_custom_price'); ?>


                    <?php
                    $variations = $product->get_available_variations();
                    ?>

                    <div class="solution-tabslet main-tabslet" data-toggle="tabslet">
                        <ul class="tabslet-tabs">
                            <?php foreach ($variations as $key => $variation) : ?>
                                <?php
                                $labels    = [];
                                $attrsData = [];

                                foreach ($variation['attributes'] as $attr_name => $attr_value) {
                                    $taxonomy = str_replace('attribute_', '', $attr_name);
                                    $term     = get_term_by('slug', $attr_value, $taxonomy);
                                    $labels[] = $term ? $term->name : $attr_value;

                                    // Lưu tất cả attributes của biến thể
                                    $attrsData[$attr_name] = $attr_value;
                                }

                                $tab_label = implode(' / ', $labels);
                                ?>
                                <li class="tab">
                                    <a href="#tab-<?= esc_attr($key + 1) ?>"
                                        data-variation-id="<?= esc_attr($variation['variation_id']) ?>"
                                        data-attributes='<?= json_encode($variation['attributes'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <?= esc_html($tab_label) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <?php foreach ($variations as $key => $variation) :
                            $variation_obj = wc_get_product($variation['variation_id']);
                        ?>
                            <div class="tabslet-content mt-5" id="tab-<?= esc_attr($key + 1) ?>">
                                <div class="product-table-info">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td><?php _e('Mã sản phẩm', 'canhcamtheme'); ?></td>
                                                <td class="product-sku"><?= esc_html($variation_obj->get_sku()) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="content">
                                    <?php
                                    echo $variation_obj ? $variation_obj->get_description() : '';
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>


                    <?php if (!empty($group_solution['image_package'])) :
                    ?>
                        <?php if (!empty($group_solution['image_package'])) : ?>
                            <a class="solution-modal" href="#modal-solution-compare" data-fancybox>
                                <i class=" fa-solid fa-circle-info"></i>
                                <span>
                                    <?php printf('Xem so sánh %s phiên bản', count($variations), 'canhcamtheme'); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <style>
                    .solution-info-top>.product-flex,
                    .single_variation,
                    table.variations {
                        display: none !important;
                    }
                </style>
                <div class="solution-info-bot">
                    <?php do_action('woocommerce_single_product_summary'); ?>
                    <?php $url_product = get_permalink($product_id); ?>
                    <?php if (!empty($socials_product)) : ?>
                        <div class="social-list">
                            <ul>
                                <?php foreach ($socials_product as $key => $social) : ?>
                                    <li><a href="<?= !empty($social['url']) ? esc_url($social['url']) . 'sharer/sharer.php?u=' . $url_product : 'javascript:;' ?>" target="_blank"><?= !empty($social['icon']) ? $social['icon'] : '' ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<section class="solution-adv-dis-section relative z-1 bg-white">
    <?php get_template_part('components/section-uu-nhuoc', null, ['advantages_and_disadvantages' => $group_solution['advantages_and_disadvantages']]); ?>
</section>

<section class="solution-ingredient-section section-large relative z-1 bg-secondary-6">
    <?php get_template_part('components/section-thanh-phan', null, ['ingredient' => $group_solution['ingredient']]); ?>
</section>

<?php get_template_part('components/section-solution'); ?>

<section class="product-comment-section section-small relative z-1 bg-white">
    <div class="container">

        <h2 class="site-title text-center"><?php _e('Bình luận sản phẩm', 'canhcamtheme'); ?></h2>
        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>
    </div>
</section>
<?php
$category_id = get_the_terms($product_id, 'product_cat');
$args_related = array(
    'post_type' => 'product',
    'post__not_in' => array($product_id),
    'posts_per_page' => 5,
    'orderby' => 'rand',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $category_id[0]->term_id,
        ),
    ),
);
$related_products = new WP_Query($args_related);
?>
<section class="product-related-section section-t-small section-b-large relative z-1 bg-white">
    <div class="container">
        <h2 class="site-title text-center"><?php _e('Combo giải pháp khác', 'canhcamtheme'); ?></h2>
        <div class="relative home-product-slider pb-10 mt-10 lg:pb-0">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php while ($related_products->have_posts()) : $related_products->the_post(); ?>
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
            <div class="mobile-only">
                <div class="swiper-pagination"></div>
            </div>
            <div class="desktop-only">
                <div class="swiper-button is-abs">
                    <div class="button-prev"><i class="fa-light fa-chevron-left"></i></div>
                    <div class="button-next"><i class="fa-light fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if (!empty($group_solution['image_package'])) : ?>
    <div class="modal modal-solution-compare" id="modal-solution-compare">
        <div class="modal-wrap">
            <div class="modal-body">
                <h3 class="sub-header-h6 font-bold text-primary-1"><?php printf('So sánh cấu hình %s phiên bản', count($variations), 'canhcamtheme'); ?></h3>
                <?php if (!empty($group_solution['image_package'])) : ?>
                    <div class="image text-center mt-4"><img src="<?php echo $group_solution['image_package']['url'] ?>" alt=""></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>