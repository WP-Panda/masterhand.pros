<?php
defined( 'ABSPATH' ) || exit; ?>
<table class="form-table">
    <p><?php echo __( 'You can test if your server is capable of sending emails.', 'woo-confirmation-email' ); ?></p>
    <tr>
        <th><?php echo __( 'Email', 'woo-confirmation-email' ); ?></th>
        <td>
            <input name="wc_email_test_recipient" type="text" class="wuev-input-text" placeholder="Enter Email" required>
        </td>
    </tr>
</table>
