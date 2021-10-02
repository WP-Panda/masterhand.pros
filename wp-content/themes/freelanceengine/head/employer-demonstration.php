<div class="color-left">
    <div class="content-sub">
        <h1><?php fre_project_demonstration( true ); ?></h1>
        <p><a href="<?php echo et_get_page_link( [
				'page_type'  => 'submit-project',
				'post_title' => __( "Post a Project", ET_DOMAIN )
			] ); ?>"
              class="btn-sumary btn-sub-post box-shadow-button-dark-blue"><?php _e( "Post a Project", ET_DOMAIN ); ?></a>
        </p>
    </div>
</div>
<div class="color-right">
    <div class="content-sub">
        <h1><?php fre_profile_demonstration( false ); ?></h1>
        <p>
            <a href="<?php echo get_post_type_archive_link( PROFILE ) ?>"
               class="btn-sumary btn-sub-create box-shadow-button-orange">
				<?php _e( "Find a Freelancer", ET_DOMAIN ); ?>
            </a>
        </p>
    </div>
</div>