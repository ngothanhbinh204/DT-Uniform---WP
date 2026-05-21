<?php
/**
 * Section: Banner Video Slider
 * ACF: home_banner_slides (repeater)
 *   - slide_type: select (image | video)
 *   - slide_image: image array
 *   - slide_video_url: text (url)
 */

$home_banner_slides = get_field('home_banner_slides');
$home_h1_text       = get_field('home_h1_text');

if (empty($home_banner_slides)) return;
?>

<section class="section-banner-video">
	<div class="swiper">
		<div class="swiper-wrapper">
			<?php foreach ($home_banner_slides as $slide) :
				$slide_type      = !empty($slide['slide_type']) ? $slide['slide_type'] : 'image';
				$slide_image     = !empty($slide['slide_image']) ? $slide['slide_image'] : null;
				$slide_video_url = !empty($slide['slide_video_url']) ? $slide['slide_video_url'] : '';
			?>
			<div class="swiper-slide">
				<div class="img img-ratio ratio:pt-[860_1920]">
					<?php if ($slide_type === 'video' && $slide_video_url) : ?>
						<video
							class="lozad"
							data-src="<?php echo esc_url($slide_video_url); ?>"
							autoplay
							loop
							muted
							playsinline></video>
					<?php elseif ($slide_type === 'image' && $slide_image) : ?>
						<?php echo get_image_attrachment($slide_image, 'image'); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-pagination"></div>
	</div>
</section>

<?php if ($home_h1_text) : ?>
<h1 class="hidden"><?php echo esc_html($home_h1_text); ?></h1>
<?php endif; ?>
