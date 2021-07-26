<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class WPP_Company_Table extends WP_List_Table {

	function __construct() {
		global $status, $page;

		parent::__construct( [
			'singular' => 'company',
			'plural'   => 'companies',
			'ajax'     => false
		] );
	}


	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'title':
			case 'email':
			case 'phone':
			case 'address':
			case 'email_count':
			case 'rating':
				return $item[ $column_name ];
			case 'users_mailer':
				$out = '';
				if ( ! empty( $item[ $column_name ] ) ) {
					$users = explode( ',', $item[ $column_name ] );
					$out   .= '<ul>';
					foreach ( $users as $user_id ) {
						$user = get_userdata( $user_id );
						if ( ! empty( $user ) ) :
							$out .= sprintf( '<li><a target="_blank" href="%s">%s</a></li>', admin_url( 'user-edit.php?user_id=' . $user_id ), $user->display_name );
						endif;
					}
					$out .= '</ul>';
				}

				return $out;
				break;
			case 'register':
				if ( ! empty( $item[ $column_name ] ) ) {
					return '<div class="wpp-remove-company" data-id="' . $item['id'] . '"><span class="dashicons dashicons-no-alt"></span></div><span class="wpp-c-registred">.</span>';
				}
				break;
			case 'site':
				return sprintf( '<a href="%s"><img width="180px" src="https://s.wordpress.com/mshots/v1/%s?w=180&h=150">%s</a>', $item[ $column_name ], $item[ $column_name ], $item[ $column_name ] );
				break;
			default:
				return print_r( $item, true );
				break;
		}
	}


	function get_columns() {
		$columns = [
			'cb'           => '<input type="checkbox"/>',
			'id'           => __( 'ID', 'wpp' ),
			'title'        => __( 'Name', 'wpp' ),
			'email'        => __( 'Email', 'wpp' ),
			'phone'        => __( 'Phone', 'wpp' ),
			'address'      => __( 'Address', 'wpp' ),
			'site'         => __( 'Site', 'wpp' ),
			'rating'       => __( 'Rating', 'wpp' ),
			'email_count'  => __( 'E-mails', 'wpp' ),
			'users_mailer' => __( 'Users', 'wpp' ),
			'register'     => __( 'R', 'wpp' ),
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = [
			'id'          => [ 'id', true ],
			'title'       => [ 'title', true ],
			'address'     => [ 'address', true ],
			'rating'      => [ 'rating', true ],
			'email_count' => [ 'email_count', true ],
			'register'    => [ 'register', true ],
		];

		return $sortable_columns;
	}

	/**
	 * Column cb.
	 *
	 * @param  array $item Item data.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s_id[]" value="%2$s" />', esc_attr( $this->_args['singular'] ), esc_attr( $item['id'] ) );
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return [
			'trash' => __( 'Delete', 'admin-table-tut' ),
		];
	}


	/**
	 * Get bulk actions.
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		if ( 'trash' === $this->current_action() ) {
			$company_ids = filter_input( INPUT_GET, 'company_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );


			if ( is_array( $company_ids ) ) {
				$company_ids = array_map( 'intval', $company_ids );


				if ( count( $company_ids ) ) {
					array_map( 'company_delete', $company_ids );
				}
			}
		}
	}

	function prepare_items() {
		global $wpdb;

		$database_name = $wpdb->prefix . 'wpp_company_data';
		$per_page      = COMPANY_PER_PAGE;


		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = $this->get_sortable_columns();
		$primary               = 'title';
		$this->_column_headers = [ $columns, $hidden, $sortable, $primary ];
		/*	$this->_column_headers = [ $columns, $hidden, $sortable ];*/
		$this->process_bulk_action();
		$current_page = $this->get_pagenum();

		$company_args = [
			'page'     => $current_page,
			'per_page' => $per_page
		];

		#поиск
		$search = esc_sql( filter_input( INPUT_GET, 's' ) );
		if ( ! empty( $search ) ) {
			$company_args['s'] = $search;
		}

		#интервал
		$ids_vals = filter_input( INPUT_GET, 'ids_str', FILTER_DEFAULT );
		if ( ! empty( $ids_vals ) ) {
			$company_args['interval'] = $ids_vals;
		}

		#сортировка
		$company_args['orderby'] = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id'; //If no sort, default to title
		$company_args['order']   = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc'; //If no order, default to asc

		$query_c     = new WPP_Company_Query( $company_args );
		$data        = $query_c->get_companies();
		$total_items = $query_c->found_companies();


		$this->items = $data;

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		] );
	}

	public function interval_box() {
		$ids_vals = filter_input( INPUT_GET, 'ids_str', FILTER_DEFAULT );
		?>
        <label for="ids_str">
			<?php _e( 'To get the interval, enter the larger and smaller id separated by a dash,<br> to get the individual companies, enter a list of id separated by a comma', 'wpp' ); ?>
            <br>
            <input type="text" id="ids_str" name="ids_str" value="<?php echo $ids_vals; ?>">
        </label>
		<?php submit_button( 'Get Interval', '', '', false, [ 'id' => 'interval_submit' ] );
	}

	public function modal_bulk() { ?>
        <div class="wpp_modal_bulk_window wpp_modal_edit_company">
            <div class="wpp_modal_bulk_header">
                <h2><?php _e( 'Edit Company', 'wpp' ); ?></h2>
                <div class="wpp-close"><span class="dashicons dashicons-no-alt"></span></div>
            </div>
            <form id="wpp-edit-company">
                <div class="wpp_modal_bulk_body"></div>
                <div class="wpp_modal_bulk_footer">
                    <button class="button button-primary" id="wpp-edit-company">
						<?php _e( 'Save Change', 'wpp' ); ?>
                    </button>
                </div>
            </form>
        </div>
	<?php }


	public function loader() { ?>
        <div class="wpp-loader"></div>
	<?php }

	public function bulk_import() {
		printf( '<a href="javascript:void(0);" class="button" id="export_company">%s</a><div class="wpp-export-blocks"></div>', __( 'Export', 'wpp' ) );
	}

}//class


