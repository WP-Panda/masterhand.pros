<?php
/**
 * ТУТ ПРЯМО БЕДА Много разборок
 * 
 */
wp_reset_query();
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package    WordPress
 * @subpackage FreelanceEngine
 * @since      FreelanceEngine 1.0
 */
?>
<!-- FOOTER -->
<?php if ( get_post_type() == "faq" || is_page( 'help' ) ) { ?>
    <section class="still_help">
        <div class="container">
            <div class="still_help-item">
                <p>Still need help?</p>
                <a class="main_bl-btn" href="/contact-us/">Contact Us</a>
            </div>
        </div>
    </section>
<?php } ?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-4 col-xs-12">
				<?php if ( is_active_sidebar( 'fre-footer-1' ) ) {
					dynamic_sidebar( 'fre-footer-1' );
				} ?>
            </div>
            <div class="col-sm-4 col-xs-12">
				<?php if ( is_active_sidebar( 'fre-footer-2' ) ) {
					dynamic_sidebar( 'fre-footer-2' );
				} ?>
            </div>
            <div class="col-sm-4 col-xs-12">
				<?php if ( is_active_sidebar( 'fre-footer-3' ) ) {
					dynamic_sidebar( 'fre-footer-3' );
				} ?>
            </div>
        </div>
    </div>
</footer>
<?php /*
Queries = <?=get_num_queries()?><!-- queries in -->
<br>
Seconds = <?php timer_stop(1); ?><!-- seconds.-->
 */ ?>
<div class="copyright-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 hidden-xs">
                <p class="text-copyright"><?php echo $copyright ?? 'All rights reserved.'; ?></p>
            </div>
            <div class="col-sm-6 col-xs-12">
				<?php if ( is_active_sidebar( 'fre-footer-4' ) ) {
					dynamic_sidebar( 'fre-footer-4' );
				} ?>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER / END -->

<?php

if (/*!is_page_template( 'page-auth.php' ) && !is_page_template('page-submit-project.php') &&*/ ! is_user_logged_in() ) {
	/* ======= modal register template ======= */
	get_template_part( 'template-js/modal', 'register' );
	/* ======= modal register template / end  ======= */
	/* ======= modal register template ======= */
	get_template_part( 'template-js/modal', 'login' );
	/* ======= modal register template / end  ======= */
}

if ( is_page_template( 'page-pro.php' ) ) {
	$homeid      = get_option( 'page_on_front' );
	$tip1        = str_replace( '"', '\"', get_field( 'tip1', $homeid ) );
	$tip2        = str_replace( '"', '\"', get_field( 'tip2', $homeid ) );
	$tip3        = str_replace( '"', '\"', get_field( 'tip3', $homeid ) );
	$tip4        = str_replace( '"', '\"', get_field( 'tip4', $homeid ) );
	$tip5        = str_replace( '"', '\"', get_field( 'tip5', $homeid ) );
	$aboutrating = get_field( 'about_rating', $homeid );
	?>

    <script>
		<?php if ($tip1) { ?>
        $(`<div class="tip"><?php echo $tip1;?></div>`).appendTo($(".create_project_for_all td:first-child"));
        $('.create_project_for_all td:first-child > div:first-child').addClass('half');
		<?php } ?>
		<?php if ($tip2) { ?>
        $(`<div class="tip"><?php echo $tip2;?></div>`).appendTo($(".priority_in_list_project td:first-child"));
        $('.priority_in_list_project td:first-child > div:first-child').addClass('half');
		<?php } ?>
		<?php if ($tip3) { ?>
        $(`<div class="tip"><?php echo $tip3;?></div>`).appendTo($(".highlight_project td:first-child"));
        $('.highlight_project td:first-child > div:first-child').addClass('half');
		<?php } ?>
		<?php if ($tip4) { ?>
        $(`<div class="tip"><?php echo $tip4;?></div>`).appendTo($(".urgent_project td:first-child"));
        $('.urgent_project td:first-child > div:first-child').addClass('half');
		<?php } ?>
		<?php if ($tip5) { ?>
        $(`<div class="tip"><?php echo $tip5;?></div>`).appendTo($(".hidden_project td:first-child"));
        $('.hidden_project td:first-child > div:first-child').addClass('half');
		<?php } ?>
		<?php if ($aboutrating) { ?>
        $(`<div class="tip"><?php echo $aboutrating;?></div>`).appendTo($(".your_rating td:first-child"));
		<?php } ?>
    </script>
<?php }

