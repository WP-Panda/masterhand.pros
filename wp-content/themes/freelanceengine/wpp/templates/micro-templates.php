<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pro_label' ) ) :
	/**
	 * Про статус
	 */
	function pro_label() {
		printf( '<span class="status">%s</span>', __( 'PRO', WPP_TEXT_DOMAIN ) );
	}
endif;

if ( ! function_exists( 'visual_flag' ) ) :
	/**
	 * статус дополнительно
	 */
	function visual_flag( $visualFlag = null, $visualFlagNumber = 1 ) {
		if ( ! empty( $visualFlag ) ) :
			switch ( $visualFlagNumber ):
				case 1:
					$text = __( 'Master', ET_DOMAIN );
					break;
				case 2:
					$text = __( 'Creator', ET_DOMAIN );
					break;
				case 3:
					$text = __( 'Expert', ET_DOMAIN );
					break;
				default:
					$text = __( 'Master', ET_DOMAIN );
			endswitch;

			printf( '<span class="status">%s</span>', $text );
		endif;
	}
endif;

if ( ! function_exists( 'status_expire' ) ) :
	/**
	 * Истечение статуса PRO
	 */
	function status_expire( $user_pro_expire = null ) {
		printf( '<div class="status_expire">%s: %s</div>', __( 'Expire', WPP_TEXT_DOMAIN ), $user_pro_expire );
	}
endif;