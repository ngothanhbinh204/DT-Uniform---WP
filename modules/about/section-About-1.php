<?php
/**
 * Section: About-1 — Brand Story (các khối ảnh + nội dung xen kẽ)
 * ACF: about_1_blocks (repeater)
 *   - block_image_position (select: right | left)
 *   - block_title (text)
 *   - block_content (wysiwyg)
 *   - block_image (image array)
 */

$blocks = get_field('about_1_blocks');
if (empty($blocks)) return;
?>

<section class="section-About-1" data-sticky-layout>
	<div class="warp-content">
		<div class="container-fluid">
			<?php foreach ($blocks as $index => $block) :
				$img_pos   = !empty($block['block_image_position']) ? $block['block_image_position'] : 'right';
				$title     = $block['block_title'] ?? '';
				$content   = $block['block_content'] ?? '';
				$image     = $block['block_image'] ?? null;
				$img_url   = $image ? $image['url'] : '';
				$img_alt   = $image ? ($image['alt'] ?? $title) : '';

				// Class padding: khối đầu dùng section-pt, các khối sau dùng section-py
				$padding_class = $index === 0 ? 'section-pt' : 'section-py';

				// Vị trí ảnh quyết định thứ tự cột
				// right: col text (5) | col image (7)
				// left:  col image (7) | col text (5)
				$stick_side = $img_pos === 'left' ? 'left' : 'right';
			?>
			<div class="<?php echo esc_attr($padding_class); ?>">
				<div class="block-grid row<?php echo $img_pos === 'left' ? ' flex-row-reverse' : ''; ?>">

					<?php if ($img_pos === 'right') : ?>
					<!-- Text trái, Ảnh phải -->
					<div class="col-lg-5">
						<div class="main-content">
							<?php if ($title) : ?>
							<h1 class="heading-1 uppercase"><?php echo esc_html($title); ?></h1>
							<?php endif; ?>
							<?php if ($content) : ?>
							<div class="sub-title body-2">
								<?php echo wp_kses_post($content); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-lg-7">
						<div class="image" data-stick-options='{"position": "right", "stickAbove": 1200}'>
							<div class="img img-ratio ratio:pt-[686_1107]">
								<?php if ($img_url) : ?>
								<img class="lozad" data-src="<?php echo esc_url($img_url); ?>"
									alt="<?php echo esc_attr($img_alt); ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php else : ?>
					<!-- Ảnh trái, Text phải -->
					<div class="col-lg-7">
						<div class="image" data-stick-options='{"position": "left", "stickAbove": 1200}'>
							<div class="img img-ratio ratio:pt-[686_1107]">
								<?php if ($img_url) : ?>
								<img class="lozad" data-src="<?php echo esc_url($img_url); ?>"
									alt="<?php echo esc_attr($img_alt); ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="col-lg-5">
						<div class="main-content">
							<?php if ($title) : ?>
							<h1 class="heading-1 uppercase"><?php echo esc_html($title); ?></h1>
							<?php endif; ?>
							<?php if ($content) : ?>
							<div class="sub-title body-2">
								<?php echo wp_kses_post($content); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>

				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
