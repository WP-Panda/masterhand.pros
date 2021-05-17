<div class="main_bl" style="background: url(<?php if (get_theme_mod("background_banner") ) { echo get_theme_mod("background_banner"); } else { echo get_template_directory_uri(). '/img/main_bg.jpg';}?>) center no-repeat;">
    <div class="hidden-sm main_bl_bg_mob" style="background: url(<?php if (get_theme_mod("background_banner_mob") ) { echo get_theme_mod("background_banner_mob"); } else { echo get_template_directory_uri() . '/img/main_bg_mob.jpg';}?>) center no-repeat;" ></div>
   <div class="container">
       <div class="main_bl-wp">
        <?php if (get_theme_mod("title_banner") ) {?>
            <div class="main_bl-t"><?php echo get_theme_mod("title_banner");?><br/>
            <?php echo get_theme_mod("title_banner2");?></div>
        <?php } ?>
           <?php if ( ! is_user_logged_in() ) { ?>
               <a class="main_bl-btn" href="<?php echo bloginfo('url');?>/register/?role=client"><?php _e('Hire a Pro', ET_DOMAIN);?></a>
               <a class="main_bl-btn btn2" href="<?php echo bloginfo('url');?>/register/?role=professional"><?php _e('Apply as a Pro', ET_DOMAIN);?></a>
               <p><?php _e('Need a great job? ', ET_DOMAIN);?>
               <a href="<?php echo et_get_page_link('register');?>"><?php _e('Sign up', ET_DOMAIN)?></a></p>
           <?php } else {
                if ( ae_user_role( $user_ID ) == FREELANCER ) { ?>
                <a class="main_bl-btn" href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e('Apply as a Pro', ET_DOMAIN);?></a>                   
                <a class="main_bl-btn btn2" href="<?php echo et_get_page_link( "profile" ) ?>"><?php _e('My Profile', ET_DOMAIN);?></a>
           <?php } else {?>
                <a class="main_bl-btn" href="<?php echo et_get_page_link( 'submit-project' ); ?>"><?php _e('Hire a Pro', ET_DOMAIN);?></a>    
                <a class="main_bl-btn btn2" href="<?php echo et_get_page_link( "profile" ) ?>"><?php _e('My Profile', ET_DOMAIN);?></a>
            <?php }
                }?>
       </div>
    </div>
</div>