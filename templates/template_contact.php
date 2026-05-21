<?php
/*
Template name: Page - Liên hệ
*/

if ( ! defined( 'ABSPATH' ) ) exit;

$pid            = get_the_ID();
$banner_image   = get_field( 'contact_banner_image', $pid );
$banner_title   = get_field( 'contact_banner_title', $pid ) ?: get_the_title();
$banner_url     = ! empty( $banner_image['url'] ) ? $banner_image['url'] : '';
$banner_alt     = ! empty( $banner_image['alt'] ) ? $banner_image['alt'] : esc_attr( $banner_title );

$company_name      = get_field( 'contact_company_name', $pid );
$info_items        = get_field( 'contact_info_items', $pid );
$follow_heading    = get_field( 'contact_follow_heading', $pid );
$social_items      = get_field( 'contact_social_items', $pid );

$form_heading      = get_field( 'contact_form_heading', $pid );
$form_desc         = get_field( 'contact_form_desc', $pid );
$form_shortcode    = get_field( 'contact_form_shortcode', $pid );
$form_disclaimer   = get_field( 'contact_form_disclaimer', $pid );

$branches          = get_field( 'contact_branches', $pid );
$map_url           = get_field( 'contact_map_url', $pid );

get_header();
?>

<?php /* ── Section 1: Banner ──────────────────────────────────────────────── */ ?>
<section class="section-NormalBanner">
	<div class="img img-parallax ratio:pt-[450_1920]"
		data-gsap-options='{"type": "img-parallax-percent", "yPercent": 15}'>
		<?php if ( $banner_url ) : ?>
		<img class="lozad" data-src="<?php echo esc_url( $banner_url ); ?>"
			alt="<?php echo esc_attr( $banner_alt ); ?>" />
		<?php endif; ?>
		<div class="main-content">
			<div class="title-heading heading-1 text-white uppercase">
				<?php echo esc_html( $banner_title ); ?>
			</div>
			<div class="global-breadcrumb">
				<div class="section-px">
					<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) rank_math_the_breadcrumbs(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php /* ── Section 2: Contact info + Form ─────────────────────────────────── */ ?>
<section class="contact-1 section-py">
	<div class="container">
		<div class="wrapper grid grid-cols-12 xl:gap-0 gap-base">

			<?php /* Left: company info */ ?>
			<div class="col-left lg:col-span-5 col-span-full xl:rem:pr-[63px]">
				<div class="box">
					<?php if ( $company_name ) : ?>
					<h2 class="heading-4 font-bold text-Utility-white">
						<?php echo esc_html( $company_name ); ?>
					</h2>
					<?php endif; ?>

					<?php if ( $info_items ) : ?>
					<div class="contact-info">
						<?php foreach ( $info_items as $info ) :
							$label   = esc_html( $info['label'] ?? '' );
							$content = $info['content'] ?? '';
						?>
						<div class="item">
							<?php if ( $label ) : ?>
							<div class="label"><?php echo $label; ?></div>
							<?php endif; ?>
							<?php if ( $content ) : ?>
							<div class="content">
								<?php echo wp_kses_post( $content ); ?>
							</div>
							<?php endif; ?>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if ( $social_items ) : ?>
					<div class="contact-follow">
						<?php if ( $follow_heading ) : ?>
						<h3><?php echo esc_html( $follow_heading ); ?></h3>
						<?php endif; ?>
						<div class="contact-social">
							<ul>
								<?php foreach ( $social_items as $social ) :
									$icon = esc_attr( $social['icon'] ?? '' );
									$url  = ! empty( $social['url'] ) ? esc_url( $social['url'] ) : '#';
								?>
								<li>
									<a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
										<?php if ( $icon ) : ?>
										<i class="<?php echo $icon; ?>"></i>
										<?php endif; ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<?php /* Right: contact form */ ?>
			<div class="col-right lg:col-span-7 col-span-full xl:pl-4">
				<?php if ( $form_heading || $form_desc ) : ?>
				<div class="heading mb-6">
					<?php if ( $form_heading ) : ?>
					<h2 class="heading-title mb-4"><?php echo esc_html( $form_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $form_desc ) : ?>
					<div class="desc body-1 font-normal text-center">
						<?php echo nl2br( esc_html( $form_desc ) ); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( $form_shortcode ) : ?>
				<div class="block-form">
					<?php echo do_shortcode( wp_kses_post( $form_shortcode ) ); ?>
				</div>
				<?php endif; ?>

				<?php if ( $form_disclaimer ) : ?>
				<div class="desc-form">
					<div class="content-left">
						<p><?php echo esc_html( $form_disclaimer ); ?></p>
					</div>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>

<?php /* ── Section 3: Branches list + Map ───────────────────────────────────── */ ?>
<?php
$initial_map = '';
if ( ! empty( $branches ) ) {
	$first_map = get_field( 'map_iframe', $branches[0]->ID );
	if ( $first_map ) $initial_map = $first_map;
}
if ( ! $initial_map && $map_url ) {
	$initial_map = '<iframe src="' . esc_url( $map_url ) . '" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
}
?>
<?php if ( $branches || $initial_map ) : ?>
<section class="system contact-2 section-py">
	<div class="container">
		<div class="wrapper flex flex-col md:flex-row gap-base">

			<?php if ( $branches ) : ?>
			<div class="col-left md:rem:max-w-[560px] max-w-full w-full rem:h-[545px] overflow-y-auto"
				id="shop-list" data-lenis-prevent>
				<?php foreach ( $branches as $shop ) :
					$shop_id     = $shop->ID;
					$shop_title  = get_the_title( $shop_id );
					$shop_items  = get_field( 'items', $shop_id ) ?: [];
					$shop_iframe = get_field( 'map_iframe', $shop_id );
				?>
				<div class="item" data-iframe="<?php echo $shop_iframe ? esc_attr( $shop_iframe ) : ''; ?>">
					<?php if ( $shop_title ) : ?>
					<div class="title title-24 font-bold mb-2"><?php echo esc_html( $shop_title ); ?></div>
					<?php endif; ?>
					<?php if ( $shop_items ) : ?>
					<div class="system-contact">
						<ul>
						<?php foreach ( $shop_items as $item ) :
							$item_icon    = ! empty( $item['icon'] ) ? $item['icon'] : '<i class="fa-solid fa-location-dot"></i>';
							$item_content = $item['content'] ?? '';
							$item_link    = ! empty( $item['link'] ) ? esc_url( $item['link'] ) : '';
						?>
						<li>
							<a <?php if ( $item_link ) : ?>href="<?php echo $item_link; ?>" target="_blank" rel="noopener noreferrer"<?php endif; ?>>
								<div class="icon"><?php echo $item_icon; ?></div>
								<span><?php echo esc_html( $item_content ); ?></span>
							</a>
						</li>
						<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<?php if ( $initial_map ) : ?>
			<div class="col-right flex-1">
				<div class="map" id="map-iframe">
					<?php echo $initial_map; ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();