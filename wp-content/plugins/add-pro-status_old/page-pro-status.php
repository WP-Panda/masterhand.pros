<?php global $wpdb, $myplugin_prefs_table;


//foreach ($users_pro as $item) {
//    echo $item['post_title'] . ' - ' . $item['account_name'];
//}


?>
<div class="wrap">

    <h2>Users with PRO</h2>
    <table width='100%' cellpadding='5' cellspacing='0'>
        <?php foreach ($users_pro as $item) { ?>
                <tr>
                    <?php foreach ($item as $key=>$value) { ?>
                        <td>
                            <?php if(!empty($value) && ($key=='data_active' || $key=='data_deactivate'))
                                echo implode('-',unserialize($value));
                             else echo $value ?>
                        </td>
                    <?php } ?>
                </tr>
        <?php } ?>
    </table>
    <hr>

    <b>The status with a position in the list 1 is basic for users!</b><br>
    <?php if(!empty($base_status)) { ?>
        <span>Now the basic status is <b><?= $base_status['status_name'] ?></b></span>
    <?php } ?>
</div>

<?php
//Конец функции создания и обработки страницы настроек.
//}

?>
