<?php

$time        = microtime( true );
$request_url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;

if ( $request_url ) {
	header( 'Refresh: 15;url=' . $request_url, true );
}
ini_set( 'display_errors', true );

if ( ! defined( 'DISABLE_WP_CRON' ) ) {
	define( 'DISABLE_WP_CRON', true );
}

if ( ! defined( 'ABSPATH' ) ) {

	$p = null;
	if ( isset( $argv[2] ) ) {
		$p = rtrim( $argv[2], '/' ) . '/';
	} elseif ( isset( $_GET['path'] ) ) {
		$p = rtrim( $_GET['path'], '/' ) . '/';
	}

	if ( $p && is_dir( $p ) && file_exists( $p . 'wp-load.php' ) ) {
		$path = strtr(
			$p,
			array(
				"\x00" => '\x00',
				"\n"   => '\n',
				"\r"   => '\r',
				'\\'   => '\\\\',
				"'"    => "\'",
				'"'    => '\"',
				"\x1a" => '\x1a',
			)
		) . 'wp-load.php';
	} else {
		$path = realpath( dirname( $_SERVER['SCRIPT_NAME'] ) . '/../../../wp-load.php' );
	}

	if ( file_exists( $path ) ) {
		require_once $path;
	} else {
		die( 'WordPress root not found' );
	}
}

if ( ! defined( 'MAILSTER_VERSION' ) ) {
	wp_die( 'Please activate the Mailster Plugin!' );
}

$interval = isset( $_GET['interval'] ) ? (int) $_GET['interval'] : mailster_option( 'interval', 5 ) * 60;
if ( $request_url ) {
	header( "Refresh: $interval;url=" . $request_url, true );
}

$text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
$simple_output  = false;

