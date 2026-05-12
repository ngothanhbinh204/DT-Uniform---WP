<?php

/**
 * Template Name: Trang chủ
 */

global $post;
$home_banner_slides = get_field('home_banner_slides', $post->ID);
$home_intro_bg = get_field('home_intro_bg', $post->ID);
$home_intro_title = get_field('home_intro_title', $post->ID);
$home_intro_desc = get_field('home_intro_desc', $post->ID);
$home_intro_numbers = get_field('home_intro_numbers', $post->ID);
$home_intro_button = get_field('home_intro_button', $post->ID);
$home_philosophy_title = get_field('home_philosophy_title', $post->ID);
$home_philosophy_desc = get_field('home_philosophy_desc', $post->ID);
$home_philosophy_button = get_field('home_philosophy_button', $post->ID);
$home_philosophy_person_name = get_field('home_philosophy_person_name', $post->ID);
$home_philosophy_person_position = get_field('home_philosophy_person_position', $post->ID);
$home_products_title = get_field('home_products_title', $post->ID);
$home_products_desc = get_field('home_products_desc', $post->ID);
$home_products_list = get_field('home_products_list', $post->ID);
$home_partners_title = get_field('home_partners_title', $post->ID);
$home_partners_desc = get_field('home_partners_desc', $post->ID);
$home_partners_groups = get_field('home_partners_groups', $post->ID);
$home_branches_bg = get_field('home_branches_bg', $post->ID);
$home_branches_title = get_field('home_branches_title', $post->ID);
$home_branches_button = get_field('home_branches_button', $post->ID);
$home_news_title = get_field('home_news_title', $post->ID);
$home_news_posts = get_field('home_news_title', $post->ID);
get_header();
?>
<?php if (!empty($home_banner_slides)) : ?>
    <section class="home-1 relative">
        <div class="home-1-slide relative">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($home_banner_slides as $slide) : ?>
                        <div class="swiper-slide">
                            <div class="home-1-banner relative">
                                <a class="img-ratio ratio:pt-[652_1920]" <?php echo !empty($slide['link']['url']) ? 'href="' . $slide['link']['url'] . '"' : ''; ?>>
                                    <img class="lozad undefined" data-src="<?php echo !empty($slide['image']['url']) ? $slide['image']['url'] : ''; ?>" alt="image" /></a>
                                <div class="home-1-content">
                                    <div class="container">
                                        <div class="wrap" data-aos="fade-up" data-aos-duration="1000">
                                            <div class="heading-title" data-text="<?php echo !empty($slide['title']) ? $slide['title'] : ''; ?>"><?php echo !empty($slide['title']) ? $slide['title'] : ''; ?></div>
                                            <div class="sub-title"><?php echo !empty($slide['subtitle']) ? $slide['subtitle'] : ''; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="wrap-button-slide">
                <div class="btn btn-sw-1 btn-prev primary"></div>
                <div class="btn btn-sw-1 btn-next primary"></div>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="home-2 section-py relative" setBackground="<?php echo !empty($home_intro_bg['url']) ? $home_intro_bg['url'] : ''; ?>">
    <div class="container">
        <div class="heading text-center flex flex-col gap-6 mb-6">
            <h2 class="heading-title" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_intro_title) ? $home_intro_title : ''; ?></h2>
            <div class="desc body-1 font-normal text-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <?php echo !empty($home_intro_desc) ? $home_intro_desc : ''; ?>
            </div>
        </div>
        <div class="number-list inline-flex items-center gap-20 " data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <?php if (!empty($home_intro_numbers)) : ?>
                <?php foreach ($home_intro_numbers as $number) : ?>
                    <div class="item">
                        <div class="number countup" data-number="<?php echo !empty($number['number']) ? $number['number'] : ''; ?>"> <span class="count-value"></span><span class="suffix"><?php echo !empty($number['suffix']) ? $number['suffix'] : ''; ?></span></div>
                        <div class="title font-bold"><?php echo !empty($number['title']) ? $number['title'] : ''; ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($home_intro_button)) : ?>
            <div class="button-more mt-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="800">
                <a class="btn btn-primary" <?php echo !empty($home_intro_button['url']) ? 'href="' . $home_intro_button['url'] . '"' : ''; ?>> <span><?php echo !empty($home_intro_button['title']) ? $home_intro_button['title'] : ''; ?></span>
                    <div class="icon">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<section class="home-3 section-96" id="philosophy">
    <div class="container">
        <div class="wrapper">
            <div class="heading xl:mb-20 mb-base">
                <h2 class="heading-title mb-8" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_philosophy_title) ? $home_philosophy_title : ''; ?></h2>
                <div class="desc body-1 font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <?php echo !empty($home_philosophy_desc) ? $home_philosophy_desc : ''; ?>
                </div>
            </div>
            <div class="bottom flex items-center justify-between">
                <div class="left" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
                    <?php if (!empty($home_philosophy_button)) : ?>
                        <a class="btn btn-primary" <?php echo !empty($home_philosophy_button['url']) ? 'href="' . $home_philosophy_button['url'] . '"' : ''; ?>> <span><?php echo !empty($home_philosophy_button['title']) ? $home_philosophy_button['title'] : ''; ?></span>
                            <div class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="right flex flex-col gap-3 text-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="600">
                    <div class="name rem:text-[32px] font-bold text-Primary-5"><?php echo !empty($home_philosophy_person_name) ? $home_philosophy_person_name : ''; ?></div>
                    <div class="position body-1 font-normal">
                        <?php echo !empty($home_philosophy_person_position) ? $home_philosophy_person_position : ''; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="home-4 section-py">
    <div class="container">
        <div class="heading flex flex-col gap-4 text-center mb-base">
            <h2 class="heading-title" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_products_title) ? $home_products_title : ''; ?></h2>
            <div class="desc body-1 font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <?php echo !empty($home_products_desc) ? $home_products_desc : ''; ?>
            </div>
        </div>
        <div class="home-4-list flex gap-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
            <?php if (!empty($home_products_list)) : ?>
                <?php foreach ($home_products_list as $product) : ?>
                    <div class="item lg:rem:h-[640px] relative flex items-end flex-1  overflow-hidden">
                        <div class="image relative"><a href="<?php echo !empty($product['link']) ? $product['link'] : ''; ?>">
                                <?php if (!empty($product['image']['url'])) : ?>
                                    <img src="<?php echo !empty($product['image']['url']) ? $product['image']['url'] : ''; ?>" alt="<?php echo !empty($product['title']) ? $product['title'] : ''; ?>">
                                <?php endif; ?>
                            </a>
                            <div class="content text-center absolute bottom-0 w-full">
                                <h2 class="text-32 text-Neutral-White font-bold"><?php echo !empty($product['title']) ? $product['title'] : ''; ?></h2>
                                <div class="content-bottom">
                                    <div class="title"><?php echo !empty($product['subtitle']) ? $product['subtitle'] : ''; ?></div>
                                    <div class="button-view">

                                        <a class="btn btn-primary blue" href="<?php echo !empty($product['link']) ? $product['link'] : ''; ?>"> <span><?php echo !empty($product['button_text']) ? $product['button_text'] : ''; ?></span>
                                            <div class="icon">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>
