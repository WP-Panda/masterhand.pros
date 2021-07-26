<?php

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since MyTheme 1.0
 */
class Fre_Customize {
	/**
	 * This hooks into 'customize_register' (available as of WP 3.4) and allows
	 * you to add new sections and controls to the Theme Customize screen.
	 *
	 * Note: To enable instant preview, we have to actually write a bit of custom
	 * javascript. See live_preview() for more.
	 *
	 * @see add_action('customize_register',$func)
	 *
	 * @param \WP_Customize_Manager $wp_customize
	 *
	 * @since freelanceengine 1.0
	 */
	public static function register( $wp_customize ) {
		//1. Define a new section (if desired) to the Theme Customizer
		$wp_customize->add_section( 'fre_customizer',
			array(
				'title'       => __( 'Color Options', ET_DOMAIN ), //Visible title of section
				'priority'    => 35, //Determines what order this appears in
				'capability'  => 'edit_theme_options', //Capability needed to tweak
				'description' => __( 'Allows you to customize colors of website.', ET_DOMAIN ), //Descriptive tooltip
			)
		);

		//2a. Register new settings to the WP database...
		$wp_customize->add_setting( 'primary_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#1fbdbd',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//2b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
			$wp_customize, //Pass the $wp_customize object (required)
			'fre_primary_color', //Set a unique ID for the control
			array(
				'label'    => __( 'Primary Color', ET_DOMAIN ),
				//Admin-visible name of the control
				'settings' => 'primary_color',
				//Which setting to load and manipulate (serialized is okay)
				'priority' => 10,
				//Determines the order this control appears in for the specified section
				'section'  => 'colors',
				//ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		) );

		//3a. Register new settings to the WP database...
		$wp_customize->add_setting( 'secondary_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#1fbdbd',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//3b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'fre_secondary_color', //Set a unique ID for the control
				array(
					'label'    => __( 'Secondary Color', ET_DOMAIN ),
					//Admin-visible name of the control
					'settings' => 'secondary_color',
					//Which setting to load and manipulate (serialized is okay)
					'priority' => 10,
					//Determines the order this control appears in for the specified section
					'section'  => 'colors',
					//ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);

		//4a. Register new settings to the WP database...
		$wp_customize->add_setting( 'bg_header', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#fff',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//4b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'fre_header_color', //Set a unique ID for the control
				array(
					'label'    => __( 'Background Header', ET_DOMAIN ),
					//Admin-visible name of the control
					'settings' => 'bg_header',
					//Which setting to load and manipulate (serialized is okay)
					'priority' => 10,
					//Determines the order this control appears in for the specified section
					'section'  => 'colors',
					//ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);

		//5a. Register new settings to the WP database...
		$wp_customize->add_setting( 'textcolor_header', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#2c3e50',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//5b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'fre_textcolor_header', //Set a unique ID for the control
				array(
					'label'    => __( 'Color for text in header', ET_DOMAIN ),
					//Admin-visible name of the control
					'settings' => 'textcolor_header',
					//Which setting to load and manipulate (serialized is okay)
					'priority' => 10,
					//Determines the order this control appears in for the specified section
					'section'  => 'colors',
					//ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);

		//3a. Register new settings to the WP database...
		$wp_customize->add_setting( 'bg_footer', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#fff',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//3b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
			$wp_customize, //Pass the $wp_customize object (required)
			'fre_bg_footer', //Set a unique ID for the control
			array(
				'label'    => __( 'Background  Footer', ET_DOMAIN ),
				//Admin-visible name of the control
				'settings' => 'bg_footer',
				//Which setting to load and manipulate (serialized is okay)
				'priority' => 10,
				//Determines the order this control appears in for the specified section
				'section'  => 'colors',
				//ID of the section this control should render in (can be one of yours, or a WordPress default section)
			)
		) );
		//3a. Register new settings to the WP database...
		$wp_customize->add_setting( 'textcolor_footer', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#666',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//3b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'fre_textcolor_footer', //Set a unique ID for the control
				array(
					'label'    => __( 'Footer Text color', ET_DOMAIN ),
					//Admin-visible name of the control
					'settings' => 'textcolor_footer',
					//Which setting to load and manipulate (serialized is okay)
					'priority' => 10,
					//Determines the order this control appears in for the specified section
					'section'  => 'colors',
					//ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);
		//4a. Register new settings to the WP database...
		$wp_customize->add_setting( 'bg_copyright', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
			array(
				'default'    => '#666',
				//Default setting/value to save
				'type'       => 'theme_mod',
				//Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options',
				//Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage',
				//What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			)
		);

		//4b. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'fre_bg_copyright', //Set a unique ID for the control
				array(
					'label'    => __( 'Background Copyright', ET_DOMAIN ),
					//Admin-visible name of the control
					'settings' => 'bg_copyright',
					//Which setting to load and manipulate (serialized is okay)
					'priority' => 10,
					//Determines the order this control appears in for the specified section
					'section'  => 'colors',
					//ID of the section this control should render in (can be one of yours, or a WordPress default section)
				)
			)
		);

		//4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		//$wp_customize->get_setting( 'bg_header' )->transport = 'postMessage';
		$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action('wp_head',$func)
	 * @since MyTheme 1.0
	 */
	public static function header_output() {
		$primary_color   = get_theme_mod( 'primary_color', '#1fbdbd' );
		$secondary_color = get_theme_mod( 'secondary_color', '#1fbdbd' );
		?>
        <!--Customizer CSS-->
        <!-- <style type="text/css" id="header_output">
            <?php self::generate_css( 'body', 'background-color', 'background_color', '#' ); ?>
            <?php self::generate_css( '#main_header', 'background-color', 'bg_header' ); ?>
            <?php self::generate_css( '.fre-menu-main li a, .fre-login>li a', 'color', 'textcolor_header' ); ?>
            <?php self::generate_css( 'footer', 'background-color', 'bg_footer' ); ?>
             <?php self::generate_css( '.copyright-wrapper ', 'background-color', 'bg_copyright' ); ?>
            <?php self::generate_css( 'footer a, footer, footer h2, footer *, footer .widget ul li a', 'color', 'textcolor_footer' ); ?>

            body .secondary-color, .secondary-color, .secondary-color:hover,body .fre-tabs>li.active a, body .profile-secure-code-wrap>p a,
            body .fre-view-as-others, body .fre-view-as-others:hover,
            #user_avatar_browse_button,
            .fre-project-upload-file:hover,body .project-detail-attach>li a,
            body .freelance-portfolio-loadmore a, body .fre-skin-color, body .transaction-filter-clear,
            .credit-transaction-wrap .table-col-action a, .credit-transaction-wrap .table-col-action a:focus, .credit-transaction-wrap .table-col-action a:hover,
            body .fre-account-wrap>.dropdown-menu>li a.view-more-notify,
            body .fre-paginations .paginations .page-numbers.current,
            body .fre-notification-list>li .fre-notify-archive>span,
            body .fre-upload-file,.modal .fre-upload-file,
            body .freelance-certificate-action a,
            body .freelance-education-action a{
                color:  <?php echo get_theme_mod( 'secondary_color', '#1fbdbd' ); ?>;
            }

            body .primary-color,
            .fre-btn-o.primary-color:hover,
            .primary-color-hover:hover,
            .primary-bg-color:hover,
            .fre-service-pricing:hover .primary-color-hover,
            .fre-service-pricing:hover .service-price h2, .fre-service-pricing:hover .service-price p,
            body .fre-btn-o,
            body .fre-normal-btn-o, .fre-normal-btn:hover,
            body .fre-payment-list>li>.btn:hover{
               color: <?php echo $primary_color; ?>;
            }
            .fre-btn-disable:active,
            .fre-btn-disable:focus,
            .fre-btn-disable:hover, .fre-btn:active, .fre-btn:focus, .fre-btn:hover,
            .fre-payment-list>li>.btn:active, .fre-payment-list>li>.btn:focus, .fre-payment-list>li>.btn:hover {
                background: #FFFFFF;
               color: <?php echo $primary_color; ?> !important;
            }

            body .fre-normal-btn, .primary-bg-color, .fre-small-btn, .fre-submit-btn,
            body .fre-payment-list>li>.btn,
            body .fre-btn, body.fre-btn-disable, body .fre-payment-list>li>.btn {
                background-color: <?php echo $primary_color; ?>;
                border-color: <?php echo $primary_color; ?>;
            }
            .btn-apply-project, body .fre-btn-o:hover,
            body .fre-radio span:after,body .radio-inline span:after
            {
                background-color: <?php echo $primary_color; ?>;
            }

            body .primary-color, body .fre-btn-o,
            .fre-radio input[type=radio]:checked+span:before, .radio-inline input[type=radio]:checked+span:before
            {
                border-color: <?php echo $primary_color; ?>;
            }
            body .fre-project-upload-file.primary-color{
              border-color:#dbdbdb;
            }

            .fre-btn-o.primary-color:hover{
                color: #fff;
                background-color: <?php echo $primary_color; ?>;
            }
            .fre-tabs>li.active a:after{
                background: <?php echo $secondary_color; ?>;
            }
            .primary-color-hover:hover, body .fre-normal-btn-o{
                border-color: <?php echo $primary_color; ?>;
            }

            .fre-service-pricing:hover .primary-color-hover{
              border-color: <?php echo $primary_color; ?>;

          }
            .fre-service-pricing:hover .primary-color-hover:hover,body .fre-normal-btn-o:hover{
              background-color: <?php echo $primary_color; ?>;
            }
            .fre-service-pricing:hover:after{
              background-color: <?php echo $primary_color; ?>;
            }

      </style>-->
        <!--/Customizer CSS-->
		<?php
	}

	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 * Also keep in mind that this function isn't necessary unless your settings
	 * are using 'transport'=>'postMessage' instead of the default 'transport'
	 * => 'refresh'
	 *
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 * @since MyTheme 1.0
	 */
	public static function live_preview() {
		wp_enqueue_script(
			'fre-themecustomizer', // Give the script a unique ID
			get_template_directory_uri() . '/assets/js/theme-customizer.js', // Define the path to the JS file
			array( 'jquery', 'customize-preview' ), // Define dependencies
			'', // Define a version (optional)
			true // Specify whether to put in footer (leave this true)
		);
	}

	/**
	 * This will generate a line of CSS for use in header output. If the setting
	 * ($mod_name) has no defined value, the CSS will not be output.
	 *
	 * @uses get_theme_mod()
	 *
	 * @param string $selector CSS selector
	 * @param string $style The name of the CSS *property* to modify
	 * @param string $mod_name The name of the 'theme_mod' option to fetch
	 * @param string $prefix Optional. Anything that needs to be output before the CSS property
	 * @param string $postfix Optional. Anything that needs to be output after the CSS property
	 * @param bool $echo Optional. Whether to print directly to the page (default: true).
	 *
	 * @return string Returns a single line of CSS with selectors and a property.
	 * @since MyTheme 1.0
	 */
	public static function generate_css( $selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true ) {
		$return = '';
		$mod    = get_theme_mod( $mod_name );
		if ( ! empty( $mod ) ) {
			$return = sprintf( '%s { %s:%s ; }',
				$selector,
				$style,
				$prefix . $mod . $postfix
			);
			if ( $echo ) {
				echo $return;
			}
		}

		return $return;
	}
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register', array( 'Fre_Customize', 'register' ) );

// Output custom CSS to live site
add_action( 'wp_head', array( 'Fre_Customize', 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init', array( 'Fre_Customize', 'live_preview' ) );

//add_action('customize_save_after', 'ae_save_customize');
function ae_save_customize() {
	$style      = array();
	$style      = wp_parse_args( $style, array(
		'background'    => get_theme_mod( 'body_bg_color' ) ? get_theme_mod( 'body_bg_color' ) : '#ECF0F1',
		'header'        => get_theme_mod( 'header_bg_color' ) ? get_theme_mod( 'header_bg_color' ) : '#ffffff',
		'heading'       => '#525252',
		'footer_bottom' => get_theme_mod( 'footer_bg_color' ) ? get_theme_mod( 'footer_bg_color' ) : '#2C3E50',
		'footer'        => get_theme_mod( 'btm_footer_color' ) ? get_theme_mod( 'btm_footer_color' ) : '#34495E',
		'text'          => '#7b7b7b',
		'action_1'      => get_theme_mod( 'main_color' ) ? get_theme_mod( 'main_color' ) : '#e74b3b',
		'action_2'      => get_theme_mod( 'main_color' ) ? get_theme_mod( 'second_color' ) : '#2c3e50',
		'project_color' => get_theme_mod( 'project_color' ) ? get_theme_mod( 'project_color' ) : '#00afff',
		'profile_color' => get_theme_mod( 'profile_color' ) ? get_theme_mod( 'profile_color' ) : '#2dcb71',
	) );
	$customzize = et_less2css( $style );
	$customzize = et_mobile_less2css( $style );
}

if ( ! function_exists( 'et_get_customization' ) ) {

	/**
	 * Get and return customization values for
	 * @since 1.0
	 */
	function et_get_customization() {
		$style = get_option( 'ae_theme_customization', true );
		$style = wp_parse_args( $style, array(
			'background'          => '#ffffff',
			'header'              => '#2980B9',
			'heading'             => '#37393a',
			'text'                => '#7b7b7b',
			'action_1'            => '#8E44AD',
			'action_2'            => '#3783C4',
			'project_color'       => '#3783C4',
			'profile_color'       => '#3783C4',
			'footer'              => '#F4F6F5',
			'footer_bottom'       => '#fff',
			'font-heading-name'   => 'Raleway,sans-serif',
			'font-heading'        => 'Raleway',
			'font-heading-size'   => '15px',
			'font-heading-style'  => 'normal',
			'font-heading-weight' => 'normal',
			'font-text-name'      => 'Raleway, sans-serif',
			'font-text'           => 'Raleway',
			'font-text-size'      => '15px',
			'font-text-style'     => 'normal',
			'font-text-weight'    => 'normal',
			'font-action'         => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-action-size'    => '15px',
			'font-action-style'   => 'normal',
			'font-action-weight'  => 'normal',
			'layout'              => 'content-sidebar'
		) );

		return $style;
	}
}

function et_customizer_print_styles() {
	if ( current_user_can( 'manage_options' ) && ! is_admin() ) {

		et_enqueue_gfont();

		wp_register_style( 'et_colorpicker', TEMPLATEURL . '/customizer/css/colorpicker.css', array(
			'custom'
		) );
		wp_enqueue_style( 'et_colorpicker' );
		wp_register_style( 'et_customizer_css', TEMPLATEURL . '/customizer/css/customizer.css', array(
			'custom'
		) );
		wp_enqueue_style( 'et_customizer_css' );
		?>
        <script type="text/javascript" id="ae-customizer-script">
            var customizer = {};
			<?php
			$style = et_get_customization();
			foreach ( $style as $key => $value ) {
				$variable = $key;

				//$variable = str_replace('-', '_', $key);
				if ( preg_match( '/^rgb/', $value ) ) {
					preg_match( '/rgb\(([0-9]+), ([0-9]+), ([0-9]+)\)/', $value, $matches );
					$val = rgb2html( $matches[1], $matches[2], $matches[3] );
					echo "customizer['{$variable}'] = '{$val}';\n";
				} else {
					echo "customizer['{$variable}'] = '" . stripslashes( $value ) . "';\n";
				}
			}
			?>
        </script>
		<?php
	}
}

function et_get_scheme() {
	return array(
		'#8E44AD',
		'#2980B9',
		'#1BA084',
		'#904C09',
		'#E67E22',
		'#16A084',
		'#AD0A4B',
		'#B5740B'
	);
}

function et_schemes() {
	return array(
		array(
			'background'          => '#ffffff',
			'header'              => '#2980B9',
			'heading'             => '#37393a',
			'text'                => '#7b7b7b',
			'action_1'            => '#8E44AD',
			'action_2'            => '#3783C4',
			'project_color'       => '#3783C4',
			'profile_color'       => '#3783C4',
			'footer'              => '#F4F6F5',
			'font-heading-name'   => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-heading'        => 'opensans',
			'font-heading-size'   => '15px',
			'font-heading-style'  => 'normal',
			'font-heading-weight' => 'normal',
			'font-text-name'      => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-text'           => 'opensans',
			'font-text-size'      => '15px',
			'font-text-style'     => 'normal',
			'font-text-weight'    => 'normal',
			'font-action'         => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-action-size'    => '15px',
			'font-action-style'   => 'normal',
			'font-action-weight'  => 'normal',
			'layout'              => 'content-sidebar',
			'footer_bottom'       => '#ddd'
		),
		array(
			'background'          => '#ffffff',
			'header'              => '#000',
			'heading'             => '#67393a',
			'text'                => '#6b7b7b',
			'action_1'            => '#8E44AD',
			'action_2'            => '#3783C4',
			'project_color'       => '#3783C4',
			'profile_color'       => '#3783C4',
			'footer'              => '#F4F6F5',
			'font-heading-name'   => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-heading'        => 'opensans',
			'font-heading-size'   => '15px',
			'font-heading-style'  => 'normal',
			'font-heading-weight' => 'normal',
			'font-text-name'      => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-text'           => 'opensans',
			'font-text-size'      => '15px',
			'font-text-style'     => 'normal',
			'font-text-weight'    => 'normal',
			'font-action'         => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-action-size'    => '15px',
			'font-action-style'   => 'normal',
			'font-action-weight'  => 'normal',
			'layout'              => 'content-sidebar',
			'footer_bottom'       => '#ddd'
		)
	);
}

function et_page_color() {
	return array(
		'header'        => __( "Header", ET_DOMAIN ),
		'background'    => __( "Body", ET_DOMAIN ),
		'footer'        => __( "Footer", ET_DOMAIN ),
		'footer_bottom' => __( "Footer Bottom", ET_DOMAIN ),
		'action_1'      => __( "Main color", ET_DOMAIN ),
		'action_2'      => __( "Second color", ET_DOMAIN ),
		'project_color' => __( "Project", ET_DOMAIN ),
		'profile_color' => __( "Profile", ET_DOMAIN )
	);
}

/**
 * Get all font supported by theme
 *
 * @return mixed
 */
function et_get_supported_fonts() {
	$fonts = apply_filters( "et_enqueue_gfont", array(
		'raleway'    => array(
			'fontface' => 'Raleway, san-serif',
			'name'     => 'Raleway',
			'link'     => 'Raleway:400,300,500,600,700,800'
		),
		'arial'      => array(
			'fontface' => 'Arial, san-serif',
			'name'     => 'Arial',
			'link'     => 'Arial'
		),
		'quicksand'  => array(
			'fontface' => 'Quicksand, sans-serif',
			'link'     => 'Quicksand',
			'name'     => 'Quicksand'
		),
		'ebgaramond' => array(
			'fontface' => 'EB Garamond, serif',
			'link'     => 'EB+Garamond',
			'name'     => 'EB Garamond'
		),
		'imprima'    => array(
			'fontface' => 'Imprima, sans-serif',
			'link'     => 'Imprima',
			'name'     => 'Imprima'
		),
		'ubuntu'     => array(
			'fontface' => 'Ubuntu, sans-serif',
			'link'     => 'Ubuntu',
			'name'     => 'Ubuntu'
		),
		'adventpro'  => array(
			'fontface' => 'Advent Pro, sans-serif',
			'link'     => 'Advent+Pro',
			'name'     => 'EB Garamond'
		),
		'mavenpro'   => array(
			'fontface' => 'Maven Pro, sans-serif',
			'link'     => 'Maven+Pro',
			'name'     => 'Maven Pro'
		),
		'times'      => array(
			'fontface' => 'Times New Roman, serif',
			'link'     => 'Times+New+Roman',
			'name'     => 'Times New Roman'
		),
		'georgia'    => array(
			'fontface' => 'Georgia, serif',
			'link'     => 'Georgia',
			'name'     => 'Georgia'
		),
		'helvetica'  => array(
			'fontface' => 'Helvetica, san-serif',
			'link'     => 'Helvetica',
			'name'     => 'Helvetica'
		),
	) );

	return $fonts;
}

/**
 * Get google font
 *
 * @param $font_id
 *
 * @author: nguyenvanduocit
 * @return \WP_Error
 */
function et_get_gfront( $font_id ) {
	$fonts = et_get_supported_fonts();
	if ( array_key_exists( $font_id, $fonts ) ) {
		return $fonts[ $font_id ];
	}

	return new WP_Error( 'font_not_found', "Font not found" );
}

/**
 * @author: nguyenvanduocit
 */
function et_enqueue_gfont() {

	// enqueue google web font
	$fonts = et_get_supported_fonts();
	foreach ( $fonts as $key => $font ) {
		echo "<link href='//fonts.googleapis.com/css?family=" . $font['link'] . "' rel='stylesheet' type='text/css'>";
	}
}

/**
 * Enqueue google font
 *
 * @author : Nguyễn Văn Được
 */
function et_enqueue_customize_font() {

	$customization_option = et_get_customization();
	$font_heading         = $customization_option['font-heading'];
	$font_body            = $customization_option['font-text'];
	$fonts                = et_get_supported_fonts();

	if ( array_key_exists( $font_heading, $fonts ) ) {
		$url = "//fonts.googleapis.com/css?family=" . $fonts[ $font_heading ]['link'];
		wp_enqueue_style( 'et-customization-font-heading', $url );
	}

	if ( array_key_exists( $font_body, $fonts ) ) {
		$url = "//fonts.googleapis.com/css?family=" . $fonts[ $font_body ]['link'];
		wp_enqueue_style( 'et-customization-text', $url );
	}
}

/**
 * Show off the customizer pannel
 */
function et_customizer_panel() {
	if ( current_user_can( 'manage_options' ) && ! et_load_mobile() ) {
		$style  = et_get_customization();
		$layout = 'content-sidebar';

		$schemes     = et_get_scheme();
		$page_colors = et_page_color();
		$schemes     = array();
		?>
        <script type="text/javascript" id="schemes"><?php
			echo json_encode( et_schemes() ); ?></script>
        <div id="customizer" class="customizer-panel">
            <div class="close-panel"><a href="<?php
				echo esc_url( add_query_arg( 'deactivate', 'customizer' ) ); ?>" class=""><span>*</span></a></div>
            <form action="" id="f_customizer">
				<?php
				if ( ! empty( $schemes ) ) { ?>
                    <div class="section">
                        <div class="custom-head">
                            <span class="spacer"></span>
                            <h3><?php
								_e( 'Color Schemes', ET_DOMAIN ) ?></h3><span class="spacer"></span>
                        </div>
                        <div class="section-content">
                            <ul class="blocks-grid">
								<?php
								foreach ( $schemes as $key => $value ) { ?>
                                    <li class="clr-block scheme-item" data="" style="background: <?php
									echo $value; ?>"></li>
									<?php
								} ?>
                            </ul>
                        </div>
                    </div>
					<?php
				} ?>
                <div class="section">
                    <div class="custom-head">
                        <span class="spacer"></span>
                        <h3><?php
							_e( 'Page Options', ET_DOMAIN ) ?></h3><span class="spacer"></span>
                    </div>
                    <div class="section-content">
                        <h4><?php
							_e( 'Colors', ET_DOMAIN ) ?></h4>
                        <ul class="blocks-list">
							<?php
							foreach ( $page_colors as $key => $value ) { ?>
                                <li>
                                    <div class="picker-trigger clr-block" data-color="<?php
									echo $key; ?>" style="background: <?php
									echo $style[ $key ] ?>"></div>
                                    <span class="block-label"><?php
										echo $value; ?></span>
                                </li>
								<?php
							} ?>
                        </ul>
                    </div>
                </div>
                <div class="section">
                    <div class="custom-head">
                        <span class="spacer"></span>
                        <h3><?php
							_e( 'Content Options', ET_DOMAIN ) ?></h3><span class="spacer"></span>
                    </div>
                    <div class="section-content" style="display: none">
						<?php
						$fonts = et_get_supported_fonts(); ?>
                        <div class="block-select">
                            <label for=""><?php
								_e( 'Heading', ET_DOMAIN ) ?></label>
                            <div class="select-wrap">
                                <div>
                                    <select class="fontchoose" name="font-heading">
										<?php
										foreach ( $fonts as $key => $font ) { ?>
                                            <option <?php
											if ( $style['font-heading'] == $key )
												echo 'selected="selected"' ?> data-fontface="<?php
											echo $font['fontface'] ?>" value="<?php
											echo $key ?>"><?php
												echo $font['name'] ?></option>
											<?php
										} ?>
                                    </select>
                                </div>
                            </div>
                        </div>
						<?php
						/* <div class="slider-wrap">
										   <div class="slider heading-size" data-min="18" data-max="29" data-value="<?php echo str_replace( 'px', '', $style['font-heading-size'] ) ?>">
											   <input type="hidden" name="font-heading-size">
										   </div>
									   </div>  */ ?>
                        <div class="block-select">
                            <label for=""><?php
								_e( 'Content', ET_DOMAIN ) ?></label>
                            <div class="select-wrap">
                                <div>
                                    <select class="fontchoose" name="font-text" id="">
										<?php
										foreach ( $fonts as $key => $font ) { ?>
                                            <option <?php
											if ( $style['font-text'] == $key )
												echo 'selected="selected"' ?> data-fontface="<?php
											echo $font['fontface'] ?>" value="<?php
											echo $key ?>"><?php
												echo $font['name'] ?></option>
											<?php
										} ?>
                                    </select>
                                </div>
                            </div>
                        </div>
						<?php
						/*
									   <div class="slider-wrap">
										   <div class="slider text-size" data-min="12" data-max="14" data-value="<?php echo str_replace( 'px', '', $style['font-text-size'] ) ?>">
											   <input type="hidden" name="font-text-size">
										   </div>
									   </div>
					   */ ?>
                    </div>
                </div>
                <button type="button" class="btn blue-btn" id="save_customizer" title="<?php
				_e( 'Save', ET_DOMAIN ) ?>"><span><?php
						_e( 'Save', ET_DOMAIN ) ?></span></button>
                <button type="button" class="btn none-btn" id="reset_customizer" title="<?php
				_e( 'Reset', ET_DOMAIN ) ?>"><span class="icon" data-icon="D"></span></span><span><?php
						_e( 'Reset', ET_DOMAIN ) ?></span></button>
            </form>
        </div> <?php
	}
}

/**
 * Displaying the button that trigger the customizer panel
 */
function et_customizer_trigger() {
	if ( current_user_can( 'administrator' ) && ! et_load_mobile() ) { ?>
        <style type="text/css">
            #customizer_trigger {
                position: fixed;
                top: 40%;
                left: 0;
                height: 40px;
                width: 40px;
                display: block;
                border-radius: 0px 3px 3px 0px;
                -moz-border-radius: 0px 3px 3px 0px;
                -webkit-border-radius: 0px 3px 3px 0px;
                color: #7b7b7b;
                border: 1px solid #c4c4c4;
                transition: opacity 0.5s linear;
                z-index: 1000;
                padding: 5px;
                display: none !important;
            }

            #customizer_trigger:hover {
                opacity: 0.5;
                filter: alpha(opacity:50);
            }

            #customizer_trigger:before {
                font-size: 20px;
                line-height: 23px;
                margin-left: 10px;
                text-shadow: 0 -1px 1px #333333;
                -moz-text-shadow: 0 -1px 1px #333333;
                -webkit-text-shadow: 0 -1px 1px #333333;
            }

            #customizer_trigger i {
                font-size: 30px;
            }
        </style>
        <a id="customizer_trigger" title="<?php
		_e( 'Activate customization mode', ET_DOMAIN ) ?>" href="<?php
		echo esc_url( add_query_arg( 'activate', 'customizer' ) ) ?>">
            <i class="fa fa-cog"></i>
        </a>
		<?php
	}
}

define( 'CUSTOMIZE_DIR', THEME_CONTENT_DIR . '/css' );

/**
 * Trigger the customization mode here
 * When administrator decide to customize something,
 * he trigger a link that activate "customization mode".
 *
 * When he finish customizing, he click on the close button
 * on customizer panel to close the "customization mode".
 */
function et_customizer_init() {

	$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	if ( isset( $_REQUEST['activate'] ) && $_REQUEST['activate'] == 'customizer' ) {
		setcookie( 'et-customizer', '1', time() + 3600, '/' );
		wp_redirect( esc_url( remove_query_arg( 'activate' ) ) );
		exit;
	} else if ( isset( $_REQUEST['deactivate'] ) && $_REQUEST['deactivate'] == 'customizer' ) {
		setcookie( 'et-customizer', '', time() - 3600, '/' );
		wp_redirect( esc_url( remove_query_arg( 'deactivate' ) ) );
		exit;
	}

	/**
	 * cookie store customize active
	 * render customize bar and script
	 */
	if ( isset( $_COOKIE['et-customizer'] ) && ( true == $_COOKIE['et-customizer'] ) ) {
		add_action( 'wp_print_styles', 'et_customizer_print_styles', 100 );
		add_action( 'wp_print_scripts', 'et_customizer_print_scripts' );
		add_action( 'wp_ajax_save-customization', 'et_customizer_save' );
		add_action( 'wp_footer', 'et_customizer_panel' );
		add_action( 'wp_logout', 'et_customizer_destroy_cookie' );
	} else {
		add_action( 'et_after_print_styles', 'et_customization_styles' );
		add_action( 'wp_footer', 'et_customizer_trigger' );
		add_action( 'body_class', 'et_layout_classes' );
	}
	add_action( 'wp_ajax_reset-customizationer', 'et_resetCustomizerToDefault' );
}

add_action( 'init', 'et_customizer_init' );
function et_customizer_destroy_cookie() {
	setcookie( 'et-customizer', '', time() + 3600, '/' );
}

function et_customizer_save() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	try {
		if ( isset( $_REQUEST['content']['customization'][0] ) ) {
			unset( $_REQUEST['content']['customization'][0] );
		}
		$customization = $_REQUEST['content']['customization'];

		// save the customization value
		update_option( 'ae_theme_customization', $customization );

		$customzize = et_less2css( $customization );
		$customzize = et_mobile_less2css( $customization );

		$resp = array(
			'success' => true,
			'code'    => 200,
			'msg'     => __( "Changes are saved successfully.", ET_DOMAIN ),
			'data'    => $customization
		);
	} catch ( Exception $e ) {
		$resp = array(
			'success' => false,
			'code'    => true,
			'msg'     => sprintf( __( "Something went wrong! System cause following error <br /> %s", ET_DOMAIN ), $e->getMessage() )
		);
	}
	wp_send_json( $resp );
}

/**
 * Reset customizer option
 *
 * @param void
 *
 * @return void
 * @since void
 * @package void
 * @category void
 * @author Tambh
 */
function et_resetCustomizerToDefault() {
	global $user_ID;
	$resp = array(
		'success' => false,
		'msg'     => __( "Reset failed!", ET_DOMAIN )
	);
	if ( current_user_can( 'manage_options' ) ) {
		update_option( 'ae_less_customize', '' );
		update_option( 'ae_mobile_less_customize', '' );
		$resp = array(
			'success' => true,
			'msg'     => __( "Reset successfully!", ET_DOMAIN )
		);
	}
	wp_send_json( $resp );
}

/**
 * Adds theme layout classes to the array of body classes.
 */
function et_layout_classes( $existing_classes ) {
	$current_layout = 'content-sidebar';

	if ( in_array( $current_layout, array(
		'content-sidebar',
		'sidebar-content'
	) ) ) {
		$classes = array(
			'two-column'
		);
	} else {
		$classes = array(
			'one-column'
		);
	}

	if ( 'content-sidebar' == $current_layout ) {
		$classes[] = 'right-sidebar';
	} elseif ( 'sidebar-content' == $current_layout ) {
		$classes[] = 'left-sidebar';
	} else {
		$classes[] = $current_layout;
	}

	$classes = apply_filters( 'et_layout_classes', $classes, $current_layout );

	return array_merge( $existing_classes, $classes );
}

// add_filter( 'body_class', 'et_layout_classes' );

function et_customizer_print_scripts() {

	if ( current_user_can( 'manage_options' ) && ! is_admin() ) {

		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-slider' );

		// color picker
		wp_register_script( 'et-colorpicker', TEMPLATEURL . '/customizer/js/colorpicker.js' );
		wp_enqueue_script( 'et-colorpicker', false, array(
			'jquery'
		), '1.0', true );

		// scrollbar
		wp_register_script( 'et-tinyscrollbar', TEMPLATEURL . '/customizer/js/jquery.tinyscrollbar.min.js' );
		wp_enqueue_script( 'et-tinyscrollbar', false, array(
			'jquery',
			'underscore',
			'backbone',
			'appengine'
		), '1.0', true );

		// customizer script
		wp_register_script( 'et_customizer', TEMPLATEURL . '/customizer/js/customizer.js', array(
			'jquery',
			'et-colorpicker',
			'appengine'
		), false, true );
		wp_enqueue_script( 'et_customizer', false, array(
			'jquery',
			'et-colorpicker',
			'appengine'
		), '1.0', true );

		add_action( 'print_define_less', 'print_define_less' );
	}
}

function print_define_less() { ?>
    <link rel="stylesheet/less" type="txt/less" href="<?php
	echo TEMPLATEURL . '/customizer/define.less' ?>">
	<?php
	wp_register_script( 'less-js', TEMPLATEURL . '/customizer/js/less-1.4.1.min.js', '1.0', true );
	wp_enqueue_script( 'less-js' );
}
