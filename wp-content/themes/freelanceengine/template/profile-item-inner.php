<?php
	/**
	 * The template for displaying inner profile in a loop
	 *
	 * @since    1.0
	 * @package  FreelanceEngine
	 * @category Template
	 */

	global $inner_query_profiles, $inner_query_companies, $post, $ae_post_factory;

	if ( ! isset( $post ) ) {
		return;
	}

	$user_type = isset( $inner_query_profiles ) ? PROFILE : COMPANY;

	$post_object = $ae_post_factory->get( $user_type );
	$current     = $post_object->convert( $post );

	$profile_category       = $current->tax_input[ 'project_category' ];
	$profile_experience     = $current->experience;
	$profile_project_worked = $current->project_worked;
	$profile_desc           = $current->excerpt;

	$hou_rate                   = (int) $current->hour_rate;
	$current_profile_categories = $profile_category;
	$user_status                = get_user_pro_status( $current->post_author );
	$visualFlag                 = getValueByProperty( $user_status, 'visual_flag' );
	if ( $visualFlag ) {
		$visualFlagNumber = get_user_meta( $current->post_author, 'visual_flag', true );
	}

	#echo '<pre>'; var_export($current); echo '</pre>';

	if ( is_tax() ) { ?>
        <div <?php post_class( 'col-sm-12 col-xs-12 profile-item fre-profiles-list-item cl-' . $current->ID ); ?> >
        <div class="profile-content fre-freelancer-wrap">
        <div class="row">
        <div class="col-sm-8 col-xs-12">
        <div class="fre-info">
	<?php } else { ?>
        <div <?php post_class( 'col-sm-6 col-xs-12 profile-item fre-profiles-list-item cl-' . $current->ID ); ?> >
        <div class="profile-content fre-freelancer-wrap">
	<?php } ?>
    <a class="free-avatar" href="<?php echo get_author_posts_url( $current->post_author ); ?>">
		<?php echo get_avatar( $current->post_author ); ?>
    </a>
    <div class="row">
        <div class="col-sm-8 col-xs-8">
            <div class="clearfix">
                <a class="free-name" href="<?php the_permalink(); ?>">
					<?php
						#the_author_meta('display_name', $current->post_author);
						echo $current->post_title;
					?>
                </a>
                <span class="status">
                                <?php if ( ! empty( $current->str_pro_account ) ) {
	                                _e( 'PRO', ET_DOMAIN );
                                } ?>
                            </span>
				<?php if ( $visualFlag ) {
					switch ( $visualFlagNumber ) {
						case 1:
							echo '<span class="status">' . translate( 'Master', ET_DOMAIN ) . '</span>';
							break;
						case 2:
							echo '<span class="status">' . translate( 'Creator', ET_DOMAIN ) . '</span>';
							break;
						case 3:
							echo '<span class="status">' . translate( 'Expert', ET_DOMAIN ) . '</span>';
							break;
					}
				} ?>
            </div>
			<?php if ( $hou_rate > 0 ) { ?>
                <div class="free-hourly-rate">
					<?php printf( __( '%s/hr', ET_DOMAIN ), "<span>" . fre_price_format( $current->hour_rate ) . "</span>" ); ?>
                </div>
			<?php } ?>
        </div>
        <div class="col-sm-4 col-xs-4">
            <div class="free-rating-new">
                +<?= getActivityRatingUser( $current->post_author ); ?></div>
            <div class="free-rating"><? HTML_review_rating_user( $current->post_author ) ?></div>
        </div>
    </div>

    <div class="free-category">
		<?php if ( $current_profile_categories ) {
			echo baskserg_profile_categories( $current_profile_categories );
		} ?>
    </div>

    <div class="free-experience">
        <span><?php echo $profile_experience ?></span>
        <span><?php echo $profile_project_worked ?></span>
    </div>
<?php if ( is_tax() ) { ?>
    <div class="fre-location"><?php echo $current->str_location; ?></div>
    </div><!--fre-info-->
	<?php $leng_desc = strlen( $profile_desc );
	if ( $leng_desc > 0 ) { ?>
        <div class="profile-list-desc">
			<?php if ( $leng_desc > 270 ) { ?>
                <div class="excp-txt"><?php echo mb_substr( $profile_desc, 0, 270 ) . '... <a class="more-info">' . __( 'More info', ET_DOMAIN ) . '</a>'; ?></div>
                <div class="scroll-pane"><?php echo $profile_desc; ?></div>
			<?php } else {
				echo $profile_desc;
			} ?>
        </div>
	<?php } ?>
    </div><!--col-sm-8-->
    <div class="col-sm-4 col-xs-12">
		<?php
			$author_id = $post->post_author;
			$args      = [
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'post_type'      => PORTFOLIO,
				'author'         => $author_id
			];
			$query     = new WP_Query( $args );
			while ( $query->have_posts() ) {
				$query->the_post(); ?>
                <div class="freelance-portfolio_img_wp">
                    <div class="freelance-portfolio_img"
                         style="background:url(<?php the_post_thumbnail_url(); ?>) center no-repeat;"></div>
                </div>
			<?php }
			#wp_reset_query();
			$wp_query->reset_postdata();
		?>
        <a class="fre-submit-btn fre-view-profile"
           href="<?php echo get_author_posts_url( $current->post_author ); ?>"><?php echo _e( 'View Profile', ET_DOMAIN ); ?></a>
    </div>
    </div><!--row-->

    </div><!--profile-content-->
    </div>
<?php } else { ?>
    <div class="profile-list-desc">
		<?php $leng_desc = strlen( $profile_desc );
			if ( $leng_desc > 130 ) {
				echo mb_substr( strip_tags( $profile_desc ), 0, 130 ) . '...';
			} else {
				echo mb_substr( strip_tags( $profile_desc ), 0, 130 );
			} ?>
    </div>
    </div>
    </div>
<?php }