<div class="me-notification">
	<?php
	$me_notices = marketengine_get_notices('error');
	foreach ($me_notices as $key => $notice):
	    ?>
			<span class="me-alert-error" role="alert">- <?php echo $notice ?></span>
		<?php
	endforeach;
	?>
</div>