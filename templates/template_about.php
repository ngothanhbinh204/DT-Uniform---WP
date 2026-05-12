<?php

/**
 * Template Name: Giới thiệu
 */
global $post;
$home_page_id = apply_filters('wpml_object_id', get_option('page_on_front'), 'page', true);
$home_partners_title = get_field('home_partners_title', $home_page_id);
$home_partners_desc = get_field('home_partners_desc', $home_page_id);
$home_partners_groups = get_field('home_partners_groups', $home_page_id);
$home_philosophy_title = get_field('home_philosophy_title', $home_page_id);
$home_philosophy_desc = get_field('home_philosophy_desc', $home_page_id);
$home_philosophy_button = get_field('home_philosophy_button', $home_page_id);
$home_philosophy_person_name = get_field('home_philosophy_person_name', $home_page_id);
$home_philosophy_person_position = get_field('home_philosophy_person_position', $home_page_id);

$gioi_thieu_tieu_de = get_field('gioi_thieu_tieu_de', $post->ID);
$gioi_thieu_noi_dung = get_field('gioi_thieu_noi_dung', $post->ID);
$gioi_thieu_so_lieu = get_field('gioi_thieu_so_lieu', $post->ID);
$background_sec_1 = get_field('background_sec_1', $post->ID);
$y_nghia_anh_chinh = get_field('y_nghia_anh_chinh', $post->ID);
$y_nghia_logo = get_field('y_nghia_logo', $post->ID);
$y_nghia_tieu_de = get_field('y_nghia_tieu_de', $post->ID);
$y_nghia_noi_dung = get_field('y_nghia_noi_dung', $post->ID);
$background_sec_2 = get_field('background_sec_2', $post->ID);
$background_sec_3 = get_field('background_sec_3', $post->ID);
$image_sec_4 = get_field('image_sec_4', $post->ID);
$tabs_sec_4 = get_field('tabs_sec_4', $post->ID);
$gtcl_tieu_de = get_field('gtcl_tieu_de', $post->ID);
$gtcl_logo = get_field('gtcl_logo', $post->ID);
$gtcl_danh_sach = get_field('gtcl_danh_sach', $post->ID);
$chat_luong_tieu_de = get_field('chat_luong_tieu_de', $post->ID);
$chat_luong_mo_ta = get_field('chat_luong_mo_ta', $post->ID);
$chat_luong_danh_sach = get_field('chat_luong_danh_sach', $post->ID);


get_header();
?>
<section class="section-scrollTo-active" id="menu-spy">
	<ul>
		<li> <a href="#about"><?php _e('Giới thiệu', 'canhcamtheme'); ?></a></li>
		<li> <a href="#meaning"><?php _e('Ý nghĩa thương hiệu', 'canhcamtheme'); ?></a></li>
		<li> <a href="#vision"><?php _e('Tầm nhìn - Sứ mệnh - Giá trị cốt lõi', 'canhcamtheme'); ?></a></li>
		<li> <a href="#philosophy"><?php _e('Triết lý kinh doanh', 'canhcamtheme'); ?></a></li>
		<li> <a href="#quality"><?php _e('Cam kết chất lượng', 'canhcamtheme'); ?></a></li>
		<li> <a href="#customer"><?php _e('Đối tác', 'canhcamtheme'); ?></a></li>
	</ul>
