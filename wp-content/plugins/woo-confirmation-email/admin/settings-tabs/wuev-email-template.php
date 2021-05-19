<?php
defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'media-upload' );
wp_enqueue_script( 'thickbox' );
wp_enqueue_style( 'thickbox' );
?>
<table class="form-table">
    <tr class="wuev-tr-border-bottom">
        <th><?php echo __( 'Header Footer Customisation', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_verification_method1"><input id="xlwuev_verification_method1"
                                                            data-add='["tr_email_body_wysisyg"]'
                                                            data-remove='["tr_email_body_textarea","tr_seperate_email"]'
                                                            data-condition='{"xlwuev_verification_type":"1"}'
                                                            data-condition-show='["tr_email_subject","tr_email_heading"]'
                                                            class="conditional_radio" name="xlwuev_verification_method"
                                                            type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_verification_method'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Yes, Customise Header and Footer from WYSIWYG', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_method2"><input id="xlwuev_verification_method2"
                                                            data-add='["tr_email_body_textarea","tr_seperate_email"]'
                                                            data-remove='["tr_email_body_wysisyg"]'
                                                            class="conditional_radio" name="xlwuev_verification_method"
                                                            type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_verification_method'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo sprintf( __( 'No, use WooCommerce native Email Header, Footer and styling options (<a target="_blank" href="%s">Click Here</a>)', 'xlplugins' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ); ?>
            </label>
        </td>
    </tr>
    <tr class="tr_seperate_email wuev-tr-border-bottom">
        <th><?php echo __( 'Separate Email for Verification', 'woo-confirmation-email' ); ?></th>
        <td>
            <label for="xlwuev_verification_type1"><input id="xlwuev_verification_type1"
                                                          data-add='["tr_email_subject","tr_email_heading"]'
                                                          data-remove="0"
                                                          class="conditional_radio" name="xlwuev_verification_type"
                                                          type="radio" value="1"
					<?php echo ( '1' == $tab_options['xlwuev_verification_type'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'Yes', 'woo-confirmation-email' ); ?>
            </label>
            <br>
            <label for="xlwuev_verification_type2"><input id="xlwuev_verification_type2" data-add="0"
                                                          data-remove='["tr_email_subject","tr_email_heading"]'
                                                          class="conditional_radio" name="xlwuev_verification_type"
                                                          type="radio" value="2"
					<?php echo ( '2' == $tab_options['xlwuev_verification_type'] ) ? 'checked' : ''; ?>>&nbsp;<?php echo __( 'No, include Verification Email Text in WooCommerce Welcome Email', 'woo-confirmation-email' ); ?>
            </label>
        </td>
    </tr>
    <tr class="tr_email_subject wuev-tr-border-bottom">
        <th><?php echo __( 'Subject', 'woo-confirmation-email' ); ?></th>
        <td>
            <input name="xlwuev_email_subject" type="text" class="wuev-input-text" placeholder="Enter Email Subject"
                   value="<?php echo $tab_options['xlwuev_email_subject']; ?>" required>
        </td>
    </tr>
    <tr class="tr_email_heading wuev-tr-border-bottom">
        <th><?php echo __( 'Email Heading', 'woo-confirmation-email' ); ?></th>
        <td>
            <input name="xlwuev_email_heading" type="text" class="wuev-input-text" placeholder="Enter Email Heading"
                   value="<?php echo $tab_options['xlwuev_email_heading']; ?>" required>
        </td>
    </tr>
    <tr class="tr_email_body_textarea wuev-tr-border-bottom">
        <th><?php echo __( 'Email Body', 'woo-confirmation-email' ); ?></th>
        <td>
			<textarea name="xlwuev_email_body" class="wuev-input-textarea" placeholder="Enter Email Content" rows="10"
                      required><?php echo $tab_options['xlwuev_email_body']; ?></textarea>
        </td>
    </tr>
    <tr class="tr_email_body_wysisyg wuev-tr-border-bottom">
        <th><?php echo __( 'Email Body', 'woo-confirmation-email' ); ?></th>
        <td>
			<?php wp_editor( $tab_options['xlwuev_email_header'], 'xlwuev_email_header' ); ?>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th></th>
        <td>
            <p class="description"><?php echo __( 'You can use following merge tags in email body.', 'woo-confirmation-email' ); ?></p>
            {{xlwuev_user_login}} = <?php echo __( 'User Login Name for login', 'woo-confirmation-email' ); ?><br>
            {{xlwuev_display_name}} = <?php echo __( 'User Display Name', 'woo-confirmation-email' ); ?><br>
            {{xlwuev_user_email}} = <?php echo __( 'User Email', 'woo-confirmation-email' ); ?><br>
            {{xlwuev_user_verification_link}} = <?php echo __( 'Email Verification Link', 'woo-confirmation-email' ); ?><br>
            {{wcemailverificationcode}} = <?php echo __( 'Email Verification Link', 'woo-confirmation-email' ); ?><br>
            {{sitename}} = <?php echo __( 'Your Website Name', 'woo-confirmation-email' ); ?><br>
            {{sitename_with_link}} = <?php echo __( 'Your Website Name with link', 'woo-confirmation-email' ); ?><br>
            {{xlwuev_verification_link_text}} = <?php echo __( 'Shows the verification link text', 'woo-confirmation-email' ); ?><br>
            <br><br>
            <p class="description"><?php echo __( 'You can use following shortcode in email body editor.', 'woo-confirmation-email' ); ?></p>
            [wcemailverificationcode] = <?php echo __( 'Email Verification Link', 'woo-confirmation-email' ); ?>
        </td>
    </tr>
</table>
