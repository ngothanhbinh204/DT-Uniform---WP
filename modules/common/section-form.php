<?php
$title     = get_field('contact_title', 'option');
$desc      = get_field('contact_desc', 'option');
$shortcode = get_field('contact_form_shortcode', 'option');

if (!$title && !$desc && !$shortcode) return;
?>

<section class="section-About-4">
	<div class="section-py">
		<div class="container-fluid">
			<div class="block-grid row">
				<div class="col-lg-4">
					<div class="main-content">
						<?php if ($title) : ?>
						<h2 class="heading-2 text-white"><?php echo esc_html($title); ?></h2>
						<?php endif; ?>
						<?php if ($desc) : ?>
						<div class="sub-title text-white body-2">
							<?php echo wp_kses_post($desc); ?>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="col-lg-7">
					<div class="block-form">
						<?php if ($shortcode) : ?>
						<?php echo do_shortcode(wp_kses_post($shortcode)); ?>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>