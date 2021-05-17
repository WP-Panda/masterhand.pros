<div class="get-started-content">
	<?php if(!is_user_logged_in()){ ?>
		<div class="title_start"><?php echo get_theme_mod("title_start") ? get_theme_mod("title_start") : __('Need work done? Join Masterhand Pro community!', ET_DOMAIN);?></div>
        <?php if(fre_check_register()){ ?>
            <a class="fre-btn fre-submit-btn" href="<?php echo bloginfo('url');?>/register/?role=client"><?php _e('Get Started', ET_DOMAIN)?></a>
        <?php } ?>
	<?php }else{ ?>
			<div class="title_start"><?php echo get_theme_mod("title_start_employer") ? get_theme_mod("title_start_employer") : __('The best way to find <br/> perfect professionals!', ET_DOMAIN);?></div>
		    <a class="fre-btn fre-submit-btn" href="<?php echo et_get_page_link('submit-project'); ?>"><?php _e('Post a Project', ET_DOMAIN)?></a>
	<?php } ?>
</div>