<?php
global $user_ID, $ae_post_factory, $packs;
$step         = 3;
$class_active = '';

//$disable_plan = ae_get_option('disable_plan', false);
$disable_plan = true;

if ( $disable_plan ) {
	$step --;
	$class_active = 'active';
}
if ( $user_ID ) {
	$step --;
}
$post = '';

$package_data = AE_Package::get_package_data( $user_ID );

?>
<div id="fre-post-project-2" class="fre-post-project-step step-wrapper step-post <?php echo $class_active; ?>">
	<?php
	//    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
	//    if ($id) {
	//        $post = get_post($id);
	//        if ($post) {
	//            global $ae_post_factory, $option_for_project;
	//            $post_object = $ae_post_factory->get($post->post_type);
	//            $post_convert = $post_object->convert($post);
	//            echo '<script type="data/json"  id="edit_postdata">' . json_encode($post_convert) . '</script>';
	//
	//            $data_ex = get_post_meta($id, 'et_expired_date');
	//
	//            $max_days = (mktime(0, 0, 0, date('m', strtotime($data_ex[0])), date('d', strtotime($data_ex[0])), date('Y', strtotime($data_ex[0])))
	//                    - mktime(0, 0, 0, date("m"), date("d"), date("Y"))) / 86400;
	//
	//            $ae_pack = $ae_post_factory->get('pack');
	//            $packs = $ae_pack->fetch('pack');
	//            $pro_func = '';
	//            foreach ($packs as $key => $package) {
	//                if (array_search($package->sku, $option_for_project) !== false) {
	//                    unset($packs[$key]);
	//                    $pro_func[] = $package;
	//                }
	//            }
	//            echo '<script type="data/json" id="pro_func">' . json_encode($pro_func) . '</script>';
	//
	//            $opt = '';
	//            foreach ($option_for_project as $value) {
	//                if ($post_convert->$value == 1)
	//                    $opt[] = array(
	//                        'name' => $value,
	//                        'et_date' => date('d-m-Y', strtotime(get_post_meta($_REQUEST['id'], 'et_' . $value)[0]))
	//                    );
	//            }
	//            echo '<script type="data/json" id="opt_on">' . json_encode($opt) . '</script>';
	//        }
	//    }

	?>
    <div class="fre-post-project-box">
        <form class="post" role="form">
            <div class="step-post-project edit-options" id="fre-post-project">
                <div class="fre-input-field">
                    <label class="fre-field-title"
                           for="fre-project-title"><?php _e( 'Your project title', ET_DOMAIN ); ?></label>
					<?php $number_free_plan_used = AE_Package::get_used_free_plan( $user_ID );
					$id                          = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
					if ( $id ) {
						$post    = get_post( $id );
						$pack_id = $post->et_payment_package;

						foreach ( $packs as $key => $package ) {
							if ( ! empty( $package->sku ) && $pack_id === $package->sku ) {
								$number_of_post = $package->et_number_posts;
								if ( $number_of_post >= 1 ) {
									// get package current order
									//if ( ! empty( $orders[ $sku ] ) ) {
									if ( ! empty( $orders[ 'sku' ] ) ) {
										$order = get_post( $orders['sku' ] );
									}
									if ( empty( $package_data[ 'sku' ] ) && isset( $order->post_status ) && $order->post_status != 'draft' ) {
										$package_data_sku = $package_data[ 'sku' ];
										if ( isset( $package_data_sku['qty'] ) && $package_data_sku['qty'] > 0 ) {
											/**
											 * print text when company has job left in package
											 */
											$number_of_post = $package_data_sku['qty'];
										}
									}

									if ( ! $package->et_price ) { // if free package.
										$number_of_post = (int) $number_of_post - (int) $number_free_plan_used;
										if ( $number_of_post < 0 ) {
											$number_of_post = 0;
										}
									}


									if ( $number_of_post > 0 ) {
										if ( $number_of_post == 1 ) {
											$texthidden = $number_of_post;
										} else {
											$texthidden = $number_of_post;
										}
									} else {
										$texthidden = $number_of_post;
									}
								}
								echo '<div class="hidden"><div class="plan-cols">' . $texthidden . '</div><div class="plans-name">' . $package->post_title . '</div></div>';
							}
						}
					} ?>
                    <input class="input-item text-field" id="fre-project-title" type="text" name="post_title" readonly>
                </div>
				<?php get_template_part( 'template/pro-option-for-project2' ); ?>
                <!--new2-->
				<?php
				// Add hook: add more field
				echo '<ul class="fre-custom-field">';
				do_action( 'ae_submit_post_form', PROJECT, $post );
				echo '</ul>';
				?>
                <div class="fre-post-project-btn">
                    <button class="fre-btn fre-post-project-next-btn fre-submit-btn wpp-submit"  type="submit">
                        <?php _e( "Save Project", ET_DOMAIN ); ?>
                    </button>
                    <span class="fre-btn fre-cancel-btn wpp-clear-options ">
                        <?php _e( 'Cancel', WPP_TEXT_DOMAIN ); ?>
                    </span>
                </div>

            </div>
        </form>
    </div>
</div>