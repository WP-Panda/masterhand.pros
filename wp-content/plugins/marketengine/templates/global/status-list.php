<?php
	$status_list = marketengine_get_order_status_list();
	if( !empty($status_list) ) :
		if( $type == 'order' ) {
			unset($status_list['me-pending']);
		}
			unset($status_list['publish']);
?>
	<select name="order_status" id="">
		<option value="any"><?php _e("Filter order's status", 'enginethemes'); ?></option>
	<?php
		foreach ($status_list as $key => $status) :
			$curr_status = isset($_GET['order_status']) ? esc_attr( $_GET['order_status'] ) : '';
	?>
		<option value="<?php echo $key; ?>" <?php echo ($curr_status === $key) ? 'selected' : '' ?>><?php echo $status; ?></option>
	<?php
		endforeach;
	?>
	</select>
<?php
	endif;
?>