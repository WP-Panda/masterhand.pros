<?php
	defined( 'ABSPATH' ) || exit;

	function menu() {

		add_menu_page(
			__( 'Paid Users', WPP_TEXT_DOMAIN ),
			__( 'Paid Users', WPP_TEXT_DOMAIN ),
			'Pro Paid Users',
			'manage_options',
			'pro-status',
			'show_status_users'
		);

		$page_freelance = add_submenu_page( 'pro-status', 'Freelance status', 'Freelance status', 'manage_options', 'freelance', 'show_settings_freelance' );

		add_action( 'admin_print_styles-' . $page_freelance, 'wpp_pro_admin_styles' );

		$page_employer = add_submenu_page( 'pro-status', 'Employer status', 'Employer status', 'manage_options', 'employer', 'show_settings_employer' );
		add_action( 'admin_print_styles-' . $page_employer, 'wpp_pro_admin_styles' );
	}

	add_action( 'admin_menu', 'menu' );

	function wpp_pro_admin_styles() {
		wp_enqueue_style( 'wpp_pro_status', get_template_directory_uri() . '/wpp/modules/pro_status/css/add-pro-status-public.css', [], time(), 'all' );
	}


	// Include table with pro users
	function show_status_users() {
		require_once( __DIR__ . '/includes/class-pro-paid-users-table.php' );
		render_list_paid_users();
	}

	function get_base_status( $user_role = '' ) {
		global $wpdb;
		if ( ! empty( $user_role ) ) {
			$where_and = " AND user_role='$user_role'";
		} else {
			$where_and = '';
		}

		$table = $wpdb->get_blog_prefix() . 'pro_status';

		$base_status = $wpdb->get_results( "select id, status_name, user_role from {$table} where status_position=1 $where_and", ARRAY_A );

		if ( empty( $base_status ) ) {
			return null;
		} else {
			return $base_status[ 0 ];
		}
	}

	function show_settings_freelance() {
		show_settings( 'freelance' );
	}

	function show_settings_employer() {
		show_settings( 'employer' );
	}


	function show_settings( $type ) {
		global $wpdb;
		$type_user  = $type;
		$where      = '';
		$result     = table_properties( $type_user, $where, 1 );
		$table      = $wpdb->get_blog_prefix() . 'pro_status';
		$statuses   = $wpdb->get_results( "SELECT id as status_id, status_position, status_name FROM $table WHERE user_role='$type_user' ORDER BY status_position", ARRAY_A );
		$table      = $wpdb->get_blog_prefix() . 'pro_properties';
		$properties = $wpdb->get_results( "
SELECT p.id as property_id, p.property_position, p.property_name, p.property_display, p.property_type, p.property_published, o.option_value
FROM $table p
left join wp_pro_options o ON o.property_id=p.id AND o.option_key='time'
WHERE user_role='$type_user'
ORDER BY property_position", ARRAY_A );
		$table      = $wpdb->get_blog_prefix() . 'pro_options';
		$options    = $wpdb->get_results( "SELECT id as option_id, option_value, option_key FROM $table WHERE option_key!='time'", ARRAY_A );
		include 'page-pro-properties.php';
	}

	function table_properties( $type, $where_and = '', $options = 0 ) // проверить при одинаковых названиях статусов берет только первый
	{
		global $wpdb;
		$where            = "WHERE p.user_role='$type' AND s.user_role='$type' " . $where_and;
		$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
		$table_status     = $wpdb->get_blog_prefix() . 'pro_status';
		$table_values     = $wpdb->get_blog_prefix() . 'pro_values';
		$table_options    = $wpdb->get_blog_prefix() . 'pro_options';
		$sql              = "
SELECT s.id as status_id, s.status_name, p.id as property_id, p.property_name, p.property_nickname, p.property_type, p.property_display, p.property_published, v.property_value, o.option_value FROM $table_values v
left JOIN $table_properties p on p.id=v.property_id
left JOIN $table_status s on s.id=v.status_id
left join $table_options o ON o.property_id=p.id AND o.option_key='time'
$where
ORDER BY s.status_position, p.property_position";
		$result           = $wpdb->get_results( $sql, ARRAY_A );
		//var_dump($sql);
		// массив названий статусов со сброшнными индексами, без повторений
		$statuses = array_values( array_unique( array_column( $result, 'status_name' ) ) );
		// вставляеться в начало массива
		array_unshift( $statuses, '' );
		$arr[ 0 ] = $statuses;
		//var_dump($result);
		$i = 1;
		foreach ( $result as $item ) {
			$key_status   = array_search( $item[ 'status_name' ], $statuses );
			$key_property = array_search( $item[ 'property_name' ], array_column( $arr, 0 ) );
			if ( $key_property === false ) {  // новое свойство по имени
				$arr[ $i ][ 0 ]                          = $item[ 'property_name' ];
				$key_property                            = $i;
				$arr[ $key_property ][ 'property_id' ]   = $item[ 'property_id' ];
				$arr[ $key_property ][ 'property_type' ] = $item[ 'property_type' ];
				$i ++;
			} else { // свойство с таким именем уже есть
				$id           = array_search( $item[ 'property_id' ], array_column( $arr, 'property_id' ) );
				$key_property = $id + 1;
				if ( $id === false ) { // новое свойство по id
					$arr[ $i ][ 0 ]                          = $item[ 'property_name' ];
					$key_property                            = $i;
					$arr[ $key_property ][ 'property_id' ]   = $item[ 'property_id' ];
					$arr[ $key_property ][ 'property_type' ] = $item[ 'property_type' ];
					$i ++;
				}
			}

			$arr[ $key_property ][ $key_status ]          = $item[ 'property_value' ];
			$arr[ $key_property ][ 'property_published' ] = $item[ 'property_published' ];
			$arr[ $key_property ][ 'property_nickname' ]  = $item[ 'property_nickname' ];

			if ( $options ) {
				$arr[ $key_property ][ 'property_display' ] = $item[ 'property_display' ];
				$arr[ $key_property ][ 'option_value' ]     = $item[ 'option_value' ];
			}
		}

		$id_statuses = array_values( array_unique( array_column( $result, 'status_id' ) ) );
		array_unshift( $id_statuses, 'id' );
		$arr[] = $id_statuses;

		return $arr;
	}

	function сheckout_order() {
		$res = $_POST;
		if ( ! empty( $res ) ) {
			$where_and         = "AND s.status_position<>1 AND p.property_published=1";
			$result            = table_properties( $res[ 'role' ], $where_and, 1 );
			$res[ 'statuses' ] = $result;

			return $res;
		} else {
			wp_redirect( home_url( '/404/' ) );
			exit();
		}
	}

	function getAllPrice( $type ) {
		global $wpdb;
		$sql = "select vl.status_id, pr.id as property_id, vl.property_value, op.option_value from " . $wpdb->get_blog_prefix() . 'pro_properties' . " pr
    left join " . $wpdb->get_blog_prefix() . 'pro_values' . " vl on pr.id=vl.property_id
    left join " . $wpdb->get_blog_prefix() . 'pro_options' . " op on op.property_id=vl.property_id
where pr.property_type=2 and pr.user_role='" . $type . "'
ORDER BY pr.property_position, vl.status_id";

		$result = $wpdb->get_results( $sql, ARRAY_A );

		$arr   = [ 'value' => [ '' ], 'time' => [ '' ] ];
		$props = [];
		foreach ( $result as $item ) {
			$arr[ 'value' ][ $item[ 'status_id' ] ][ $item[ 'property_id' ] ] = $item[ 'property_value' ];
			$arr[ 'time' ][ $item[ 'status_id' ] ][ $item[ 'property_id' ] ]  = $item[ 'option_value' ];
			$props[ $item[ 'option_value' ] ]                                 = $item[ 'property_id' ];
		}
		$arr[ 'value' ] = array_values( $arr[ 'value' ] );
		$arr[ 'time' ]  = array_values( $arr[ 'time' ] );
		$arr[ 'props' ] = $props;

		return $arr;
	}

	$action = ! empty( $_POST[ 'action' ] ) ? $_POST[ 'action' ] : '';

	switch ( $action ) {
		case "create_property":
			create_property();
			break;
		case "get_property":
			print_r( json_encode( get_property() ) );
			exit;
			break;
		case "edit_property":
			edit_property();
			break;
		case "delete_property":
			delete_property_with_value();
			exit;
			break;
		case "create_additional":
			create_additional();
			break;
		case "get_additional":
			print_r( json_encode( get_additional() ) );
			exit;
			break;
		case "edit_additional":
			edit_additional();
			break;
		case "delete_additional":
			delete_additional();
			exit;
			break;
		case "create_status":
			echo create_status_with_prop();
			break;
		case "get_status":
			print_r( json_encode( get_status() ) );
			exit;
			break;
		case "edit_status":
			edit_status();
			break;
		case "delete_status":
			delete_status_with_value();
			exit;
			break;
		case "edit_options":
			edit_options();
			break;
	}


	/** Админка **/
	function create_property() {
		global $wpdb;
		$res = $_POST;

		if ( $res && $res[ 'position' ] !== 'no' ) {
			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

			$prop  = [
				'property_name'      => $res[ 'name' ],
				'property_position'  => $res[ 'position' ] == 0 ? 1 : $res[ 'position' ] + 1,
				'property_display'   => (string) $res[ 'display' ],
				'property_published' => (string) $res[ 'published' ],
				'property_type'      => (string) $res[ 'type' ],
				'user_role'          => $res[ 'type_user' ]
			];
			$where = $res[ 'position' ] != 0 ? "AND property_position>{$res['position']}" : '';
			$wpdb->query( "UPDATE $table_properties SET property_position = property_position+1 WHERE user_role='{$res['type_user']}' $where" );
			$wpdb->insert( $table_properties, $prop, [ '%s', '%d', '%d', '%d', '%d', '%s' ] );
			$id_new_property = $wpdb->insert_id;

			if ( $id_new_property ) {
				$table_status  = $wpdb->get_blog_prefix() . 'pro_status';
				$arr_status_id = $wpdb->get_col( "SELECT id FROM {$table_status} WHERE user_role='{$res['type_user']}'" );

				$table_values = $wpdb->get_blog_prefix() . 'pro_values';
				foreach ( $arr_status_id as $item ) {

					$wpdb->query( "INSERT INTO {$table_values} (`status_id`, `property_id`, `property_value`) VALUES ($item, $id_new_property, 0)" );
				}
			}

			if ( ! empty( $res[ 'time' ] ) && $res[ 'type' ] == 2 ) {
				$table_options = $wpdb->get_blog_prefix() . 'pro_options';
				$wpdb->insert( $table_options, [
					'option_key'   => 'time',
					'option_value' => $res[ 'time' ],
					'property_id'  => $id_new_property
				], [ '%s', '%s', '%d' ] );
			}
		}
	}

	function get_property() {
		global $wpdb;
		$res = $_POST;
		if ( $res && $res[ 'property_id' ] ) {
			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
			$property         = $wpdb->get_results( "
SELECT p.id, p.property_name, p.property_position, p.property_display, p.property_type, o.option_value
FROM $table_properties p
left join wp_pro_options o ON o.property_id={$res['property_id']} && o.option_key='time'
WHERE p.id={$res['property_id']}", ARRAY_A ); //, p.property_published
		} else {
			$property = '';
		}

		return $property;
	}

	function get_property_name_by_nickname( $nickname ) {
		global $wpdb;
		if ( empty( $nickname ) ) {
			return '';
		}

		$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

		return $wpdb->get_var( "
SELECT p.property_name
FROM $table_properties p
WHERE p.property_nickname = '{$nickname}'" );
	}

	function edit_property() {
		global $wpdb;
		$res = $_POST;
		if ( $res ) {
			$table_property = $wpdb->get_blog_prefix() . 'pro_properties';
			if ( $res[ 'position' ] != $res[ 'position_old_property' ] ) {
				if ( $res[ 'position_old_property' ] > $res[ 'position' ] ) {
					$wpdb->query( "UPDATE $table_property SET property_position = property_position+1 WHERE property_position<{$res['position_old_property']} AND property_position>{$res['position']} AND user_role='{$res['type_user']}'" );
					if ( $res[ 'position' ] == 0 ) {
						$wpdb->query( "UPDATE $table_property SET property_position = 1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'" );
					} else {
						$wpdb->query( "UPDATE $table_property SET property_position = {$res['position']}+1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'" );
					}
				} else {
					$wpdb->query( "UPDATE $table_property SET property_position = property_position-1 WHERE property_position>{$res['position_old_property']} AND property_position<={$res['position']} AND user_role='{$res['type_user']}'" );
					$wpdb->query( "UPDATE $table_property SET property_position = {$res['position']} WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'" );
				}
			}

			$prop = [
				'property_name'      => $res[ 'name' ],
				'property_display'   => (string) $res[ 'display' ],
				'property_published' => (string) $res[ 'published' ],
				'property_type'      => (string) $res[ 'type' ]
			];

			$wpdb->update( $table_property, $prop, [ 'id' => $res[ 'property_id' ] ] );

			if ( ! empty( $res[ 'time' ] ) && $res[ 'type' ] == 2 ) {
				$table_options = $wpdb->get_blog_prefix() . 'pro_options';
				$time_var      = $wpdb->get_var( "SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'" );
				if ( ! empty( $time_var ) ) {
					$wpdb->update( $table_options, [ 'option_value' => $res[ 'time' ] ], [ 'property_id' => $res[ 'property_id' ] ] );
				} else {
					$wpdb->insert( $table_options, [
						'option_key'   => 'time',
						'option_value' => $res[ 'time' ],
						'property_id'  => $res[ 'property_id' ]
					], [ '%s', '%d', '%d' ] );
				}
			} else {
				$table_options = $wpdb->get_blog_prefix() . 'pro_options';
				$time_var      = $wpdb->get_var( "SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'" );
				if ( ! empty( $time_var ) ) {
					$wpdb->delete( $table_options, [
						'option_key'  => 'time',
						'property_id' => $res[ 'property_id' ]
					], [ '%d' ] );
				}
			}
		}
	}

	function delete_property_with_value() {
		global $wpdb;
		echo time() . '-' . $_SERVER[ 'REQUEST_TIME' ] . '-' . ( time() - $_SERVER[ 'REQUEST_TIME' ] );
		$res = $_POST;
		if ( $res && $res[ 'property_id' ] ) {
			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

			$wpdb->query( "DELETE FROM $table_properties WHERE id={$res['property_id']} AND user_role='{$res['type_user']}'" );
			$wpdb->query( "UPDATE $table_properties SET property_position = property_position-1 WHERE property_position>{$res['position']} AND user_role='{$res['type_user']}'" );

			$table_values = $wpdb->get_blog_prefix() . 'pro_values';

			$wpdb->query( "DELETE FROM $table_values WHERE property_id={$res['property_id']}" );

			$table_options = $wpdb->get_blog_prefix() . 'pro_options';

			$wpdb->query( "DELETE FROM $table_options WHERE property_id={$res['property_id']}" );
		}
	}

	function create_additional() {
		global $wpdb;
		$res = $_POST;

		//var_dump($res);

		if ( $res && $res[ 'position' ] !== 'no' ) {
			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

			$prop = [
				'property_name'      => $res[ 'name' ],
				'property_position'  => $res[ 'position' ] == 0 ? 1 : $res[ 'position' ] + 1,
				'property_display'   => (string) $res[ 'display' ],
				'property_published' => (string) $res[ 'published' ],
				'property_type'      => (string) $res[ 'type' ],
				'user_role'          => $res[ 'type_user' ]
			];

			$where = $res[ 'position' ] != 0 ? "AND property_position>{$res['position']}" : '';
			$wpdb->query( "UPDATE $table_properties SET property_position = property_position+1 WHERE user_role='{$res['type_user']}' AND property_type=3 $where" );
			$wpdb->insert( $table_properties, $prop, [ '%s', '%d', '%d', '%d', '%d', '%s' ] );
			$id_new_property = $wpdb->insert_id;

			if ( $id_new_property ) {
				$table_status  = $wpdb->get_blog_prefix() . 'pro_status';
				$arr_status_id = $wpdb->get_col( "SELECT id FROM {$table_status} WHERE user_role='{$res['type_user']}'" );

				$table_values = $wpdb->get_blog_prefix() . 'pro_values';
				foreach ( $arr_status_id as $item ) {
					$wpdb->insert( $table_values, [
						'status_id'      => $item,
						'property_id'    => $id_new_property,
						'property_value' => 0
					], [ '%d', '%d', '%d' ] );
				}
			}
		}
	}

	function get_additional() {
		global $wpdb;
		$res = $_POST;
		if ( $res && $res[ 'additional_id' ] ) {
			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
			$additional       = $wpdb->get_results( "
SELECT id, property_name, property_position, property_display, property_type
FROM $table_properties
WHERE id={$res['additional_id']}", ARRAY_A ); //, p.property_published
		} else {
			$additional = '';
		}

		return $additional;
	}

	function edit_additional() {
		global $wpdb;
		$res = $_POST;
		if ( $res ) {

			$table_property = $wpdb->get_blog_prefix() . 'pro_properties';
			if ( $res[ 'position' ] != $res[ 'position_old_additional' ] ) {
				if ( $res[ 'position_old_additional' ] > $res[ 'position' ] ) {
					$wpdb->query( "UPDATE $table_property SET property_position = property_position+1 WHERE property_position<{$res['position_old_additional']} AND property_position>{$res['position']} AND user_role='{$res['type_user']}' AND property_type=3" );
					if ( $res[ 'position' ] == 0 ) {
						$wpdb->query( "UPDATE $table_property SET property_position = 1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3" );
					} else {
						$wpdb->query( "UPDATE $table_property SET property_position = {$res['position']}+1 WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3" );
					}
				} else {
					$wpdb->query( "UPDATE $table_property SET property_position = property_position-1 WHERE property_position>{$res['position_old_additional']} AND property_position<={$res['position']} AND user_role='{$res['type_user']}' AND property_type=3" );
					$wpdb->query( "UPDATE $table_property SET property_position = {$res['position']} WHERE id={$res['property_id']} AND user_role='{$res['type_user']}' AND property_type=3" );
				}
			}

			$prop = [
				'property_name'      => $res[ 'name' ],
				'property_display'   => (string) $res[ 'display' ],
				'property_published' => (string) $res[ 'published' ],
				'property_type'      => (string) $res[ 'type' ]
			];
			$wpdb->update( $table_property, $prop, [ 'id' => $res[ 'property_id' ] ] );

			if ( ! empty( $res[ 'time' ] ) && $res[ 'type' ] == 2 ) {
				$table_options = $wpdb->get_blog_prefix() . 'pro_options';
				$time_var      = $wpdb->get_var( "SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'" );
				if ( ! empty( $time_var ) ) {
					$wpdb->update( $table_options, [ 'option_value' => $res[ 'time' ] ], [ 'property_id' => $res[ 'property_id' ] ] );
				} else {
					$wpdb->insert( $table_options, [
						'option_key'   => 'time',
						'option_value' => $res[ 'time' ],
						'property_id'  => $res[ 'property_id' ]
					], [ '%s', '%d', '%d' ] );
				}
			} else {
				$table_options = $wpdb->get_blog_prefix() . 'pro_options';
				$time_var      = $wpdb->get_var( "SELECT count(*) FROM {$table_options} WHERE property_id={$res['property_id']} AND option_key='time'" );
				if ( ! empty( $time_var ) ) {
					$wpdb->delete( $table_options, [
						'option_key'  => 'time',
						'property_id' => $res[ 'property_id' ]
					], [ '%d' ] );
				}
			}
		}

	}

	function create_status_with_prop() {
		global $wpdb;
		$res = $_POST;
		if ( $res && $res[ 'position' ] !== 'no' ) {
			$table_status = $wpdb->get_blog_prefix() . 'pro_status';
			if ( $res[ 'position' ] == 0 ) {
				$wpdb->query( "UPDATE $table_status SET status_position = status_position+1 WHERE user_role='{$res['type_user']}'" );
				$wpdb->insert( $table_status, [
					'status_name'     => $res[ 'name' ],
					'status_position' => 1,
					'user_role'       => $res[ 'type_user' ]
				], [ '%s', '%d', '%s' ] );
			} else {
				$wpdb->query( "UPDATE $table_status SET status_position = status_position+1 WHERE status_position>{$res['position']} AND user_role='{$res['type_user']}'" );
				$wpdb->insert( $table_status, [
					'status_name'     => $res[ 'name' ],
					'status_position' => $res[ 'position' ] + 1,
					'user_role'       => $res[ 'type_user' ]
				], [ '%s', '%d', '%s' ] );
			}
			$id_new_status = $wpdb->insert_id;
			if ( $id_new_status ) {
				$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
				$properties       = $wpdb->get_col( "SELECT id FROM $table_properties WHERE user_role='{$res['type_user']}'" );
				$table_values     = $wpdb->get_blog_prefix() . 'pro_values';
				foreach ( $properties as $key ) {
					if ( empty( $res[ $key ] ) ) {
						$value = '0';
					} else {
						if ( $res[ $key ] == 'on' ) {
							$value = '1';
						} else {
							$value = $res[ $key ];
						}
					}
					$wpdb->insert( $table_values, [
						'status_id'      => $id_new_status,
						'property_id'    => $key,
						'property_value' => $value
					] );
				}
			}
		}
	}

	function get_status( $status_id = 0, $type_user = '' ) {

		if ( ! empty( $_POST ) ) {
			$status_id = empty( $_POST[ 'status_id' ] ) ? '' : $_POST[ 'status_id' ];
			$type_user = empty( $_POST[ 'type_user' ] ) ? '' : $_POST[ 'type_user' ];
		}

		if ( ! empty( $status_id ) && ! empty( $type_user ) ) {
			$where_and           = "AND s.id={$status_id} AND p.property_published=1";
			$property_for_status = table_properties( $type_user, $where_and, 1 );
		} else {
			$property_for_status = '';
		}

		return $property_for_status;
	}

	function edit_status() {
		global $wpdb;
		$res = $_POST;
		if ( $res ) {
			$table_status = $wpdb->get_blog_prefix() . 'pro_status';
			if ( $res[ 'position' ] != $res[ 'position_old_status' ] ) {
				if ( $res[ 'position_old_status' ] > $res[ 'position' ] ) {
					$wpdb->query( "UPDATE $table_status SET status_position = status_position+1 WHERE status_position<{$res['position_old_status']} AND status_position>{$res['position']}" );
					if ( $res[ 'position' ] == 0 ) {
						$wpdb->query( "UPDATE $table_status SET status_position = 1 WHERE id={$res['status_id']}" );
					} else {
						$wpdb->query( "UPDATE $table_status SET status_position = {$res['position']}+1 WHERE id={$res['status_id']}" );
					}
				} else {
					$wpdb->query( "UPDATE $table_status SET status_position = status_position-1 WHERE status_position>{$res['position_old_status']} AND status_position<={$res['position']}" );
					$wpdb->query( "UPDATE $table_status SET status_position = {$res['position']} WHERE id={$res['status_id']}" );
				}
			}

			$wpdb->query( "UPDATE $table_status SET status_name = '{$res['name']}' WHERE id={$res['status_id']}" );

			$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
			$properties       = $wpdb->get_col( "SELECT id FROM $table_properties" );

			$table_values = $wpdb->get_blog_prefix() . 'pro_values';
			foreach ( $properties as $key ) {
				if ( empty( $res[ $key ] ) ) {
					$value = '0';
				} else {
					if ( $res[ $key ] == 'on' ) {
						$value = '1';
					} else {
						$value = $res[ $key ];
					}
				}
				$wpdb->update( $table_values, [ 'property_value' => $value ], [
					'status_id'   => $res[ 'status_id' ],
					'property_id' => $key
				] );
			}
		}
	}

	function delete_status_with_value() {
		global $wpdb;
		$res = $_POST;
		if ( $res && $res[ 'status_id' ] ) {
			$table_status = $wpdb->get_blog_prefix() . 'pro_status';
			$wpdb->delete( $table_status, [ 'id' => $res[ 'status_id' ], 'user_role' => $res[ 'type_user' ] ], [
				'%d',
				'%s'
			] );
			$wpdb->query( "UPDATE $table_status SET status_position = status_position-1 WHERE status_position>{$res['position']} AND user_role='{$res['type_user']}'" );

			$table_values = $wpdb->get_blog_prefix() . 'pro_values';
			$wpdb->delete( $table_values, [ 'status_id' => $res[ 'status_id' ] ], [ '%d' ] );
		}
	}

	function edit_options() {
		global $wpdb;
		$res = $_POST;
		if ( $res ) {
			$table_options = $wpdb->get_blog_prefix() . 'pro_options';
			$wpdb->update( $table_options, [ 'option_value' => $res[ 'currency' ] ], [ 'option_key' => 'currency' ] );
		}
	}

	function get_pro_option( $name ) {
		global $wpdb;
		if ( $name ) {
			$table_options = $wpdb->get_blog_prefix() . 'pro_options';

			return $wpdb->get_var( "SELECT option_value FROM {$table_options} WHERE option_key = '{$name}'" );
		}

		return null;
	}

	/**
	 * Removing pro users with expired date
	 */
	function remove_pro_user_task_hook() {
		global $wpdb;
		$sql    = "SELECT id, expired_date FROM '`{$wpdb->prefix}pro_paid_users`' WHERE `expired_date` <= NOW()";
		$result = $wpdb->get_results( $sql );
		foreach ( $result as $item ) {
			$wpdb->delete( $wpdb->prefix . 'pro_paid_users', [ 'id' => $item->id ], [ '%d' ] );
		}
	}

	// Including custom functions
	require_once( __DIR__ . '/includes/func-freelancer.php' );