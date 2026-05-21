<?php
/**
 * Section: Home 4 — Parallax image + MEDICAL UNIFORM
 * ACF: home_4_bg, home_4_title, home_4_subtitle, home_4_button
 */

$home_4_bg       = get_field('home_4_bg');
$home_4_title    = get_field('home_4_title');
$home_4_subtitle = get_field('home_4_subtitle');
$home_4_button   = get_field('home_4_button');

if (!$home_4_bg && !$home_4_title) return;

$bg_url = $home_4_bg ? esc_url($home_4_bg['url']) : '';
$bg_alt = $home_4_bg ? esc_attr($home_4_bg['alt']) : '';
?>

<section class="section-Home-4">
	<div class="img img-parallax ratio:pt-[960_1920]" data-gsap-options='{"type": "img-parallax-percent", "yPercent": 15}'>
		<?php if ($home_4_bg) : ?>
			<img src="<?php echo $bg_url; ?>" alt="<?php echo $bg_alt; ?>" />
		<?php endif; ?>
		<div class="main-content">
			<?php if ($home_4_title) : ?>
				<h2 class="heading-2 text-white uppercase">
					<?php echo esc_html($home_4_title); ?>
				</h2>
			<?php endif; ?>

			<?php if ($home_4_subtitle) : ?>
				<div class="sub-title text-white body-2">
					<?php echo wp_kses_post($home_4_subtitle); ?>
				</div>
			<?php endif; ?>

			<?php if ($home_4_button) : ?>
				<a class="btn btn-primary"
					href="<?php echo esc_url($home_4_button['url']); ?>"
					<?php echo !empty($home_4_button['target']) ? 'target="' . esc_attr($home_4_button['target']) . '"' : ''; ?>>
					<span><?php echo esc_html($home_4_button['title']); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
