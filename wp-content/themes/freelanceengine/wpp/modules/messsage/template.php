<?php
/**
 * Plugin  template
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
/**
 * Message button
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
if( !function_exists('ae_private_message_button') ) {
	function ae_private_message_button($bid, $project)
	{
		global $user_ID;
		$to_user = ae_private_msg_user_profile((int)$bid->post_author);
		$response = ae_private_message_created_a_conversation(array('bid_id'=>$bid->ID,'project_id' => $project->ID,'author' => $bid->post_author));

        // for button Contact in page project with author bid
        $bid_accepted = $project->accepted;
        $bid_accepted_author = get_post_field('post_author', $bid_accepted);
        if (($user_ID == (int)$project->post_author || $bid_accepted_author == $user_ID) && ($project->post_status == 'publish' or $project->post_status == 'close')) {
//        if ($user_ID == (int)$project->post_author && $project->post_status == 'publish') {
            if( $response['success'] ){
                $data = array(
					'bid_id'=> $bid->ID,
					'to_user'=> $to_user,
					'project_id'=> $project->ID,
					'project_title'=> $project->post_title,
					'from_user'=> $user_ID,
				);
				?>
                <a class="unsubmit-btn btn-right btn-send-msg btn-open-msg-modal" href="javascript:void(0)"><?php _e('Contact',ET_DOMAIN) ?>
                    <script type="data/json"  class="privatemsg_data">
                        <?php  echo json_encode( $data ) ?>
                    </script>
                </a>
			<?php }else{ ?>
                <a class="fre-cancel-btn btn-send-msg btn-redirect-msg" href="javascript:void(0)"  data-conversation="<?php echo $response['conversation_id'] ?>">
					<?php _e('Contact',ET_DOMAIN) ?>
                </a>
				<?php
			} ?>

            <?php /*if ($bid_accepted_author == $user_ID) { ?>
                <a class="fre-action-btn fre-submit-btn btn-right bid-action" data-action="edit" data-bid-id="<?php echo $bid->ID?>" style="vertical-align:top">
                    <?php _e('Edit', ET_DOMAIN)?>
                </a>
            <?php }*/ ?>

		<?php }
	}
}
/**
 * Private message modal
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category PRIVATE MESSAGE
 * @author Tambh
 */
if( !function_exists( 'ae_private_message_modal' ) ){
	function ae_private_message_modal(){ ?>
        <!-- MODAL Send Message -->
        <div class="modal fade" id="modal_msg">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"></button>
                        <?php _e("Send Message", ET_DOMAIN) ?>
                    </div>
                    <div class="modal-body">
                        <div>
                            <form role="form" id="private_msg_form" class="fre-modal-form" >
                                <div class="fre-content-confirm">
                                    <p><?php _e("Type your message into the message box, and then click the Send button.",ET_DOMAIN) ?></p>
                                    <br>
                                </div>

                                <input id="inputSubject" name="post_title" type="hidden" class="form-control width100p" value="<?php _e('no subject', ET_DOMAIN); ?>">

                                <div class="fre-input-field">
                                    <label class="fre-field-title"><?php _e('Message', ET_DOMAIN); ?></label>
                                    <textarea name="post_content" id="" cols="30" rows="10"></textarea>
                                </div>

                                <div class="fre-form-btn">
                                    <button type="submit" class="fre-submit-btn btn-left btn-send-msg-modal"><?php _e('Send', ET_DOMAIN); ?></button>
                                    <span class="fre-cancel-btn" data-dismiss="modal"><?php _e('Cancel', ET_DOMAIN) ?></span>
                                </div>

                                <input type="hidden" name="from_user" value="" />
                                <input type="hidden" name="to_user" value="" />
                                <input type="hidden" name="project_id" value="" />
                                <input type="hidden" name="project_name" value="" />
                                <input type="hidden" name="bid_id" value="" />
                                <input type="hidden" name="is_conversation" value="1" />
                                <input type="hidden" name="conversation_status" value="unread" />
                                <input type="hidden" name="sync_type" value="conversation" />
                            </form>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
	<?php }
}
if( !function_exists('ae_private_message_add_profile_tab_template') ) {
	/**
	 * add profile tab content html
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category PRIVATE MESSAGE
	 * @author Tambh
	 */
	function ae_private_message_add_profile_tab_template()
	{
		global $user_ID;
		$number = ae_private_message_get_new_number($user_ID);
		$class = '';
		?>
        <li>
            <a href="#tab_private_msg" role="tab" data-toggle="tab" class="ae-private-message-conversation-show">
                <span>
                     <?php _e('Messages', ET_DOMAIN);
                     $num = $number;
                     if($number > 100){
	                     $num = '99+';
                     }
                     if($num <= 0){
	                     $num = 0;
	                     $class = 'hidden';
                     }
                     echo '<span class="msg-number '. $class .' "> ' . $num . ' </span>';
                     ?>
                </span>
            </a>
        </li>
	<?php }
}

