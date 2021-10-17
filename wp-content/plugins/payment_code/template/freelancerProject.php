<div class="workspace-payment-code-wrap">
    <?php if($dataCode['used']){ ?>
    <div class="fre-submit-btn paycode open-modal-review" data-pid="<?=$projectId;?>"><?php _e('Review for Client');?></div>
        <style>
            .paycode.open-modal-review{
                float:right; margin-top: 20px; margin-right: 20px;
            }
        </style>
    <?php } else { ?>
    <div class="workspace-title"><?php _e("Payment Code", ET_DOMAIN ); ?>
    <?php $homeid = get_option('page_on_front');?>
       <a class="payment-desc" data-toggle="tooltip" data-html="true" title='<?php the_field('about_payment_code',$homeid);?>'></a>
    </div>
    <div class="workspace-payment-code">
        <form class="form-send-payment-code" method="POST" data-pid="<?=$projectId;?>">
            <input type="hidden" name="action" value="payCode"/>
            <input type="text" size="50" max-size="100" name="code" value="" placeholder="<?php _e( "Enter code", ET_DOMAIN ); ?>" />
            <input type="submit" value="<?php _e( 'Submit', ET_DOMAIN ); ?>" />
        </form>
    </div>
    <?php } ?>
</div>