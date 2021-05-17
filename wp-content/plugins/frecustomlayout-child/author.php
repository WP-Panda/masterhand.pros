<?php
/**
 * The Template for displaying a user profile
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get(PROFILE);
$author_id 	= 	get_query_var( 'author' );
$author_name = get_the_author_meta('display_name', $author_id);
$author_available = get_user_meta($author_id, 'user_available', true);
// get user profile id
$profile_id = get_user_meta($author_id, 'user_profile_id', true);
// get post profile
$profile = get_post($profile_id);
$convert = '';

if( $profile && !is_wp_error($profile) ){
    $convert = $post_object->convert( $profile );
}

// try to check and add profile up current user dont have profile
if(!$convert && ( fre_share_role() || ae_user_role($author_id) == FREELANCER) ) {
    $profile_post = get_posts(array('post_type' => PROFILE,'author' => $author_id));
    if(!empty($profile_post)) {
        $profile_post = $profile_post[0];
        $convert = $post_object->convert( $profile_post );
        $profile_id = $convert->ID;
        update_user_meta($author_id, 'user_profile_id', $profile_id);
    }else {
        $convert = $post_object->insert( array( 'post_status' => 'publish' ,
                                                'post_author' => $author_id ,
                                                'post_title' => $author_name ,
                                                'post_content' => '')
                                        );

        $convert = $post_object->convert( get_post($convert->ID) );
        $profile_id = $convert->ID;
    }
}
//  count author review number
$count_review = fre_count_reviews($author_id);

get_header();
$next_post = false;
if($convert) {
    $next_post = ae_get_adjacent_post($convert->ID, false, '', true, 'skill');
}

?>
	<section class="breadcrumb-wrapper">
		<div class="breadcrumb-single-site">
        	<div class="container">
    			<div class="row">
                	<div class="col-xs-12">
                    	<ol class="breadcrumb">
                            <li><a href="<?php echo home_url(); ?>"><?php _e("Home", ET_DOMAIN); ?></a></li>
                            <li class="active"><?php printf(__("Profile of %s", ET_DOMAIN), $author_name); ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
	</section>
    <div class="single-profile-wrapper">
    	<div class="container">
        	<div class="row">
            	<div class="col-md-8">
                	<div class="tab-content-single-profile">
                    	<!-- Title -->
                    	<div class="row title-tab-profile">
                            <div class="col-md-12">
                                <h2><?php printf(__('ABOUT %s', ET_DOMAIN), strtoupper($author_name) ); ?></h2>
                            </div>
                        </div>
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                        	<div class="single-profile-top">
                                <ul class="single-profile">
                                    <li class="img-avatar"><span class="avatar-profile"><?php echo get_avatar($author_id, 70); ?></span></li>
                                    <li class="info-profile">
                                        <span class="name-profile"><?php echo $author_name; ?></span>
                                        <?php if($convert) { ?>
                                        <span class="position-profile"><?php echo $convert->et_professional_title; ?></span>
                                        <?php } ?>
                                        <span class="number-review-profile">
                                            <?php
                                                if($count_review < 2) {
                                                    if($count_review == 0){
                                                        printf(__('%d reviews', ET_DOMAIN), $count_review );
                                                    }else{
                                                        printf(__('%d review', ET_DOMAIN), $count_review );
                                                    }
                                                }else {
                                                    printf(__('%d reviews', ET_DOMAIN), $count_review );
                                                }
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                                <?php if($convert && (fre_share_role() || ae_user_role($author_id) == FREELANCER ) ){ ?>
                                <div class="list-skill-profile">
                                    <ul>
                                    <?php
                                        if(isset($convert->tax_input['skill']) && $convert->tax_input['skill']){
                                            foreach ($convert->tax_input['skill'] as $tax){
                                                echo '<li><span class="skill-name-profile">'.$tax->name.'</span></li>';
                                         	}
                                        }
                                    ?>
                                    </ul>
                                </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="single-profile-bottom">
                                <?php if($convert) { ?>
                                <!-- overview -->
                                <div class="profile-overview">
                                	<h4 class="title-single-profile"><?php _e('Overview', ET_DOMAIN);?></h4>
                                    <p><?php echo $convert->post_content; ?></p>
                                    <?php
                                        if(function_exists('et_the_field')) {
                                            et_render_custom_meta($convert);
                                            et_render_custom_taxonomy($convert);
                                        }
                                    ?>
                                </div>
                                <!--// overview -->
                                <?php }
                                // EMPLOYER TEMPLATE
                                if(fre_share_role() || ae_user_role($author_id) != FREELANCER ){
                                    get_template_part('template/author', 'employer-history');
                                }
                                // FREELANCER TEMPLATE
                                if( fre_share_role() || ae_user_role($author_id) == FREELANCER ){
                                    get_template_part('template/author', 'freelancer-history');
                                    $bid_posts   = $wp_query->found_posts;
                                ?>

                                    <?php
                                        query_posts( array(
                                                        // 'post_parent' => $convert->ID,
                                                        'post_status' => 'publish',
                                                        'post_type' => PORTFOLIO,
                                                        'author' => $author_id )
                                                    );
                                        if(have_posts()):
                                        echo '<div class="portfolio-container">';
                                            get_template_part('template/portfolios', 'filter' );
                                            // list portfolio
                                            get_template_part( 'list', 'portfolios' );
                                        echo '</div>';
                                        else :
                                        endif;
                                        wp_reset_query();
                                    ?>

                                <?php } ?>
                                </div>
                        </div>
                        <!-- Content project / End -->
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Title -->
                    <div class="row title-tab-profile">
                        <div class="col-md-12">
                            <h2><?php _e('INFO', ET_DOMAIN);?></h2>
                        </div>
                    </div>
                    <div class="single-profile-content">
                        <?php
                        if( fre_share_role() || ( ae_user_role($author_id) == FREELANCER && (ae_user_role($user_ID) == EMPLOYER || current_user_can('manage_options'))) ) {
                         if($author_available == 'on' || $author_available == '' ){ ?>
                            <div class="contact-link">
                                <a href="#" data-toggle="modal" class="fre-normal-btn <?php if ( is_user_logged_in() ) { echo 'invite-open';}else{ echo 'login-btn';} ?>"  data-user="<?php echo $convert->post_author ?>">
                                    <?php _e("Invite me to join", ET_DOMAIN) ?>
                                </a>
                            </div>
                        <?php } else {
                                echo '<h3 style="padding: 20px 25px;margin:0;">'.$author_name .'</h3>';
                            }
                        }
                        $rating = Fre_Review::freelancer_rating_score($author_id);
                        ?>
                    </div>
                    <?php if( ae_user_role($author_id) == FREELANCER ){?>
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                            <ul class="list-detail-info">
                            	<li>
                                    <i class="fa fa-dollar"></i>
                                    <span class="text"><?php _e('Hourly Rate:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo fre_price_format($convert->hour_rate);  ?></span>
                                </li>
                                <li>
                                	<i class="fa fa-star"></i>
                                    <span class="text"><?php _e('Rating:',ET_DOMAIN);?></span>
                                	<div class="rate-it" data-score="<?php echo $rating['rating_score']; ?>"></div>
                                </li>
                                <li>
                                    <i class="fa fa-pagelines"></i>
                                    <span class="text"><?php _e('Experience:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo $convert->experience; ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-briefcase"></i>
                                    <span class="text"><?php _e('Projects worked:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo $bid_posts; ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-money"></i>
                                    <span class="text"><?php _e('Total earned:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo fre_price_format(fre_count_total_user_earned($author_id)); ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-map-marker"></i>
                                    <span class="text"><?php _e('Country:',ET_DOMAIN);?></span>
                                    <span class="text-right">
                                        <?php
                                        if($convert->tax_input['country']){
                                            echo $convert->tax_input['country']['0']->name;
                                        } ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    <?php }else{ ?>
                        <div class="info-company-wrapper">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php fre_display_user_info( $author_id ); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Content project / End -->
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();