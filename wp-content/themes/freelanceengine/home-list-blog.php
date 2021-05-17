<?php 
	$query = new WP_Query(array(
        'post_type' => 'post',
        'showposts' => 12,
        'post_per_page' => 12,
        'orderby'   => 'date',
        'order'     => 'DESC',
        /*'meta_query' => array(
            array(
                'key' => 'showonhome',
                'value' => 1
            )
        )*/
    ));
?>

<div class="fre-blog-list owl-carousel">
    <?php global $post;
        if($query->have_posts()){
            while($query->have_posts()){ $query->the_post();?>
	        <div class="fre-blog-list-item">
                        <div class="fre-blog-item">
                            <div class="fre-blog-item_img" style="background:url(<?php if( has_post_thumbnail() ) {the_post_thumbnail_url();} else { echo get_template_directory_uri().'/img/noimg.png';}?>) center no-repeat;"><a href="<?php the_permalink();?>"></a></div>
                            <div class="fre-blog-item_t"><a href="<?php the_permalink();?>"><?php echo the_title();?></a></div>
                            <p class="fre-blog-item_date"><?php echo _e('Updated',  ET_DOMAIN ) . ' ' . get_the_date();?></p>
                        </div>    
            </div>
				<? }
                }
      wp_reset_query();?>	
</div>