<?php
	global $post, $ae_post_factory, $current_user, $user_role;
	$role = ae_user_role();
	$post_object = $ae_post_factory->get( 'ae_milestone' );
	$milestone = $post_object->convert( $post );
	$milestone_meta = ae_get_milestone_meta( $milestone );
	$project = get_post( $milestone->post_parent );
	$icon_class = 'fa fa-circle-o';
	extract( $milestone_meta );

	// Get project owner and bid author;
	$author_id = get_post_field( 'post_author', $project->ID );
	$bid_accepted = get_post_meta( $project->ID, 'accepted', true );
	$bid_author = get_post_field( 'post_author', $bid_accepted );
?>

<?php
	// Milestone list view for vistor and freelancer
?>
<li class="item-list-milestone">
	<div class="item-list-milestone-wrapper <?php echo $milestone->class_label; ?>">
		<span class="icon-status-milestone">
			<i class="<?php echo $icon_class ?>"></i>
		</span>
		<div class="content-steps-milestone">
			<span><?php echo $milestone->post_title; ?></span>

			<div class="txt-status-milestone">
				<a class="change-status-milestone" href="javascript:void(0);">
					<span><?php echo $milestone->status_label; ?></span>
					<?php
					if ( (current_user_can('manage_options') ||$current_user->ID == $author_id) && $milestone->post_status != 'done' ) {
						echo '<i class="fa fa-angle-down"></i>';
					} elseif( (current_user_can('manage_options') || $current_user->ID != $author_id) && $milestone->post_status != 'resolve' && $milestone->post_status != 'done' ) {
						echo '<i class="fa fa-angle-down"></i>';
					}
					?>
				</a>

				<?php if( (current_user_can('manage_options') || $current_user->ID == $author_id) && $milestone->post_status != 'done' ) { ?>
				<ul class="cat-action-milestone">
					<!-- show full action for project owner -->
					<!--Employer-->
					<?php if( $milestone->post_status == 'resolve' ) { ?>
						<li><a data-icon="fa fa-circle-o" class="reopen-milestone" data-status="opened" href=""><i class="fa fa-circle-o color-text"></i>mark <span class="color-text">Re-open</span></a></li>
						<li><a data-icon="fa fa-circle" class="close-milestone" data-status="closed" href=""><i class="fa fa-circle color-text"></i>mark <span class="color-text">Closed</span></a></li>
					<?php } else if( $milestone->post_status == 'open' || $milestone->post_status == 'reopen' ) { ?>
						<li><a data-icon="fa fa-circle" class="close-milestone" data-status="closed" href=""><i class="fa fa-circle color-text"></i>mark <span class="color-text">Closed</span></a></li>
					<?php } ?>
					<!-- show limit action for bid author -->
					<!--Freelancer-->
				</ul>
				<?php } elseif( (current_user_can('manage_options') || $current_user->ID != $author_id) && $milestone->post_status != 'resolve' && $milestone->post_status != 'done' ) { ?>
				<ul class="cat-action-milestone">
					<li><a data-icon="fa fa-adjust" class="resolve-milestone" data-status="resolved" href=""><i class="fa fa-adjust color-text"></i>mark <span class="color-text">Resolved</span></a></li>
				</ul>
				<?php } ?>
			</div>
		</div>
	</div>
</li>