<?php
/**
 * Breadcrumb dạng section độc lập — dùng trong trang single-product
 * HTML: <section class="global-breadcrumb">
 */
?>
<section class="global-breadcrumb">
	<div class="container-fluid">
		<?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
	</div>
</section>
