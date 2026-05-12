  <?php
    global $post;
    $items = get_field('items', $post->ID);
    $map_iframe = get_field('map_iframe', $post->ID);
    ?>
  <div class="item" data-iframe="<?php echo !empty($map_iframe) ? esc_attr($map_iframe) : ''; ?>">
      <div class="title title-24 font-bold mb-2"><?php echo get_the_title($post->ID); ?></div>
      <div class="system-contact">
          <ul>
              <?php foreach ($items as $item) { ?>
                  <li>
                      <a <?php echo $item['link'] ? 'href="' . $item['link'] . '" target="_blank" rel="nofollow"' : ''; ?>>
                          <div class="icon">
                              <?php echo !empty($item['icon']) ? $item['icon'] : '<i class="fa-solid fa-location-dot"></i>'; ?>
                          </div>
                          <span><?php echo !empty($item['content']) ? $item['content'] : ''; ?></span>
                      </a>
                  </li>
              <?php } ?>

          </ul>
      </div>
  </div>