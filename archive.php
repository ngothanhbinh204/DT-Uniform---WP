<?php
$term        = get_queried_object();
$term_id     = $term instanceof WP_Term ? $term->term_id : 0;
$taxonomy    = $term instanceof WP_Term ? $term->taxonomy : '';
$terms_child = [];

if ( $term instanceof WP_Term && $taxonomy ) {
    $all_terms = get_terms( [
        'taxonomy'        => $taxonomy,
        'hide_empty'      => false,
        'suppress_filter' => true,
    ] );

    $terms_child = array_filter( $all_terms, function ( $t ) use ( $term_id ) {
        return (int) $t->parent === (int) $term_id;
    } );

    if ( empty( $terms_child ) && $term->parent != 0 ) {
        $terms_child = array_filter( $all_terms, function ( $t ) use ( $term ) {
            return (int) $t->parent === (int) $term->parent;
        } );
    }
}

$page_title   = $term instanceof WP_Term ? $term->name : __( 'News', 'canhcamtheme' );
$banner_image = $term_id ? get_field( 'archive_banner_image', $taxonomy . '_' . $term_id ) : null;

get_header();
?>

<section class="section-NormalBanner">
	<div class="img img-parallax ratio:pt-[450_1920]" data-gsap-options='{"type":"img-parallax-percent","yPercent":15}'>
		<?php if ( ! empty( $banner_image['url'] ) ) : ?>
		<img src="<?php echo esc_url( $banner_image['url'] ); ?>" alt="<?php echo esc_attr( $page_title ); ?>" />
		<?php endif; ?>
		<div class="main-content">
			<div class="title-heading heading-1 text-white uppercase"><?php echo esc_html( $page_title ); ?></div>
			<div class="global-breadcrumb">
				<div class="section-px">
					<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) rank_math_the_breadcrumbs(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section-newList">
	<div class="container-fluid">

		<?php if ( ! empty( $terms_child ) ) : ?>
		<div class="wrap-padding">
			<div class="block-button">
				<div class="filter-dropdown">
					<div class="filter-toggle">
						<span class="selected-text"><?php echo esc_html( $page_title ); ?></span>
						<i class="fa-regular fa-chevron-down"></i>
					</div>
					<ul class="tabslet-tab filter-menu">
						<?php foreach ( $terms_child as $cat ) : ?>
						<li <?php echo ( (int) $cat->term_id === (int) $term_id ) ? 'class="active"' : ''; ?>>
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
								<span><?php echo esc_html( $cat->name ); ?></span>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="wrap-padding">
			<div class="block-grid row">
				<div class="col-xl-7">
					<?php $count = 0; while ( have_posts() ) : the_post(); ?>
					<?php if ( $count === 0 ) get_template_part( 'components/content-post' ); ?>
					<?php $count++; endwhile; wp_reset_postdata(); ?>
				</div>
				<div class="col-xl-5">
					<div class="other-new">
						<?php $count = 0; while ( have_posts() ) : the_post(); ?>
						<?php if ( $count >= 1 && $count <= 3 ) get_template_part( 'components/post-item' ); ?>
						<?php $count++; endwhile; wp_reset_postdata(); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="warp-listNew">
			<div class="block-grid">
				<?php $count = 0; while ( have_posts() ) : the_post(); ?>
				<?php if ( $count > 3 ) : ?>
				<a class="card-ProductHot group" href="<?php the_permalink(); ?>">
					<div class="image-card">
						<div class="img img-ratio ratio:pt-[527_453] zoom-img">
							<img class="lozad"
								data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>"
								alt="<?php the_title_attribute(); ?>" />
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
				<?php endif; ?>
				<?php $count++; endwhile; wp_reset_postdata(); ?>
			</div>
			<div class="block-pagination">
				<?php echo wp_bootstrap_pagination(); ?>
			</div>
		</div>

	</div>
</section>

<?php get_footer(); ?>