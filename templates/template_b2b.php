<?php
/**
 * Template Name: Page - B2B
 *
 * @package canhcamtheme
 */

get_header();

$pid = get_the_ID();
?>

<main>
	<?php
/* ============================================================
   SECTION 1 — NormalBanner
   ============================================================ */
$banner_image = get_field('b2b_banner_image', $pid);
$banner_title = get_field('b2b_banner_title', $pid) ?: get_the_title();
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

	<?php
/* ============================================================
   SECTION 2 — AutoFit (Swiper trái + Nội dung phải)
   ============================================================ */
$s2_enable  = get_field('b2b_s2_enable', $pid);
$s2_images  = get_field('b2b_s2_images', $pid);
$s2_heading = get_field('b2b_s2_heading', $pid);
$s2_content = get_field('b2b_s2_content', $pid);

if ($s2_enable && ($s2_images || $s2_heading || $s2_content)) :
?>
	<section class="section-AutoFit" data-sticky-layout>
		<div class="container-fluid">
			<div class="block-swiper row">
				<?php if ($s2_images) : ?>
				<div class="col-xl-7">
					<div class="swiper-dynamic-config" data-id-swiper="capacity-2"
						data-swiper-options='{"slidesPerView": 1, "centeredSlides": true, "spaceBetween":"getVw(15, 110)", "loop": true, "res":{"xl":"1.2"}}'
						data-stick-options='{"position": "left", "stickAbove": 1200}'>
						<div class="swiper">
							<div class="swiper-wrapper h-full">
								<?php foreach ($s2_images as $img) : ?>
								<div class="swiper-slide">
									<div class="item">
										<div class="img img-ratio ratio:pt-[720_1020] zoom-img">
											<img class="lozad" data-src="<?php echo esc_url($img['url']); ?>"
												alt="<?php echo esc_attr($img['alt'] ?? ''); ?>" />
										</div>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="button-swiper">
							<div class="btn-swiper btn-prev btn-swiper-primary" data-id-swiper="capacity-2">
								<div class="icon"></div>
							</div>
							<div class="btn-swiper btn-next btn-swiper-primary" data-id-swiper="capacity-2">
								<div class="icon"></div>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<?php if ($s2_heading || $s2_content) : ?>
				<div class="col-xl-5">
					<div class="main-content">
						<div class="box-content">
							<?php if ($s2_heading) : ?>
							<h2 class="heading-2"><?php echo esc_html($s2_heading); ?></h2>
							<?php endif; ?>
							<?php if ($s2_content) : ?>
							<div class="desc-content body-2"><?php echo wp_kses_post($s2_content); ?></div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
/* ============================================================
   SECTION 3 — Resources (Nội dung trái + Ảnh sticky phải)
   ============================================================ */
$s3_enable  = get_field('b2b_s3_enable', $pid);
$s3_heading = get_field('b2b_s3_heading', $pid);
$s3_content = get_field('b2b_s3_content', $pid);
$s3_image   = get_field('b2b_s3_image', $pid);
$s3_img_url = $s3_image ? $s3_image['url'] : '';

if ($s3_enable && ($s3_heading || $s3_content || $s3_img_url)) :
?>
	<section class="section-Resources" data-sticky-layout>
		<div class="container-fluid">
			<div class="wrap-padding">
				<div class="block-grid row">
					<?php if ($s3_heading || $s3_content) : ?>
					<div class="col-xl-4">
						<div class="main-content">
							<div class="box-content">
								<?php if ($s3_heading) : ?>
								<h2 class="heading-2"><?php echo esc_html($s3_heading); ?></h2>
								<?php endif; ?>
								<?php if ($s3_content) : ?>
								<div class="desc-content"><?php echo wp_kses_post($s3_content); ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<?php if ($s3_img_url) : ?>
					<div class="col-xl-8">
						<div class="box-img" data-stick-options='{"position": "right", "stickAbove": 1200}'>
							<div class="img img-ratio zoom-img">
								<img class="lozad" data-src="<?php echo esc_url($s3_img_url); ?>"
									alt="<?php echo esc_attr($s3_image['alt'] ?? ''); ?>" />
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
/* ============================================================
   SECTION 4 — B2B-3 (Ảnh full-width + văn bản overlay)
   ============================================================ */
$s4_enable  = get_field('b2b_s4_enable', $pid);
$s4_image   = get_field('b2b_s4_image', $pid);
$s4_heading = get_field('b2b_s4_heading', $pid);
$s4_content = get_field('b2b_s4_content', $pid);
$s4_img_url = $s4_image ? $s4_image['url'] : '';

if ($s4_enable && ($s4_img_url || $s4_heading || $s4_content)) :
?>
	<section class="section-B2B-3">
		<div class="img img-ratio ratio:pt-[720_1920] zoom-img">
			<?php if ($s4_img_url) : ?>
			<img class="lozad" data-src="<?php echo esc_url($s4_img_url); ?>"
				alt="<?php echo esc_attr($s4_image['alt'] ?? ''); ?>" />
			<?php endif; ?>

			<div class="main-content">
				<div class="container-fluid">
					<div class="box-content">
						<?php if ($s4_heading) : ?>
						<h2 class="heading-2"><?php echo esc_html($s4_heading); ?></h2>
						<?php endif; ?>
						<?php if ($s4_content) : ?>
						<div class="sub-title body-2"><?php echo wp_kses_post($s4_content); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
/* ============================================================
   SECTION 5 — B2B-4 (Production Capacity: Stats + Buttons)
   ============================================================ */
