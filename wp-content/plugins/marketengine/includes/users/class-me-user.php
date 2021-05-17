<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME_User
 *
 * User behavior manager
 *
 * @class       ME User
 * @version     1.0
 * @package     MarketEngine/Users
 * @author      EngineThemesTeam
 * @category    Class
 */
class ME_User {
    public $id;
    public $user_data;
    public function __construct($user) {
        $this->id = $user->ID;
        $this->user_data = $user->user_data;
    }

    public function __get($name) {
        if (isset($this->user_data[$name])) {
            return $this->user_data[$name][0];
        }
        return get_the_author_meta($name, $this->id);
    }

    public function is_activated() {
        $is_required_email_confirmation = marketengine_option('user-email-confirmation') ? true : false;
        return (!$is_required_email_confirmation || !get_user_meta($this->id, 'activate_email_key', true));
    }

    public function get_avatar($size = 96) {
        $user_avatar = get_user_meta( $this->id, 'user_avatar', true);
        if($user_avatar) {
            $avatar_obj = wp_get_attachment_image_src( $user_avatar, 'thumbnail' );
            return '<img alt="" src="'.$avatar_obj[0].'" class="avatar avartar-'.$size.' photo" height="'.$size.'" width="'.$size.'">';
        }
        return get_avatar($this->id);
    }

    public function get_user_avatar_id() {
        return get_user_meta( $this->id, 'user_avatar', true);
    }
}