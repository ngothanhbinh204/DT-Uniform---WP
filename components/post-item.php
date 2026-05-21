<?php
defined( 'ABSPATH' ) || exit;

$post_terms  = get_the_terms( get_the_ID(), 'category' );
$post_term_0 = ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) ? reset( $post_terms ) : null;
?>
<a class="group card-newOther" href="<?php the_permalink(); ?>">
    <div class="card-img">
        <div class="img img-ratio ratio:pt-[114_183] zoom-img">
            <img class="lozad" data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php the_title_attribute(); ?>" />
        </div>
    </div>
    <div class="main-content">
        <div class="date"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></div>
        <div class="title-newOther heading-4"><?php the_title(); ?></div>
        <?php if ( $post_term_0 ) : ?>
        <div class="category-new"><?php echo esc_html( $post_term_0->name ); ?></div>
        <?php endif; ?>
    </div>
</a>