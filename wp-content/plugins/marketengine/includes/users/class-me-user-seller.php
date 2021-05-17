<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
/**
* ME_Seller
*
* User behavior manager
*
* @class       ME Seller
* @version     1.0
* @package     MarketEngine/Users
* @author      EngineThemesTeam
* @category    Class
*/
class ME_Seller extends ME_User {
	public function __construct($id) {
        $this->id = $id;
        $this->user_data = get_user_meta($id);
    }
    public function get_my_listings_permalink() {
    	$profile_page = marketengine_get_page_permalink( 'user_account' );
    	$listings_endpoint = marketengine_get_endpoint_name( 'listings' );
    	return $profile_page . '/'.$listings_endpoint;
    }
}
