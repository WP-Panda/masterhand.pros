<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

if ( use_paypal_to_escrow() ) { ?>
    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
        <label class="fre-field-title"><?php _e( 'Paypal account', ET_DOMAIN ) ?></label>
        <input type="text"
               value="<?php echo $user_data->paypal ?>"
               name="user_paypal" id="user_paypal"
               placeholder="<?php _e( 'Your paypal login', ET_DOMAIN ) ?>">
    </div>
<?php }