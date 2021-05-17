<?php if(get_current_user_id() == $case->sender) : ?>

    <div class="me-row">
        <div class="me-col-md-6">
            <div class="me-disputed-close">
                <a onclick="if(!confirm('<?php _e("Are you sure you want to close this dispute?", "enginethemes"); ?>')) return false;" 
                href="<?php echo wp_nonce_url(add_query_arg(array('close' => $case->ID) ), 'me-close_dispute' ,'wpnonce' ) ?>">
                    <?php _e("Close dispute", "enginethemes"); ?>
                </a>
                <p>
                    <?php _e("In case you totally agree with what the seller offers, you can close this dispute. Once the dispute is closed, it cannot be re-opened.", "enginethemes"); ?>
                </p>
            </div>
        </div>
        <?php marketengine_get_template('resolution/case-details/buyer-escalate-button', array('case' => $case)); ?>
    </div>

<?php else : ?>

    <div class="me-row">
        <div class="me-col-md-6">
            <div class="me-disputed-close">
                <a onclick="if(!confirm('<?php _e("Are you sure you want to remind buyer of closing this dispute??", "enginethemes"); ?>')) return false;" 
                href="<?php echo wp_nonce_url(add_query_arg(array('request-close' => $case->ID) ), 'me-request_close_dispute' ,'wpnonce' ) ?>">
                    <?php _e("Request To Close", "enginethemes"); ?>
                </a>
                <p><?php _e("In case both buyer and you agree with the deal, you can request to finish the dispute.", "enginethemes"); ?></p>
            </div>
        </div>
        <?php marketengine_get_template('resolution/case-details/seller-escalate-button', array('case' => $case)); ?>
    </div>

<?php endif; ?>