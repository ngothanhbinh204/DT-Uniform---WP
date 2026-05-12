<?php
function custom_mega_menu()
{
    // Start output buffering
    ob_start();

    // Save the global query state to restore later
    global $wp_query, $post;
    $original_query = $wp_query;
    $original_post = $post;

    // Include the template part
    get_template_part("/components/UI/megaMenu");

    // Capture the output
    $mega_menu = ob_get_clean();

    // Reset global post and query states to prevent leakage
    wp_reset_postdata();
    wp_reset_query();

    // Restore original global states
    $wp_query = $original_query;
    $post = $original_post;

    // Unset the custom global variable if it exists
    global $ACTIVE_CLASS;
    unset($GLOBALS['ACTIVE_CLASS']);

    return $mega_menu;
}



function custom_mega_menu_2()
{
    // Start output buffering
    ob_start();

    // Save the global query state to restore later
    global $wp_query, $post;
    $original_query = $wp_query;
    $original_post = $post;

    // Include the template part
    get_template_part("/components/UI/megaMenu2");

    // Capture the output
    $mega_menu = ob_get_clean();

    // Reset global post and query states to prevent leakage
    wp_reset_postdata();
    wp_reset_query();

    // Restore original global states
    $wp_query = $original_query;
    $post = $original_post;

    // Unset the custom global variable if it exists
    global $ACTIVE_CLASS;
    unset($GLOBALS['ACTIVE_CLASS']);

    return $mega_menu;
}


function get_post_primary_category($post_id, $term = 'category', $return_all_categories = false)
{
    $return = array();

    if (class_exists('WPSEO_Primary_Term')) {
        // Show Primary category by Yoast if it is enabled & set
        $wpseo_primary_term = new WPSEO_Primary_Term($term, $post_id);
        $primary_term = get_term($wpseo_primary_term->get_primary_term());

        if (!is_wp_error($primary_term)) {
            $return['primary_category'] = $primary_term;
        }
    }

    if (empty($return['primary_category']) || $return_all_categories) {
        $categories_list = get_the_terms($post_id, $term);

        if (empty($return['primary_category']) && !empty($categories_list)) {
            $return['primary_category'] = $categories_list[0]; //get the first category
        }
        if ($return_all_categories) {
            $return['all_categories'] = array();

            if (!empty($categories_list)) {
                foreach ($categories_list as &$category) {
                    $return['all_categories'][] = $category->term_id;
                }
            }
        }
    }

    return $return;
};
