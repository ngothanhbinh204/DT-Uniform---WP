<?php
/**
 * Section: Home 2 — Parallax image + CASUAL WEAR
 * ACF: home_2_bg, home_2_title, home_2_subtitle, home_2_button
 */

$home_2_bg       = get_field('home_2_bg');
$home_2_title    = get_field('home_2_title');
$home_2_subtitle = get_field('home_2_subtitle');
$home_2_button   = get_field('home_2_button');

if (!$home_2_bg && !$home_2_title) return;

$bg_url = $home_2_bg ? esc_url($home_2_bg['url']) : '';
$bg_alt = $home_2_bg ? esc_attr($home_2_bg['alt']) : '';
?>

<section class="section-Home-2">
	<div class="img img-parallax ratio:pt-[960_1920]" data-gsap-options='{"type": "img-parallax-percent", "yPercent": 15}'>
		<?php if ($home_2_bg) : ?>
			<img src="<?php echo $bg_url; ?>" alt="<?php echo $bg_alt; ?>" />
		<?php endif; ?>
		<div class="main-content">
			<?php if ($home_2_title) : ?>
				<h2 class="heading-2 text-white uppercase title-heading">
					<?php echo esc_html($home_2_title); ?>
				</h2>
			<?php endif; ?>

			<?php if ($home_2_subtitle) : ?>
				<div class="sub-title text-white body-2">
					<?php echo wp_kses_post($home_2_subtitle); ?>
				</div>
			<?php endif; ?>

			<?php if ($home_2_button) : ?>
				<a class="btn btn-primary"
					href="<?php echo esc_url($home_2_button['url']); ?>"
					<?php echo !empty($home_2_button['target']) ? 'target="' . esc_attr($home_2_button['target']) . '"' : ''; ?>>
					<span><?php echo esc_html($home_2_button['title']); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
