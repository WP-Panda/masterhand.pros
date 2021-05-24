<?php
	/**
	 * Raw database query output.
	 *
	 * @package query-monitor
	 */

	class QM_Output_Raw_DB_Queries extends QM_Output_Raw{

		/**
		 * Collector instance.
		 *
		 * @var QM_Collector_DB_Queries Collector.
		 */
		protected $collector;

		public $query_row = 0;

		public function name() {
			return __( 'Database Queries', 'query-monitor' );
		}

		public function get_output() {
			$output = [];
			$data   = $this->collector->get_data();

			if ( empty( $data[ 'dbs' ] ) ) {
				return $output;
			}

			$dbs = [];

			foreach ( $data[ 'dbs' ] as $name => $db ) {
				$dbs[ $name ] = $this->output_queries( $name, $db, $data );
			}

			$output[ 'dbs' ] = $dbs;

			if ( ! empty( $data[ 'errors' ] ) ) {
				$output[ 'errors' ] = [
					'total'  => count( $data[ 'errors' ] ),
					'errors' => $data[ 'errors' ],
				];
			}

			if ( ! empty( $data[ 'dupes' ] ) ) {
				$dupes = $data[ 'dupes' ];

				// Filter out SQL queries that do not have dupes
				$dupes = array_filter( $dupes, [ $this->collector, 'filter_dupe_items' ] );

				// Ignore dupes from `WP_Query->set_found_posts()`
				unset( $dupes[ 'SELECT FOUND_ROWS()' ] );

				$output[ 'dupes' ] = [
					'total'   => count( $dupes ),
					'queries' => $dupes,
				];
			}

			return $output;
		}

		protected function output_queries( $name, stdClass $db, array $data ) {
			$this->query_row = 0;

			$output = [];

			if ( empty( $db->rows ) ) {
				return $output;
			}

			foreach ( $db->rows as $row ) {
				$output[] = $this->output_query_row( $row );
			}

			return [
				'total'   => $db->total_qs,
				'time'    => (float) number_format_i18n( $db->total_time, 4 ),
				'queries' => $output,
			];
		}

		protected function output_query_row( array $row ) {
			$output = [];

			$output[ 'i' ]    = ++ $this->query_row;
			$output[ 'sql' ]  = $row[ 'sql' ];
			$output[ 'time' ] = (float) number_format_i18n( $row[ 'ltime' ], 4 );

			if ( isset( $row[ 'trace' ] ) ) {
				$stack          = [];
				$filtered_trace = $row[ 'trace' ]->get_display_trace();

				foreach ( $filtered_trace as $item ) {
					$stack[] = $item[ 'display' ];
				}
			} else {
				$stack = explode( ', ', $row[ 'stack' ] );
				$stack = array_reverse( $stack );
			}

			$output[ 'stack' ]  = $stack;
			$output[ 'result' ] = $row[ 'result' ];

			return $output;
		}
	}

	function register_qm_output_raw_db_queries( array $output, QM_Collectors $collectors ) {
		$collector = QM_Collectors::get( 'db_queries' );
		if ( $collector ) {
			$output[ 'db_queries' ] = new QM_Output_Raw_DB_Queries( $collector );
		}

		return $output;
	}

	add_filter( 'qm/outputter/raw', 'register_qm_output_raw_db_queries', 20, 2 );