if( !function_exists('ae_private_message_add_profile_tab_content_template') ){
	function ae_private_message_add_profile_tab_content_template(){
	    ?>
        <div class="fre-page-wrapper conversation-panelr" id="tab_private_msg">
            <div class="fre-page-title">
                <div class="container">
                    <h1><?php _e('Messenger') ?></h1>
                </div>
            </div>

            <div class="fre-page-section">
                <div class="container">
                    <div class="fre-inbox-wrap">
                        <div class="row">
                            <div class="col-md-4 private-message-conversation-contents ">
                                <div class="inbox-user-wrap">
                                    <div class="search-inbox-user">
                                        <?php $placeholder_search = 'Search professional name';
                                        global $user_ID;
                                        $user_role = ae_user_role($user_ID);
                                        if($user_role == 'freelancer'){
                                            $placeholder_search = 'Search client name';
                                        }
                                        ?>
                                        <input class="search" type="text" name="s"
                                               placeholder="<?php _e($placeholder_search,ET_DOMAIN) ?>">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <div class="chosen-inbox-read">
		                                <?php
		                                global $user_ID;
		                                $name = 'conversation_status';
		                                if( ae_user_role($user_ID) == EMPLOYER ||ae_user_role($user_ID) == 'administrator'){
			                                $name = 'post_status';
		                                }
		                                ?>
                                        <select class="fre-chosen-single fre-filter-conversation"
                                                data-chosen-width="20%" data-chosen-disable-search="1"
                                                data-placeholder="<?php _e('Select a status') ?>" name="<?php echo $name; ?>">
                                            <option value=""><?php _e('All', ET_DOMAIN); ?></option>
                                            <option value="unread"><?php _e('UnRead', ET_DOMAIN); ?></option>
                                        </select>
                                    </div>
                                        
									<?php
									$args = array();
									$args = ae_private_message_default_query_args($args, true);
									global $ae_post_factory, $post, $wp_query;
									query_posts($args);
									$post_object = $ae_post_factory->get('ae_private_message');
									$conversation_data = array();
									if( have_posts() ):
                                        echo '<div class="inbox-user-list-wrap">';
                                        echo '<ul class="inbox-user-list">';
										while( have_posts() ) : the_post();
											$convert    = $post_object->convert($post);
											$conversation_data[]  = $convert;
										endwhile;
                                        echo '</ul>';
                                        echo '</div>';
									else:
										_e('<div class="no-message-wrap"><p>You have not created any conversation yet. Please come back to your bidders list in Project detail for starting a conversation with bidders.</p></div>', ET_DOMAIN);
									endif;
									?>

                                    <?php
                                        ae_pagination($wp_query, get_query_var('paged'), 'load_more');
                                        wp_reset_query();
                                    ?>

									<?php echo '<script type="data/json" class="ae_private_conversation_data" >'.json_encode($conversation_data).'</script>';?>

                                </div>
                            </div>
                            <div class="col-md-8 private-message-reply-contents" <?php echo empty($conversation_data) ? 'style ="display:block"' : '' ?>>
								<?php
                                ae_private_message_reply_content();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<?php }
}
if( !function_exists('ae_private_message_loop_item') ){
	/**
	 * Private message conversation loop item
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category PRIVATE MESSAGE
	 * @author Tambh
	 */
	function ae_private_message_loop_item(){
		global $user_ID;
		?>
        <script type="text/template" id="ae-private-message-loop">
            <div class="inbox-item-wrap inbox-item-wrap-{{=ID}} action" data-action="show">
                {{=conversation_author_avatar}}
                <h2>{{= conversation_author_name }}</h2>
                <h2>{{= project_name }}</h2>
                <p>
                    {{= last_conversation_icon}} {{= last_conversation_content }}
                </p>
                <span>{{= last_conversation_date }}</span>
            </div>
        </script>
	<?php    }

}

