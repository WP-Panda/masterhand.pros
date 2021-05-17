<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="me-party-involve">
    <h3><?php _e("Related Party", "enginethemes"); ?></h3>
    
    <?php if(current_user_can( 'manage_options' ) || $case->sender == get_current_user_id()) : ?>
        <p>
            <?php _e("Seller:", "enginethemes"); ?>
             <a href="<?php echo get_author_posts_url( $case->receiver ); ?>">
                <?php echo get_the_author_meta( 'display_name', $case->receiver ); ?>
            </a>
        </p>
    <?php endif; ?>

    <?php if(current_user_can( 'manage_options' ) || $case->receiver == get_current_user_id()) : ?>
        
        <p>
            <?php _e("Buyer:", "enginethemes"); ?>
            <a href="<?php echo get_author_posts_url( $case->sender ); ?>">
                <?php echo get_the_author_meta( 'display_name', $case->sender ); ?>
            </a>
        </p>
    
    <?php endif; ?>

</div>