$s5_enable   = get_field('b2b_s5_enable', $pid);
$s5_bg_image = get_field('b2b_s5_bg_image', $pid);
$s5_heading  = get_field('b2b_s5_heading', $pid);
$s5_subtitle = get_field('b2b_s5_subtitle', $pid);
$s5_stats    = get_field('b2b_s5_stats', $pid);
$s5_buttons  = get_field('b2b_s5_buttons', $pid);
$s5_bg_url   = $s5_bg_image ? $s5_bg_image['url'] : '';

if ($s5_enable && ($s5_heading || $s5_stats)) :
	$bg_attr = $s5_bg_url ? ' data-bg-options=\'{"src":"' . esc_url($s5_bg_url) . '"}\'' : '';
?>
	<section class="section-B2B-4" <?php echo $bg_attr; ?>>
		<div class="section-py">
			<div class="container-fluid">
				<?php if ($s5_heading || $s5_subtitle) : ?>
				<div class="main-content">
					<?php if ($s5_heading) : ?>
					<h2 class="heading-1 text-white uppercase block text-center">
						<?php echo esc_html($s5_heading); ?>
					</h2>
					<?php endif; ?>
					<?php if ($s5_subtitle) : ?>
					<div class="sub-title body-2 text-white block text-center">
						<?php echo nl2br(esc_html($s5_subtitle)); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ($s5_stats) : ?>
				<div class="block-grid">
					<?php foreach ($s5_stats as $stat) :
					$stat_num   = intval($stat['number'] ?? 0);
					$stat_sfx   = $stat['suffix'] ?? '';
					$stat_sep   = $stat['separator'] ?? '';
					$stat_label = $stat['label'] ?? '';
					$stat_url   = $stat['url'] ?? '';

					$countup_opts = ['number' => $stat_num, 'duration' => 3, 'padZero' => true];
					if ($stat_sep) {
						$countup_opts['separator'] = $stat_sep;
					}
					$countup_json = wp_json_encode($countup_opts);

					$tag_open  = $stat_url ? '<a class="item-content" href="' . esc_url($stat_url) . '">' : '<div class="item-content">';
					$tag_close = $stat_url ? '</a>' : '</div>';
				?>
					<?php echo $tag_open; ?>
					<div class="number">
						<span data-countup-options='<?php echo esc_attr($countup_json); ?>'></span>
						<?php if ($stat_sfx) : ?>
						<span><?php echo esc_html($stat_sfx); ?></span>
						<?php endif; ?>
					</div>
					<?php if ($stat_label) : ?>
					<div class="content">
						<?php echo wp_kses_post($stat_label); ?>
					</div>
					<?php endif; ?>
					<?php echo $tag_close; ?>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ($s5_buttons) : ?>
				<div class="block-button">

					<?php foreach ($s5_buttons as $item) :

							$link = $item['link'] ?? null;

							if (!$link) {
								continue;
							}

							$btn_label = $link['title'] ?? '';
							$btn_url   = $link['url'] ?? '#';
							$btn_target = $link['target'] ?? '_self';

						?>

					<a class="btn btn-white" href="<?php echo esc_url($btn_url); ?>"
						target="<?php echo esc_attr($btn_target); ?>">
						<span><?php echo esc_html($btn_label); ?></span>
					</a>

					<?php endforeach; ?>

				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
/* ============================================================
   SECTION 6 — B2B-5 (Danh mục sản phẩm - Swiper)
   ============================================================ */
$s6_enable  = get_field('b2b_s6_enable', $pid);
$s6_heading = get_field('b2b_s6_heading', $pid);
$s6_terms   = get_terms([
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'orderby'    => 'menu_order',
	'order'      => 'ASC',
]);

if ($s6_enable && ($s6_heading || (!is_wp_error($s6_terms) && !empty($s6_terms)))) :
?>
	<section class="section-B2B-5">
		<div class="container-fluid">
			<div class="section-py">
				<?php if ($s6_heading) : ?>
				<h2 class="heading-1 block text-center title-heading mb-base">
					<?php echo esc_html($s6_heading); ?>
				</h2>
				<?php endif; ?>

				<?php if (!is_wp_error($s6_terms) && !empty($s6_terms)) : ?>
				<div class="block-swiper swiper-column-auto" data-swiper-id="B2B-5"
					data-swiper-options='{"slidesPerView": 2, "spaceBetween": 20, "centeredSlides": true, "loop": true, "watchDistance": true, "breakpoints": {"1200": {"slidesPerView": 5}}}'>
					<div class="swiper">
						<div class="swiper-wrapper">
							<?php foreach ($s6_terms as $term) :
							$t_name  = $term->name;
							$t_url   = get_term_link($term);
							$t_thumb = get_term_meta($term->term_id, 'thumbnail_id', true);
							$t_img   = $t_thumb ? wp_get_attachment_image_url($t_thumb, 'large') : '';
						?>
							<div class="swiper-slide">
								<a class="card-imageZoom" href="<?php echo esc_url($t_url); ?>">
									<div class="img img-ratio ratio:pt-[548_366] zoom-img">
										<?php if ($t_img) : ?>
										<img class="lozad" data-src="<?php echo esc_url($t_img); ?>"
											alt="<?php echo esc_attr($t_name); ?>" />
										<?php endif; ?>
									</div>
									<div class="content">
										<p><?php echo esc_html($t_name); ?></p>
									</div>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="swiper-button">
						<div class="btn-swiper btn-prev btn-swiper-primary" data-id-swiper="B2B-5">
							<div class="icon"></div>
						</div>
						<div class="btn-swiper btn-next btn-swiper-primary" data-id-swiper="B2B-5">
							<div class="icon"></div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
/* ============================================================
   SECTION 7 — Contact Form (từ Theme Options)
   ============================================================ */
get_template_part('modules/common/section-form');
?>

</main>

<?php get_footer();