if ( ( is_page_template( 'page-edit-project.php' ) ) || ( is_page_template( 'page-submit-project.php' ) ) || ( is_page_template( 'page-options-project.php' ) ) || ( is_page_template( 'page-pro-order-payment.php' ) ) ) {
	$homeid = get_option( 'page_on_front' );
	$tip1   = str_replace( '"', '\"', get_field( 'tip1', $homeid ) );
	$tip2   = str_replace( '"', '\"', get_field( 'tip2', $homeid ) );
	$tip3   = str_replace( '"', '\"', get_field( 'tip3', $homeid ) );
	$tip4   = str_replace( '"', '\"', get_field( 'tip4', $homeid ) );
	$tip5   = str_replace( '"', '\"', get_field( 'tip5', $homeid ) ); ?>

    <script>
        jQuery(function ($) {


			<?php if ($tip1) { ?>
            $('.create_project_for_all .tip').text("<?php echo $tip1;?>");
			<?php } ?>
			<?php if ($tip2) { ?>
            $('.priority_in_list_project .tip').text("<?php echo $tip2;?>");
			<?php } ?>
			<?php if ($tip3) { ?>
            $('.highlight_project .tip').text("<?php echo $tip3;?>");
			<?php } ?>
			<?php if ($tip4) { ?>
            $('.urgent_project .tip').text("<?php echo $tip4;?>");
			<?php } ?>
			<?php if ($tip5) { ?>
            $('.hidden_project .tip').text("<?php echo $tip5;?>");
			<?php } ?>
        })
    </script>
<?php }

if ( is_page_template( 'page-profile.php' ) ) {
	/* ======= modal add portfolio template ======= */
	get_template_part( 'template-js/modal', 'add-portfolio' );

	get_template_part( 'template-js/modal', 'delete-portfolio' );

	get_template_part( 'template-js/modal', 'edit-portfolio' );

	get_template_part( 'template-js/modal', 'delete-meta-history' );
	get_template_part( 'template-js/modal', 'upload-avatar' );

	get_template_part( 'template-js/modal', 'reply' );
	// get_template_part('template-js/modal', 'change-phone');
	/* ======= modal add portfolio template / end  ======= */
}
//new start

//new end

/* ======= modal change password template ======= */
get_template_part( 'template-js/modal', 'change-pass' );
/* ======= modal change password template / end  ======= */

get_template_part( 'template-js/post', 'item' );
if ( is_page_template( 'page-home.php' ) ) {
	get_template_part( 'template-js/project', 'item-old' );
	get_template_part( 'template-js/profile', 'item-old' );
} else {
	get_template_part( 'template-js/project', 'item' );
	get_template_part( 'template-js/profile', 'item' );
}
get_template_part( 'template-js/user', 'bid-item' );

get_template_part( 'template-js/portfolio', 'item' );
get_template_part( 'template-js/work-history', 'item' );
get_template_part( 'template-js/skill', 'item' );

if ( is_singular( 'project' ) ) {

	get_template_part( 'template-js/bid', 'item' );
	get_template_part( 'template-js/modal', 'review' );
	get_template_part( 'template-js/modal', 'reply' );
	get_template_part( 'template-js/modal', 'review-paycode' );
	get_template_part( 'template-js/modal', 'bid' );
	get_template_part( 'template-js/modal', 'not-bid' );
	get_template_part( 'template-js/modal', 'transfer-money' );
	get_template_part( 'template-js/modal', 'arbitrate' );
	get_template_part( 'template-js/modal', 'accept-bid-no-escrow' );
	if ( ae_get_option( 'use_escrow' ) ) {
		get_template_part( 'template-js/modal', 'accept-bid' );
	}
}

if ( is_author() ) {
	get_template_part( 'template-js/author-project', 'item' );
}
//print modal contact template
if ( is_singular( PROFILE ) || is_author() ) {
	get_template_part( 'template-js/modal', 'contact' );
	/* ======= modal invite template ======= */
	get_template_part( 'template-js/modal', 'invite' );
	get_template_part( 'template-js/modal', 'info' );
}

