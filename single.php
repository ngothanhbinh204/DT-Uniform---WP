<?php
get_header();
$taxonomy = get_taxonomy(get_the_ID());
$category_id = !empty(get_the_category(get_the_ID(), $taxonomy)) ? get_the_category(get_the_ID(), $taxonomy) : array();
$args = array(
	'post_type' => 'post',
	'posts_per_page' => 4,
	'post__not_in' => array(get_the_ID()),
	'category__in' => !empty($category_id) ? $category_id[0]->term_id : array(),

);

$related_posts = new WP_Query($args);


?>
<section class="news-detail section-py">
	<div class="container">
		<div class="news-detail-main grid grid-cols-12 gap-base">
			<div class="col-left lg:col-span-8 col-span-full">
				<div class="position-relative relative">
					<h1 class="heading-4 text-Primary-5 font-bold"><?php the_title(); ?></h1>
					<div class="news-item-meta py-3 flex gap-2 border-b border-b-Neutral-200">
						<div class="news-item-date text-gray-300"><?php the_date(''); ?></div>
						<div class="news-item-category text-Primary-2 font-bold"><a href="<?php echo get_term_link($category_id[0]->term_id); ?>"><?php echo $category_id[0]->name; ?></a></div>
					</div>
					<div class="format-content">
						<?php the_content(); ?>
					</div>
					<div class="sticky-share-post absolute right-full top-0 mr-5 bottom-0">
						<div class="detail-share flex lg:flex-col gap-5 sticky top-[calc(var(--header-height)+1.5625rem)]">
							<ul>
								<li> <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"> <i class="fa-brands fa-facebook-f"></i></a></li>
								<!-- <li> <a href="https://www.twitter.com/share?url=<?php the_permalink(); ?>" target="_blank"> <i class="fa-brands fa-twitter"></i></a></li> -->
								<li> <a href="https://www.linkedin.com/shareArticle?url=<?php the_permalink(); ?>" target="_blank"> <i class="fa-brands fa-linkedin"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php if ($related_posts->have_posts()) : ?>
				<div class="col-right lg:col-span-4 col-span-full">
					<h2 class="title-24 font-bold text-Primary-5 mb-6"><?php _e('Bài viết liên quan', 'canhcamtheme'); ?></h2>
					<div class="news-detail-list flex flex-col gap-6">
						<?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
							<?php get_template_part('components/post-item'); ?>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
get_footer();
