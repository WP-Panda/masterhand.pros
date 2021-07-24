<?php 
/** Fenom template 'list_skill.tpl' compiled at 2020-05-15 12:08:15 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php  if(!empty($var["skills"]) && (is_array($var["skills"]) || $var["skills"] instanceof \Traversable)) {
  foreach($var["skills"] as $var["skill"]) { ?>
    <tr class="">
        <td><?php
/* list_skill.tpl:3: {$skill.id} */
 echo $var["skill"]["id"]; ?></td>
        <td><?php
/* list_skill.tpl:4: {$skill.group_skill} */
 echo $var["skill"]["group_skill"]; ?></td>
        <td><?php
/* list_skill.tpl:5: {$skill.title} */
 echo $var["skill"]["title"]; ?></td>
        <td><?php
/* list_skill.tpl:6: {$skill.used} */
 echo $var["skill"]["used"]; ?></td>
        
            
        
        <td class="text-center">
            <button class="btn btn-danger btn-sm" data-id="<?php
/* list_skill.tpl:11: {$skill.id} */
 echo $var["skill"]["id"]; ?>" data-name="<?php
/* list_skill.tpl:11: {$skill.title} */
 echo $var["skill"]["title"]; ?>" onclick="mod.delSkill(this)">
                <?php
/* list_skill.tpl:12: {$lang.delete} */
 echo $var["lang"]["delete"]; ?>
            </button>
        </td>
    </tr>
<?php
/* list_skill.tpl:16: {/foreach} */
   } } ?>
<?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'list_skill.tpl',
	'base_name' => 'list_skill.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'list_skill.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
