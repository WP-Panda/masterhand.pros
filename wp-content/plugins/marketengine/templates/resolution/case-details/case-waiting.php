<?php if(get_current_user_id() == $case->sender) : ?>
    <div class="me-row">
        <div class="me-col-md-6">
            <div class="me-disputed-close">
                <a onclick="if(!confirm('<?php _e("Are you sure you want to close this dispute?", "enginethemes"); ?>')) return false;" 
                href="<?php echo wp_nonce_url(add_query_arg(array('close' => $case->ID) ), 'me-close_dispute' ,'wpnonce' ) ?>">
                    <?php _e("Accept close", "enginethemes"); ?>
                </a>
                <p>
                    <?php _e("The seller has requested to close this dispute. In case you are not satisfied with the proposal, you can continue negotiating or escalating to admin.", "enginethemes"); ?>
                </p>
            </div>
        </div>
        <?php marketengine_get_template('resolution/case-details/buyer-escalate-button', array('case' => $case)); ?>
    </div>

<?php else : ?>

    <div class="me-row">
        <div class="me-col-md-6">
            <div class="me-disputed-request-close">
                <h4><?php printf(__("Waiting for Buyer's respond", "enginethemes"), get_the_author_meta( 'display_name', $case->sender)); ?></h4>
                <p><?php _e("You have already sent a request to close the dispute to buyer. Whenever buyer accepts your request, the dispute will be closed.", "enginethemes"); ?></p>
            </div>

        </div>
        <?php marketengine_get_template('resolution/case-details/seller-escalate-button', array('case' => $case)); ?>
    </div>

<?php endif; ?>