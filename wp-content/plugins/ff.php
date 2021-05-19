<?php
	/**
	 * Plugin Name:       Admin Table Tutorial
	 * Plugin URI:        www.vijayan.in
	 * Description:       This plugin is created for the purpose to understand the WordPress admin table.
	 * Author:            Vijayan
	 * Author URI:        www.vijayan.in
	 * Text Domain:       admin-table-tut
	 * Domain Path:       /languages
	 * Version:           0.1.0
	 * Requires at least: 5.4
	 * Requires PHP:      7.2
	 *
	 * @package         Admin_Table_Tut
	 */

	namespace Vijayan;

	defined( 'ABSPATH' ) || exit;

	/**
	 * Adding WP List table class if it's not available.
	 */
	if ( ! class_exists( \WP_List_Table::class ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	/**
	 * Class Drafts_List_Table.
	 *
	 * @since 0.1.0
	 * @package Admin_Table_Tut
	 * @see WP_List_Table
	 */
	class Drafts_List_Table extends \WP_List_Table {

		/**
		 * Const to declare number of posts to show per page in the table.
		 */
		const POSTS_PER_PAGE = 10;

		/**
		 * Property to store post types
		 *
		 * @var  array Array of post types
		 */
		private $allowed_post_types;

		/**
		 * Draft_List_Table constructor.
		 */
		public function __construct() {

			parent::__construct(
				array(
					'singular' => 'Draft',
					'plural'   => 'Drafts',
					'ajax'     => false,
				)
			);

			$this->allowed_post_types = $this->allowed_post_types();

		}

		/**
		 * Retrieve post types to be shown in the table.
		 *
		 * @return array Allowed post types in an array.
		 */
		private function allowed_post_types() {
			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );

			return $post_types;
		}

		/**
		 * Convert slug string to human readable.
		 *
		 * @param string $title String to transform human readable.
		 *
		 * @return string Human readable of the input string.
		 */
		private function human_readable( $title ) {
			return ucwords( str_replace( '_', ' ', $title ) );
		}

		/**
		 * A map method return all allowed post types to human readable format.
		 *
		 * @return array Array of allowed post types in human readable format.
		 */
		private function allowed_post_types_readable() {
			$formatted = array_map(
				array( $this, 'human_readable' ),
				$this->allowed_post_types
			);

			return $formatted;
		}

		/**
		 * Return instances post object.
		 *
		 * @return WP_Query Custom query object with passed arguments.
		 */
		protected function get_posts_object() {
			$post_types = $this->allowed_post_types;

			$post_args = array(
				'post_type'      => $post_types,
				'post_status'    => array( 'draft' ),
				'posts_per_page' => self::POSTS_PER_PAGE,
			);

			$paged = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );

			if ( $paged ) {
				$post_args['paged'] = $paged;
			}

			$post_type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );

			if ( $post_type ) {
				$post_args['post_type'] = $post_type;
			}

			$orderby = sanitize_sql_orderby( filter_input( INPUT_GET, 'orderby' ) );
			$order   = esc_sql( filter_input( INPUT_GET, 'order' ) );

			if ( empty( $orderby ) ) {
				$orderby = 'date';
			}

			if ( empty( $order ) ) {
				$order = 'DESC';
			}

			$post_args['orderby'] = $orderby;
			$post_args['order']   = $order;

			$search = esc_sql( filter_input( INPUT_GET, 's' ) );
			if ( ! empty( $search ) ) {
				$post_args['s'] = $search;
			}

			return new \WP_Query( $post_args );
		}

		/**
		 * Display text for when there are no items.
		 */
		public function no_items() {
			esc_html_e( 'No posts found.', 'admin-table-tut' );
		}

		/**
		 * The Default columns
		 *
		 * @param  array  $item        The Item being displayed.
		 * @param  string $column_name The column we're currently in.
		 * @return string              The Content to display
		 */
		public function column_default( $item, $column_name ) {
			$result = '';
			switch ( $column_name ) {
				case 'date':
					$t_time    = get_the_time( 'Y/m/d g:i:s a', $item['id'] );
					$time      = get_post_timestamp( $item['id'] );
					$time_diff = time() - $time;

					if ( $time && $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
						/* translators: %s: Human-readable time difference. */
						$h_time = sprintf( __( '%s ago', 'admin-table-tut' ), human_time_diff( $time ) );
					} else {
						$h_time = get_the_time( 'Y/m/d', $item['id'] );
					}

					$result = '<span title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $item['id'], 'date', 'list' ) . '</span>';
					break;

				case 'author':
					$result = $item['author'];
					break;

				case 'type':
					$result = $item['type'];
					break;
			}

			return $result;
		}

		/**
		 * Get list columns.
		 *
		 * @return array
		 */
		public function get_columns() {
			return array(
				'cb'     => '<input type="checkbox"/>',
				'title'  => __( 'Title', 'admin-table-tut' ),
				'type'   => __( 'Type', 'admin-table-tut' ),
				'author' => __( 'Author', 'admin-table-tut' ),
				'date'   => __( 'Date', 'admin-table-tut' ),
			);
		}

		/**
		 * Return title column.
		 *
		 * @param  array $item Item data.
		 * @return string
		 */
		public function column_title( $item ) {
			$edit_url  = get_edit_post_link( $item['id'] );
			$post_link = get_permalink( $item['id'] );

			$output = '<strong>';

			/* translators: %s: Post Title */
			$output .= '<a class="row-title" href="' . esc_url( $edit_url ) . '" aria-label="' . sprintf( __( '%s (Edit)', 'admin-table-tut' ), $item['title'] ) . '">' . esc_html( $item['title'] ) . '</a>';
			$output .= _post_states( get_post( $item['id'] ), false );
			$output .= '</strong>';

			// Get actions.
			$actions = array(
				'edit'  => '<a href="' . esc_url( $edit_url ) . '">' . __( 'Edit', 'admin-table-tut' ) . '</a>',
				'trash' => '<a href="' . esc_url( get_delete_post_link( $item['id'] ) ) . '" class="submitdelete">' . __( 'Trash', 'admin-table-tut' ) . '</a>',
				'view'  => '<a href="' . esc_url( $post_link ) . '">' . __( 'View', 'admin-table-tut' ) . '</a>',
			);

			$row_actions = array();

			foreach ( $actions as $action => $link ) {
				$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
			}

			$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

			return $output;
		}

		/**
		 * Column cb.
		 *
		 * @param  array $item Item data.
		 * @return string
		 */
		public function column_cb( $item ) {

			return sprintf(
				'<input type="checkbox" name="%1$s_id[]" value="%2$s" />',
				esc_attr( $this->_args['singular'] ),
				esc_attr( $item['id'] )
			);
		}

		/**
		 * Prepare the data for the WP List Table
		 *
		 * @return void
		 */
		public function prepare_items() {
			$columns               = $this->get_columns();
			$sortable              = $this->get_sortable_columns();
			$hidden                = array();
			$primary               = 'title';
			$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
			$data                  = array();

			$this->process_bulk_action();

			$get_posts_obj = $this->get_posts_object();

			if ( $get_posts_obj->have_posts() ) {

				while ( $get_posts_obj->have_posts() ) {

					$get_posts_obj->the_post();

					$data[ get_the_ID() ] = array(
						'id'     => get_the_ID(),
						'title'  => get_the_title(),
						'type'   => ucwords( get_post_type_object( get_post_type() )->labels->singular_name ),
						'date'   => get_post_datetime(),
						'author' => get_the_author(),
					);
				}
				wp_reset_postdata();
			}

			$this->items = $data;

			$this->set_pagination_args(
				array(
					'total_items' => $get_posts_obj->found_posts,
					'per_page'    => $get_posts_obj->post_count,
					'total_pages' => $get_posts_obj->max_num_pages,
				)
			);
		}

		/**
		 * Get bulk actions.
		 *
		 * @return array
		 */
		public function get_bulk_actions() {
			return array(
				'trash' => __( 'Move to Trash', 'admin-table-tut' ),
			);
		}

		/**
		 * Get bulk actions.
		 *
		 * @return void
		 */
		public function process_bulk_action() {
			if ( 'trash' === $this->current_action() ) {
				$post_ids = filter_input( INPUT_GET, 'draft_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

				do_action( 'qm/debug', $this->current_action());
				do_action( 'qm/debug', $_GET );
				do_action( 'qm/debug', $post_ids  );

			/*	if ( is_array( $post_ids ) ) {
					$post_ids = array_map( 'intval', $post_ids );

					if ( count( $post_ids ) ) {
						array_map( 'wp_trash_post', $post_ids );
					}
				}*/
			}
		}

		/**
		 * Generates the table navigation above or below the table
		 *
		 * @param string $which Position of the navigation, either top or bottom.
		 *
		 * @return void
		 */
		protected function display_tablenav( $which ) {
			?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>">

				<?php if ( $this->has_items() ) : ?>
					<div class="alignleft actions bulkactions">
						<?php $this->bulk_actions( $which ); ?>
					</div>
				<?php
				endif;
					$this->extra_tablenav( $which );
					$this->pagination( $which );
				?>

				<br class="clear" />
			</div>
			<?php
		}

		/**
		 * Overriden method to add dropdown filters column type.
		 *
		 * @param string $which Position of the navigation, either top or bottom.
		 *
		 * @return void
		 */
		protected function extra_tablenav( $which ) {

			if ( 'top' === $which ) {
				$drafts_dropdown_arg = array(
					'options'   => array( '' => 'All' ) + $this->allowed_post_types_readable(),
					'container' => array(
						'class' => 'alignleft actions',
					),
					'label'     => array(
						'class'      => 'screen-reader-text',
						'inner_text' => __( 'Filter by Post Type', 'admin-table-tut' ),
					),
					'select'    => array(
						'name'     => 'type',
						'id'       => 'filter-by-type',
						'selected' => filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING ),
					),
				);

				$this->html_dropdown( $drafts_dropdown_arg );

				submit_button( __( 'Filter', 'admin-table-tut' ), 'secondary', 'action', false );
			}
		}

		/**
		 * Navigation dropdown HTML generator
		 *
		 * @param array $args Argument array to generate dropdown.
		 *
		 * @return void
		 */
		private function html_dropdown( $args ) {
			?>

			<div class="<?php echo( esc_attr( $args['container']['class'] ) ); ?>">
				<label
					for="<?php echo( esc_attr( $args['select']['id'] ) ); ?>"
					class="<?php echo( esc_attr( $args['label']['class'] ) ); ?>">
				</label>
				<select
					name="<?php echo( esc_attr( $args['select']['name'] ) ); ?>"
					id="<?php echo( esc_attr( $args['select']['id'] ) ); ?>">
					<?php
						foreach ( $args['options'] as $id => $title ) {
							?>
							<option
								<?php if ( $args['select']['selected'] === $id ) { ?>
									selected="selected"
								<?php } ?>
								value="<?php echo( esc_attr( $id ) ); ?>">
								<?php echo esc_html( \ucwords( $title ) ); ?>
							</option>
							<?php
						}
					?>
				</select>
			</div>

			<?php
		}

		/**
		 * Include the columns which can be sortable.
		 *
		 * @return Array $sortable_columns Return array of sortable columns.
		 */
		public function get_sortable_columns() {

			return array(
				'title'  => array( 'title', false ),
				'type'   => array( 'type', false ),
				'date'   => array( 'date', false ),
				'author' => array( 'author', false ),
			);
		}
	}

	/**
	 * Fires in head section of admin page
	 */
	add_action(
		'admin_head',
		function() : void {
			$page = esc_attr( filter_input( INPUT_GET, 'page' ) );
			if ( 'all-drafts' !== $page ) {
				return;
			}

			echo '<style type="text/css">';
			echo '.wp-list-table .column-type { width: 10%; }';
			echo '</style>';
		}
	);

	/**
	 * Fires before the administration menu loads in the admin.
	 */
	add_action(
		'admin_menu',
		function() : void {
			add_menu_page(
				'All Drafts',
				'All Drafts',
				'manage_options',
				'all-drafts',
				__NAMESPACE__ . '\bootload_drafts_table',
				'dashicons-edit-page',
				100
			);
		}
	);

	/**
	 * This function is responsible for render the drafts table
	 */
	function bootload_drafts_table() : void {
		$drafts_table = new Drafts_List_Table();
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'All Drafts List', 'admin-table-tut' ); ?></h2>
			<form id="all-drafts" method="get">
				<input type="hidden" name="page" value="all-drafts" />

				<?php
					$drafts_table->prepare_items();
					$drafts_table->search_box( 'Search', 'search' );
					$drafts_table->display();
				?>
			</form>
		</div>
		<?php
	}
