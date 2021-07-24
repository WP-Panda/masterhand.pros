<?php 
/** Fenom template 'listSkills.tpl' compiled at 2020-04-30 16:04:07 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><ul>
<?php  if(!empty($var["items"]) && (is_array($var["items"]) || $var["items"] instanceof \Traversable)) {
  foreach($var["items"] as $var["skill"]) { ?>
<li class="item-list-skills">
    <?php
/* listSkills.tpl:4: {if $modeEndorse} */
 if($var["modeEndorse"]) { ?>
    <span class="item-endorse-skill mode-endorse <?php
/* listSkills.tpl:5: {if $skill.endorsed} */
 if($var["skill"]["endorsed"]) { ?>endorsed<?php
/* listSkills.tpl:5: {/if} */
 } ?>"
          data-uid="<?php
/* listSkills.tpl:6: {$userId} */
 echo $var["userId"]; ?>" data-skill="<?php
/* listSkills.tpl:6: {$skill.id} */
 echo $var["skill"]["id"]; ?>"
    ><?php
/* listSkills.tpl:7: {$skill.title} */
 echo $var["skill"]["title"]; ?></span> <span class="endorse-skill" title="counts of endorsement"><?php
/* listSkills.tpl:7: {$skill.endorse} */
 echo $var["skill"]["endorse"]; ?></span>
    <?php
/* listSkills.tpl:8: {else} */
 } else { ?>
    <span class="item-endorse-skill"><?php
/* listSkills.tpl:9: {$skill.title} */
 echo $var["skill"]["title"]; ?></span> <span class="endorse-skill" title="counts of endorsement"><?php
/* listSkills.tpl:9: {$skill.endorse} */
 echo $var["skill"]["endorse"]; ?></span>
    <?php
/* listSkills.tpl:10: {/if} */
 } ?>
</li>
<?php
/* listSkills.tpl:12: {/foreach} */
   } } ?>
</ul><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'listSkills.tpl',
	'base_name' => 'listSkills.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'listSkills.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
