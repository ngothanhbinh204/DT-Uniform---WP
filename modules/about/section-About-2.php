<?php
/**
 * Section: About-2 — Our Team (bg image + flex items)
 * ACF: about_2_title (text), about_2_bg (image array), about_2_items (repeater)
 *   - item_icon (text - FA class)
 *   - item_name (text)
 *   - item_desc (wysiwyg)
 */

$title = get_field('about_2_title');
$bg    = get_field('about_2_bg');
$items = get_field('about_2_items');
$bg_url = $bg ? $bg['url'] : '';
?>

<section class="section-About-2"
	<?php if ($bg_url) : ?>data-bg-options='{"src":"<?php echo esc_url($bg_url); ?>"}'<?php endif; ?>>
	<div class="warp-padding">
		<div class="container">
			<div class="main-content">
				<?php if ($title) : ?>
				<h2 class="heading-1 uppercase block text-center text-white">
					<?php echo esc_html($title); ?>
				</h2>
				<?php endif; ?>

				<?php if (!empty($items)) : ?>
				<div class="block-flex">
					<?php foreach ($items as $item) :
						$icon = $item['item_icon'] ?? '';
						$name = $item['item_name'] ?? '';
						$desc = $item['item_desc'] ?? '';
					?>
					<div class="flex-content">
						<div class="top-content">
							<?php if ($icon) : ?>
							<div class="icon"><i class="<?php echo esc_attr($icon); ?>"></i></div>
							<?php endif; ?>
							<?php if ($name) : ?>
							<div class="name"><span><?php echo esc_html($name); ?></span></div>
							<?php endif; ?>
						</div>
						<?php if ($desc) : ?>
						<div class="bottom-content">
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
</section>
