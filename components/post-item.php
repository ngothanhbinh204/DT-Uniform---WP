<div class="news-item rounded-2 overflow-hidden grid grid-cols-[40.68%_1fr] gap-6 items-center">
    <div class="img"> <a class="img-ratio ratio:pt-[101_179] zoom-img" href="<?php the_permalink(); ?>"> <img class="lozad undefined" data-src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php the_title(); ?>" /></a></div>
    <div class="content">
        <div class="top">
            <div class="date"><?php echo get_the_date(); ?></div>
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