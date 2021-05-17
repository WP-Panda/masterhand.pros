<?php
/**
 * Template Name: Member Profile Page
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;

//convert current user
$ae_users = AE_Users::get_instance();
$user_profile_id = get_user_meta($user_ID, "user_profile_id", true);
$user_data = $ae_users->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);
//convert current profile
$post_object = $ae_post_factory->get(PROFILE);

$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
$user_phone_code = get_user_meta($user_ID, 'ihs-country-code', true);
$user_phone = get_user_meta($user_ID, 'user_phone', true);

$profile = array();
if ($profile_id) {
    $profile_post = get_post($profile_id);

    if ($profile_post && !is_wp_error($profile_post)) {
        $profile = $post_object->convert($profile_post);
    }
}

$isFreelancer = (userRole($author_id) == FREELANCER) ? 1 : 0;

$current_profile_categories = get_the_terms($profile, 'project_category');
//define variables:
$job_title = isset($profile->et_professional_title) ? $profile->et_professional_title : '';
$hour_rate = isset($profile->hour_rate) ? $profile->hour_rate : '';
$currency = isset($profile->currency) ? $profile->currency : '';
$experience = isset($profile->et_experience) ? $profile->et_experience : '';
$hour_rate = isset($profile->hour_rate) ? $profile->hour_rate : '';
$about = isset($profile->post_content) ? $profile->post_content : '';
$display_name = $user_data->display_name;
$user_available = isset($user_data->user_available) && $user_data->user_available == "on" ? 'checked' : '';

include $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
$location = getLocation($user_ID);

//for email
$user_confirm_email = get_user_meta($user_ID, 'register_status', true);

get_header();
// Handle email change requests
$user_meta = get_user_meta($user_ID, 'adminhash', true);


if (!empty($_GET['adminhash'])) {
    if (is_array($user_meta) && $user_meta['hash'] == $_GET['adminhash'] && !empty($user_meta['newemail'])) {
        wp_update_user(array(
            'ID' => $user_ID,
            'user_email' => $user_meta['newemail']
        ));
        delete_user_meta($user_ID, 'adminhash');
    }
    echo "<script> window.location.href = '" . et_get_page_link("profile") . "'</script>";
} elseif (!empty($_GET['dismiss']) && 'new_email' == $_GET['dismiss']) {
    delete_user_meta($user_ID, 'adminhash');
    echo "<script> window.location.href = '" . et_get_page_link("profile") . "'</script>";
}

$role_template = 'employer';

$projects_worked = get_post_meta($profile_id, 'total_projects_worked', true);
$project_posted = fre_count_user_posts_by_type($user_ID, 'project', '"publish","complete","close","disputing","disputed", "archive" ', true);
$hire_freelancer = fre_count_hire_freelancer($user_ID);

$currency = ae_get_option('currency', array(
    'align' => 'left',
    'code' => 'USD',
    'icon' => '$'
));

$user_status = get_user_pro_status($user_ID);
if ($user_status) {
    $user_pro_expire = get_user_pro_expire($user_ID);
    $user_pro_expire = strtotime($user_pro_expire);
    $user_pro_expire_normalize = date( 'F d, Y', $user_pro_expire );
    $user_pro_expire = date( 'M-d-Y', $user_pro_expire );
    $user_pro_name   = get_user_pro_name($user_ID);
}


$personal_cover = getValueByProperty($user_status, 'personal_cover');

if ($personal_cover) {
    $img_url = get_user_meta($user_ID, 'cover_url');
    if ($img_url) {
        $style = 'style="background-image: url(' . $img_url[0] . '); background-repeat: no-repeat; background-size: 100% 100%;"';
    }
}
$visualFlag = getValueByProperty($user_status, 'visual_flag');
if ($visualFlag) {
    $visualFlagNumber = get_user_meta($user_ID, 'visual_flag', true);
}

if (is_plugin_active('referral_code/referral_code.php')) {
    $referral_code = get_referral_code_by_user($user_ID);
    $count_referrals = get_count_referrals($user_ID);
    $referrals = get_list_referrals('all', $user_ID);
    $sponsor_name = get_sponsor($user_ID);
} else {
    $referral_code = '0000000000';
    $count_referrals = 0;
}
$is_company = get_user_meta($user_ID, 'is_company', true);

?>

<div class="fre-page-wrapper list-profile-wrapper" <?= $style ?>>
    <div class="fre-page-title hidden-xs">
        <div class="container">
            <h1>
                <?php _e('My Profile', ET_DOMAIN) ?>
            </h1>
        </div>
    </div>

    <div class="fre-page-section">
        <div class="container">
            <div class="profile-freelance-wrap">
                 <?php if ((!empty($user_confirm_email) && $user_confirm_email !== 'confirm') || (empty($user_confirm_email))) { ?>
                    <div class="notice-first-login blue">
                        <p>
                            <i class="fa fa-warning"></i>
                            Please confirm your email to activate your account <a class="request-confirm fre-submit-btn btn-right">Activate Account</a>
                        </p>
                    </div>
                <?php }?>
                <?php if (empty($profile_id) && (fre_share_role() || ae_user_role($user_ID) == FREELANCER)) { ?>
                    <div class="notice-first-login">
                        <p>
                            <i class="fa fa-warning"></i>
                            <?php _e('Paypal account and Profile completion are required to bid on projects and make deals. Please go to Settings to complete your profile.', ET_DOMAIN) ?>
                        </p>
                    </div>
                <?php } ?>
                <?php if (empty($profile_id) && (fre_share_role() || ae_user_role($user_ID) == EMPLOYER)) { ?>
                    <div class="notice-first-login">
                        <p>
                            <i class="fa fa-warning"></i>
                            <?php _e('Paypal account and Profile completion are recommended to make SafePay deals and receive money(refunds). Please go to Settings to complete your profile.', ET_DOMAIN) ?>
                        </p>
                    </div>
                <?php } ?>
                <div class="fre-profile-box">
                    <div class="profile-freelance-info-wrap active">
                        <div class="profile-freelance-info top cnt-profile-hide row" id="cnt-profile-default"
                             style="display: block">
                            <div class="col-sm-2 col-xs-4 text-center avatar_wp">
                                <?php echo get_avatar($user_data->ID, 145); ?>
                            </div>
                            <div class="col-lg-3 col-sm-4 col-md-3 col-xs-8 no-pad">
                                <div class="col-sm-12 col-md-12 col-lg-7 col-xs-12 freelance-name">
                                    <?php echo $display_name ?>
                                    <?php if ($user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER) {
                                        echo '<span class="status">' . translate('PRO', ET_DOMAIN) . ' </span>';
                                       // echo '<span class="status">' . $user_pro_name . ' </span>';
                                        echo '<div class="status_expire">Expire: ' . $user_pro_expire . '</div>';
                                    } ?>
                                    <?php switch ($visualFlagNumber) {
                                        case 1:
                                            echo '<span class="status">' . translate('Master', ET_DOMAIN) . '</span>';
                                            break;
                                        case 2:
                                            echo '<span class="status">' . translate('Creator', ET_DOMAIN) . '</span>';
                                            break;
                                        case 3:
                                            echo '<span class="status">' . translate('Expert', ET_DOMAIN) . '</span>';
                                            break;
                                    } ?>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xs-12 free-rating">
                                    <?php HTML_review_rating_user($user_ID) ?>
                                </div>
                                <div class="col-sm-12 col-xs-12 freelance-profile-country">
                                    <?php
                                    if ($location && !empty($location['country'])) {
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
                                <?php if (fre_share_role() || ae_user_role($user_data->ID) == FREELANCER) { ?>
                                    <div class="col-sm-6 hidden-xs skill">
                                        <?php echo !empty($profile->experience) ? $profile->experience : '<span>0</span>' . __('years experience', ET_DOMAIN); ?></div>
                                    <div class="col-sm-6 hidden-xs skill">
                                        <?php printf(__('<span>%s</span> projects worked', ET_DOMAIN), intval($projects_worked)); ?> </div>
                                <?php } else { ?>
                                    <div class="col-sm-6 hidden-xs skill">
                                        <?php printf(__('<span>%s</span> projects posted', ET_DOMAIN), intval($project_posted)); ?></div>
                                    <div class="col-sm-6 hidden-xs skill">
                                        <?php printf(__('<span>%s</span> professionals hired', ET_DOMAIN), intval($hire_freelancer)); ?></div>
                                <?php } ?>
                            </div>

                            <?php if (fre_share_role() || ae_user_role($user_data->ID) == FREELANCER) { ?>
                                <div class="hidden-sm col-xs-12">
                                    <div class="col-xs-6 skill">
                                        <?php echo !empty($profile->experience) ? $profile->experience : '<span>0</span>' . __('years experience', ET_DOMAIN); ?></div>
                                    <div class="col-xs-6 skill">
                                        <?php printf(__('<span>%s</span> projects worked', ET_DOMAIN), intval($projects_worked)); ?> </div>
                                </div>
                            <?php } else { ?>
                                <div class="hidden-sm col-xs-12">
                                    <div class="col-xs-6 skill">
                                        <?php printf(__('<span>%s</span> projects posted', ET_DOMAIN), intval($project_posted)); ?></div>
                                    <div class="col-xs-6 skill">
                                        <?php printf(__('<span>%s</span> professionals hired', ET_DOMAIN), intval($hire_freelancer)); ?></div>
                                </div>
                            <?php } ?>

                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 else-info">
                                <div class="rating-new">
                                    <?php echo __('Rating:', ET_DOMAIN); ?>
                                    <span>+<?= getActivityRatingUser($user_ID) ?></span>
                                </div>
                                <div class="secure-deals">
                                    <a href="/give-endorsements">
                                        <?php echo __('SafePay Deals:', ET_DOMAIN); ?>
                                    </a><span><?= (get_user_meta($user_ID,'safe_deals_count',1) == '')? 0 : get_user_meta($user_ID,'safe_deals_count',1) ?></span>
                                </div>
                                <div class="reviews">
                                    <?php echo __('Reviews:', ET_DOMAIN); ?>
                                    <span><?= get_count_reviews_user($user_ID); ?></span>
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

                            <div class="col-md-2 col-sm-3 col-lg-2 col-xs-12 skills">
                                <div class="skill col-sm-12 col-xs-6">
                                    <?php echo __('skills & endorsements', ET_DOMAIN); ?>
                                    <span><?= countEndorseSkillsUser($user_ID) ?></span>
                                </div>
                                <div class="skill col-sm-12 col-xs-6">
                                    <?php echo __('awards', ET_DOMAIN); ?><span>0</span>
                                </div>
                            </div>

                            <div class="col-sm-3 col-md-3 col-lg-2 col-xs-12 fre-profile_refinfo">
                                <span><?php echo __('My referral code:', ET_DOMAIN) ?></span>
                                <?php $url = $_SERVER["HTTP_HOST"] . '/register/?code='; ?>
                                <div id="Text" class="copy refnumber">
                                    <span><?= $referral_code; ?></span>
                                </div>
                                <script>
                                    function selectText(doc, elementId, text) {
                                        var range, selection;
                                        text.innerText = '<?= $url ?>' + text.innerText;
                                        if (doc.body.createTextRange) {
                                            range = document.body.createTextRange();
                                            range.moveToElementText(text);
                                            range.select();
                                        } else if (window.getSelection) {
                                            selection = window.getSelection();
                                            range = document.createRange();
                                            range.selectNodeContents(text);
                                            selection.removeAllRanges();
                                            selection.addRange(range);
                                        }
                                    }

                                    document.getElementsByClassName('copy')[0].click(function () {
                                        var doc = document,
                                            text = doc.getElementById(this.id),
                                            str = text.innerText;

                                        selectText(doc, this.id, text);
                                        doc.execCommand("copy");
                                        doc.getElementById(this.id).innerText = str;
                                        alert("text copied")
                                    });

                                </script>
                                <a href='/pro' class='fre-status'>
                                    <?php _e('Change Account Pro', ET_DOMAIN) ?>
                                </a>
                                <a href="<?php echo $user_data->author_url ?>" id="link_as_others">
                                    <?php _e('View my profile as others', ET_DOMAIN) ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <!--first--info-->

                <?php $linkname = '';
                $linkurl = $user_data->author_url;//'http://master.loc/author/alexey_marat/';
                $linkref = $_SERVER["HTTP_HOST"] . '/register/?code=' . $referral_code;
                if (function_exists('ADDTOANY_SHARE_SAVE_KIT')) {
                    ADDTOANY_SHARE_SAVE_KIT(compact('linkname', 'linkurl'));
                } ?>
                <script>
                    var a2a_config = a2a_config || {};
                    a2a_config.templates = a2a_config.templates || {};
                    var link = <?php echo json_encode($linkref); ?>

                        a2a_config.templates.email = {
                            subject: "Check this out: ${title}",
                            body: "Click the link by registry with referral code:\n" + link
                        };
                </script>

                <div class="fre-profile-box skills_awards_wp">
                    <div class="row skills_awards">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 skill-list">  <!-- пока нет наград - ставим 12 вместо col-lg-6 col-md-6 col-sm-6-->
                            <a href="#modal_edit_skills" data-toggle="modal"
                                   class="unsubmit-btn btn-right open-edit-skills">
                                <? _e('Add skills', ET_DOMAIN); ?>
                            </a>
                            <div class="bl_t">
                                <?php echo __('Skills and Endorsements:', ET_DOMAIN); ?>
                                <?php if (!$is_company){ ?>
                                	 <div class="skill-list__placeholder"> <?php echo __('You can put here your personal skills, related keywords . For example Polite, Demanding, Recognize Brilliance, Pay Promptly etc.', ET_DOMAIN); ?>
                                	 </div>
                                <?php }else{ ?>
                                	<div class="skill-list__placeholder">
                                	<?php echo __('You can put here your professional and personal skills, related keywords . For example Drilling, General Woodworking, Cleaning Sewer Lines, Problem-Solving etc.', ET_DOMAIN);
                                	 ?>
                                	 </div>
                                <?php } ?>
                            </div>
                          
                            <ul id="list_skills_user">
                                <?php
                                wp_enqueue_style('endoSkSel');
                                wp_enqueue_style('endoSk');
                                wp_enqueue_script('endoSkSel');
                                wp_enqueue_script('endoSk');
                                renderSkillsInProfile($user_ID);
                                ?>
                            </ul>
                            <? get_template_part('template-js/modal', 'edit-skills'); ?>
                        </div>
                        <!-- пока нет наград - скрываем НЕ УДАЛЯТЬ!!!!!!!!!!!!
                        <div class="col-sm-6 col-xs-12 award-list">
                            <div class="bl_t">
                                <?php echo __('Awards:', ET_DOMAIN); ?>
                            </div>
                            <ul class="row">
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw1.png" alt=""/>1-st place on DC 2018
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw2.png" alt=""/>Pro league in Germany
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw3.png" alt=""/>Gold Members
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw4.png" alt=""/>League of Masters
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw1.png" alt=""/>League of Masters
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw2.png" alt=""/> Best proffesional
                                </li>
                                <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/aw3.png" alt=""/>100 deals of Octomber
                                </li>
                            </ul>
                        </div>-->
                    </div>
                    <div class="show_more">
                        <?php echo __('Show more', ET_DOMAIN); ?><i class="fa fa-angle-down"></i>
                    </div>
                    <div class="hide_more">
                        <?php echo __('Hide more', ET_DOMAIN); ?><i class="fa fa-angle-up"></i>
                    </div>
                </div>
                <!--profile-box--->

                <?php if (fre_share_role() || $user_role == FREELANCER) { ?>
                    <div class="profile-freelance-available hidden">
                        <div class="fre-input-field">
                            <input type="checkbox" <?php /*echo $user_available; */ ?> class="js-switch user-available"
                                   name="user_available"/>
                            <span class="user-status-text text <?php /*echo $user_available ? 'yes' : 'no' */ ?>"></span>
                        </div>
                        <div class="fre-input-field">
                            <label for="fre-switch-user-available" class="fre-switch">
                                <input id="fre-switch-user-available" type="checkbox" checked>
                                <div class="fre-switch-slider"></div>
                            </label>
                        </div>
                    </div>
                <?php } ?>

                <ul class="nav nav-justify-content-center" id="Tabs" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="rating-tab" data-toggle="tab" href="#rating" role="tab"
                           aria-controls="rating" aria-selected="true">
                            <?php echo __("Rating", ET_DOMAIN); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab"
                           aria-controls="reviews" aria-selected="false">
                            <?php echo __("Reviews", ET_DOMAIN); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
                           aria-controls="settings" aria-selected="false">
                            <?php echo __("Settings", ET_DOMAIN); ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="TabsContent">
                    <div class="tab-pane fade in active" id="rating" role="tabpanel" aria-labelledby="rating-tab">
                        <div class="row">
                            <div class="col-sm-12 col-md-8 col-lg-8 col-xs-12 fre-profile-rating fre-profile-box">
                                <div class="fre-profile-rating_t">
                                    <?php echo __("My rating", ET_DOMAIN); ?><span class="total-rating">+<?= getActivityRatingUser($user_ID) ?></span>
                                </div>
                                <ul class="pro-dop">
                                    <li>
                                      <?php echo __("PRO status ", ET_DOMAIN); ?><span class="pro-rating">+<?= getActivityProRatingUser($user_ID) ?></span>
                                    </li>
                                </ul>
                                <ul class="dop">
                                  <? getActivityDetailUser($user_ID) ?>
                                </ul>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 col-xs-12">
                                <div class="category">
