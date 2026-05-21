<?php
/**
 * Section: Home 6 — Parallax image + B2B Order
 * ACF: home_6_bg, home_6_title, home_6_subtitle, home_6_button
 */

$home_6_bg       = get_field('home_6_bg');
$home_6_title    = get_field('home_6_title');
$home_6_subtitle = get_field('home_6_subtitle');
$home_6_button   = get_field('home_6_button');

if (!$home_6_bg && !$home_6_title) return;

$bg_url = $home_6_bg ? esc_url($home_6_bg['url']) : '';
$bg_alt = $home_6_bg ? esc_attr($home_6_bg['alt']) : '';
?>

<section class="section-Home-6">
	<div class="img img-parallax ratio:pt-[860_1920]" data-gsap-options='{"type": "img-parallax-percent", "yPercent": 15}'>
		<?php if ($home_6_bg) : ?>
			<img src="<?php echo $bg_url; ?>" alt="<?php echo $bg_alt; ?>" />
		<?php endif; ?>
		<div class="main-content">
			<div class="container-fluid">
				<?php if ($home_6_title) : ?>
					<h2 class="heading-2 text-white uppercase">
						<?php echo esc_html($home_6_title); ?>
					</h2>
				<?php endif; ?>

				<?php if ($home_6_subtitle) : ?>
					<div class="sub-title body-2 text-white">
						<?php echo wp_kses_post($home_6_subtitle); ?>
					</div>
				<?php endif; ?>

				<?php if ($home_6_button) : ?>
					<a class="btn btn-primary"
						href="<?php echo esc_url($home_6_button['url']); ?>"
						<?php echo !empty($home_6_button['target']) ? 'target="' . esc_attr($home_6_button['target']) . '"' : ''; ?>>
						<span><?php echo esc_html($home_6_button['title']); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
