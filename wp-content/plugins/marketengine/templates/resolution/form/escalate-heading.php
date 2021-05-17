<div class="marketengine-page-title me-have-breadcrumb">
    <h2>
        <?php _e("ESCALATE", "enginethemes"); ?>
    </h2>
    <a href="<?php echo marketengine_rc_dispute_link($case->ID); ?>"></a>
    <ol class="me-breadcrumb">
        <li>
            <a href="<?php echo marketengine_resolution_center_url(); ?>">
                <?php _e("Resolution Center", "enginethemes"); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo marketengine_rc_dispute_link($case->ID); ?>">
                #<?php echo $case->ID; ?>
            </a>
        </li>
        <li>
            <a href="#">
                <?php _e("Escalate", "enginethemes"); ?>
            </a>
        </li>
    </ol>
</div>