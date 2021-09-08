<?php
/**
 * Всякие функции для разметки
 *
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */


defined( 'ABSPATH' ) || exit;

function wpp_header_data() {
	?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--old favicon commented<?php ae_favicon(); ?>-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo WPP_HOME; ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo WPP_HOME; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo WPP_HOME; ?>/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo WPP_HOME; ?>/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo WPP_HOME; ?>/favicon.png">
    <link rel="manifest" href="<?php echo WPP_HOME; ?>/site.webmanifest">
	<?php
}

add_action( 'wp_head', 'wpp_header_data', 5 );
