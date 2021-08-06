<?php
defined( 'ABSPATH' ) || exit;

function an_enj_set_screen_specific( $status, $option, $value ) {
	return (int) $value;
}

function an_enj_set_screen_all( $status, $option, $value ) {
	return ( $option === 'referral_codes_table_per_page' ) ? (int) $value : $status;
}

add_filter( 'set_screen_option_referral_codes_table_per_page', 'an_enj_set_screen_specific', 10, 3 );
add_filter( 'set-screen-option', 'an_enj_set_screen_all', 10, 3 );



function an_enj_register_referral_page(){
	$hook = add_menu_page(
		'Referral codes',
		'Referral codes',
		'manage_options',
		'referral-codes',
		'referral_codes_table_page',
		'',
		100
	);

	add_action( "load-{$hook}", 'referral_codes_table_page_load' );
}


add_action( 'admin_menu', 'an_enj_register_referral_page' );

function referral_codes_table_page_load() {
	require_once __DIR__ . '/class-Referral_Codes_List_Table.php';

	$GLOBALS['referral_codes_list_table'] = new Referral_Codes_List_Table();
}

function referral_codes_table_page() {

    global $referral_codes_list_table;
	?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

		<?php
		echo '<form action="" method="POST">';
		echo '<input type="hidden" name="search" value="' . ( isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '' ) . '" />';
		$referral_codes_list_table->search_box( 'Поиск', 'referral-find' );
		$referral_codes_list_table->display();
		echo '</form>';
		?>

    </div>
	<?php
}