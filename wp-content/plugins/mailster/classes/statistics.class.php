<?php

class MailsterStatistics {

	public function __construct() {}


	/**
	 *
	 *
	 * @param unknown $range (optional)
	 * @return unknown
	 */
	public function get_dashboard( $range = '7 days' ) {

		$rawdata = $this->get_signups( strtotime( '-' . $range ), time() );

		return array(
			'labels'   => $this->get_labels( $rawdata ),
			'datasets' => $this->get_datasets( $rawdata ),
		);

	}


	/**
	 *
	 *
	 * @param unknown $rawdata
	 * @return unknown
	 */
	private function get_labels( $rawdata ) {

		global $wp_locale;

		$dates = array_keys( $rawdata );

		$i    = 0;
		$prev = null;

		foreach ( $rawdata as $date => $count ) {
			$d   = strtotime( $date );
			$str = $wp_locale->weekday_abbrev[ $wp_locale->weekday[ date( 'w', $d ) ] ];
			if ( ! is_null( $prev ) ) {
				$grow = $count - $prev;
				if ( $grow > 0 ) {
					$str .= ' ▲+' . $this->format( $grow ) . ' ';
				} elseif ( $grow < 0 ) {
					$str .= ' ▼-' . $this->format( $grow ) . ' ';
				}
			}
			$prev        = $count;
			$dates[ $i ] = $str;
			$i++;
		}

		return $dates;

	}


	/**
	 *
	 *
	 * @param unknown $rawdata
	 * @return unknown
	 */
	private function get_datasets( $rawdata ) {

		return array(
			array(
				'data'                      => array_values( $rawdata ),
				'backgroundColor'           => 'rgba(43,179,231,0.2)',
				'borderColor'               => 'rgba(43,179,231,1)',
				'pointColor'                => 'rgba(43,179,231,1)',
				'pointBorderColor'          => 'rgba(43,179,231,1)',
				'pointBackgroundColor'      => '#fff',
				'pointHoverBackgroundColor' => 'rgba(43,179,231,1)',
			),
		);

	}


	/**
	 *
	 *
	 * @param unknown $from (optional)
	 * @param unknown $to   (optional)
	 * @return unknown
	 */
	public function get_signups( $from = null, $to = null ) {

		global $wpdb;

		$from = is_null( $from ) ? time() : $from;
		$to   = is_null( $to ) ? time() + DAY_IN_SECONDS - 1 : $to;

		$dates = $this->get_date_range( $from, $to );
		$dates = array_fill_keys( $dates, 0 );

		$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT subscribers.ID) FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id WHERE subscribers.status = 1 AND (list_subscribers.added != 0 OR list_subscribers.added IS NULL) AND IF(subscribers.confirm, subscribers.confirm, subscribers.signup) < %d", $from ) );

		$sql = "SELECT FROM_UNIXTIME(IF(subscribers.confirm, subscribers.confirm, subscribers.signup), '%Y-%m-%d') AS the_date, COUNT(DISTINCT subscribers.ID) AS increase FROM {$wpdb->prefix}mailster_subscribers AS subscribers LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS list_subscribers ON subscribers.ID = list_subscribers.subscriber_id WHERE subscribers.status = 1 AND (list_subscribers.added != 0 OR list_subscribers.added IS NULL) GROUP BY the_date HAVING the_date >= '" . date( 'Y-m-d', $from ) . "' AND the_date <= '" . date( 'Y-m-d', $to ) . "'";

		$increase_data = $wpdb->get_results( $sql );

		if ( ! empty( $increase_data ) ) {
			$increase_data = array_combine( wp_list_pluck( $increase_data, 'the_date' ), wp_list_pluck( $increase_data, 'increase' ) );
		}

		foreach ( $dates as $date => $count ) {

			if ( isset( $increase_data[ $date ] ) ) {
				$total += $increase_data[ $date ];
			}
			$dates[ $date ] = $total;
		}

		return $dates;

	}


	/**
	 *
	 *
	 * @param unknown $first
	 * @param unknown $last
	 * @param unknown $step   (optional)
	 * @param unknown $format (optional)
	 * @return unknown
	 */
	private function get_date_range( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {

		$dates   = array();
		$current = $first;

		while ( $current <= $last ) {

			$dates[] = date( $format, $current );
			$current = strtotime( $step, $current );
		}

		return $dates;
	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	private function format( $value ) {

		$value = (int) $value;

		if ( $value >= 1000000 ) {
			return round( $value / 1000, 1 ) . 'M';
		} elseif ( $value >= 1000 ) {
			return round( $value / 1000, 1 ) . 'K';
		}

		return ! ( $value % 1 ) ? $value : '';
	}

}
