<?php

if( ! is_admin() ) return;


AJAXS_options_page::init();

final class AJAXS_options_page {

	static function init(){

		## Create a plugin settings page
		add_action( 'network_admin_menu', array( __CLASS__, 'add_submenu_page') );
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu_page') );

		## Link on the plugins page
		add_filter( 'network_admin_plugin_action_links', array( __CLASS__, 'plugin_action_links'), 10, 2 );
		add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links'), 10, 2 );

	}

	static function add_submenu_page(){

		// change mofile path if plugin installed in mu-plugins
		if( false !== strpos( AJAXS_PATH, wp_normalize_path(WPMU_PLUGIN_DIR) ) ){
			add_filter( 'load_textdomain_mofile', function( $mofile, $domain ){
				if( $domain === 'jxs' )
					$mofile = str_replace( WP_PLUGIN_DIR, WPMU_PLUGIN_DIR, $mofile );
				return $mofile;
			}, 10, 2 );
		}
		load_plugin_textdomain( 'jxs', false, basename(AJAXS_PATH) .'/languages/' );

		add_submenu_page( null, '', '', 'manage_options', 'ajaxs_opt_page', array( __CLASS__, 'options_page_out') );
	}

	static function plugin_action_links( $actions, $plugin_file ){

		if( false !== strpos( $plugin_file, basename(AJAXS_PATH) ) ){
			$optfile = (false !== strpos( current_filter(), 'network_' )) ? 'settings.php' : 'options.php';
			array_unshift( $actions, '<a href="'. $optfile .'?page=ajaxs_opt_page">'. __('Settings') .'</a>' );
		}

		return $actions;
	}

	static function options_page_out(){

		//register_setting( 'ajxs_options_group', AJAXS_OPTNAME, 'ajxs_sanitize_opts' );

		add_settings_section( 'ajxs_section', __('Ajax Simply Settings','jxs'), '', 'ajaxs_opt_section' );

		$id = 'allow_nonce';
		add_settings_field( $id, __('Default nonce check','jxs'), array(__CLASS__, 'opt_field'), 'ajaxs_opt_section', 'ajxs_section', array(
			'id'    => $id,
			'title' => __('Check default nonce code for all ajaxs requests. Recommended if your site don`t use page cache plugin like WP Super Cache.','jxs'),
		) );

		$id = 'use_inline_js';
		add_settings_field( $id, __('Use inline js','jxs'), array(__CLASS__, 'opt_field'), 'ajaxs_opt_section', 'ajxs_section', array(
			'id'    => $id,
			'title' => __('Include js into HTML code, but don`t connect it as a file.','jxs'),
		) );

		$id = 'front_request_file';
		add_settings_field( $id, __('Front request file','jxs'), array(__CLASS__, 'opt_field'), 'ajaxs_opt_section', 'ajxs_section', array(
			'id'    => $id,
			'title' => __('Use another file for front requests (not admin-ajax.php).','jxs'),
		) );

		$id = 'post_max_size';
		add_settings_field( $id, __('Max upload file and POST request sizes','jxs'), array(__CLASS__, 'opt_field'), 'ajaxs_opt_section', 'ajxs_section', array(
			'id'    => $id,
		) );

		?>
		<div class="wrap">

			<form id="ajaxs_options_form">
				<?php do_settings_sections( 'ajaxs_opt_section' ); ?>

				<br><br>

				<div class="jxs_ajax_serult" style="float:right; width:50%; width:calc(100% - 200px); margin-top:-.8em;"></div>

				<input type="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes'); ?>" />
			</form>

		</div>
		<?php
	}

