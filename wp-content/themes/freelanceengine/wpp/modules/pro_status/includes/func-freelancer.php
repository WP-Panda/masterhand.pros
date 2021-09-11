<?php
/**
 * Функционал фрилансера
 */

function getBasicStatus( $user_role ) {
	global $wpdb;
	$table = $wpdb->get_blog_prefix() . 'pro_status';

	return $wpdb->get_var( "select s.id from {$table} s where s.user_role='{$user_role}' and s.status_position=1" );//
}

define( 'PRO_BASIC_STATUS_FREELANCER', (int) getBasicStatus( 'freelance' ) );
define( 'PRO_BASIC_STATUS_EMPLOYER', (int) getBasicStatus( 'employer' ) );

$option_for_project = wpp_additional_options();

$min_rating_for_free_show = 2.5;
// Для установления параметров для первичной фильтрации
function set_function_for_add_pro_status( $query_my ) {
	global $user_ID;


	if ( ! fre_share_role() ) {
		$role_template = 'gost';
	} else {
		$role_template = 'employer';
		if ( ae_user_role( $user_ID ) == FREELANCER ) {
			$role_template = 'freelance';
		}
	}

	if ( count( $query_my ) == 1 ) {
		if ( $query_my['post_type'] == PROJECT ) {
			$query_my['post_'] = PROJECT;
		} elseif ( $query_my['post_type'] == PROFILE ) {
			$query_my['post_'] = PROFILE;
		}
	}
	if ( $query_my && /*$query_my[ 'post_' ] == PROJECT && */
	     ( $role_template == 'freelance' || $role_template == 'gost' ) ) {
		//unset( $query_my[ 'post_' ] );
		$query_my["priority_in_list_project"] = 1;
		if ( ! is_user_logged_in() ) {
			$query_my["hidden_project"] = 0;
		} elseif ( empty( get_user_pro_status( $user_ID ) ) || getValueByProperty( get_user_pro_status( $user_ID ), 'access_to_pro_projects' ) == 0 ) {
			//            $query_my["create_pro_project"] = 0;
		}
	} elseif ( $query_my /*&& $query_my[ 'post_' ] == PROFILE*/ && ( $role_template == 'employer' || $role_template == 'gost' ) ) {
		$query_my["priority_in_list_freelancer"] = 1;
	}

	return $query_my;
}

add_filter( 'posts_join', 'filter_join_project', 10, 2 );
function filter_join_project( $join ) {
	global $wpdb, $wp_query;
	if ( empty( $_REQUEST ) ) {
		$query_my = $wp_query->query;
	} else {
		$query_my = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
	}

	if ( ! empty( $query_my['post_type'] ) ) {
		if ( $query_my['post_type'] == "project" ) {

			$query_my = set_function_for_add_pro_status( $query_my );

			if ( array_key_exists( 'priority_in_list_project', $query_my ) && $query_my['priority_in_list_project'] == 1 ) {
				if ( getIdStatusByPublicProperty( 'priority_in_list_project' ) ) {
					$join .= " LEFT JOIN {$wpdb->postmeta} priority ON ( wp_posts.ID = priority.post_id ) AND priority.meta_key='priority_in_list_project' ";
				}
			}//+ ORDER BY
			if ( array_key_exists( 'hidden_project', $query_my ) && $query_my['hidden_project'] == 0 ) {
				if ( getIdStatusByPublicProperty( 'hidden_project' ) ) {
					$join .= " LEFT JOIN {$wpdb->postmeta} hidden ON ( wp_posts.ID = hidden.post_id ) AND hidden.meta_key='hidden_project' ";
				}
			}//+ WHERE
			//        if (array_key_exists('create_pro_project', $query_my) && $query_my['create_pro_project'] == 0) {
			//            if (getIdStatusByPublicProperty('access_to_pro_projects')) {
			//                $join .= " LEFT JOIN {$wpdb->postmeta} for_pro ON ( wp_posts.ID = for_pro.post_id ) AND for_pro.meta_key='create_pro_project' ";
			//            }
			//        }//+ WHERE
		} elseif ( $query_my['post_type'] == "fre_profile" ) {

			$query_my = set_function_for_add_pro_status( $query_my );

			if ( array_key_exists( 'priority_in_list_freelancer', $query_my ) && $query_my['priority_in_list_freelancer'] == 1 ) {
				$statuses = getIdStatusByPublicProperty( 'priority_in_list_freelancer' );
				if ( $statuses ) {
					$status_id = 'in(';
					for ( $i = 0; $i < count( $statuses ) - 1; $i ++ ) {
						$status_id .= $statuses[ $i ] . ', ';
					}
					$status_id .= $statuses[ $i ] . ')';

					$join .= " LEFT JOIN wp_pro_paid_users ON wp_posts.post_author = wp_pro_paid_users.user_id AND wp_pro_paid_users.status_id " . $status_id;
				}
			}
		}
		//        file_put_contents(__DIR__ . '/c.txt', "\r\n" . '$join-' . json_encode($join, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);
	}

	return $join;
}

