<?php
/*
Template name: Page - Liên hệ
*/

global $post;
$contact_company_name = get_field('contact_company_name', $post->ID);
$contact_info_repeater = get_field('contact_info_repeater', $post->ID);
$contact_social_title = get_field('contact_social_title', $post->ID);
$contact_social_links = get_field('contact_social_links', $post->ID);
$contact_company_name = get_field('contact_company_name', $post->ID);
$contact_form_title = get_field('contact_form_title', $post->ID);
$contact_form_description = get_field('contact_form_description', $post->ID);
$form_shortcode = get_field('form_shortcode', $post->ID);
$system_shops = get_field('system_shops', $post->ID);


get_header();

?>
<section class="contact-1 section-py">
    <div class="container">
        <div class="wrapper grid grid-cols-12 xl:gap-0 gap-base">
            <div class="col-left lg:col-span-5 col-span-full xl:rem:pr-[63px]">
                <div class="box">
                    <h1 class="heading-4 font-bold text-Utility-white"><?php echo $contact_company_name; ?></h1>
                    <div class="contact-info">
                        <?php if (!empty($contact_info_repeater)) : ?>
                            <?php foreach ($contact_info_repeater as $item) : ?>
                                <div class="item">
                                    <div class="label"><?php echo !empty($item['label']) ? $item['label'] : ''; ?></div>
                                    <ul>
                                        <?php foreach ($item['items'] as $content) : ?>
                                            <li><a class="contact-link" <?php echo !empty($content['link']) ? 'href="' . $content['link'] . '"' : ''; ?>><?php echo !empty($content['text']) ? $content['text'] : ''; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="contact-follow">
                        <h3><?php echo !empty($contact_social_title) ? $contact_social_title : ''; ?></h3>
                        <?php if (!empty($contact_social_links)) : ?>
                            <div class="contact-social">
                                <ul>
                                    <?php foreach ($contact_social_links as $item) : ?>
                                        <li>
                                            <a <?php echo !empty($item['url']) ? 'href="' . $item['url'] . '"' : ''; ?>>
                                                <?php echo !empty($item['icon']) ? $item['icon'] : ''; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-right lg:col-span-7 col-span-full xl:pl-4">
                <div class="heading mb-6">
                    <h2 class="heading-title mb-4"><?php echo !empty($contact_form_title) ? $contact_form_title : ''; ?></h2>
                    <div class="desc body-1 font-normal text-center">
                        <p><?php echo !empty($contact_form_description) ? $contact_form_description : ''; ?></p>
                    </div>
                </div>
                <?php if (!empty($form_shortcode)) : ?>
                    <?php echo do_shortcode($form_shortcode); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
$args = array(
    'post_type' => 'shop',
    'posts_per_page' => -1,
    'post__in' => !empty($system_shops) ? $system_shops : array(),
    'orderby' => 'post__in',
    'order' => 'DESC',

);
$query = new WP_Query($args);
if ($query->have_posts() && !empty($system_shops)) :
?>
    <section class="system contact-2 section-py">
        <div class="container">
            <div class="wrapper flex flex-col md:flex-row">
                <div class="col-left md:rem:max-w-[560px] max-w-full w-full rem:h-[545px] overflow-y-auto" id="shop-list">
                    <?php
                    $query = new WP_Query($args);
                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            get_template_part('components/content-system');
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="col-right flex-1">
                    <div class="map" id="map-iframe">
                        <?php if ($query->have_posts()) { ?>
                            <?php $map_iframe = get_field('map_iframe', $query->posts[0]->ID); ?>
                            <?php if ($map_iframe) { ?>
                                <?php echo $map_iframe; ?>
                            <?php } else { ?>
                                <div class="empty">
                                    <p><?php _e('Không có dữ liệu', 'canhcamtheme'); ?></p>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
get_footer();
?>