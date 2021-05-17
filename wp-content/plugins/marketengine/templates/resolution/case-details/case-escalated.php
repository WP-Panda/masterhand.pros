<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$escalated_user = marketengine_get_message_meta($case->ID, '_escalated_by', true);
$escalate_user_name = get_the_author_meta( 'display_name', $escalated_user );
$winner = empty($_POST['me-dispute-win']) ? '' : $_POST['me-dispute-win'];
?>
<?php marketengine_print_notices(); ?>
<?php if(current_user_can('manage_options')) : ?>
    <form id="me-dispute-arbitrate-form" action="" method="post">
        <p><?php printf(__("%s has escalated the dispute. The final result of the dispute is your adjudication.", "enginethemes"), $escalate_user_name) ?></p>
        <div class="marketengine-radio-field">
            <span><?php _e("Who would win the dispute:", "enginethemes"); ?></span>
            <div>
                <input type="hidden" name="me-dispute-win" value="0" />
                <label class="me-radio" for="me-dispute-win-buyer">
                    <input <?php checked( $winner, $case->sender ); ?> class="me-receive-item-field" id="me-dispute-win-buyer" name="me-dispute-win" value="<?php echo $case->sender; ?>" type="radio" >
                    <span><?php echo get_the_author_meta( 'display_name', $case->sender ); ?> (<?php _e("Buyer", "enginethemes") ?>)</span>
                </label>  
            </div>
            <div>
                <label class="me-radio" for="me-dispute-win-seller">
                    <input <?php checked( $winner, $case->receiver ); ?> class="me-receive-item-field" id="me-dispute-win-seller" name="me-dispute-win" value="<?php echo $case->receiver; ?>" type="radio" >
                    <span><?php echo get_the_author_meta( 'display_name', $case->receiver ); ?> (<?php _e("Seller", "enginethemes") ?>)</span>
                </label>   
            </div>
        </div>
        <textarea cols="30" rows="10" name="arbitrate_content" placeholder="<?php _e("Your adjudication here", "enginethemes") ?>"><?php if(isset($_POST['arbitrate_content'])) { echo $_POST['arbitrate_content']; } ?></textarea>
            
        <?php wp_nonce_field( 'marketengine_arbitrate-dispute' ); ?>
        <input type="hidden" name="dispute" value="<?php echo $case->ID; ?>">

        <input type="submit" class="me-arbitrate-btn" value="<?php _e("ARBITRATE", "enginethemes"); ?>">
    </form>
    
<?php else : ?>

    <?php if($escalated_user == get_current_user_id()) : ?>

        <p><?php _e("You have escalated the dispute. The final result of the dispute is the adjudication of the Admin. Argument or proofs are very helpful for the next step.", "enginethemes"); ?></p>

    <?php else : ?>

        <p><?php printf(__("%s has escalated the dispute. The final result of the dispute is the adjudication of the Admin. Argument or proofs are very helpful for the next step.", "enginethemes"), $escalate_user_name ); ?></p>

    <?php endif; ?>

<?php endif; ?>