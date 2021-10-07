<?php 
/** Fenom template 'config.tpl' compiled at 2020-08-14 05:32:39 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><div class="col-xs-12">
    <form class="col-xs-8 d-table" method="POST">
        
            
            
        
        <div class="d-table-row">
            <div class="d-table-cell"><?php
/* config.tpl:8: {$lang.confSendNotice} */
 echo $var["lang"]["confSendNotice"]; ?></div>
            <div class="d-table-cell p-3"><input type="checkbox" name="send_notice" value="1" <?php
/* config.tpl:9: {!$config.send_notice?: 'checked'} */
 echo (empty(!$var["config"]["send_notice"]) ? ('checked') : !$var["config"]["send_notice"]); ?>></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell"><?php
/* config.tpl:12: {$lang.confNewReviewPublish} */
 echo $var["lang"]["confNewReviewPublish"]; ?></div>
            <div class="d-table-cell p-3"><input type="checkbox" name="new_review_publish" value="1" <?php
/* config.tpl:13: {!$config.new_review_publish?: 'checked'} */
 echo (empty(!$var["config"]["new_review_publish"]) ? ('checked') : !$var["config"]["new_review_publish"]); ?>></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell"><?php
/* config.tpl:16: {$lang.confPageStep} */
 echo $var["lang"]["confPageStep"]; ?></div>
            <div class="d-table-cell"><input name="page_step" value="<?php
/* config.tpl:17: {$config.page_step} */
 echo $var["config"]["page_step"]; ?>" class=" form-control mx-3"></div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell"><?php
/* config.tpl:20: {$lang.confPercentPayReview} */
 echo $var["lang"]["confPercentPayReview"]; ?></div>
            <div class="d-table-cell"><input name="percent_pay_review" value="<?php
/* config.tpl:21: {$config.percent_pay_review} */
 echo $var["config"]["percent_pay_review"]; ?>" class=" form-control mx-3"></div>
        </div>
        <div class="d-table-row">
            <?php
/* config.tpl:24: {set $ae_currency = $.call.ae_get_option('currency')} */
 $var["ae_currency"]=call_user_func_array('ae_get_option', array('currency')); ?>
            <?php
/* config.tpl:25: {set $currency = $ae_currency.code} */
 $var["currency"]=$var["ae_currency"]["code"]; ?>
            <div class="d-table-cell"><?php
/* config.tpl:26: {$.call.sprintf($lang.confMinPayReview, $currency)} */
 echo call_user_func_array('sprintf', array($var["lang"]["confMinPayReview"], $var["currency"])); ?></div>
            <div class="d-table-cell"><input name="min_pay_review" value="<?php
/* config.tpl:27: {$config.min_pay_review} */
 echo $var["config"]["min_pay_review"]; ?>" class=" form-control mx-3"></div>
        </div>

        <div><button type="submit" class="btn btn-primary" onclick="mod.saveConf(this.form)"><?php
/* config.tpl:30: {$lang.save} */
 echo $var["lang"]["save"]; ?></button></div>
    </form>
</div>
    <?php
/* config.tpl:33: {if $UPDATE} */
 if($var["UPDATE"]) { ?>
        <form class="col-xs-12" action="" method="POST" style="margin:40px 0px 20px 0px">
            <input type="hidden" name="action" value="update">
            <button class="btn btn-warning btn-lg blink"><?php
/* config.tpl:36: {$lang.updateModule} */
 echo $var["lang"]["updateModule"]; ?></button>
        </form>
    <?php
/* config.tpl:38: {/if} */
 } ?>

    
        
            
            
        
    <?php
}, array(
	'options' => 128,
	'provider' => false,
	'name' => 'config.tpl',
	'base_name' => 'config.tpl',
	'time' => 1588253053,
	'depends' => array (
  0 => 
  array (
    'config.tpl' => 1588253053,
  ),
),
	'macros' => array(),

        ));
