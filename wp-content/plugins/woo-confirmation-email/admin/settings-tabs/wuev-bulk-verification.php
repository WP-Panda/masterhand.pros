<?php
defined( 'ABSPATH' ) || exit;
?>
<table class="form-table">
    <tr>
        <th><?php echo __( 'Select User Role', 'woo-confirmation-email' ); ?></th>
        <td>
            <select name="wc_email_user_role">
				<?php wp_dropdown_roles(); ?>
            </select>
        </td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th><input type="submit" value="<?php echo __( 'Verify', 'woo-confirmation-email' ); ?>"
                   class="button button-primary" name="role_bulk_users"/></th>
        <td></td>
    </tr>
    <tr>
        <th><?php echo __( 'Verify All the Users', 'woo-confirmation-email' ); ?></th>
        <td><p class="description"><?php echo __( 'Verify all the Users of all roles', 'woo-confirmation-email' ); ?></p></td>
    </tr>
    <tr class="wuev-tr-border-bottom">
        <th><input type="submit" value="<?php echo __( 'Verify All', 'woo-confirmation-email' ); ?>"
                   class="button button-primary" name="site_bulk_users"/></th>
        <td></td>
    </tr>
</table>