add_filter( 'posts_where', 'filter_where_project', 10, 2 );
function filter_where_project( $where ) {
	global $wp_query;
	if ( empty( $_REQUEST ) ) {
		$query_my = $wp_query->query;
	} else {
		$query_my = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
	}

	if ( ! empty( $query_my['post_type'] ) ) {
		if ( $query_my['post_type'] == "project" ) {

			$query_my = set_function_for_add_pro_status( $query_my );

			if ( array_key_exists( 'hidden_project', $query_my ) && $query_my['hidden_project'] == 0 ) {
				if ( getIdStatusByPublicProperty( 'hidden_project' ) ) {
					//                $where .= " AND hidden.meta_key = 'hidden_project' AND hidden.meta_value <> '1' ";
					$where .= " AND hidden.meta_value IS NULL ";
				}
			}
			//        if (array_key_exists('create_pro_project', $query_my) && $query_my['create_pro_project'] == 0) {
			//            if (getIdStatusByPublicProperty('access_to_pro_projects')) {
			//                $where .= " AND for_pro.meta_key = 'create_pro_project' AND for_pro.meta_value = '0' ";
			//            }
			//        }
			//        file_put_contents(__DIR__ . '/c.txt', "\r\n" . '$where-' . json_encode($where, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);
		}
	}

	return $where;
}

add_filter( 'posts_orderby', 'filter_orderby_project', 10, 2 );
function filter_orderby_project( $orderby ) {
	global $wp_query;
	if ( empty( $_REQUEST ) ) {
		$query_my = $wp_query->query;
	} else {
		$query_my = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
	}

	if ( ! empty( $query_my['post_type'] ) ) {
		if ( $query_my['post_type'] == "project" ) {

			$query_my = set_function_for_add_pro_status( $query_my );

			if ( array_key_exists( 'priority_in_list_project', $query_my ) && $query_my['priority_in_list_project'] == 1 ) {
				if ( getIdStatusByPublicProperty( 'priority_in_list_project' ) ) {
					$orderby = " priority.meta_value DESC, " . $orderby;
				}
			}

			//        file_put_contents(__DIR__ . '/c.txt', "\r\n" . '$orderby-' . json_encode($orderby, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), FILE_APPEND);
		} elseif ( $query_my['post_type'] == "fre_profile" ) {

			$query_my    = set_function_for_add_pro_status( $query_my );
			$str_orderby = '';
			if ( array_key_exists( 'priority_in_list_freelancer', $query_my ) && $query_my['priority_in_list_freelancer'] == 1 ) {
				$str_orderby .= " wp_pro_paid_users.status_id DESC, ";
			}
			if ( ! empty( $query_my['orderby'] ) && $query_my['orderby'] == 'rating' ) {
				$str_orderby .= " rating DESC, ";
			}
			$orderby = $str_orderby . $orderby;
		}
	}

	return $orderby;
}

// Get user pro status
function get_user_pro_status( $userId ) {
	global $wpdb;
	$result = null;

	if ( intval( $userId ) ) {
		$q      = $wpdb->get_row( "SELECT ppu.status_id FROM {$wpdb->prefix}pro_paid_users as ppu WHERE user_id = $userId" );
		$result = empty( $q ) ? null : $q->status_id;
	}

	// если нет про-статуса
	if ( empty( $result ) ) {
		if ( ae_user_role( $userId ) ) {
			if ( ae_user_role( $userId ) == FREELANCER ) {
				$result = PRO_BASIC_STATUS_FREELANCER;
			} else {
				$result = PRO_BASIC_STATUS_EMPLOYER;
			}
		} else {
			$result = PRO_BASIC_STATUS_FREELANCER;
		}
	}

	return (int) $result;
}