</section>
<section class="about-2" id="about" setBackground="<?php echo !empty($background_sec_1['url']) ? $background_sec_1['url'] : ''; ?>">
	<div class="container">
		<div class="wrapper grid lg:grid-cols-[40.29%_1fr] xl:rem:gap-[156px] gap-base">
			<div class="col-left section-py">
				<h2 class="heading-2 text-Utility-white uppercase mb-base" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($gioi_thieu_tieu_de) ? $gioi_thieu_tieu_de : ''; ?></h2>
				<div class="format-content text-Utility-white" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="400">
					<?php echo !empty($gioi_thieu_noi_dung) ? $gioi_thieu_noi_dung : ''; ?>
				</div>
			</div>
			<div class="col-right" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="600">
				<?php if (!empty($gioi_thieu_so_lieu)) : ?>
					<div class="number-list inline-flex items-center gap-20 ">
						<?php foreach ($gioi_thieu_so_lieu as $item) : ?>
							<div class="item">
								<div class="number countup" data-number="<?php echo !empty($item['gia_tri']) ? $item['gia_tri'] : ''; ?>"> <span class="count-value"></span><span class="suffix"><?php echo !empty($item['hau_to']) ? $item['hau_to'] : ''; ?></span></div>
								<div class="title font-bold"><?php echo !empty($item['nhan']) ? $item['nhan'] : ''; ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<section class="about-3" id="meaning">
	<?php if (!empty($background_sec_2)) : ?>
		<div class="decor"> <img src="<?php echo !empty($background_sec_2['url']) ? $background_sec_2['url'] : ''; ?>" alt="image"></div>
	<?php endif; ?>
	<div class="container">
		<div class="wrapper grid lg:grid-cols-2 grid-cols-1 items-center">
			<div class="col-left relative z-10" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
				<div class="img lg:rem:w-[654px]">
					<?php if (!empty($y_nghia_anh_chinh)) : ?>
						<div class="image img-ratio ratio:pt-[475_654]"><img class="lozad undefined" data-src="<?php echo !empty($y_nghia_anh_chinh['url']) ? $y_nghia_anh_chinh['url'] : ''; ?>" alt="image" />
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-right flex flex-col gap-8 xl:rem:pl-[74px]">
				<div class="logo rem:w-[127px]" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
					<div class="image img-ratio ratio:pt-[82_127]">
						<?php if (!empty($y_nghia_logo)) : ?>
							<img class="lozad undefined" data-src="<?php echo !empty($y_nghia_logo['url']) ? $y_nghia_logo['url'] : ''; ?>" alt="image" />
						<?php endif; ?>
					</div>
				</div>
				<h2 class="heading-2 uppercase text-Utility-white" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
					<?php echo !empty($y_nghia_tieu_de) ? $y_nghia_tieu_de : ''; ?> </h2>
				<div class="format-content text-Utility-white font-normal" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="600">
					<?php echo !empty($y_nghia_noi_dung) ? $y_nghia_noi_dung : ''; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="about-4 section-py" id="vision" setBackground="<?php echo !empty($background_sec_3['url']) ? $background_sec_3['url'] : ''; ?>">
	<div class="vector"> <img src="<?php echo get_template_directory_uri() ?>/img/Vector.svg" alt=""></div>
	<div class="container">
		<div class="wrapper grid lg:grid-cols-[51.14%_1fr] xl:rem:gap-[120px] gap-base">
			<div class="col-left" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
				<?php if (!empty($image_sec_4)) : ?>
					<div class="global-img img-ratio ratio:pt-[658_716]">
						<img class="lozad undefined" data-src="<?php echo !empty($image_sec_4['url']) ? $image_sec_4['url'] : ''; ?>" alt="" />
					</div>
				<?php endif; ?>
			</div>
			<div class="col-right">
				<?php if (!empty($tabs_sec_4)) : ?>
					<div class="wrap" data-toggle="tabslet">
						<ul class="tabslet-tab" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
							<?php foreach ($tabs_sec_4 as $key => $tab) : ?>
								<li class="<?php echo $key == 0 ? 'active' : ''; ?>"><a href="#tab<?php echo $key + 1; ?>"><?php echo !empty($tab['title_tab']) ? $tab['title_tab'] : ''; ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php foreach ($tabs_sec_4 as $key => $tab) : ?>
							<div class="tabslet-content <?php echo $key == 0 ? 'active' : ''; ?>" id="tab<?php echo $key + 1; ?>">
								<div class="heading">
									<h2 class="heading-title" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($tab['title']) ? $tab['title'] : ''; ?></h2>
								</div>
								<div class="format-content mt-base" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
									<?php echo !empty($tab['description']) ? $tab['description'] : ''; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<section class="section-about-5 section-py !pt-0">
	<div class="container">
		<div class="text-center">
			<h2 class="heading-title mb-base text-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($gtcl_tieu_de) ? $gtcl_tieu_de : ''; ?></h2>
		</div>
		<div class="box-core-item" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="400">
			<div class="circle-middle-item"><img class="lozad undefined" data-src="<?php echo get_template_directory_uri() ?>/img/circle.png" alt="" />
				<?php if (!empty($gtcl_logo)) : ?>
					<div class="logo"> <a href="<?php echo home_url(); ?>" "> <img class=" lozad undefined" data-src="<?php echo !empty($gtcl_logo['url']) ? $gtcl_logo['url'] : ''; ?>" alt="" /></a></div>
				<?php endif; ?>
			</div>
			<div class="top-core-item wrap-core-item">
				<?php if (!empty($gtcl_danh_sach)) : ?>
					<?php foreach ($gtcl_danh_sach as $key => $item) : ?>
						<?php if ($key % 2 == 0) : ?>
							<div class="core-item">
								<div class="content flex flex-col gap-3">
									<div class="title"><?php echo !empty($item['tieu_de']) ? $item['tieu_de'] : ''; ?></div>
									<div class="ctn"><?php echo !empty($item['mo_ta']) ? $item['mo_ta'] : ''; ?></div>
									<div class="button-more">
										<a href="#form-value-<?php echo $key; ?>" data-fancybox> <span><?php _e('Xem thêm', 'canhcamtheme'); ?></span>
											<div class="icon">
												<i class="fa-solid fa-angle-right"></i>
											</div>
										</a>
									</div>
								</div>
								<?php if (!empty($item['bieu_tuong'])) : ?>
									<div class="hexagon flex-center"><img src="<?php echo !empty($item['bieu_tuong']['url']) ? $item['bieu_tuong']['url'] : ''; ?>" alt=""></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>

			</div>
			<div class="bottom-core-item wrap-core-item">
				<?php if (!empty($gtcl_danh_sach)) : ?>
					<?php foreach ($gtcl_danh_sach as $key => $item) : ?>
						<?php if ($key % 2 != 0) : ?>
							<div class="core-item">
								<div class="content flex flex-col gap-3">
									<div class="title"><?php echo !empty($item['tieu_de']) ? $item['tieu_de'] : ''; ?></div>
									<div class="ctn"><?php echo !empty($item['mo_ta']) ? $item['mo_ta'] : ''; ?></div>
									<div class="button-more">
										<a href="#form-value-<?php echo $key; ?>" data-fancybox> <span><?php _e('Xem thêm', 'canhcamtheme'); ?></span>
											<div class="icon">
												<i class="fa-solid fa-angle-right"></i>
											</div>
										</a>
									</div>
								</div>
								<?php if (!empty($item['bieu_tuong'])) : ?>
									<div class="hexagon flex-center"><img src="<?php echo !empty($item['bieu_tuong']['url']) ? $item['bieu_tuong']['url'] : ''; ?>" alt=""></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if (!empty($gtcl_danh_sach)) : ?>
	<?php foreach ($gtcl_danh_sach as $key => $item) : ?>
		<div id="form-value-<?php echo $key; ?>" class="form-value" style="display: none;" data-fancybox-modal>
			<div class="popup-content w-full relative z-50">
				<div class="core-item">
					<div class="hexagon flex-center"><img src="<?php echo !empty($item['bieu_tuong']['url']) ? $item['bieu_tuong']['url'] : ''; ?>" alt=""></div>
					<div class="content flex flex-col gap-3">
						<div class="title"><?php echo !empty($item['tieu_de']) ? $item['tieu_de'] : ''; ?></div>
						<div class="ctn"><?php echo !empty($item['mo_ta']) ? $item['mo_ta'] : ''; ?></div>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
