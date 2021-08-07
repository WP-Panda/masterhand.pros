<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>
<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field">
    <label class="fre-field-title"><?php _e( 'Phone', ET_DOMAIN ); ?>
        <span><?php if ( $user_phone ) {
				_e( '(Confirmed by sms)', ET_DOMAIN );
			} ?></span></label>
    <a href="#modal_change_phone" data-toggle="modal"
       data-dismiss="modal" class="change-phone"
       data-ctn_edit="ctn-edit-account" id="btn_edit">
		<?php echo ! empty( $user_phone_code . $user_phone ) ? $user_phone_code . $user_phone : __( 'Edit phone', ET_DOMAIN ); ?>
    </a>
</div>