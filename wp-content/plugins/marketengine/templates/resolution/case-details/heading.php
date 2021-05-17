
<div class="marketengine-page-title me-have-breadcrumb">
    <h2>
        <?php printf(__("CASE #%d", "enginethemes"), $case->ID); ?>
    </h2>
    <a href="<?php echo marketengine_resolution_center_url(); ?>"></a>
    <ol class="me-breadcrumb">
        <li>
            <a href="<?php echo marketengine_resolution_center_url(); ?>">
                <?php _e("Resolution Center", "enginethemes"); ?>
            </a>
        </li>
        <li>
            <a href="#">
                #<?php echo $case->ID; ?>
            </a>
        </li>
    </ol>
</div>