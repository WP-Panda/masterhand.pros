<?php
	global $post, $ae_post_factory;
	$post_object = $ae_post_factory->get( 'ae_milestone' );
	$milestone = $post_object->convert( $post );

	$milestone_meta = ae_get_milestone_meta( $milestone );
	extract( $milestone_meta );
?>

<?php
	// Milestone list view for vistor and freelancer
?>
<li data-rel="item-list-milestone">
	<span class="icon-status-milestone">
		<i class="fa fa-circle-o"></i>
	</span>
	<div class="content-steps-milestone">
		<span><?php echo $milestone->post_title; ?></span>
	</div>
</li>