<?php

/**
 * Template Name: About
 */

get_header();
?>

<main>
	<?php get_template_part('modules/about/section-NormalBanner'); ?>
	<?php get_template_part('modules/about/section-About-1'); ?>
	<?php get_template_part('modules/about/section-About-2'); ?>
	<?php get_template_part('modules/about/section-About-3'); ?>
	<?php get_template_part('modules/about/section-About-4'); ?>
	<?php get_template_part('modules/common/section-form'); ?>
</main>

<?php
get_footer();