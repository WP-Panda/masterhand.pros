<?php
	/**
	 * Class Wpp_Pf_Endpoints
	 * Регистрация корнечных точек
	 */

	class Wpp_Pf_Endpoints{

		function __construct() {

			register_activation_hook( __FILE__, [
				$this,
				'activate'
			] );

			add_action( 'init', [
				__CLASS__,
				'add_endpoints'
			] );
			add_action( 'template_include', [
				__CLASS__,
				'change_template'
			] );
			add_action( 'wpp_nav_points', [
				__CLASS__,
				'endpoints_nav'
			] );
			#add_action( 'init', 'do_rewrite' );
		}

		function activate() {
			set_transient( 'wpp_fr_endpoints', 1, 60 );
		}


		/*function do_rewrite() {
			$args = self::endpoint_settings();
			foreach ( $args as $one_point => $val ) {

				// Правило перезаписи
				add_rewrite_rule( sprintf( '^(%s)/([^/]*)/([^/]*)/?', $one_point ), sprintf( 'index.php?%s=$matches[1]', $one_point ), 'top' );

			}
		}*/


		/**
		 * Добавление конечных точек.
		 */
		public static function add_endpoints() {
			$args = self::endpoint_settings();

			foreach ( $args as $one_point => $val ) {

				if ( !empty( $val[ 'parent_point' ] ) ) :
					continue;
				endif;

				if ( !empty( $one_point ) ) {
					$mask = !empty( $val[ 'places' ] ) ? esc_attr( $val[ 'places' ] ) : EP_ROOT;
					add_rewrite_endpoint( $one_point, $mask );
				}

			}

			if ( get_transient( 'wpp_fr_endpoints' ) ) {
				delete_transient( 'wpp_fr_endpoints' );
				flush_rewrite_rules();
			}
		}

		/**
		 * Get query current active query var.
		 *
		 * @return string
		 */
		public static function get_current_endpoint() {

			global $wp;

			$args = self::endpoint_settings();

			foreach ( $args as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return $key;
				}
			}

			return false;
		}

		/**
		 * Замена шаблона
		 *
		 * @param $template
		 *
		 * @return string
		 */
		public static function change_template( $template ) {


			#wpp_dump( get_query_var('account'));

			$point = self::get_current_endpoint();

			if ( empty( $point ) ) {
				return $template;
			}

			$args = self::endpoint_settings();

			if ( get_query_var( $point, false ) !== false ) {

				$page = get_query_var( $point );

				if ( !is_user_logged_in() && ( ( !empty( $args[ $point ][ 'child' ][ $page ][ 'registred' ] ) && $args[ $point ][ 'child' ][ $page ][ 'registred' ] === true ) || ( !empty( $args[ $point ][ 'registred' ] ) && $args[ $point ][ 'registred' ] === true ) ) ) {

					return apply_filters( 'wpp_fr_load_not_logged_template_part', wpp_fr()->plugin_path() . '/wpp-extention/wpp-account/templates/not-logged-user.php' );

				} else {

					$tenplate_change = !empty( $args[ $point ][ 'child' ][ $page ][ 'template' ] ) ? $args[ $point ][ 'child' ][ $page ][ 'template' ] : ( !empty( $args[ $point ][ 'template' ] ) ? $args[ $point ][ 'template' ] : null );

					$template_name = !empty( $tenplate_change ) ? $tenplate_change : wpp_fr()->plugin_path() . '/wpp-extention/wpp-account/templates/main-template.php';

					$newTemplate = locate_template( [ basename( $template_name ) . PHP_EOL ] );

					if ( '' != $newTemplate ) {
						return $newTemplate;
					}

					if ( file_exists( $template_name ) ) {
						return $template_name;
					}
				}
			}

			return $template;
		}

		/**
		 * Get page title for an endpoint.
		 *
		 * @param  string $endpoint Endpoint key.
		 *
		 * @return array
		 */
		public static function endpoint_settings() {

			$end_points = [];

			/*
			 * Пример массива
			'models'      => array (
			'title'    => __( 'Item Title' ),
			'icons'    => '',
			'template' => '/templates/pages/list-models.php',
			'order'    => 5,
			'caps'     => 'manage_options',
			'places'   => EP_ROOT
			),
			 */


			return apply_filters( 'wpp_pf_endpoints_args', $end_points );
		}


		public static function endpoints_nav() {

			$navs = self::endpoint_settings();
			uasort( $navs, wpp_fr_make_comparer( 'order' ) );
			$out = '';

			$one_item = <<<ITEM
			<li class="nav-item start">
			    <a href="%s" class="nav-link nav-toggle">
			        %s
			        <span class="title">%s</span>
			    </a>
			</li>
ITEM;

			foreach ( $navs as $nav => $item ) {
				//показывать в меню или нет
				if ( empty( $item[ 'in-nav' ] ) || true !== $item[ 'in-nav' ] ) {
					continue;
				}

				$out .= sprintf( $one_item, home_url( '/' . $nav ), !empty( $item[ 'icons' ] ) ? sprintf( '<i class="material-icons">%s</i>', esc_attr( $item[ 'icons' ] ) ) : null, esc_html( $item[ 'title' ] ) );

				// если есть дочерние страницы
				$child_pages = $item[ 'child' ];

				if ( !empty( $child_pages ) && is_array( $child_pages ) ) :

					uasort( $child_pages, wpp_fr_make_comparer( 'order' ) );

					foreach ( $child_pages as $one_child => $child_item ) :

						if ( empty( $child_item[ 'in-nav' ] ) || true !== $child_item[ 'in-nav' ] ) {
							continue;
						}

						$out .= sprintf( $one_item, home_url( '/' . $nav . '/' . $one_child ), !empty( $child_item [ 'icons' ] ) ? sprintf( '<i class="material-icons">%s</i>', esc_attr( $child_item [ 'icons' ] ) ) : null, esc_html( $child_item [ 'title' ] ) );

					endforeach;
				endif;
			}


			return $out;
		}
	}

	new Wpp_Pf_Endpoints();