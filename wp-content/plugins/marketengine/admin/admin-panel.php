<?php
/**
 * ME Admin Panel Functions
 *
 * @author   EngineThemes
 * @category Function
 * @package  Admin/Functions
 * @version  1.0
 */

/**
 * Retrieve list of page
 *
 * @since 1.0
 * @return array
 */
function marketengine_get_list_of_page() {
    $args = array(
        'post_type'   => 'page',
        'post_status' => 'publish',
    );

    $pages        = get_pages($args);
    $list_of_page = array();
    foreach ($pages as $page) {
        $list_of_page[$page->ID] = $page->post_title;
    }
    return $list_of_page;
}

/**
 * Render admin menu option view
 * @category Admin/Options
 * @since 1.0
 */
function marketengine_option_view() {

    marketengine_option_header();
    $tabs = array(
        'marketplace-settings'  => array(
            'title'    => __("Marketplace", "enginethemes"),
            'slug'     => 'marketplace-settings',
            'template' => include (dirname(__FILE__) . '/options/marketplace.php'),
            'index' => 0
        ),
        'payment-gateways'      => array(
            'title'    => __("Payment Gateways", "enginethemes"),
            'slug'     => 'payment-gateways',
            'template' => include (dirname(__FILE__) . '/options/payment-gateways.php'),
            'index' => 5
        ),
        'pages-settings'     => array(
            'title'    => __("Pages", "enginethemes"),
            'slug'     => 'pages-settings',
            'template' => include (dirname(__FILE__) . '/options/pages.php'),
            'index' => 10
        ),
    );

    /**
     * Filter admin settings table
     *
     * @param array $tabs The admin settings tabs
     * @since 1.0
     */
    $tabs = apply_filters('marketengine_settings_tabs', $tabs);

    echo '<div class="marketengine-tabs">';

    echo '<ul class="me-nav me-tabs-nav">';

    if (empty($_REQUEST['tab']) || !array_key_exists($_REQUEST['tab'], $tabs)) {
        $requested_tab = 'marketplace-settings';
    } else {
        $requested_tab = $_REQUEST['tab'];
    }

    foreach ($tabs as $key => $tab) {
        $class = '';
        // check is current tab
        if ($requested_tab == $key) {
            $class = 'class="active"';
        }
        echo '<li ' . $class . '><a href="?page=me-settings&tab=' . $tab['slug'] . '">' . $tab['title'] . '</a></li>';
    }
    echo '</ul>';

    echo '<div class="me-tabs-container">';

    $tab = new ME_Tab($tabs[$requested_tab]);
    $tab->render();

    echo '</div>';

    echo '</div>';
    marketengine_option_footer();
}

/**
 * Render admin menu reports view
 * @category Admin/Reports
 * @since 1.0
 */
function marketengine_report_view() {
    marketengine_option_header();
    marketengine_get_template('admin/overview', $_REQUEST);
    marketengine_option_footer();
}

/**
 * Add marketengine admin menu
 * @category Admin
 */
function marketengine_option_menu() {
    global $submenu;
    unset($submenu['edit.php?post_type=listing'][10]);
    // Hide link on listing page
    add_action( 'admin_head', 'marketengine_add_header_style' );

    add_menu_page(
        __("MarketEngine Dashboard", "enginethemes"),
        __("MarketEngine", "enginethemes"),
        'manage_options',
        'marketengine',
        null,
        null,
        28
    );
}
add_action('admin_menu', 'marketengine_option_menu');

function marketengine_reports_menu() {
    add_submenu_page(
        'marketengine',
        __("Reports", "enginethemes"),
        __("Reports", "enginethemes"),
        'manage_options',
        'me-reports',
        'marketengine_report_view'
    );
}
add_action('admin_menu', 'marketengine_reports_menu', 20);

function marketengine_settings_menu() {
    add_submenu_page(
        'marketengine',
        __("Settings", "enginethemes"),
        __("Settings", "enginethemes"),
        'manage_options',
        'me-settings',
        'marketengine_option_view'
    );
}
add_action('admin_menu', 'marketengine_settings_menu', 25);


function marketengine_setupwizard_menu() {
    add_submenu_page(
        'marketengine',
        __("Setup Wizard", "enginethemes"),
        __("Setup Wizard", "enginethemes"),
        'manage_options',
        '?page=marketengine-setup',
        null
    );
}
add_action('admin_menu', 'marketengine_setupwizard_menu', 30);

/**
 * Prints styles to admin head.
 *
 * @since   1.0.1
 */
