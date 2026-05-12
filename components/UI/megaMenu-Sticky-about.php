<?php
$menu_name = 'menu-about';
$locations = get_nav_menu_locations();

if (!isset($locations[$menu_name])) return;

$menu_items = wp_get_nav_menu_items($locations[$menu_name], [
    'update_post_term_cache' => false,
    'suppress_filters' => false
]);

if (empty($menu_items)) return;

// Build menu hierarchy and set active states

if (!function_exists('build_menu_with_active_states')) {
    function build_menu_with_active_states($menu_items)
    {
        $ACTIVE_CLASS = "current-custom-active current-post-ancestor";

        $current_url = trailingslashit(home_url(add_query_arg(array(), $_SERVER['REQUEST_URI'])));
        $menu_hierarchy = array();
        $items_by_id = array();
        $current_post_cats = array(); // Initialize as empty array

        if (is_single()) {
            $post_id = get_the_ID();
            $post_type = get_post_type($post_id);

            if ($post_type == 'post') {
                $categories = get_the_category($post_id);
                foreach ($categories as $cat) {
                    $current_post_cats[] = $cat->term_id;
                    // Also add all parent categories
                    $parent_id = $cat->parent;
                    while ($parent_id) {
                        $current_post_cats[] = $parent_id;
                        $parent = get_term($parent_id, 'category');
                        $parent_id = $parent->parent;
                    }
                }
            } else {
                // Handle custom post types and their taxonomies
                $taxonomies = get_object_taxonomies($post_type);
                foreach ($taxonomies as $tax) {
                    $terms = get_the_terms($post_id, $tax);
                    if ($terms && !is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $current_post_cats[] = $term->term_id;
                            // Also add all parent terms
                            $parent_id = $term->parent;
                            while ($parent_id) {
                                $current_post_cats[] = $parent_id;
                                $parent = get_term($parent_id, $tax);
                                $parent_id = $parent->parent;
                            }
                        }
                    }
                }
            }
        }
        foreach ($menu_items as $item) {
            $item->is_active = false;
            $item->classes = array_unique((array)$item->classes);

            // Broader active state check
            $item_url = trailingslashit($item->url);
            $is_home_url = trailingslashit(home_url()) === $item_url;
            if (
                $item_url === $current_url || // Exact URL match
                (!$is_home_url && strpos($current_url, $item_url) === 0) ||
                strpos($current_url, $item_url) === 0 || // Current URL starts with item URL (for parent pages)
                in_array($ACTIVE_CLASS, $item->classes) || // Already marked active
                (is_singular() && get_the_ID() == $item->object_id) || // Matches current post/page
                (is_single() && $item->type == 'taxonomy' && in_array($item->object_id, $current_post_cats))
            ) {
                $item->is_active = true;
            }
            // $item->title = $item->title . 'fix';
            $items_by_id[$item->ID] = $item;
            $item->children = array();
        }

        // Second pass: build hierarchy and propagate active states
        foreach ($menu_items as $item) {
            if ($item->menu_item_parent == 0) {
                $menu_hierarchy[] = $item;
            } elseif (isset($items_by_id[$item->menu_item_parent])) {
                $parent = $items_by_id[$item->menu_item_parent];
                $parent->children[] = $item;
                if ($item->is_active) {
                    // Only propagate active state if parent is not "Shop All"
                    $parent_url = trailingslashit($parent->url);
                    $shop_all_url = trailingslashit(home_url('/shop/')); // Adjust this to your actual "Shop All" URL
                    if ($parent_url !== $shop_all_url) {
                        $parent->is_active = true;
                        $current_parent = $parent;
                        while (
                            $current_parent->menu_item_parent != 0 &&
                            isset($items_by_id[$current_parent->menu_item_parent])
                        ) {
                            $current_parent = $items_by_id[$current_parent->menu_item_parent];
                            // Only propagate if not "Shop All"
                            $current_parent_url = trailingslashit($current_parent->url);
                            if ($current_parent_url === $shop_all_url) {
                                break;
                            }
                            $current_parent->is_active = true;
                        }
                    }
                }
            }

            // Ensure active class is added
            if ($item->is_active && !in_array($ACTIVE_CLASS, $item->classes)) {
                $item->classes[] = $ACTIVE_CLASS;
            }
        }


        return $menu_hierarchy;
    }
}


// Apply the new function
$menu_items = build_menu_with_active_states($menu_items);

