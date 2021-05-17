<?php
	global $post, $wp_post_statuses;

	array_unshift(
		$conditions,
		array(
			array(
				'field'    => '',
				'operator' => '',
				'value'    => '',
			),
		)
	);

	$forms               = mailster( 'forms' )->get_all();
	$lists               = mailster( 'lists' )->get();
	$all_campaigns       = mailster( 'campaigns' )->get_campaigns(
		array(
			'post__not_in' => $post ? array( $post->ID ) : null,
			'orderby'      => 'post_title',
			'order'        => 'ASC',
		)
	);
	$all_campaigns_stati = wp_list_pluck( $all_campaigns, 'post_status' );
	asort( $all_campaigns_stati );
	$statuses = mailster( 'subscribers' )->get_status( null, true );
	if ( $geo = mailster_option( 'track_location' ) ) {
		$countries  = mailster( 'geo' )->get_countries( true );
		$continents = mailster( 'geo' )->get_continents( true );
	}

	?>
<div class="mailster-conditions">
	<div class="mailster-condition-container"></div>
	<div class="mailster-conditions-wrap" data-emptytext="<?php esc_attr_e( 'Please add your first condition.', 'mailster' ); ?>">
	<?php foreach ( $conditions as $i => $condition_group ) : ?>
	<div class="mailster-condition-group" data-id="<?php echo $i; ?>" data-operator="<?php esc_attr_e( 'and', 'mailster' ); ?>"<?php echo ( ! $i ) ? ' style="display:none"' : ''; ?>>
			<a class="add-or-condition button button-small"><?php esc_html_e( 'Add Condition', 'mailster' ); ?> [<span><?php esc_html_e( 'or', 'mailster' ); ?></span>]</a>
			<?php
			foreach ( $condition_group as $j => $condition ) :
				$value          = $condition['value'];
				$field          = $condition['field'];
				$field_operator = $this->get_field_operator( $condition['operator'] );
				?>
		<div class="mailster-condition" data-id="<?php echo $j; ?>" data-operator="<?php esc_attr_e( 'or', 'mailster' ); ?>">
			<a class="remove-condition" title="<?php esc_attr_e( 'remove condition', 'mailster' ); ?>">&#10005;</a>
			<div class="mailster-conditions-field-fields">
				<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][field]" class="condition-field" disabled>

					<optgroup label="<?php esc_attr_e( 'Fields', 'mailster' ); ?>">
					<?php
					foreach ( $this->fields as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					}
					?>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'User related', 'mailster' ); ?>">
					<?php
					foreach ( $this->custom_fields as $key => $customfield ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $customfield['name'] . '</option>';
					}
					?>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'Campaign related', 'mailster' ); ?>">
					<?php
					foreach ( $this->campaign_related as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					}
					?>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'List related', 'mailster' ); ?>">
					<?php
					foreach ( $this->list_related as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					}
					?>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'Meta Data', 'mailster' ); ?>">
					<?php
					foreach ( $this->meta_fields as $key => $name ) {
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					}
					?>
					</optgroup>
					<optgroup label="<?php esc_attr_e( 'WordPress User Meta', 'mailster' ); ?>">
					<?php
					foreach ( $this->wp_user_meta as $key => $name ) {
						if ( is_integer( $key ) ) {
							$key = $name;
						}
						echo '<option value="' . $key . '"' . selected( $condition['field'], $key, false ) . '>' . $name . '</option>';
					}
					?>
					</optgroup>
					</select>
				</div>

				<div class="mailster-conditions-operator-fields">
					<div class="mailster-conditions-operator-field mailster-conditions-operator-field-default">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled>
						<?php
						foreach ( $this->operators as $key => $name ) :
							echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
									endforeach;
						?>
						</select>
					</div>
					<div class="mailster-conditions-operator-field" data-fields=",rating,">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled>
						<?php
						foreach ( $this->simple_operators as $key => $name ) :
							echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
									endforeach;
						?>
						</select>
					</div>
					<div class="mailster-conditions-operator-field" data-fields=",lang,client,referer,firstname,lastname,email,">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled>
						<?php
						foreach ( $this->string_operators as $key => $name ) :
							echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
									endforeach;
						?>
						</select>
					</div>
					<div class="mailster-conditions-operator-field" data-fields=",wp_capabilities,status,form,clienttype,geo,">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled>
						<?php
						foreach ( $this->bool_operators as $key => $name ) :
							echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
									endforeach;
						?>
						</select>
					</div>
					<div class="mailster-conditions-operator-field" data-fields=",<?php echo implode( ',', $this->time_fields ); ?>,">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled>
						<?php
						foreach ( $this->date_operators as $key => $name ) :
							echo '<option value="' . $key . '"' . selected( $field_operator, $key, false ) . '>' . $name . '</option>';
									endforeach;
						?>
						</select>
					</div>
					<div class="mailster-conditions-operator-field" data-fields=",_sent,_sent__not_in,_open,_open__not_in,_click,_click__not_in,_click_link,_click_link__not_in,_lists__not_in,_lists__in,">
						<input type="hidden" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][operator]" class="condition-operator" disabled value="is">
					</div>
				</div>

				<div class="mailster-conditions-value-fields">
					<?php
					if ( is_array( $value ) ) {
						$value_arr = $value;
						$value     = $value[0];
					} else {
						$value_arr = array( $value );
					}
					?>
					<div class="mailster-conditions-value-field mailster-conditions-value-field-default">
					<input type="text" class="regular-text condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]">
				</div>
				<div class="mailster-conditions-value-field" data-fields=",rating,">
					<?php
							$stars = ( round( $this->sanitize_rating( $value ) / 10, 2 ) * 50 );
							$full  = max( 0, min( 5, floor( $stars ) ) );
							$half  = max( 0, min( 5, round( $stars - $full ) ) );
							$empty = max( 0, min( 5, 5 - $full - $half ) );
					?>
					<div class="mailster-rating">
					<?php
							echo str_repeat( '<span class="mailster-icon enabled"></span>', $full )
							. str_repeat( '<span class="mailster-icon enabled"></span>', $half )
							. str_repeat( '<span class="mailster-icon"></span>', $empty )
					?>
					</div>
					<input type="hidden" class="condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]">
					</div>
					<div class="mailster-conditions-value-field" data-fields=",<?php echo implode( ',', $this->time_fields ); ?>,">
					<input type="text" class="regular-text datepicker condition-value" disabled autocomplete="off" value="<?php echo esc_attr( $value ); ?>" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]">
					</div>
					<div class="mailster-conditions-value-field" data-fields=",id,wp_id,">
					<input type="text" class="regular-text condition-value" disabled value="<?php echo esc_attr( $value ); ?>" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]">
					</div>
					<div class="mailster-conditions-value-field" data-fields=",wp_capabilities,">
					<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]" class="condition-value" disabled>
						<?php echo wp_dropdown_roles( $value ); ?>
					</select>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",status,">
					<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]" class="condition-value" disabled>
						<?php foreach ( $statuses as $key => $name ) : ?>
							<option value="<?php echo (int) $key; ?>" <?php selected( $key, $value ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",form,">
					<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]" class="condition-value" disabled>
						<?php
						if ( ! empty( $forms ) ) :
							foreach ( $forms as $form ) :
								echo '<option value="' . $form->ID . '"' . selected( $form->ID, $value, false ) . '>#' . $form->ID . ' ' . $form->name . '</option>';
							endforeach;
							else :
								echo '<option value="0">' . esc_html__( 'No Form found', 'mailster' ) . '</option>';
						endif;
							?>
					</select>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",clienttype,">
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]" class="condition-value" disabled>
							<option value="desktop"<?php selected( $value, 'desktop' ); ?>><?php esc_html_e( 'Desktop', 'mailster' ); ?></option>
							<option value="webmail"<?php selected( $value, 'webmail' ); ?>><?php esc_html_e( 'Webmail', 'mailster' ); ?></option>
							<option value="mobile"<?php selected( $value, 'mobile' ); ?>><?php esc_html_e( 'Mobile', 'mailster' ); ?></option>
						</select>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",_sent,_sent__not_in,_open,_open__not_in,_click,_click__not_in,">
					<?php if ( $all_campaigns ) : ?>
						<?php foreach ( $value_arr as $k => $v ) : ?>
						<div class="mailster-conditions-value-field-multiselect">
							<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value][]" class="condition-value" disabled>
								<option value="0">--</option>
								<optgroup label="<?php esc_attr_e( 'Aggregate Campaigns', 'mailster' ); ?>">
								<?php
								foreach ( $this->special_campaigns as $key => $name ) :
									echo '<option value="' . $key . '"' . selected( $v, $key, false ) . '>' . $name . '</option>';
								endforeach;
								?>
								</optgroup>
								<?php
								$status = '';
								foreach ( $all_campaigns_stati as $cj => $c ) :
									$c = $all_campaigns[ $cj ];
									if ( $status != $c->post_status ) :
										if ( $status ) {
											echo '</optgroup>';
										}
										echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
										$status = $c->post_status;
									endif;
									?>
								<option value="<?php echo $c->ID; ?>" <?php selected( $v, $c->ID ); ?>><?php echo ( $c->post_title ? esc_html( $c->post_title ) : '[' . esc_html__( 'no title', 'mailster' ) . ']' ) . ' (# ' . $c->ID . ')'; ?></option>
								<?php endforeach; ?>
								</optgroup>
							</select>
						<a class="mailster-condition-remove-multiselect" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
						<a class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></a>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No campaigns available', 'mailster' ); ?><input type="hidden" class="condition-value" disabled value="0" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]"></p>
				<?php endif; ?>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",_lists__not_in,_lists__in,">
					<?php if ( $lists ) : ?>
						<?php foreach ( $value_arr as $k => $v ) : ?>
						<div class="mailster-conditions-value-field-multiselect">
							<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value][]" class="condition-value" disabled>
								<option value="0">--</option>
								<?php
								$status = '';
								foreach ( $lists as $lj => $list ) :
									?>
								<option value="<?php echo $list->ID; ?>" <?php selected( $v, $list->ID ); ?>><?php echo ( $list->name ? esc_html( $list->name ) : '[' . esc_html__( 'no title', 'mailster' ) . ']' ); ?></option>
								<?php endforeach; ?>
							</select>
						<a class="mailster-condition-remove-multiselect" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
						<a class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></a>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php esc_html_e( 'No campaigns available', 'mailster' ); ?><input type="hidden" class="condition-value" disabled value="0" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]"></p>
				<?php endif; ?>
					</div>
					<div class="mailster-conditions-value-field" data-fields=",_click_link,_click_link__not_in,">
					<div>
					<?php foreach ( $value_arr as $k => $v ) : ?>
						<?php
						if ( is_numeric( $v ) || in_array( $v, array_keys( $this->special_campaigns ) ) ) {
							continue;
						}
						?>
					<div class="mailster-conditions-value-field-multiselect">
						<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<input type="text" class="regular-text condition-value" disabled value="<?php echo esc_attr( $v ); ?>" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value][]" placeholder="https://example.com">
						<a class="mailster-condition-remove-multiselect" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
						<a class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></a>
					</div>
					<?php endforeach; ?>
					</div>
					<?php if ( $all_campaigns ) : ?>
					<span><?php esc_html_e( 'in', 'mailster' ); ?> </span>
					<div>
						<?php foreach ( $value_arr as $k => $v ) : ?>
							<?php
							if ( ! is_numeric( $v ) && ! in_array( $v, array_keys( $this->special_campaigns ) ) && $v != '' ) {
								continue; }
							?>
						<div class="mailster-conditions-value-field-multiselect">
							<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
							<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value][]" class="condition-value" disabled>
								<option value="0"><?php esc_html_e( 'Any Campaign', 'mailster' ); ?></option>
								<optgroup label="<?php esc_attr_e( 'Aggregate Campaigns', 'mailster' ); ?>">
									<?php
									foreach ( $this->special_campaigns as $key => $name ) :
										echo '<option value="' . $key . '"' . selected( $v, $key, false ) . '>' . $name . '</option>';
									endforeach;
									?>
								</optgroup>
								<?php
								$status = '';
								foreach ( $all_campaigns_stati as $cj => $c ) :
									$c = $all_campaigns[ $cj ];
									if ( $status != $c->post_status ) :
										if ( $status ) {
											echo '</optgroup>';
										}
										echo '<optgroup label="' . $wp_post_statuses[ $c->post_status ]->label . '">';
										$status = $c->post_status;
									endif;
									?>
									<option value="<?php echo $c->ID; ?>" <?php selected( $v, $c->ID ); ?>><?php echo ( $c->post_title ? esc_html( $c->post_title ) : '[' . esc_html__( 'no title', 'mailster' ) . ']' ) . ' (# ' . $c->ID . ')'; ?></option>
								<?php endforeach; ?>
								</optgroup>
							</select>
						<a class="mailster-condition-remove-multiselect" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
						<a class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></a>
						</div>
					<?php endforeach; ?>
					</div>
					<?php else : ?>
						<p><?php esc_html_e( 'No campaigns available', 'mailster' ); ?><input type="hidden" class="condition-value" disabled value="0" name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value]"></p>
					<?php endif; ?>
					</div>
					<?php if ( $geo ) : ?>
					<div class="mailster-conditions-value-field" data-fields=",geo,">
						<?php foreach ( $value_arr as $k => $v ) : ?>
						<div class="mailster-conditions-value-field-multiselect">
						<span><?php esc_html_e( 'or', 'mailster' ); ?> </span>
						<select name="<?php echo $inputname; ?>[<?php echo $i; ?>][<?php echo $j; ?>][value][]" class="condition-value" disabled>
							<option value="0">--</option>
							<optgroup label="<?php esc_attr_e( 'Continents', 'mailster' ); ?>">
							<?php foreach ( $continents as $code => $continent ) : ?>
								<option value="<?php echo $code; ?>" <?php selected( $v, $code ); ?>><?php echo $continent; ?></option>
							<?php endforeach; ?>
							</optgroup>
							<?php foreach ( $countries as $continent => $sub_countries ) : ?>
							<optgroup label="<?php echo $continent; ?>">
								<?php foreach ( $sub_countries as $code => $country ) : ?>
								<option value="<?php echo $code; ?>" <?php selected( $v, $code ); ?>><?php echo $country; ?></option>
								<?php endforeach; ?>
							</optgroup>
							<?php endforeach; ?>
						</select>
					<a class="mailster-condition-remove-multiselect" title="<?php esc_attr_e( 'remove', 'mailster' ); ?>">&#10005;</a>
					<a class="button button-small mailster-condition-add-multiselect"><?php esc_html_e( 'or', 'mailster' ); ?></a>
					</div>
				<?php endforeach; ?>
					</div>
				<?php endif; ?>
				</div>
				<div class="clear"></div>
			</div><?php endforeach; ?>
		</div><?php endforeach; ?>
	</div>
		<a class="button add-condition"><?php esc_html_e( 'Add Condition', 'mailster' ); ?></a>

	<div class="mailster-condition-empty">
	</div>
</div>
