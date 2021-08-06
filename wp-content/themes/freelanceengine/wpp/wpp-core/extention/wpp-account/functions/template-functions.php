<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Date: 06.05.2019
	 * Time: 21:49
	 */

	/**
	 * Меню конечных точек
	 */
	function wpp_pf_acc_endpoints_nav() {
		wpp_get_template_part( 'wpp-extention/wpp-account/templates/points-nav', [] );
	}



	/**
	 * Доп хэдер личного кабинета
	 */
	function wpp_pf_acc_endpoints_header() {
		wpp_get_template_part( 'wpp-extention/wpp-account/templates/header/point-header', [] );
	}