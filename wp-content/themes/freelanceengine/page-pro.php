<?php
	/**
	 * Template Name: Member Pro-status Page
	 */

	global $user_ID;
	global $post;
	the_post();

	$role_template = 'employer';
	if ( userRole( $user_ID ) == FREELANCER ) {
		$role_template = 'freelance';
	}

	$where_and = "AND p.property_display=1";// AND p.property_type<>3

	$result = table_properties( $role_template, $where_and );

	$allPrice = getAllPrice( $role_template );

	$base_status = get_base_status( $role_template );
	//var_dump($base_status);
	$pro_status        = get_user_pro_status( $user_ID );
	$pro_status_period = get_user_pro_status_duration( $user_ID );


	$escrow_paypal = ae_get_option( 'escrow_paypal' );
	$businessAcc   = $escrow_paypal[ 'business_mail' ];

	$currency = ae_get_option( 'currency', [
		'align' => 'left',
		'code'  => 'USD',
		'icon'  => '$'
	] );

	#$urlRequest = ((int)ae_get_option('test_mode') == 1) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
	$urlRequest = '/order/payment/';

	get_header();
?>

<div class="fre-page-wrapper page-pro">
    <div class="fre-page-title">
        <div class="container">
            <h1 class="page_t"><?php echo __( 'Plans & Benefits', ET_DOMAIN ) ?></h1>
        </div>
    </div>

    <div class="fre-page-section">
        <div class="container">


            <div class="page-pro_wp">
				<?php if ( empty( $profile_id ) && ( fre_share_role() || ae_user_role( $user_ID ) == FREELANCER ) ) { ?>
                    <div class="page-sub_t"><?php echo __( 'For professionals', ET_DOMAIN ) ?></div>
					<?php the_content(); ?>
				<?php } else { ?>
                    <div class="page-sub_t"><?php echo __( 'For clients', ET_DOMAIN ) ?></div>
					<?php the_field( 'text_for_clients' ); ?>
				<?php } ?>
            </div>


            <div class="pro-<?php echo $role_template; ?>-wrap">
                <div class="shop_wp">
                    <table class="text-center page-pro_table">
						<?php foreach ( $result as $item ) { ?>
							<?php if ( $item[ 0 ] == '' || $item[ 0 ] == 'id' || ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] != 3 ) ) { ?>
                                <tr class="<?php echo $item[ 'property_nickname' ];
									if ( $item[ 0 ] == 'Price' ) {
										echo ' hidden-xs price ';
									} ?>">
									<?php foreach ( $item as $key => $value ) {
										if ( $value == '' && $item[ 0 ] == '' ) {
											echo '<td>' . __( 'Options & Benefits', ET_DOMAIN ) . '</td>';
										} else if ( $role_template == 'employer' && $value == $result[ 0 ][ 1 ] ) {
											echo '<td>' . $value . '<p class="sub">Free</p></td>';
										} else if ( $role_template == 'employer' && $value == $result[ 0 ][ 2 ] ) {
											echo '<td>' . $value . '<a href="#prices">See prices below</a></td>';
										} else {
											if ( is_numeric( $key ) ) { ?>
                                                <td>
													<?php if ( $item[ 0 ] != 'id' ) {
														if ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 2 && is_numeric( $value ) ) {
															echo '<div class="months">' . implode( $allPrice[ 'time' ][ $key ], '/' ) . ' months' . '</div>';
															echo '<div>' . implode( $allPrice[ 'value' ][ $key ], '/' ) . ' ' . $currency[ 'icon' ] . '</div>';
														} else {
															switch ( $value ) {
																case '0':
																	echo '<img src="' . get_stylesheet_directory_uri() . '/img/fa-cancel.svg" alt=""/>';
																	break;
																case '1':
																	echo '<img src="' . get_stylesheet_directory_uri() . '/img/fa-ok.svg" alt=""/>';
																	break;
																case '-1':
																	echo '~';
																	break;
																default:
																	echo $value;
																	break;
															}
														}
													} else {
														if ( ae_user_role( $user_ID ) == FREELANCER ) {
															if ( $value != 'id' && ! empty( $base_status ) && $value != $base_status[ 'id' ] && $value != $pro_status ) { ?>
                                                                <div class="pro-buy hidden-xs text-center">
                                                                    <form action="/order" method="post">
                                                                        <!--<input type="submit" class="fre-normal-btn-o">-->
                                                                        <input type="submit"
                                                                               class="fre-normal-btn-o fre-submit-btn"
                                                                               value="BUY">
                                                                        <input name="status" type="hidden"
                                                                               value="<?php echo $value ?>">
                                                                        <input name="role" type="hidden"
                                                                               value="<?php echo $role_template ?>">
                                                                        <input name="userID" type="hidden"
                                                                               value="<?php echo $user_ID ?>">
                                                                    </form>
                                                                </div>
															<?php } elseif ( $value == $pro_status ) {
																echo '<div class="hidden-xs fre-submit-btn btn-center active-acc">Active</div>';
															}
														}
													} ?>
                                                </td>
											<?php }
										} ?>
									<?php } ?>
                                </tr>
							<?php } ?>
						<?php } ?>

                    </table>

					<?php if ( empty( $profile_id ) && ( fre_share_role() || ae_user_role( $user_ID ) == FREELANCER ) ) { ?>
                        <div class="hidden-sm page-pro_table_t"><?php echo __( 'Price', ET_DOMAIN ); ?></div>
                        <table class="hidden-sm text-center spec_style page-pro_table row">
							<?php foreach ( $result as $item ) { ?>
								<?php if ( $item[ 0 ] == '' || $item[ 0 ] == 'id' || ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 2 ) ) { ?>
                                    <tr class="col-xs-4">
										<?php foreach ( $item as $key => $value ) {
											if ( is_numeric( $key ) ) { ?>
                                                <td>
													<?php if ( $item[ 0 ] != 'id' ) {
														if ( $item[ 'property_type' ] != 2 ) {
															echo $value;
														} else if ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 2 && is_numeric( $value ) ) {
															echo '<div class="months">' . implode( $allPrice[ 'time' ][ $key ], '/' ) . ' months' . '</div>';
															echo '<div>' . implode( $allPrice[ 'value' ][ $key ], '/' ) . ' ' . $currency[ 'icon' ] . '</div>';
														} else {
															echo 'Always';
														}
													} else {
														if ( ae_user_role( $user_ID ) == FREELANCER ) {
															if ( $value != 'id' && ! empty( $base_status ) && $value != $base_status[ 'id' ] && $value != $pro_status ) { ?>
                                                                <div class="pro-buy text-center">
                                                                    <form action="/order" method="post">
                                                                        <input type="submit"
                                                                               class="fre-normal-btn-o fre-submit-btn"
                                                                               value="BUY">
                                                                        <input name="status" type="hidden"
                                                                               value="<?php echo $value ?>">
                                                                        <input name="role" type="hidden"
                                                                               value="<?php echo $role_template ?>">
                                                                        <input name="userID" type="hidden"
                                                                               value="<?php echo $user_ID ?>">
                                                                    </form>
                                                                </div>
															<?php } elseif ( $value == $pro_status ) {
																echo '<div class="fre-submit-btn btn-center active-acc">Active</div>';
															} else {
																echo 'Free';
															}
														}
													} ?>
                                                </td>
											<?php } ?>
										<?php } ?>
                                    </tr>
								<?php } ?>
							<?php } ?>
                        </table>
					<?php } ?>
                </div>

				<?php if ( $role_template == 'employer' ) {
					?>
                    <div class="page-pro_table_t"><?php echo __( 'Extra options', ET_DOMAIN ); ?></div>
                    <div class="shop_wp">
                        <table class="page-pro_table style2" width="100%" cellpadding="5" cellspacing="0">
							<?php foreach ( $result as $item ) { ?>
								<?php if ( $item[ 0 ] == '' || $item[ 0 ] == 'id' || ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 3 ) ) {
									if ( $item[ 0 ] != 'id' && $item[ 0 ] != '' ) { ?>
                                        <tr class="<?php echo $item[ 'property_nickname' ]; ?>">
											<?php foreach ( $item as $key => $value ) { ?>
												<?php if ( is_numeric( $key ) ) { ?>
                                                    <td> <?php if ( $item[ 'property_type' ] == 3 ) {
															if ( is_numeric( $value ) ) {
																echo $currency[ 'icon' ] . $value . ' for ' . preg_replace( '/plan/', '', strtolower( $result[ 0 ][ $key ] ) ) . ' account';
															} else {
																echo '<div>' . $value . '</div>';
															}
														} ?></td>
												<?php }
											} ?>
                                        </tr>
									<?php }
								} ?>
							<?php } ?>
                        </table>
                    </div>

                    <div id="prices" class="page-pro_prices pro-order">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 page-pro_order_t"><?php echo __( 'Prices for Pro account', ET_DOMAIN ) ?></div>
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">

                                <div class="pro-order_status">
									<?php foreach ( $result

										as $item ) { ?>
									<?php if ( $item[ 1 ] == '' || $item[ 1 ] == 'id' || ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 2 ) ) { ?>

									<?php foreach ( $item

										as $key => $value ) {
										if ( is_numeric( $key ) ) { ?>

                                    <td>
										<?php
											if ( array_key_exists( 'property_type', $item ) && $item[ 'property_type' ] == 2 && is_numeric( $value ) ) { ?>

                                                <ul class="nav nav-tabs hidden-sm">
													<?php
														$prices = array_slice( $allPrice[ 'value' ][ $key ], 0, 3 );

														$times = array_slice( $allPrice[ 'time' ][ $key ], 0, 3 );
														$props = $allPrice[ 'props' ];

														$timescount = count( $allPrice[ 'time' ][ $key ] );
														for ( $i = 0; $i < $timescount; $i ++ ) {
															echo '<li><a data-toggle="tab" href="#price-' . $key . '_' . $prices[ $i ] . '">' . $times[ $i ] . ' month';
															if ( $i > 0 ) {
																echo 's';
															}
															echo '</a></li>';
														} ?>
                                                </ul>
                                                <div class="row tab-content">
													<?php for ( $i = 0; $i < $timescount; $i ++ ) {
														$status_id = $result[ count( $result ) - 1 ][ $key ];
														?>
                                                        <div id="price_<?php echo $key . '_' . $prices[ $i ] ?>"
                                                             class="tab-pane fade col-sm-4 col-xs-12">
                                                            <div class="fre-profile-box">
                                                                <input type="hidden" class="radioStatus"
                                                                       name="radioStatus"
                                                                       value="<?php echo $status_id; ?>">
                                                                <input type="hidden" class="radioTime" name="radioTime"
                                                                       value="<?php echo $props[ $times[ $i ] ] ?>">
                                                                <input type="hidden" class="radioStatusprice"
                                                                       name="price_<?php echo $status_id ?>_<?= $prices[ $i ]; ?>"
                                                                       value="<?php echo $prices[ $i ] ?>">
																<?php $str = $times[ $i ] != 1 ? ' months' : ' month'; ?>
                                                                <input type="hidden" name="pro_plan_name"
                                                                       value="<?php echo $times[ $i ] . $str; ?>">

                                                                <div class="pro-order_subt"><?php echo $times[ $i ] . $str; ?></div>
                                                                <div class="pro-order_price"><?php echo $prices[ $i ] . ' ' . $currency[ 'icon' ]; ?></div>
                                                                <span>
                                                    <?php if ( $times[ $i ] == 3 ) {
	                                                    _e( 'Save 33%', ET_DOMAIN );
                                                    } else if ( $times[ $i ] == 6 ) {
	                                                    _e( 'Save 66%', ET_DOMAIN );
                                                    } else if ( $times[ $i ] == 12 ) {
	                                                    _e( 'Save 72%', ET_DOMAIN );
                                                    } else {
	                                                    _e( 'Regular price', ET_DOMAIN );
                                                    } ?>
                                                </span>
																<?php if ( ( $times[ $i ] == $pro_status_period ) && ( $status_id == $pro_status ) ) {
																	echo '<div class="fre-submit-btn btn-center active-acc">Active</div>';
																} else { ?>
                                                                    <button class="fre-submit-btn"><?php _e( 'Buy PRO account', ET_DOMAIN ) ?></button>
																<?php } ?>
                                                            </div>
                                                        </div>
													<?php } ?>
                                                </div>
                                                <div class="hidden-sm tab-arr">
                                                    <div class="owl-nav">
                                                        <div class="owl-prev"></div>
                                                        <div class="owl-next"></div>
                                                    </div>
                                                </div>
												<?php
											}
											}
											} ?>

										<?php } ?>
										<?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
				<?php } ?>


                <div class="page-pro_order">
					<?php if ( have_rows( 'order_steps' ) ) { ?>
                        <div class="page-pro_order_t"><?php echo __( 'How to order', ET_DOMAIN ) ?></div>
                        <div class="row">
							<?php $numb = 1;
								while ( have_rows( 'order_steps' ) ) : the_row(); ?>
                                    <div class="col-sm-4 col-xs-12">
                                        <div class="col-sm-6 col-xs-6"><i
                                                    class="page-pro_order_step"><?php echo $numb; ?></i><?php the_sub_field( 'step' ); ?>
                                        </div>
                                    </div>
									<?php $numb ++; endwhile; ?>
                        </div>
					<?php } ?>
                </div>

                <div class="pro-buttons hidden">
                    <form method="post" action="<?= $urlRequest; ?>">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="business" value="<?= $businessAcc; ?>">
                        <input type="hidden" name="item_name" value="Pay for Order">
                        <input type="hidden" name="item_number" value="<?= $user_ID ?>">
                        <input type="hidden" name="amount" value="">
                        <input type="hidden" name="no_shipping" value="1">
                        <input type="hidden" name="rm" value="2">
                        <!--URL, куда покупатель будет перенаправлен после успешной оплаты. Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="return" value="<?= bloginfo( 'home' ) ?>/payment-completed">
                        <!--URL, куда покупатель будет перенаправлен при отмене им оплаты . Если этот параметр не передать, покупатель останется на сайте PayPal-->
                        <input type="hidden" name="cancel_return" value="<?= bloginfo( 'home' ) ?>/cancel-payment">
                        <!--URL, на который PayPal будет предавать информацию о транзакции (IPN). Если не передавать этот параметр, будет использоваться значение, указанное в настройках аккаунта. Если в настройках аккаунта это также не определено, IPN использоваться не будет-->
                        <input type="hidden" name="notify_url"
                               value="<?php bloginfo( 'stylesheet_directory' ); ?>/ipn.php">
                        <input type="hidden" name="custom" value="">
                        <input name="status" type="hidden" value="<?= $res[ 'status' ] ?>">
                        <input name="time" type="hidden" value="">
                        <input name="price" type="hidden" value="">
                        <input name="currency_code" type="hidden" value="<?= fre_currency_sign() ?>">
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
    jQuery(function ($) {
        $(document).ready(function () {
            /*tabs*/
            $('.tab-content .tab-pane:first-child').addClass('first in active');
            $('.nav-tabs li:first-child').addClass('first active');
            $('.tab-content .tab-pane:last-child').addClass('last');
            $('.nav-tabs li:last-child').addClass('last');

            $('.owl-next').click(function () {
                var navul = $(this).parent().parent().parent().children('.nav-tabs'),
                    tabpan = $(this).parent().parent().parent().children('.tab-content');
                if (navul.children('.active').hasClass('last') == false) {
                    navul.children('.active').removeClass('active')
                        .next('li').addClass('active');
                }
                if (tabpan.children('.active').hasClass('last') == false) {
                    tabpan.children('.active').removeClass('in active')
                        .next('.tab-pane').addClass('in active');
                }
            });
            $('.owl-prev').click(function () {
                var navul = $(this).parent().parent().parent().children('.nav-tabs'),
                    tabpan = $(this).parent().parent().parent().children('.tab-content');
                if (navul.children('.active').hasClass('first') == false) {
                    navul.children('.active').removeClass('active')
                        .prev('li').addClass('active');
                }
                if (tabpan.children('.active').hasClass('first') == false) {
                    tabpan.children('.active').removeClass('in active')
                        .prev('.tab-pane').addClass('in active');
                }
            });
            /*end tabs*/

            $('button.fre-submit-btn').click(function () {
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