<?php
/**
 * Section: Normal Banner — Banner đầu trang (dùng cho trang About)
 * ACF: about_banner_image (image array), about_banner_title (text)
 */

$banner_image = get_field('about_banner_image');
$banner_title = get_field('about_banner_title') ?: get_the_title();
$banner_url   = $banner_image ? $banner_image['url'] : '';
?>

<section class="section-NormalBanner">
	<div class="img img-parallax ratio:pt-[450_1920]"
		data-gsap-options='{"type": "img-parallax-percent", "yPercent": 15}'>

		<?php if ($banner_url) : ?>
		<img class="lozad" data-src="<?php echo esc_url($banner_url); ?>"
			alt="<?php echo esc_attr($banner_image['alt'] ?? $banner_title); ?>" />
		<?php endif; ?>

		<div class="main-content">
			<?php if ($banner_title) : ?>
			<div class="title-heading heading-1 text-white uppercase">
				<?php echo esc_html($banner_title); ?>
			</div>
			<?php endif; ?>

			<?php get_template_part('components/breadcrumb'); ?>
		</div>
	</div>
</section>
