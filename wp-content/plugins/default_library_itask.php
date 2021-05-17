<?php

	// ====================================================================================================================== //
	// =================================================    iTask.software    =============================================== //
	// ====================================================================================================================== //
	// =================================     Default library used by all of our plugins   =================================== //
	// ====================================================================================================================== //
	// ====================================================================================================================== //


/**
 * Base Library Class for all our plugins.
 * We use symlinks for easy changes across all plugins : https://itask.software/blog/easiest-method-create-symlink-symbolic/ 
 *
 * @package   iTask.software plugin
 * @author    T.Todua <contact@itask.software>
 * @license   GPL-3.0+
 * @link      https://itask.software
 * @copyright 2018-2019 iTask.software
 *
*/

if(!trait_exists('default_methods__iTask_Software')) 
{
  trait default_methods__iTask_Software
  {

	public function __construct($arg1=false)
	{
		$this->is_development 	= defined("_itask_machine_") ;			// set in my_superglobals.php in devmachine
		if($this->is_development)	$this-> display_errors();

		// #### Because this is a trait, we don't use "__FILE__" & "__DIR__" here, but "Reflection". ####
		$reflection = (new \ReflectionClass(__CLASS__));
		$this->plugin_NAMESPACE	= $reflection->getNamespaceName(); 		// get parent's namespace name
		$this->prefix			= strtolower( preg_replace('![^A-Z]+!', '', $this->plugin_NAMESPACE) );// get prefix from current namespace initials (i.e. xyz)
		$this->prefix_			= $this->prefix .'_';
		$this->plugin_FILE		= $reflection->getFileName(); 			// set plugin's main file path
		$this->plugin_DIR		= dirname($this->plugin_FILE);			// set plugin's dir	path
		$this->plugin_URL		= plugin_dir_url($this->plugin_FILE);	// get plugin's dir URL
		$this->wp_URL 			= network_home_url('/');				// WP installation home
		$this->wp_FOLDER 		= network_home_url('/', 'relative');	// WP folder 
		$this->home_URL			= home_url('/');						// current sub/site home url
		$this->home_FOLDER		= home_url('/', 'relative');			// current sub/site home folder
		$this->is_https			= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443);
		$this->httpsCurrent		= $this->is_https ? 'https://' : 'http://';
		$this->httpsReal		= preg_replace('/(http(s|):\/\/)(.*)/i', '$1', $this->home_URL);
		$this->domainCurrent	= $_SERVER['HTTP_HOST'];
		$this->domainReal		= $this->getDomain($this->home_URL);
		$this->domain			= $this->httpsReal.$this->domainReal;
		$this->siteslug			= str_ireplace('.','_',   $this->domainReal);
		$this->requestURI		= $_SERVER["REQUEST_URI"];
		$this->urlAfterHome		= substr($this->requestURI, strlen($this->home_FOLDER) );
		$this->pathAfterHome	= parse_url($this->urlAfterHome, PHP_URL_PATH);
		$this->currentURL		= $this->domain . $this->requestURI;
		$this->homeUrlStripped	= $this->stripUrlPrefixes($this->home_URL);
		$this->is_localhost 	= (stripos($this->home_URL,'://127.0.0.1')!==false || stripos($this->home_URL,'://localhost')!==false );
		$this->is_settings_page = false;
		$this->options_tabs 	= [];
		
		// initial variables
		$this->my_plugin_vars();
		$this->newtork_managed = is_multisite() && $this->getNetworkedState();
		$this->opts= $this->refresh_options();								//setup final variables
		$this->refresh_options_TimeGone();
		$this->check_if_pro_plugin();
		$this->__construct_my();											//all other custom construction hooks

																																	 $pgnm = property_exists($this,'customOptsPageUrl') ? $this->customOptsPageUrl : $this->slug;
		$this->settingsPHP_page			= ( array_key_exists('menu_button_level', $this->initial_static_options) && $this->initial_static_options['menu_button_level']=="mainmenu" ) ? 'admin.php' : (is_network_admin() || $this->newtork_managed ? 'settings.php' : 'options-general.php');
		$this->plugin_page_url_current	= ( is_network_admin() || $this->newtork_managed ? network_admin_url($this->settingsPHP_page) : admin_url($this->settingsPHP_page) )	.'?page='.$pgnm; 
		$this->plugin_page_url_main		= ( is_multisite() ? network_admin_url($this->settingsPHP_page) : admin_url($this->settingsPHP_page) )	.'?page='.$pgnm; 
		$this->plugin_files		= array_merge( (property_exists($this, 'plugin_files') ? $this->plugin_files : [] ),   ['index.php'] );
		$this->translated_phrases= $this->get_option_CHOSEN('`translated_phrases', [] );
		$this->ip				= $this->get_visitor_ip();
		$this->isMobile			= false;
		$this->logs_table_name	= $GLOBALS['wpdb']->base_prefix . $this->plugin_slug_u.'_error_logs';
		$this->is_in_customizer	= (stripos($this->currentURL, admin_url('customize.php')) !== false);
		$this->myplugin_class	= 'myplugin postbox version_'. (!$this->opts['has_pro_version']  ? "free" : ($this->is_pro_legal ? "pro" : "not_pro") );
		$this->addon_namepart	= 'itask.software';
		
		$this->define_option_tabs();

		//translation hook
		add_action('init', [$this, 'load_textdomain'] );

		//==== my other default hooks ===//
		$this->setupLinksAndMenus();

		//shortcodes
		$this->shortcodes_initialize();

		// if buttons needed
		$this->tinymce_funcs();

		// for backend ajax
		add_action( 'wp_ajax_'.$this->plugin_slug_u.'_all',	[$this, 'ajax_backend_call'] );

		//activation & deactivation (empty hooks by default. all important things migrated into `refresh_options`)
		register_activation_hook( $this->plugin_FILE, [$this, 'activate']   );
		register_deactivation_hook( $this->plugin_FILE, [$this, 'deactivate']);

		add_action( 'admin_head', [$this,'admin_head_func']);
		add_action( 'current_screen', function(){ $this->admin_scripts(null); } );

		//add uninstaller file
		if(is_admin()) $this->add_default_uninstall();	//add_action( 'shutdown', [$this, 'my_shutdown_for_versioning']);

		add_action('wp',		[$this, 'flush_checkpoint'], 999);

		// functions for PRO-ADDON upload
		// add_filter( 'pre_move_uploaded_file', function( $null, $file, $new_file, $type ){ return $path; }, 10, 4);
		$this->pro_file_part = 'itask-software';
		if($this->opts['has_pro_version']) 	{
			add_filter( 'upload_mimes', [$this,'upload_mimes_filter'], 1); 
			add_filter( 'wp_handle_upload', [$this,'wp_handle_upload_filter'], 10, 2);
		}
	}

	public function setupLinksAndMenus()
	{
		// If plugin has options   
		if($this->opts['show_opts'])
		{
			//add admin menu
			if (is_multisite()){
				add_action('network_admin_menu', [$this, 'register_menus'] );
				if (!$this->newtork_managed)
					add_action('admin_menu', [$this, 'register_menus'] );
			}
			else {
				add_action('admin_menu', [$this, 'register_menus'] );
			}		
			//redirect to settings page after activation (if not bulk activation)
			add_action('activated_plugin', function($plugin) { if ($this->is_not_bulk_activation($plugin))  { exit( wp_redirect($this->plugin_page_url_main.'&isactivation') ); } } );
		}

		// add Settings & Donate buttons in plugins list
		add_filter( (is_network_admin() ? 'network_admin_' : ''). 'plugin_action_links_'.plugin_basename($this->plugin_FILE),  function($links){
			if(!$this->opts['has_pro_version'])	{ $links[] = '<a href="'.$this->static_settings['donate_url'].'">'.$this->static_settings['menu_text']['donate'].'</a>'; }
			if($this->opts['show_opts']){ $links[] = '<a href="'.$this->plugin_page_url_current.'">'.$this->static_settings['menu_text']['settings'].'</a>';  }
			if(isset($this->opts['custom_opts_page'])){ $links[] = '<a href="'.$this->opts['custom_opts_page'].'">'.$this->static_settings['menu_text']['settings'].'</a>';  }
			//if(is_network_admin() && $this->initial_static_options['allowed_on'] =='singlesite'){ unset($links['activate']); $links[] = '<b style="color:red;">'.$this->static_settings['menu_text']['deactivated_only_from'].' SUB-SITES</b>';  }
			return $links;
		});
	}
	
	public function register_menus()
	{
		$menu_button_name = (array_key_exists('menu_button_name', $this->opts) ? $this->opts['menu_button_name'] : $this->opts['name'] );
		if( array_key_exists('menu_button_level', $this->initial_static_options) && $this->initial_static_options['menu_button_level']=="mainmenu" )
																																					// icons: https://goo.gl/WXAYCi 
			add_menu_page($menu_button_name, $menu_button_name, $this->opts['required_role'] , $this->slug, [$this, 'opts_page_output_parent'], $this->opts['menu_icon'] );
		else 
			add_submenu_page($this->settingsPHP_page, $menu_button_name, $menu_button_name, $this->opts['required_role'] , $this->slug,  [$this, 'opts_page_output_parent'] );

		// if target is custom link (not options page)
		if(array_key_exists('menu_button_link', $this->opts)){
			add_action( 'admin_footer', function (){
					?>
					<script type="text/javascript">
						jQuery('a.toplevel_page_<?php echo $this->slug;?>').attr('href','<?php echo $this->opts['menu_button_link'];?>').attr('target','_blank');
					</script>
					<?php
				}
			);
		}
	}
	

	// ================  dont use activation/deactivation hooks =====================//
	public function activate($network_wide){
		if ( is_multisite() && !$network_wide && !is_network_admin() )
		{
			$text= '<h2><code>'.$this->opts['name'].'</code>: '. $this->static_settings['menu_text']['activated_only_from']. ' <b style="color:red;">NETWORK DASHBOARD</b></h2>';
			//$text .=  '<script>alert("'.strip_tags($text).'");</script>';
			die($text);
		}
		//$this->plugin_updated_hook();
		if(method_exists($this, 'activation_funcs') ) {   $this->activation_funcs();  }
	}
	/*
	public function activate_old($network_wide){
		$actKey = $this->slug.'_activat';
		$show_die=isset($_GET['action']) && $_GET['action']=='error_scrape';
	
		//if activation allowed from only on multisite or singlesite or Both?
		if(!$show_die)
		{
			if (get_site_option($actKey))
			{
				$show_die=true;
				delete_site_option($actKey);
			}
			else 
			{
				$show_die= ! $this->if_correct_activable($network_wide) ;
				if($show_die) 
				{
					//update_site_option($actKey,true);
				}
			}
		}
		
		if($show_die) {
			$text= '<h2>('.$this->opts['name'].') '. $this->static_settings['menu_text']['activated_only_from']. ' <b style="color:red;">'.strtoupper($this->opts['allowed_on']).'</b> </h2>';
			//$text .=  '<script>alert("'.strip_tags($text).'");</script>';
			die($text);
		}
		//$this->plugin_updated_hook();
		if(method_exists($this, 'activation_funcs') ) {   $this->activation_funcs();  }
	}
	public function if_correct_activable($network_wide=false)
	{
		return !is_multisite() ? true : ( $this->initial_static_options['allowed_on'] == 'both' ?  true :  (   ($this->initial_static_options['allowed_on'] =='network' && is_network_admin() ) || ( $this->initial_static_options['allowed_on'] =='singlesite' && (!$network_wide && !is_network_admin()) ) ) );
	}
	*/

	public function deactivate($network_wide){
		if(method_exists($this, 'deactivation_funcs') ) {   $this->deactivation_funcs($network_wide);  }
	}


	//add my default values
	public function my_plugin_vars($step=0)
	{
		//get default plugin data: https://goo.gl/Z3z8FW
		include_once(ABSPATH . "wp-admin/includes/plugin.php");
		$plugin_vars = $this->pluginvars();
		$this->slug			= sanitize_key($plugin_vars['TextDomain']);	//same as foldername
		$this->plugin_slug	= $this->slug;								//same as foldername
		$this->plugin_slug_u= str_replace('-','_', $this->slug);

		$domain = $this->is_development ? 'http://127.0.0.1/wp/itask.software' : 'https://itask.software';
		$this->static_settings	= $plugin_vars   +   array(
				'menu_text'			=> array(
					'donate'				=>__('Donate', 'default_methods__iTask_Software'),
					'settings'				=>__('Settings', 'default_methods__iTask_Software'),
					'open_settings'			=>__('You can access settings from dashboard of:', 'default_methods__iTask_Software'),
					'activated_only_from'	=>__('Plugin activable only from', 'default_methods__iTask_Software'),
					'deactivated_only_from'	=>__('Plugin deactivable only from', 'default_methods__iTask_Software'),
				),
				'lang'				=> $this->get_locale__SANITIZED(),
				'wp_rate_url'		=> 'https://wordpress.org/support/plugin/'.$this->slug.'/reviews/#new-post',
				'public_assets_url'	=> 'https://ps.w.org/internal-functions-for-protectpages-com-users/trunk/',
				'question_mark_icon'=> 'https://ps.w.org/internal-functions-for-protectpages-com-users/trunk/assets/question-mark-2.png',
				'donate_url'		=> 'https://paypal.me/ProtectPagesCom', // business: http://paypal.me/ProtectPagesCom   ||  personal : http://paypal.me/tazotodua
				'mail_errors'		=> 'wp_plugin_errors@itask.software',
				'licenser_domain'	=> $domain,
				'musthave_plugins'	=> $domain.'/blog/must-have-wordpress-plugins/',
				'purchase_url'		=> $domain.'/?purchase_wp_plugin='.$this->slug,
				'purchase_check'	=> $domain.'/?purchase_wp_act=',
				'wp_tt_freelancers'	=> 'https://goo.gl/wZKANN',
				'wp_fl_freelancers'	=> 'https://goo.gl/JSVy37',
				'wp_pph_freelancers'=> 'https://goo.gl/vhrqiM'
		);
		//enrich from main class
		$this->declare_settings();
		//support for old versions
		if(!property_exists($this, 'initial_static_options')) $this->initial_static_options = $this->initial_static_settings; 
 
		$this->static_settings					= $this->static_settings + $this->initial_static_options;
	}

	//load translation
	public function load_textdomain(){
		load_plugin_textdomain( $this->slug, false, basename($this->plugin_DIR). '/languages/' );
	}

	public function is_not_bulk_activation($plugin)
	{
		return ( $plugin == plugin_basename( $this->plugin_FILE ) && !((new WP_Plugins_List_Table())->current_action()=='activate-selected'));
	}

	public function pluginvars(){
		/*
		[Name] => My Plugin Name
		[PluginURI] => https://example.com
		[Version] => 1.23
		[Description] => Plugin Description. By [Author].
		[Author] => myAuthorTitle
		[AuthorURI] => https://example.com/xyz
		[TextDomain] => my-plugin-name
		[DomainPath] => /languages
		[Network] => 
		[Title] => My Plugin Name
		[AuthorName] => Author Name
		*/
		return get_plugin_data( $this->plugin_FILE, $markup = true, $translate = false);    //dont $translate, otherwise you will get error of: https://core.trac.wordpress.org/ticket/43869
	}

	//get latest options (in case there were updated,refresh them)
	public function refresh_options(){
		$this->opts	= $this->get_option_CHOSEN($this->slug, []);
		foreach($this->initial_user_options as $name=>$value){ if (!array_key_exists($name, $this->opts)) { $this->opts[$name]=$value;  $should_update=true; }  }
		$this->opts = array_merge($this->opts, $this->initial_static_options);
		$this->opts['name']		=$this->static_settings['Name'];
		$this->opts['title']	=$this->static_settings['Title'];
		$this->opts['version']	=$this->static_settings['Version'];
		if(isset($should_update)) {	$this->update_opts(); }
		return $this->opts;
	}

	public function refresh_options_TimeGone(){
		//if never updated
		if(empty($this->opts['last_update_time'])) {
			$this->opts['last_update_time'] = time();   $should_update=true;
		}
		if(empty($this->opts['last_updates'])) {
			$this->opts['last_updates'] = [];   $should_update=true;
		}
		if(empty($this->opts['fist_install_date'])) {
			$this->opts['fist_install_date'] = time();  $should_update=true;
		}
		//if plugin updated through hook or manually... to avoid complete break..
		if( empty($this->opts['last_version']) || $this->opts['last_version'] != $this->opts['version'] ){
			$this->opts['last_version'] = $this->opts['version'];
			$should_update=true;
			$reload_needed=true;
		}
		if(isset($should_update)) {	$this->update_opts(); }
		if(isset($reload_needed)) { $this->plugin_updated_hook(true); }
	}

	
	public function reset_plugin_to_defaults()
	{
		$this->update_opts([]) ;
		$this->update_option_CHOSEN('`translated_phrases',  [] ) ;
		if(property_exists($this, 'plugin_reset_callback'))   $this->plugin_reset_callback();
	}

	//update library file on activation/update
	public function plugin_updated_hook($redirect=false)
	{
		return;	
	}

	// quick method to update this plugin's opts
	public function optName($optname, $prefix=false){
		if( substr($optname,  0, 1) == '`'  ) {  $prefix=true;  $optname= substr($optname,1); }
		return ( !$prefix || stripos($optname, $this->slug) !== false )  ? $optname :  $this->slug . '_' . $optname;
	}

	public function update_opts($opts=false){
		return $this->update_option_CHOSEN($this->slug, ( $opts!==false ? $opts : $this->opts) );
	}

	public function get_option_CHOSEN($optname, $default=false        				, $prefix=false){
		return call_user_func("get_".		( $this->newtork_managed ? "site_" : "" ). "option",  $this->optName($optname, $prefix), $default );
	}
	public function update_option_CHOSEN($optname, $optvalue, $autoload=null		, $prefix=false){
		return call_user_func("update_".	( $this->newtork_managed ? "site_" : "" ). "option",  $this->optName($optname, $prefix), $optvalue, $autoload );
	}
	public function delete_option_CHOSEN($optname									, $prefix=false){
		return call_user_func("delete_".	( $this->newtork_managed ? "site_" : "" ). "option",  $this->optName($optname, $prefix) );
	}

	public function get_transient_CHOSEN($optname, $default=false        				, $prefix=false){
		return call_user_func("get_".		( $this->newtork_managed ? "site_" : "" ). "transient",  $this->optName($optname, $prefix), $default );
	}
	public function update_transient_CHOSEN($optname, $optvalue, $autoload=null		, $prefix=false){
		return call_user_func("update_".	( $this->newtork_managed ? "site_" : "" ). "transient",  $this->optName($optname, $prefix), $optvalue, $autoload );
	}
	public function delete_transient_CHOSEN($optname									, $prefix=false){
		return call_user_func("delete_".	( $this->newtork_managed ? "site_" : "" ). "transient",  $this->optName($optname, $prefix) );
	}
 
 
	public function getNetworkedState(){
		return get_site_option( $this->slug . '_network_managed', $this->initial_static_options['default_managed']=='network' );
	}
	public function updateNetworkedState($value){
		return update_site_option( $this->slug . '_network_managed', $value );
	}
	
	//when is_admin or when page is unknown (for example, custom page or "wp-login.php" or etc... )
	public function Is_Backend(){
		$includes=get_included_files();
		$path	= str_replace( ['\\','/'], DIRECTORY_SEPARATOR, ABSPATH);
		return (is_admin() || in_array($path.'wp-login.php', $includes) || in_array($path.'wp-register.php', $includes) );
		//return (!!array_intersect([$ABSPATH_MY.'wp-login.php',$ABSPATH_MY.'wp-register.php'] , get_included_files())) ;
	}
	
	public function is_gutenberg($active=true){
		return ( function_exists( 'is_gutenberg_page' ) && (!$active || is_gutenberg_page() ) );
	}
	
	public function is_gutenberg_page($active=true){
		if (is_admin()) {
			global $current_screen;
			if (!isset($current_screen)) {$current_screen = get_current_screen();}
			if ( method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor() ||  $this->is_gutenberg(true) ) {
				return true;
			}
		}
		return false;
	}

	//Get Blog slug, i.e. "subdir"  from "http://example.com/subdir/"
	public function get_blog_name(){
		if(is_multisite()){
			global $blog_id;
			$current_blog_details = !function_exists('get_blog_details') ? get_site($blog_id) : get_blog_details( ['blog_id' => $blog_id] );
			$b_slug = basename($current_blog_details->path);
			return $b_slug;
		}
		return false;
	}
	
	//CHECK IF USER IS ADMIN
	public function is_administrator()	{ if (function_exists('current_user_can') || require_once(ABSPATH.'wp-includes/pluggable.php')) return (current_user_can('install_plugins')); }

	public function phrase($key, $removed=false) { return ( isset($this->translated_phrases[$key])  ? $this->translated_phrases[$key] : $key); }
	public function string_to_truefalse($string) { return ( $string ==='true' ? true : ($string ==='false' ? false : $string)); }
	public function truefalse_to_string($string) { return ( $string === true ? 'true' : ($string ===false ? 'false' : $string)); }

	public function string_to_array($string) {  return array_map('trim', array_filter( explode(',', $string) ) ); }

	public function add_prefix_to_array_keys($array, $prefix){
		$new_array =[];
		foreach ($array as $k => $v) {
			 $new_array[$prefix.$k] = $v;
		}
		return $new_array;
	}

	public function get_visitor_ip() {
		$proxy_headers = array("CLIENT_IP", "FORWARDED", "FORWARDED_FOR", "FORWARDED_FOR_IP", "HTTP_CLIENT_IP", "HTTP_FORWARDED", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED_FOR_IP", "HTTP_PC_REMOTE_ADDR", "HTTP_PROXY_CONNECTION", "HTTP_VIA", "HTTP_X_FORWARDED", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED_FOR_IP", "HTTP_X_IMFORWARDS", "HTTP_XROXY_CONNECTION", "VIA", "X_FORWARDED", "X_FORWARDED_FOR");
		foreach($proxy_headers as $proxy_header) {
			if (isset($_SERVER[$proxy_header])) {
				if(preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $_SERVER[$proxy_header])) {
					return $_SERVER[$proxy_header];
				}
				else if (stristr(",", $_SERVER[$proxy_header]) !== FALSE) {
					$proxy_header_temp = trim(array_shift(explode(",", $_SERVER[$proxy_header])));
					if (($pos_temp = stripos($proxy_header_temp, ":")) !== FALSE) {$proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp); }
					if (preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $proxy_header_temp)) { return $proxy_header_temp; }
				}
			}
		}
		return $_SERVER["REMOTE_ADDR"];
	}

	public function arrayToObject($array) { return json_decode(json_encode($array)); }
	public function objectToArray($object){ return json_decode(json_encode($object), true); }

	public function mail_scrambler($email) {  return str_replace('@', '&#64;', $email);}

	/*
	public function shortcode_handler($atts, $content=false){
		$d=debug_backtrace()[0];
		if(!empty($d['args']))
		{
			if(!empty($d['args'][2]))
			{
				$name = $d['args'][2];
				$args = $this->shortcode_atts($name, $atts);
				return call_user_func( [$this, $name], $args, $content);
			}
		}
	}
	*/

	public function add_prefix_to_object_keys($object, $prefix){
		$new_object = new stdClass();
		foreach ($object as $k => $v) {
			$new_object->{$prefix . $k} = $v;
		}
		return $new_object;
	}

	public function dieMessage($txt){
		echo
		'<div style="padding: 50px; margin:100px auto; width:50%; text-align:center; line-height: 1.4; display:flex; justify-content:center; flex-direction:column; font-family: cursive; font-size: 1.7em; box-shadow:0px 0px 10px gray; border-radius: 10px;">'.
			 '<div><h3>'.$txt.'</h3></div>'.
		'</div>';
		exit;
	}
 
	public function create_log_table()
	{
		return $GLOBALS['wpdb']->query("CREATE TABLE IF NOT EXISTS `". $this->logs_table_name ."` (
				`id` int(50) NOT NULL AUTO_INCREMENT,
				`gmdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				`function` longtext NOT NULL,
				`function_args` longtext NOT NULL,
				`message` longtext NOT NULL, 
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			)  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci AUTO_INCREMENT=1" 
			//	)  " . $wpdb->get_charset_collate()   || DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci AUTO_INCREMENT=1
		);  // or die("error_2345_". $wpdb->print_error())	//CHARACTER SET utf8mb4
	}

	public function clear_errorslog(){
		return $GLOBALS['wpdb']->query("TRUNCATE TABLE ".$this->logs_table_name );
	} 

	public function get_errorslog(){
		return $GLOBALS['wpdb']->get_results("SELECT * from ".$this->logs_table_name);
	}

	//i.e. $this->log("couldnt get results", '<code>'.print_r($response, true).'</code>' );
	public function log( $message ="", $exception="", $retrying=false)
	{
		global $wpdb; 
		$prev	= debug_backtrace()[1];
		
		$final_msg	= ($message ? print_r($message,true) : "") .   "\r\n"    . ($exception ? print_r($exception,true) : "");
		//$res = $wpdb->insert( 'table', ['time'=>time(), 'function'=>$prev['function'], 'function'=>$prev['function'] ], ['%s', '%d' ] );
		$res = $wpdb->insert( $this->logs_table_name, $arr=[ 'gmdate'=> gmdate("Y-m-d H:i:s"), 'function'=>$prev['function'], 'function_args'=>print_r($prev['args'],true), 'message'=>print_r($message, true). "\r\n".print_r($exception, true) ] );
		if(!$res && !$retrying){
			$this->create_log_table();
			$this->log( $message, $exception, true); 
		}
		return $res;
	}

	public function send_error_mail($error){
		return wp_mail($this->static_settings['mail_errors'], 'wp plugin error at '. home_url(),  (is_array($error) ? print_r($error, true) : $error)  );
	}
	public function sanitizer($text)	{ return preg_replace('/\W/si','',$text); }
	public function remove_double_slashes($input){$x=$input;   $x=str_replace('//','/', $x);  $x=str_replace('\\\\','\\', $x);  return str_replace(':/','://',$x);}
	
	public function question_mark($text, $dialog=0) {
		$mouseover='';
		$content = '';
		if($dialog==0){
			$content = $text;
		}
		else if($dialog==1){
			$content = '';
			$mouseover = ' onmouseover="jQuery(\'#\'+this.parentNode.id).tooltip({ items:this,   content:\''.$text.'\', show: { effect: \'blind\', duration: 800 } 	}).tooltip(\'open\');"'; 	
		}
		else if($dialog==2){
			$content = '';
			$mouseover = ' onmouseover="jQuery(\'<div>'.$text.'</div>\').dialog({   modal:true,   width:600 });"';
		}
		return '<span id="xx"><img src="'. $this->static_settings['question_mark_icon'] .'" class="question_mark" style="cursor:none; width:20px;" alt="'.$content.'" title="'.$content.'" '.$mouseover.' /></span>';
	}

	public function serialize_argv($argvs)
	{
		if(empty($argvs) || !is_array($argvs)) return $argvs;
	
		$new_ar=[];
		foreach($argvs as $key=>$value)
		{
			if(stripos($value,'=')===false)
			{
				$new_ar[$key] = $value;
			}
			else{
				parse_str($argvs[$key], $params);
				$key1=array_keys($params)[0];
				if(!empty($argvs) && is_array($params))
					$new_ar[$key1] =  $params[$key1];
			}
			   
		}
		return $new_ar;
	}
	

	public function get_remote_data($url, $post_paramtrs=false,            $extra=array('schemeless'=>true, 'replace_src'=>true, 'return_array'=>false, "curl_opts"=>[]) ) { 
		// start curl
		$c = curl_init();curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		//if parameters were passed to this function, then transform into POST method.. (if you need GET request, then simply change the passed URL)
		if($post_paramtrs){ curl_setopt($c, CURLOPT_POST,TRUE);  curl_setopt($c, CURLOPT_POSTFIELDS,   (is_array($post_paramtrs) ? http_build_query($post_paramtrs) : $post_paramtrs)  ); }
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST,false); 
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($c, CURLOPT_COOKIE, 'CookieName1=Value;'); 
			$headers[]= "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:76.0) Gecko/20100101 Firefox/76.0";	 $headers[]= "Pragma: ";  $headers[]= "Cache-Control: max-age=0";
			if (!empty($post_paramtrs) && !is_array($post_paramtrs) && is_object(json_decode($post_paramtrs))){ $headers[]= 'Content-Type: application/json'; $headers[]= 'Content-Length: '.strlen($post_paramtrs); }
		curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($c, CURLOPT_MAXREDIRS, 10); 
		//if SAFE_MODE or OPEN_BASEDIR is set,then FollowLocation cant be used.. so...
		$follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;  if ($follow_allowed){curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);}
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
		curl_setopt($c, CURLOPT_REFERER, $url);    
		curl_setopt($c, CURLOPT_TIMEOUT, 60);
		curl_setopt($c, CURLOPT_AUTOREFERER, true);
		curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
		//set extra options if passed
		if(!empty($extra['curl_opts'])) foreach($extra['curl_opts'] as $key=>$value) curl_setopt($c, constant($key), $value);
		$data = curl_exec($c);
		$status=curl_getinfo($c); curl_close($c);
		// if redirected, then get that redirected page
		if($status['http_code']==301 || $status['http_code']==302) { 
			//if we FOLLOWLOCATION was not allowed, then re-get REDIRECTED URL
			//p.s. WE dont need "else", because if FOLLOWLOCATION was allowed, then we wouldnt have come to this place, because 301 could already auto-followed by curl  :)
			if (!$follow_allowed){
				//if REDIRECT URL is found in HEADER
				if(empty($redirURL)){if(!empty($status['redirect_url'])){$redirURL=$status['redirect_url'];}}
				//if REDIRECT URL is found in RESPONSE
				if(empty($redirURL)){preg_match('/(Location:|URI:)(.*?)(\r|\n)/si', $data, $m);	                if (!empty($m[2])){ $redirURL=$m[2]; } }
				//if REDIRECT URL is found in OUTPUT
				if(empty($redirURL)){preg_match('/moved\s\<a(.*?)href\=\"(.*?)\"(.*?)here\<\/a\>/si',$data,$m); if (!empty($m[1])){ $redirURL=$m[1]; } }
				//if URL found, then re-use this function again, for the found url
				if(!empty($redirURL)){$t=debug_backtrace(); return call_user_func( $t[0]["function"], trim($redirURL), $post_paramtrs);}
			}
		}
		// if not redirected,and nor "status 200" page, then error..
		elseif ( $status['http_code'] != 200 ) { $data =  "ERRORCODE22 with $url<br/><br/>Last status codes:".json_encode($status)."<br/><br/>Last data got:$data";}
		//URLS correction
		$answer = ( !empty($extra['return_array']) ? array('data'=>$data, 'header'=>$header, 'info'=>$status) : $data);
		return $answer;      
	}



	// ====================== tinymce buttons ==================== //
	public function tinymce_funcs()
	{
		// Add button in TinyMCE
		if( !empty($this->tinymce_buttons) ) {
			add_action( 'admin_init', 			function(){
					add_filter( 'mce_external_plugins',	[$this, 'tinymce_js'] );
					add_filter( 'mce_buttons_2',		[$this, 'register_buttons'] );
					//add_filter( 'tiny_mce_version',  function ( $ver ) { return $ver + 3;}  );
			} );
			//tinymce buttons if needed
			$this->tinymce_buttons_body();
			foreach($this->tinymce_buttons as $each_button){
				if( !empty($each_button["shortcode"]) ){
					add_shortcode($each_button["shortcode"], [$this, $each_button["shortcode"]] );
				}
			}
		}
	}


	
	public function array_fields($array, $parent="plugin_slug[sample][sub]", $pairs=false)
	{ 
		echo '<div class="inpHolder">';
		if( empty($array) ){
			$array = [];
		} 

		echo '<div class="inputsBlock">';
		if (is_array($array)) 
		{
			if (!$pairs)
			{
				foreach ($array as $fieldKey=>$value)
				{
					echo $this->field_out_helper1($parent, $fieldKey, $value, $pairs) ;
				}
			}
			else 
			{
				foreach ($array as $fieldKey=>$value)
				{
					echo $this->field_out_helper1($parent, $fieldKey, $value, $pairs) ;
				}
			}
		}

		$fieldKey= "abc_".rand(1,999999)*rand(1,999999);
		$sample_field = $this->field_out_helper1($parent, $fieldKey, "", $pairs, true);
		//echo $sample_field;
		echo '</div>';
		?>
				<?php $unique = $this->sanitizer($parent); ?>
		<a class="button" href="#" onclick="return <?php echo $unique;?>_addNewArrayField_k(this);" class="addNewArrayInput"><?php _e('Add New', 'default_methods__iTask_Software');?></a>
		<script>
		function <?php echo $unique;?>_addNewArrayField_k(el)
		{ 
			var rand=  Math.random().toString(36).substring(7);
			value="abc_52153157945"
			var newEl = '<?php echo $sample_field;?>'.replace(/value="<?php echo $fieldKey;?>"/g, 'value=""').replace(/<?php echo $fieldKey;?>/g, "abc_" +rand);
			el.parentNode.getElementsByClassName("inputsBlock")[0].insertAdjacentHTML("beforeend", newEl);
			return false;
		}
		</script>
		<?php
		echo '</div>';
	}

	public function field_out_helper1($parent, $key, $value, $pairs, $sample=false)
	{
		$output='<div class="eachInputBlock">';
		if (!$pairs) { 
			$output .= '<input name="'.$parent.'['.$key.']"  class="eachInput each_'.$key.' regular-text" type="text" value="'.$value.'"  placeholder="" />';
		} else { 
			
			$output .= '<input name="'.$parent.'['.$key.'][name]"  class="eachInput each_'.$key.' medium-text _key" type="text" value="'. ($sample ? '' : $key).'"  placeholder="name" />';
			$output .= '<input name="'.$parent.'['.$key.'][value]"  class="eachInput each_'.$key.' medium-text _value" type="text" value="'.$value.'"  placeholder="value" />';
		}
		$output .='</div>';
		return $output;
	}



	public function arrayFieldsResort($ar)
	{
		$new=[];
		foreach($ar as $key=>$val)
		{
			$new[ sanitize_text_field($val["name"]) ] = sanitize_text_field($val["value"]);
		}
		return $new;
	}


	public function get_fb_name_regex($fb_url){
		preg_match('/'.preg_quote('^(?:https?://)?(?:www.|m.|touch.)?(?:facebook.com|fb(?:.me|.com))/(?!$)(?:(?:\w)#!/)?(?:pages/)?(?:[\w-]/)?(?:/)?(?:profile.php?id=)?([^/?\s])(?:/|&|?)?.*$/'), $fb_url, $n);
		return $n[1];
	}
	
	// public function NonceCheck($value, $action_name){if ( !isset($value) || !wp_verify_nonce($value, $action_name) ) { die("error_5151, Refresh the page");}}	

	public function define_translations_exist(){
		//check if translations exist
		$last_vers  = get_site_option($this->slug . '_transl_lastvers');
		if( ! $last_vers || $last_vers != $this->static_settings["Version"] ){
			update_site_option('transl_lastvers', $this->static_settings["Version"]);
			$res = !empty($this->phrases_array());
			update_site_option($this->slug . '_transl_exists', $res);
			return $res;
		}
		return get_site_option($this->slug . '_transl_exists');
	}	


	// settings page
	public function define_option_tabs(){
		if(!is_admin()) return;
		
		$this->show_tabs	= $this->opts['display_tabs'];
		$this->options_tabs	= array_merge( 
			["Options"],
			$this->options_tabs, 
			property_exists($this,'shortcodes') ? ['Shortcodes'] : [],
			( $this->define_translations_exist() ? ["Translations & Phrases"] : [] ),
			["Errors-Log & Reset"]  //( ! property_exists($this, 'errors_tab') || $this->errors_tab ? ['Errors log'] :  []  )
		); 

	}

	public function options_tab($tabs_array =false){
		if(!$tabs_array)  $tabs_array = $this->options_tabs;
		$this->active_tab = $tabs_array[0];
		foreach($tabs_array as $each_tab){
			if (isset($_GET['tab']) && sanitize_key($each_tab)==$_GET['tab'])  
				$this->active_tab=$each_tab;
		}
		echo '<div class="nav-tab-wrapper customNav '. (!$this->show_tabs ? "displaynone" : "") .'">';
		foreach($tabs_array as $each_tab){
			echo '<a  href="'.add_query_arg('tab', sanitize_key($each_tab) ).'" class="nav-tab '. sanitize_key($each_tab).' '. ($this->active_tab == $each_tab ? 'nav-tab-active  whiteback' : ''). '">'. __( $each_tab,'default_methods__iTask_Software').'</a>';
		}
		echo '</div>';
	}


	public function opts_page_output_parent($args=false)
	{
		if(is_network_admin())
		{ 
			if( !empty( $_POST["mng_nonce"] ) && check_admin_referer( "nonce_mng_" . $this->slug, "mng_nonce" ) ) 
			{
				if(isset( $_POST[$this->slug]['managed_from_changer'] ) ){
					$val = sanitize_key($_POST[$this->slug]['managed_from_site']) == "network";
					$this->updateNetworkedState($val) ;
					$this->newtork_managed = $val;
				}
			} 
			?>
			<style>
			.networked_switcher_itsf { box-shadow: 0px 0px 30px black; z-index: 3; min-width:210px; background:#b5b5b5; border-radius:0 0 0 100px; padding:2px 10px 10px 30px; position: absolute; top: 0px; right: 0px; line-height:2em; }
			</style>
			<div class="networked_switcher_itsf">
			<form method="post" action="">
				<?php _e("Choose, from where this plugin settings should be managed:", 'default_methods__iTask_Software'); ?><br/>
				<?php _e("Network");?>:<input onchange="managed_from_onchanger(this)" type="radio" name="<?php echo $this->slug;?>[managed_from_site]" value="network" <?php checked($this->newtork_managed);?> /> &nbsp; &nbsp;
				<?php _e("Per sub-site");?>:<input onchange="managed_from_onchanger(this)"  type="radio" name="<?php echo $this->slug;?>[managed_from_site]" value="singlesite" <?php checked(!$this->newtork_managed);?>  />
				<input type="hidden" name="<?php echo $this->slug;?>[managed_from_changer]" value="ok" />  
				<?php wp_nonce_field( "nonce_mng_".$this->slug, "mng_nonce" ); ?>
			</form>
			<script>
			function managed_from_onchanger(e)
			{
				e.parentNode.submit();
			}
			</script>
			</div>
		<?php 
		}
		

		if ( (is_network_admin() && $this->newtork_managed)  || (!is_network_admin() && !$this->newtork_managed)  )
		{
			$this->opts_page_output();
		}
		else{
			echo '<div style="display: flex; background: white; flex-direction: column; max-width: 600px; margin: 100px auto; border-radius: 10px; padding: 30px;"><h1>'.__("Plugin is set to be managed per: ". ($this->newtork_managed ? "Network": "Sub-sites") ).'</h1></div>';
		}
	}
	


	public function settings_page_part($type)
	{
		$this->is_settings_page = true;

		if($type=="start")
		{
			if( !empty( $_POST["_wpnonce"] ) && check_admin_referer( "nonce_" . $this->slug, "_wpnonce" ) ) 
			{
				if(!empty($_POST[$this->slug]) ) {
					$this->opts['last_update_time'] = time();
					$this->update_opts();
				}
				
				if(isset( $_POST[$this->slug]['clear_error_logs'] ) ){
					$this->clear_errorslog();
				}

				if(isset( $_POST[$this->slug]['reset_plugin_defaults'] ) ){
					$this->reset_plugin_to_defaults(); $this->js_redirect();
				}

				if(isset( $_POST[$this->slug]['update_transl_phrases'] ) ){
					$this->translated_phrases =  array_map('sanitize_text_field', $_POST[$this->slug]['phrases']);
					$this->update_option_CHOSEN('`translated_phrases',  $this->translated_phrases ) ;
				}
			} 
			
				
			if( !empty( $_GET["_wpnonce"] ) && check_admin_referer( "nonce_" . $this->slug, "_wpnonce" ) ) 
			{
				if(isset($_GET[$this->slug.'-remove-pro']) ) {
					delete_site_option($this->license_keyname());
					$this->js_redirect(remove_query_arg($this->slug.'-remove-pro'));
				}
			}
			 
			?>
			<div class="clear"></div>
			<div class="<?php echo $this->myplugin_class;?>">

				<h1 class="plugin-title"><?php echo $this->opts['name'];?></h1> 
				<?php $this->options_tab();  ?>
				<!-- <h2 class="settingsTitle"><?php _e('Plugin Settings Page!', 'default_methods__iTask_Software');?></h2> -->
				
			  	<div class="optwindow">
				<?php



				if ($this->active_tab == "Shortcodes")
				{ 
					echo '<h1 class="shortcodes_maintitle">'. __('Shortcodes Usage', 'default_methods__iTask_Software').'</h1>';
					
					foreach($this->shortcodes as $key=>$value)
					{
						$this->shortcodes_table($key, $value);
					}
				}



				if ($this->active_tab == "Translations & Phrases")
				{ ?>
					<div class="translations_page">
						<form method="post" action="">
							<?php _e("Here will show up all phrases that are outputed on your site fron-end by this plugin, so you can translate/customize them.", 'default_methods__iTask_Software'); ?>
							<table class="translations_table">
								<tbody>
									<?php 
									$phrases_arr = $this->phrases_array();
									$phrases = $this->translated_phrases;
									if(is_array($phrases_arr)){
										foreach ($phrases_arr as $key=>$value){
											$value = array_key_exists($key, $phrases) ? $phrases[$key] : $key;
											echo '<tr>';
											echo '<td>'. $key.'</td><td><input type="text" name="'.$this->slug.'[phrases]['.$key.']" value="'. $value .'" /></td>';
											echo '</tr>';
										}
									}
									?>
								</tbody>
							</table>
							<input type="hidden" name="<?php echo $this->slug;?>[update_transl_phrases]" value="ok" />
							<?php
							wp_nonce_field( "nonce_".$this->slug);
							submit_button(  __( 'Save', 'default_methods__iTask_Software' ), 'button-secondary', '', true  );
							?>
						</form>
					</div>
				<?php
				}



				if ($this->active_tab == "Errors-Log & Reset")
				{ ?>
					<div class="errors_page">
						<div class="errors_table_container">
							<table class="errors_log">
								<style>
								.myplugin .errors_page .errors_table_container { max-height: 500px;  overflow-y: scroll;  border: 1px solid #b5b5b5;}
								.myplugin .errors_page table {border-collapse: collapse;}
								.myplugin .errors_page table tr > * { border: 1px solid #c7c7c7; padding: 5px 10px; }

								.myplugin .errors_page .errors_log tr{transition:0.5s all;}
								.myplugin .errors_page .errors_log tr:hover{background:#fdf7f7;}
								.myplugin .errors_page .errors_log td{min-width: 10px;} 
								.myplugin .errors_page .errors_log td:nth-child(1){max-width:80px;}
								.myplugin .errors_page .errors_log td:nth-child(2){max-width:100px;}
								.myplugin .errors_page .errors_log td:nth-child(3){max-width:150px;}
								.myplugin .errors_page .errors_log td pre {
									white-space: pre-wrap;
									white-space: -moz-pre-wrap;
									white-space: -o-pre-wrap;
									word-wrap: break-word;
								}
								</style>
								<tbody>
									<?php
									$errors = $this->get_errorslog();

									if(!empty($errors)){
										rsort($errors);  //reverse order, last added to top
										$column_count =  count(array_keys( ((array)$errors[0]) ));
										foreach ($errors as $each_err ) {
											$each_err= (array) $each_err;
											echo '<tr>';
											for($i=0; $i<$column_count; $i++){
												$out='';
												$current = $each_err[ array_keys($each_err)[$i]];
												if (!empty($current) )
												{
													$out = $current;
												}
												echo '<td><pre>'. htmlentities($out).'</pre></td>';
											}
											echo '</tr>';
										}

									}
									?>
								</tbody>
							</table>
						</div>
						

						<div class="clear-errors-log">
						<form method="post" action="">
							<input type="hidden" name="<?php echo $this->slug;?>[clear_error_logs]" value="ok" />
							<?php
							wp_nonce_field( "nonce_".$this->slug);
							submit_button(  __( 'Clear Errors Log', 'default_methods__iTask_Software' ), 'button-secondary red-button', '', true  );
							?>
						</form>
						</div>


						<div class="plugin-reset-defaults">
						<form method="post" action="">
							<input type="hidden" name="<?php echo $this->slug;?>[reset_plugin_defaults]" value="ok" />
							<?php
							wp_nonce_field( "nonce_".$this->slug);
							submit_button(  __( 'Reset plugin options to defaults', 'default_methods__iTask_Software' ), 'button-secondary red-button', '', true  );
							?>
						</form>
						</div>
					</div>
				<?php
				}
		}

		
		elseif ($type=="end")
		{ ?>
				</div><!-- optwindow -->
				<?php $this->endStyles();?>
			</div><!-- myplugin -->
		<?php
		}
	}





	
	public function endStyles($external=false)
	{ ?>
		<?php 
		if ($external===false) {
			//
		}
		elseif ($external===true) {
			echo '<div class="'.$this->myplugin_class.'">'; 
		}
		?>

		<style>
		.myplugin { margin: 20px 20px 0 0; line-height:1.2;}
		.myplugin * { position:relative;}
		.myplugin code {font-weight:bold; padding: 3px 5px;  display: inline-block;}
		.myplugin { max-width:100%; display:flex; flex-wrap:rap; justify-content:center; flex-direction:column; padding: 20px; }
		.myplugin >h2 {text-align:center;}
		.myplugin h1,
		.myplugin h2,
		.myplugin h3 {text-align:center; margin: 0.5em 1em 1em;}
		.myplugin table tr { border-bottom: 1px solid #cacaca; }
		.myplugin table td {min-width:50px;}
		.myplugin .form-table  { border: 1px solid #cacaca; padding:2px;  }
		.myplugin .form-table td { padding: 15px 5px;  }
		.myplugin .form-table th { padding: 20px 10px 20px 10px; } 
		.myplugin p.submit {text-align: center;}
		.myplugin .optwindow { border: 1px solid #b5b5b56e;  padding: 10px; border-width: 0px 1px 1px 1px; border-radius: 0px 0px 30px 30px; }
		zz.myplugin input[type="text"]{width:100%;}
		.myplugin .additionals{ display:flex;  font-family: initial; font-size: 1.5em;   text-align:center; margin: 25px 5px 5px; padding: 5px; background: #efeab7;  padding: 5px 0 0 20px;  border-radius: 0% 20px 140px 90%; }
		z.myplugin .additionals:before { content: ""; position: absolute; top: 5%; left: 5%; height: 90%; width: 90%; background: #a222ff61; border-radius: 60% 60% 770% 110px;opacity: 0.6; transform: rotatez(-2deg); }
		.myplugin .additionals:after { content: ""; position: absolute; top: 5%; left: 5%; width: 90%; background: #6bd5ff45; border-radius: 10% 40% 20% 110px; opacity: 0.6; transform: rotatez(3deg); z-index: 0; height: 100px; }
		.myplugin .additionals a{font-weight:bold;font-size:1.1em; color:blue;}
		.myplugin .in_additional { z-index:3; width: 700px; background: #ffffff00; box-shadow: 0px 0px 20px #7d7474; border-radius: 30px; padding: 11px; margin: 0 auto; margin: 20px auto; }
		z.myplugin .additionals li { list-style-type: circle; list-style-type: circle; float: left; margin: 5px 0 5px 40px;}
		.myplugin .whiteback { background:white; border-bottom:1px solid white; }
		.myplugin.version_pro .donations_block, .myplugin.version_not_pro .donations_block { display:none; }
		.myplugin .donation_li a{  color: #d47b09; }
		.myplugin .customNav {margin: 0 0 0 0;}
		.myplugin .customNav .errors-logreset{ color: #903e4c; font-size: 0.7em; font-style: italic; opacity:  0.6;  float:right;}
		.myplugin .customNav .nav-tab{ border-radius: 60% 30% 5% 0px; }
		.myplugin .customNav .nav-tab-active{ color: #43ceb5; pointer-events: none; }
		.myplugin .freelancers {font-style: italic; font-family: arial; font-size: 0.9em; margin: 15px; padding: 10px; border-radius: 5px; opacity: 0.7; }
		.myplugin .freelancers a{}
		.myplugin .button { top: -4px; }
		.myplugin .red-button { background: #ec5d5d;   zbackground:  #ffdfdf;}
		.myplugin .pink-button { background: pink; }
		.myplugin .green-button { background: #44d090; }
		.myplugin .float-left { float:left; }
		.myplugin .float-right { float:right; }
		.myplugin .displaynone { display:none; }
		.myplugin .clearboth { clear:both;  height: 20px;  }
		.myplugin .noinput { border: none!important; box-shadow:none!important; width:auto!important; display:inline-block!important; font-weight:bold; }
		.myplugin .translations_table { margin: 20px 0 0 30px; border-collapse: collapse;}
		.ui-widget-overlay { z-index: 9991; background: #000000; opacity: 0.8; filter: Alpha(Opacity=80); }

		.myplugin a,.myplugin a.button { display: inline; }
		.ui-dialog {z-index: 9222!important; }
		.myplugin .disabled { pointer-events: none; }
		.myplugin .nonclickable { pointer-events: none; }

		.myplugin .review_block{ float:right; }
		.myplugin .review_block a{ float:right; font-size:20px; font-weight:bold; }
		.myplugin .review_block .stars{ height:30px; }
		.myplugin .review_block span.leaverating {position:absolute; z-index:4; margin:0 auto; text-align:center; width:auto; white-space:nowrap; top:15px; color:#000000de; font-size:0.8em; left:20px; text-shadow:0px 0px 25px black;}
		.myplugin .review_block img.stars{ height:30px; vertical-align:middle; }

		.myplugin .shortcode_atts{ color:#b900f3; }
		.myplugin .shortcodes_maintitle { font-style:italic; }
		.myplugin table .shortcode_tr_descr { font-weight:bold; color:black; }
		.myplugin .site_author_block{ text-align:center; font-size:0.8em; }
		.myplugin .site_author_block a{ text-decoration:none; color:black;}
		.myplugin .shortcodes_block { box-shadow: 0px 0px 15px #00000066; padding: 10px 0 0 0; margin: 20px 0;}
		.myplugin .shortcodes_block z.h3{ color:#f34500; text-align:center; }

		.myplugin .datachange-save-button{ display:none; }
		.myplugin ._save_button{ display:none; }
		.myplugin .numeric_input{ width:50px; font-weight:bold;}
		</style>


	<div class="newBlock additionals">
		<div class="in_additional">
			<h4></h4>
			<h3><?php _e('More Actions', 'default_methods__iTask_Software');?></h3>
			<ul class="donations_block">
				<li class="donation_li">
					<?php _e('If you found this plugin useful, any donation is welcomed',  'default_methods__iTask_Software');?> : 
					$<input id="donate_pt" type="number" class="numeric_input" value="2" /> <button onclick="tt_donate_trigger();"/><?php _e('Donate',  'default_methods__iTask_Software');?></button>
					<script>
					function tt_donate_trigger()
					{
						var url= '<?php echo $this->static_settings['donate_url'];?>';
						window.open(url + '/'+ document.getElementById('donate_pt').value,'_blank');
					}
					</script>
					<!-- <a href="%s" class="button" target="_blank">donation</a> -->
				</li>
			</ul>
			<ul>
				<li>
					<?php //printf(__('You can check other useful plugins at: <a href="%s">Must have free plugins for everyone</a>', 'default_methods__iTask_Software'),  $this->static_settings['musthave_plugins'] ).'.';	?>
				</li>
			</ul>
			<ul class="freelancers">
				<li>
					<?php //printf(__('To hire a qualified WordPress specialist, you can use:<br/><a target="_blank" href="%s">TopTal.com</a>, <a target="_blank" href="%s">FreeLancer.com</a> or <a target="_blank" href="%s">PeoplePerHour.com</a> ', 'default_methods__iTask_Software'),  $this->static_settings['wp_tt_freelancers'],  $this->static_settings['wp_fl_freelancers'], $this->static_settings['wp_pph_freelancers']  );  ?>
				</li>
			</ul>
		</div>






		<?php if($this->opts['show_rating_message']) 
		{ ?>
		<div class="review_block">
			<a class="review_leave" href="<?php echo $this->static_settings['wp_rate_url'];?>" target="_blank">
				<span class="leaverating"><?php _e('Rate plugin', 'default_methods__iTask_Software');?></span>
				<img class="stars" src="<?php echo $this->static_settings['public_assets_url'];?>/assets/rating-transparent-shaded.png" />
			</a>
		</div>
		<?php
		}
		?>
		<?php if(property_exists($this, 'show_author_block') && $this->show_author_block) 
		{ ?>
		<div class="site_author_block">
			<a class="autor_url" href="<?php echo $this->static_settings['AuthorURI'];?>" target="_blank">
				<?php echo parse_url($this->static_settings['AuthorURI'])["host"];?>
			</a>
		</div>
		<?php
		}
		?>

	</div>

	<div class="clear"></div>
	<script> tt_ajax_action = '<?php echo $this->plugin_slug_u;?>_all';</script>
	
	<script>
	function pro_field(targetEl){
		var is_pro = <?php echo $this->unregistered_pro() ? "true" : "false";?>; 
		if(is_pro) {
			targetEl.attr("data-pro-overlay","pro_overlay");
		}
	}
	</script> 

	<?php 
	if( $this->opts['has_pro_version']) {  
		if ( $this->is_pro === false || $this->addon_missing() )
			$this->purchase_pro_block(); 
	} 
	?>

	
	<!-- Show "SAVE" button after input change,  type="text" id="manual_pma_login_url" data-onchange-save="true"  data-onchange-hide=".type_manual" name=" -->
	<div class="datachange-save-button">
		<?php submit_button( false, 'button-primary _save_button', '', true,  $attrib=  ['id' => '_save_button'] ); ?> 

		<script>
		(function($){ 

			// save button show/hide
			var save_button=$('.myplugin #_save_button');
			$('.myplugin [data-onchange-save]').on("change, input", function(e){
				save_button.insertAfter( $(this) );
				save_button.show();
				save_button.css( { 'margin-left': "-"+(save_button.css("width")), 'position':'relative', 'top':'0px', 'left':save_button.css("width")  });
				var target=$(this).attr("data-onchange-hide"); if(target && target.length) {   $( target ).css("visibility","hidden");   }
			});

			//noinput types
			if($(".noinput").length) $(".noinput").attr('size', $(".noinput").val().length);
		})(jQuery); 
		</script> 
	</div>


	<?php if ($external===true) echo '</div>'; ?>
	<?php
	}



	public function tinymce_js( $plugin_array )	 {
		$plugin_array[ "button_handle_" . $this->slug ] = $this->home_URL  . '?tinymce_buttons_'.$this->slug;
		return $plugin_array;
	}

	public function register_buttons( $buttons ) {
		$button_names = array_map(  function($ar){ return $ar['button_name']; }, $this->tinymce_buttons );
		return array_merge( $buttons,   $button_names );
	}
	
	public function replace_slashes($path){
		return 	str_replace( ['/','\\',DIRECTORY_SEPARATOR], '/', $path); 
	}
	
	public function tinymce_buttons_body( )
	{
		if( ! isset($_GET['tinymce_buttons_'. $this->slug] ) ) return;

			session_cache_limiter('none');
		// http://stackoverflow.com/a/1385982/2377343
		//Caching with "CACHE CONTROL"
			header('Cache-control: max-age='. ($year=60*60*24*365) .', public');
		//Caching with "EXPIRES"  (no need of EXPIRES when CACHE-CONTROL enabled)
			//header('Expires: '.gmdate(DATE_RFC1123,time()+$year));
		//To get best cacheability, send Last-Modified header and ...
			header('Last-Modified: '.gmdate(DATE_RFC1123,filemtime(__file__)));  //i.e.  1467220550 [it's 30 june,2016]
		//reply using: status 304 (with empty body) if browser sends If-Modified-Since header.... This is cheating a bit (doesn't verify the date), but remove if you dont want to be cached forever:
			// if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {  header('HTTP/1.1 304 Not Modified');   die();	}
			header("Content-type: application/javascript;  charset=utf-8");
		?>
		// ************ these useful scripts got from: https://github.com/tazotodua/useful-javascript/   **********
		// "<script>"  dont remove this line,,, because, now JAVSCRIPT highlighting started in text-editor

		<?php
		$random_name = "button_".rand(1,999999999).rand(1,999999999);
		?>
		"use strict";

		(function ()
		{
		  // Name the plugin anything we want
			tinymce.create( 'tinymce.plugins.<?php echo $random_name;?>',
			{
				init: function (ed, url)
				{

				<?php foreach ($this->tinymce_buttons as $each_button ) { ?>
				  // The button name should be the same as used in PHP function of WP
					ed.addButton( '<?php echo $each_button["button_name"];?>',
					{
					  // Title of button
						title: '<?php echo $each_button["shortcode"];?>',
					  // icon url of button
						image: '<?php echo $each_button["icon"];?>', //url +
					  // Onclick action onto button
						onclick: function ()
						{
						  // Create shortcode string, with default values
							var val = '<?php echo $this->shortcode_example($each_button["shortcode"], $each_button["default_atts"]);?>';
						  // Insert shortcode in text-editor
							ed.execCommand( 'mceInsertContent', false, val );
						}
					});
				<?php } ?>


				},
				createControl: function (n, cm) {
					return null;
				}

			});

			// first parameter	- the same name as defined in PHP function of WP
			// second parameter	- the module name (as defined a bit above)
			tinymce.PluginManager.add( '<?php echo "button_handle_" . $this->slug;?>', tinymce.plugins.<?php echo $random_name;?> );

		})();
		//</script>
		<?php
		exit;
	}

	// ========================================== //

	public function shortcode_example_string($array, $strip_tags=false, $htmlentities=false, $ended=false){
		$out = '<code>';
		$out .= '['. $array['name'].'<span class="shortcode_atts">'; foreach($array['atts'] as $key=>$value){ $out .= " ".$value[0].'="'. $this->truefalse_to_string($value[1]).'"';} $out .='</span>]'; 
		$out = ( $strip_tags	? strip_tags($out) : $out);
		$out = ( $htmlentities	? htmlentities($out) : $out);		
		if( $ended ) 
			$out .= "...[/".$array['name']."]";
		$out .= '</code>';
		return $out;
	}

	public function shortcode_example($shortcode, $array, $ended=false){
		$out="[$shortcode ";   foreach($array as $key=>$value){   $out .= $key.'="'.$this->valueToString($value) .'" ';  }   $out = trim($out). "]";
		if( $ended ) 
			$out .= "...[/$shortcode]";
		return $out;
	}

	public function shortcode_atts($shortcode, $atts){
		$new_arr=[]; 
		foreach($this->shortcodes[$shortcode]['atts'] as $x){
			$new_arr[ $x[0] ] = htmlentities( $this->valueToString( (!empty($x[1]) ? $x[1] : false) ) );
		} 
		if (!empty($atts)) $new_arr = array_merge($new_arr, $atts);
		if (array_key_exists("...", $new_arr)) unset($new_arr["..."]);
		$new_atts = shortcode_atts($new_arr, [] );
		return $new_atts;
	}

	public function valueToString( $value ){
		return ( !is_bool( $value ) ?  $value : ($value ? 'true' : 'false' )  );
	}



	public function shortcodes_initialize(){
		if(property_exists($this,'shortcodes'))
		{
			//enable shortcodes (if it's disabled)
			add_filter( 'widget_text', 'do_shortcode' );

			foreach($this->shortcodes as $name=>$val)
			{
				//add "name" manually as name
				$this->shortcodes[$name]['name']=$name;
				add_shortcode($name, [$this, $name]);
			}
		}
	}
	

	public function phrases_array()
	{
		//get all phrases
		$cont='';
		foreach( $this->plugin_files as $each)
		{
			$cont .= file_get_contents(__DIR__.'/'.$this->slug.'/'.$each);
		}
		preg_match_all( '/\$this\-\>phrase\(\W(.*?)\W\)/si', $cont, $phrases_array ); $phrases_array= array_flip($phrases_array[1]);
		return $phrases_array;
	}

	
	public function shortcodes_table($name, $array)
	{ 
		/*======= example ========
		
		$this->shortcodes_table( "breadcrumbs", [
			[ 'id', 				'',			__('Post ID (you can ignore that parameter if you want to get for current post)', 'breadcrumbs-shortcode') ],
			[ 'delimiter',			'hello', 	__('Your desired delimiter', 'breadcrumbs-shortcode') ],
		]);
		*/
	?>
	<div class="shortcodes_block">
		<h3><?php echo $array['description'];?></h3>
		<table class="form-table shortcodes">
		<tr>
			<td><?php _e('Example:', 'default_methods__iTask_Software');?></td>
			<td>
				<?php echo $this->shortcode_example_string($array, false,false, array_key_exists('ended', $array) );?>
			</td>
		</tr>
		<tr>
			<td><?php _e('Parameters:', 'default_methods__iTask_Software');?></td>
			<td>
				<table>
				<tr class="shortcode_tr_descr">
					<td><?php _e('name', 'default_methods__iTask_Software');?></td><td><?php _e('default value', 'default_methods__iTask_Software');?></td><td><?php _e('description', 'default_methods__iTask_Software');?></td>
				<tr>
				<?php 
				foreach($array['atts'] as $key=>$value)
				{ ?>
				<tr>
					<td><code><?php echo $value[0];?></code></td><td><code><?php echo $this->truefalse_to_string($value[1]);?></code></td><td><?php echo $value[2];?></td>
				</tr>
				<?php 
				}
				?>
				</table>
			</td>
		</tr>
		<!-- <tr>
			<td><?php _e('Example Function:', 'default_methods__iTask_Software');?></td>
			<td><code>$GLOBALS['<?php echo $this->plugin_NAMESPACE;?>']-><?php echo $array['name'];?>( ['parameter'=>123, ....] );</code></td>
		</tr>
		-->
		</table>
	</div>
	  <?php
	}


	public function display_errors()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}
	
	
	
	
	
	
	
	
	

	public function admin_head_func()
	{ 
		if( defined("ttLibrary_scripts_loaded") ) return;  define("ttLibrary_scripts_loaded", true); 
		?>
		<script>
		//window.onload REPLACEMENT
		// window.addEventListener ? window.addEventListener("load",yourFunction,false) : window.attachEvent && window.attachEvent("onload",yourFunction);
		ttLibrary =
		{
			// check for Ajax calls from front-end
			backend_call : function (data, callback)
			{
				data["action"] = tt_ajax_action;
				ttLibrary.spinner(true);

				jQuery.post
				(
					ajaxurl,
					data,
					function(response){  ttLibrary.spinner(false);   callback(response); }
				);
			},

			reload_this_page : function(){
				window.location = window.location.href;
			},

			//show Spinner (loader-waiter)
			spinner: function(action)
			{
				var spinner_id = "tt_spinner";
				if(action)
				{
					var div=
					'<div id="'+spinner_id+'" style="background:black; position:fixed; height:100%; width:100%; opacity:0.9; z-index:33333;   display: flex; justify-content: center; align-items: center;">'+
						'<div style=""><img style="height:80px; width:80px;" src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loaderWhite.gif" />'+
					'</div>';
					document.body.insertAdjacentHTML("afterbegin", div);
				}
				else
				{
					var el = document.getElementById(spinner_id);
					if (el) {
						el.parentNode.removeChild(el);
					}
				}
			},

			//shorthand for jQuery dialog
			dialog: function(message)
			{
				jQuery('<div class="ttDialog">'+message+'</div>').dialog({
					modal: true,
					width: 500,
					close: function (event, ui) {
						jQuery(this).remove();	// Remove it completely on close
					}
				});
			},
			//shortand for the same, to remember easily
			message: function(message)
			{
				return ttLibrary.dialog(message);
			},



			// make an element field to blink/animate
			blink_field : function (fieldObj)
			{
				fieldObj.animate({backgroundColor: '#00bb00'}, 'slow').animate({backgroundColor: '#FFFFFF'}, 'slow');
			},


			// hide content if chosen radio box not chosen  
			radiobox_onchange_hider : function (selector,desiredvalue, target_hidding_selector, SHOW_or_hide)
			{
				SHOW_or_hide = SHOW_or_hide || false;
				if( typeof dropdown_objs == 'undefined') { dropdown_objs = {}; } 
				if( typeof dropdown_objs[selector] == 'undefined' ){
					dropdown_objs[selector] = true ; var funcname= arguments.callee.name;
					//jQuery(selector).change(function() { window[funcname](selector,desiredvalue, target_hidding_selector, SHOW_or_hide);	});
					jQuery(selector).change(function() { ttLibrary.radiobox_onchange_hider(selector,desiredvalue, target_hidding_selector, SHOW_or_hide);	});
				}
				var x = jQuery(target_hidding_selector);
				if( jQuery(selector+':checked').val() == desiredvalue)	{ if(SHOW_or_hide)	x.show(); else x.hide(); } 
				else 													{ if(SHOW_or_hide)	x.hide(); else x.show(); }
			}
		};


		</script>
	  <?php
	}

	// Adding .zip extension
	public function upload_mimes_filter( $mime_types ) {
		if (!array_key_exists('zip', $mime_types)) $mime_types['zip'] = 'application/zip';  
		if (!array_key_exists('gz|gzip|zip', $mime_types)) $mime_types['gz|gzip|zip'] = 'application/x-zip'; 
		//	['gz|gzip'] => application/x-gzip
		//	[rar] => application/rar
		//	[7z] => application/x-7z-compressed
		return $mime_types;
	}

	//move uploaded addon to it's folder
	public function wp_handle_upload_filter( $array=['file' => 'path/to/wp-content/uploads/2018/12/example.ext', 'url'  => 'https://.....example.ext', 'type' => 'application/zip'],   $action= 'sideload|upload' )
	{
		$file = $array['file'];
		if($array['type']=="application/zip" || $array['type']=="application/x-zip")
		{
			$filename = basename($file);
			$found = false; 

			$found_files=[];
			if (function_exists('zip_open'))
			{
				$zip = zip_open($file);
				if (is_resource($zip))
				{
					while ($zip_entry = zip_read($zip))
					{
						$found_files[]=zip_entry_name($zip_entry);
						//if (zip_entry_open($zip, $zip_entry))
						//{
							//echo zip_entry_read($zip_entry);
							//zip_entry_close($zip_entry);
						//}
					}
					zip_close($zip);
				}
			}
			elseif (class_exists('\ZipArchive'))
			{
				$za = new ZipArchive();
				$za->open($file);  
				for( $i = 0; $i < $za->numFiles; $i++ ){ 
					$stat = $za->statIndex( $i ); 
					$found_files[] = basename( $stat['name'] ) ;
				}
			}
			//elseif( stripos($filename, $this->pro_file_part) !== false)
			//{
			//	$found = true;
			//}

			//if contains
			if(!empty($found_files))
			{
				foreach(array_filter($found_files) as $each)
				{
					if( stripos($each, $this->addon_namepart.'/'.$this->slug)!==false)
					{
						$found = true;
					}
				}
			} 

			if($found)
			{
				$this->unzip($file, $this->addons_dir);
				$this->move_folder_contents($this->addons_dir.'/'. $this->addon_namepart, $this->addons_dir);
				$this->rmdir_recursive($this->addons_dir.'/'. $this->addon_namepart);
				$need_space = stripos($_SERVER['REQUEST_URI'], 'upload.php') !== false ? '        ' : '';
				return ['error'=> $need_space."Thank You ⭐ Addon has been installed, you can activate it with the key !"];
			}
		}
		return $array;
	}

	public function move_folder_contents($from, $to)
	{
		foreach( glob($from ."/*") as $each)
		{
			$target=$to."/".basename($each);
			if(is_dir($target)) {
				//$this->rmdir_recursive($target);
			}
			elseif(is_file($target)) 
			{
				@unlink($target);
				//rename($each, $target);
			}
		}
	}

	public function ajax_backend_call()
	{
		if(isset($_POST['action']) && $_POST['action']==$this->plugin_slug_u .'_all')
		{
			if(isset($_POST['PRO_check_key'])){
				echo $this->license_status($_POST['PRO_check_key'], "activate");
			}

			elseif(isset($_POST['PRO_save_results'])){

			}

			elseif(method_exists($this, 'backend_call')){
				$this->backend_call( sanitize_key($_POST['act']) );
			}

			wp_die();
		}
		exit( __('Unknown-action','default_methods__iTask_Software') );
	}
 


	public function insert_code_in_file($filepath, $replace_what, $replace_with)
	{
		if(is_admin())
		{
			if(file_exists($filepath))
			{
				$content= file_get_contents($filepath);
				if(stripos($content, $replace_with) === false)	//if it doesnt contain
				{
					file_put_contents( $filepath, str_replace($replace_what, $replace_with, $content) );
				}
			} 
		}
	}
	



	public function admin_scripts($hook)  //i.e. edit.php
	{
		if($this->is_this_settings_page()){
			$this->admin_scripts_out($hook);
		}
	}

	public function admin_scripts_out($hook)  //i.e. edit.php
	{
		$where='admin';
		$this->register_stylescript($where, 'script', 'jquery');

		//jquery ui 
		$this->register_stylescript($where, 'style',	'wp-jquery-ui-core');
		$this->register_stylescript($where, 'script',	'jquery-ui-core');
		$this->register_stylescript($where, 'script',	'jquery-effects-core');

		$this->register_stylescript($where, 'style',	'ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',  false,  '1.1');
		$this->register_stylescript($where, 'style',	'wp-jquery-ui-dialog');
		$this->register_stylescript($where, 'script',	'jquery-ui-dialog');

		$this->register_stylescript($where, 'script',	'jquery-ui-tooltip');
		
		// spin.js
		//$this->register_stylescript($where, 'script',	'spin', 'https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js',  ['jquery'],  '2.3.2', true);

		// touch-punch.js
		//$this->register_stylescript($where, 'script', 'touch-punch', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js', ['jquery'],  '0.2.3', true );

		//add_action('admin_footer', function() { <script></script> } );
	}

	public function register_stylescript($admin_or_wp, $type, $handle=false, $url=false, $dependant=null, $version=false, $target=false)
	{
		add_action( $admin_or_wp.'_enqueue_scripts',	function() use($type, $handle, $url, $dependant, $version, $target) {
			$this->enqueue($type, $handle, $url, $dependant, $version, $target);
		  }
		); 
	}

	public function enqueue($type, $handle=false, $url=false, $dependant=null, $version=false, $target=false)
	{
		//lets allow shorthanded start
		$localstart = 'assets';
		if( substr($url,0, strlen($localstart) ) == $localstart ) 
			$url = $this->plugin_URL. $url;

		if ( ! call_user_func("wp_".$type."_is",	$handle, "registered" ) ){
			call_user_func("wp_register_".$type,	$handle, $url,  $dependant,  $version, $target );   //,'jquery-migrate'
		}
		if ( ! call_user_func("wp_".$type."_is",	$handle, "enqueued" ) ){
			call_user_func("wp_enqueue_".$type,	$handle);
		}
	}



	public function add_localscript($handle, $string){
		$is_js = stripos($string,'<script>')!==false;
		$js_or_css = $is_js ? 'script' : 'style';
		//$url = $this->plugin_URL . '?
		//register_stylescript($js_or_css, $handle, $url)
	}

	public function unzip_url($url, $where)
	{
		$zipLoc = $where.'/'.(basename($url)).'.zip';
		wp_remote_get
		(
			$this->cat_thumbs_download_url,
			[
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $zipLoc
			]
		);
		$this->unzip($zipLoc, $where);
		@unlink($zipLoc);
	}

	public function unzip($path, $where)
	{ 
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		\WP_Filesystem();
		$this->mkdir($where);
		\unzip_file($path, $where);
		usleep(300000);
	}

	public function unzip_in_dir($dir, $rewrite=true)
	{
		foreach( array_filter(glob($dir.'/*.zip'), 'is_file')  as $each_zip)
		{
			$uniqueTag	= md5($each_zip);
			$each_dir	= substr($each_zip, 0, -4); //trim .zip
			if (empty($each_dir)) return; // ! must have, to avoid empty directory threat

			// remove if previous unpack was partial.
			if( is_dir($each_dir) &&  $rewrite )
			{
				if( empty($this->opts['folderOk_'.$uniqueTag]) )
				{
					$this->rmdir_recursive($each_dir);
					usleep(400000);
					//$this->mkdir_recursive($pathh);
				}
			}
			if( !is_dir($each_dir) )
			{
				$this->opts['folderOk_'.$uniqueTag] = false; $this->update_opts();
				$this->unzip($each_zip, dirname($each_zip));
				$this->opts['folderOk_'.$uniqueTag] = true;  $this->update_opts();
			}
		}
	}



	// common funcs
	public function  str_replace_first($from, $to, $content, $type="plain"){
		if($type=="plain"){
			$pos = strpos($content, $from);
			if ($pos !== false) {
				$content = substr_replace($content, $to, $pos, strlen($from));
			}
			return $content;
		}
		elseif($type=="regex"){
			$from = '/'.preg_quote($from, '/').'/';
			return preg_replace($from, $to, $content, 1);
		}
	}


	public function is_activation(){
		return (isset($_GET['isactivation']));
	}

	public function reload_without_query($params=array(), $js_redir=true){
		$url = remove_query_arg( array_merge($params, ['isactivation'] ) );
		if ($js_redir=="js"){ $this->js_redirect($url); }
		else { $this->php_redirect($url); }
	}

	public function if_activation_reload_with_message($message){
		if($this->is_activation()){
			echo '<script>alert(\''.$message.'\');</script>';
			$this->reload_without_query();
		}
	}

	public function add_default_uninstall(){
		if( is_admin() && !$this->is_development)
		{
			$wp_uninstall_file = $this->plugin_DIR.'/uninstall.php';
			if( !file_exists($wp_uninstall_file) )
			{
				$content=
				'<'.'?php
				// If uninstall not called from WordPress, then exit
				if ( ! defined( "WP_UNINSTALL_PLUGIN" ) ) {
					exit;
				}

				$lib = dirname(__DIR__)."/'.basename(__FILE__).'";
				if(file_exists($lib)){
					//@unlink($lib);
				}';

				file_put_contents($wp_uninstall_file, $content);
			}
		}
	}
	public function getDomain($url){
		return preg_replace('/http(s|):\/\/(www.|)(.*?)(\/.*|$)/i', '$3', $url);
	}

	public function adjustedUrlPrefixes($url){
		if(strpos($url, '://') !== false){
			return preg_replace('/^(http(s|)|):\/\/(www.|)/i', 'https://www.', $url);
		}
		else{
			return 'https://www.'.$url;
		}
	}

	public function stripUrlPrefixes($url){
		return preg_replace('/http(s|):\/\/(www.|)/i', '',  $url);
	}

	public function stripDomain($url){
		return str_replace( $this->adjustedUrlPrefixes($this->domainReal), '', $this->adjustedUrlPrefixes($url) );
	}

	public function safemode_basedir_set(){
		return ( ini_get('open_basedir') || ini_get('safe_mode') ) ;
	}

	public function try_increase_exec_time($seconds){
		if( ! $this-> safemode_basedir_set() ) {
			@set_time_limit($seconds);
			@ini_set('max_execution_time', $seconds);
			$this->try_increase_memory(512);
			return true;
		}
		return false;
	}
	public function try_increase_memory($limit=512){
		if( ! $this-> safemode_basedir_set() ) {
			$limitBytes = $limit * 1048576;
			$currentLimit = (int) trim(ini_get('memory_limit'));
			$lastChar = strtolower($currentLimit[strlen($currentLimit)-1]);
			switch($lastChar) {
				case 'g': $currentLimit *= 1024;
				case 'm': $currentLimit *= 1024;
				case 'k': $currentLimit *= 1024;
			}
			if ($currentLimit < $limitBytes)
				return ini_set('memory_limit', $limit . 'M');
		}
		return false;
	}

	public function convert_urls_in_text($text) {
		return preg_replace('@([^\"\']https?://([-\w\.]+)+(:\d+)?(/([\w/_\.%-=#][^<]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $text);
	}

	public function randomString($length = 11) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);
	}

	public function OneSlash($url){
		$prefix='';
		if(substr($url,0,2)=='//'){
			$prefix = '//';
			$url=substr($url,2);
		}
		return $prefix.preg_replace( '/([^:])\/\//',  '$1/', $url);
	}
	
	public function PlainString(&$text1=false,&$text2=false,&$text3=false,&$text4=false,&$text5=false,&$text6=false,&$text7=false,&$text8=false){
		for($i=1; $i<=8; $i++){    if(${'text'.$i}) {${'text'.$i} = preg_replace('/\W/si','',${'text'.$i});} 	}
		return $text1;
	}
	
	public function get_locale__SANITIZED(){
		return ( get_locale() ? "en" : preg_replace('/_(.*)/','',get_locale()) ); //i.e. 'en'
		//$x=$GLOBALS['wpdb']->get_var("SELECT lng FROM ".$this->options." WHERE `lang` = '".$lang."'"); return !empty($x);
		// preg_replace('/[^\w\d_\-]/', '',  filter_var($input,	FILTER_SANITIZE_STRING)	);
	}


	public function is_this_settings_page(){
		return 
		(
		  is_admin() && 
		  (
			(
				(stripos(get_current_screen()->base, $this->slug) !== false)  &&  (isset($_GET['page']) && $_GET['page']==$this->slug)
			)
			||
			( property_exists($this,'customOptsPageUrl') && stripos($this->currentURL, $this->customOptsPageUrl) !==false )
		  )
		);
	}

	public function blog_prefix()
	{
		$blog_prefix = '';
		if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( get_option( 'permalink_structure' ), '/blog/' ) ) {
			$blog_prefix = '/blog';
		}
		$this->blog_prefix = $blog_prefix;
		return $blog_prefix;
	}

	public function path_after_blog()
	{
		$prf = $this->blog_prefix();
		$path = $this->pathAfterHome; 
		return ( ($prf=="/blog") ? str_replace('/blog/', '', '/'.$path) : $path );
	}

	public function readUrl( $url){
		return wp_remote_retrieve_body(  wp_remote_get( $url )  );
	}

	public function checkMyselfAgainstModification()
	{
		//if ($this->is_development) return;
		$name = '_itask_default_lib_last_revision';
		$opt= $this->get_option_CHOSEN($name, 0 );
		$days=7;
		if( time() - $opt > $days* 86400 )
		{
			//$wp_url  =readUrl   
			//https://plugins.trac.wordpress.org/browser/simple-post-views-count/#trunk
			//https://plugins.trac.wordpress.org/browser/simple-post-views-count/trunk/default_library_itask.php
			// https://plugins.svn.wordpress.org/simple-post-views-count/trunk/
			update_option_CHOSEN($name, time() );
		}
		if(time() - $opt < 0 ){
			update_option_CHOSEN($name, 0 );
		}
	}

	public function set_cookie($name, $val, $time_length = 86400, $path=false, $domain=false, $httponly=true){
		$site_urls = parse_url( (function_exists('home_url') ? home_url() : $_SERVER['SERVER_NAME']) );
		$real_domain = $site_urls["host"];
		$path = $path ? $path : ( (!empty($this) && property_exists($this,'home_FOLDER') ) ?  $this->home_FOLDER : '/');
		$domain = $domain ? $domain : (substr($real_domain, 0, 4) == "www.") ? substr($real_domain, 4) : $real_domain;
		setcookie ( $name , $val , time()+$time_length, $path = $path, $domain = $domain,  $only_on_secure_https = FALSE,  $httponly  );
	}

	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	public function MessageAgainstMaliciousAttempt(){
		return 'Not allowed. Try again.';//'Well... I know that these words won\'t change you, but I\'ll do it again: Developers try to create a balance & harmony in internet, and some people like you try to steal things from other people. Even if you can it, please don\'t do that.';
	}

	public function FullIframeScript(){ ?>
		<script>
		function MakeIframeFullHeight_tt(iframeElement, cycling, overwrite_margin){
			cycling= cycling || false;
			overwrite_margin= overwrite_margin || false;
			iframeElement.style.width	= "100%";
			var ifrD = iframeElement.contentDocument || iframeElement.contentWindow.document;
			var mHeight = parseInt( window.getComputedStyle( ifrD.documentElement).height );  // Math.max( ifrD.body.scrollHeight, .. offsetHeight, ....clientHeight,
			var margins = ifrD.body.style.margin + ifrD.body.style.padding + ifrD.documentElement.style.margin + ifrD.documentElement.style.padding;
			if(margins=="") { margins=0; if(overwrite_margin) {  ifrD.body.style.margin="0px"; } }
			(function(){
			   var interval = setInterval(function(){
				if(ifrD.readyState  == 'complete' ){
					setTimeout( function(){
						if(!cycling) { setTimeout( function(){ clearInterval(interval);}, 500); }
						iframeElement.style.height	= (parseInt(window.getComputedStyle( ifrD.documentElement).height) + parseInt(margins)+1) +"px";
					}, 200 );
				}
			   },200)
			})();
				//var funcname= arguments.callee.name;
				//window.setTimeout( function(){ console.log(funcname); console.log(cycling); window[funcname](iframeElement, cycling); }, 500 );
		}
		</script>
		<?php
	}




	// ======== manual stripslashes_deep ========
	public function array_map_deep( $value, $callback ) 
	{
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $item ) {
					$value[ $index ] = $this->array_map_deep( $item, $callback );
			}
		} elseif ( is_object( $value ) ) {
			$object_vars = get_object_vars( $value );
			foreach ( $object_vars as $property_name => $property_value ) {
					$value->$property_name = $this->array_map_deep( $property_value, $callback );
			}
		} else {
			$value = call_user_func( $callback, $value );
		}
        return $value;
	}
	public function stripslashes_from_strings_only( $value ) {
		return is_string( $value ) ? stripslashes( $value ) : $value;	
	}
	public function stripslashes_deep($value){ return $this->array_map_deep($value, [$this,'stripslashes_from_strings_only'] ); }
	// ================================================


	public function cookieFuncs(){
	?>
	<script>
	// ================= create, read,delete cookies  =================
	function Is_Cookie_Set_tt(cookiename) { return document.cookie.indexOf('; '+cookiename+'=');}

	function createCookie_tt(name,value,days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}
	function readCookie_tt(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	function eraseCookie_tt(name) {
		document.cookie = name+'=; Max-Age=-99999999;';
	}
			function setCookie(name,value,days) { createCookie(name,value,days); }
			function getCookie(name) { return readCookie(name); }
			function setCookieOnce(name) { createCookie(name, "okk" , 1000); }
	// ===========================================================================================
	</script>
	<?php
	}


	//only open and close the same-origin creator of session  (argument can be TRUE/FALSE or STRING too
	public function session_state ($arg) { 
		if($arg===true)	{	if(session_status() == PHP_SESSION_NONE)	{ $GLOBALS['my_session_pp']='sess'.rand(1,99999999); session_start();  return $GLOBALS['my_session_pp']; }   	}     
		else			{	if(session_status() == PHP_SESSION_ACTIVE)	{ if(!$arg || $arg==$GLOBALS['my_session_pp']) session_write_close();  }   	}  
	}
	public function set_session_var ($name,$value) {
		$id= $this->session_state(true);
		$_SESSION[$name] = $value;
		$this->session_state($id);
	}
	
	public function startSessionIfNotStarted(){
		if(session_status() == PHP_SESSION_NONE)  { $this->session_being_opened = true; session_start();  }
	}
	public function endSessionIfWasStarted( $method=2){
		if(session_status() != PHP_SESSION_NONE && property_exists($this,"session_being_opened") )  {
			if($method==1) session_destroy();
			elseif($method==2) session_write_close();
			elseif($method==3) session_abort();
		}
	}
	public function array_map_recursive(callable $func, array $array) {
		return filter_var($array, \FILTER_CALLBACK, ['options' => $func]);
	}

	//convert unsorted array (i.e. [ 'first'=>["a","b","c"], 'second'=>[1,2,3] , ] ) to associative   [ "a"=>1, "b"=2 ]
	public function array_to_associative($array) {
		
	}

	
	  

		//$transient_name = md5( json_encode( $args ) );
		//$transient_results = get_transient( $transient_name );
		//set_transient( $transient_name, $result,  $this->opts['feed_cache_expire'] * MINUTE_IN_SECONDS );

	public function nextKeyInArray($target_keyname, $array){
		$keys = array_keys($array);
		$index_of_target_keyname = array_search($target_keyname,  $keys , true);
		return (count($array) > $index_of_target_keyname+1 ) ? $keys[$index_of_target_keyname+1]  :  $keys[0];
	}

	public function nextValueInArray($target_value, $array, $by_key=false){
		$keys = array_keys($array);
		$target_keyname = $by_key ? $target_value : array_search($target_value,  $array, true );
		$index_of_target_keyname = array_search($target_keyname,  $keys, true );
		return (count($array) > $index_of_target_keyname+1 ) ? $array[ $keys[$index_of_target_keyname+1] ]  :  $array[  $keys[0]  ];
	}

	public function getIndexOfKey($array, $key){
		return array_search($key, array_keys($array) );
	}

	public function getMemberByIndex($array, $idx){
		$keys= array_keys($array);
		return (!empty($keys) && !empty($array[$keys[$idx]])) ? $array[$keys[$idx]] : null ;
	}

	public function getIndexOfValue($array, $key){
		return array_search($key, $array );
	}

	public function resortArrayByKey($array, $key, $remove_current= false){
		$remaining =  array_splice ($array, $this->getIndexOfKey($array, $key)   );
		if($remove_current){
			$array[$key]= $remaining[$key];
			unset($remaining[$key] );
		}
		return array_merge($remaining, $array);
	}

	public function arrayPhpToJs($array){
		return '["'. implode('","', $array) .'"]';
	}

	public function ListAllInDir($path, $only_files = false) {
		$all_list = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
				( $only_files ? \RecursiveIteratorIterator::LEAVES_ONLY : \RecursiveIteratorIterator::SELF_FIRST )
		);
		$files = [];
		foreach ($all_list as $file)
			$files[] = $file->getPathname();

		return $files;
	}


	public function replace_occurences_in_dir($dir_base, $from, $to, $exts=array('php','shtml') ){
		$dirIterator = $this->ListAllInDir($dir_base, true);
		foreach($dirIterator as $idx => $value) {
			$filext = pathinfo($value, PATHINFO_EXTENSION);
			if( in_array($filext,  $exts ) ){
				$cont = file_get_contents($value);
				if(stripos($cont, $from) !== false){
					$new_cont = str_replace($from, $to, file_get_contents($value) );
					file_put_contents($value, $new_cont);
				}
			}
		}
	}

	public function replace_in_file($file, $from_pattern, $to){
		if(file_exists($file))
		{
			$cont= file_get_contents($file);
			$new_cont= preg_replace($from_pattern, $to, $cont);
			file_put_contents($file, $new_cont);
		}
	}

	public function update_or_insert($tablename, $NewArray, $WhereArray=[]){	global $wpdb; $arrayNames= array_keys($WhereArray);
		//convert array to STRING
		$o=''; $i=1; foreach ($WhereArray as $key=>$value){ $o .= $key . ' = \''. $value .'\''; if ($i != count($WhereArray)) { $o .=' AND '; $i++;}  }
		//check if already exist
		if(!empty($o)){
			$CheckIfExists = $wpdb->get_var("SELECT ".$arrayNames[0]." FROM ".$tablename." WHERE ".$o);
			if ( $wpdb->update($tablename,	$NewArray,	$WhereArray	) ) return true;
		}
		if ( $wpdb->insert($tablename, 	array_merge($NewArray, $WhereArray)	) ) return true;
		return false;
	}

	public function startsWith($haystack, $needle) {   return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false; }
	public function endsWith($haystack, $needle) { $length = strlen($needle);  return $length === 0 ||  (substr($haystack, -$length) === $needle); }
	public function contains($content, $needle, $case_sens= true){ return ($case_sens ? strpos($content, $needle) : stripos($content, $needle)) !== false;  }

	// ================ flash rules ================= //
	// unique func to flush rewrite rules when needed. if not hooked into wp_footer, hangs plugin options resaving 
	public function flush_rules_if_needed($temp_key=false){
		// lets check if refresh needed
		$key="b".get_current_blog_id()."_". md5(    (empty($temp_key) ?  "sample" : ( stripos($temp_key, basename($this->plugin_DIR)) !== false ? md5(filemtime($temp_key)) : $temp_key ))    );
		if( !array_key_exists($key, $this->opts['last_updates']) || $this->opts['last_updates'][$key] < $this->opts['last_update_time']){
			$this->opts['last_updates'][$key] = $this->opts['last_update_time'];
			$this->update_opts();
			add_action('wp_footer', function(){ $this->flush_rules("js"); } );
		}
	}

	public function is_JSON_string($string){
	   return (is_string($string) && is_array(json_decode($string, true)));
	}

	public function arrayed_json($answer){
		$result = [];
		if(!$this->is_JSON_string($answer)){
			$result['error'] = $answer;
		}
		else{
			$result = json_decode($answer, true);
		}
		return $result;
	}

	public function arrayed_answer($answer){
		$result = [];
		if(!$this->is_JSON_string($answer)){
			$result['error'] = $answer;
		}
		else{
			$result = json_decode($answer, true);
		}
		return $result;
	}


	public function flush_rules($redirect=false){
		flush_rewrite_rules();
		if($redirect) {
			if ($redirect=="js"){ $this->js_redirect(); }
			else { $this->php_redirect(); }
		}
	}
	
	public function flush_rules_checkmark($redirect=false){
		flush_rewrite_rules();
		$this->opts['needs_flushing'] = true; $this->update_opts();
		if($redirect) {
			if ($redirect=="js"){ $this->js_redirect(); }   else { $this->php_redirect(); }
		}
	}
	public function flush_checkpoint(){
		if(isset($this->opts['needs_flushing']))
		{
			unset($this->opts['needs_flushing']);
			$this->update_opts();
			$this->flush_rules(true);
		}
	}
	

	// TODO - handle $_POST
	public function disable_cache($hard=false, $file=false){
		header("Expires: Mon, 4 Jan 1999 12:00:00 GMT");        // Expired already 
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");     
		header("Cache-Control: no-cache, must-revalidate");      // good for HTTP/1.1 
		header("Pragma: no-cache"); 
		if($hard){
			if(!isset($_GET['rand']))
				$this->php_redirect( $this->AddStringToUrl($_SERVER['REQUEST_URI'], 'rand='.rand(1,9999999) )   );
		}
		ini_set("opcache.enable", 0); 
		if($file){
			opcache_invalidate($file);
		}
	}

	public function AddStringToUrl($url, $string){
		return $url .( stripos($url,'?')===false ?  '?'.$string :  '&'.$string);
	}

	public function js_redirect($url=false, $echo=true){
		$str = '<script>window.location = "'. ( $url ?: $_SERVER['REQUEST_URI'] ) .'";</script>';
		if($echo) { exit($str); }  else { return $str; }
	}
	public function php_redirect($url=false, $code=302){
		header("location: ". ( $url ?: $_SERVER['REQUEST_URI'] ), true, $code); exit;
	}

	public function js_redirect_message($message, $url=false){
		echo '<script>alert(\''.$message.'\');</script>';
		$this->js_redirect($url);
	}

	public function mkdir_recursive($dest, $permissions=0755, $create=true){
		if(!is_dir($dest)){
			//at first, recursively create directory if doesn't exist
			if(!is_dir(dirname($dest))){ $this->mkdir_recursive(dirname($dest), $permissions, $create); }
			mkdir($dest, $permissions, $create); 
		}
		else{return true;}
	}

	public function mkdir($dest, $permissions=0755, $create=true){
		return $this->mkdir_recursive($dest, $permissions, $create);
	}
	
	public function rmdir_recursive($path){
		if(!empty($path) && is_dir($path) ){
			$dir  = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS); //upper dirs not included,otherwise DISASTER HAPPENS :)
			$files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $f) {if (is_file($f)) {unlink($f);} else {$empty_dirs[] = $f;} } if (!empty($empty_dirs)) {foreach ($empty_dirs as $eachDir) {rmdir($eachDir);}} rmdir($path);
			return true;
		}
		return true;
		//include_once(ABSPATH.'/wp-admin/includes/class-wp-filesystem-base.php');
		//\WP_Filesystem_Base::rmdir($fullPath, true);
	}

	public function file_put_contents($file, $content) {
		if(!empty($file))
		{
			$this->mkdir(dirname($file));
			file_put_contents($file,$content);
		}
	}

	// ================ flash rules ================= //









































	// ========= my functions for PRO plugins ========== //
	
	public function addon_path()
	{
		return WP_PLUGIN_DIR .'/_addons/'.$this->slug .'-addon/addon.php';
	}
	
	public function addon_exists()
	{
		return (file_exists($this->addon_path()));
	}

	public function load_pro()
	{
		if ($this->is_pro)
		{
			if ($this->is_pro_legal)
			{
				$itask_last_class = $this;
				if($this->addon_exists())
					include_once($this->addon_path());
			}
			else {
				add_action('admin_notices', function () {
					?>
					<div class="notice notice-error is-dismissible">
						<p><?php _e( printf('Notice: License for plugin <code><b>%s</b></code> is invalidated, so it\'s <b style="color:red;">PRO</b> functionality has been disabled.', $this->static_settings['Name']), 'default_methods__iTask_Software' ); ?></p>
					</div>
					<?php
				});
			}
		}
	}


	public function check_if_pro_plugin()
	{
		$this->is_pro		= null;
		$this->is_pro_legal	= null;
		if( $this->opts['has_pro_version'] ){
			//$this->has_pro_version = true;  // it is price of plugin
			$ar= $this->get_license();
			$this->is_pro		= $ar['status'];
			$this->is_pro_legal	= $ar['legal'];
		}

		if(is_admin() && $this->is_pro_legal)
		{
			$this->pro_check_once_in_a_while();
		}

		$this->addons_dir = WP_PLUGIN_DIR.'/_addons'; //wp_plugins_dir();
	}

	public function addon_missing()
	{
		$res=false;
		if( $this->opts['has_pro_version'] ){
			if(!file_exists($this->addon_path()) && $this->is_pro){
				$res=true;
			}
		}
		return $res;
	}

	public function license_keyname(){
		return $this->plugin_slug_u ."_l_key";
	}

	public function get_license($key=false){
		$def_array = [
			'status' => false,
			'legal' => false,
			'key' => '',
		];
		$license_arr = get_site_option($this->license_keyname(), $def_array );
		return ($key ? $license_arr[$key] : $license_arr);
	}

	public function update_license($val, $val1=false){
		if(is_array($val)){
			$array = $val;
		}
		else{
			$array= $this->get_license();
			$array[$val]=$val1;
		}
		update_site_option( $this->license_keyname(), $array );
	}



	public function license_answer($key, $type="check/or/activate")
	{
		$this->info_arr	= ['key' => $key] + ['siteurl'=>home_url(), 'plugin_slug'=>$this->slug ] + $this->pluginvars() + $this->opts;

		$answer =
			wp_remote_retrieve_body(
				wp_remote_post($this->static_settings['purchase_check'].$type,
					[
						'method' => 'POST',
						'timeout' => 25,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => [],
						'body' => $this->info_arr,
						'cookies' => []
					]
				)
			);
		return $answer;
	}
 
	public function license_status($license, $type="check/or/activate")
	{
		$key = sanitize_text_field($license);
		$answer = $this->license_answer($key, $type);

		if(!$this->is_JSON_string($answer)){
			$result = [];
			$result['error'] = $answer;;
		}
		else{
			$result = json_decode($answer, true);
		}
		//
		if(isset($result['valid'])){
			if($result['valid']){
				if(!isset($result['error'])){
					$ar['status']= true;
					$ar['legal']= true;
					$ar['key']= $key;
					$this->update_license($ar);
				}
				else { 
					$this->update_license( 'legal', false );
					$result['error'] = $answer;
				} 
			}
		}
		else{
			$result['error'] = $answer;
			$this->log('Error while calling to vendor', $result['error']);
		}
		return json_encode($result);
	}

	public function pro_check_once_in_a_while()
	{	
		$name= '`_last_license_check';
		$opt= $this->get_option_CHOSEN($name, time() );
		$days=7;
		if( time() - $opt > $days* 86400 )
		{
			$lic = $this->get_license();
			$res= $this->license_status($lic['key'], 'activate');
			update_option_CHOSEN($name, time() );
		}
		if(time() - $opt < 0 ){
			update_option_CHOSEN($name, 0 );
		}
	}

	public function unregistered_pro() { return $this->opts['has_pro_version'] && !$this->is_pro_legal; }

	public function pro_field($echo=true){
		if($this->unregistered_pro()){
			$res= 'data-pro-overlay="pro_overlay"';
			if($echo) echo $res;
			else return $res;
			//echo '<span class="pro_overlay overlay_lines"></span> ';
		}
	}

	public function purchase_pro_block(){ ?>
		<div class="pro_block">
			<style>
			.myplugin .dialog_enter_key{ display:none; }
			.get_pro_version { line-height: 1.2; z-index: 123; background: #ff1818;  text-align: center; border-radius: 100% 100% 0 0; display: inline-block;  position: fixed; bottom: 0px; right: 0; left: 0; padding: 10px 10px; max-width: 750px; margin: 0 auto; text-shadow: 0px 0px 6px white;  box-shadow: 0px 0px 52px black; }
			.get_pro_version .centered_div > span  { font-size: 1.5em; }
			.get_pro_version .centered_div .or_enter_key_phrase{ font-style: italic; font-size:1em; }
			.get_pro_version .centered_div > span  a { font-size: 1em; color: #7dff83;}
			.init_hidden{ display:none; }
			z#check_results{ display:inline; flex-direction:row; font-style:italic; }
			#check_results .correct{  background: #a8fba8;  }
			#check_results .incorrect{  background: pink;  }
			#check_results span{  padding:3px 5px;  }
			.dialog_enter_key_content {  display: flex; flex-direction: column; align-items: center;  }
			.dialog_enter_key_content > *{  margin: 10px ;  }

			[data-pro-overlay=pro_overlay]{  pointer-events: none;  cursor: default;  position:relative;  min-height: 2em;  padding:5px; }
			[data-pro-overlay=pro_overlay]::before{   content:""; width: 100%; height: 100%; position: absolute; background: black; opacity: 0.3; z-index: 1;  top: 0;   left: 0;
				background: url("https://ps.w.org/internal-functions-for-protectpages-com-users/trunk/assets/overlay-1.png");
			}
			[data-pro-overlay=pro_overlay]::after{ 
				white-space: pre; content: "<?php $str=__('Only available in FULL VERSION', 'default_methods__iTask_Software');  echo str_repeat($str.'\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a\a', 4).$str;?>"; 
				text-shadow: 0px 0px 5px black; padding: 5px;  opacity:1;  text-align: center;  animation-name: blinking;  zzanimation-name: moving;  animation-duration: 6s;  animation-iteration-count: infinite;  overflow:hidden; display: flex; justify-content: center; align-items: center; position: absolute; top: 0; left: 0; bottom: 0; right: 0; z-index: 3; overflow: hidden; font-size: 2em; color: red;
			}
			@keyframes blinking {
				0% {opacity: 0;}
				50% {opacity: 1;}
				100% {opacity: 0;}
			}
			@keyframes moving {
				0% {left: 30%;}
				40% {left: 100%;}
				100% {left: 0%;}
			}
			</style>
			<div class="get_pro_version">
				<span class="centered_div">
					<?php 
					if ($this->is_pro === false)
					{ ?>
						<?php if (!$this->addon_exists()) { ?>
						<span class="purchase_phrase">
							<a id="purchase_key" href="<?php echo esc_url($this->static_settings['purchase_url']);?>" target="_blank"><?php _e('GET FULL VERSION', 'default_methods__iTask_Software');?></a> <span class="price_amnt"><?php _e('only', 'default_methods__iTask_Software');?> <?php echo $this->opts['has_pro_version'];?>$</span>
						</span>
						<?php } ?>
						<span class="or_enter_key_phrase">
						( <?php _e('After purchase', 'default_methods__iTask_Software');?> <a id="enter_key"  href=""><?php _e('Enter License Key', 'default_methods__iTask_Software');?></a> )
						</span>	
						<?php
					} 
					elseif ($this->addon_missing())
					{ ?>
						<span class="addon_missing">
						( <?php _e('Seems you have bought a PRO version , but the addon is not installed.', 'default_methods__iTask_Software');?>)
						</span>	
					  <?php
					}
					else {
						?><style>
						.myplugin.version_free .get_pro_version, .myplugin.version_pro .get_pro_version{ display:none; }
						</style>
						<?php
					}
					?>
				</span>
			</div>

			<div class="dialog_enter_key">
				<div class="dialog_enter_key_content" title="Enter the purchased license key">
					<input id="key_this" class="regular-text" type="text" value="<?php echo $this->get_license('key');?>"  />
					<button id="check_key" ><?php _e( 'Check key', 'default_methods__iTask_Software' );?></button>
					<span id="check_results">
						<span class="correct init_hidden"><?php _e( 'correct', 'default_methods__iTask_Software' );?></span>
						<span class="incorrect init_hidden"><?php _e( 'incorrect', 'default_methods__iTask_Software' );?></span>
					</span>
				</div>
			</div>
		</div>
		<?php
		$this->plugin_scripts();
	}

	public function plugin_scripts(){
		?>
		<script>
		function main_tt()
		{ 
			var this_action_name = '<?php echo $this->plugin_slug_u;?>';

			(function ( $ ) {
				$(function () {
					//$("#purchase").on("click", function(e){ this_name_tt.open_license_dialog(); } );
					$("#enter_key").on("click", function(e){ return this_name_tt.enter_key_popup(); } );
					$("#check_key").off().on("click", function(e){ return this_name_tt.check_key(); } );
				});
			})( jQuery );

			// Create our namespace
			this_name_tt = {

				/*
				*	Method to check (using AJAX, which calls WP back-end) if inputed username is available
				*/
				enter_key_popup: function(e) {

					// Show jQuery dialog
					jQuery('.dialog_enter_key_content').dialog({
						modal: true,
						width: 500,
						close: function (event, ui) {
							//jQuery(this).remove();	// Remove it completely on close
						}
					});
					return false;
				},

				IsJsonString: function(str) {
					try {
						JSON.parse(str);
					} catch (e) {
						return false;
					}
					return true;
				},

				check_key : function(e) {

					var this1 = this;

					var inp_value = jQuery("#key_this").val();

					if (inp_value == ""){  return;  }
 
					ttLibrary.backend_call(
						{
							'PRO_check_key': inp_value
						},

						// Function when request complete
						function (answer) {

							if(typeof window.ttdebug != "undefined"){  console.log(answer);  }

							if(this1.IsJsonString(answer)){
								var new_res=  JSON.parse(answer);
								if(new_res.hasOwnProperty('valid')){
									if(new_res.valid){
										this1.show_green();
									}
									else{
										var reponse1 = JSON.parse(new_res.response);
										this1.show_red(reponse1.message);
									}
								}
								else {
									this1.show_red(new_res);
								}
							}
							else{
								this1.show_red(answer);
							}
						}
					);
				},

				show_green : function(){
					jQuery("#check_results .correct").show();
					jQuery("#check_results .incorrect").hide();
					alert("<?php _e("Thanks! License is activated for this domain.", 'default_methods__iTask_Software'); echo '\n\n\n\n'; _e("NOTE: Sharing or unauthorized use of the license will be ended with the suspension of the license code.", 'default_methods__iTask_Software') ;?>");
					ttLibrary.reload_this_page();
					//this.save_results();
				},

				show_red : function(e){
					jQuery("#check_results .correct").hide();
					jQuery("#check_results .incorrect").show();
					jQuery("#check_results .incorrect").html(e);

					/*
					var message = 'Your inputed username "' + tw_usr + '" is incorrect! \nPlease, change it.';
					// Show jQuery dialog
					jQuery('<div>' + message + '</div>').dialog({
						modal: true,
						width: 500,
						close: function (event, ui) {
							jQuery(this).remove();	// Remove it completely on close
						}
					});
					*/
				}

			};
		}
		main_tt();
		</script>

		<?php
	}

	// ================================================   ##end of default block##  ============================================= //
	// ========================================================================================================================== //
  }


} //EOT
?>