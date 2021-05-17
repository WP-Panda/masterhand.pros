<?php 
/** Fenom template 'main.tpl' compiled at 2020-05-15 12:08:15 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><?php
/* main.tpl:1: {include '_head.tpl'} */
 $tpl->getStorage()->getTemplate('_head.tpl')->display($var); ?>
<a class="btn-refresh btn btn-outline-info btn-sm m-2" href="<?php
/* main.tpl:2: {$MODULE_URL} */
 echo $var["MODULE_URL"]; ?>">
    <i class="fas fa-sync-alt"> <?php
/* main.tpl:3: {$lang.refresh} */
 echo $var["lang"]["refresh"]; ?></i>
</a>
<div class="tab-content" id="tabs">

    <div class="row">
        
            
            
                
                    
                    
                
                
            
        
        <div class="input-group col-md-4 col-md-offset-4">
            <input class="form-control" type="text" id="search_field" placeholder="<?php
/* main.tpl:19: {$lang.search} */
 echo $var["lang"]["search"]; ?>...">
            <div class="input-group-append">
                <button class="btn btn-info run-search">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-secondary reset-search">
                    <?php
/* main.tpl:25: {$lang.reset} */
 echo $var["lang"]["reset"]; ?>
                </button>
            </div>
        </div>
    </div>
    <table class="table table-hover skill-table">
        <thead class="font-weight-bold">
            <td><?php
/* main.tpl:32: {$lang.id} */
 echo $var["lang"]["id"]; ?></td>
            <td><?php
/* main.tpl:33: {$lang.group} */
 echo $var["lang"]["group"]; ?></td>
            <td><?php
/* main.tpl:34: {$lang.name} */
 echo $var["lang"]["name"]; ?></td>
            <td><?php
/* main.tpl:35: {$lang.countEndorsed} */
 echo $var["lang"]["countEndorsed"]; ?></td>
            <td class="text-center"><?php
/* main.tpl:36: {$lang.action} */
 echo $var["lang"]["action"]; ?></td>
        </thead>
        <tbody id="list_skill">
        <?php
/* main.tpl:39: {include 'list_skill.tpl'} */
 $tpl->getStorage()->getTemplate('list_skill.tpl')->display($var); ?>
        </tbody>
        <tfoot class="pagination-skills">
        <tr>
            <td class="pagination-skills" colspan="5">
            <?php
/* main.tpl:44: {$pagination} */
 echo $var["pagination"]; ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<?php
/* main.tpl:51: {include '_footer.tpl'} */
 $tpl->getStorage()->getTemplate('_footer.tpl')->display($var); ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'main.tpl',
	'base_name' => 'main.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'main.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
