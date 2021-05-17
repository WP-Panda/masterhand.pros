<?php
require_once dirname(__FILE__) . '/less.inc.php';

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since DirectoryEngine 1.0
 */
class AE_Customize
{

    /**
     * This hooks into 'customize_register' (available as of WP 3.4) and allows
     * you to add new sections and controls to the Theme Customize screen.
     *
     * Note: To enable instant preview, we have to actually write a bit of custom
     * javascript. See live_preview() for more.
     *
     * @see add_action('customize_register',$func)
     * @param \WP_Customize_Manager $wp_customize
     * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
     * @since DirectoryEngine 1.0
     */
    public static function register($wp_customize) {

        //1. Define a new section (if desired) to the Theme Customizer
        $wp_customize->add_section('de_customizer_options', array(
            'title' => __('FRE Options', ET_DOMAIN) ,
            'priority' => 35,
            'capability' => 'edit_theme_options',
            'description' => __('Allows you to customize some example settings for FreelanceEngine.', ET_DOMAIN) ,

            //Descriptive tooltip


        ));

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('header_bg_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('body_bg_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('footer_bg_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('btm_footer_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('main_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('second_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('project_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_setting('profile_color', array(
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
        ));

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_bg_color', array(
            'label' => __('Header Background Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'header_bg_color',
            'priority' => 10,
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'body_bg_color', array(
            'label' => __('Body Background Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'body_bg_color',
            'priority' => 10,
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_bg_color', array(
            'label' => __('Footer Background Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'footer_bg_color',
            'priority' => 10,
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'btm_footer_color', array(
            'label' => __('Copyright Background Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'btm_footer_color',
            'priority' => 10,
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'main_color', array(
            'label' => __('Main Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'main_color',
            'description' => __("Site main color, such view profile, apply project button", ET_DOMAIN) ,
            'priority' => 10,
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'second_color', array(
            'label' => __('Secondary Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'second_color',
            'description' => __("On background have 2 color, it is the gray", ET_DOMAIN) ,
            'priority' => 10
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'project_color', array(
            'label' => __('Project Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'project_color',
            'description' => __("Profile main color, such as link, title, create project button", ET_DOMAIN) ,
            'priority' => 10
        )));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'profile_color', array(
            'label' => __('Profile Color', ET_DOMAIN) ,
            'section' => 'colors',
            'settings' => 'profile_color',
            'description' => __("Profile main color, such as professional title", ET_DOMAIN) ,
            'priority' => 10,
        )));

        //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
        $wp_customize->get_setting('blogname')->transport = 'postMessage';
        $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
        $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';
        $wp_customize->get_setting('background_color')->transport = 'postMessage';
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
        if (et_load_mobile()) return;
?>
            <!--Customizer CSS-->
            <style type="text/css" id="header_output">
                <?php
        self::generate_css('#menu-top', 'background-color', 'header_bg_color'); ?>
                <?php
        self::generate_css('body', 'background-color', 'body_bg_color'); ?>
                <?php
        self::generate_css('footer', 'background-color', 'footer_bg_color') ?>
                <?php
        self::generate_css('.copyright-wrapper', 'background-color', 'btm_footer_color') ?>
                <?php
?>
                .option-search.right input[type="submit"] {
                    color: #fff;
                }
            </style>
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
     * @since DirectoryEngine 1.0
     */
    public static function live_preview() {
        wp_enqueue_script('de-themecustomizer',

        // Give the script a unique ID
        get_template_directory_uri() . '/customizer/customizer.js',

        // Define the path to the JS file
        array(
            'jquery',
            'customize-preview'
        ) ,

        // Define dependencies
        '',

        // Define a version (optional)
        true

        // Specify whether to put in footer (leave this true)
        );

        et_customizer_print_styles();
        echo '<link rel="stylesheet/less" type="txt/less" href="' . get_template_directory_uri() . '/customizer/admin-define.less">';
        wp_enqueue_script('lessc', get_template_directory_uri() . '/customizer/less.js', array() , true);
    }

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     *
     * @uses get_theme_mod()
     * @param string $selector CSS selector
     * @param string $style The name of the CSS *property* to modify
     * @param string $mod_name The name of the 'theme_mod' option to fetch
     * @param string $prefix Optional. Anything that needs to be output before the CSS property
     * @param string $postfix Optional. Anything that needs to be output after the CSS property
     * @param bool $echo Optional. Whether to print directly to the page (default: true).
     * @return string Returns a single line of CSS with selectors and a property.
     * @since DirectoryEngine 1.0
     */
    public static function generate_css($selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true) {
        $return = '';
        $mod = get_theme_mod($mod_name);
        if (!empty($mod)) {
            $return = sprintf('%s { %s:%s ; }', $selector, $style, $prefix . $mod . $postfix);
            if ($echo) {
                echo $return;
            }
        }
        return $return;
    }
}

//Setup the Theme Customizer settings and controls...
add_action('customize_register', array(
    'AE_Customize',
    'register'
));

// Output custom CSS to live site
add_action('wp_footer', array(
    'AE_Customize',
    'header_output'
));

// Enqueue live preview javascript in Theme Customizer admin screen
add_action('customize_preview_init', array(
    'AE_Customize',
    'live_preview'
));
