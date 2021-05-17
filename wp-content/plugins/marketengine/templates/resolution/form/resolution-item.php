<?php foreach ($resolutions as $key => $resolution) : ?>

<div class="marketengine-radio-field dispute-get-refund">
	<label class="me-radio" for="<?php echo $key; ?>">
    	<input id="<?php echo $key; ?>" name="expect_solution" type="radio" value="<?php echo $key; ?>"<?php checked(isset($_POST['expect_solution']) && $_POST['expect_solution'] == $key); ?>>
    	<span><?php echo $resolution['label']; ?></span>
	</label>
	<span><?php echo $resolution['description']; ?></span>
</div>

<?php endforeach; ?>