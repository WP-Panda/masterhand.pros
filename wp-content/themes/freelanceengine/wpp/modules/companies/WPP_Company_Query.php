<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

Class WPP_Company_Query {

	/**
	 * Query vars set by the user
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $query;
	/**
	 * Company Table Name
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $table_name = 'wpp_company_data';

	public function __construct( $query = '' ) {
		if ( ! empty( $query ) ) {
			$this->query( $query );
		}
	}

	/**
	 * Sets up the WordPress query by parsing query string.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $query URL query string or array of query arguments.
	 *
	 * @return array company.
	 */
	public function query( $query ) {
		$this->init();
		$this->query = wp_parse_args( $query );

		//$this->query_vars = $this->query;
		return $this->get_companies();
	}

	/**
	 * Initiates object properties and sets default values.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		global $wpdb;
		unset( $this->query );
		$this->table_name = $wpdb->prefix . $this->table_name;

	}

	/**
	 * Retrieves an array of companies based on query variables.
	 *
	 * There are a few filters and actions that can be used to modify the company
	 * database query.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array company.
	 */
	public function get_companies() {
		global $wpdb;
		$query_str = $this->query_string();

		$query_return = $wpdb->get_results( $query_str, ARRAY_A );

		return $query_return;
	}


	public function query_string() {
		global $wpdb;
		$q = &$this->query;

		$query_str  = "SELECT * FROM $this->table_name";
		$query_prep = [];

		if ( ! empty( $q['per_page'] ) && 'all' !== $q['per_page'] ) {
			$q['page']     = ! empty( $q['page'] ) ? absint( $q['page'] ) : false;
			$q['per_page'] = ! empty( $q['per_page'] ) ? absint( $q['per_page'] ) : COMPANY_PER_PAGE;
		}

		#Поиск
		if ( ! empty( $q['s'] ) && ! preg_match( '/(?:\s|^)\-/', $q['s'] ) ) {
			$like         = '%' . $wpdb->esc_like( $q['s'] ) . '%';
			$query_prep[] = $wpdb->prepare( "( `title` LIKE %s OR `address` LIKE %s)", $like, $like );
		}


		#Интервал
		if ( ! empty( $q['interval'] ) ) {
			$interval   = str_replace( ' ', '', $q['interval'] );
			$interval_1 = explode( '-', str_replace( ',', '', $interval ) );
			$interval_1 = array_map( 'absint', $interval_1 );

			$data_interval = [];
			if ( count( $interval_1 ) === 2 ) {
				$i = $interval_1[0];
				while ( $i <= $interval_1[1] ) {
					$data_interval[] = $i;
					$i ++;
				}
			} else {
				$interval_2    = explode( ',', $interval );
				$data_interval = array_map( 'absint', $interval_2 );
			}

			if ( ! empty( $data_interval ) ) {
				$data_interval_for_sql = implode( ', ', $data_interval );
				$query_prep[]          = sprintf( "`id` IN (%s)", $data_interval_for_sql );
			}
		}

		if ( ! empty( $query_prep ) ) {
			$n = 1;
			foreach ( $query_prep as $query_one_str ) {
				$pref      = 1 === $n ? ' WHERE' : ' AND';
				$query_str .= $pref . $query_one_str;
				$n ++;
			}
		}

		#Cортировка
		if ( empty( $q['order'] ) ) {
			$query_str .= " ORDER BY id DESC";
		} else {
			$query_str .= sprintf( " ORDER BY %s %s", $q['orderby'], $q['order'] );
		}

		#Пагинация
		if ( ! empty( $q['page'] ) && 'all' !== $q['per_page'] ) {
			$q['offset'] = ( (int) $q['page'] - 1 ) * $q['per_page'];

			$query_str .= ' LIMIT ' . $q['per_page'] . ' OFFSET ' . $q['offset'];
		}

		$this->query = $q;

		return $query_str;

	}

	public function found_companies() {
		global $wpdb;
		$query_str = $this->query_string();

		$found_posts_query = str_replace( [
			"SELECT *",
			' LIMIT ' . $this->query['per_page'] . ' OFFSET ' . $this->query['offset']
		], [ "SELECT COUNT(`id`) AS `found`", '' ], $query_str );


		$found_posts = $wpdb->get_var( $found_posts_query );

		return absint( $found_posts );

	}

}