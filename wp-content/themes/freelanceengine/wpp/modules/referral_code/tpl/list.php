<tr class="">
	<?php $str = $_SERVER[ "HTTPS" ] == 'on' ? 'https' : 'http' ?>
    <td width="30%">
        <a href="<?= $str . '://' . $_SERVER[ "SERVER_NAME" ] . '/author/' . $item[ 'user_login' ] ?>"><?= $item[ 'user_name' ] ?></a>
    </td>
    <td width="20%"><?= $item[ 'referral_code' ] ?></td>
    <td width="30%">
        <a href="<?= $str . '://' . $_SERVER[ "SERVER_NAME" ] . '/author/' . $item[ 'user_referral_login' ] ?>"><?= $item[ 'user_referral_name' ] ?></a>
    </td>
	<?php if ( $item[ 'count_referrals' ] != 0 ) { ?>
        <td width="20%"
            onclick="mod.show_user_referrals(this,<?= $item[ 'user_id' ] ?>)"><?= $item[ 'count_referrals' ] ?>
            <a style="color: #17a2b8;">Click for show more</a></td>
	<?php } else { ?>
        <td width="20%"><?= $item[ 'count_referrals' ] ?></td>
	<?php } ?>
</tr>