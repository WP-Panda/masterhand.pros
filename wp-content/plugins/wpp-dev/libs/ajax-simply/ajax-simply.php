<?php
/**
 * Plugin Name: AJAX Simply
 * Description: Allows to create AJAX applications on WordPress by simple way.
 *
 * Author URI: http://wp-kama.ru/
 * Author: Kama
 * Plugin URI:
 *
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: jxs
 * Domain Path: /languages/
 *
 * Require PHP: 5.3+
 *
 * Version: 1.3.0
 */

define( 'AJAXS_PATH',    wp_normalize_path( __DIR__ ) .'/' );
define( 'AJAXS_URL',     plugin_dir_url(__FILE__)          );
define( 'AJAXS_OPTNAME', 'ajaxs_options'                   );

require_once AJAXS_PATH . 'options-page.php';

## init plugin
add_action( (is_admin() ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts'), 'ajax_simply_enqueue_scripts', 9999 );


## helper function for get current $jx object somewhere else in ajaxs functions
function jx(){
	return AJAX_Simply_Core::$instance;
}

## default options
function jxs_def_options(){
	return array(
		'allow_nonce'        => false,
		'use_inline_js'      => false,
		'front_request_file' => false,
		'front_request_url'  => '',
		'post_max_size'       => 0, // bytes or short form
		'upload_max_filesize' => 0, // bytes or short form
	);
}

## get options
function jxs_options( $name = '' ){

	static $opts;

	if( $opts === null ){
		$opts = get_site_option( AJAXS_OPTNAME, array() ); // multisite support
		$opts = array_merge( jxs_def_options(), $opts );

		$opts['allow_nonce']         = apply_filters( 'allow_ajaxs_nonce',   $opts['allow_nonce'] );
		$opts['use_inline_js']       = apply_filters( 'ajaxs_use_inline_js', $opts['use_inline_js'] );

		// upload limits
		$post_max_size       = apply_filters( 'ajaxs_post_max_size',       $opts['post_max_size'] );
		$upload_max_filesize = apply_filters( 'ajaxs_upload_max_filesize', $opts['upload_max_filesize'] );

		if( (ini_get('post_max_size') && wp_convert_hr_to_bytes($post_max_size) > wp_convert_hr_to_bytes(ini_get('post_max_size'))) || ! $post_max_size )
			$post_max_size = ini_get('post_max_size');

		if( (ini_get('upload_max_filesize') && wp_convert_hr_to_bytes($upload_max_filesize) > wp_convert_hr_to_bytes(ini_get('upload_max_filesize'))) || ! $upload_max_filesize )
			$upload_max_filesize = ini_get('upload_max_filesize');

		$opts['post_max_size']       = $post_max_size;
		$opts['upload_max_filesize'] = $upload_max_filesize;
	}

	if( $name )
		return $opts[ $name ];

	return $opts;
}

## enqueue script in admin or front
function ajax_simply_enqueue_scripts(){

	$js           = 'ajaxs.min.js'; // ajaxs.js
	$extra_object = 'jxs';          // can't be 'ajaxs'

	$request_url = admin_url( 'admin-ajax.php', 'relative' );
	if( ! is_admin() ){
		// 'ajaxs_front_request_url' hook allow to change AJAX request URL for front
		if( $_url = apply_filters('ajaxs_front_request_url', '') )
			 $request_url = $_url;
		elseif( jxs_options('front_request_file') )
			 $request_url = jxs_options('front_request_url') ?: wp_make_link_relative(AJAXS_URL .'front-ajaxs.php');
	}
	$nonce = wp_create_nonce( 'ajaxs_action' );
	$extra_data = array(
		'url'                 => "$request_url?action=ajaxs_action&ajaxs_nonce=$nonce&jxs_act=",
		'post_max_size'       => wp_convert_hr_to_bytes( jxs_options('post_max_size') ),
		'upload_max_filesize' => wp_convert_hr_to_bytes( jxs_options('upload_max_filesize') ),
	);

	// check jquery existence
	if( wp_script_is( 'jquery-core', 'enqueued' ) ){
		$handler = 'jquery-core';
	}
	else {
		$handler = 'jquery';

		if( ! wp_script_is( 'jquery', 'registered' ) ){
			add_action( 'wp_footer', function(){
				echo '<script>console.error("ERROR: Ajax Simply requires jQuery! jQuery was force registered and added!");</script>'."\n";
			} );

			wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', false, null, true ); // 3.3.1 2.2.4
			wp_enqueue_script( 'jquery' );
		}
		elseif( ! wp_script_is( 'jquery', 'enqueued' ) ){
			wp_enqueue_script( 'jquery' );
		}
	}

	// inline script
	if( jxs_options('use_inline_js') ){
		wp_add_inline_script( $handler, "var $extra_object = " . wp_json_encode( $extra_data ) . ';' . file_get_contents( AJAXS_PATH . $js ) );
	}
	// enqueue script
	else {
		$data = get_file_data( __FILE__, array('ver'=>'Version') );
		$ver = WP_DEBUG ? filemtime( AJAXS_PATH . $js ) : $data['ver'];

		wp_enqueue_script( 'ajaxs_script', AJAXS_URL . $js, array( $handler ), $ver, true );
		wp_localize_script( 'ajaxs_script', $extra_object, $extra_data );
	}

}

## DOING_AJAX - DOING AJAXS - INIT all in earle state
if( isset($_REQUEST['jxs_act']) ){

	// @ ini_set( 'display_errors', 1 ); // no need - works on any state of 'display_errors' - 0 or 1

	// when handler function echo or die() string data, but not return it. Or when php errors occur.
	// or for functions like: 'wp_send_json_error()' which echo and die()
	ob_start( function($buffer){
		// check return of handler function
		if( AJAX_Simply_Core::$__buffer === null )
			AJAX_Simply_Core::$__buffer = $buffer;

		return ''; // clear original buffer: die, exit or php errors. We dont need it, as we save it...
	} );

	// catch not fatal errors in early state...
	if( WP_DEBUG && WP_DEBUG_DISPLAY ){
		set_error_handler( array('AJAX_Simply_Core', '_console_error_massage') );
	}

	// for cases when handler function uses: die, exit. And
	// catch fatal errors in early state...
	register_shutdown_function( array('AJAX_Simply_Core', '_shutdown_function') );

	// need it in early state for catching errors response...
	if( ! headers_sent() ){
		@ header( 'Content-Type: application/json; charset=' . get_option('blog_charset') );
	}

	add_action( 'wp_ajax_'.'ajaxs_action',        array( 'AJAX_Simply_Core', 'init'), 0 );
	add_action( 'wp_ajax_nopriv_'.'ajaxs_action', array( 'AJAX_Simply_Core', 'init'), 0 );

}

/**
 * @property array|int|mixed|string|null select_terminal
 */
class AJAX_Simply_Core {

	public $data     = array(); // POST data

	static $__reply  = array();

	static $__buffer = null;

	static $instance = null;

	function __construct(){}

    function set_prop( $name, $value ){
        return $this->$name = $value;
    }

	## for isset() and empty()
	function __isset( $name ){
		return $this->_get_param( $name ) !== null;
	}

	function __get( $name ){
		return $this->_get_param( $name );
	}

	function _get_param( $name ){

		if( !empty($_FILES) ){
			foreach( $_FILES as & $files )
				$files = self::_maybe_compact_files( $files );
		}

		if( isset($_FILES[ $name ]) )
			return $_FILES[ $name ];

		if( isset($this->data[ $name ]) ){
			$val = $this->data[ $name ];

			if( is_array($val) ){
				array_walk_recursive( $val, function( &$val, $key ){
					if( $val && !preg_match('/[^0-9]/',$val) && intval($val) == $val ) $val = intval($val);
					elseif( is_string($val) ) $val = trim( $val, ' ' ); // delete spaces
				});
			}
			elseif( $val && !preg_match('/[^0-9]/',$val) && intval($val) == $val ) $val = intval($val); // error ex: is_numeric('682e825771') - true
			elseif( is_string($val)  ) $val = trim( $val, ' ' ); // delete spaces

			return $val;
		}

		// at the end
		if( $name === 'files' )
			return $_FILES;

		return null;
	}

	## collects an uncomfortable array of files into compact arrays of each file and adds the resulting array to the 'compact' index.
	static function _maybe_compact_files( $files ){
		if( isset($files['compact']) )                               return $files; // already added
		if( !isset($files['name']) || ! is_array( $files['name'] ) ) return $files; // if 'name' is not an array, then the field is not 'multiple'...

		foreach( $files as $key => $data ){
			foreach( $data as $index => $val ) $files['compact'][ $index ][ $key ] = $val; // добалвяем
		}

		return $files;
	}

	static function init(){

		$_DATA = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

		// not ajaxs action
		if( empty($_REQUEST['jxs_act']) )
			return;

		if( !empty($_FILES) && !ini_get('safe_mode') ) @ set_time_limit( 300 ); // increase run time when files are uploaded

		// ajaxs_nonce - not depends on POST or GET request method
		$_DATA['ajaxs_nonce'] = isset($_REQUEST['ajaxs_nonce']) ? $_REQUEST['ajaxs_nonce'] : '';

		// action can be:               function_name | class::method
		// it will turns to:      ajaxs_function_name | AJAXS_class::method
		// or to:            ajaxs_priv_function_name | AJAXS_PRIV_class::method
		// or to:                                     | class::ajaxs_method
		// or to:                                     | class::ajaxs_priv_method
		$jxs_act = $_REQUEST['jxs_act'];
		$jxs_act = preg_replace( '~[^a-zA-Z0-9_:\->()]~', '', $jxs_act ); // delete unwonted characters
		$jxs_act = preg_replace( '~\(\)$~', '', $jxs_act ); // delete '()' at the end

		unset( $_DATA['action'], $_DATA['jxs_act'] ); // clear no need internal vars

		// init instance -----------

		$jx = self::$instance = new self;

		$jx->data = wp_unslash( $_DATA );

		// change bool types to it's type
		array_walk_recursive( $jx->data, function( &$val, $key ){
			if    ( strcasecmp($val, 'false')      == 0 ) $val = false;
			elseif( strcasecmp($val, 'true')       == 0 ) $val = true;
			elseif( strcasecmp($val, 'null')       == 0 ) $val = null;
			elseif( strcasecmp($val, 'undefined')  == 0 ) $val = null;
			// 'int' and 'string' stays raw here, it will be modified in magic properties
		});

		// basic nonce check
		if( jxs_options('allow_nonce') && ! wp_verify_nonce( $jx->ajaxs_nonce, 'ajaxs_action' ) ){
			$jx->console( 'AJAXS ERROR: wrong nonce code', 'error' );
			wp_die( -1, 403 );
		}

		$action = $jxs_act;

		// to insert General checks through a filter, before processing the request.
		// For example, if a query group requires the same authorization check,
		// in order not to write the same thing every time in the handler function, you can add a check once through this hook.
		$allow = apply_filters( 'ajaxs_allow_process', true, $action, $jx );
		if( ! $allow && $allow !== null ){
			$jx->console( 'AJAXS ERROR: process not allowed', 'error' );
			wp_die( -1, 403 );
		}

		// parse action - class::method
		if(     strpos($action, '->') ) $action = explode('->', $action ); // 'myclass->method'
		elseif( strpos($action, '::') ) $action = explode('::', $action ); // 'myclass::method'

		$actions = array();
		$fn__has_prefix = function( $string ){
			return preg_match( '/^ajaxs_/i', $string );
		};

		// class method
		if( is_array($action) ){
			list( $class, $method ) = $action;

			// add prefixes, if there are no prefix in the class and method name: 'AJAXS_' or 'AJAXS_PRIV_' (for class) и 'ajaxs_' or 'ajaxs_priv_' (for method)
			if( $fn__has_prefix($class) || $fn__has_prefix($method) ){
				$actions[] = array( $class, $method );
			}
			else {
				$actions[] = array( "AJAXS_{$class}", $method );
				$actions[] = array( $class, "ajaxs_$method" );

				if( is_user_logged_in() ){
					$actions[] = array( "AJAXS_PRIV_$class", $method );
					$actions[] = array( $class, "ajaxs_priv_$method" );
				}
			}


		}
		// function
		else {
			$action = preg_replace( '~[^A-Za-z0-9_]~', '', $action );

			if( $fn__has_prefix($action) ){
				$actions[] = $action;
			}
			else {
				$actions[] = "ajaxs_{$action}";

				if( is_user_logged_in() )
					$actions[] = "ajaxs_priv_{$action}";
			}

		}

		// CALL action

		// try to find the handler
		foreach( $actions as $_action ){
			if( is_callable($_action) ){
				$action_found = true;
				self::$__buffer = call_user_func( $_action, $jx );
			}
		}

		// no matching function - use basic hooks WP AJAX: 'wp_ajax_{$action}' or 'wp_ajax_nopriv_{$action}'
		if( empty($action_found) ){

			if ( is_user_logged_in() )
				$hook_name = "wp_ajax_{$jxs_act}";
			else
				$hook_name = "wp_ajax_nopriv_{$jxs_act}";

			$return = apply_filters( $hook_name, $jx );

			if( $return instanceof AJAX_Simply_Core )
				$jx->console( 'AJAXS ERROR: There is no function, method, hook for handle AJAXS request in PHP! Current action: "'. $jxs_act .'"', 'error' );
			else
				self::$__buffer = $return;
		}

		//ob_end_clean(); // works on exit;

		exit;
	}

	static function _shutdown_function(){

		if( WP_DEBUG && WP_DEBUG_DISPLAY ){
			AJAX_Simply_Core::_console_error_massage( error_get_last() ); // for fatal error
		}

		$reply  = self::$__reply;
		$buffer = self::$__buffer;

		// if handler function return data
		if( ! isset($reply['response']) ){
			$reply['response'] = null;

			if( $buffer !== null ){
				// $is_json - for functions like 'wp_send_json_error()'
				$is_json = is_string( $buffer ) && is_array( json_decode($buffer, true) ) && ( json_last_error() == JSON_ERROR_NONE );

				$reply['response'] = $is_json ? json_decode( $buffer ) : $buffer;
			}
		}

		// remove no need element 'response' if there is no 'extra' parameter and send response as it is
		if( empty($reply['extra']) )
			$reply = $reply['response'];

		echo wp_json_encode( $reply );
	}

	static function _console_error_massage( $args ){

		// error_get_last() has no error
		if( $args === null ) return;

		// error_get_last()
		if( is_array($args) ){
			list( $errno, $errstr, $errfile, $errline ) = array_values( $args );

			// only for fatal errors, because we cant define @suppress here
			if( ! in_array( $errno, array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING) ) )
				 return;

			$console_type = 'error'; // fatal error
		}
		// set_error_handler()
		else {
			list( $errno, $errstr, $errfile, $errline ) = func_get_args();
		}

		// for @suppress
		$errno = $errno & error_reporting();
		if( $errno == 0 ) return;

		if( ! defined('E_STRICT') )            define('E_STRICT', 2048);
		if( ! defined('E_RECOVERABLE_ERROR') ) define('E_RECOVERABLE_ERROR', 4096);

		$err_names = array(
			// fatal errors
			E_ERROR             => 'Fatal error',
			E_PARSE             => 'Parse Error',
			E_CORE_ERROR        => 'Core Error',
			E_CORE_WARNING      => 'Core Warning',
			E_COMPILE_ERROR     => 'Compile Error',
			E_COMPILE_WARNING   => 'Compile Warning',
			// other errors
			E_WARNING           => 'Warning',
			E_NOTICE            => 'Notice',
			E_STRICT            => 'Strict Notice',
			E_RECOVERABLE_ERROR => 'Recoverable Error',
			// user type errors
			E_USER_ERROR        => 'User Error',
			E_USER_WARNING      => 'User Warning',
			E_USER_NOTICE       => 'User Notice',
		);

		$err_name = "Unknown error ($errno)";
		if( isset($err_names[ $errno ]) )
			$err_name = $err_names[ $errno ];

		static $once;
		if( ! $once ){
			$once = 1;
			AJAX_Simply_Core::_static_console( 'PHP errors:' ); // title for errors
		}

		if( empty($console_type) )
			$console_type = 'log';
		elseif( in_array( $errno, array(E_WARNING, E_USER_WARNING) ) )
			$console_type = 'warn';

		AJAX_Simply_Core::_static_console( "PHP $err_name: $errstr in $errfile on line $errline\n", $console_type );

		return true; // don't execute PHP internal error handler for set_error_handler()
	}


	// RESPONSE METHODS -------------------------

	## alias of success()
	function done( $data = null ){
		$this->success( $data );
	}

	## alias of success()
	function ok( $data = null ){
		$this->success( $data );
	}

	function success( $data = null ){
		if( is_wp_error( $data ) )
			$this->error( $data );

		self::$__reply['response'] = array(
			'success' => true,
			'ok'      => true, // alias of success
			'error'   => false,
			'data'    => $data
		);

		exit;
	}

	function error( $data = null ){
		if( is_wp_error( $data ) ){
			$new_data = array();
			foreach( $data->errors as $code => $messages ){
				foreach( $messages as $message )
					$new_data[] = "$code: $message\n\n";
			}
			$data = implode( '<br><br>', $new_data );
		}

		self::$__reply['response'] = array(
			'success' => false,
			'ok'      => false, // alias of success
			'error'   => true,
			'data'    => $data
		);

		exit;
	}

	## $delay in milliseconds: 1000 = 1 second
	function reload( $delay = 0 ){
		self::$__reply['extra']['reload'] = $delay ? intval($delay) : 1;
	}

	## $delay in milliseconds: 1000 = 1 second
	function redirect( $url, $delay = 0 ){
		self::$__reply['extra']['redirect'] = array( wp_sanitize_redirect($url), $delay );
	}

	function html( $selector, $html ){
		self::$__reply['extra']['html'][] = array( $selector, $html );
	}

	## alias of console()
	function log( $params ){
		call_user_func_array( array($this, 'console'), func_get_args() );
	}

	## $type: log, warn, error. Except multiple parameters
	function console( $data, $type = 'log' ){
		$args = func_get_args();

		// if last element is: log, warn, error
		if( in_array( end($args), array('log','warn','error') ) ){
			$type = array_pop( $args ); // cut last element

			foreach( $args as $data )
				self::_static_console( $data, $type );
		}
		else {
			foreach( $args as $data )
				self::_static_console( $data, 'log' );
		}

	}

	## var_dump to console. Except multiple parameters
	function dump( $data ){
		foreach( func_get_args() as $data ){
			ob_start();
			var_dump( $data );
			$data = ob_get_clean();

			self::_static_console( $data );
		}
	}

	function alert( $data ){
		if( is_array($data) || is_object($data) ){
			$data = print_r( $data, 1 );
		}

		self::$__reply['extra']['alert'][] = $data;
	}

	function trigger( $event, $selector = false, $args = array() ){
		self::$__reply['extra']['trigger'][] = array( $event, $selector, $args );
	}

	function call( $func_name /* $param1, $param2 */ ){
		$args = array_slice( func_get_args(), 1 );

		self::$__reply['extra']['call'][] = array( $func_name, $args );
	}

	function jseval( $jscode  ){
		self::$__reply['extra']['jseval'][] = $jscode;
	}

	## normaly not used: PHP function can just return any value
	function response( $val ){
		self::$__reply['response'] = $val;
	}

	## internal do not use - uses internally for PHP errors
	static function _static_console( $data, $type = 'log' ){
		if( is_array($data) || is_object($data) ){
			$data = print_r( $data, 1 );
		}

		self::$__reply['extra']['console'][] = array( $data, $type );
	}

}