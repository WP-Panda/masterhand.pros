<?php 
/** Fenom template 'main.tpl' compiled at 2021-10-03 13:17:21 */
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
<form class="tab-content" onsubmit="return false" method="POST">
    <table class="table table-hover skill-table">
        <thead class="font-weight-bold">
        <td><?php
/* main.tpl:8: {$lang.name} */
 echo $var["lang"]["name"]; ?></td>
        <td colspan="2"><?php
/* main.tpl:9: {$lang.value} */
 echo $var["lang"]["value"]; ?></td>
        </thead>
        <tbody id="list_config">
        <tr class="text-center">
            <td></td>
            <td><?php
/* main.tpl:14: {$lang.freelancer} */
 echo $var["lang"]["freelancer"]; ?></td>
            <td><?php
/* main.tpl:15: {$lang.employer} */
 echo $var["lang"]["employer"]; ?></td>
        </tr>
        <?php
/* main.tpl:17: {include 'config.tpl'} */
 $tpl->getStorage()->getTemplate('config.tpl')->display($var); ?>
        </tbody>
    </table>
    <button class="btn btn-primary" type="submit" onclick="mod.saveConf(this.form)"><?php
/* main.tpl:20: {$lang.save} */
 echo $var["lang"]["save"]; ?></button>
</form>

<?php
/* main.tpl:23: {include '_footer.tpl'} */
 $tpl->getStorage()->getTemplate('_footer.tpl')->display($var); ?><?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'main.tpl',
	'base_name' => 'main.tpl',
	'time' => 1627304159,
	'depends' => array (
  0 => 
  array (
    'main.tpl' => 1627304159,
  ),
),
	'macros' => array(),

        ));
