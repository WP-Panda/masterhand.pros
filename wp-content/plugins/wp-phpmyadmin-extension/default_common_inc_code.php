
				// ============================================================================================= //
				// ================================ wp-phpMyAdmin addition ===================================== //
				// ============================================================================================= //
				
				//disable caching, makes problem with cookie
				header('Expires: Sun, 01 Jan 2000 00:00:00 GMT');
				header("Cache-Control: no-store, no-cache, must-revalidate");	//, max-age=0  removed from some examples
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");

				// ============= INCLUDING WP CORE ================= //
				$include_core=0;
				// Including core breaks whole application
				if ($include_core)
				{
					$abspth = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))); 
					$wp_loader = $abspth."/wp-load.php";
					if(file_exists($wp_loader)){ 
						if(!defined("ABSPATH")) include_once( $wp_loader );
						if(!current_user_can("install_plugins") || !current_user_can("manage_options")){ 
							exit("no_access");
						} 
						if(session_status() == PHP_SESSION_NONE)   session_start();
						if(session_status() != PHP_SESSION_NONE)  session_write_close();      //this line is needed to close any open sessions in WP , otherwise pma errors caused
						remove_action( "shutdown",  "wp_ob_end_flush_all",   1 );
					}
					else{
						exit("wp_content_location_is_different");
					}
				}
				else
				{
					function __($str) { return __RENAMED($str);}
				}
				// ============= ## INCLUDING WP CORE ##================= //
				
				//note, cookies are nulled after the below "use" namespaces load, so lets check here.
				$file = dirname(__DIR__)."/_session_temp.php";  	if (!file_exists($file)) exit("session file doesnt exist");
				include($file);
				$your_ip 			= $_SERVER['REMOTE_ADDR'];
				$incorrect_session	= ( empty($_COOKIE[$sess_vars["name"]]) ||  $_COOKIE[$sess_vars["name"]] !=  $sess_vars["value"]);
				$incorrect_ip		= ( $sess_vars['require_ip'] && $your_ip !== $sess_vars['ip'] );
				$incorrect_time		= $sess_vars['time'] < time() - 3*60*60;
				if( $incorrect_session || $incorrect_ip || $incorrect_time )	{
					$notice = $incorrect_session ? "Session mismatch" : ($incorrect_ip ? "Your IP ($your_ip) not allowed. If your ISP provider assigns you the dynamic IP address on each request, then you can temporarily disable the checkbox <code style='background:#e7e7e7;'>Restrict access only to current IP</code> on Dashboard-PhpMyAdmin page (and after you are done with your work in PhpMyAdmin, enable that checkbox again, so you dont leave it unchecked)." : ($incorrect_time ? "Session time expired" : "")); 
					exit($notice ." <br/>Now, go back to Dashboard-PhpMyAdmin page, and then click <b>Login phpMyAdmin</b> button again. If you still have problems, open ticket at <a href=\"https://wordpress.org/support/plugin/wp-phpmyadmin-extension/\">Support pages</a>, probably something breaks a normal page-load in dashboard.");
				}
				else{
					define('wp_pma_allowed', true);
				}

				
				// ======== manual stripslashes_deep ========
				function _wpe_array_map_deep( $value, $callback ) 
				{
					if ( is_array( $value ) ) {
						foreach ( $value as $index => $item ) {
								$value[ $index ] = _wpe_array_map_deep( $item, $callback );
						}
					} elseif ( is_object( $value ) ) {
						$object_vars = get_object_vars( $value );
						foreach ( $object_vars as $property_name => $property_value ) {
								$value->$property_name = _wpe_array_map_deep( $property_value, $callback );
						}
					} else {
						$value = call_user_func( $callback, $value );
					}
					return $value;
				}
				function _wpe_stripslashes_from_strings_only( $value ) { return is_string($value) ? stripslashes( $value ) : $value;	}
				function _wpe_stripslashes_deep($value){ return _wpe_array_map_deep($value, '_wpe_stripslashes_from_strings_only'); }
				// ================================================
					

				$strip_in_core = false ; //get_magic_quotes_gpc()
				if ($sess_vars["strip_slashes"] || ($include_core && $strip_in_core) )
				{ 
					$_GET       = array_map("_wpe_stripslashes_deep", $_GET);
					$_POST      = array_map("_wpe_stripslashes_deep", $_POST);
					$_REQUEST   = array_map("_wpe_stripslashes_deep", $_REQUEST);
					// $_SERVER    = array_map("_wpe_stripslashes_deep", $_SERVER);	//probably no need for this
					// $_COOKIE    = array_map("_wpe_stripslashes_deep", $_COOKIE);   //dont do this, it makes the value NULL for some reasons!
				}
					
				//$strip = false;
				//if( function_exists("set_magic_quotes_runtime") && function_exists("get_magic_quotes_runtime") ){
				//    $strip = true;
				//    set_magic_quotes_runtime(false);
				//}
				// ============================================================================================= //
				// ============================================================================================= //
				// ============================================================================================= //


