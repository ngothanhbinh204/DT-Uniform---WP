 <?php
    $type =  isset($args['type']) ? $args['type'] : '';
    $video_url = get_field('video_link', get_the_ID());
    $album = get_field('album', $post->ID);

    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');


    $link = $type == 'video' ? $video_url : $image_url;
    ?>
 <?php if ($type == 'video') { ?>
     <div class="video-item group">
         <div class="img">
             <a class="img-ratio ratio:pt-[459_681] zoom-img" data-src="<?php echo $link; ?>" data-fancybox> <img class="lozad undefined" data-src="<?php echo $image_url; ?>" alt="" /></a>
             <a class="icon-play" data-src="<?php echo $link; ?>" data-fancybox>
                 <div class="icon">
                     <i class="fa-solid fa-play"></i>
                 </div>
             </a>
         </div>
         <div class="content mt-5">
             <div class="title title-20 font-bold group-hover:text-Primary-1">
                 <a data-src="<?php echo $link; ?>" data-fancybox>
                     <?php echo get_the_title(); ?>
                 </a>
             </div>
         </div>
     </div>
 <?php } else { ?>
     <a class="video-item" data-src="<?php echo $image_url; ?>" data-fancybox="gallery<?php echo $type == 'video' ? 'video' : '-album-' . get_the_ID(); ?>">
         <div class="img">
             <div class="img-ratio ratio:pt-[459_681]">
                 <img class="lozad undefined" data-src="<?php echo $image_url; ?>" alt="" />
             </div>
         </div>
         <div class="content mt-5">
             <div class="title title-20 font-bold">
                 <div>
                     <?php echo get_the_title(); ?>
                 </div>
             </div>
         </div>
     </a>
     <div class="album-images" style="display: none;">
         <?php foreach ($album as $image) { ?>
             <a data-src="<?php echo $image['url']; ?>" data-fancybox="gallery<?php echo $type == 'video' ? 'video' : '-album-' . get_the_ID(); ?>">
                 <img class="lozad undefined" data-src="<?php echo $image['url']; ?>" alt="" />
             </a>
         <?php } ?>
     </div>
 <?php } ?>