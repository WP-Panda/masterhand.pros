<?php 
/** Fenom template 'viewReview.tpl' compiled at 2020-08-23 08:00:52 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php
/* viewReview.tpl:1: {include '_head.tpl'} */
 $tpl->getStorage()->getTemplate('_head.tpl')->display($var); ?>
<script>
    $(document).ready(function(){
    })
</script>

<div class="container-fluid">
    <div class="row">
        <a class="btn btn-primary btn-sm mx-1" href="<?php
/* viewReview.tpl:9: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>"><?php
/* viewReview.tpl:9: {$lang.back} */
 echo $var["lang"]["back"]; ?></a>
        <a class="btn btn-primary btn-sm mx-1" href="<?php
/* viewReview.tpl:10: {$.server.REQUEST_URI} */
 echo (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : null); ?>"><?php
/* viewReview.tpl:10: {$lang.refresh} */
 echo $var["lang"]["refresh"]; ?></a>
    </div>
    <div class="row w-100">
        <div class="col-sm-12 text-center">
            <h4><?php
/* viewReview.tpl:14: {$lang.reviewOnDoc} */
 echo $var["lang"]["reviewOnDoc"]; ?> <a class="badge badge-info" href="<?php
/* viewReview.tpl:14: {$doc.guid} */
 echo $var["doc"]["guid"]; ?>" target="_blank"><?php
/* viewReview.tpl:14: {$doc.post_title} */
 echo $var["doc"]["post_title"]; ?></a></h4>
        </div>
    </div>
    <form class="row form-edit-review w-100" method="POST">
        <input type="hidden" name="rwId" value="<?php
/* viewReview.tpl:18: {$review.id} */
 echo $var["review"]["id"]; ?>">
        <div class="col-sm-12">
            <div class="review-rating-summary">
            <span class="review-rating-empty"></span>
            <span class="review-rating-result" style="width: <?php
/* viewReview.tpl:22: {$review.vote * 20} */
 echo $var["review"]["vote"] * 20; ?>%"></span>
            </div>
        </div>
        <div class="col-sm-12 review-doc">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="inputGroupSelect"><?php
/* viewReview.tpl:28: {$lang.statusReview} */
 echo $var["lang"]["statusReview"]; ?></label>
                </div>
                <select id="inputGroupSelect" name="status" class="custom-select" style="height: auto">
                        <option value=""></option>
                    <?php  if(!empty($var["listStatuses"]) && (is_array($var["listStatuses"]) || $var["listStatuses"] instanceof \Traversable)) {
  foreach($var["listStatuses"] as $var["status"]) { ?>
                        <?php
/* viewReview.tpl:33: {if $status == $review.status} */
 if($var["status"] == $var["review"]["status"]) { ?>
                        <option value="<?php
/* viewReview.tpl:34: {$status} */
 echo $var["status"]; ?>" selected><?php
/* viewReview.tpl:34: {$lang[$status]} */
 echo $var["lang"][$var["status"]]; ?></option>
                        <?php
/* viewReview.tpl:35: {else} */
 } else { ?>
                        <option value="<?php
/* viewReview.tpl:36: {$status} */
 echo $var["status"]; ?>" ><?php
/* viewReview.tpl:36: {$lang[$status]} */
 echo $var["lang"][$var["status"]]; ?></option>
                        <?php
/* viewReview.tpl:37: {/if} */
 } ?>
                    <?php
/* viewReview.tpl:38: {/foreach} */
   } } ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 review-username">
            <label for="review_username" class="review-username-label"><?php
/* viewReview.tpl:43: {$lang.username} */
 echo $var["lang"]["username"]; ?></label>
            <input id="review_username" title="<?php
/* viewReview.tpl:44: {$lang.username} */
 echo $var["lang"]["username"]; ?>" class="form-control content-review-username"
                   type="text" name="username" value="<?php
/* viewReview.tpl:45: {$review.username} */
 echo $var["review"]["username"]; ?>" maxlength="100" disabled>
        </div>
        <div class="col-sm-12 review-title">
            <label for="review_title" class="review-title-label"><?php
/* viewReview.tpl:48: {$lang.title} */
 echo $var["lang"]["title"]; ?></label>
            <input id="review_title" title="<?php
/* viewReview.tpl:49: {$lang.title} */
 echo $var["lang"]["title"]; ?>" class="form-control content-review-title"
                   type="text" name="title" value="<?php
/* viewReview.tpl:50: {$review.title} */
 echo $var["review"]["title"]; ?>" maxlength="100" disabled>
        </div>
        <div class="col-sm-12 review-comment">
            <div class="review-comment-label"><?php
/* viewReview.tpl:53: {$lang.review} */
 echo $var["lang"]["review"]; ?></div>
            <textarea class="col-sm-12 form-control content-review-comment" name="comment"
                      placeholder="<?php
/* viewReview.tpl:55: {$lang.textReview} */
 echo $var["lang"]["textReview"]; ?>" title="<?php
/* viewReview.tpl:55: {$lang.textReview} */
 echo $var["lang"]["textReview"]; ?>" disabled><?php
/* viewReview.tpl:55: {$review.comment} */
 echo $var["review"]["comment"]; ?></textarea>
        </div>
        <div class="reviews-response-message"></div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-success btn-md m-3 edit-review"><?php
/* viewReview.tpl:59: {$lang.save} */
 echo $var["lang"]["save"]; ?></button>
            <a class="btn btn-primary btn-md m-3" href="<?php
/* viewReview.tpl:60: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>"><?php
/* viewReview.tpl:60: {$lang.cancel} */
 echo $var["lang"]["cancel"]; ?></a>
        </div>
    </form>
</div>
<?php
/* viewReview.tpl:64: {include '_footer.tpl'} */
 $tpl->getStorage()->getTemplate('_footer.tpl')->display($var); ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'viewReview.tpl',
	'base_name' => 'viewReview.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'viewReview.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
