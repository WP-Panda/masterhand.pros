<?php
$transaction = marketengine_get_order($case->post_parent);
$items = $transaction->get_listing_items();
$item = array_pop($items);
?>
<div class="me-disputed-info">
    <div class="me-disputed-product-order">
        <div class="me-row">
            <div class="me-col-md-6">
                <div class="me-disputed-order-info">
                    <h3><?php _e("Case information", "enginethemes");?></h3>
                    <p>
                        <span><?php _e("Case status:", "enginethemes");?></span>
                        <?php echo marketengine_dispute_status_label($case->post_status); ?>
                    </p>
                    <p>
                        <span><?php _e("Open date:", "enginethemes");?></span>
                        <?php echo date_i18n(get_option('date_format'), strtotime($case->post_date)); ?></p>
                    <p>
                        <span><?php _e("Listing:", "enginethemes");?></span>
                        <a href="<?php echo get_permalink($item['ID']); ?>">
                            <?php echo esc_html( $item['title'] ); ?>
                        </a>
                    </p>
                    <p>
                        <span><?php _e("Problem:", "enginethemes");?></span>
                        <?php echo marketengine_rc_dispute_problem_text($case->ID); ?>
                    </p>
                    <p>
                        <span><?php _e("You want to:", "enginethemes");?></span>
                        <?php echo marketengine_rc_case_expected_solution_label($case->ID); ?>
                    </p>
                </div>
            </div>
            <div class="me-col-md-6">
                <div class="me-disputed-order-info">
                    <h3><?php _e("Order information", "enginethemes");?></h3>
                    <p>
                        <span><?php _e("Order ID:", "enginethemes");?></span>
                        <a href="<?php echo $transaction->get_order_detail_url(); ?>">
                            #<?php echo $transaction->ID; ?>
                        </a>
                    </p>
                    <p>
                        <span><?php _e("Total amount:", "enginethemes");?></span>
                        <?php echo marketengine_price_format($transaction->get_total()); ?>
                    </p>
                    <p>
                        <span><?php _e("Order date:", "enginethemes");?></span>
                        <?php echo get_the_date(get_option('date_format'), $transaction->ID); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if( $case->post_status !=  'me-closed' ) : ?>
    
        <div class="me-disputed-action me-dispute-arbitrate">

            <?php

            if($case->post_status == 'me-open') {
                marketengine_get_template('resolution/case-details/case-open', array('case' => $case));
            }

            if($case->post_status == 'me-waiting') {
                marketengine_get_template('resolution/case-details/case-waiting', array('case' => $case));
            }

            if($case->post_status == 'me-escalated') {
                marketengine_get_template('resolution/case-details/case-escalated', array('case' => $case));
            }

            if($case->post_status == 'me-resolved') {
                marketengine_get_template('resolution/case-details/case-resolved', array('case' => $case));
            }

            ?>

        </div>

    <?php endif; ?>
    
</div>