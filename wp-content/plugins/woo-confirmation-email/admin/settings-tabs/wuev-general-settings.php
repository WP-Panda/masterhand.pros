<?php
defined( 'ABSPATH' ) || exit;

if ( '1' == $tab_options['xlwuev_verification_page'] ) {
	$custom_page_hide_class = 'wuev-custom-page-hide';
	$selected               = 0;
} else {
	$custom_page_hide_class = '';
	$selected               = $tab_options['xlwuev_verification_page_id'];
}
$args = array(
	'depth'                 => 0,
	'child_of'              => 0,
	'selected'              => $selected,
	'echo'                  => 1,
	'name'                  => 'xlwuev_verification_page_id',
	'id'                    => null, // string
	'class'                 => null, // string
	'show_option_none'      => null, // string
	'show_option_no_change' => null, // string
	'option_none_value'     => null, // string
);

if ( '1' == $tab_options['xlwuev_verification_error_page'] ) {
	$custom_page_hide_error_class = 'wuev-custom-page-hide';
	$selected_error               = 0;
} else {
	$custom_page_hide_error_class = '';
	$selected_error               = $tab_options['xlwuev_verification_error_page_id'];
}
$args1 = array(
	'depth'                 => 0,
	'child_of'              => 0,
	'selected'              => $selected_error,
	'echo'                  => 1,
	'name'                  => 'xlwuev_verification_error_page_id',
	'id'                    => null, // string
	'class'                 => null, // string
	'show_option_none'      => null, // string
	'show_option_no_change' => null, // string
	'option_none_value'     => null, // string
);

?>
<table class="form-table">

    <tr class="wuev-tr-border-bottom">
        <th><?php echo __( 'Allow User', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_restrict_user1"><input id="xlwuev_restrict_user1" data-add='["tr-no-login"]' data-remove='["tr-yes-login"]'
                                                      class="conditional_radio" name="xlwuev_restrict_user" type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_restrict_user'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'No, Force Users to Verify the Account before they can login', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_restrict_user2"><input id="xlwuev_restrict_user2" data-add='["tr-yes-login"]' data-remove='["tr-no-login"]'
                                                      class="conditional_radio" name="xlwuev_restrict_user" type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_restrict_user'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Yes, Allow User to login and show a Notice for Verification Email', 'woo-confirmation-email' ); ?>
            </label>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom tr-no-login">
        <th><?php echo __( 'Notice', 'woo-confirmation-email' ); ?></th>
        <td>
            <textarea name="xlwuev_email_error_message_not_verified_outside" class="wuev-input-textarea" required rows="3"><?php echo $tab_options['xlwuev_email_error_message_not_verified_outside']; ?></textarea>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom tr-yes-login">
        <th><?php echo __( 'Notice', 'woo-confirmation-email' ); ?></th>
        <td>
            <textarea name="xlwuev_email_error_message_not_verified_inside" class="wuev-input-textarea" required rows="3"><?php echo $tab_options['xlwuev_email_error_message_not_verified_inside']; ?></textarea>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th><?php echo __( 'Verification Success Page', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_verification_page1"><input id="xlwuev_verification_page1" data-add-class="wuev-custom-page-hide" data-remove-class="0"
                                                          data-element="all-pages-dropdown"
                                                          class="xlwuev_verification_page_radio" name="xlwuev_verification_page"
                                                          type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_verification_page'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'WooCommerce My-Account Page', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_page2"><input id="xlwuev_verification_page2" data-add-class="0" data-remove-class="wuev-custom-page-hide"
                                                          data-element="all-pages-dropdown"
                                                          class="xlwuev_verification_page_radio" name="xlwuev_verification_page"
                                                          type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_verification_page'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Custom Page', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_page"
                   class="all-pages-dropdown <?php echo $custom_page_hide_class; ?>"><?php wp_dropdown_pages( $args ); ?>
                <p class="description">Add <b>[wcemailverificationmessage]</b> shortcode on your selected custom page to see success notification.</p>
            </label>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th><?php echo __( 'Verification Error Page', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_verification_error_page1"><input id="xlwuev_verification_error_page1" data-add-class="wuev-custom-page-hide" data-remove-class="0"
                                                                data-element="all-pages-dropdown-error"
                                                                class="xlwuev_verification_page_radio" name="xlwuev_verification_error_page"
                                                                type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_verification_error_page'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'WooCommerce My-Account Page', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_error_page2"><input id="xlwuev_verification_error_page2" data-add-class="0" data-remove-class="wuev-custom-page-hide"
                                                                data-element="all-pages-dropdown-error"
                                                                class="xlwuev_verification_page_radio" name="xlwuev_verification_error_page"
                                                                type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_verification_error_page'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Custom Page', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_error_page"
                   class="all-pages-dropdown-error <?php echo $custom_page_hide_error_class; ?>"><?php wp_dropdown_pages( $args1 ); ?>
            </label>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th><?php echo __( 'Allow Automatic Login After Successful Verification', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_automatic_user_login1"><input id="xlwuev_automatic_user_login1" name="xlwuev_automatic_user_login" type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_automatic_user_login'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'No', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_automatic_user_login2"><input id="xlwuev_automatic_user_login2" name="xlwuev_automatic_user_login" type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_automatic_user_login'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Yes', 'woo-confirmation-email' ); ?>
            </label>
        </td>
    </tr>
</table>
