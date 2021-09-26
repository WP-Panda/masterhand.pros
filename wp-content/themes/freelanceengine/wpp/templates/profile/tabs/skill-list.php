<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 *
 * Список скиллов
 */

defined( 'ABSPATH' ) || exit;
extract( $args );
/**
 * Для ускорения вынес из цикла
 */
$allow         = wpp_is_endorse_allow( $user_ID );
$endorse_class = ! empty( $allow ) ? ' mode-endorse' : '';
$skills        = WPP_Skills_User::getInstance()->get_user_skill_list( $user_ID )
?>
<ul id="list_skills_user">
	<?php if ( ! empty( $skills ) ) :
		foreach ( $skills as $skill ) {

			$endorsed_data = ! empty( $allow ) ? sprintf( ' data-uid="%s" data-skill="%s"', $user_ID, $skill['id'] ) : '';
			$endorsed      = wpp_is_endorsed( $user_ID, $skill['id'] ) ? ' endorsed' : '';

			printf( '<li class="item-list-skills"><span class="item-endorse-skill%s%s"%s>%s</span><span class="endorse-skill" title="%s">%s</span></li>',
				$endorse_class,
				$endorsed,
				$endorsed_data,
				$skill['title'],
				__( 'counts of endorsement', WPP_TEXT_DOMAIN ),
				$skill['count']
			);
		}
	endif; ?>
</ul>