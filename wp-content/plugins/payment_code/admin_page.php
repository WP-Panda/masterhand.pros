<?php
if(!defined('WP_ADMIN')){
    die('Bad Request!!!');
}
$payCode = \PaymentCode\Admin::getInstance();
$payCode->updateOptions();

?>
<div class="wrap">
    <h2><?php _e('Payment Code settings page');?></h2>
    <form method="POST" action="">
    <table>
        <tr>
            <td><?php _e('Subject Letter');?></td>
            <td><input type="text" name="subjectLetter" value="<?=$payCode::getOptSubjLetter()?>"></td>
        </tr>
<!--        <tr>-->
<!--            <td>--><?// _e('Message Letter');?><!--</td>-->
<!--            <td><textarea name="messageLetter">--><?//=$payCode::getOptMsgLetter()?><!--</textarea></td>-->
<!--        </tr>-->
        <tr>
            <td><?php _e('Notice SMS');?></td>
            <td>
                <?php $optNoticeSMS = $payCode::getOptNoticeSMS();?>
                <label for="NoticeSMSOn"><?php _e('On');?></label>
                <input type="radio" id="NoticeSMSOn" name="noticeSMS" value="1" <?=$optNoticeSMS? 'checked': '';?>>
                <label for="NoticeSMSOff"><?php _e('Off');?></label>
                <input type="radio" id="NoticeSMSOff" name="noticeSMS" value="0" <?=$optNoticeSMS? '': 'checked';?>>
            </td>
        </tr>
        <tr>
            <td><?php _e('Message SMS');?></td>
            <td><input type="text" name="messageSMS" value="<?=htmlentities($payCode::getOptMsgSMS())?>" maxlength="50"></td>
        </tr>
        <tr>
            <td><?php submit_button();?></td>
            <td>
                <label for="resetPayCode"><?php _e('Set of default');?></label>
                <input type="checkbox" id="resetPayCode" name="resetPayCode" value="1">
            </td>
        </tr>
    </table>
    </form>
</div>
<style>
    input[type=text]{
        width: 400px;
    }
    textarea{
        width: 400px;
        height: 150px;
    }
</style>
