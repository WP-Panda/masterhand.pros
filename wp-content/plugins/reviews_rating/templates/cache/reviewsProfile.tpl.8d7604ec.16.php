<?php 
/** Fenom template 'reviewsProfile.tpl' compiled at 2020-09-27 05:37:34 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php  if(!empty($var["list_reviews"]) && (is_array($var["list_reviews"]) || $var["list_reviews"] instanceof \Traversable)) {
  foreach($var["list_reviews"] as $var["rwProject"]) { ?>

    <?php
/* reviewsProfile.tpl:3: {if $rwProject['its_reply'] != true} */
 if($var["rwProject"]['its_reply'] != true) { ?>
    <li>
        <?php
/* reviewsProfile.tpl:5: {$debug} */
 echo $var["debug"]; ?>
        <div class="fre-author-project-box row" data-id="<?php
/* reviewsProfile.tpl:6: {$rwProject['user_id']} */
 echo $var["rwProject"]['user_id']; ?>">
            <div class="col-sm-1 col-xs-3 avatar_wp"><?php
/* reviewsProfile.tpl:7: {$.call.get_avatar($rwProject['user_id'])} */
 echo call_user_func_array('get_avatar', array($var["rwProject"]['user_id'])); ?></div>
            <div class="col-sm-11 col-xs-9">
                <div class="col-sm-9 col-md-10 col-lg-10 col-xs-12 fre-author-project">
                    <span class="fre-author-project-box_t"><?php
/* reviewsProfile.tpl:10: {$rwProject['author_project']} */
 echo $var["rwProject"]['author_project']; ?></span>
                    <?php
/* reviewsProfile.tpl:11: {set $user_status = $.call.get_user_pro_status($rwProject['user_id'])} */
 $var["user_status"]=call_user_func_array('get_user_pro_status', array($var["rwProject"]['user_id'])); ?>
                     
                <?php
/* reviewsProfile.tpl:13: {if $user_status && $user_status != $.const.PRO_BASIC_STATUS_EMPLOYER && $user_status != $.const.PRO_BASIC_STATUS_FREELANCER} */
 if($var["user_status"] && $var["user_status"] != @constant('PRO_BASIC_STATUS_EMPLOYER') && $var["user_status"] != @constant('PRO_BASIC_STATUS_FREELANCER')) { ?>
                    <span class="status"><?php
/* reviewsProfile.tpl:14: {$.call.translate('PRO', $.const.ET_DOMAIN)} */
 echo call_user_func_array('translate', array('PRO', @constant('ET_DOMAIN'))); ?></span>
                <?php
/* reviewsProfile.tpl:15: {/if} */
 } ?>
                    <span class="hidden-xs rating-new">+<?php
/* reviewsProfile.tpl:16: {$.call.getActivityRatingUser($rwProject['user_id'])} */
 echo call_user_func_array('getActivityRatingUser', array($var["rwProject"]['user_id'])); ?></span>
                </div>
                <?php
/* reviewsProfile.tpl:18: {if $rwProject['vote'] > 3} */
 if($var["rwProject"]['vote'] > 3) { ?>
                <div class="free-rating">
                    <div class="reviews-rating-summary" title="">
                        <div class="review-rating-result" style="width: <?php
/* reviewsProfile.tpl:21: {$rwProject['vote']*20} */
 echo $var["rwProject"]['vote'] * 20; ?>%"></div>
                    </div>
                </div>
                <?php
/* reviewsProfile.tpl:24: {/if} */
 } ?>
                <span class="visible-xs col-xs-6 rating-new">+<?php
/* reviewsProfile.tpl:25: {$.call.getActivityRatingUser($rwProject['user_id'])} */
 echo call_user_func_array('getActivityRatingUser', array($var["rwProject"]['user_id'])); ?></span>
                <div class="col-sm-8 hidden-xs fre-project_lnk">
                    <span><?php
/* reviewsProfile.tpl:27: {$.call._e('Project:', $const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array('Project:', $var["const"]["ET_DOMAIN"])); ?></span>
                    <a href="<?php
/* reviewsProfile.tpl:28: {$rwProject['guid']} */
 echo $var["rwProject"]['guid']; ?>" title="<?php
/* reviewsProfile.tpl:28: {$.call.esc_attr($rwProject['post_title'])} */
 echo call_user_func_array('esc_attr', array($var["rwProject"]['post_title'])); ?>">
                        <?php
/* reviewsProfile.tpl:29: {$rwProject['post_title']} */
 echo $var["rwProject"]['post_title']; ?>
                    </a>
                </div>

                <span class="hidden-xs col-sm-4 posted text-right">
                    <?php
/* reviewsProfile.tpl:34: {$.call._e($.call.date('F d, Y', $.call.strtotime($rwProject['post_date'])), $.const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array(call_user_func_array('date', array('F d, Y', call_user_func_array('strtotime', array($var["rwProject"]['post_date'])))), @constant('ET_DOMAIN'))); ?>
                </span>
            </div>
            <div class="visible-xs col-xs-12 fre-project_lnk">
                <span><?php
/* reviewsProfile.tpl:38: {$.call._e('Project:', $.const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array('Project:', @constant('ET_DOMAIN'))); ?></span>
                <a href="<?php
/* reviewsProfile.tpl:39: {$rwProject['guid']} */
 echo $var["rwProject"]['guid']; ?>" title="<?php
/* reviewsProfile.tpl:39: {$.call.esc_attr($rwProject['post_title'])} */
 echo call_user_func_array('esc_attr', array($var["rwProject"]['post_title'])); ?>">
                    <?php
/* reviewsProfile.tpl:40: {$rwProject['post_title']} */
 echo $var["rwProject"]['post_title']; ?>
                </a>
            </div>
            <div class="col-sm-12 col-xs-12 author-project-comment">
                <div class="col-sm-8 col-md-9 col-lg-9 col-xs-12">
                    <?php
/* reviewsProfile.tpl:45: {$.call.string_is_nl2br($rwProject['comment'])} */
 echo call_user_func_array('string_is_nl2br', array($var["rwProject"]['comment'])); ?>
                    <div class="review_reply_comment">
                    <?php
/* reviewsProfile.tpl:47: {if $rwProject['reply_name'] != ''} */
 if($var["rwProject"]['reply_name'] != '') { ?>
                        <b><?php
/* reviewsProfile.tpl:48: {$rwProject['reply_name']} */
 echo $var["rwProject"]['reply_name']; ?>:</b>
                        <?php
/* reviewsProfile.tpl:49: {$rwProject['reply_comment']} */
 echo $var["rwProject"]['reply_comment']; ?>
                    <?php
/* reviewsProfile.tpl:50: {/if} */
 } ?>
                    </div>
                </div>
                <span class="visible-xs col-xs-12 posted text-right"><?php
/* reviewsProfile.tpl:53: {$.call._e($rwProject['post_date'], $.const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array($var["rwProject"]['post_date'], @constant('ET_DOMAIN'))); ?></span>

                <div class="col-sm-4 col-md-3 col-lg-3 col-xs-12">
                    <?php
/* reviewsProfile.tpl:56: {if $rwProject['status'] == 'hidden' && $user_ID == $rwProject['for_user_id']} */
 if($var["rwProject"]['status'] == 'hidden' && $var["user_ID"] == $var["rwProject"]['for_user_id']) { ?>
                        <span data-review_id="<?php
/* reviewsProfile.tpl:57: {$rwProject['id']} */
 echo $var["rwProject"]['id']; ?>" class="review-must-paid  btn-right fre-submit-btn">
                            <?php
/* reviewsProfile.tpl:58: {$.call._e('Add to Rating & Reviews', $.const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array('Add to Rating & Reviews', @constant('ET_DOMAIN'))); ?>
                        </span>
                    <?php
/* reviewsProfile.tpl:60: {/if} */
 } ?>

                    <?php
/* reviewsProfile.tpl:62: {if $rwProject['status'] != 'hidden' && $rwProject['reply_to_review'] != '' && $user_ID == $rwProject['for_user_id']} */
 if($var["rwProject"]['status'] != 'hidden' && $var["rwProject"]['reply_to_review'] != '' && $var["user_ID"] == $var["rwProject"]['for_user_id']) { ?>
                        <a href='#' data-review_id='<?php
/* reviewsProfile.tpl:63: {$rwProject['reply_to_review']} */
 echo $var["rwProject"]['reply_to_review']; ?>' data-project-id="<?php
/* reviewsProfile.tpl:63: {$rwProject['ID']} */
 echo $var["rwProject"]['ID']; ?>" id='<?php
/* reviewsProfile.tpl:63: {$rwProject['reply_to_review']} */
 echo $var["rwProject"]['reply_to_review']; ?>' class='fre-submit-btn btn-left project-employer__reply project-employer_reply_history main_bl-btn'><?php
/* reviewsProfile.tpl:63: {$.call._e('Reply to review', $.const.ET_DOMAIN)} */
 echo call_user_func_array('_e', array('Reply to review', @constant('ET_DOMAIN'))); ?></a>
                    <?php
/* reviewsProfile.tpl:64: {/if} */
 } ?>
                </div>
            </div>
        </div>
    </li>
    <?php
/* reviewsProfile.tpl:69: {/if} */
 } ?>
 
<?php
/* reviewsProfile.tpl:71: {/foreach} */
   } } ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'reviewsProfile.tpl',
	'base_name' => 'reviewsProfile.tpl',
	'time' => 1601185052,
	'depends' => array (
  0 => 
  array (
    'reviewsProfile.tpl' => 1601185052,
  ),
),
	'macros' => array(),

        ));