function marketengine_add_header_style () {
    if (isset($_GET['post_type']) && $_GET['post_type'] == 'me_order') {
        echo '<style type="text/css">
            #favorite-actions, .add-new-h2, .page-title-action, .hide-if-no-js { display:none; }
            @media screen and (max-width: 782px) {
                .wp-list-table tr:not(.inline-edit-row):not(.no-items) td:not(.column-primary)::before {
                    content: "" !important;
                }
                .wp-list-table .column-order_id,.wp-list-table .column-primary,.wp-list-table .column-status {
                    display : table-cell !important;
                }
                .wp-list-table thead th.column-primary {
                    width : 40% !important;
                }
            }
        </style>';
    }

    if (isset($_GET['post_type']) && $_GET['post_type'] == 'listing') {
        echo '<style type="text/css">
            #favorite-actions, .add-new-h2, .page-title-action, .hide-if-no-js { display:none; }
            .sign {font-weight: bold;}
            @media screen and (max-width: 782px) {
                .wp-list-table tr:not(.inline-edit-row):not(.no-items) td:not(.column-primary)::before {
                    content: "" !important;
                }
                .wp-list-table .column-author,.wp-list-table .column-primary{
                    display : table-cell !important;
                }
                .wp-list-table thead th.column-primary {
                    width : 40% !important;
                }
            }
        </style>';
    }
}

/**
 * Add marketengine admin menu
 * @category Admin/External
 */
function marketengine_load_admin_option_script_css() {
    if (!empty($_REQUEST['page']) && (strpos($_REQUEST['page'], 'me') !== false)) {
        wp_register_style('scrollbar-css', MARKETENGINE_URL . 'assets/admin/jquery.mCustomScrollbar.min.css', array(), '1.0');
        wp_enqueue_style('marketengine-font-icon', MARKETENGINE_URL . 'assets/css/marketengine-font-icon.css', array(), '1.0');
        wp_enqueue_style('me-option-css', MARKETENGINE_URL . 'assets/admin/marketengine-admin.css');

        wp_enqueue_script('backbone');
        wp_enqueue_script('jquery-scrollbar', MARKETENGINE_URL . 'assets/admin/jquery.mCustomScrollbar.min.js', array('jquery'), '1.0', true);
        wp_enqueue_script('marketengine-option', MARKETENGINE_URL . 'assets/admin/script-admin.js', array('jquery', 'jquery-scrollbar'), '1.0', true);
        wp_enqueue_script('option-view', MARKETENGINE_URL . 'assets/admin/option-view.js', array('jquery', 'backbone', 'jquery-scrollbar'), '1.0', true);
        wp_localize_script(
            'backbone',
            'me_globals',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
            )
        );
    }
    ?>
    <style type="text/css">
    .toplevel_page_marketengine, #menu-posts-listing {
        margin-top: 10px;
    }
    </style>
    <?php
}
add_action('admin_enqueue_scripts', 'marketengine_load_admin_option_script_css');

/**
 * Render marketengine admin option header
 * @category Admin
 * @since 1.0
 */
function marketengine_option_header() {
    ?>
<div class="wrap">
<?php marketengine_option_notices(); ?>
<div class="marketengine-admin">
    <div class="me-header">
        <span class="pull-left"><?php _e("MARKETENGINE", "enginethemes");?></span>
        <span class="pull-right"><?php _e("Power by", "enginethemes");?><a href="https://www.enginethemes.com/"><i class="icon-me-logo"></i></a></span>
    </div>
    <div class="me-body">
<?php
}

/**
 * Render marketengine admin option footer
 * @category Admin
 * @since 1.0
 */
function marketengine_option_footer() {

    ?>
    </div>
</div>
</div>
<?php
}

/**
 * Add custom class to marketengine admin menu
 * @category Admin
 * @since 1.0
 */
function marketengine_admin_menu_class() {
    global $menu;
    $menu[28][6] .= '-icon-me-logo';
}
add_action( 'admin_menu', 'marketengine_admin_menu_class', 10 );

function marketengine_admin_footer_text( $text) {
    $text = sprintf( 'Thank you for creating with <a href="%s">EngineThemes</a>.', 'https://www.enginethemes.com/' );
    return $text;
}
add_filter( 'admin_footer_text', 'marketengine_admin_footer_text' );

/**
 * Render MarketEngine Admin notices
 * @category    Admin
 * @since       1.0.1
 */
function marketengine_option_notices() {
?>
    <?php marketengine_get_template( 'admin/notices' ); ?>
<?php
}