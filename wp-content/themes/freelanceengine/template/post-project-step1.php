<!-- Step 1 -->
<?php
$pack_id  = isset( $_GET['pack_id'] ) ? $_GET['pack_id'] : 0;
$selected = 0;
global $packs, $user_ID;
//$ae_pack = $ae_post_factory->get('pack');
//$packs = $ae_pack->fetch('pack');
//
$package_data = AE_Package::get_package_data( $user_ID );
//
$orders = AE_Payment::get_current_order( $user_ID );

$number_free_plan_used = AE_Package::get_used_free_plan( $user_ID );
?>

<div id="fre-post-project-1" class="fre-post-project-step step-plan active">
    <div class="page-post-project-subt">
		<?php _e( 'Choose your most appropriate package', ET_DOMAIN ) ?>
    </div>
    <div class="step-post-package">
        <ul class="row fre-post-package">
			<?php foreach ( $packs as $key => $package ) {
				$number_of_post = $package->et_number_posts;
				$sku            = $package->sku;
				$text           = '';
				$texthidden     = '';
				$order          = false;
				if ( $number_of_post >= 1 ) {
					// get package current order

					if ( isset( $orders[ $sku ] ) ) {
						$order = get_post( $orders[ $sku ] );
					}

					if ( isset( $package_data[ $sku ] ) && isset( $order->post_status ) && $order->post_status != 'draft' ) {
						$package_data_sku = $package_data[ $sku ];
						if ( isset( $package_data_sku['qty'] ) && $package_data_sku['qty'] > 0 ) {
							/**
							 * print text when company has job left in package
							 */
							$number_of_post = $package_data_sku['qty'];
						}
					}

					if ( ! $package->et_price ) { // if free package.

						//number_free_plan_used == number posted free
						$number_of_post = (int) $number_of_post - (int) $number_free_plan_used;


						if ( $number_of_post < 0 ) {
							$number_of_post = 0;
						}
					}

					if ( $package->et_price ) {
						$price = fre_price_format( $package->et_price );
					} else {
						$price = __( "Free", ET_DOMAIN );
					}
					$disabled = '';

					if ( $package->et_duration > 0 ) {
						if ( $package->et_duration == 1 ) {
							$duration_text = sprintf( __( '%s day', ET_DOMAIN ), $package->et_duration );
						} else {
							$duration_text = sprintf( __( '%s days', ET_DOMAIN ), $package->et_duration );
						}
					} else {
						$duration_text = sprintf( __( '%s days', ET_DOMAIN ), $package->et_duration );
					}
					if ( $number_of_post > 0 ) {
						if ( $number_of_post == 1 ) {
							$text       = sprintf( __( "%s for %s project", ET_DOMAIN ), $price, $number_of_post );
							$texthidden = $number_of_post;
						} else {
							$text       = sprintf( __( "%s for %s projects", ET_DOMAIN ), $price, $number_of_post );
							$texthidden = $number_of_post;
						}
					} else {

						$disabled   = 'disabled';
						$text       = sprintf( __( "%s for %s projects", ET_DOMAIN ), $price, $number_of_post );
						$texthidden = $number_of_post;
					}
				}
				$class_select = '';
				$checked      = false;

				if ( $pack_id ) {

					if ( $package->ID == $pack_id && $package->et_price > 0 ) {
						$checked      = true;
						$class_select = 'auto-select';
					}
				} else {

					if ( isset( $package_data[ $sku ] ) ) {
						$package_data_sku = $package_data[ $sku ];
						if ( $package->et_price > 0 && isset( $package_data_sku['qty'] ) && $package_data_sku['qty'] > 0 ) {
							$order = get_post( $orders[ $sku ] );
							if ( $order && ! is_wp_error( $order ) && $order->post_status != 'draft' ) {
								// auto select package is available to post.
								$class_select = ' auto-select ' . $order->post_status;
								$checked      = true;
							}
						}
					}
				}


				?>
                <li class="col-sm-4 col-xs-12 <?php echo $class_select; ?> <?php echo $disabled; ?>"
                    data-sku="<?php echo trim( $package->sku ); ?>" data-id="<?php echo $package->ID ?>"
                    data-price="<?php echo $package->et_price; ?>"
                    data-package-type="<?php echo $package->post_type; ?>"
                    data-title="<?php echo $package->post_title; ?>" data-description="<? #php echo $text; ?>">
                    <div class="fre-post-package_wp">
                        <h6 class="fre-post-package_t">
							<?php echo $package->post_title; ?>
                        </h6>
                        <div class="fre-post-package_txt">
                            <strong><?php echo $text; ?></strong>
							<?php echo wp_strip_all_tags( $package->post_content ); ?>
                        </div>
                        <span class="desc-hidden hidden"><?php echo $texthidden; ?></span>
                        <div class="fre-submit-btn chose-plan">
                            <input id="package-<?php echo $package->ID ?>" name="post-package"
                                   type="radio" <?php echo $disabled;
							echo ( $checked ) ? "checked='checked'" : ''; ?>>
							<?php _e( 'Choose a plan', ET_DOMAIN ); ?>
                        </div>
                    </div>
                </li>
			<?php } ?>
        </ul>
		<?php echo '<script type="data/json" id="package_plans">' . json_encode( $packs ) . '</script>'; ?>
        <div class="hidden">
            <input class="fre-btn fre-post-project-next-btn select-plan" type="button"
                   value="<?php _e( 'Choose a plan', ET_DOMAIN ); ?>">
        </div>
    </div>
</div>