if ( $simple_output ) {
	ob_start();
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo function_exists( 'get_language_attributes' ) && function_exists( 'is_rtl' ) ? get_language_attributes() : "dir='$text_direction'"; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">
	<title>Mailster <?php echo esc_html( MAILSTER_VERSION ); ?> - Cron</title>
	<meta name='robots' content='noindex,nofollow'>
	<meta http-equiv="refresh" content="<?php echo (int) $interval; ?>">
	<style type="text/css">
html{background:#f1f1f1}body{background:#fff;color:#444;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;margin:50px auto 2em;padding:1em 2em;max-width:700px;box-shadow:0 1px 3px rgba(0,0,0,.13)}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "Open Sans",sans-serif;margin:30px 0 0;padding:0 0 7px}body p,ul li{font-size:14px}body p{line-height:1.5;margin:25px 0 20px}body code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px}a{color:#0073aa}a:active,a:hover{color:#00a0d2}a:focus{color:#124964;box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);outline:0}h2{font-size:18px;font-weight:100}pre{padding:0;font-size:12px;white-space:pre;white-space:pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;white-space:-moz-pre-wrap;word-wrap:break-word}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:13px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-appearance:none;border-radius:3px;white-space:nowrap;box-sizing:border-box;box-shadow:0 1px 0 #ccc;vertical-align:top}.button.button-large{height:30px;line-height:28px;padding:0 12px 2px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#23282d}.button:focus{border-color:#5b9dd9;box-shadow:0 px rgba(0,115,170,.8);outline:0}.button:active{background:#eee;border-color:#999;box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);-webkit-transform:translateY(1px);-ms-transform:translateY(1px);transform:translateY(1px)}table{margin-bottom:20px;border-top:1px solid #ccc}table tr.odd{background-color:#fafafa}table tr.even{background-color:#fff}table,td{font-size:12px;border-collapse:collapse}td{padding:5px 9px;border-bottom:1px solid #ccc;}.error{color:#f33;}
	</style>
</head>
<body>
<div>
<?php

$secret = mailster_option( 'cron_secret' );
if ( ( isset( $_GET[ $secret ] ) ) ||
	( isset( $_GET['secret'] ) && $_GET['secret'] == $secret ) ||
	( isset( $_SERVER['HTTP_SECRET'] ) && $_SERVER['HTTP_SECRET'] == $secret ) ||
	( isset( $argv[1] ) && $argv[1] == $secret ) ||
	( defined( 'MAILSTER_CRON_SECRET' ) && MAILSTER_CRON_SECRET == $secret ) ) :

	// spawn wp_cron if it should
	if ( wp_next_scheduled( 'mailster_cron' ) - $time < 0 ) {
		spawn_cron();
	}

	if ( mailster_option( 'cron_service' ) != 'cron' ) {
		echo '<h2>' . esc_html__( 'WordPress Cron in use!', 'mailster' ) . '</h2>';
	} else {

		?>
	<script type="text/javascript">
		var finished = false;
		window.addEventListener('load', function () {
			if(!finished) document.getElementById('info').innerHTML = '<h2>Your servers execution time has been exceed!</h2><p>No worries, emails still get sent. But it\'s recommended to increase the "max_execution_time" for your server, add <code>define("WP_MEMORY_LIMIT", "256M");</code> to your wp-config.php file  or decrease the <a href="<?php echo admin_url( '/edit.php?post_type=newsletter&page=mailster_settings' ); ?>#delivery" target="_blank" rel="noopener">number of mails sent</a> maximum in the settings!</p><p><a onclick="location.reload();" class="button" id="button">OK, now reload</a></p>';
		});

	</script>
	<div id="info"><p><?php esc_html_e( 'progressing', 'mailster' ); ?>&hellip;</p></div>
		<?php

		if ( isset( $argv[2] ) ) {
			$worker = $argv[2];
		} else {
			$worker = get_query_var( '_mailster_extra' );
		}
		if ( empty( $worker ) ) {
			do_action( 'mailster_cron_autoresponder' );
			do_action( 'mailster_cron_worker' );
			do_action( 'mailster_cron_bounce' );
			do_action( 'mailster_cron_cleanup' );
		} elseif ( in_array( $worker, apply_filters( 'mailster_cron_workers', array( 'autoresponder', 'worker', 'bounce', 'cleanup' ) ) ) ) {
			echo '<h2>' . esc_html__( 'Single Cron', 'mailster' ) . ': ' . ucwords( $worker ) . '</h2>';
			do_action( 'mailster_cron_' . $worker );
		} else {
			echo '<h2>' . esc_html__( 'Invalid Cron Worker!', 'mailster' ) . '</h2>';
		}
		?>
	<p>
		<a onclick="location.reload();clearInterval(i);" class="button" id="button"><?php esc_html_e( 'reload', 'mailster' ); ?></a>
	</p>
	<p>
		<small><?php echo $time = round( microtime( true ) - $time, 4 ); ?> <?php esc_html_e( 'sec', 'mailster' ); ?>.</small>
	</p>
	<script type="text/javascript">finished = true;document.getElementById('info').innerHTML = ''</script>

<?php } ?>

<?php else : ?>
	<h2><?php esc_html_e( 'The Secret is missing or wrong!', 'mailster' ); ?></h2>
<?php endif; ?>

</div>
<script type="text/javascript">
var a = <?php echo floor( $interval ); ?>,
	b = document.getElementById('button'),
	c = document.title,
	d = b.innerHTML,
	e = new Date().getTime(),
	f = setInterval(function(){
		var x = a-Math.ceil((new Date().getTime()-e)/1000),
			t = new Date(x*1000),
			h = t.getHours()-1,
			m = t.getMinutes(),
			s = t.getSeconds(),
			o = (x>=3600 ? (h<10?'0'+h:h)+':' : '')+(x>=60 ? (m<10?'0'+m:m)+':' : '' )+(s<10?'0'+s:s),
			p = '('+o+')';

		if(x<=0){
			o = '&#x27F2;';
			p = '<?php esc_html_e( 'progressing', 'mailster' ); ?>';
			clearInterval(f);
		}
	document.title = p+' '+c;
	b.innerHTML = d+' ('+o+')';
}, 1000);
</script>
</body>
</html>
<?php
if ( $simple_output ) :
	$output = ob_get_contents();
	ob_end_clean();
	$output = preg_replace( '#<a[^>]*?>.*?</a>#si', '', $output );
	$output = mailster( 'helper' )->plain_text( $output );
	echo $output;
endif;
