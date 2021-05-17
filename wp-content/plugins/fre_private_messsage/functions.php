<?php

define( 'FREELANCER_UNREAD','freelancer_unread');
define( 'EMPLOYER_UNREAD','employer_unread');
/**
 * Plugin  function
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
*/
/**
  * get user profile
  * @param int $user_id the ID of user
  * @return array $userdata
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
if( !function_exists('ae_private_msg_user_profile') ){
	function ae_private_msg_user_profile( $user_id ){
		global $ae_post_factory;
		$profile_object = $ae_post_factory->get(PROFILE);
		$profile_id = get_user_meta($user_id, 'user_profile_id', true);
		$profile = get_post($profile_id);
		if(empty($profile)) return false;
		$profile = $profile_object->convert($profile);
		$userdata = array(
			'ID'=> $user_id,
			'position'=> $profile->et_professional_title,
			'avatar'=> get_avatar($user_id, 70),
			'user_name'=> $profile->post_title,
			'user_link'=> $profile->guid
		);
		return $userdata;
	}
}
/**
  * Check user is a project's owner
  * @param integer $user_id
  * @param integer $project_id
  * @return bool true/ false
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function is_project_owner( $user_id, $project_id ){
	$project = get_post( $project_id );
	if( isset($project->post_author) && (int)$project->post_author == $user_id ){
		return true;
	}
	return false;

}
/**
 * Check user is a project's owner
 * @param integer $user_id
 * @param integer $bid_id
 * @return bool true/ false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function is_bid_owner( $user_id, $bid_id ){
	$bid = get_post( $bid_id );
	if( isset($bid->post_author) && (int)$bid->post_author == $user_id ){
		return true;
	}
	return false;

}
/**
 * Get user email
 * @param integer $user_id
 * @return string $email | false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_get_user_email( $user_id ){
	$user_data = get_userdata( $user_id );
	if( isset($user_data->user_email) ) {
		$user_email = $user_data->user_email;
		return $user_email;
	}
	else{
		return false;
	}
}
/**
 * Get user display name
 * @param integer $user_id
 * @return string $display_name | false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_get_user_display_name( $user_id ){
	$user_data = get_userdata( $user_id );
	if( isset($user_data->display_name ) ) {
		$display_name  = $user_data->display_name ;
		return $display_name ;
	}
	else{
		return false;
	}
}
/**
  * default query_args of private message fetch
  * @param array $args
  * @param boolean $login
  * @return array $args after filter
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function ae_private_message_default_query_args( $args , $login = true){
	global $user_ID;
	$default = array(
		'posts_per_page' => 10,
		'post_type' => 'ae_private_message',
		'post_status' => array('publish', 'unread'),
		'meta_query' => array(
			array(
				'key' => 'is_conversation',
				'value' => '1'
			)

		)
	);
	$args = wp_parse_args( $args, $default);
	if(!fre_share_role() || $login){
		if( ae_user_role($user_ID) == EMPLOYER ||  ae_user_role($user_ID) == 'administrator'){
			$args = wp_parse_args( array('author'=> $user_ID), $args);
			$meta_query = array(
				'relation'=> 'AND',
				array(
					'key'=>'archive_on_sender',
					'value'=> 0
				)
			);
			$args['meta_query'] = wp_parse_args( $meta_query, $args['meta_query']);
		}
		else if(ae_user_role($user_ID) == FREELANCER ) {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key' => 'to_user',
					'value' => $user_ID
				),
				array(
					'key' => 'archive_on_receiver',
					'value' => 0
				)
			);
			$args['meta_query'] = wp_parse_args($meta_query, $args['meta_query']);
		}
	} else {
		add_filter('posts_join', 'custom_post_join');
		add_action('posts_where', 'custom_post_where');
	}
	return $args;
}
function custom_post_join($join){
    global $wpdb, $wp_query;
    $query = $wp_query->query;
    if (isset($query['post_type']) && $query['post_type'] == 'ae_private_message'){
        $join .= " INNER JOIN {$wpdb->postmeta} as postmeta1 ON {$wpdb->posts}.ID = postmeta1.post_id ";
        $join .= " INNER JOIN {$wpdb->postmeta} as postmeta2 ON {$wpdb->posts}.ID = postmeta2.post_id ";
    }
    return $join;
}
function custom_post_where($where){
	global $wp_query, $user_ID;
	$query = $wp_query->query;
    if (isset($query['post_type']) && $query['post_type'] == 'ae_private_message'){
		$where .= " AND (
					( postmeta1.meta_key = 'archive_on_sender' AND postmeta1.meta_value = '0' AND postmeta2.meta_key = 'from_user' AND postmeta2.meta_value = $user_ID) OR

					( postmeta1.meta_key = 'archive_on_receiver' AND postmeta1.meta_value = '0' AND postmeta2.meta_key = 'to_user' AND postmeta2.meta_value = $user_ID)
				)";
	}
	return $where;
}
/**
 * default query_args of private message replies fetch
 * @param array $args
 * @return array $args after filter
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_replies_default_query_args( $args ){
	$default = array(
		'post_type' => 'ae_private_message',
		'post_status' => array('publish', 'unread'),
		'meta_query' => array(
			array(
				'key' => 'is_conversation',
				'value' => '0'
			)
		)
	);
	$args = wp_parse_args( $args, $default);
	return $args;
}
/**
 * get new message number
 * @param int $user_id the ID of user
 * @return integer  new message number
 * @since 1.1.5
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author danng
 */
