<?php
defined( 'ABSPATH' ) || exit;

$post_terms  = get_the_terms( get_the_ID(), 'category' );
$post_term_0 = ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) ? reset( $post_terms ) : null;
?>
<a class="content-left group" href="<?php the_permalink(); ?>">
    <div class="img img-ratio ratio:pt-[572_912] zoom-img">
        <img class="lozad" data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php the_title_attribute(); ?>" />
        <div class="main-content">
            <div class="warp">
                <div class="category-new">
                    <span><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
                    <?php if ( $post_term_0 ) : ?>
                    <span><?php echo esc_html( $post_term_0->name ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="title-new">
                    <h2 class="title-heading text-white group-hover:text-primary-2 all-linear-500"><?php the_title(); ?></h2>
                </div>
                <button class="btn btn-new btn-prmary btn-icon">
                    <span><?php esc_html_e( 'View Detail', 'canhcamtheme' ); ?></span>
                    <div class="icon"></div>
                </button>
            </div>
        </div>
    </div>
</a>