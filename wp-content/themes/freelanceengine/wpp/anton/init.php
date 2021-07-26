<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'set_screen_option_' . 'referral_codes_table_per_page', function ( $status, $option, $value ) {
	return (int) $value;
}, 10, 3 );

add_filter( 'set-screen-option', function ( $status, $option, $value ) {
	return ( $option == 'referral_codes_table_per_page' ) ? (int) $value : $status;
}, 10, 3 );

// создаем страницу в меню, куда выводим таблицу
add_action( 'admin_menu', function () {
	$hook = add_menu_page( 'Referral codes', 'Referral codes', 'manage_options', 'referral-codes', 'referral_codes_table_page', '', 100 );

	add_action( "load-$hook", 'referral_codes_table_page_load' );
} );

function referral_codes_table_page_load() {
	require_once __DIR__ . '/class-Referral_Codes_List_Table.php';

	// создаем экземпляр и сохраним его дальше выведем
	$GLOBALS['Referral_Codes_List_Table'] = new Referral_Codes_List_Table();
}

function referral_codes_table_page() {
	?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

		<?php
		// выводим таблицу на экран где нужно
		echo '<form action="" method="POST">';
		echo '<input type="hidden" name="search" value="' . ( isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '' ) . '" />';
		$GLOBALS['Referral_Codes_List_Table']->search_box( 'Поиск', 'referral-find' );
		$GLOBALS['Referral_Codes_List_Table']->display();
		echo '</form>';
		?>

    </div>
	<?php
}
