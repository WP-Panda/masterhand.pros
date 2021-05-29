<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$array = [
	'Wpp_Fr_Assets',//
	'Wpp_Fr_Post_Gallery',
	'Wpp_Pf_Slider',//
	'WPP_Get_IMG',//
	'Wpp_Pf_Endpoints',//
	//'Zebra_Image',
	'WPP_Pf_Breadcrumbs',//
	'Wpp_Fr_Geo',//
	'Wpp_Fr_Custom_Taxonomy',//
	'WPP_Tax_Term_Img',
	'menu-custom-fields/Menu',
	'menu-custom-fields/Menu_Walker_Edit'
];

foreach ( $array as $one ) {
	require_once $one . '.php';
}