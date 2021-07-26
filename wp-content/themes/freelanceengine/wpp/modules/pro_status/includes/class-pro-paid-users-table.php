<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23.02.2019
 * Time: 22:31
 */

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Pro_Paid_Users_Table extends WP_List_Table {

	public function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( [
			'singular' => 'user',     //singular name of the listed records
			'plural'   => 'users',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		] );
	}

	/**
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'user_id':
			case 'user_email':
			case 'status_name':
			case 'price':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/** ************************************************************************
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	function column_display_name( $item ) {

		// create a nonce
		$delete_nonce = wp_create_nonce( 'delete_pro_user' );

		//Build row actions
		$actions = [
			//            'edit'      => sprintf('<a href="?page=edit_paid_user&user=%s">Edit</a>', absint($item['id'])),
			'delete' => sprintf( '<a href="?page=%s&action=%s&user=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
			'view'   => sprintf( '<a href="%s/author/%s">View</a>', home_url(), $item['user_nicename'] ),
		];

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s', /*$1%s*/
			$item['display_name'], /*$2%s*/
			$item['id'], /*$3%s*/
			$this->row_actions( $actions ) );
	}

	function column_role( $item ) {
		$user_meta = get_userdata( $item['user_id'] );

		return $user_meta->roles[0];
	}

	function column_price( $item ) {
		return '$' . (int) $item['price'];
	}

	function column_order_duration( $item ) {
		return sprintf( '%1$s %2$s', $item['order_duration'], ( $item['order_duration'] > 1 ) ? 'months' : 'month' );
	}

	function column_activation_date( $item ) {
		$ad   = $item['activation_date'];
		$date = date_create_from_format( 'Y-m-d H:i:s', $ad );

		return sprintf( '<span data-activation-date="%1$s">%2$s</span>', $ad, date_format( $date, 'Y-m-d' ) );
	}

	function column_expired_date( $item ) {
		$ed   = $item['expired_date'];
		$date = date_create_from_format( 'Y-m-d H:i:s', $ed );

		return sprintf( '<span data-expired-date="%1$s">%2$s</span>', $ed, date_format( $date, 'Y-m-d' ) );
	}

	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = [ $columns, $hidden, $sortable ];


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		//        $data = $this->example_data;

		$data = self::get_pro_users();


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'expired_date'; //If no sort, default to expired date
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; //If no order, default to desc
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order

			return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
		}

		usort( $data, 'usort_reorder' );


		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );

		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page,   //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		] );
	}

	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
	//    function column_cb($item){
	//        return sprintf('<input type="checkbox" name="delete[]" value="%1$s" />', $item['id']);
	//    }

	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns() {
		$columns = [
			//            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			//            'id'     => '#',
			'display_name'    => 'User Name',
			'user_email'      => 'User Email',
			'role'            => 'Role',
			'price'           => 'Price',
			'user_id'         => 'User ID',
			'status_name'     => 'Status Name',
			'order_duration'  => 'Order Duration',
			'activation_date' => 'Activation Date',
			'expired_date'    => 'Expired Date',
		];

		return $columns;
	}

	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable:
	 *               'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = [

			//            'id'     => ['id', false],     //true means it's already sorted
			'status_name'     => [ 'status_name', false ],
			'order_duration'  => [ 'order_duration', false ],
			'activation_date' => [ 'activation_date', false ],
			'expired_date'    => [ 'expired_date', false ],

		];

		return $sortable_columns;
	}

	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	//    function get_bulk_actions() {
	//        $actions = array(
	//            'delete'    => 'Delete'
	//        );
	//        return $actions;
	//    }

	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );


			if ( ! wp_verify_nonce( $nonce, 'delete_pro_user' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_user( absint( $_GET['user'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}
		}

	}

	/**
	 * Delete a user record.
	 *
	 * @param int $id user ID
	 */
	public static function delete_user( $id ) {
		global $wpdb;

		//        $wpdb->delete("{$wpdb->prefix}pro_paid_users",['id' => $id],['%d']);

		$user_id = $wpdb->get_var( "SELECT user_id FROM {$wpdb->prefix}pro_paid_users WHERE id={$id}" );

		if ( ae_user_role( $user_id ) == FREELANCER ) {
			$wpdb->update( "{$wpdb->prefix}pro_paid_users", [ 'status_id' => PRO_BASIC_STATUS_FREELANCER ], [ 'id' => $id ] );
		} else {
			$wpdb->update( "{$wpdb->prefix}pro_paid_users", [ 'status_id' => PRO_BASIC_STATUS_EMPLOYER ], [ 'id' => $id ] );
		}
	}

	/**
	 * Retrieve pro users data from database
	 *
	 * @return array|object|null
	 */
	public static function get_pro_users() {
		global $wpdb;

		$sql = "
                SELECT ppu.id, ppu.user_id, ppu.order_duration, ppu.price, ppu.activation_date, ppu.expired_date, u.user_nicename, u.user_email, u.display_name, ps.status_name  
                FROM {$wpdb->prefix}pro_paid_users as ppu
                LEFT JOIN {$wpdb->prefix}users as u ON ppu.user_id = u.id
                LEFT JOIN {$wpdb->prefix}pro_status as ps ON ppu.status_id = ps.id
               ";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;

	}
}

/**
 * Register the page
 */
//function add_menu_pro_users() {
//    add_submenu_page(
//        'pro-status',
//        'Paid Users',
//        'Pro Paid Users',
//        'manage_options',
//        'paid_users',
//        'render_list_paid_users'
//    );
//
////    add_submenu_page(
////        null,
////        'My Custom Submenu Page',
////        'My Custom Submenu Page',
////        'manage_options',
////        'edit_paid_user',
////        'render_edit_paid_user'
////    );
//}
//add_action( 'admin_menu', 'add_menu_pro_users' );


function render_list_paid_users() {
	//Create an instance of our package class...
	$pro_Paid_Users_Table = new Pro_Paid_Users_Table();
	//Fetch, prepare, sort, and filter our data...
	$pro_Paid_Users_Table->prepare_items();
	?>
    <div class="wrap">
        <h1>List Paid Users</h1>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="pro-paid-users-table" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <!-- Now we can render the completed list table -->
			<?php $pro_Paid_Users_Table->display() ?>
        </form>
    </div>
	<?php
}

/*function render_edit_paid_user() {
	global $wpdb;
	echo '<h2>Edit Paid User</h2>';

	*/ ?><!--

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

--><?php
/*

    $user_id = absint($_GET['user']);

    $result = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}pro_paid_users WHERE id = {$user_id}" );

    if (!$result) wp_die('<div class="alert alert-danger"><strong>Current user not found!</strong></div>');



    var_dump($result);




//        wp_redirect( esc_url( add_query_arg() ) );
//        exit;
//    }


}*/