if (!function_exists('get_item_class')) {
    function get_item_class($menu_item)
    {
        global $ACTIVE_CLASS;
        $classes = array();

        $is_mega_menu = get_field('mega_menu', $menu_item);

        if ($menu_item->children) $classes[] = 'has-children menu-item-has-children';
        if ($is_mega_menu) $classes[] = 'has-megamenu drop-mega mega-product';
        if ($menu_item->is_active) $classes[] = $ACTIVE_CLASS;
        $classes[] = 'menu-item';
        $classes = array_merge($classes, array_filter($menu_item->classes));
        return implode(' ', array_unique($classes));
    }
}
?>
<?php if ($menu_items) : ?>

    <ul class="nav" id="menu-site-menu">
        <?php foreach ($menu_items as $parent_key => $menu_level_1): ?>
            <?php
            $is_mega_menu = get_field('mega_menu', $menu_level_1);

            ?>

            <li class="<?= get_item_class($menu_level_1) ?>">
                <?php if ($menu_level_1->children) : ?>
                    <div class="title">
                        <a href="<?= $menu_level_1->url ?>" title="<?= $menu_level_1->title ?>" <?= $menu_level_1->target ? ' target="' . htmlspecialchars($menu_level_1->target, ENT_QUOTES) . '"' : '' ?>><?= $menu_level_1->title ?>
                    </div>


                    <?php if (!$is_mega_menu) : ?>
                        <ul class="sub-menu">
                            <?php foreach ($menu_level_1->children as $menu_level_2) : ?>
                                <li class="<?= get_item_class($menu_level_2) ?>">
                                    <a href="<?= $menu_level_2->url ?>" title="<?= $menu_level_2->title ?>" <?= $menu_level_2->target ? ' target="' . htmlspecialchars($menu_level_2->target, ENT_QUOTES) . '"' : '' ?>><?= $menu_level_2->title ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                <?php elseif (!$menu_level_1->children && !$is_mega_menu) : ?>
                    <div class="title">
                        <a href="<?= $menu_level_1->url ?>" title="<?= $menu_level_1->title ?>" <?= $menu_level_1->target ? ' target="' . htmlspecialchars($menu_level_1->target, ENT_QUOTES) . '"' : '' ?>><?= $menu_level_1->title ?></a>
                    </div>
                <?php endif; ?>
                <?php if ($is_mega_menu) : ?>
                    <?php
                    $image_first = '';
                    $title_first = '';
                    $url_first = '';
                    ?>
                    <div class="mega-menu">
                        <div class="container-fluid relative">
                            <div class="row no-gutters">
                                <div class="col w-full lg:w-8/12 relative">
                                    <ul class="nav-sub menu-lv-1">
                                        <?php foreach ($menu_level_1->children as $menu_level_2) : ?>
                                            <?php
                                            ?>
                                            <li class="nav-link">
                                                <div class="head"> <a href="<?= $menu_level_2->url ?>" title="<?= $menu_level_2->title ?>" <?= $menu_level_2->target ? ' target="' . htmlspecialchars($menu_level_2->target, ENT_QUOTES) . '"' : '' ?>><?= $menu_level_2->title ?></a><em class="fa-light fa-chevron-right"></em></div>
                                                <ul class="nav-sub menu-lv-2">
                                                    <?php foreach ($menu_level_2->children as $menu_level_3) : ?>
                                                        <li class="nav-link is-active">
                                                            <div class="head"> <a href="<?= $menu_level_3->url ?>" title="<?= $menu_level_3->title ?>" <?= $menu_level_3->target ? ' target="' . htmlspecialchars($menu_level_3->target, ENT_QUOTES) . '"' : '' ?>><?= $menu_level_3->title ?></a><em class="fa-solid fa-chevron-down"></em></div>
                                                            <ul class="nav-sub menu-lv-3">
                                                                <?php foreach ($menu_level_3->children as $key => $menu_level_4) :
                                                                    $image = get_field('image', $menu_level_4->ID);
                                                                ?>
                                                                    <?php if ($key === 0) : ?>
                                                                        <?php $image_first = $image; ?>
                                                                        <?php $title_first = $menu_level_4->title; ?>
                                                                        <?php $url_first = $menu_level_4->url; ?>
                                                                    <?php endif; ?>
                                                                    <li class="nav-link" data-link="<?= $menu_level_4->url ?>" data-title="<?= $menu_level_4->title ?>" data-img="<?= !empty($image['url']) ? $image['url'] : '' ?>"><a><?= $menu_level_4->title ?></a></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col w-full lg:w-4/12">
                                    <div class="temp-product">
                                        <div class="img zoom-in overflow-hidden"><a href="<?= $url_first ?>"><img src="<?= $image_first['url'] ?>" alt="<?= $image_first['alt'] ?>"></a></div>
                                        <div class="txt-wrap h-fit">
                                            <h3 class="sub-title"><a class="underline center-item text-center temp-title font-bold text-primary-1 text-24" href="<?= $url_first ?>"><?= $title_first ?></a></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>