<?php
// if edit bid event
if ( isset( $_REQUEST['event'] ) && $_REQUEST['event'] == 'edit_bid' ) {
	include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

	$is_edit_bid = true;

	$bid_id   = (int) $_REQUEST['id'];
	$bid_data = (array) get_post( $bid_id );

	foreach ( get_post_meta( $bid_id ) as $key => $value ) {
		$bid_data[ $key ] = $value[0];
	}

	$user_ID = $bid_data->post_author;
} else {
	$is_edit_bid = false;
	wp_reset_query();
	global $user_ID, $post;
	$payer_of_commission = ae_get_option( 'payer_of_commission' );
	$commission_type     = ae_get_option( 'commission_type' );
	$currency            = ae_get_option( 'currency', array( 'align' => 'left', 'code' => 'USD', 'icon' => '$' ) );
	$commission          = ae_get_option( 'commission', 0 );
	?>

    <div class="modal fade" id="modal_bid_forbidden">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="modal-title text-center"><?php _e( 'Bids limit per day have been reached. Please upgrade your account to PRO for unlimited bids.', ET_DOMAIN ); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL BIG -->
    <div class="modal fade" id="modal_bid">

<?php }

if ( $is_edit_bid ) {
	$currency_data = fre_currency_data( $bid_data['post_parent'] );
} else {
	$currency_data = fre_currency_data();
}