<?php if (fre_share_role() || ae_user_role($user_data->ID) == FREELANCER) { ?>
  <div class="fre-blog-item fre-blog-item-2">
  <div class="overlay-block">
<div class="fre-blog-item_t center"><a href="https://www.masterhand.pro/business-promotion-with-know-how/">Business promotion with Know-How</a></div>
<div class="fre-blog-item_d center"><p>Some 2-3 articles/posts per month would work very effectively to promote your business and support your brand with potential customers.</p></div>
<br>
<div class="fre-blog-item_more"><a href="https://www.masterhand.pro/business-promotion-with-know-how/">MORE DETAILS</a></div>
</div></div>
<div class="fre-blog-item fre-blog-item-2">
<div class="overlay-block">
<div class="fre-blog-item_t center"><a href="/pro-benefits-for-pro/">Pro benefits for Pro</a></div>
<div class="fre-blog-item_d center"><p>You are a Trusted Professional. Choose and activate your PRO plan to get benefits from it.
</p></div>
<br>
<div class="fre-blog-item_more"><a href="/pro-benefits-for-pro/">MORE DETAILS</a></div>
</div>
</div>
<div class="fre-blog-item fre-blog-item-2">
<div class="overlay-block">
<div class="fre-blog-item_t center"><a href="/why-referals-are-very-important-pro-2/">Why referals are very important for PRO</a></div>
<div class="fre-blog-item_d center"><p>You can be in TOP Professionals. Promote your business constantly. Share your profile via email, in social networks, and even offline.
</p></div>
<br>
<div class="fre-blog-item_more"><a href="/why-referals-are-very-important-pro-2/">MORE DETAILS</a></div>
</div>
</div>
                            <?php } else { ?>
                              <div class="fre-blog-item fre-blog-item-1">
                                <div class="overlay-block">
                                  <div class="fre-blog-item_t center"><a href="/pro-benefits-for-client/">Pro benefits for Client</a></div>
                                  <div class="fre-blog-item_d center"><p>You are a Trusted Client. Choose and activate your PRO plan to get many benefits from it.</p></div>
                                  <br>
                                  <div class="fre-blog-item_more"><a href="/pro-benefits-for-client/">MORE DETAILS</a></div>
                                </div>

                               </div>
                               <div class="fre-blog-item fre-blog-item-1">
                                 <div class="overlay-block">
                                   <div class="fre-blog-item_t center"><a href="/why-referals-are-very-important-client/">Why referals are very important for Client</a></div>
                                   <div class="fre-blog-item_d center"><p>You can be a highly ranked Client- uphold your reputation and increase your rating constantly. Invite new referrals using special tools via email and social networks.</p></div>
                                   <br>
                                   <div class="fre-blog-item_more"><a href="/why-referals-are-very-important-client/">MORE DETAILS</a></div>
                                 </div>
                               </div>
                                <?php } ?>
                                </div> </div>
                            <div class="col-sm-12 col-md-4 col-lg-4 col-xs-12" hidden>
                                <div class="category">
                                    <?php $stposts = array('numberposts' => 3, 'post_type' => 'post', 'orderby' => 'date', 'order' => 'desc', 'suppress_filters' => true);
                                    $lastposts = get_posts($stposts);
                                    foreach ($lastposts as $post) {
                                        setup_postdata($post);
                                        get_template_part('template/blog', 'stickynoimg');
                                    }
                                    wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tabs_wp fre-profile-box accpro col-sm-12 col-xs-12">
                            <div class="col-sm-9 col-xs-12">
                                <div class="tabs_wp_t">
                                    <?php echo __("Account", ET_DOMAIN);
                                    if ($user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER) { ?>
                                    <span class="status"><?php echo __('PRO', ET_DOMAIN); ?></span>
                                    </div>
                                    <div class="pro-account">
                                        <p>
                                            <?php echo __("Your account has $user_pro_name status.", ET_DOMAIN); ?>
                                        </p>
                                        <div class="benefits">
                                            <?php echo __("My PRO Benefits:", ET_DOMAIN) . '<span class="confirmed">' . __("Enabled", ET_DOMAIN) . ' (expire on '.$user_pro_expire_normalize.')</span>'; ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    </div>
                                    <div class="pro-account">
                                        <p>
                                            <?php echo __("Get PRO status to have more benefits.", ET_DOMAIN); ?>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div><!--col-sm-9 col-xs-12-->
                        <div class="col-sm-3 col-xs-12">
                            <a href='/pro' class='fre-status unsubmit-btn btn-right'>
                                <?php _e('Pro status', ET_DOMAIN) ?>
                            </a>
                        </div>
                    </div>
                    <div class="tabs_wp fre-profile-box referalac col-sm-12 col-xs-12">
                        <div class="col-sm-9 col-xs-12">
                            <div class="tabs_wp_t">
                                <?php echo __("My Referral Activity", ET_DOMAIN); ?>
                            </div>
                            <div class="refcount">
                                <p>
                                    <?php echo __("Referrals Connected:", ET_DOMAIN); ?>
                                    <span><?= $count_referrals ?></span></p>
                            </div>
                           <!-- <div class="awards"><span><?php echo __("Awards:", ET_DOMAIN); ?></span>
                                <ul>
                                    <li><img src="<?php echo get_template_directory_uri(); ?>/img/aw3.png" alt=""/><span
                                                class="visible-xs">Gold Members</span></li>
                                    <li><img src="<?php echo get_template_directory_uri(); ?>/img/aw4.png" alt=""/><span
                                                class="visible-xs">Gold Members</span></li>
                                    <li><img src="<?php echo get_template_directory_uri(); ?>/img/aw1.png" alt=""/><span
                                                class="visible-xs">Gold Members</span></li>
                                    <li><img src="<?php echo get_template_directory_uri(); ?>/img/aw2.png" alt=""/><span
                                                class="visible-xs">Gold Members</span></li>
                                </ul>
                            </div>-->
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <a href='/referrals' class='fre-status unsubmit-btn btn-right'>
                                <?php _e('Get more referrals', ET_DOMAIN) ?>
                            </a>
                            <table class="table table-hover accordion">
                                <a href='<?= '/give-endorsements' . $sponsor_name ?>' class='unsubmit-btn btn-right' style="margin-top: 10px;">
                                    <?php _e('Give Endorsement', ET_DOMAIN) ?>
                                </a>
                            </table>
                        </div>
                    </div>
                </div><!--rating rab-->

                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <?php
                    get_template_part('template/author', 'freelancer-history');
                    ?>
                </div><!--review rab-->

                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <div class="fre-profile-box">
                        <div class="profile-freelance-info-wrap">
                            <div class="profile-freelance-info row" id="cnt-profile-default">
                                <div class="col-sm-2 col-xs-4 avatar_wp">
                                    <?php echo get_avatar($user_data->ID, 145); ?>
                                    <a href="#" id="user_avatar_browse_button" class="hidden-xs">
                                        <?php _e('Change Photo', ET_DOMAIN) ?>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-md-7 col-lg-8 col-xs-8">
                                    <div class="col-sm-8 col-xs-12 freelance-name">
                                        <?php echo $display_name ?>
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
                                        } else {
                                            echo '<i>' . __('No country information', ET_DOMAIN) . '</i>';
                                        } ?>
                                    </div>
                                    <div class="col-sm-12 hidden-xs fre-jobs_txt">
                                        <?php $post = isset($profile);
                                        if ($post) {
                                            setup_postdata($profile);
                                            if (!empty($profile_id)) {
                                                the_content();
                                            }
                                            wp_reset_postdata();
                                        } ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 visible-xs fre-jobs_txt">
                                    <?php if (fre_share_role() || $isFreelancer) {
                                        if ($hour_rate > 0) { ?>
                                            <div class="rate visible-xs">
                                                <?php echo __("Rate:", ET_DOMAIN); ?>
                                                <span><?php echo sprintf(__('%s/hr ', ET_DOMAIN), fre_price_format($hour_rate)); ?></span>
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                </div>
                                <div class="col-sm-4 col-md-3 col-lg-2 col-xs-12">
                                    <a href="#editprofile" data-toggle="modal"
                                       class="fre-submit-btn employer-info-edit-btn btn-right">
                                        <?php _e('Edit', ET_DOMAIN) ?>
                                    </a>
                                    <?php if (fre_share_role() || $isFreelancer) { ?>
                                        <?php if ($hour_rate > 0) { ?>
                                            <div class="rate hidden-xs">
                                                <?php echo __("Rate:", ET_DOMAIN); ?>
                                                <span><?php echo sprintf(__('%s/hr ', ET_DOMAIN), fre_price_format($hour_rate)); ?></span>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>

                            <div class="row secure-bl">
                                <div class="col-sm-12 col-md-6 col-lg-5 col-xs-12">
                                    <div class="cnt-profile-hide" id="cnt-account-default" style="display: block">
                                        <p>
                                            <?php _e('Email:', ET_DOMAIN) ?>
                                            <span><?php echo $user_data->user_email; ?></span></p>
                                        <?php if ((!empty($user_confirm_email) && $user_confirm_email !== 'confirm') || (empty($user_confirm_email))) { ?>
                                            <span class="not-confirm"><?php echo __('Not confirmed', ET_DOMAIN); ?><i>X</i></span>
                                        <?php } else { ?>
                                            <span class="confirm"><?php echo __('Confirmed', ET_DOMAIN); ?> <i
                                                        class="fa fa-check"></i></span>
                                        <?php } ?>
                                        <script>
                                            function confirm_email_again($type) {
                                                if ($type) {
                                                    document.getElementById('user_email').value = '<?= trim($user_data->user_new_email) ?>'
                                                }
                                                document.getElementById('account_form_submit').click()
                                            }
                                        </script>
                                        <div>
                                            <?php
                                            if ($user_data->user_new_email) {
                                                printf(__('<p class="noti-update">There is a pending change of the email to %1$s.</p>', ET_DOMAIN),
                                                    '<code>' . $user_data->user_new_email . '</code>',
                                                    esc_url(et_get_page_link("profile") . '?dismiss=new_email')
                                                );
                                            } ?>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modal_change_phone" style="background:rgba(0,0,0,.45);">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"></button>
                                                    <div class="modal_t"><?php _e("Update your phone number", ET_DOMAIN) ?></div>
                                                </div>
                                                <div class="modal-body phone_form">
                                                    <div class="profile-employer-secure-edit" id="ctn-edit-account"
                                                         style="display:block;">
                                                        <form role="form" id="account_form_phone"
                                                              class="account_form fre-modal-form auth-form chane_phone_form">
                                                            <div class="fre-input-field">
                                                                <input type="number" class="user_phone" id="user_phone"
                                                                       name="user_phone"
                                                                       value="<?php echo $user_phone ?>"
                                                                       placeholder="<?php _e('XXXXXXXXXX', ET_DOMAIN) ?>">
																																			 
                                                            </div>
                                                            <div class="fre-form-btn">
                                                                <input type="submit"
                                                                       class="btn-left fre-submit-btn fre-btn save-btn phone_up"
                                                                       id="account_form_submit"
                                                                       value="<?php _e('SAVE', ET_DOMAIN) ?>">
                                                                <a href="#editprofile" data-toggle="modal"
                                                                   class="fre-cancel-btn employer-info-cancel-btn"
                                                                   data-dismiss="modal">
                                                                    <?php _e('Cancel', ET_DOMAIN) ?>
                                                                </a>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <script>
                                        jQuery(function ($) {
                                            var page = document.getElementsByClassName('page-id-9')
                                            if(page.length !== 0) {
                                                var phone_code = document.getElementsByName('ihs-country-code')
                                                var phone = document.getElementById('user_phone')
                                                phone_code[0].value = '<?=$user_phone_code?>'
                                                phone_code[0].parentElement.style.width = "9rem"
                                                phone.style.width = "calc(100% - 9rem)"
                                            }
                                            $('#ihs-mobile-otp1').attr('placeholder','Enter Verification Code')
                                        })
                                    </script>

                                    <div class="phone-secure">
                                        <p>
                                            <?php _e("Phone", ET_DOMAIN);
                                            echo '<span>' . $user_phone_code . $user_phone . '</span>'; ?></p>
                                        <?php if ($user_phone) {
                                            echo '<span class="confirm">' . __('Confirmed', ET_DOMAIN) . '<i class="fa fa-check"></i></span>'; ?>
                                        <?php } else {
                                            echo '<span class="not-confirm">' . __('Not confirmed', ET_DOMAIN) . '<i>X</i></span>'; ?>
                                        <?php } ?>
                                    </div>

                                    <?php 
                                    $paypal_confirmation = get_user_meta($user_ID, 'paypal_confirmation', true);
                                    $paypal = get_user_meta($user_ID, 'paypal', true);
                                    if (use_paypal_to_escrow()) { ?>
                                        <p style="position: relative;">
                                            <?php _e('Paypal account email', ET_DOMAIN) ?>

                                            <?php
                                            
                                            if (!empty($paypal)) {
                                                echo '<span class="paypal_account_field"><span>' . $paypal . '</span></span>';
                                                ?>
                                                <?php if ($paypal_confirmation) {
                                                    echo '<span class="confirm">' . __('Confirmed', ET_DOMAIN) . '<i class="fa fa-check"></i></span>'; ?>
                                                <?php } else {
                                                    echo '<span class="not-confirm">' . __('Not confirmed', ET_DOMAIN) . '<i>X</i></span>'; ?>
                                                <?php } 
                                                if(!$paypal_confirmation) {
                                                    echo "<div style='color:red;'>The paypal account must be confirmed to make deals</div>";
                                                    echo "<div style='color:red;'>To confirm your paypal account, we will get 1$ and then return it back</div>";
                                                }
                                            } else { ?>
                                                <span class="freelance-empty-info"><?php _e('Not updated', ET_DOMAIN) ?></span>
                                            <?php } ?>
                                        </p>
                                        <style>
                                            .paypal_account_field span {
                                                white-space: nowrap;
                                                width: 100px;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                                display: inline-block;
                                                vertical-align: top;
                                                padding-left: 10px;
                                                padding-right: 0;
                                            }
                                        </style>
                                    <?php } ?>

                                    <div class="confirm-btns">
                                        <?php if(!$paypal_confirmation && !empty($paypal)):?>
                                            <a href="javascript:;" class="btn-left fre-submit-btn confrim_paypal_account">
                                                <?php _e("Confirm paypal account", ET_DOMAIN) ?>
                                            </a>
                                        <?php endif;?>
                                        <a href="#modal_change_phone" data-toggle="modal"
                                           class="btn-left fre-submit-btn change-phone">
                                            <?php _e("Confirm by sms", ET_DOMAIN) ?>
                                        </a>

                                        <?php
                                            
                                        ?>

                                        <?php if (!empty($user_confirm_email) && $user_confirm_email !== 'confirm') { // когда пустое поле мета ничего не происходит
                                            printf(__('<a class="request-confirm fre-submit-btn btn-right">Confirm E-mail</a>', ET_DOMAIN),
                                                '<code>' . esc_html($user_data->user_new_email) . '</code>'
                                            );
                                        } elseif (!$user_confirm_email) {
                                            if (!$user_data->user_email) {
                                                printf(__('<p class="noti-update">You must add an email</p>', ET_DOMAIN));
                                            } else {
                                                printf(__('<a class="request-confirm btn-right fre-submit-btn">Confirm E-mail</a>', ET_DOMAIN),
                                                    '<code>' . esc_html($user_data->user_new_email) . '</code>'
                                                );
                                            }
                                        } ?>
                                    </div>

                                    <?php //stripe
                                    $escrow_stripe_api = ae_get_option('escrow_stripe_api', false);
                                    $use_escrow = ae_get_option('use_escrow', false);
                                    if (!empty($escrow_stripe_api) && $use_escrow && function_exists('ae_stripe_recipient_field')) {
                                        if (!empty($escrow_stripe_api['use_stripe_escrow'])) { ?>
                                            <div class="stripe-connect__wrap">
                                                <p><?php _e("Stripe account", ET_DOMAIN) ?></p>

                                                <div class="stripe-connect__btns-wrap">
                                                    <?php do_action('ae_escrow_stripe_user_field'); ?>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>


                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-7 col-xs-12">
                                    <?php $fb = get_post_meta($profile_id, 'facebook', true);
                                    $skype = get_post_meta($profile_id, 'skype', true);
                                    $web = get_post_meta($profile_id, 'website', true);
                                    $viber = get_post_meta($profile_id, 'viber', true);
                                    $wapp = get_post_meta($profile_id, 'whatsapp', true);
                                    $telegram = get_post_meta($profile_id, 'telegram', true);
                                    $wechat = get_post_meta($profile_id, 'wechat', true);
                                    $ln = get_post_meta($profile_id, 'linkedin', true);
                                    if ($fb) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Facebook:</span>
                                            <a href="<?php echo $fb; ?>" target="_blank" rel="nofollow">
                                                <?php echo $fb; ?>
                                            </a>
                                        </p>
                                    <?php }
                                    if ($skype) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Skype:</span>
                                            <?php echo $skype; ?>
                                        </p>
                                    <?php }
                                    if ($web) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Website:</span>
                                            <?php echo $web; ?>
                                        </p>
                                    <?php }
                                    if ($viber) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Viber:</span>
                                            <?php echo $viber; ?>
                                        </p>
                                    <?php } ?>
                                    <?php if ($wapp) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>WhatsApp:</span>
                                            <?php echo $wapp; ?>
                                        </p>
                                    <?php }
                                    if ($telegram) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Telegram:</span>
                                            <?php echo $telegram; ?>
                                        </p>
                                    <?php }
                                    if ($wechat) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>WeChat:</span>
                                            <?php echo $wechat; ?>
                                        </p>
                                    <?php }
                                    if ($ln) { ?>
                                        <p class="col-sm-6 col-xs-12"><span>Linkedin:</span>
                                            <a href="<?php echo $ln; ?>" target="_blank" rel="nofollow">
                                                <?php echo $ln; ?>
                                            </a>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                            <!--soc inf + email verf -->

                        </div>

                        <!--edit-info--modal-->
                        <div class="modal fade" id="editprofile" role="dialog" aria-labelledby="myModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <?php _e('My settings', ET_DOMAIN) ?>
                                        <button type="button" class="close" data-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="profile-employer-info-edit cnt-profile-hide" id="ctn-edit-profile">
                                            <div class="fre-employer-info-form" id="accordion" role="tablist"
                                                 aria-multiselectable="true">
                                                <form id="profile_form" class="row form-detail-profile-page" action=""
                                                      method="post" novalidate>
                                                    <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 employer-info-avatar avatar-profile-page">
                                                        <span class="employer-avatar img-avatar image"><?php echo get_avatar($user_ID, 125) ?></span>
                                                        <a href="#" id="user_avatar_browse_button">
                                                            <?php _e('Change Photo', ET_DOMAIN) ?>
                                                        </a>
                                                    </div>
                                                    <div class=" <?php if (fre_share_role() || $user_role == FREELANCER) { ?> col-md-8 col-lg-6 <? } else { ?> col-md-10 col-lg-10 <?php } ?> col-sm-12 col-xs-12 fre-input-field">
                                                        <label class="fre-field-title">About me</label>
                                                        <?php 
                                                        	
                                                        wp_editor($about, 'post_content', ae_editor_settings()); 
                                                        ?>
                                                    </div>
                                                    <?php if (fre_share_role() || $user_role == FREELANCER) { ?>
                                                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-hourly-field">
                                                            <label class="fre-field-title ratelbl"><?php _e('Rate', ET_DOMAIN) ?></label>
                                                            <input type="number" <?php if ($hour_rate) {
                                                                echo "value= $hour_rate ";
                                                            } ?> name="hour_rate" id="hour_rate" step="5" min="0"
                                                                   placeholder="<?php _e('Your rate', ET_DOMAIN) ?>">
                                                        </div>
                                                    <?php } ?>
                                                    <div class="clearfix"></div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                        <label class="fre-field-title"><?php _e('Name', ET_DOMAIN); ?></label>
                                                        <input type="text" value="<?php echo $display_name ?>"
                                                               name="display_name" id="display_name"
                                                               placeholder="<?php _e('Your name', ET_DOMAIN) ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                        <label class="fre-field-title"><?php _e('Email', ET_DOMAIN); ?>
                                                            <span><?php if (!empty($user_confirm_email)) {
                                                                    _e('(Confirmed email address)', ET_DOMAIN);
                                                                } ?></span></label>
                                                        <input type="text" value="<?php echo $user_data->user_email ?>"
                                                               name="user_email" id="user_email"
                                                               placeholder="<?php _e('Your email', ET_DOMAIN) ?>">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                        <label class="fre-field-title"><?php _e('Phone', ET_DOMAIN); ?>
                                                            <span><?php if ($user_phone) {
                                                                    _e('(Confirmed by sms)', ET_DOMAIN);
                                                                } ?></span></label>
                                                        <a href="#modal_change_phone" data-toggle="modal"
                                                           data-dismiss="modal" class="change-phone"
                                                           data-ctn_edit="ctn-edit-account" id="btn_edit">
                                                            <?php echo !empty($user_phone_code . $user_phone) ? $user_phone_code . $user_phone : _e('Edit phone', ET_DOMAIN); ?>
                                                        </a>
                                                    </div>

                                                    <!--new start-->
                                                    <?php include_once 'inc/select-location-profile-edit.php'; ?>
                                                    <!--new end-->

                                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field default-currency-wrap">
                                                        <label class="fre-field-title">
                                                            <?php _e('Currency', ET_DOMAIN); ?>
                                                        </label>

                                                        <select name="project_currency">
                                                            <?php
                                                                $selected_currency = get_user_meta($user_ID, 'currency', true);

                                                                foreach (get_currency() as $key => $data){
                                                                    $is_selected = '';
                                                                    $user_country = get_user_country();
                                                                    $user_country = $user_country['name'];

                                                                    if (empty($selected_currency)){
                                                                        if ($user_country == $data['country']){
                                                                            $is_selected = 'selected';
                                                                        }
                                                                    } else {
                                                                        if ($selected_currency == $data['code']){
                                                                            $is_selected = 'selected';
                                                                        }
                                                                    } ?>
                                                                    <option data-icon="<?=$data['flag']?>" <?=$is_selected?>>
                                                                        <?=$data['code']?>
                                                                    </option>
                                                                <? }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <?php if (fre_share_role() || $user_role == FREELANCER) { ?>
                                                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field fre-experience-field">
                                                            <label class="fre-field-title"><?php _e('Years experience', ET_DOMAIN); ?></label>
                                                            <input type="number" value="<?php echo $experience; ?>"
                                                                   name="et_experience" id="et_experience" min="0"
                                                                   placeholder="<?php _e('Total', ET_DOMAIN) ?>">
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                        <label class="fre-field-title"><?php _e('Password', ET_DOMAIN); ?></label>
                                                        <a href="#" class="change-password">
                                                            <?php _e('******', ET_DOMAIN); ?>
                                                        </a>

                                                        <?php if (function_exists('fre_credit_add_request_secure_code')) {
                                                            $fre_credit_secure_code = ae_get_option('fre_credit_secure_code');
                                                            if (!empty($fre_credit_secure_code)) {
                                                                ?>
                                                                <ul class="fre-secure-code">
                                                                    <li>
                                                                        <span><?php _e("Secure code", ET_DOMAIN) ?></span>
                                                                    </li>
                                                                    <?php do_action('fre-profile-after-list-setting'); ?>
                                                                </ul>
                                                            <?php }
                                                        } ?>
                                                    </div>
                                                    <?php if (use_paypal_to_escrow()) { ?>
                                                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                            <label class="fre-field-title"><?php _e('Paypal account', ET_DOMAIN) ?></label>
                                                            <input type="text" value="<?php echo $user_data->paypal ?>"
                                                                   name="user_paypal" id="user_paypal"
                                                                   placeholder="<?php _e('Your paypal login', ET_DOMAIN) ?>">
                                                        </div>
                                                    <?php } ?>
                                                    <?php do_action('ae_edit_post_form', PROFILE, $profile); ?>
                                                    <?php if ($visualFlag) { ?>
                                                        <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12 fre-input-field">
                                                            <label class="fre-field-title"><?php _e('Choose your level', ET_DOMAIN); ?></label>
                                                            <div class="fre-radio-container">
                                                                <label class="fre-radio" for="flag_no">
                                                                    <input id="flag_no" type="radio"
                                                                           name="visual_flag"
                                                                           value="0" <?php checked($visualFlagNumber, ''); ?>><span></span>
                                                                    <?php _e('No', ET_DOMAIN) ?>
                                                                </label>
                                                                <label class="fre-radio" for="flag_master">
                                                                    <input id="flag_master" type="radio"
                                                                           name="visual_flag"
                                                                           value="1" <?php checked($visualFlagNumber, 1); ?>><span></span>
                                                                    <?php _e('Master', ET_DOMAIN) ?>
                                                                </label>
                                                                <label class="fre-radio" for="flag_creator">
                                                                    <input id="flag_creator" type="radio"
                                                                           name="visual_flag"
                                                                           value="2" <?php checked($visualFlagNumber, 2); ?> ><span></span>
                                                                    <?php _e('Creator', ET_DOMAIN) ?>
                                                                </label>
                                                                <label class="fre-radio" for="flag_expert">
                                                                    <input id="flag_expert" type="radio"
                                                                           name="visual_flag"
                                                                           value="3" <?php checked($visualFlagNumber, 3); ?> ><span></span>
                                                                    <?php _e('Expert', ET_DOMAIN) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <? if ($user_role == FREELANCER) { ?>
                                                        <div class="col-sm-12 col-xs-12 fre-input-field">
                                                            <?php $email_skill = isset($profile->email_skill) ? (int)$profile->email_skill : 1; ?>
                                                            <label class="checkline" for="email-skill">
                                                                <input id="email-skill" type="checkbox"
                                                                       name="email_skill"
                                                                       value="1" <?php checked($email_skill, 1); ?> >
                                                                <span class="<?=($email_skill? 'active' : '')?>"><?php _e('Email me jobs that are relevant to my skills', ET_DOMAIN) ?></span>
                                                            </label>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-sm-12 col-xs-12 fre-input-field">
                                                        <?php $installmentPlan = isset($profile->installmentPlan) ? (int)$profile->installmentPlan : 1; ?>
                                                        <label class="checkline" for="installmentPlan">
                                                            <input id="installmentPlan" type="checkbox"
                                                                   name="installmentPlan"
                                                                   value="1" <?php checked($installmentPlan, 1); ?> >
                                                            <span class="<?=($installmentPlan? 'active' : '')?>"><?php _e('Trusted Partner program participation', ET_DOMAIN) ?></span>
                                                        </label>
                                                    </div>
                                                    <?php if ($personal_cover) { ?>
                                                        <div class="col-sm-12 col-xs-12 text-center">
                                                            <label class="fre-field-title"><?php _e('Personal cover', ET_DOMAIN); ?>
                                                                <span><? _e('(Max upload file size 2MB, allowed file types png, jpg)', ET_DOMAIN); ?></span>
                                                            </label>
                                                            <div class="box_upload_img">
                                                                <ul id="listImgPreviews"
                                                                    class="portfolio-thumbs-list row image">
                                                                    <?
                                                                    $attachment = $wpdb->get_row("SELECT ID, guid FROM {$wpdb->prefix}posts WHERE post_parent = {$profile->ID} AND post_type='attachment'");
                                                                    if (!empty($attachment)) {
                                                                        ?>
                                                                        <li class="col-sm-3 col-xs-12 item"
                                                                            data-id="<?= $attachment->ID; ?>">
                                                                            <div class="portfolio-thumbs-wrap">
                                                                                <div class="portfolio-thumbs img-wrap">
                                                                                    <img src="<?= $attachment->guid; ?>">
                                                                                </div>
                                                                                <div class="portfolio-thumbs-action delete-file">
                                                                                    <i class="fa fa-trash-o"></i>Remove
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    <? } ?>
                                                                </ul>
                                                                <div class="upfiles-container">
                                                                    <div class="fre-upload-file">
                                                                        Upload Files
                                                                        <input id="upfiles" type="file" multiple=""
                                                                               accept="image/jpeg,image/gif,image/png,application/pdf,application/doc,application/exel">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="itemPreviewTemplate" style="display: none;">
                                                            <li class="col-sm-3 col-xs-12 item">
                                                                <div class="portfolio-thumbs-wrap">
                                                                    <div class="portfolio-thumbs img-wrap">
                                                                        <div class="portfolio-thumbs_file-name"></div>
                                                                        <img src="">
                                                                    </div>
                                                                    <div class="portfolio-thumbs-action delete-file">
                                                                        <i class="fa fa-trash-o"></i>Remove
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </div>
                                                        <?php wp_enqueue_script('ad-freelancer', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true); ?>
                                                    <?php } ?>

                                                    <div class="col-sm-12 col-xs-12 employer-info-save btn-update-profile">
                                                        <input type="submit" class="btn-left fre-submit-btn btn-submit"
                                                               value="<?php _e('Save', ET_DOMAIN) ?>">
                                                        <span class="employer-info-cancel-btn fre-cancel-btn"
                                                              data-dismiss="modal"><?php _e('Cancel', ET_DOMAIN) ?></span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--edit--form-->
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                        </div>
                        <!--edit-info--modal-->
                    </div>
                    <!--second--info-->
                    <?php
                    if ($user_role == FREELANCER && !empty($profile_id)) { ?>
                        <div class="skills skills2 fre-profile-box">
                            <div class="row">
                                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                                    <div class="freelance-portfolio-title">
                                        <?php echo __("Specializations:", ET_DOMAIN); ?>
                                    </div>
                                    <div class="skill-list">
                                        <?php if (isset($profile->tax_input['project_category']) && $profile->tax_input['project_category']) {
                                            echo baskserg_profile_categories4($profile->tax_input['project_category']);
                                        } else {
                                            echo '<span>No Specializations</span>';
                                        } ?>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href='#editcategory' data-toggle="modal" class='fre-submit-btn btn-right'>
                                        <?php _e('Add category', ET_DOMAIN) ?>
                                    </a>
                                    <?php get_template_part('template-js/modal', 'profile-specialisation');?>
                                </div>
                            </div>
                        </div>

                        <?php get_template_part('list', 'portfolios');
                        get_template_part('list', 'documents');
                        wp_reset_query();
                        if (!$is_company) {
                            ?>
                            <div class="fre-profile-box">
                                <?php get_template_part('list', 'experiences'); ?>
                            </div>
                        <?php }
                        get_template_part('list', 'certifications');
                        if (!$is_company) { ?>
                            <div class="fre-profile-box">
                                <?php get_template_part('list', 'educations');
                                wp_reset_query(); ?>
                            </div>
                        <?php }
                    } ?>
                </div><!--tab-settings-->
            </div><!--tabs-->
        </div>
    </div>
</div>
</div>


<!-- CURRENT PROFILE -->
<?php if ($profile_id && $profile_post && !is_wp_error($profile_post)) { ?>
    <script type="data/json" id="current_profile">
        <?php echo json_encode($profile) ?>



    </script>
<?php } ?>
<!-- END / CURRENT PROFILE -->

<?php get_footer(); ?>
