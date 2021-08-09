<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

# количество компаний на страницу
if ( ! defined( 'COMPANY_PER_PAGE' ) ) :
	define( 'COMPANY_PER_PAGE', 10 );
endif;

$deny_params = [ 'cat', 'sub', 'country', 'state', 'city', 'string' ];

# разрешенные гет параметры для фильтра
if ( ! defined( 'FIlTER_DENY_PARAMS' ) ) :
	define( 'FIlTER_DENY_PARAMS', $deny_params );
endif;

if ( ! defined( 'WPP_THEME_DIR' ) ) {
	define( 'WPP_THEME_DIR', get_template_directory() );
}


$array = [
	//'s',
	'WppFr',
	'WppFr_Assets',
	'ajax_check', #пока тут - потом убрать
	'wpp-core/init',
	'helpers/init',
	'users/init',
	'fixes',
	'deprecated',
	'php_ext',
	'db',
	'anton/init',
	'modules/Wpp_Module_Base',
	'modules/extention/init',
	'modules/companies/init',
	'error-api',
	'helpers',
	'ajax/init',
	'setting/init',
	'modules/skills/init',
	'modules/activity_rating/index',
	'modules/referral_code/referral_code',
	'modules/pro_status/add-pro-status'

];

foreach ( $array as $file ) :
	require_once $file . '.php';
endforeach;

$GLOBALS['wpp_fr'] = new WppMain\WppFr();