<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&amp;display=swap"
		rel="stylesheet">
	<link
		href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
	<?php wp_head(); ?>
</head>

<?php
// ===== VARIABLES SECTION =====
// User and authentication
$current_user        = wp_get_current_user();
$is_user_logged_in   = is_user_logged_in();

// URLs
$cart_url            = wc_get_cart_url();
$myaccount_url       = get_permalink(get_option('woocommerce_myaccount_page_id'));
$login_page_url      = get_page_link_by_template('template-woocommerce/page-login.php');
$register_page_url   = get_page_link_by_template('template-woocommerce/page-register.php');

// Logo
$custom_logo_id      = get_theme_mod('custom_logo');
$logo_image          = wp_get_attachment_image_src($custom_logo_id, 'full');

// Cart
$cart_items_count    = WC()->cart->get_cart_contents_count();
$cart_count_display  = $cart_items_count ? $cart_items_count : 0;

// ACF Options
$product_detail      = is_product() ? 'product-detail' : '';
$config_head         = get_field('config_head', 'option');
$config_body         = get_field('config_body', 'option');
$header_top_text     = get_field('header_top_text', 'option');

if (!empty($config_head)) {
	echo $config_head;
}
?>

<body <?php body_class(get_field('add_class_body', get_the_ID()) . ' ' . $product_detail); ?>>
	<?php echo !empty($config_body) ? $config_body : ''; ?>

	<header>
		<div class="section-header">

			<?php if (!empty($header_top_text)) : ?>
			<!-- Header Top: Banner thông báo -->
			<div class="header-top">
				<div class="title-header body-4 text-white"><?php echo esc_html($header_top_text); ?></div>
				<div class="icon-header"><i class="fa-thin fa-arrow-right"></i></div>
			</div>
			<?php endif; ?>

			<!-- Header Bottom: Logo + Menu + Actions -->
			<div class="header-bottom">

				<!-- Logo -->
				<a class="header-logo" href="<?php echo esc_url(home_url('/')); ?>">
					<?php if ($logo_image) : ?>
					<div class="img img-ratio ratio:pt-[72_94]">
						<img class="lozad" data-src="<?php echo esc_url($logo_image[0]); ?>"
							alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
					</div>
					<?php else : ?>
					<span><?php bloginfo('name'); ?></span>
					<?php endif; ?>
				</a>

				<div class="header-main">

					<!-- Menu trái + Menu phải -->
					<div class="main-top">
						<div class="header-left">
							<?php wp_nav_menu(array(
								'theme_location' => 'header-menu-left',
								'container'      => false,
								'fallback_cb'    => false,
							)); ?>
						</div>
						<div class="header-right">
							<?php wp_nav_menu(array(
								'theme_location' => 'header-menu-right',
								'container'      => false,
								'fallback_cb'    => false,
							)); ?>
						</div>
					</div>

					<!-- Search + Cart + Language + Hamburger -->
					<div class="main-bottom">

						<!-- Search -->
						<div class="header-Seacher">
							<form action="<?php echo esc_url(home_url('/')); ?>" method="get">
								<div class="icon-Seacher">
									<div class="img img-ratio ratio:pt-[1_1]">
										<!-- <i class="fa-light fa-magnifying-glass"></i> -->
										<img class="lozad"
											src="<?php echo get_template_directory_uri(); ?>/img/Seacher.svg"
											alt="<?php esc_attr_e('Tìm kiếm', 'canhcamtheme'); ?>" />
									</div>
								</div>
								<input type="text" placeholder="<?php esc_attr_e('Tìm kiếm...', 'canhcamtheme'); ?>"
									name="s" value="<?php echo esc_attr(get_search_query()); ?>">
							</form>
						</div>

						<div class="header-cart">

							<!-- Profile / My Account -->
							<a class="profile-icon"
								href="<?php echo $is_user_logged_in ? esc_url($myaccount_url) : esc_url($login_page_url); ?>"
								aria-label="<?php esc_attr_e('Tài khoản', 'canhcamtheme'); ?>">
								<div class="img img-ratio ratio:pt-[1_1]">
									<!-- <i class="fa-light fa-user"></i> -->
									<img class="lozad" src="<?php echo get_template_directory_uri(); ?>/img/avatar.svg"
										alt="<?php esc_attr_e('Tài khoản', 'canhcamtheme'); ?>" />
								</div>
							</a>

							<!-- Cart -->
							<a class="cart-icon open-cart" aria-label="<?php esc_attr_e('Cart', 'canhcamtheme'); ?>">
								<div class="img img-ratio ratio:pt-[1_1]">
									<img class="lozad" src="<?php echo get_template_directory_uri(); ?>/img/cart.svg"
										alt="<?php esc_attr_e('Cart', 'canhcamtheme'); ?>" />
								</div>
								<div class="cart-number"><span><?php echo absint($cart_count_display); ?></span></div>
							</a>

							<!-- Language Switcher (WPML) -->
							<div class="header-lang">
								<?php echo do_shortcode('[wpml_lang_selector]'); ?>
							</div>

							<!-- Mobile Hamburger -->
							<div class="header-mobile">
								<div class="header-hamburger">
									<div class="wrap"><span></span><span></span><span></span></div>
									<div id="pulseMe">
										<div class="bar left"></div>
										<div class="bar top"></div>
										<div class="bar right"></div>
										<div class="bar bottom"></div>
									</div>
								</div>
							</div>

						</div><!-- /.header-cart -->
					</div><!-- /.main-bottom -->
				</div><!-- /.header-main -->
			</div><!-- /.header-bottom -->
		</div><!-- /.section-header -->
	</header>


	<!-- Overlay Mobile -->
	<div class="menu-overlay mobile-overlay"></div>

	<!-- ==================== MOBILE MENU ==================== -->
	<div class="navbar-mobile p-0">
		<div class="mobi-bg w-full md:w-1/2 xl:w-[450px] !max-w-full h-full bg-white z-50 p-5 relative">

			<!-- Search Mobile -->
			<div class="header-search-form-mobile productsearchbox">
				<form action="<?php echo esc_url(home_url('/')); ?>" method="get">
					<input type="text" placeholder="<?php esc_attr_e('Tìm kiếm...', 'canhcamtheme'); ?>" name="s"
						value="<?php echo esc_attr(get_search_query()); ?>">
					<button class="btn-search" type="submit"><?php esc_html_e('Tìm kiếm', 'canhcamtheme'); ?></button>
				</form>
			</div>

			<!-- User Mobile -->
			<div class="header-user-mobile">
				<?php if ($is_user_logged_in) : ?>
				<a class="user-profile" href="<?php echo esc_url($myaccount_url); ?>">
					<div class="icon">
						<div class="img img-ratio ratio:pt-[1_1]">
							<i class="fa-light fa-user"></i>
						</div>
					</div>
					<div class="namer-info"><?php echo esc_html($current_user->display_name); ?></div>
				</a>
				<?php else : ?>
				<div class="button-login">
					<a class="btn btn-secondary" href="<?php echo esc_url($register_page_url); ?>">
						<span><?php esc_html_e('Đăng Ký', 'canhcamtheme'); ?></span>
					</a>
					<a class="btn btn-secondary" href="<?php echo esc_url($login_page_url); ?>">
						<span><?php esc_html_e('Đăng nhập', 'canhcamtheme'); ?></span>
					</a>
				</div>
				<?php endif; ?>
			</div>

			<!-- Mobile Menu List (dùng Menu chính - Mobile) -->
			<div class="menu-list">
				<?php wp_nav_menu(array(
					'theme_location' => 'header-menu',
					'container'      => false,
					'fallback_cb'    => false,
				)); ?>
			</div>

			<!-- Language Mobile (WPML) -->
			<div class="header-langMobile">
				<?php echo do_shortcode('[wpml_lang_selector]'); ?>
			</div>

		</div>
	</div><!-- /.navbar-mobile -->

	<!-- ==================== SEARCH OVERLAY ==================== -->
	<div class="header-search-form">
		<div class="close"><i class="fa-light fa-xmark"></i></div>
		<div class="container">
			<form class="wrap-form-search-product" action="<?php echo esc_url(home_url('/')); ?>" method="get">
				<div class="productsearchbox">
					<input type="text" placeholder="<?php esc_attr_e('Tìm kiếm...', 'canhcamtheme'); ?>" name="s"
						value="<?php echo esc_attr(get_search_query()); ?>">
					<button class="btn-search" type="submit"><?php esc_html_e('Tìm kiếm', 'canhcamtheme'); ?></button>
				</div>
				<div class="message-search"><?php esc_html_e('Nhấn', 'canhcamtheme'); ?> <span>Esc</span>
					<?php esc_html_e('để đóng', 'canhcamtheme'); ?></div>
			</form>
		</div>
	</div>

	<div class="mini-cart-wrapper">
		<div class="top-mini-cart">
			<p><?php esc_html_e('Cart', 'canhcamtheme'); ?></p>
			<button class="close" aria-label="<?php esc_attr_e('Close', 'canhcamtheme'); ?>" fdprocessedid="a57ydq"><i
					class="fa-light fa-xmark"></i></button>
		</div>
		<div class="widget_shopping_cart_content">

			<?php woocommerce_mini_cart(); ?>


		</div>
	</div>

	<main>
		<?php get_template_part('modules/common/banner'); ?>