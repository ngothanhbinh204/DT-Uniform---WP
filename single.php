<?php
get_header();

$post_id      = get_the_ID();
$post_terms   = get_the_terms( $post_id, 'category' );
$first_term   = ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) ? reset( $post_terms ) : null;
$category_ids = ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) ? wp_list_pluck( $post_terms, 'term_id' ) : [];

$sidebar_related = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [ $post_id ],
    'category__in'   => ! empty( $category_ids ) ? $category_ids : [],
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

$related_posts = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 5,
    'post__not_in'   => [ $post_id ],
    'category__in'   => ! empty( $category_ids ) ? $category_ids : [],
    'orderby'        => 'date',
    'order'          => 'DESC',
] );
?>

<section class="section-newDetail">
    <div class="section-pt-80">

        <div class="main-iconSocial">
            <div class="social">
                <a class="icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a class="icon" href="https://www.linkedin.com/shareArticle?url=<?php echo esc_url( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <div class="container-fluid">
            <div class="block-content row">

                <div class="col-lg-9">
                    <div class="top-content">
                        <h1 class="title-heading text-black"><?php the_title(); ?></h1>
                        <div class="date-category">
                            <span><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
                            <?php if ( $first_term ) : ?>
                            <strong>
                                <a href="<?php echo esc_url( get_term_link( $first_term ) ); ?>"><?php echo esc_html( $first_term->name ); ?></a>
                            </strong>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="main-content">
                        <div class="prose">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <?php if ( $sidebar_related->have_posts() ) : ?>
                    <div class="sticky-layoyt">
                        <h2 class="heading-3 text-primary-1 uppercase mb-5"><?php esc_html_e( 'Related News', 'canhcamtheme' ); ?></h2>
                        <div class="other-new">
                            <?php while ( $sidebar_related->have_posts() ) : $sidebar_related->the_post(); ?>
                            <?php get_template_part( 'components/post-item' ); ?>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

    <?php if ( $related_posts->have_posts() ) : ?>
    <div class="wrap-backgroud">
        <div class="section-py">
            <div class="container-fluid">
                <h2 class="heading-2 text-primary-1 block text-center mb-base"><?php esc_html_e( 'Related Articles', 'canhcamtheme' ); ?></h2>
                <div class="swiper-column-auto auto-3-column" data-id-swiper="newDetail">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                            <div class="swiper-slide">
                                <a class="card-ProductHot group" href="<?php the_permalink(); ?>">
                                    <div class="image-card">
                                        <div class="img img-ratio ratio:pt-[527_453] zoom-img">
                                            <img class="lozad" data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php the_title_attribute(); ?>" />
                                        </div>
                                    </div>
                                    <div class="content-card">
                                        <div class="wrap-content">
                                            <div class="name-card"><?php the_title(); ?></div>
                                            <div class="desc-card"><?php echo esc_html( get_the_excerpt() ); ?></div>
                                        </div>
                                        <button class="btn btn-view"><?php esc_html_e( 'View More', 'canhcamtheme' ); ?></button>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <div class="button-swiper">
                        <div class="btn-swiper btn-prev btn-swiper-primary" data-id-swiper="newDetail">
                            <div class="icon"></div>
                        </div>
                        <div class="btn-swiper btn-next btn-swiper-primary" data-id-swiper="newDetail">
                            <div class="icon"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</section>

<?php get_footer();
