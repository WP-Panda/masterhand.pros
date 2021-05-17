<?php
/**
 * Template Name: Page Contact Us
 */
global $user_ID;
if (fre_share_role() || ae_user_role($user_ID) == FREELANCER) {
    $role = 'professional';
} else if (fre_share_role() || ae_user_role($user_ID) == EMPLOYER) {
    $role = 'client';
} 
$username = get_the_author_meta('display_name', $user_ID);
get_header();
?>
<div class="notification success-bg <?php if (is_user_logged_in()) { echo 'having-adminbar';}?>" style="display:none;">
    <div class="main-center">Your message has been sent</div>
</div>
<?php if (is_user_logged_in()) { ?>
<div class="fre-page-wrapper page-contact-us">
    <div class="hidden user-role"><?php echo $role;?></div>
    <div class="hidden user-name"><?php echo $username;?></div>

    <div class="fre-page-section">
        <div class="container">
            <div class="fre-profile-box">           
                <h1 class="text-center"><?php _e('Contact us', ET_DOMAIN) ?></h1>
                <?php echo do_shortcode('[contact-form-7 id="7742"]');?>
            </div>        
        </div>
    </div>
</div>
<?php } else {
    header("Location: /login");
    exit();
}?>

<script>

	(function ($) {    
 document.addEventListener( 'wpcf7mailsent', function( event ) {
        $('.screen-reader-response').css('display','block');
        $('.notification').fadeIn();
        setTimeout(function () {
            $('.notification').fadeOut();
        }, 3500);
    }, false );
})(jQuery);

</script>    
<?php get_footer(); ?>