<section class="home-3 section-96" id="philosophy">
	<div class="container">
		<div class="wrapper">
			<div class="heading xl:mb-20 mb-base">
				<h2 class="heading-title mb-8" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_philosophy_title) ? $home_philosophy_title : ''; ?></h2>
				<div class="desc body-1 font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
					<?php echo !empty($home_philosophy_desc) ? $home_philosophy_desc : ''; ?>
				</div>
			</div>
			<div class="bottom flex items-center justify-between" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="600">
				<div class="left">

				</div>
				<div class="right flex flex-col gap-3 text-center">
					<div class="name rem:text-[32px] font-bold text-Primary-5"><?php echo !empty($home_philosophy_person_name) ? $home_philosophy_person_name : ''; ?></div>
					<div class="position body-1 font-normal">
						<?php echo !empty($home_philosophy_person_position) ? $home_philosophy_person_position : ''; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="about-7" id="quality">
	<div class="container default-container-js">
		<div class="wrapper  grid lg:grid-cols-2 items-center">
			<div class="col-left xl:pr-20">
				<div class="top mb-base">
					<h2 class="heading-title mb-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($chat_luong_tieu_de) ? $chat_luong_tieu_de : ''; ?></h2>
					<div class="desc font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
						<?php echo !empty($chat_luong_mo_ta) ? $chat_luong_mo_ta : ''; ?>
					</div>
				</div>
				<div class="wrap-item-toggle flex flex-col gap-4" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
					<?php if (!empty($chat_luong_danh_sach)) : ?>
						<?php foreach ($chat_luong_danh_sach as $key => $item) : ?>
							<div class="item-toggle group transition-300" data-id="<?php echo $key + 1; ?>">
								<div class="title flex items-center justify-between cursor-pointer transition-300" data-target="program-<?php echo $key + 1; ?>">
									<span class="body-1 text-Utility-gray-500 font-bold"><?php echo !empty($item['tieu_de']) ? $item['tieu_de'] : ''; ?></span><i class="fa-solid fa-plus"></i>
								</div>
								<div class="content" style="display: none;">
									<div class="desc   font-normal">
										<?php echo !empty($item['noi_dung']) ? $item['noi_dung'] : ''; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-right relative" stick-to-edge="right" unstick-min="1024" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="600">
				<?php if (!empty($chat_luong_danh_sach)) : ?>
					<?php foreach ($chat_luong_danh_sach as $key => $item) : ?>
						<div class="img" data-id="<?php echo $key + 1; ?>"> <a class="img-ratio ratio:pt-[640_960] ">
								<img class="lozad undefined" data-src="<?php echo !empty($item['anh']['url']) ? $item['anh']['url'] : ''; ?>" alt="image" /></a></div>
					<?php endforeach; ?>
				<?php endif; ?>

			</div>
		</div>
	</div>
