<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
extract( $args );

switch ( $visualFlagNumber ) :
	case 1:
		$label = __( 'Master', WPP_TEXT_DOMAIN );
		break;
	case 2:
		$label = __( 'Creator', WPP_TEXT_DOMAIN );
		break;
	case 3:
		$label = __( 'Expert', WPP_TEXT_DOMAIN );
		break;
	default:
		$label = false;
		break;
endswitch;

if ( ! empty( $label ) ) {
	printf( '<span class="status">%s</span>', $label );
}