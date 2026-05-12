<div class="sidebar-wrapper">
    <div class="sidebar-toggle">
        <button class="btn btn-sidebar-toggle"><i class="fa-light fa-chevron-left"></i>
            <div class="tooltip" data-tooltip-close="Đóng" data-tooltip-open="Mở"><span><?php _e('Đóng', 'canhcamtheme'); ?></span></div>
        </button>
    </div>
    <div class="sidebar-menu-list">
        <div class="menu-top menu-list">
            <ul>
                <li class="<?= is_home() || is_front_page() ? 'current' : '' ?>"><a href="<?= home_url() ?>"> <img class="img-svg" src="<?php echo get_template_directory_uri(); ?>/img/house-window.svg" alt=""><span><?= _e('Trang chủ', 'canhcamtheme') ?></span></a></li>
                <li class="<?= is_login_trend() ? 'current' : '' ?>"><a href="<?php echo get_login_trend(); ?>"> <img class="img-svg" src="/wp-content/uploads/2025/07/circle-arrow-up-right.svg" alt=""><span><?= _e('Xu hướng', 'canhcamtheme') ?></span></a></li>
            </ul>
        </div>
        <div class="menu-list-wrapper">
            <div class="menu-list-title"><?php _e('Chủ đề chính', 'canhcamtheme'); ?></div>
            <div class="menu-list">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'menu-main',
                    'container' => 'ul',
                    'menu_class' => 'menu-list',
                ));
                ?>
            </div>
        </div>
        <div class="menu-list-wrapper">
            <div class="menu-list-title"><?php _e('SPECIAL TOPIC', 'canhcamtheme'); ?></div>
            <div class="menu-list">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'menu-special',
                    'container' => 'ul',
                    'menu_class' => 'menu-list',
                ));
                ?>
            </div>
        </div>
    </div>
</div>