if( !function_exists('ae_private_message_add_notification_menu_template') ){
	/**
	 * header menu message template
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category PRIVATE MESSAGE
	 * @author Tambh
	 */
	function ae_private_message_add_notification_menu_template(){
	    $class = 'class="trigger-overlay trigger-messages"';?>
        <li>
            <a href="<?php echo et_get_page_link('private-message') ?>">
                <!-- <i class="fa fa-inbox"></i> -->
				<?php
				global $user_ID;
				$message_number = get_user_meta($user_ID, 'total_unread', true);
				_e("Messenger", ET_DOMAIN);
				if( $message_number ) {
					echo ' <span class="notify-number">(' . $message_number . ')</span> ';
				}
				?>
            </a>
        </li>
	<?php }
}

if( !function_exists('ae_private_message_reply_content') ){
	function ae_private_message_reply_content(){
		?>
        <div class="row">
            <div class="col-md-12  col-sm-12 col-xs-12">
                <div class="inbox-content-wrap">
                    <h2 class="inbox-project-title">
                            <span class="fre-back-inbox-btn visible-sm visible-xs">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            </span>
                        <span class="title-conversation" style="text-decoration: none"></span>
                    </h2>
                    <div class="fre-conversation-wrap fre-inbox-message" style="position: relative">
                        <ul class="fre-conversation-list">
                        </ul>
                    </div>
                    <div class="conversation-typing-wrap ae-pr-msg-reply-form">
                        <form action="" id="private_msg_reply_form" novalidate="novalidate">
                            <div class="conversation-typing">
                                <textarea name="post_title" class="content-chat" placeholder="<?php _e('Your message here...', ET_DOMAIN) ?>" style="height: 38px;"></textarea>

                                <input type="hidden" name="post_content" value="" />
                                <input type="hidden" name="post_parent" value="" />
                                <input type="hidden" name="sync_type" value="reply" />
                            </div>

                            <div class="conversation-submit-btn">
                                <label class="conversation-send-message-btn disabled" for="conversation-send-message">
                                    <input id="conversation-send-message" type="submit" class="ae-pr-msg-reply-submit" disabled>
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
	<?php   }
}
if( !function_exists('ae_private_message_rely_loop_item') ){
	/**
	 * Private message reply loop item
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category PRIVATE MESSAGE
	 * @author Tambh
	 */
	function ae_private_message_reply_loop_item(){ ?>
        <script type="text/template" id="ae-private-message-reply-loop">
            <span class="message-avatar">
                {{= post_author_avatar}}
            </span>
            <div class="message-item">
                <p> {{= post_content }}</p>
            </div>
        </script>
	<?php    }

}
function ae_private_message_redirect(){
	if( isset($_GET['pr_msg_c_id']) ){
		$conversation = ae_private_message_get_conversation($_GET['pr_msg_c_id']);
		if( $conversation && ae_private_message_user_can_view_conversation($conversation) ){
			echo '<script type="data/json" class="ae_private_conversation_redirect_data" >'.json_encode($conversation).'</script>';
		}
	}
}