</section>
<section class="section-partner bg-Utility-gray-50 section-py" id="customer">
	<div class="container">
		<div class="heading text-center mb-base">
			<h2 class="heading-title mb-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><?php echo !empty($home_partners_title) ? $home_partners_title : ''; ?></h2>
			<div class="desc body-1 font-normal" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
				<?php echo !empty($home_partners_desc) ? $home_partners_desc : ''; ?>
			</div>
		</div>
		<div class="bottom grid xl:grid-cols-2 grid-cols-1 gap-base" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="600"> 
			<?php if (!empty($home_partners_groups)) : ?>
				<?php foreach ($home_partners_groups as $group) : ?>
					<div class="box bg-Utility-white rounded-5 xl:p-8 p-5 shadow-Shadow-1">
						<h3 class="title"><?php echo !empty($group['group_title']) ? $group['group_title'] : ''; ?></h3>
						<div class="partner-list relative">
							<div class="swiper">
								<div class="swiper-wrapper">
									<?php if (!empty($group['logos'])) : ?>
										<?php foreach ($group['logos'] as $partner) :
										?>
											<div class="swiper-slide">
												<div class="item">
													<div class="img img-ratio ratio:pt-[78_84] zoom-img">
														<img class="lozad undefined" data-src="<?php echo !empty($partner['logo']['url']) ? $partner['logo']['url'] : ''; ?>" alt="image" />
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="arrow-button flex items-center justify-center gap-base mt-base">
								<div class="btn btn-sw-1 btn-prev"></div>
								<div class="btn btn-sw-1 btn-next"></div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php
get_footer();
