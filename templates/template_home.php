<?php

/**
 * Template Name: Trang chủ
 *
 * Trang chủ DT Uniform — ánh xạ từ dist/index.html
 * Sections: banner-video, Home-1 → Home-7
 * ACF: group_home_page (location: page_template = templates/template_home.php)
 */

get_header();

get_template_part('modules/home/section-banner-video');
get_template_part('modules/home/section-Home-1');
get_template_part('modules/home/section-Home-2');
get_template_part('modules/home/section-Home-3');
get_template_part('modules/home/section-Home-4');
get_template_part('modules/home/section-Home-5');
get_template_part('modules/home/section-Home-6');
get_template_part('modules/home/section-Home-7');

get_footer();
?>