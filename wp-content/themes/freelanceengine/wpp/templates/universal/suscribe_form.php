<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args )
?>
<div class="fre-blog-subscribe-form mailster-subscribe__block">
    <div class='fre-blog-subscribe-form_title'><?php echo __( 'Subscribe', ET_DOMAIN ) ?></div>
    <div class="emaillist">
		<?php
		if ( function_exists( 'mailster_form' ) ) :
			mailster_form( $id );
		endif;
		?>
    </div>
</div>