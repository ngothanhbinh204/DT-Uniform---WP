<?php

/**
 * Template Name: Video
 */
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'video',
    'paged' => $paged,
    'posts_per_page' => 5,
);
$videos = new WP_Query($args);


get_header();
?>
<section class="gallery section-py">
    <div class="container">
        <div class="gallery-nav">
            <?php
            $current_id = get_the_ID();
            $video_id = get_page_id_by_template('templates/template_video.php');
            $album_id = get_page_id_by_template('templates/template_album.php');
            ?>
            <ul>
                <li class="<?php echo $current_id == $video_id ? 'active' : ''; ?>"><a href="<?php echo get_permalink($video_id); ?>"><?php _e('Video', 'canhcamtheme'); ?></a></li>
                <li class="<?php echo $current_id == $album_id ? 'active' : ''; ?>"> <a href="<?php echo get_permalink($album_id); ?>"><?php _e('Hình ảnh', 'canhcamtheme'); ?></a></li>
            </ul>
        </div>
        <div class="gallery-wrapper grid grid-cols-2 gap-base">
            <?php
            $count = 0;
            if ($videos->have_posts()) :

                while ($videos->have_posts()) : $videos->the_post();
                    if ($count <=  1) :
                        get_template_part('components/content-library', null, array('type' => 'video'));
                    endif;
                    $count++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <div class="gallery-list grid md:grid-cols-3 grid-cols-2 gap-base mt-base">
            <?php
            $count = 0;
            if ($videos->have_posts()) :
                while ($videos->have_posts()) : $videos->the_post();
                    if ($count > 1) :
                        get_template_part('components/content-library', null, array('type' => 'video'));
                    endif;
                    $count++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <?php echo wp_bootstrap_pagination(array('custom_query' => $videos)); ?>
    </div>
</section>
<?php
get_footer();
?>