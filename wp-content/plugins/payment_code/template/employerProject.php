<div class="workspace-payment-code-wrap">
	<?php if ( $dataCode['used'] ) { ?>
        <div class="fre-submit-btn paycode open-modal-review"
             data-pid="<?php echo $projectId; ?>"><?php _e( 'Review for Professional' ); ?></div>
        <style>
            .paycode.open-modal-review {
                float: right;
                margin-top: 20px;
                margin-right: 20px;
            }
        </style>
	<?php } else { ?>
        <div class="workspace-title"><?php echo __( "Payment Code", ET_DOMAIN ); ?>
			<?php $homeid = get_option( 'page_on_front' ); ?>
            <a class="payment-desc" data-toggle="tooltip" data-html="true"
               title='<?php the_field( 'about_payment_code', $homeid ); ?>'></a>
        </div>
        <div class="workspace-payment-code">
            <div class="client-code">
                <div class="code"><?php echo $code ?></div>
                <button class="fre-submit-btn send-payment-code"
                        data-pid="<?php echo $projectId ?>"><?php _e( 'Get it by email', ET_DOMAIN ); ?></button>
            </div>
        </div>
	<?php } ?>
</div>