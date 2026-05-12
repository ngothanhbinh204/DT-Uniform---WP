<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&amp;display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false) : ?>
	<?php endif; ?>
	<?php wp_head(); ?>


</head>
<?php
// ===== VARIABLES SECTION =====
// User and authentication
$current_user = wp_get_current_user();
$is_user_logged_in = is_user_logged_in();

// URLs
$template_directory = get_template_directory_uri();
$cart_url = wc_get_cart_url();
$myaccount_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
$login_page_url = get_page_link_by_template('template-woocommerce/page-login.php');
$register_page_url = get_page_link_by_template('template-woocommerce/page-register.php');
// Logo
$custom_logo_id = get_theme_mod('custom_logo');
$logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');

// Cart and checkout
$is_checkout = is_checkout() || is_cart();
$cart_items_count = WC()->cart->get_cart_contents_count();
$cart_count_display = $cart_items_count ? $cart_items_count : 0;

// Site info
$site_name = get_bloginfo('name');
$header = get_field('header', 'option');
$product_detail = is_product() ? 'product-detail' : '';
$config_head = get_field('config_head', 'option');
$config_body = get_field('config_body', 'option');
if (!empty($config_head)) {
	echo $config_head;
}
?>

<body <?php body_class(get_field('add_class_body', get_the_ID()) . ' ' . $product_detail) ?>>
	<?php echo !empty($config_body) ? $config_body : ''; ?>
	<header class="header">
		<div class="container">
			<div class="header-wrapper">
				<div class="header-logo rem:w-[202px]">
					<?php the_custom_logo(); ?>
				</div>
				<div class="header-menu">
					<?php wp_nav_menu(array(
						'theme_location' => 'header-menu',
						'menu_id' => 'header-menu',
						'menu_class' => 'header-nav',
					)); ?>
				</div>
				<div class="header-language">
					<?php do_action('wpml_add_language_selector'); ?>
				</div>
				<div class="header-hambuger"><span></span><span></span><span></span>
					<div id="pulseMe">
						<div class="bar left"></div>
						<div class="bar top"></div>
						<div class="bar right"></div>
						<div class="bar bottom"></div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<div class="header-overlay"></div>
	<div class="header-search-form">
		<div class="close flex items-center justify-center absolute top-0 right-0 bg-white text-3xl cursor-pointer w-12.5 h-12.5"><i class="fa-light fa-xmark"></i></div>
		<div class="container">
			<form class="wrap-form-search-product" action="<?php echo home_url(); ?>" method="get">
				<div class="productsearchbox">
					<input type="text" placeholder="Tìm kiếm thông tin" name="s" value="<?php echo get_search_query(); ?>">
					<button><i class="fa-light fa-magnifying-glass"></i></button>
				</div>
			</form>
		</div>
	</div>
	<main>


		<?php get_template_part('modules/common/banner'); ?>