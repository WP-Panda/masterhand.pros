<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Options_Handle
{

    static $_instance;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        add_action('wp_ajax_me-option-sync', array($this, 'option_sync'));
    }
    /**
     *  Sync option
     *  @author     KyNguyen
     */

    public function option_sync()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json(array('success' => false, 'msg' => __("You do not have permission to change option.", 'enginethemes')));
        }

        $request = $_REQUEST;

        $name  = sanitize_text_field( $request['name'] );
        $value = array();

        $options = ME_Options::get_instance();

        if (is_string($request['value'])) {
            $request['value'] = stripslashes(sanitize_text_field($request['value'] ));
        }

        $value          = $request['value'];
        if(!empty($request['type']) && $request['type'] == 'number') {
            $value =(float) $value;
        }

        $options->$name = $value;
        $options->save();

        $options_arr = $options->get_all_current_options();
        $id          = array_search($name, array_keys($options_arr));
        $response    = array(
            'success' => true,
            'data'    => array(
                'ID' => $id,
            ),
            'msg'     => __("Update option successfully!", 'enginethemes'),
        );
        wp_send_json($response);
    }
}
ME_Options_Handle::instance();