	static function opt_field( $args ){

		$opts = jxs_options();

		$key = $args['id'];
		$name = AJAXS_OPTNAME . "[$key]";

		$val = $opts[ $key ];

		if(0){}
		elseif( $key === 'allow_nonce' || $key === 'use_inline_js' ){
			echo '<label><input type="checkbox" name="'. $name .'" value="1" '. checked( 1, $val, 0 ) .' /> '. $args['title'] .'</label>';
		}
		elseif( $key === 'post_max_size' ){
			$key = 'post_max_size';
			$val = esc_attr($opts[ $key ] == ini_get($key) ? '' : $opts[ $key ]);
			echo '<input type="text" name="'. AJAXS_OPTNAME .'['.$key.']" value="'. $val .'" placeholder="'.ini_get($key).'" /> '.
				'<span class="description">POST. '. sprintf( __('Default: %s','jxs'), ini_get($key) ." &nbsp;&nbsp;&nbsp;= ini_get('$key')" ) .'</span>';

			echo '<br>';

			$key = 'upload_max_filesize';
			$val = esc_attr($opts[ $key ] == ini_get($key) ? '' : $opts[ $key ]);
			echo '<input type="text" name="'. AJAXS_OPTNAME .'['.$key.']" value="'. $val .'" placeholder="'.ini_get($key).'" /> '.
				'<span class="description">FILE. '. sprintf( __('Default: %s','jxs'), ini_get($key) ." &nbsp;&nbsp;&nbsp;= ini_get('$key')" ) .'</span>';

			echo '
			<p class="description">'.
				__('Specify the size in bytes or in sort form: 500K, 15M, 1G.','jxs') .'<br>'.
				__('If you need set the value larger than default, you must configure your server.','jxs') .'
			</p>';

		}
		elseif( $key === 'front_request_file' ){
			echo '
			<label onclick="front_file_toggle(this)">
				<input type="checkbox" name="'. $name .'" value="1" '. checked( 1, $val, 0 ) .' /> '. $args['title'] .'
			</label>

			<div id="front_request_use_fields" style="margin-left:1.8em; display:'.( $val ? 'block' : 'none' ).';">
				<p>
					'. __('By default plugin file will be used, but in some cases path to `wp-load.php` in it could be wrong.','jxs') .'
					'. __('In this case, you need to create file "front-ajaxs.php" with the code below in your theme or plugin and specify here URL to this file:','jxs') .'
				</p>
				<p>
					<input type="text" name="'. AJAXS_OPTNAME . "[front_request_url]" .'" value="'. esc_attr( $opts['front_request_url'] ) .'" style="width:50%;" placeholder="'. wp_make_link_relative(AJAXS_URL .'front-ajaxs.php') .'" />
				</p>
				<p><a href="#" onclick="jQuery(\'#front-ajaxs\').slideToggle(); return false;">'. sprintf( __('show %s code','jxs'), '"front-ajaxs.php"') .'</a></p>
				<div id="front-ajaxs" style="display:none;">
					<pre style="font-size:90%; background:#f9f9f9; padding:1em;">'. htmlspecialchars( file_get_contents( dirname(__FILE__) .'/front-ajaxs.php' ) ) .'</pre>
					<p>'. __('<b>IMPORTANT:</b> in most cases you must set correct path to wp-load.php file, see the code.','jxs') .'</p>
					<p>'. sprintf( __('Current %s:','jxs'), '<code>$_SERVER[\'DOCUMENT_ROOT\']</code>' ) .' <code>'. $_SERVER['DOCUMENT_ROOT'] .'</code></p>
				</div>
			</div>
			';

			add_action( 'admin_print_footer_scripts', function(){
				?>
				<script>
				function front_file_toggle(that){

					var $bk = jQuery('#front_request_use_fields');

					if( jQuery(that).find('input').is(':checked') )
						$bk.slideDown();
					else
						$bk.slideUp();
				}

				jQuery(document).ready(function($){
					var $form   = $('#ajaxs_options_form'),
						$submit = $form.find('#submit'),
						$serult = $form.find('.jxs_ajax_serult'),
						tmout;

					$form.on('submit', function(ev){
						ev.preventDefault();

						$submit.css({opacity:0.5});
						ajaxs( '<?= __CLASS__ ?>::ajaxs_save_options', $form, function(res){
							$serult.hide().html( res ).slideDown(300);

							clearTimeout(tmout);
							tmout = setTimeout(function(){   $serult.html('');   }, 5000);
						} )
						.always(function(){
							$submit.css({opacity:1});
						});
					});

				});
				</script>
				<?php
			}, 99 );

		}


	}

	## save options
	static function ajaxs_save_options( $jx ){

		if( ! current_user_can('manage_options') )
			return '<div class="notice is-dismissible notice-error"><p>ERROR: You are not admin</p></div>';

		// sanitize
		$opts = array_intersect_key( $jx->ajaxs_options, jxs_def_options() );
		foreach( $opts as $key => & $val ){
			if( in_array( $key, array('allow_nonce', 'use_inline_js', 'front_request_file') ) )
				$val = !! $val;
			else
				$val = sanitize_text_field( $val );
		}

		if( $done = update_site_option( AJAXS_OPTNAME, $opts ) )
			$class = 'notice-success';
		else
			$class = 'notice-info';

		return '<div class="notice is-dismissible '. $class .'"><p>'. __('Updated') .'</p></div>';
	}


}