function wpp_company_table() {
	$companies_html = new WPP_Company_Table();
	$title          = __( 'Companies', 'wpp' );
	?>
    <div class="wrap">
        <h1>
			<?php echo esc_html( $title ); ?>
        </h1>

        <form method="get" id="wpp-list-company-table">
            <input type="hidden" name="page" value="wpp_company"/>
			<?php
			$companies_html->prepare_items();
			$companies_html->interval_box();
			$companies_html->bulk_import();
			$companies_html->search_box( 'Search', 'search' );
			$companies_html->display();
			?>
        </form>
		<?php
		$companies_html->modal_bulk();
		$companies_html->loader();
		?>

    </div>
    <script>
        jQuery(function ($) {
            $(document).on('click', '.toggle-row', function (e) {
                e.preventDefault();

                let $el = $(this),
                    $td = $el.parent('td'),
                    $tr = $td.parent('tr'),
                    $id = $td.text().replace('Show more details', '');


                $('.wpp-loader').show();

                var $data = {
                    action: 'wpp_export_company_edit_load',
                    id: $id
                }

                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', $data, function (response) {

                    if (response.success) {
                        $('.wpp_modal_bulk_body').html(response.data.content)
                    }

                    $('.wpp-loader').hide();
                    $('.wpp_modal_bulk_window').show();
                });


            });

            $(document).on('click', '.wpp-close', function (e) {
                e.preventDefault();

                $('.wpp_modal_bulk_window').hide();
                $('.wpp_modal_bulk_body').html('')
            });

            /**
             *Редактирование компании
             */
            $(document).on('submit', '#wpp-edit-company', function (e) {
                e.preventDefault();
                $('.wpp-loader').show();
                var $data = {
                    action: 'wpp_export_company_edit',
                    data: $(this).serialize()
                }

                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', $data, function (response) {

                    if (response.success) {
                        window.location.reload();
                    }

                });

            })

            /**
             * Удаление элкомпании из таблицы
             */
            $(document).on('click', '.wpp-remove-company', function (e) {
                e.preventDefault();
                $('.wpp-loader').show();
                var $data = {
                    action: 'wpp_export_company_remove',
                    id: $(this).data('id')
                }

                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', $data, function (response) {

                    if (response.success) {
                        window.location.reload();
                    }

                });

            })

            /**
             * Export выборки
             */
            $(document).on('click', '#export_company', function (e) {
                e.preventDefault();

                $('.wpp-loader').show();

                var $data = {
                    action: 'wpp_interval_export',
                    form: $('#wpp-list-company-table').serialize(),
                }

                $.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', $data, function (response) {

                    if (response.success) {
                        $('.wpp-export-blocks').html(response.data.msg).show()
                    }

                    $('.wpp-loader').hide();
                });
            });
        })
    </script>
	<?php
}

/**
 * Yfgb
 */