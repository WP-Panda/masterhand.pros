<div class="get-started-content">
	<?php if ( ! is_user_logged_in() ) { ?>
        <div class="title_start"><?php echo __( 'Get a Great Job.<br/> Join us now!!', ET_DOMAIN ); ?></div>
        <a class="fre-btn fre-submit-btn"
           href="<?php echo bloginfo( 'url' ); ?>/register/?role=professional"><?php _e( 'Sign up as a Pro', ET_DOMAIN ) ?></a>
	<?php } else { ?>
        <div class="title_start"><?php echo get_theme_mod( "title_start_freelancer" ) ? get_theme_mod( "title_start_freelancer" ) : __( "It's time to start finding jobs online!", ET_DOMAIN ); ?></div>
        <a class="fre-btn fre-submit-btn"
           href="<?php echo get_post_type_archive_link( PROJECT ); ?>"><?php _e( 'Find Projects', ET_DOMAIN ) ?></a>
	<?php } ?>
</div>