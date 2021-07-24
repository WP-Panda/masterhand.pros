<?php 
/** Fenom template 'listSkillsInProject.tpl' compiled at 2020-04-30 22:00:32 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><ul>
<?php  if(!empty($var["items"]) && (is_array($var["items"]) || $var["items"] instanceof \Traversable)) {
  foreach($var["items"] as $var["skill"]) { ?>
<li class="item-list-skills">
    <span class="item-endorse-skill mode-endorse <?php
/* listSkillsInProject.tpl:4: {if $skill.endorsed} */
 if($var["skill"]["endorsed"]) { ?>endorsed<?php
/* listSkillsInProject.tpl:4: {/if} */
 } ?>"
          data-uid="<?php
/* listSkillsInProject.tpl:5: {$userId} */
 echo $var["userId"]; ?>" data-skill="<?php
/* listSkillsInProject.tpl:5: {$skill.id} */
 echo $var["skill"]["id"]; ?>"
    ><?php
/* listSkillsInProject.tpl:6: {$skill.title} */
 echo $var["skill"]["title"]; ?></span> <span class="endorse-skill" title="counts of endorsement"><?php
/* listSkillsInProject.tpl:6: {$skill.endorse} */
 echo $var["skill"]["endorse"]; ?></span>
</li>
<?php
/* listSkillsInProject.tpl:8: {/foreach} */
   } } ?>
</ul><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'listSkillsInProject.tpl',
	'base_name' => 'listSkillsInProject.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'listSkillsInProject.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
