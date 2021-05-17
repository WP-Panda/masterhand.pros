<?php 
/** Fenom template 'list_post.tpl' compiled at 2020-08-14 05:33:17 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php  if(!empty($var["posts"]) && (is_array($var["posts"]) || $var["posts"] instanceof \Traversable)) {
  foreach($var["posts"] as $var["item"]) { ?>
	<tr class="">
		<td><?php
/* list_post.tpl:3: {$item.ID} */
 echo $var["item"]["ID"]; ?></td>
		<td><?php
/* list_post.tpl:4: {$item.post_title} */
 echo $var["item"]["post_title"]; ?></td>
		<td><?php
/* list_post.tpl:5: {$item.likes_users} */
 echo $var["item"]["likes_users"]; ?></td>
		<td><a class="btn btn-primary btn-sm" href="<?php
/* list_post.tpl:6: {$item.guid} */
 echo $var["item"]["guid"]; ?>" target="_blank"><?php
/* list_post.tpl:6: {$lang.viewPost} */
 echo $var["lang"]["viewPost"]; ?></a></td>
		<td>
			
			
					
			
			<button class="btn btn-info btn-sm" data-id="<?php
/* list_post.tpl:12: {$item.ID} */
 echo $var["item"]["ID"]; ?>" data-name="<?php
/* list_post.tpl:12: {$item.post_title} */
 echo $var["item"]["post_title"]; ?>"
					onclick="mod.moreDetail(this)"><?php
/* list_post.tpl:13: {$lang.readMore} */
 echo $var["lang"]["readMore"]; ?></button>
		</td>
	</tr>
<?php
/* list_post.tpl:16: {/foreach} */
   } } ?>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'list_post.tpl',
	'base_name' => 'list_post.tpl',
	'time' => 1588253052,
	'depends' => array (
  0 => 
  array (
    'list_post.tpl' => 1588253052,
  ),
),
	'macros' => array(),

        ));
