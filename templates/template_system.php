<?php

/**
 * Template Name: Hệ thống cửa hàng
 */
get_header();

$system_id = isset($_GET['system_id']) ? $_GET['system_id'] : '';

if ($system_id) {
    $args = array(
        'post_type' => 'shop',
        'post__in' => array($system_id),

        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'turn_off_shop',
                'value' => '1',
                'compare' => '!=',
            ),
        ),
    );
} else {

    $args = array(
        'post_type' => 'shop',
        'posts_per_page' => -1,

        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'turn_off_shop',
                'value' => '1',
                'compare' => '!=',
            ),
        ),

    );
}


?>
<section class="system section-py">
	<div class="container">
		<div class="heading rem:max-w-[1160px] w-full mx-auto">
			<div class="desc text-center font-normal mb-6">
				<?php the_content(); ?>
			</div>
			<form class="system-select flex flex-col md:flex-row items-center mb-base" id="system-select">
				<div class="select-view">
					<div class="select-view-item relative">
						<select id="category-shop" name="category-shop">
							<option value=""><?php _e('Ngành hàng', 'canhcamtheme'); ?></option>
							<?php
                            $terms = get_terms(array(
                                'taxonomy' => 'shop-category',
                                'hide_empty' => false,
                            ));
                            foreach ($terms as $term) { ?>
							<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="select-view">
					<div class="select-view-item relative">
						<select id="area-shop-parent" name="area-shop-parent">
							<option value=""><?php _e('Khu vực', 'canhcamtheme'); ?></option>
							<?php
                            $terms = get_terms(array(
                                'taxonomy' => 'shop-area',
                                'hide_empty' => false,
                                'parent' => 0,
                            ));
                            foreach ($terms as $term) { ?>
							<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="select-view">
					<div class="select-view-item relative">
						<select id="area-shop-child" name="area-shop-child">
							<option value=""><?php _e('Tỉnh / Thành phố', 'canhcamtheme'); ?></option>
						</select>
					</div>
				</div>
				<?php $current_lang = apply_filters('wpml_current_language', null); ?>
				<input type="hidden" name="lang" value="<?php echo $current_lang; ?>">
				<button class="btn btn-primary" type="submit">
					<span><?php _e('Tìm kiếm', 'canhcamtheme'); ?></span>
					<div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
				</button>
			</form>
		</div>
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
<?php
get_footer();
?>