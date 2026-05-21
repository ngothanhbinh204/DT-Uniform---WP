<?php
/**
 * Section: Home 1 — Stacking Section với hình nền + tiêu đề lớn
 * ACF: home_1_bg, home_1_title, home_1_subtitle, home_1_button
 */

$home_1_bg       = get_field('home_1_bg');
$home_1_title    = get_field('home_1_title');
$home_1_subtitle = get_field('home_1_subtitle');
$home_1_button   = get_field('home_1_button');

if (!$home_1_bg && !$home_1_title) return;
?>

<section class="section-Home-1" data-gsap-options='{"type": "stacking-section", "endTarget": ".section-Home-2"}'>
	<div class="block-bg">
		<div class="img img-ratio ratio:pt-[640_1920]">
			<?php if ($home_1_bg) : ?>
				<?php echo get_image_attrachment($home_1_bg, 'image'); ?>
			<?php endif; ?>
			<div class="wrap-padding">
				<div class="container">
					<?php if ($home_1_title) : ?>
						<h2 class="title-Banner text-white uppercase">
							<?php echo nl2br(esc_html($home_1_title)); ?>
						</h2>
					<?php endif; ?>

					<?php if ($home_1_subtitle) : ?>
						<div class="sub-title text-white body-2">
							<?php echo wp_kses_post($home_1_subtitle); ?>
						</div>
					<?php endif; ?>

					<?php if ($home_1_button) : ?>
						<a class="btn btn-primary"
							href="<?php echo esc_url($home_1_button['url']); ?>"
							<?php echo !empty($home_1_button['target']) ? 'target="' . esc_attr($home_1_button['target']) . '"' : ''; ?>>
							<span><?php echo esc_html($home_1_button['title']); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