function ae_private_message_get_new_number( $user_id ){
	global $ae_post_factory, $user_ID;
    $post = $ae_post_factory->get('ae_private_message');
    $query_args = array();
    $array = array();
    if( ae_user_role($user_ID) == EMPLOYER ||  ae_user_role($user_ID) == 'administrator'){
        $array = array('post_status' => array('unread'), 'posts_per_page' => -1);
        $query_args['meta_query'] = array(
        array(
            'key' => 'is_conversation',
            'value' => 1)
        );
    }elseif(ae_user_role($user_ID) == FREELANCER ){
        $array = array('post_status' => array('unread', 'publish'), 'posts_per_page' => -1);
        $query_args['meta_query'] = array(
        array(
            'key' => 'is_conversation',
            'value' => 1),
        array(
            'key' => 'conversation_status',
            'value' => 'unread')
        );
    }
    $query_args = ae_private_message_default_query_args($query_args, true);
    $query_args = wp_parse_args( $array, $query_args);
    $data = $post->fetch($query_args);
    $data = empty($data['query']->found_posts) ? '0' : $data['query']->found_posts;
    return $data;

 //    $count = wp_cache_get( "reply_unread-{$user_id}", 'counts' );
	// if ( false !== $count ) {
	// 	return $count;
	// }

	// global $wpdb;
	// $meta = 'number_reply_fre_unread';
 //    if( ae_user_role($user_id) == EMPLOYER || current_user_can( 'manage_options' ) ) {
 //        $meta = 'number_reply_emp_unread';

 //    }
 //    $args = ae_private_message_default_query_args(array(), true);
 //    $s = 0;
 //    $conversations = new WP_Query($args);
	// if($conversations->have_posts()){

	//     while( $conversations->have_posts() ):

 //        $conversations->the_post();
	//        		$number_reply_unread = (int)get_post_meta( get_the_ID(), $meta, true);
	//        		$s = $s + $number_reply_unread;
	//     endwhile;
	//     wp_reset_query();

	// }
	// wp_cache_set( "reply_unread-{$user_id}", $s, 'counts' );
	// return $s;
}

