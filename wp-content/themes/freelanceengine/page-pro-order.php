<?php
/**
 * Template Name: Member Pro-order Page
 */
global $user_ID;
$role_template = 'employer';
if (fre_share_role() || ae_user_role($user_ID) == FREELANCER) {
    $role_template = 'freelance';
}

$res = сheckout_order();
$path = plugins_url();

$pro_status_period = get_user_pro_status_duration($user_ID);
$pro_status = get_user_pro_status($user_ID);

$currency = ae_get_option('currency', array(
    'align' => 'left',
    'code' => 'USD',
    'icon' => '$'
));

#$urlRequest = ((int)ae_get_option('test_mode') == 1)? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
$urlRequest = '/order/payment/';

get_header();
?>
<div class="fre-page-wrapper list-profile-wrapper">
    <div class="fre-page-title hidden">
        <div class="container">
            <h1><?php _e('PRO order', ET_DOMAIN) ?></h1>
        </div>
    </div>

    <div class="fre-page-section">
        <div class="container">
            <div class="pro-order pro-<?php echo $role_template; ?>-wrap">
                <!-- отображение всей информации по стату -->
                <?php $count = count(reset($res['statuses'])); ?>
                <?php for ($i = 1; $i < $count; $i++) { ?>
                <div class="pro-order_status">
                    <div class="pro-order_t"><?php echo reset($res['statuses'])[$i] ?><?php _e(' plans', ET_DOMAIN) ?></div>
                    <ul class="nav nav-tabs hidden-sm">
                        <?php foreach ($res['statuses'] as $value) { ?>
                        <?php if (array_key_exists('property_type', $value) && $value['property_type'] == 2) {
                        $str = $value['option_value'] != 1 ? ' months' : ' month';?>
                        <li><a data-toggle="tab" href="#price<?php echo end($res['statuses'])[$i];?>-<?php echo $value['option_value'];?>"><?php echo $value['option_value'] . $str; ?></a></li>
                        <?php } 
                        }?>
                    </ul>
                    <div class="row tab-content">
                        <?php foreach ($res['statuses'] as $value) { ?>
                        <?php if (array_key_exists('property_type', $value) && $value['property_type'] == 2) { ?>
                        <div id="price<?php echo end($res['statuses'])[$i];?>-<?php echo $value['option_value'];?>" class="tab-pane fade col-sm-4 col-xs-12">
                            <div class="fre-profile-box">
                                <input type="hidden" class="radioStatus" name="radioStatus" value="<?= end($res['statuses'])[$i] ?>">
                                <input type="hidden" class="radioTime" name="radioTime" value="<?php echo $value['property_id'];?>">
                                <input type="hidden" class="radioStatusprice" name="price_<?= end($res['statuses'])[$i] ?>_<?= $value['property_id'] ?>" value="<?= $value[$i] ?>">
                                <?php $str = $value['option_value'] != 1 ? ' months' : ' month';?>

                                <input type="hidden" name="pro_plan_name" value="<?php echo reset($res['statuses'])[$i] ?> plan (<?php echo $value['option_value'] . $str; ?>)">

                                <div class="pro-order_subt"><?php echo $value['option_value'] . $str; ?></div>
                                <div class="pro-order_price"><?php echo $value[$i] . ' ' . $currency['icon']; ?></div>
                                <span>
                                    <?php if ($value['option_value'] == 3 ) { _e('Save 33%', ET_DOMAIN); }
                                          else if ($value['option_value'] == 6) { _e('Save 66%', ET_DOMAIN); }
                                          else if ($value['option_value'] == 12) { _e('Save 72%', ET_DOMAIN); }
                                          else { _e('Regular price', ET_DOMAIN); }?>
                                </span>
                                <?php if (($value['option_value'] == $pro_status_period)&&(end($res['statuses'])[$i] == $pro_status)) {
                                    echo '<div class="fre-submit-btn btn-center active-acc">Active</div>';
                                } else { ?>
                                    <button class="fre-submit-btn"><?php _e('Buy PRO account', ET_DOMAIN) ?></button>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="hidden-sm tab-arr">
                        <div class="owl-nav">
                            <div class="owl-prev"></div>
                            <div class="owl-next"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="pro-buttons hidden">
                    <form method="post" action="<?=$urlRequest;?>">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="business" value="<?=$businessAcc;?>">
                        <input type="hidden" name="item_name" value="Pay for Order">
                        <input type="hidden" name="item_number" value="<?= $user_ID ?>">
                        <input type="hidden" name="amount" value="">
                        <input type="hidden" name="no_shipping" value="1">
                        <input type="hidden" name="rm" value="2">
                        <!--URL, куда покупатель будет перенаправлен после успешной оплаты. Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="return" value="<?= bloginfo('home') ?>/payment-completed">
                        <!--URL, куда покупатель будет перенаправлен при отмене им оплаты . Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="cancel_return" value="<?= bloginfo('home') ?>/cancel-payment">
                        <!--URL, на который PayPal будет предавать информацию о транзакции (IPN). Если не передавать этот параметр, будет использоваться значение, указанное в настройках аккаунта. Если в настройках аккаунта это также не определено, IPN использоваться не будет-->
                        <input type="hidden" name="notify_url" value="<?php bloginfo('stylesheet_directory'); ?>/ipn.php">
                        <input type="hidden" name="custom" value="">
                        <input type="hidden" name="status" value="<?= $res['status'] ?>">
                        <input type="hidden" name="time" value="">
                        <input type="hidden" name="price" value="">
                        <input type="hidden" name="currency_code" value="<?=ae_currency_code()?>">
                        <input type="hidden" name="plan_name" value="">

                        <input type="submit" class="fre-normal-btn-o" value="Pay for Order">
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
    jQuery(function($) {
        $(document).ready(function() {
            /*tabs*/
            $('.tab-content .tab-pane:first-child').addClass('first in active');
            $('.nav-tabs li:first-child').addClass('first active');
            $('.tab-content .tab-pane:last-child').addClass('last');
            $('.nav-tabs li:last-child').addClass('last');
            
            $('.owl-next').click(function(){
                var navul = $(this).parent().parent().parent().children('.nav-tabs'),
                tabpan = $(this).parent().parent().parent().children('.tab-content');
                if ( navul.children('.active').hasClass('last') == false) {
                    navul.children('.active').removeClass('active')
                                             .next('li').addClass('active');
                }
                if ( tabpan.children('.active').hasClass('last') == false) {
                    tabpan.children('.active').removeClass('in active')
                                              .next('.tab-pane').addClass('in active');  
                }
             });
             $('.owl-prev').click(function(){
                var navul = $(this).parent().parent().parent().children('.nav-tabs'),
                tabpan = $(this).parent().parent().parent().children('.tab-content');
                if ( navul.children('.active').hasClass('first') == false) {
                    navul.children('.active').removeClass('active')
                                             .prev('li').addClass('active');
                }
                if ( tabpan.children('.active').hasClass('first') == false) {
                    tabpan.children('.active').removeClass('in active')
                                              .prev('.tab-pane').addClass('in active');  
                }
             });
            /*end tabs*/

            $('button.fre-submit-btn').click(function(){
                var status = $(this).parent().children('input[name=radioStatus]').val();
                var price = $(this).parent().children('input.radioStatusprice').val();
                var time = $(this).parent().children('input[name=radioTime]').val();
                var name = $(this).parent().children('input[name="pro_plan_name"]').val();

                document.querySelector('input[name="plan_name"]').value = name;
                document.querySelector('input[name="status"]').value = status;
                document.querySelector('input[name="time"]').value = time;
                document.querySelector('input[name="price"]').value = price;
                document.querySelector('input[name="amount"]').value = price;
                document.querySelector('input[name="custom"]').value = status + '_' + time;

                $('.pro-buttons input[type=submit]').trigger('click');         
            });


        });
    });

</script>
