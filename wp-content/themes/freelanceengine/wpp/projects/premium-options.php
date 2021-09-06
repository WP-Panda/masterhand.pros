<?php
/**
 * Всякие допопции
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Список опций
 *
 * @return array
 */
function get_premium_options_list() {
	global $user_ID, $ae_post_factory, $option_for_project;

	$ae_pack = $ae_post_factory->get( 'pack' );

	//получение паков из опций
	$packs = $ae_pack->fetch( 'pack' );



	#тут статусы юзера возвращает посмотреть иъ ID
	$user_status = get_user_pro_status( $user_ID );

	#тут некие опции не совсем понятно какие
	$options = getOptionsEmployer();

	# тут получение платных опций
	$pro_em_functions = [];

	foreach ( $packs as $key => $package ) {

		$key_option = array_search( $package->sku, $options );

		if ( $key_option !== false ) {
			unset( $packs[ $key ] );
			$pro_em_functions[ $key_option ] = [
				'sku'   => $options[ $key_option ],
				'price' => getValueByProperty( $user_status, $package->sku )
			];
		}

	}

	#это опции
	ksort( $pro_em_functions );
	$GLOBALS['packs'] = $packs;
	$GLOBALS['pro_em_functions'] = $pro_em_functions;
	return $pro_em_functions;
}

/**
 * Список опций Json
 */
function premium_options_json() {
	$pro_em_functions = get_premium_options_list();

	if ( ! empty( $pro_em_functions ) ) {
		printf( '<script type="data/json" id="pro_em_functions">%s</script>', json_encode( $pro_em_functions ) );
	}
}
