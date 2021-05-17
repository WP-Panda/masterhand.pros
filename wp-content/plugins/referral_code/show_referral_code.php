<?php


?>
<link href="<?= $PATH_INC ?>/css/bootstrap.min.css" rel="stylesheet"/>
<div class="col-xs-12">
    <div class="text-info text-center"><?= $plugin_data['Name'] ?> v<?= $plugin_data['Version'] ?></div>
</div>
<div class="blockLoader">
    <div class="showLoad" onclick="mod.hideLoad()"></div>
</div>
<div class="body-alerts"></div>
<div class="tab-content">
    <div class="tab-pane active show" id="referrals">
        <table class="table table-hover accordion">
            <thead class="font-weight-bold">
            <tr data-tb="referrals"><!-- ratings-->
                <td class="col-sort" onclick="mod.setSort(this, 'user_login')"><i class="fas">User</i></td>
                <td class="col-sort"><i class="fas">Referral code</i></td>
                <td class="col-sort" onclick="mod.setSort(this, 'user_referral_login')"><i class="fas">Referral user</i>
                </td>
                <td class="col-sort" onclick="mod.setSort(this, 'count_referrals')"><i class="fas">Count referrals</i>
                </td>
                <td></td>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($referrals as $item) {
                include 'tpl/list.php';

             } ?>

            </tbody>

            <tfoot>
            <tr>
                <td class="pagination_referrals" colspan="6">
                    <?= $pagination ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    <?php include_once('js/referral_code.js') ?>
</script>
