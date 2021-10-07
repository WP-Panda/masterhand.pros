{foreach $list_reviews as $rwProject}

    {if $rwProject['its_reply'] != true}
    <li>
        {$debug}
        <div class="fre-author-project-box row" data-id="{$rwProject['user_id']}">
            <div class="col-sm-1 col-xs-3 avatar_wp">{$.call.get_avatar($rwProject['user_id'])}</div>
            <div class="col-sm-11 col-xs-9">
                <div class="col-sm-9 col-md-10 col-lg-10 col-xs-12 fre-author-project">
                    <span class="fre-author-project-box_t">{$rwProject['author_project']}</span>
                    {set $user_status = $.call.get_user_pro_status($rwProject['user_id'])}
                     
                {if $user_status && $user_status != $.const.PRO_BASIC_STATUS_EMPLOYER && $user_status != $.const.PRO_BASIC_STATUS_FREELANCER}
                    <span class="status">{$.call.translate('PRO', $.const.ET_DOMAIN)}</span>
                {/if}
                    <span class="hidden-xs rating-new">+{$.call.getActivityRatingUser($rwProject['user_id'])}</span>
                </div>
                {if $rwProject['vote'] > 3}
                <div class="free-rating">
                    <div class="reviews-rating-summary" title="">
                        <div class="review-rating-result" style="width: {$rwProject['vote']*20}%"></div>
                    </div>
                </div>
                {/if}
                <span class="visible-xs col-xs-6 rating-new">+{$.call.getActivityRatingUser($rwProject['user_id'])}</span>
                <div class="col-sm-8 hidden-xs fre-project_lnk">
                    <span>{$.call._e('Project:', $const.ET_DOMAIN)}</span>
                    <a href="{$rwProject['guid']}" title="{$.call.esc_attr($rwProject['post_title'])}">
                        {$rwProject['post_title']}
                    </a>
                </div>

                <span class="hidden-xs col-sm-4 posted text-right">
                    {$.call._e($.call.date('F d, Y', $.call.strtotime($rwProject['post_date'])), $.const.ET_DOMAIN)}
                </span>
            </div>
            <div class="visible-xs col-xs-12 fre-project_lnk">
                <span>{$.call._e('Project:', $.const.ET_DOMAIN)}</span>
                <a href="{$rwProject['guid']}" title="{$.call.esc_attr($rwProject['post_title'])}">
                    {$rwProject['post_title']}
                </a>
            </div>
            <div class="col-sm-12 col-xs-12 author-project-comment">
                <div class="col-sm-8 col-md-9 col-lg-9 col-xs-12">
                    {$.call.string_is_nl2br($rwProject['comment'])}
                    <div class="review_reply_comment">
                    {if $rwProject['reply_name'] != ''}
                        <b>{$rwProject['reply_name']}:</b>
                        {$rwProject['reply_comment']}
                    {/if}
                    </div>
                </div>
                <span class="visible-xs col-xs-12 posted text-right">{$.call._e($rwProject['post_date'], $.const.ET_DOMAIN)}</span>

                <div class="col-sm-4 col-md-3 col-lg-3 col-xs-12">
                    {if $rwProject['status'] == 'hidden' && $user_ID == $rwProject['for_user_id']}
                        <span data-review_id="{$rwProject['id']}" class="review-must-paid  btn-right fre-submit-btn">
                            {$.call._e('Add to Rating & Reviews', $.const.ET_DOMAIN)}
                        </span>
                    {/if}

                    {if $rwProject['status'] != 'hidden' && $rwProject['reply_to_review'] != '' && $user_ID == $rwProject['for_user_id']}
                        <a href='#' data-review_id='{$rwProject['reply_to_review']}' data-project-id="{$rwProject['ID']}" id='{$rwProject['reply_to_review']}' class='fre-submit-btn btn-left project-employer__reply project-employer_reply_history main_bl-btn'>{$.call._e('Reply to review', $.const.ET_DOMAIN)}</a>
                    {/if}
                </div>
            </div>
        </div>
    </li>
    {/if}
 
{/foreach}