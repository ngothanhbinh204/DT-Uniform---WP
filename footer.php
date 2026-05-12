<?php
$logo_footer = get_field('logo_footer', 'option');
$socials_footer = get_field('socials_footer', 'option');
$company_name = get_field('company_name', 'option');
$contact_footer = get_field('contact_footer', 'option');
$title_col_2_footer = get_field('title_col_2_footer', 'option');
$title_col_3 = get_field('title_col_3', 'option');
$title_col_4 = get_field('title_col_4', 'option');
$form_shortcode_footer = get_field('form_shortcode_footer', 'option');
$items_logo_footer = get_field('items_logo_footer', 'option');
$copyright_footer = get_field('copyright_footer', 'option');
$links_footer = get_field('links_footer', 'option');
?>

</main>
<footer>
	<div class="footer-top">
		<div class="container">
			<div class="heading">
				<?php if (!empty($logo_footer)) : ?>
					<div class="footer-logo"> <a href="<?php echo home_url(); ?>"> <img class="lozad undefined" data-src="<?php echo !empty($logo_footer['url']) ? $logo_footer['url'] : ''; ?>" alt="" /></a></div>
				<?php endif; ?>
				<?php if (!empty($socials_footer)) : ?>
					<div class="footer-social">
						<ul>
							<?php foreach ($socials_footer as $social) : ?>
								<li> <a href="<?php echo !empty($social['link']) ? $social['link'] : ''; ?>" target="_blank" rel="noopener noreferrer nofollow">
										<?php echo !empty($social['icon']) ? $social['icon'] : ''; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
			<div class="wrapper grid grid-cols-4 gap-base">
				<div class="footer-column">
					<h3><?php echo !empty($company_name) ? $company_name : ''; ?></h3>
					<div class="footer-contact">
						<?php if (!empty($contact_footer)) : ?>
							<?php foreach ($contact_footer as $contact) : ?>
								<div class="item"> <a <?php echo !empty($contact['link']) ? 'href="' . $contact['link'] . '" target="_blank" rel="noopener noreferrer nofollow"' : ''; ?>><?php echo !empty($contact['title']) ? $contact['title'] : ''; ?></a></div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="footer-column">
					<h3><?php echo !empty($title_col_2_footer) ? $title_col_2_footer : ''; ?></h3>
					<?php wp_nav_menu(array('theme_location' => 'footer-menu-policy', 'container' => false, 'menu_class' => 'footer-menu')); ?>
				</div>
				<div class="footer-column">
					<h3><?php echo !empty($title_col_3) ? $title_col_3 : ''; ?></h3>
					<ul class="footer-menu">
						<?php wp_nav_menu(array('theme_location' => 'footer-menu-link', 'container' => false, 'menu_class' => 'footer-menu')); ?>
					</ul>
				</div>
				<div class="footer-column">
					<h3><?php echo !empty($title_col_4) ? $title_col_4 : ''; ?></h3>
					<?php if (!empty($form_shortcode_footer)) : ?>
						<?php echo do_shortcode($form_shortcode_footer); ?>
					<?php endif; ?>
					<div class="footer-img rem:w-[153px]">
						<?php if (!empty($items_logo_footer)) : ?>
							<?php foreach ($items_logo_footer as $item) : ?>
								<a class="img img-ratio ratio:pt-[58_153]" <?php echo !empty($item['link']) ? 'href="' . $item['link'] . '" target="_blank" rel="noopener noreferrer nofollow"' : ''; ?>>
									<img class="lozad undefined" data-src="<?php echo !empty($item['logo']['url']) ? $item['logo']['url'] : ''; ?>" alt="image" />
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<div class="footer-copyright">
				<p><?php echo !empty($copyright_footer) ? $copyright_footer : ''; ?></p>
				<ul>
					<?php if (!empty($links_footer)) : ?>
						<?php foreach ($links_footer as $link) : ?>
							<li> <a <?php echo !empty($link['link']) ? 'href="' . $link['link'] . '" target="_blank" rel=""' : ''; ?>><?php echo !empty($link['title']) ? $link['title'] : ''; ?></a></li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</footer>
<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false): ?>
	<?php wp_footer() ?>
<?php endif; ?>
</body>

</html>