/**
 * update new message
 * @param integer $user_id
 * @param integer $pl
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_update_new_number( $user_id, $pl = 1 ){
	$number = ae_private_message_get_new_number($user_id);
	$number += $pl;
	if( $number < 0 ){
		$number = 0;
	}
	update_user_meta($user_id, 'fre_new_private_message', $number);
}
/**
 * update new message
 * @param int $user_id
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_reset_unread_message( $user_id ){
	update_user_meta($user_id, 'fre_new_private_message', 0);
}
/**
 * get conversation info
 * @param int $conversation_id
 * @return object conversation or false
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_get_conversation( $conversation_id ) {
	global $ae_post_factory;
	$post = get_post($conversation_id);
	$conversation = false;
	if ($post && !is_wp_error($post)) {
		$post_obj = $ae_post_factory->get('ae_private_message');
		$conversation = $post_obj->convert($post);
		$conversation->post_date_gmt = $post->post_date_gmt;
	}

	return $conversation;
}
/**
  * update freelancer unread reply
  * @param object $conversation
  * @return void
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
 function ae_private_message_update_unread_reply( $conversation ){
	 global $user_ID;
	 if( ae_user_role($user_ID) == EMPLOYER || current_user_can( 'manage_options' ) ){
		 $unread = 0;
		 if( isset($conversation->freelancer_unread) ) {
			 $unread = (int)$conversation->freelancer_unread;
		 }
		 $unread += 1;
		 update_post_meta($conversation->ID, FREELANCER_UNREAD, $unread);
	 } else if( ae_user_role($user_ID) == FREELANCER ){
		 $unread = 0;
		 if( isset($conversation->employer_unread)){
			 $unread = (int)$conversation->employer_unread;
		 }
		 $unread += 1;
		 update_post_meta($conversation->id, EMPLOYER_UNREAD , $unread);
	 }
 }
/**
 * reset freelancer unread reply
 * @param object $conversation
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_reset_unread_reply( $conversation ){
	global $user_ID;
	if( ae_user_role($user_ID) == EMPLOYER || current_user_can( 'manage_options' ) ){
		update_post_meta($conversation->ID, EMPLOYER_UNREAD, 0);
	}
	else if(ae_user_role($user_ID) == FREELANCER ){
		update_post_meta($conversation->id,FREELANCER_UNREAD , 0);
	}
}
/**
 * get freelancer or employer unread reply
 * @param object $conversation
 * @return integer unread reply
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_get_unread_reply( $conversation )
{
	global $user_ID;
	if(isset($conversation->employer_unread) ) {
		if (ae_user_role($user_ID) == EMPLOYER || current_user_can( 'manage_options' ) ) {
			return (int)$conversation->employer_unread;
		} else if (ae_user_role($user_ID) == FREELANCER) {
			return (int)$conversation->freelancer_unread;
		}
	}
	return 1;
}
/**
 * check did employer send a conversation to a freelancer
 * @param array $data
 * @return array $response
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
function ae_private_message_created_a_conversation($data){
	$response = array(
		'success' => false,
		'msg' => __("You already created a conversation for this bid!", ET_DOMAIN)
	);
	if( !isset($data['bid_id']) ){
		return $response;
	}
	$sent = get_post_meta($data['bid_id'], 'sent_private_msg', true);
	$response['conversation_id'] = $sent;
	if( !$sent ){
		$response = array(
			'success' => true,
			'msg' => __("You hasn't created a conversation for this bid,yet!", ET_DOMAIN),
			'data'=> $data
		);

		if(!empty($data['project_id']) && !empty($data['author'])){
			$list_conversation_of_project = get_posts(array(
				'posts_per_page' => -1,
				'post_status' => array('publish','unread'),
				'post_type' => 'ae_private_message',
				'author' => get_current_user_id(),
				'meta_query' => array(
					array(
						'key'   => 'to_user',
						'value' => $data['author'],
					),
					array(
						'key'   => 'is_conversation',
						'value' => '1',
					),
					array(
						'key'   => 'project_id',
						'value' => $data['project_id'],
					)
				)
			));

			if(!empty($list_conversation_of_project)){
				$old_conversation = array_shift($list_conversation_of_project);

				update_post_meta($old_conversation->ID,'bid_id',$data['bid_id']);

				$response = array(
					'success' => false,
					'msg' => __("You already created a conversation for this bid!", ET_DOMAIN),
					'conversation_id'=> $old_conversation->ID,
				);
			}
		}

		return $response;
	}
	return $response;

}
/**
  * check current user can view this conversation
  * @param array $conversation
  * @return bool true if current user can view this conversation and false if can't
  * @since 1.0
  * @package FREELANCEENGINE
  * @category PRIVATE MESSAGE
  * @author Tambh
  */
function ae_private_message_user_can_view_conversation($conversation){
	global $user_ID;
	if( $user_ID == $conversation->post_author || $user_ID == $conversation->to_user){
		return true;
	}
	return false;
}
function aepm_update_count_message_unread($user_id, $conversation_id){
	global $wpdb;
	$meta = 'number_reply_emp_unread';
    if( ae_user_role($user_id) == EMPLOYER || current_user_can( 'manage_options' ) ) {
        $meta = 'number_reply_fre_unread';
    }
    $number_new_message = (int) get_post_meta($conversation_id, $meta,true);
    $number_new_message = $number_new_message +1;
    update_post_meta( $conversation_id, $meta, $number_new_message );
}
function aem_reset_count_unread( $conversation, $user_id){
	$meta = 'number_reply_fre_unread';
    if( $conversation->post_author == $user_id || current_user_can( 'manage_options' ) ) {
        $meta = 'number_reply_emp_unread';
    }
    update_post_meta( $conversation->ID, $meta, 0 );
}