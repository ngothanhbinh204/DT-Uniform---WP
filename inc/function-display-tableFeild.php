<?php 
	add_filter('manage_edit-pa_color_columns', function ($columns) {
	$new_columns = [];
	foreach ($columns as $key => $label) {

		$new_columns[$key] = $label;

			if ($key === 'name') {
				$new_columns['color_preview'] = 'Color';
				}
				}

				return $new_columns;
				});


				add_filter('manage_pa_color_custom_column', function ($content, $column_name, $term_id) {

				if ($column_name !== 'color_preview') {
				return $content;
				}

				$color = get_field('color', 'pa_color_' . $term_id);

				if (!$color) {
				return '—';
				}

				return sprintf(
				'<span style="
            display:inline-block;
            width:24px;
            height:24px;
            border-radius:999px;
            background:%s;
            border:1px solid #ccc;
        "></span>
				<code style="margin-left:8px">%s</code>',
				esc_attr($color),
				esc_html($color)
				);

				}, 10, 3);
				
				

add_filter('manage_edit-product_columns', function ($columns) {

    $new_columns = [];

    foreach ($columns as $key => $label) {

        $new_columns[$key] = $label;

        if ($key === 'name') {
            $new_columns['product_color'] = __('Color', 'canhcamtheme');
        }
    }

    return $new_columns;
});


add_action('manage_product_posts_custom_column', function ($column, $post_id) {

    if ($column !== 'product_color') {
        return;
    }

    $terms = get_the_terms($post_id, 'pa_color');

    if (empty($terms) || is_wp_error($terms)) {
        echo '—';
        return;
    }

    $colors = wp_list_pluck($terms, 'name');

    echo esc_html(implode(', ', $colors));

}, 10, 2);



add_shortcode('wpml_lang_selector', 'canhcam_wpml_lang_selector');
function canhcam_wpml_lang_selector() {
    ob_start();
    
    // Nếu có WPML đang kích hoạt
    if (function_exists('icl_get_languages')) {
        $languages = icl_get_languages('skip_missing=0&orderby=code');
        if (!empty($languages)) {
            echo '<ul>';
            foreach ($languages as $l) {
                if ($l['active']) {
                    echo '<li class="wpml-ls-item"><a href="' . esc_url($l['url']) . '"><img src="' . esc_url($l['country_flag_url']) . '" alt="' . esc_attr($l['language_code']) . '" /><span class="wpml-ls-native">' . esc_html(strtoupper($l['language_code'])) . '</span></a>';
                    // Hiển thị các ngôn ngữ còn lại trong sub-menu
                    echo '<ul>';
                    foreach ($languages as $sub_l) {
                        if (!$sub_l['active']) {
                            echo '<li><a href="' . esc_url($sub_l['url']) . '"><img src="' . esc_url($sub_l['country_flag_url']) . '" alt="' . esc_attr($sub_l['language_code']) . '" /><span>' . esc_html(strtoupper($sub_l['language_code'])) . '</span></a></li>';
                        }
                    }
                    echo '</ul>';
                    echo '</li>';
                    break; 
                }
            }
            echo '</ul>';
        }
    } else {
        ?>
<ul>
	<li class="wpml-ls-item">
		<a href="#"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/VN.png" alt="VN" /><span
				class="wpml-ls-native">VN</span></a>
		<ul>
			<li><a href="#"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/EN.png"
						alt="EN" /><span>EN</span></a></li>
		</ul>
	</li>
</ul>
<?php
    }
    
    return ob_get_clean();
}