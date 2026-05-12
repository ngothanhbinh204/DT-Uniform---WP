<?php
add_filter('facetwp_i18n', function ($string) {
    if (isset(FWP()->facet->http_params['lang'])) {
        $lang = FWP()->facet->http_params['lang'];
        $translations = [];
        $translations['vi']['Show more'] = 'Xem thêm';
        $translations['vi']['Loading...'] = 'Đang tải...';
        $translations['vi']['Select Industry'] = 'Chọn ngành';


        if (isset($translations[$lang][$string])) {
            return $translations[$lang][$string];
        }
    }

    return $string;
});
add_filter('facetwp_facet_html', function ($output, $params) {
    if ($params['facet']['type'] === 'pager' && $params['facet']['pager_type'] === 'load_more') {
        $output = str_replace('facetwp-load-more', 'facetwp-load-more btn mx-auto rem:mt-10 btn-primary', $output);
    }
    return $output;
}, 10, 2);


add_action('facetwp_scripts', function () { ?>
    <!-- <script>
        (function($) {
            // On start of the facet refresh, but not on first page load
            $(document).on('facetwp-refresh', function() {
                if (FWP.loaded) {
                    $('.facetwp-template').prepend('<div class="loading-overlay absolute inset-0 z-3"><div class="loading"></div></div>');
                }
            });

            // On finishing the facet refresh
            $(document).on('facetwp-loaded', function() {
                //     var is_visible = (FWP.settings.pager.page < FWP.settings.pager.total_pages);
                $('.facetwp-template .loading-overlay').remove();
                useVariables.lozadInit()
            });
        })(fUtil);
    </script> -->
<?php }, 100);


// add_filter('facetwp_assets', function ($assets) {
//     unset($assets['front.css']);
//     return $assets;
// });



$GLOBALS['CANHCAM']['POSTS_PER_PAGE'] = [
    'post' => 14,
    'solution' => 8,
    'catalogue' => 8,
    'support-department' => -1,
    'faqs' => 10,
    // 'otherPostType' => 6,
];
add_filter('facetwp_query_args', function ($query_args, $class) {

    //*** Fix bug: pagination/load_more multiple total_page ***
    if (is_category()) {
        $query_args['post_type'] = 'post';
        $query_args['posts_per_page'] = get_posts_per_page('post');
    }

    if (is_tax('solution_tax')) {
        $query_args['post_type'] = 'solution';
        $query_args['posts_per_page'] = get_posts_per_page('solution');
    }

    if (is_tax('catalogue-tax')) {
        $query_args['post_type'] = 'catalogue';
        $query_args['posts_per_page'] = get_posts_per_page('catalogue');
    }
    if (is_page_template('templates/SupportDepartment.php')) {
        $query_args['post_type'] = 'support-department';
        $query_args['posts_per_page'] = get_posts_per_page('support-department');
    }
    return $query_args;
}, 10, 2);

function get_posts_per_page($post_type)
{
    $p_setting = $GLOBALS['CANHCAM']['POSTS_PER_PAGE'][$post_type];
    return isset($p_setting) ? $p_setting : 1;
}

function get_FWP()
{
    return [
        'total_page' => FWP()->facet->pager_args['total_pages'],
        'current_page' => FWP()->facet->pager_args['page'],
    ];
}


add_filter('facetwp_is_main_query', function ($is_main_query, $query) {
    if ($query->is_archive() && $query->is_main_query()) {
        // Exclude WooCommerce queries
        if (is_woocommerce() || is_shop() || is_product_category() || is_product_tag()) {
            $is_main_query = true;
        } else {
            $is_main_query = false;
        }
    }

    return $is_main_query;
}, 10, 2);

// add_filter('facetwp_load_css', '__return_false');
// add_filter('facetwp_facet_dropdown_show_counts', '__return_false');
