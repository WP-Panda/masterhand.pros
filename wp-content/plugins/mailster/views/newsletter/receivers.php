<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );

if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}

$ignore_lists = isset( $this->post_data['ignore_lists'] ) ? ! ! $this->post_data['ignore_lists'] : false;

?>
<?php if ( $editable ) : ?>
<div>
	<div id="receivers-dialog" style="display:none;">
		<div class="mailster-conditions-thickbox">
			<div class="inner">
				<?php mailster( 'conditions' )->view( $this->post_data['list_conditions'] ); ?>
			</div>
			<div class="foot">
				<div class="alignleft"><?php esc_html_e( 'Total receivers', 'mailster' ); ?>: <span class="mailster-total">&ndash;</span></div>
				<div class="alignright">
				<button class="button button-primary close-conditions"><?php esc_html_e( 'Close', 'mailster' ); ?></button>
				<span class="spinner" id="conditions-ajax-loading"></span>
				</div>
			</div>
		</div>
	</div>

	<div>
		<div class="lists">

			<?php $checked = wp_parse_args( isset( $_GET['lists'] ) ? $_GET['lists'] : array(), $this->post_data['lists'] ); ?>

			<div id="list-checkboxes"<?php echo $ignore_lists ? ' style="display:none"' : ''; ?>>
				<?php mailster( 'lists' )->print_it( null, null, 'mailster_data[lists]', true, $checked ); ?>
				<label><input type="checkbox" id="all_lists"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label>
			</div>
			<ul>
				<li><label><input id="ignore_lists" type="checkbox" name="mailster_data[ignore_lists]" value="1" <?php checked( $ignore_lists ); ?>> <?php esc_html_e( 'List doesn\'t matter', 'mailster' ); ?> </label></li>
			</ul>

		</div>
		<div><strong><?php esc_html_e( 'Conditions', 'mailster' ); ?>:</strong>
			<div id="mailster_conditions_render">
			<?php mailster( 'conditions' )->render( $this->post_data['list_conditions'] ); ?>
			</div>
		</div>
	</div>
	<p>
		<button class="button edit-conditions"><?php esc_html_e( 'Edit Conditions', 'mailster' ); ?></button> <?php esc_html_e( 'or', 'mailster' ); ?> <a class="remove-conditions" href="#"><?php esc_html_e( 'remove all', 'mailster' ); ?></a>
	</p>

</div>
<p class="totals"><?php esc_html_e( 'Total receivers', 'mailster' ); ?>: <span class="mailster-total">&ndash;</span></p>
	<?php else : ?>
	<p>
		<?php
		if ( $ignore_lists ) :

			esc_html_e( 'Any List', 'mailster' );

		else :
			$list  = array();
			$lists = mailster( 'lists' )->get();

			if ( ! empty( $this->post_data['lists'] ) ) {
				esc_html_e( 'Lists', 'mailster' );
				foreach ( $lists as $i => $list ) {
					if ( in_array( $list->ID, $this->post_data['lists'] ) ) {
						if ( $i ) {
							echo ', ';
						}
						echo ' <strong><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
					}
				}
			} else {
				esc_html_e( 'no lists selected', 'mailster' );
			}

		endif;
		?>
	</p>
		<?php if ( isset( $this->post_data['list_conditions'] ) ) : ?>
		<p><strong><?php esc_html_e( 'only if', 'mailster' ); ?>:</strong>
			<?php mailster( 'conditions' )->render( $this->post_data['list_conditions'] ); ?>
		</p>
		<?php endif; ?>
	<?php endif; ?>



<?php if ( ! $editable && 'autoresponder' != $post->post_status && current_user_can( 'mailster_edit_lists' ) ) : ?>

	<a class="create-new-list button" href="#"><?php esc_html_e( 'create new list', 'mailster' ); ?></a>
	<div class="create-new-list-wrap">
		<h4><?php esc_html_e( 'create a new list with all', 'mailster' ); ?></h4>
		<p>
		<select class="create-list-type">
		<?php
		$options = array(
			'sent'           => esc_html__( 'who have received', 'mailster' ),
			'not_sent'       => esc_html__( 'who have not received', 'mailster' ),
			'open'           => esc_html__( 'who have opened', 'mailster' ),
			'open_not_click' => esc_html__( 'who have opened but not clicked', 'mailster' ),
			'click'          => esc_html__( 'who have opened and clicked', 'mailster' ),
			'not_open'       => esc_html__( 'who have not opened', 'mailster' ),
		);
		foreach ( $options as $id => $option ) {
			?>
			<option value="<?php echo $id; ?>"><?php echo $option; ?></option>
		<?php } ?>
		</select>
		</p>
		<p>
			<a class="create-list button"><?php esc_html_e( 'create list', 'mailster' ); ?></a>
		</p>
		<p class="totals">
			<?php esc_html_e( 'Total receivers', 'mailster' ); ?>: <span class="mailster-total">-</span>
		</p>
	</div>
<?php endif; ?>
