<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	/**
	 * фикс для смены дирректории
	 *
	 * @param $upload
	 *
	 * @return mixed
	 */
	function wpp_alter_the_upload_dir( $upload ) {
		$upload[ 'subdir' ] = '/wpp/company/import';
		$upload[ 'path' ]   = $upload[ 'basedir' ] . $upload[ 'subdir' ];
		$upload[ 'url' ]    = $upload[ 'baseurl' ] . $upload[ 'subdir' ];

		return $upload;
	}


	/**
	 * Запрос для вывода компании
	 *
	 * @param $country
	 * @param $paged
	 * @param $data_params
	 *
	 * @return array
	 */
	function wpp_company_query( $country, $paged, $data_params ) {

		if ( ! empty( $country ) ) {

			global $wpdb;

			$offset = ( (int) $paged - 1 ) * COMPANY_PER_PAGE;

			//получение кода страны
			$country_id = wpp_get_country( $country );

			$table_name = $wpdb->prefix . 'wpp_company_data';

			$str = '';

			if ( ! empty( $data_params ) ) {

				$n = 1;
				foreach ( FIlTER_DENY_PARAMS as $param ) {

					$pref = $n === 1 ? ' WHERE ' : ' AND ';

					if ( empty( $data_params[ $param ] ) ) {
						continue;
					}

					if ( $param === 'string' ) {
						$str .= $pref . '(`title` LIKE \'%' . $data_params[ $param ] . '%\' OR `address` LIKE \'%' . $data_params[ $param ] . '%\')';
					} else {

						$str .= $pref . $param . '=' . $data_params[ $param ];
					}

					$n ++;
				}
			}

			if ( empty( $str ) && empty( $data_params[ 'all' ] ) ) {
				$str = 'WHERE `country` =' . $country_id;
			}


			//получение списка компаний
			$companies = $wpdb->get_results( "SELECT * FROM $table_name $str ORDER BY `title` ASC LIMIT " . COMPANY_PER_PAGE . " OFFSET $offset" );

			//wpp_dump($companies);

			if ( ! empty( $companies ) ) {
				$found_posts_nums = $wpdb->get_results( "SELECT COUNT(`id`) FROM $table_name $str  ", ARRAY_N );
				$found_posts_num  = (int) $found_posts_nums[ 0 ][ 0 ];
				$found_labels     = wpp_found_labels( $found_posts_num );
			} else {
				$found_posts_num = 0;
				$found_labels    = false;
			}



		}

		return [
			'found_labels'    => $found_labels,
			'found_posts_num' => $found_posts_num,
			'companies'       => $companies
		];
	}


	/**
	 * Удаление компрании
	 *
	 * @param $_ID
	 */
	function company_delete( $_ID ) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpp_company_data';
		$check      = $wpdb->get_row( "SELECT * FROM $table_name WHERE `id` = " . $_ID );

		if ( empty( $check ) ) {
			return false;
			//wp_send_json_error( [ 'msg' => wpp_message_codes( 5 ) ] );
		}

		$wpdb->delete( $table_name, [ 'id' => $_ID ] );

	}


	/**
	 * Количество компаний
	 *
	 * @return int
	 */
	function wpp_companies_found() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpp_company_data';
		$check      = $wpdb->get_results( "SELECT COUNT( `id` ) FROM $table_name", ARRAY_N );

		return empty( $check[ 0 ][ 0 ] ) ? 0 : absint( $check[ 0 ][ 0 ] );

	}