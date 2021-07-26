<?php
global $wp_query, $ae_post_factory, $post, $current_user, $wpdb, $user_ID;
$post_object = $ae_post_factory->get( PROJECT );
$author_id   = get_query_var( 'author' );
if ( ! $author_id ) {
	return;
}

$sql     = "select ID from  $wpdb->posts as P
						join $wpdb->comments as C
							on P.ID=C.comment_post_ID
						join $wpdb->commentmeta as M
							on M.comment_id=C.comment_ID
					where post_status = 'publish'
							AND M.meta_key ='invite'
							AND P.post_author = $user_ID
							AND C.comment_approved = 1
							AND M.meta_value = $author_id
					";
$results = $wpdb->get_col( $sql );
query_posts( array(
	'post_status'  => 'publish',
	'showposts'    => - 1,
	'post_type'    => 'project',
	'author'       => $current_user->ID,
	'post__not_in' => $results
) );

?>
    <div class="modal fade designed-modal <?php if ( ! have_posts() ) {
		echo 'modal-invite-no-project';
	} ?>" id="modal_invite">
        <div class="modal-dialog <?php if ( ! have_posts() ) {
			echo 'modal-sm';
		} ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
                    <div class="modal_t-big"><?php if ( have_posts() ) {
							printf( __( 'Select project(s) to invite %s to join', ET_DOMAIN ), get_the_author_meta( 'display_name', $author_id ) );
						} else {
							_e( 'Invite to bid', ET_DOMAIN );
						} ?>
                    </div>
                </div>

				<?php if ( have_posts() ) { ?>
                    <form role="form" class="fre-modal-form text-center" id="submit_invite">
                        <div class="invites-list">
							<?php
							while ( have_posts() ) {
								the_post();
								$budget = get_post_meta( $post->ID, 'et_budget', true );
								?>
                                <div class="name-project">
                                    <div class="checkline">
                                        <input id="<?php echo 'project-' . $post->ID; ?>" type="checkbox"
                                               name="project_invites[]" value="<?php echo $post->ID; ?>">
                                        <span><?php the_title(); ?></span>
                                    </div>
                                    <span class="project-price"><?php echo fre_price_format( $budget ); ?></span>
                                </div>
							<?php } ?>
                        </div>
                        <div class="all-selected">
                            <a class="select-all" href="#"><?php _e( 'Select all', ET_DOMAIN ) ?></a>
                            <a class="remove-all" href="#"
                               style="display:none;"><?php _e( 'Remove all', ET_DOMAIN ) ?></a>
                        </div>

                        <div class="fre-form-btn">
                            <button type="submit"
                                    class="fre-submit-btn btn-left btn-submit" <?php if ( $wp_query->found_posts == 0 ) {
								echo 'disabled="disabled"';
							} ?> >
								<?php _e( 'Invite', ET_DOMAIN ) ?>
                            </button>
                            <span class="fre-cancel-btn" data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                        </div>
                    </form>
				<?php } else { ?>
                    <form role="form" class="fre-modal-form">
                        <div class="invite-no-project text-center">
                            <div class="modal_t"><?php _e( 'No projects available to invite!', ET_DOMAIN ); ?></div>
                            <p><?php _e( 'There are currently no projects available to invite this user.', ET_DOMAIN ); ?></p>
                            <p><?php _e( 'Please post a project to invite this Professional.', ET_DOMAIN ); ?></p>
                            <div class="fre-form-btn">
								<?php echo '<a class="fre-submit-btn btn-left" href="' . et_get_page_link( 'submit-project' ) . '" >' . __( "Post a Project", ET_DOMAIN ) . '</a>'; ?>
                                <span class="fre-cancel-btn"
                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                            </div>
                        </div>
                    </form>
				<?php } ?>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php wp_reset_query(); ?>