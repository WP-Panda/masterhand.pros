<?php marketengine_get_template('resolution/case-details/heading', array('case' => $case)); ?>
<!-- marketengine-content -->
<div class="marketengine-content">

    <?php marketengine_get_template('resolution/case-details/mobile-nav'); ?>

    <div class="me-disputed-case">
        
        <?php marketengine_get_template('resolution/case-details/info', array('case' => $case)); ?>

        <div class="me-disputed-conversation">
            <div class="me-row">
                <div class="me-col-md-3 me-col-md-pull-9 me-col-sm-4 me-col-sm-pull-8">
                    <div class="me-sidebar-contact">
                            
                        <?php marketengine_get_template('resolution/case-details/related-party', array('case' => $case)); ?>
                        <?php marketengine_get_template('resolution/case-details/dispute-event', array('case' => $case)); ?>
                        
                    </div>
                </div>
                <div class="me-col-md-9 me-col-md-push-3 me-col-sm-8 me-col-sm-push-4">
                    <div class="me-contact-messages-wrap">
                        <div class="me-contact-message-user">
                            <p>
                                <?php 
                                if(get_current_user_id() == $case->sender) {
                                    echo get_the_author_meta( 'display_name', $case->receiver );
                                }elseif(get_current_user_id() == $case->receiver) {
                                    echo get_the_author_meta( 'display_name', $case->sender );
                                } elseif (current_user_can('manage_options')) {
                                    echo get_the_author_meta( 'display_name', $case->receiver );
                                }
                                ?>
                            </p>
                        </div>

                        <?php 
                            $message_query = new ME_Message_Query(array('post_type' => array('message', 'revision'), 'post_parent' => $case->ID, 'showposts' => 12));
                            $messages = array_reverse ($message_query->posts);
                        ?>
                        <div class="dispute-message-wrapper">
                            <div class="me-contact-messages" id="messages-container" style="overflow: hidden;overflow-y: scroll; max-height: 500px;">
                                <ul class="me-contact-messages-list">
                                <?php if( $messages ) : ?>
        						<?php foreach ($messages  as $key => $message) : ?>
        							<?php 
                                    if($message->post_type == 'revision') {
                                        marketengine_get_template('resolution/revision-item', array('message' => $message));
                                    }else {
                                        marketengine_get_template('resolution/message-item', array('message' => $message));    
                                    }
                                     ?>
        						<?php endforeach; ?>
        						<?php endif; ?>
                                    
                                </ul>
                            </div>
                            <script type="text/javascript">
                                var objDiv = document.getElementById("messages-container");
                                objDiv.scrollTop = objDiv.scrollHeight;
                            </script>
                        </div>
                        

                        <?php if($case->post_status != 'me-closed' && $case->post_status != 'me-resolved') : ?>

                            <div class="me-message-typing-form">
                                <form id="dispute-message-form">

                                    <textarea name="post_content" id="debate_content" placeholder="New message"></textarea>
                                    <div class="me-dispute-attachment">
                                        <div class="me-row">
                                            <div class="me-col-lg-10 me-col-md-9">
                                                <?php 
                                                    marketengine_get_template('upload-file/upload-file-form', array(
                                                        'id' => 'dispute-file',
                                                        'class' => 'me-gallery-file-wrap',
                                                        'name' => 'dispute_file',
                                                        'source' => '',
                                                        'button' => 'me-dipute-upload',
                                                        'button_text' => '<i class="icon-me-attach"></i> ' . __("Add attachment", "enginethemes"),
                                                        'multi' => true,
                                                        'maxsize' => esc_html( '2mb' ),
                                                        'maxcount' => 5,
                                                        'close' => true,
                                                    ));

                                                ?>
                                                <?php wp_nonce_field('marketengine', 'me-dispute-file'); ?>
                                            </div>
                                            
                                            <div class="me-col-lg-2 me-col-md-3">
                                                
                                                <input class="marketengine-btn me-dispute-message-btn" type="submit" value="<?php _e("SUBMIT", "enginethemes"); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php wp_nonce_field( 'me-debate'); ?>
                                    <input type="hidden" name="dispute" id="dispute_id" value="<?php echo $case->ID; ?>">
                                </form>
                            </div>

                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--// marketengine-content -->