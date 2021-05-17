<?php 
get_header();
$case_id = get_query_var( 'case_id' );
$case = marketengine_get_message($case_id);
?>
<div id="marketengine-page">
    <div class="me-container">
        <div class="marketengine-content-wrap">

            <?php if(!empty($_GET['action']) && $_GET['action'] == 'escalate' && in_array($case->post_status, array('me-open', 'me-waiting'))) : ?>
                <?php marketengine_get_template('resolution/form/escalate', array('case' => $case) ) ?>
            <?php else : ?>
                <?php marketengine_get_template('resolution/case-details', array('case' => $case) ) ?>
            <?php endif; ?>
            
        </div>
    </div>
</div>
<?php
get_footer();
?>