<section class="section-partner bg-Utility-gray-50 section-py">
    <div class="container">
        <div class="heading text-center mb-base">
            <h2 class="heading-title mb-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_partners_title) ? $home_partners_title : ''; ?></h2>
            <div class="desc body-1 font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <?php echo !empty($home_partners_desc) ? $home_partners_desc : ''; ?>
            </div>
        </div>
        <div class="bottom grid xl:grid-cols-2 grid-cols-1 gap-base" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="600">
            <?php if (!empty($home_partners_groups)) : ?>
                <?php foreach ($home_partners_groups as $group) : ?>
                    <div class="box bg-Utility-white rounded-5 xl:p-8 p-5 shadow-Shadow-1">
                        <h3 class="title"><?php echo !empty($group['group_title']) ? $group['group_title'] : ''; ?></h3>
                        <div class="partner-list relative">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    <?php if (!empty($group['logos'])) : ?>
                                        <?php foreach ($group['logos'] as $partner) :
                                        ?>
                                            <div class="swiper-slide">
                                                <div class="item">
                                                    <div class="img img-ratio ratio:pt-[78_84] zoom-img">
                                                        <img class="lozad undefined" data-src="<?php echo !empty($partner['logo']['url']) ? $partner['logo']['url'] : ''; ?>" alt="image" />
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="arrow-button flex items-center justify-center gap-base mt-base">
                                <div class="btn btn-sw-1 btn-prev"></div>
                                <div class="btn btn-sw-1 btn-next"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>
<section class="home-6" setBackground="<?php echo !empty($home_branches_bg['url']) ? $home_branches_bg['url'] : ''; ?>">
    <div class="container">
        <div class="box text-center rem:max-w-[960px] w-full mx-auto">
            <h2 class="heading-2 uppercase font-bold text-Utility-white mb-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_branches_title) ? $home_branches_title : ''; ?></h2>
            <div class="wrapper grid lg:grid-cols-[65.42%_1fr] xl:gap-2 gap-base" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <div class="branch-select">
                    <?php
                    $args = array(
                        'post_type' => 'shop',
                        'posts_per_page' => -1,
                        'orderby' => 'menu_order',
                        'order' => 'DESC',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => 'turn_off_shop',
                                'value' => '1',
                                'compare' => '!=',
                            ),
                        ),
                    );
                    $query = new WP_Query($args);
                    ?>
                    <div class="select-view">
                        <select id="view" name="system_id" onchange="window.location.href = '<?php echo get_system_page_url(); ?>?system_id=' + this.value + '#shop-list';">>
                            <option value="12"><?php _e('Chọn chi nhánh', 'canhcamtheme'); ?></option>
                            <?php if ($query->have_posts()) : ?>
                                <?php while ($query->have_posts()) : ?>
                                    <?php $query->the_post(); ?>
                                    <option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <?php if (!empty($home_branches_button)) : ?>
                    <div class="button-search">
                        <a class="btn btn-primary">
                            <span><?php echo !empty($home_branches_button['title']) ? $home_branches_button['title'] : ''; ?></span>
                            <div class="icon">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
$args_news = array(
    'post_type' => 'post',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
);

$query_news = new WP_Query($args_news);
?>
<section class="home-7 section-py">
    <div class="container">
        <div class="heading text-center flex-center mb-base">
            <h2 class="heading-2 font-bold text-black" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_news_title) ? $home_news_title : ''; ?></h2>
        </div>
        <div class="swiper-column-auto relative" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
            <div class="slide">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php if ($query_news->have_posts()): ?>
                            <?php while ($query_news->have_posts()): ?>
                                <?php $query_news->the_post(); ?>
                                <div class="swiper-slide">
                                    <?php get_template_part('components/content-post'); ?>
                                </div>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="wrap-button-slide">
                <div class="btn btn-sw-1 btn-prev"></div>
                <div class="btn btn-sw-1 btn-next"></div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>