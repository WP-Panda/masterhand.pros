<?php
	global $post, $ae_post_factory, $current_user, $user_role;
	$role = ae_user_role();
	$post_object = $ae_post_factory->get( 'ae_milestone' );
	$milestone = $post_object->convert( $post );
	$milestone_meta = ae_get_milestone_meta( $milestone );
	extract( $milestone_meta );
?>

<?php
	// Milestone list view for vistor and freelancer
?>
<li class="item-list-milestone">
	<div class="item-list-milestone-wrapper <?php echo $milestone->class_label; ?>">
		<span class="icon-status-milestone">
			<i class="<?php echo $icon_class; ?>"></i>
		</span>
		<div class="content-steps-milestone">
			<span><?php echo $milestone->post_title; ?></span>

			<div class="txt-status-milestone">
				<a class="change-status-milestone" href="javascript:void(0);">
					<span><?php echo $milestone->status_label; ?></span>
				</a>
			</div>
		</div>
	</div>
</li>