/* ======= modal invite template / end  ======= */
/* ======= modal forgot pass template ======= */
get_template_part( 'template-js/modal', 'forgot-pass' );


/* ======= modal view portfolio  ======= */
get_template_part( 'template-js/modal', 'view-portfolio' );
get_template_part( 'template-js/modal', 'delete-project' );
get_template_part( 'template-js/modal', 'archive-project' );
get_template_part( 'template-js/modal', 'approve-project' );
get_template_part( 'template-js/modal', 'reject-project' );
get_template_part( 'template-js/modal', 'cancel-bid' );
get_template_part( 'template-js/modal', 'remove-bid' );

get_template_part( 'template-js/modal', 'delete-file' );
get_template_part( 'template-js/modal', 'lock-file' );
get_template_part( 'template-js/modal', 'unlock-file' );

// modal edit project
if ( ( get_query_var( 'author' ) == $user_ID && is_author() ) || current_user_can( 'manage_options' ) || is_post_type_archive( PROJECT ) || is_page_template( 'page-profile.php' ) || is_singular( PROJECT ) ) {
	get_template_part( 'template-js/modal', 'edit-project' ); ////////NEED CHECK ON USING////////////////
	get_template_part( 'template-js/modal', 'reject' );
}

if ( is_singular( PROJECT ) ) {
	get_template_part( 'template-js/message', 'item' );
	get_template_part( 'template-js/report', 'item' );
}
if ( is_page_template( 'page-list-testimonial.php' ) ) {
	get_template_part( 'template-js/testimonial', 'item' );
}
get_template_part( 'template-js/notification', 'template' );

get_template_part( 'template-js/freelancer-current-project-item' );
get_template_part( 'template-js/freelancer-previous-project-item' );
get_template_part( 'template-js/employer-current-project-item' );
get_template_part( 'template-js/employer-previous-project-item' );

wp_footer();
?>

<script type="text/template" id="ae_carousel_template">
    <li class="image-item" id="{{= attach_id }}">
        <div class="attached-name"><p>{{= name }}</p></div>
        <div class="attached-size">{{= size }}</div>
        <div class="attached-remove"><span class=" delete-img delete"><i class="fa fa-times"></i></span></div>
    </li>
</script>
<!-- MODAL QUIT PROJECT-->
<div class="modal fade" id="quit_project" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
				<?php _e( "Discontinue project", ET_DOMAIN ) ?>
            </div>
            <div class="modal-body">
                <form role="form" id="quit_project_form" class="quit_project_form fre-modal-form">
                    <p class="notify-form">
						<?php _e( "This project will be marked as disputed and your case will have resulted soon by admin. Please provide as many as proofs and statement explaining why you quit the project.", ET_DOMAIN ); ?>
                    </p>
                    <p class="notify-form">
						<?php _e( "Workspace is still available for you to access in case of necessary.", ET_DOMAIN ); ?>
                    </p>
                    <input type="hidden" id="project-id" value="">
                    <div class="fre-input-field">
                        <label class="fre-field-title"
                               for="comment-content"><?php _e( 'Provide us the reason why you quit:', ET_DOMAIN ) ?></label>
                        <textarea id="comment-content" name="comment_content"></textarea>
                    </div>
                    <div class="fre-form-btn">
                        <button type="submit" class="btn-left fre-normal-btn fre-submit-btn btn-submit">
							<?php _e( 'Discontinue', ET_DOMAIN ) ?>
                        </button>
                        <span class="fre-form-close fre-cancel-btn "
                              data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ); ?></span>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!--// MODAL QUIT PROJECT-->


<!-- MODAL CLOSE PROJECT-->
<div class="modal fade" id="close_project_success" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="content-close-wrapper">
                    <p class="alert-close-text">
						<?php _e( "We will review the reports from both freelancer and employer to give the best decision. It will take 3-5 business days for reviewing after receiving two reports.", ET_DOMAIN ) ?>
                    </p>
                    <button type="submit" class="fre-submit-btn btn btn-ok">
						<?php _e( 'OK', ET_DOMAIN ) ?>
                    </button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!--// MODAL CLOSE PROJECT-->
</body>
</html>