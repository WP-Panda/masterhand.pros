<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Меню
 */
function wpp_rating_page_register() {
	add_menu_page(
		__( 'Activity Rating', WPP_TEXT_DOMAIN ),
		__( 'Activity Rating', WPP_TEXT_DOMAIN ),
		'administrator',
		'wpp_rating',
		'wpp_rating_page',
		'dashicons-database'
	);
}

add_action( 'admin_menu', 'wpp_rating_page_register', 1 );


/**
 * Настройка
 */
function register_my_setting() {
	register_setting( 'wpp_skills_group', 'wpp_skills' );
}

add_action( 'admin_init', 'register_my_setting' );


/**
 * Элемент таблицы с инпутом
 *
 * @param $val
 * @param $name
 * @param bool $disabled
 */
function wpp_line_tpl( $val, $name, $disabled = false ) {
	printf( '<td><input class="wpp-input" type="number" value="%s" name="wpp_skills[%s]"%s></td>', $val, $name, ! empty( $disabled ) ? ' disabled=disabled' : '' );
}

/**
 * Отрисовка страницы
 */
function wpp_rating_page() {

	$config = wpp_rating_config();

	$fields = $config['fields'];
	$all    = $only = [];

	//Универсальные поля
	foreach ( $fields as $key => $item ) :
		if ( 'all' === $item['for'] ) {
			$all[ $key ] = $fields[ $key ];
			unset( $fields[ $key ] );
		}
	endforeach;


	foreach ( $fields as $key => $item ) {
		$only[ $item['label'] ][] = [ $item['for'], $item['def'], $key, $item['disabled'] ?? false ];
		unset( $fields[ $key ] );
	}
	$opt = get_option( 'wpp_skills' );
	?>
    <div class="wrap">
        <style>
            input.wpp-input {
                width: 100%;
            }
        </style>
        <h1 class="wp-heading-inline"><?php _e( 'Activity Rating', WPP_TEXT_DOMAIN ); ?></h1>
        <form method="post" action="options.php">
            <table class="wp-list-table widefat fixed striped skill-table">
                <thead>
                <tr>
                    <td><?php echo $config['messages']['name'] ?></td>
                    <td><?php echo $config['messages']['freelancer'] ?></td>
                    <td><?php echo $config['messages']['employer'] ?></td>
                </thead>
                </tr>
                <tbody id="the-list">
				<?php
				settings_fields( 'wpp_skills_group' ); // название настроек

				foreach ( $all as $key => $item ) :
					$value = isset( $opt[ $key ] ) ? $opt[ $key ] : $item['def'];
					?>
                    <tr class="text-center">
                        <td>
							<?php echo $item['label'] ?>
                        </td>
                        <td class="text-center" colspan="2">
                            <input class="wpp-input" type="text" value="<?php echo $value ?>"
                                   name="wpp_skills[<?php echo $key ?>]">
                        </td>
                    </tr>
				<?php
				endforeach;
				foreach ( $only as $key => $item ) :
					?>
                    <tr class="text-center">
                        <td>
							<?php echo $key ?>
                        </td>

						<?php
						//Колонка фрилансера
						if ( ! empty( $item[0][0] ) && 'freelancer' === $item[0][0] ) {
							$value = isset( $opt[ $item[0][2] ] ) ? $opt[ $item[0][2] ] : $item[0][1];
							wpp_line_tpl( $value, $item[0][2], $item[0][3] );
						} else {
							echo '<td></td>';
						}

						//Если фрилансер пуст
						if ( ! empty( $item[0][0] ) && 'employer' === $item[0][0] ) {
							$value = isset( $opt[ $item[0][2] ] ) ? $opt[ $item[0][2] ] : $item[0][1];
							wpp_line_tpl( $value, $item[0][2], $item[0][3] );
						}

						//колонка заказчика
						if ( ! empty( $item[1][0] ) && 'employer' === $item[1][0] ) {
							$value = isset( $opt[ $item[1][2] ] ) ? $opt[ $item[1][2] ] : $item[1][1];
							wpp_line_tpl( $value, $item[1][2], $item[1][3] );
						}

						if ( empty( $item[1] ) && 'freelancer' === $item[0][0] ) {
							echo '<td></td>';
						}
						?>

                    </tr>
				<?php
				endforeach;
				?>
                </tbody>
            </table>
			<?php submit_button(); ?>
        </form>
    </div>
	<?php

}