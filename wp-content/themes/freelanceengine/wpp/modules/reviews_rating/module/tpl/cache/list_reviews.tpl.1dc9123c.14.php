<?php 
/** Fenom template 'list_reviews.tpl' compiled at 2020-08-14 05:32:39 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php  if(!empty($var["reviews"]) && (is_array($var["reviews"]) || $var["reviews"] instanceof \Traversable)) {
  foreach($var["reviews"] as $var["review"]) { ?>
    <tr class="row-review <?php
/* list_reviews.tpl:2: {if $review.status == 'pending'} */
 if($var["review"]["status"] == 'pending') { ?>table-warning<?php
/* list_reviews.tpl:2: {elseif $review.status == 'not_approved'} */
 } elseif($var["review"]["status"] == 'not_approved') { ?>table-secondary<?php
/* list_reviews.tpl:2: {/if} */
 } ?>">
        <td><?php
/* list_reviews.tpl:3: {$review.created} */
 echo $var["review"]["created"]; ?></td>
        <td><?php
/* list_reviews.tpl:4: {$review.author_review} */
 echo $var["review"]["author_review"]; ?> (<?php
/* list_reviews.tpl:4: {$review.username} */
 echo $var["review"]["username"]; ?>)</td>
        
        <td><?php
/* list_reviews.tpl:6: {$review.vote} */
 echo $var["review"]["vote"]; ?></td>
        <td>
            <?php
/* list_reviews.tpl:8: {$review.comment|truncate:50:"<a class='badge badge-secondary' href='{$MODULE_URL}&action=detailReview&rw={$review.id}'>{$lang.readMore}...</button>"} */
 echo Fenom\Modifier::truncate($var["review"]["comment"], 50, "<a class='badge badge-secondary' href='".($var["MODULE_URL"])."&action=detailReview&rw=".($var["review"]["id"])."'>".($var["lang"]["readMore"])."...</button>"); ?>
        </td>
        <td><?php
/* list_reviews.tpl:10: {$lang[$review.status]} */
 echo $var["lang"][$var["review"]["status"]]; ?></td>
        <td><a href="<?php
/* list_reviews.tpl:11: {$review.guid} */
 echo $var["review"]["guid"]; ?>" target="_blank"><?php
/* list_reviews.tpl:11: {$review.pagetitle} */
 echo $var["review"]["pagetitle"]; ?></a></td>
        <td></td>
        <td>
            <div class="btn-group" role="group">
                <a class="btn btn-primary btn-sm mx-1" href="<?php
/* list_reviews.tpl:15: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>&action=detailReview&rw=<?php
/* list_reviews.tpl:15: {$review.id} */
 echo $var["review"]["id"]; ?>"><?php
/* list_reviews.tpl:15: {$lang.detail} */
 echo $var["lang"]["detail"]; ?></a>
                <button class="btn btn-danger btn-sm mx-1" onclick="mod.delReview(<?php
/* list_reviews.tpl:16: {$review.id} */
 echo $var["review"]["id"]; ?>)"><?php
/* list_reviews.tpl:16: {$lang.delete} */
 echo $var["lang"]["delete"]; ?></button>
            </div>
        </td>
    </tr>
<?php
/* list_reviews.tpl:20: {/foreach} */
   } } ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'list_reviews.tpl',
	'base_name' => 'list_reviews.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'list_reviews.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
