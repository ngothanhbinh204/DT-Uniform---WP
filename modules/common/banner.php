<?php

$id_add = isset($args['term']) ? $args['term'] : '';
$id_category = !empty(get_queried_object()->term_id) ? get_queried_object()->term_id : '';
$term = get_queried_object();
$taxonomy = !empty(get_queried_object()->taxonomy) ? get_queried_object()->taxonomy : '';
if ($id_category) {
	$id = $taxonomy . '_' . $id_category;
	$title = !empty($term) ? $term->name : '';
} else {
	$id = get_the_ID();
	$title = get_the_title($id);
}
if (!empty($id_add)) {
	$banner = get_field('banner_bottom', $id_add);
} else {
	$banner = get_field('banner_select_page', $id);
}
?>
<?php if (!empty($banner)):
	$banner_item = $banner[0];
	$thumbnail = get_the_post_thumbnail_url($banner_item, 'full');
?>
	<section class="page-banner-main">
		<div class="img img-ratio pt-[calc(660/1920*100rem)]">
			<img class="lozad undefined" data-src="<?php echo !empty($thumbnail) ? $thumbnail : ''; ?>" alt="Banner">
		</div>
		<div class="content">
			<h1 class="title"><?php echo !empty($title) ? $title : ''; ?></h1>
			<div class="global-breadcrumb">
				<div class="container">
					<?php if (function_exists("rank_math_the_breadcrumbs")) rank_math_the_breadcrumbs(); ?>
				</div>
			</div>
		</div>
	</section>
<?php else: ?>
	<section class="global-breadcrumb">
		<div class="container">
			<?php if (function_exists("rank_math_the_breadcrumbs")) rank_math_the_breadcrumbs(); ?>
		</div>
	</section>
<?php endif; ?>