$currency_flag = $currency_data['flag'];
$currency_code = $currency_data['code']; ?>

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e( 'Bid this project', ET_DOMAIN ); ?></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="bid_form" class="bid-form fre-modal-form">
                    <div class="fre-input-field step-post-project">

                        <div class="bid-type__wrap">
                            <label class="bid-type__btn <?php echo ! $is_edit_bid || ( $is_edit_bid && $bid_data['bid_type'] == 'not_final' ) ? 'bid-type__btn--selected' : '' ?>">
								<?php _e( 'Preliminary quote', ET_DOMAIN ); ?>
                                <input type="radio" name="bid_type"
                                       value="not_final" <?php echo ! $is_edit_bid || ( $is_edit_bid && $bid_data['bid_type'] == 'not_final' ) ? 'checked' : '' ?>>
                            </label>

                            <span class="bid-type__or">or</span>

                            <label class="bid-type__btn <?php echo $is_edit_bid && $bid_data['bid_type'] == 'final' ? 'bid-type__btn--selected' : '' ?>">
								<?php _e( 'Final bid', ET_DOMAIN ); ?>
                                <input type="radio" name="bid_type"
                                       value="final" <?php echo $is_edit_bid && $bid_data['bid_type'] == 'final' ? 'checked' : '' ?>>
                            </label>
                        </div>

                        <label class="fre-field-title" for="bid_budget"><?php _e( 'Your Bid', ET_DOMAIN ); ?></label>
                        <div class="fre-project-budget">
                            <div class="bid-currency__wrap">
								<?php if ( $currency_flag != '' ) { ?>
                                    <img class="bid-currency__flag" src="<?php echo $currency_flag ?>">
								<?php } ?>

                                <p class="bid-currency__code"><?php echo $currency_code ?></p>
                            </div>

                            <input id="bid_budget" required type="number" placeholder="1"
                                   value="<?php echo $is_edit_bid ? $bid_data['bid_budget'] : '' ?>"
                                   class="input-item text-field is_number numberVal"
                                   pattern="-?(\d+|\d+.\d+|.\d+)([eE][-+]?\d+)?"
                                   onkeydown="if (event.keyCode == 16 || event.keyCode == 69 || event.keyCode == 189) return false"
                                   name="bid_budget" min="1">
                            <!--span><?php echo fre_currency_sign( false ); ?></span-->
                        </div>

						<?php if ( ae_get_option( 'use_escrow' ) ) {
							if ( $payer_of_commission == 'worker' ) {
								if ( $commission_type == 'percent' ) {
									$commission_fee = $commission . '%';
								} else {
									$commission_fee = $currency['icon'] . $commission;
								}
								printf( __( "<p class='bid-commission-fee'>Commission fee: <b>%s</b></p>", ET_DOMAIN ), $commission_fee );
							}
						} ?>
                    </div>

                    <div class="fre-input-field bid-time step-post-project">
                        <label class="fre-field-title" for="bid_time"><?php _e( 'Delivery', ET_DOMAIN ); ?></label>
                        <div class="row">
                            <div class="col-md-9 col-sm-8 col-xs-6 quantity">
                                <input id="bid_time" required type="number" placeholder="1"
                                       value="<?php echo $is_edit_bid ? $bid_data['bid_time'] : '' ?>"
                                       class="input-item text-field is_number numberVal"
                                       pattern="-?(\d+|\d+.\d+|.\d+)([eE][-+]?\d+)?"
                                       onkeydown="if (event.keyCode == 16 || event.keyCode == 69 || event.keyCode == 189) return false"
                                       name="bid_time" min="1">
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-6 no-padding-left quantity">
                                <select class="fre-chosen-single" name="type_time">
                                    <option value="day" <?php echo $is_edit_bid && $bid_data['type_time'] == 'day' ? 'selected' : '' ?>>
										<?php _e( 'days', ET_DOMAIN ); ?>
                                    </option>

                                    <option value="week" <?php echo $is_edit_bid && $bid_data['type_time'] == 'week' ? 'selected' : '' ?>>
										<?php _e( 'week', ET_DOMAIN ); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="fre-input-field">
                        <label class="fre-field-title" for="post_content"><?php _e( 'Add Notes', ET_DOMAIN ); ?></label>
                        <textarea id="bid_content"
                                  name="bid_content"><?php echo $is_edit_bid ? $bid_data['post_content'] : '' ?></textarea>
                    </div>

					<?php $statusUser = get_user_pro_status( $user_ID );
					if ( $statusUser && $statusUser != PRO_BASIC_STATUS_FREELANCER ) { ?>
                        <div class="fre-input-field">
                            <label class="fre-field-title" for="bid_background_color"><?php _e( 'Background color' ); ?>
                                :</label>
                            <input id="bid_background_color" type="text" name="bid_background_color"
                                   value="<?php echo $is_edit_bid ? $bid_data['bid_background_color'] : '' ?>"
                                   class=""
                                   onclick="setJsColorPicker(this)" readonly/>
                            <script>
                                var isPickerNotSetup = true;

                                function setJsColorPicker(el) {
                                    if (isPickerNotSetup) {
                                        var picker = new jscolor(el);
                                        picker.show();
                                        isPickerNotSetup = false;
                                    }
                                }
                            </script>
                        </div>
					<?php } ?>

					<?
					$max_bid = getValueByProperty( $statusUser, 'show_max_bid' );
					if ( $max_bid ) { ?>
                        <div class="fre-input-field box_upload_img">
                            <label class="fre-field-title"><?php _e( 'Work examples' ); ?></label>
                            <ul id="listImgPreviews" class="portfolio-thumbs-list row image">
                            </ul>
                            <div class="upfiles-container">
                                <div class="fre-upload-file">
                                    Upload Files
                                    <input id="upfiles" type="file" multiple="" accept="image/jpeg,image/gif,image/png">
                                </div>
                            </div>
                            <p class="fre-allow-upload">
								<?php _e( '(Maximum upload file size is limited to 2MB, maximum for 10 items, allowed file types in the png, jpg.)' ); ?>
                            </p>
                        </div>
                        <style>
                            .upfiles-container {
                                position: relative;
                                height: 50px;
                            }

                            .fre-upload-file {
                                position: absolute;
                                top: 0px;
                                left: 0px;
                                width: 100%;
                                height: 44px;
                                overflow: hidden;
                            }

                            #upfiles {
                                cursor: pointer;
                                display: block;
                                font-size: 999px;
                                opacity: 0;
                                position: absolute;
                                top: 0px;
                                left: 0px;
                                width: 100%;
                                height: 100%;
                            }

                            .fre-allow-upload {
                                text-align: center;
                            }

                            .delete-file {
                                cursor: pointer;
                            }
                        </style>
						<?php
					} ?>

					<?php if ( $is_edit_bid ) { ?>
                        <input type="hidden" name="post_parent" value="<?php echo $bid_data['post_parent'] ?>"/>
                        <input type="hidden" name="bid_id" value="<?php echo $bid_id ?>">
					<?php } else { ?>
                        <input type="hidden" name="post_parent" value="<?php the_ID() ?>"/>
					<?php } ?>

                    <input type="hidden" name="method" value="<?php echo $is_edit_bid ? 'update' : 'create' ?>"/>
                    <input type="hidden" name="action" value="ae-sync-bid"/>

					<?php do_action( 'after_bid_form' ); ?>
                    <div class="fre-form-btn">
                        <button type="submit" class="fre-submit-btn btn-left btn-submit">
							<?php if ( ! $is_edit_bid ) {
								_e( 'Submit', ET_DOMAIN );
							} else {
								_e( 'Update', ET_DOMAIN );
							} ?>
                        </button>
                        <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

<?php if ( ! $is_edit_bid ) { ?>
    </div><!-- /.modal -->


    <div id="itemPreviewTemplate" style="display: none;">
        <li class="col-sm-3 col-xs-12 item">
            <div class="portfolio-thumbs-wrap">
                <div class="portfolio-thumbs img-wrap">
                    <img src="">
                </div>
                <div class="portfolio-thumbs-action delete-file">
                    <i class="fa fa-trash-o"></i>Remove
                </div>
            </div>
        </li>
    </div>
	<?
	wp_enqueue_script( 'jscolor', '/wp-content/themes/freelanceengine/js/jscolor.min.js', [], false, true );
	wp_enqueue_script( 'ad-freelancer', '/wp-content/themes/freelanceengine/js/ad-freelancer.js', [], false, true );
}
?>