// Get user pro expire
function get_user_pro_expire( $userId ) {
	global $wpdb;
	$result = null;

	if ( intval( $userId ) ) {
		$q      = $wpdb->get_row( "SELECT ppu.expired_date FROM {$wpdb->prefix}pro_paid_users as ppu WHERE user_id = $userId" );
		$result = empty( $q ) ? null : $q->expired_date;
	}

	return $result;
}

// Get user pro name
function get_user_pro_name( $userId ) {
	global $wpdb;
	$result      = null;
	$status_name = null;
	$table       = $wpdb->get_blog_prefix() . 'pro_status';
	$statuses    = $wpdb->get_results( "SELECT id as status_id, status_name FROM $table", ARRAY_A );

	if ( intval( $userId ) ) {
		$q      = $wpdb->get_row( "SELECT ppu.status_id FROM {$wpdb->prefix}pro_paid_users as ppu WHERE user_id = $userId" );
		$result = empty( $q ) ? null : $q->status_id;


		foreach ( $statuses as $s ) {
			if ( $s['status_id'] === $result ) {
				$status_name = $s['status_name'];
			}
		}
	}

	return $status_name;
}

// Get user pro status
function get_user_pro_status_duration( $userId ) {
	global $wpdb;
	$result = null;

	if ( intval( $userId ) ) {
		$q      = $wpdb->get_row( "SELECT ppu.order_duration FROM {$wpdb->prefix}pro_paid_users as ppu WHERE user_id = $userId" );
		$result = empty( $q ) ? null : $q->order_duration;
	}

	return (int) $result;
}

// Получить значение свойства, если нет значения вернуть false
function getIsPublishProperty( $property_nickname ) {
	global $wpdb;
	if ( $property_nickname ) {
		$value = $wpdb->get_var( "SELECT p.property_published FROM {$wpdb->prefix}pro_properties p
        WHERE p.property_nickname='{$property_nickname}'" );
		$value = $value === false ? false : (int) $value;
	} else {
		$value = false;
	}

	return $value;
}

// Получить значение свойства, если нет значения вернуть false
function getValueByProperty( $status_id, $property_nickname ) {
	global $wpdb;
	if ( $status_id && $property_nickname ) {
		$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
		$table_values     = $wpdb->get_blog_prefix() . 'pro_values';
		$value            = $wpdb->get_var( "SELECT v.property_value FROM {$table_properties} p
                INNER JOIN {$table_values} v ON p.id=v.property_id
                WHERE v.status_id={$status_id} AND p.property_nickname='{$property_nickname}' AND p.property_published=1" );
		$value            = $value === false ? false : $value;

	} else {
		$value = false;
	}

	return $value;
}

function getNameByProperty( $property_nickname ) {
	global $wpdb;
	if ( $property_nickname ) {
		$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';

		$value = $wpdb->get_var( "SELECT property_name FROM {$table_properties}
                WHERE property_nickname='{$property_nickname}' AND property_published=1" );
		$value = $value === false ? false : $value;

	} else {
		$value = false;
	}

	return $value;
}

function getOptionsEmployer() {
	global $user_ID;
	$tab_properties = table_properties( 'employer', 'AND s.id = ' . get_user_pro_status( $user_ID ) . ' AND p.property_type <> 2 ' );
	if ( ! empty( $tab_properties ) ) {
		$option = [];
		foreach ( $tab_properties as $property ) {
			if ( ! empty( $property['property_nickname'] ) ) {
				$option[] = $property['property_nickname'];
			}
		}

		return $option;
	}

	return false;
}

// Получить id статусов со значением всех свойств опубликованных и не равных 0
function getIdStatusByPublicProperty( $property_nickname ) {
	global $wpdb;
	if ( $property_nickname ) {
		$table_properties = $wpdb->get_blog_prefix() . 'pro_properties';
		$table_values     = $wpdb->get_blog_prefix() . 'pro_values';
		$value            = $wpdb->get_col( "SELECT status_id FROM {$table_values} v
                INNER JOIN {$table_properties} p on p.id=v.property_id
                WHERE v.property_id =(select id from wp_pro_properties wp where wp.property_nickname='{$property_nickname}') AND v.property_value<>0 and p.property_published=1" );
	} else {
		$value = false;
	}

	return $value;
}

function get_name_pro_status( $status_id ) {
	global $wpdb;

	if ( ! empty( $status_id ) ) {
		return $wpdb->get_var( "SELECT status_name FROM " . $wpdb->get_blog_prefix() . "pro_status WHERE id=" . (int) $status_id );
	}

	return '';
}