 <?php  $user_role = ae_user_role($user_ID);
        $postid = $post->post_author;
        if ( $user_role == FREELANCER ) { 
                     $args2 = array(
                         'post_type' => 'project',
                         'orderby' => 'date', 
                         'order' => 'desc',
                         'author' => $postid,
                         'meta_key' => 'professional_id',
                         'meta_value' => $user_ID
                     ); 
                 } else {
                    $args2 = array(
                         'post_type' => 'project',
                         'orderby' => 'date', 
                         'order' => 'desc',
                         'author' => $user_ID,
                         'meta_key' => 'professional_id',
                         'meta_value' => $postid
                     ); 
                 }
             $query2 = new WP_Query($args2);
    $count = $query2->found_posts;
?>

 <div class="page-referrals_item">
     <div class="row">
         <div class="col-sm-6 col-xs-5">
             <a class="hidden-xs" href="<?php the_permalink();?>"><?php echo get_avatar($postid, 70); ?></a>
             <a class="name" href="<?php the_permalink();?>"><?php the_title();?></a>
             <?php if (userHaveProStatus($postid)) {
                                                echo '<span class="status">'.translate('PRO', ET_DOMAIN).'</span>';
                                            } ?>
             <span class="rating-new">+<?= getActivityRatingUser($postid) ?></span>
         </div>
         <div class="col-sm-3 col-xs-2 safe-deals">
             <?php echo $count;?>
         </div>
         <div class="col-sm-3 col-xs-5 text-center endors <?=checkEndorseSkills($user_ID,$postid);?>">
             <?=checkEndorseSkills($user_ID,$postid);?>
         </div>
         <div class="col-sm-12 col-xs-12 fre-project_lnk">
            <?php while($query2->have_posts()) { $query2->the_post();?>
                     <div>
                        <span><?php _e('Project:',ET_DOMAIN);?></span>
                        <a href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
                     </div>
            <?php } ?>         
         </div>
     </div>
 </div>
