<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
?>

<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 fre-input-field default-currency-wrap">

    <label class="fre-field-title">
		<?php _e( 'Currency', ET_DOMAIN ); ?>
    </label>

    <select name="project_currency">

		<?php $selected_currency = get_user_meta( $user_ID, 'currency', true );

		foreach ( get_currency() as $key => $data ) {
			$is_selected  = '';
			$user_country = get_user_country();
			$user_country = $user_country['name'];

			if ( empty( $selected_currency ) ) {
				if ( $user_country == $data['country'] ) {
					$is_selected = 'selected';
				}
			} else {
				if ( $selected_currency == $data['code'] ) {
					$is_selected = 'selected';
				}
			} ?>
            <option data-icon="<?php echo $data['flag'] ?>" <?php echo $is_selected ?>>
				<?php echo $data['code'] ?>
            </option>
		<?php } ?>

    </select>
</div>