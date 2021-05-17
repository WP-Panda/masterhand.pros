<?php
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get( PROJECT );
$convert     = $project = $post_object->current_post;
$project     = $post_object->convert( $post );
$author_id   = $project->post_author;
//$rating      = Fre_Review::employer_rating_score( $author_id );

$user_data = get_userdata( $author_id );

$profile_id = get_user_meta( $author_id, 'user_profile_id', true );
$profile    = array();
if ( $profile_id ) {
	$profile_post = get_post( $profile_id );
	if ( $profile_post && ! is_wp_error( $profile_post ) ) {
		$profile = $post_object->convert( $profile_post );
	}
}

include $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
$location = getLocation($author_id );

$display_name = $user_data->display_name;

$hire_freelancer = fre_count_hire_freelancer( $author_id );

$user_status = get_user_pro_status($author_id);

$profile_id = get_user_meta($author_id, 'user_profile_id', true);

$profile = array();
if ($profile_id) {
    $profile_post = get_post($profile_id);
    if ($profile_post && !is_wp_error($profile_post)) {
        $profile = $post_object->convert($profile_post);
    }
}

$attachment = get_children( array(
	'numberposts' => - 1,
	'order'       => 'ASC',
	'post_parent' => $post->ID,
	'post_type'   => 'attachment'
), OBJECT );
?>

    <div class="project-detail-box no-padding">
        <div class="project-detail-desc">
            <div class="project-detail-title">
                <?php _e( 'Project Description', ET_DOMAIN ); ?>
            </div>
            <div>
                <?php the_content(); ?>
            </div>
            <?php
		if ( ! empty( $attachment ) ) {
			echo '<ul class="project-detail-attach">';
			foreach ( $attachment as $key => $att ) {
				$file_type = wp_check_filetype( $att->post_title, array(
						'jpg'  => 'image/jpeg',
						'jpeg' => 'image/jpeg',
						'gif'  => 'image/gif',
						'png'  => 'image/png',
						'bmp'  => 'image/bmp'
					)
				);
				echo '<li><a href="' . $att->guid . '"><i class="fa fa-paperclip" aria-hidden="true"></i>' . $att->post_title . '</a></li>';
			}
			echo '</ul>';
		}
		?>
        </div>
        <div class="project-detail-extend">
            <!--<div class="project-detail-skill">
                <?php //list_tax_of_project( get_the_ID(), __( 'Skills Required', ET_DOMAIN ), 'skill' ); ?>
            </div>-->
            <div class="project-detail-category">
                <?php list_tax_of_project( get_the_ID(), __( 'Category', ET_DOMAIN ) ); ?>
            </div>
            <?php

		//milestone
		$args = array(
			'post_type'      => 'ae_milestone',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'post_parent'    => $project->ID,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_key'       => 'position_order'
		);

		$query = new WP_Query( $args );

		if ( function_exists( 'ae_query_milestone' ) && $query->have_posts() ) { ?>

                <div class="project-detail-milestone">
                    <div class="project-detail-title">
                        <?php echo __( "Milestones", ET_DOMAIN ); ?>
                    </div>
                    <?php do_action( 'after_sidebar_single_project', $project ); ?>
                </div>

                <?php } ?>

                <?php
		//Customfields
		if ( function_exists( 'et_render_custom_field' ) ) {
			et_render_custom_field( $project );
		}
		?>
        <?php if ($author_id != $user_ID) { ?>           
                <div class="project-detail-about">
                        <div class="project-detail-title"><?php _e( 'Client Information', ET_DOMAIN ); ?></div>
                        <div class="project-employer-rating fre-profile-box row">
                            <div class="col-sm-2 col-xs-4 text-center avatar_wp">
                                <?php $uslug = get_author_posts_url($author_id);?>
                                <a href="<?php echo $uslug;?>"><?php echo get_avatar($author_id, 145); ?></a>
                            </div>
                            <div class="col-sm-4 col-xs-8">
                                <div class="col-sm-12 col-md-12 col-lg-8 col-xs-12 freelance-name">
                                    <a href="<?php echo $uslug;?>"><?php echo $display_name ?></a>
                                    <?php if ($user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER) {
                                            echo '<span class="status">' . translate('PRO', ET_DOMAIN) . '</span>';
                                        } ?>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xs-12 free-rating">
                                    <? HTML_review_rating_user($author_id); ?>
                                </div>
                                <div class="col-sm-12 col-xs-12 freelance-profile-country">
                                    <?php if ($location && !empty($location['country'])) {
                                            $str = array();
                                            foreach ($location as $key => $item) {
                                                if (!empty($item['name'])) {
                                                    $str[] = $item['name'];
                                                }
                                            }
                                            echo !empty($str) ? implode(' - ', $str) : 'Error';
                                        } else { ?>
                                    <?php echo '<i>' . __('No country information', ET_DOMAIN) . '</i>'; ?>
                                    <?php
                                        }
                                        ?>
                                </div>
                                <div class="col-sm-6 hidden-xs skill">
                                    <?php printf( __( '<span>%s</span> project(s) posted', ET_DOMAIN ), fre_count_user_posts_by_type( $author_id, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true ) ); ?>
                                </div>
                                <div class="col-sm-6 hidden-xs skill">
                                        <?php printf(__('<span>%s</span> professionals hired', ET_DOMAIN), intval($hire_freelancer)); ?>
                                </div>
                            </div>
                            <div class="hidden-sm col-xs-6 skill">
                                <?php printf( __( '<span>%s</span> project(s) posted', ET_DOMAIN ), fre_count_user_posts_by_type( $author_id, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true ) ); ?>
                            </div>
                            <div class="col-xs-6 hidden-sm skill">
                                 <?php printf(__('<span>%s</span> professionals hired', ET_DOMAIN), intval($hire_freelancer)); ?>
                            </div>
                            <div class="col-sm-3 col-xs-12 else-info">
                                <div class="rating-new">
                                    <?php echo __('Rating:', ET_DOMAIN); ?><span>+<?=getActivityRatingUser($author_id)?></span>
                                </div>
                                <div class="secure-deals">
                                        <?php echo __('SafePay Deals:', ET_DOMAIN); ?>
                                    <span>
<!--                                        --><?//= (get_user_meta($user_ID,'safe_deals_count',1) == '')? 0 : get_user_meta($user_ID,'safe_deals_count',1) ?>
                                        <?= (get_user_meta($author_id,'safe_deals_count',1) == '') ? 0 : get_user_meta($author_id,'safe_deals_count',1) ?>
                                    </span>
                                </div>
                                <div class="reviews">
                                    <?php echo __('Reviews:', ET_DOMAIN); ?>
                                    <span>
<!--                                        --><?//=get_count_reviews_user($user_ID)?>
                                        <?= get_count_reviews_user($author_id) ?>
                                    </span>
                                </div>
                                <div class="city">
                                    <?php if ($location && !empty($location['country'])) {
                                            $str = array();
                                            foreach ($location as $key => $item) {
                                                if (!empty($item['name'])) {
                                                    $str[] = $item['name'];
                                                }
                                            }
                                            echo !empty($str) ? __('City:', ET_DOMAIN) . '<span>' . $str[2] . '</span>' : '';
                                        } ?>
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-12 skills">
                                <div class="skill col-lg-8 col-md-6 col-sm-6 col-xs-6">
                                    <?php echo __('skills & endorsements', ET_DOMAIN); ?><span><?=countEndorseSkillsUser($author_id)?></span>
                                </div>
                                <div class="skill col-sm-12 col-xs-6">
                                    <?php echo __('awards', ET_DOMAIN); ?><span>0</span>
                                </div>
                            </div>
                        </div>
                    </div>
        <?php } ?>               
        </div>
    </div>
