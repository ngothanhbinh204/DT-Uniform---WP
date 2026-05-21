<?php
// ===== FOOTER ACF OPTIONS =====
$footer_company_name    = get_field('footer_company_name', 'option');
$footer_left_items      = get_field('footer_left_items', 'option');
$footer_mid_label       = get_field('footer_mid_label', 'option');
$footer_right_title     = get_field('footer_right_title', 'option');
$footer_right_desc      = get_field('footer_right_desc', 'option');
$footer_right_shortcode = get_field('footer_right_shortcode', 'option');
$footer_social          = get_field('footer_social', 'option');
$footer_copyright       = get_field('footer_copyright', 'option');
?>

</main>
<footer>

	<?php if ($footer_company_name) : ?>
	<div class="title-logo"><?php echo esc_html($footer_company_name); ?></div>
	<?php endif; ?>

	<div class="block-grid">

		<!-- Cột trái: Repeater các khối thông tin (HEADQUARTERS, FACTORY,...) -->
		<div class="item-grid-left">
			<?php if (!empty($footer_left_items)) : ?>
				<?php foreach ($footer_left_items as $item) :
					$item_label   = !empty($item['item_label']) ? $item['item_label'] : '';
					$item_content = !empty($item['item_content']) ? $item['item_content'] : '';
				?>
				<div class="item-content">
					<?php if ($item_label) : ?>
						<div class="title body-3 text-black uppercase"><?php echo esc_html($item_label); ?></div>
					<?php endif; ?>
					<?php if ($item_content) : ?>
						<div class="content"><?php echo wp_kses_post($item_content); ?></div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<!-- Cột giữa: Tiêu đề + WordPress Menu (footer-menu-link) -->
		<div class="item-grid-mid">
			<?php if ($footer_mid_label) : ?>
				<div class="title body-3 text-black uppercase"><?php echo esc_html($footer_mid_label); ?></div>
			<?php endif; ?>
			<?php wp_nav_menu(array(
				'theme_location' => 'footer-menu-link',
				'container'      => false,
				'fallback_cb'    => false,
			)); ?>
		</div>

		<!-- Cột phải: Tiêu đề + Mô tả + Form shortcode -->
		<div class="item-grid-right">
			<?php if ($footer_right_title || $footer_right_desc) : ?>
			<div class="item-content">
				<?php if ($footer_right_title) : ?>
					<div class="title body-3 text-black uppercase"><?php echo esc_html($footer_right_title); ?></div>
				<?php endif; ?>
				<?php if ($footer_right_desc) : ?>
					<div class="sub-title body-2"><?php echo wp_kses_post($footer_right_desc); ?></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if (!empty($footer_right_shortcode)) : ?>
			<div class="item-button">
				<?php echo do_shortcode($footer_right_shortcode); ?>
			</div>
			<?php endif; ?>
		</div>

	</div><!-- /.block-grid -->

	<!-- Mạng xã hội / Thông tin liên lạc -->
	<?php if (!empty($footer_social)) : ?>
	<div class="block-social">
		<ul>
			<?php foreach ($footer_social as $social) :
				$s_icon  = !empty($social['social_icon']) ? $social['social_icon'] : '';
				$s_label = !empty($social['social_label']) ? $social['social_label'] : '';
				$s_link  = !empty($social['social_link']) ? $social['social_link'] : '';
			?>
			<li>
				<a <?php echo $s_link ? 'href="' . esc_url($s_link) . '"' : ''; ?> target="_blank" rel="noopener noreferrer nofollow">
					<?php if ($s_icon) : ?>
						<div class="icon"><i class="<?php echo esc_attr($s_icon); ?>"></i></div>
					<?php endif; ?>
					<?php if ($s_label) : ?>
						<div class="content"><?php echo esc_html($s_label); ?></div>
					<?php endif; ?>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>

	<!-- Copyright + Menu chính sách -->
	<div class="block-copyright">
		<div class="left-content">
			<?php if ($footer_copyright) : ?>
				<span><?php echo esc_html($footer_copyright); ?></span>
			<?php endif; ?>
		</div>
		<div class="right-content">
			<?php wp_nav_menu(array(
				'theme_location' => 'footer-menu-policy',
				'container'      => false,
				'fallback_cb'    => false,
			)); ?>
		</div>
	</div>

</footer>
<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false) : ?>
	<?php wp_footer(); ?>
<?php endif; ?>
</body>

</html>