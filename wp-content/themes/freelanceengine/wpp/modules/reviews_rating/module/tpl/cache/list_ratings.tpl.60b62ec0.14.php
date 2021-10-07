<?php 
/** Fenom template 'list_ratings.tpl' compiled at 2020-08-14 05:32:39 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php  if(!empty($var["ratings"]) && (is_array($var["ratings"]) || $var["ratings"] instanceof \Traversable)) {
  foreach($var["ratings"] as $var["rating"]) { ?>
	<tr class="">
		<td><?php
/* list_ratings.tpl:3: {$rating.user_id} */
 echo $var["rating"]["user_id"]; ?></td>
		<td><?php
/* list_ratings.tpl:4: {$rating.pagetitle} */
 echo $var["rating"]["pagetitle"]; ?></td>
		<td><?php
/* list_ratings.tpl:5: {$rating.votes} */
 echo $var["rating"]["votes"]; ?></td>
		<td><?php
/* list_ratings.tpl:6: {$rating.rating} */
 echo $var["rating"]["rating"]; ?></td>
		<td><?php
/* list_ratings.tpl:7: {$rating.countReviews} */
 echo $var["rating"]["countReviews"]; ?></td>
		<td><a class="btn btn-primary btn-sm" href="<?php
/* list_ratings.tpl:8: {$rating.guid} */
 echo $var["rating"]["guid"]; ?>" target="_blank"><?php
/* list_ratings.tpl:8: {$lang.viewProfile} */
 echo $var["lang"]["viewProfile"]; ?></a></td>
		<td><button class="btn btn-danger btn-sm" data-id="<?php
/* list_ratings.tpl:9: {$rating.doc_id} */
 echo $var["rating"]["doc_id"]; ?>" data-name="<?php
/* list_ratings.tpl:9: {$rating.pagetitle} */
 echo $var["rating"]["pagetitle"]; ?>"
					onclick="mod.resetRating(this)"><?php
/* list_ratings.tpl:10: {$lang.reset} */
 echo $var["lang"]["reset"]; ?></button>
		</td>
	</tr>
<?php
/* list_ratings.tpl:13: {/foreach} */
   } } ?>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'list_ratings.tpl',
	'base_name' => 'list_ratings.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'list_ratings.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
