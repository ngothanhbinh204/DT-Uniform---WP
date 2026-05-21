<?php
/**
 * Section: About-3 — Material Commitment (parallax bg + flex items)
 * ACF: about_3_title (text), about_3_bg (image array), about_3_items (repeater)
 *   - item_icon (text - FA class)
 *   - item_title (text)
 *   - item_desc (wysiwyg)
 */

$title  = get_field('about_3_title');
$bg     = get_field('about_3_bg');
$items  = get_field('about_3_items');
$bg_url = $bg ? $bg['url'] : '';
?>

<section class="section-About-3">
	<div class="img img-ratio ratio:pt-[860_1920]">
		<?php if ($bg_url) : ?>
		<img class="lozad" data-src="<?php echo esc_url($bg_url); ?>"
			alt="<?php echo esc_attr($bg['alt'] ?? ''); ?>" />
		<?php endif; ?>

		<div class="main-content">
			<div class="container">
				<div class="section-pt-60">
					<?php if ($title) : ?>
					<h2 class="heading-1 block text-center uppercase mb-base">
						<?php echo esc_html($title); ?>
					</h2>
					<?php endif; ?>

					<?php if (!empty($items)) : ?>
					<div class="block-flex">
						<?php foreach ($items as $item) :
							$icon       = $item['item_icon'] ?? '';
							$item_title = $item['item_title'] ?? '';
							$desc       = $item['item_desc'] ?? '';
						?>
						<div class="flex-content">
							<?php if ($icon) : ?>
							<div class="icon"><i class="<?php echo esc_attr($icon); ?>"></i></div>
							<?php endif; ?>
							<?php if ($item_title) : ?>
							<div class="title-heading heading-7"><?php echo esc_html($item_title); ?></div>
							<?php endif; ?>
							<?php if ($desc) : ?>
							<div class="sub-title body-1">
								<?php echo wp_kses_post($desc); ?>
							</div>
							<?php endif; ?>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</section>
