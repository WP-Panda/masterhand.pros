<?php

$editable = ! in_array( $post->post_status, array( 'active', 'finished' ) );
if ( isset( $_GET['showstats'] ) && $_GET['showstats'] ) {
	$editable = false;
}
?>
<?php if ( $editable ) : ?>

	<span class="spinner" id="colorschema-ajax-loading"></span>
	<p>
		<label>
		<input name="mailster_data[track_opens]" id="mailster_data_track_opens" value="1" type="checkbox" <?php echo ( isset( $this->post_data['track_opens'] ) ) ? ( ( $this->post_data['track_opens'] ) ? 'checked' : '' ) : ( mailster_option( 'track_opens' ) ? 'checked' : '' ); ?>> <?php esc_html_e( 'Track Opens', 'mailster' ); ?>
		</label>
	</p>
	<p>
		<label>
		<input name="mailster_data[track_clicks]" id="mailster_data_track_clicks" value="1" type="checkbox" <?php echo ( isset( $this->post_data['track_clicks'] ) ) ? ( ( $this->post_data['track_clicks'] ) ? 'checked' : '' ) : ( mailster_option( 'track_clicks' ) ? 'checked' : '' ); ?>> <?php esc_html_e( 'Track Clicks', 'mailster' ); ?>
		</label>
	</p>
	<label><?php esc_html_e( 'Colors', 'mailster' ); ?></label> <a class="savecolorschema"><?php esc_html_e( 'save this schema', 'mailster' ); ?></a>

	<?php
	$html   = $this->templateobj->get( true );
	$colors = array();
	preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
	$original_colors = array_keys( array_count_values( $hits[0] ) );
	$original_names  = array();

	foreach ( $original_colors as $i => $color ) {
		preg_match( '/' . $color . '\/\*([^*]+)\*\//', $html, $x );
		$original_names[ $i ] = isset( $x[1] ) ? $x[1] : '';
	}
	?>
	<ul class="colors<?php echo count( array_count_values( $original_names ) ) > 1 ? ' has-labels' : ''; ?>" data-original-colors='<?php echo json_encode( $original_colors ); ?>'>
	<?php

	$html = $post->post_content;

	if ( ! empty( $html ) && isset( $this->post_data['template'] ) && $this->post_data['template'] == $this->get_template() && $this->post_data['file'] == $this->get_file() ) {
		preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
		$current_colors = array_keys( array_count_values( $hits[0] ) );
	} else {
		$current_colors = $original_colors;
	}

	foreach ( $current_colors as $i => $color ) {
		$value    = strtoupper( $color );
		$colors[] = $value;

		?>
	<li class="mailster-color" id="mailster-color-<?php echo strtolower( substr( $value, 1 ) ); ?>">
	<label title="<?php echo isset( $original_names[ $i ] ) ? esc_attr( $original_names[ $i ] ) : ''; ?>"><?php echo isset( $original_names[ $i ] ) ? esc_attr( $original_names[ $i ] ) : ''; ?></label>
	<input type="text" class="form-input-tip color" name="mailster_data[newsletter_color][<?php echo substr( esc_attr( $color ), 1 ); ?>]"  value="<?php echo esc_attr( $value ); ?>" data-value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $value ); ?>">
	<a class="default-value mailster-icon" href="#" tabindex="-1"></a>
	</li>
		<?php
	}
	?>
	</ul>
	<div class="clear"></div>
	<p>
		<label><?php esc_html_e( 'Colors Schemas', 'mailster' ); ?></label>
		<?php
		$customcolors = get_option( 'mailster_colors' );
		if ( isset( $customcolors[ $this->get_template() ] ) ) :
			?>
			<a class="colorschema-delete-all"><?php esc_html_e( 'Delete all custom schemas', 'mailster' ); ?></a>
		<?php endif; ?>
	</p>
	<ul class="colorschema" title="<?php esc_attr_e( 'original', 'mailster' ); ?>">
	<?php
	$original_colors_temp = array();
	foreach ( $original_colors as $i => $color ) :
		$color                  = strtolower( $color );
		$original_colors_temp[] = $color;
		?>
		<li class="colorschema-field" title="<?php echo isset( $original_names[ $i ] ) ? $original_names[ $i ] : ''; ?>" data-hex="<?php echo esc_attr( $color ); ?>" style="background-color:<?php echo $color; ?>"></li>
	<?php endforeach; ?>
	</ul>
	<?php if ( strtolower( implode( '', $original_colors_temp ) ) != strtolower( implode( '', $current_colors ) ) ) : ?>
		<ul class="colorschema" title="<?php esc_attr_e( 'current', 'mailster' ); ?>">
			<?php foreach ( $colors as $i => $color ) : ?>
				<li class="colorschema-field" title="<?php echo isset( $original_names[ $i ] ) ? esc_attr( $original_names[ $i ] ) : ''; ?>" data-hex="<?php echo esc_attr( strtolower( $color ) ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( isset( $customcolors[ $this->get_template() ] ) ) : ?>
		<?php foreach ( $customcolors[ $this->get_template() ] as $hash => $colorschema ) : ?>
		<ul class="colorschema custom" data-hash="<?php echo esc_attr( $hash ); ?>">
			<?php foreach ( $colorschema as $i => $color ) { ?>
			<li class="colorschema-field" title="<?php echo isset( $original_names[ $i ] ) ? esc_attr( $original_names[ $i ] ) : ''; ?>" data-hex="<?php echo esc_attr( strtolower( $color ) ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
		<?php } ?>
		<li class="colorschema-delete-field"><a class="colorschema-delete">&#10005;</a></li>
		</ul>
		<?php endforeach; ?>
	<?php endif; ?>
<?php else : ?>
	<p>
	<?php echo $this->post_data['track_opens'] ? '&#10004;' : '&#10004;'; ?>
	<?php esc_html_e( 'Track Opens', 'mailster' ); ?>
	</p>
	<p>
	<?php echo $this->post_data['track_clicks'] ? '&#10004;' : '&#10004;'; ?>
	<?php esc_html_e( 'Track Clicks', 'mailster' ); ?>
	</p>
	<label><?php esc_html_e( 'Colors Schema', 'mailster' ); ?></label><br>
	<ul class="colorschema finished">
	<?php
	$colors = $this->post_data['colors'];
	foreach ( $colors as $color ) :
		?>
		<li data-hex="<?php echo esc_attr( $color ); ?>" style="background-color:<?php echo esc_attr( $color ); ?>"></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
