<?php
$term = get_queried_object();

$term_id = $term->term_id;
$taxonomy = $term->taxonomy;

// Lấy tất cả chuyên mục
$all_terms = get_terms([
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    'suppress_filter' => true, // thêm nếu muốn an toàn hơn
]);

// Lọc chuyên mục con bằng PHP
$terms_child = array_filter($all_terms, function ($t) use ($term_id) {
    return (int) $t->parent === (int) $term_id;
});

// Nếu không có con và đây là term con → lấy anh em
if (empty($terms_child) && $term->parent != 0) {
    $terms_child = array_filter($all_terms, function ($t) use ($term) {
        return (int) $t->parent === (int) $term->parent;
    });
}




$post_related = get_field('post_related', $term);
$banner_advertising = get_field('banner_advertising', $term);
get_header();
?>
<?php get_template_part('modules/common/banner'); ?>
<section class="news section-py">
    <div class="container">
        <div class="news-link">
            <ul>
                <?php foreach ($categories as  $category) : ?>
                    <li <?php echo $category->term_id == $term->term_id ? 'class="active"' : ''; ?>><a href="<?php echo get_term_link($category->term_id); ?>"><?php echo $category->name; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="news-main grid lg:grid-cols-2 rem:gap-[29px]">
            <div class="col-left">
                <?php
                $count = 0;
                while (have_posts()) : the_post(); ?>
                    <?php if ($count == 0) : ?>
                        <?php get_template_part('components/content-post'); ?>
                    <?php endif; ?>
                    <?php $count++; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
            <div class="col-right">
                <?php
                $count = 0;
                while (have_posts()) : the_post(); ?>
                    <?php if ($count > 0 && $count <= 3) : ?>
                        <div class="news-item rounded-3 overflow-hidden grid md:grid-cols-2 gap-6">
                            <div class="img"> <a class="img-ratio ratio:pt-[168_328]" href="<?php the_permalink(); ?>"> <img class="lozad undefined" data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title(); ?>" /></a></div>
                            <div class="content">
                                <div class="top">
                                    <div class="date"><?php the_date(''); ?></div>
                                    <div class="category"><a href="<?php echo get_term_link(get_the_terms(get_the_ID(), 'category')[0]->term_id); ?>"><?php echo get_the_terms(get_the_ID(), 'category')[0]->name; ?></a></div>
                                </div>
                                <div class="bottom">
                                    <div class="title title-20 font-bold text-Primary-2 mb-2 line-clamp-2"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                                    <div class="desc line-clamp-3 text-Utility-gray-800">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php $count++; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
        <div class="news-list grid md:grid-cols-3 gap-base mt-base">
            <?php
            $count = 0;
            while (have_posts()) : the_post(); ?>
                <?php if ($count > 3) : ?>
                    <?php get_template_part('components/post-item'); ?>
                <?php endif; ?>
                <?php $count++; ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        <?php echo wp_bootstrap_pagination(); ?>
    </div>
</section>
<?php
get_footer();
?>