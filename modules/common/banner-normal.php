<?php
$id_category = get_queried_object()->term_id;
$taxonomy = get_queried_object()->taxonomy;
$title_banner = '';
if ($id_category) {
	$id = $taxonomy . '_' . $id_category;
	$title_banner = get_term_field('name', $id_category);
} else {
	$id = get_the_ID();
}
$banner = get_field('banner_select_page', $id);

?>
<?php if ($banner) :
	$banner_item = $banner[0];
	$__banner = get_field('banner', $banner_item);
	$banner_image = $__banner['image'];
	$show_breadcrumb = $__banner['show_breadcrumb'];
	$__banner_title = !empty($__banner['title']) ? $__banner['title'] : '';

?>
	<section class="page-banner-main">
		<div class="img img-ratio ratio:pt-[640_1920]">
			<?php foreach ($__banner['images'] as $slide_item) :
			?>
				<img class="lozad undefined" data-src="<?php echo !empty($slide_item['url']) ? $slide_item['url'] : ''; ?>" alt="Banner" />
			<?php endforeach; ?>
		</div>
	</section>
<?php
endif;
?>