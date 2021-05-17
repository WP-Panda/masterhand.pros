<?php

$currentpage = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'import';
$currentstep = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;

?>
<div class="wrap mailster-manage">
<?php if ( 'import' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Import Subscribers', 'mailster' ); ?></h1>
<?php elseif ( 'export' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Export Subscribers', 'mailster' ); ?></h1>
<?php elseif ( 'delete' == $currentpage ) : ?>
<h1><?php esc_html_e( 'Delete Subscribers', 'mailster' ); ?></h1>
<?php else : ?>
<h1><?php esc_html_e( 'Manage Subscribers', 'mailster' ); ?></h1>
<?php endif; ?>

<h2 class="nav-tab-wrapper">

	<?php if ( current_user_can( 'mailster_import_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'import' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=import"><?php esc_html_e( 'Import', 'mailster' ); ?></a>
	<?php endif; ?>

	<?php if ( current_user_can( 'mailster_export_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'export' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=export"><?php esc_html_e( 'Export', 'mailster' ); ?></a>
	<?php endif; ?>

	<?php if ( current_user_can( 'mailster_bulk_delete_subscribers' ) ) : ?>
	<a class="nav-tab <?php echo ( 'delete' == $currentpage ) ? 'nav-tab-active' : ''; ?>" href="edit.php?post_type=newsletter&page=mailster_manage_subscribers&tab=delete"><?php esc_html_e( 'Delete', 'mailster' ); ?></a>
	<?php endif; ?>

</h2>
<div class="stuffbox">
<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>

<?php if ( 'import' == $currentpage && current_user_can( 'mailster_import_subscribers' ) ) : ?>

	<div class="step1">
		<div class="step1-body">
			<div class="upload-method">
				<h2><?php esc_html_e( 'Upload', 'mailster' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Upload you subscribers as comma-separated list (CSV)', 'mailster' ); ?></p>
				<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin-ajax.php?action=mailster_import_subscribers_upload_handler' ); ?>">

				<?php mailster( 'manage' )->media_upload_form(); ?>

				</form>
				<br>
			</div>
			<div class="upload-method-or">
				<?php esc_html_e( 'or', 'mailster' ); ?>
			</div>
			<div class="upload-method">
				<h2><?php esc_html_e( 'Paste', 'mailster' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Copy and paste from your spreadsheet app', 'mailster' ); ?></p>
				<textarea id="paste-import" class="widefat" rows="13" placeholder="<?php esc_attr_e( 'paste your list here', 'mailster' ); ?>">
justin.case@<?php echo $_SERVER['HTTP_HOST']; ?>; Justin; Case; Custom;
john.doe@<?php echo $_SERVER['HTTP_HOST']; ?>; John; Doe
jane.roe@<?php echo $_SERVER['HTTP_HOST']; ?>; Jane; Roe
				</textarea>
			</div>

		</div>
		<div class="clear"></div>
		<h2 class="import-status">&nbsp;</h2>
	</div>

	<div class="step2">
		<h2 class="import-status"></h2>
		<div class="step2-body"></div>
	</div>

	<?php if ( current_user_can( 'mailster_import_wordpress_users' ) ) : ?>

	<div id="wordpress-users">
		<h2><?php esc_html_e( 'WordPress Users', 'mailster' ); ?></h2>
		<form id="import_wordpress" method="post">
			<?php

			global $wp_roles;
			$roles = $wp_roles->get_names();

			if ( ! empty( $roles ) ) :
				?>
			<div id="wordpress-user-roles">
				<h4><?php esc_html_e( 'Import WordPress users with following roles', 'mailster' ); ?></h4>
				<p><label><input type="checkbox" class="wordpress-users-toggle" checked> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
				<ul>
				<?php
				$i = 0;
				foreach ( $roles as $role => $name ) {
					if ( ! ( $i % 8 ) && $i ) {
						echo '</ul><ul>';
					}
					?>
					<li><label><input type="checkbox" name="roles[]" value="<?php echo $role; ?>" checked> <?php echo $name; ?></label></li>
					<?php
					$i++;
				}
				?>
				</ul>
				<ul>
					<li><label><input type="checkbox" name="no_role" value="1" checked> <?php esc_html_e( 'users without a role', 'mailster' ); ?></label></li>
				</ul>
			</div>
			<div id="wordpress-user-meta">
				<?php $meta_values = mailster( 'helper' )->get_wpuser_meta_fields(); ?>
				<h4><?php esc_html_e( 'Use following meta values', 'mailster' ); ?></h4>
				<p><label><input type="checkbox" class="wordpress-users-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></p>
				<ul>
				<?php
				foreach ( $meta_values as $i => $meta_value ) {
					if ( ! ( $i % 8 ) && $i ) {
						echo '</ul><ul>';
					}
					?>
					<li><label><input type="checkbox" name="meta_values[]" value="<?php echo esc_attr( $meta_value ); ?>"> <?php echo esc_html( $meta_value ); ?></label></li>
					<?php
				}
				?>
				</ul>
			</div>
			<?php endif; ?>
			<div class="clearfix clear">
				<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Next Step', 'mailster' ); ?> &#x2192;">
			</div>
		</form>
	</div>

	<?php endif; ?>


	<?php do_action( 'mailster_import_tab' ); ?>


<?php elseif ( 'export' == $currentpage && current_user_can( 'mailster_export_subscribers' ) ) : ?>

			<?php

			$lists   = mailster( 'lists' )->get( null, false );
			$no_list = mailster( 'lists' )->count( false );

			$user_settings = get_user_option( 'mailster_export_settings' );
			$user_settings = wp_parse_args(
				$user_settings,
				array(
					'lists'        => wp_list_pluck( $lists, 'ID' ),
					'nolists'      => true,
					'status'       => array( 0, 1, 2, 3, 4 ),
					'conditions'   => array(),
					'header'       => false,
					'dateformat'   => 0,
					'outputformat' => 'xls',
					'separator'    => ';',
					'encoding'     => 'UTF-8',
					'performance'  => 1000,
					'column'       => array( 'ID', 'email', 'firstname', 'lastname' ),
				)
			);

			if ( isset( $_GET['conditions'] ) ) {
				$user_settings['conditions'] = (array) $_GET['conditions'];
			}
			if ( isset( $_GET['lists'] ) ) {
				$user_settings['lists'] = (array) $_GET['lists'];
			}

			if ( ! empty( $lists ) || $no_list ) :
				?>

		<div class="step1">
			<form method="post" id="export-subscribers">
				<?php wp_nonce_field( 'mailster_nonce' ); ?>
			<h3><?php esc_html_e( 'Lists', 'mailster' ); ?>:</h3>
				<?php if ( ! empty( $lists ) ) : ?>
			<ul>
			<li><label><input type="checkbox" class="list-toggle" checked> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></li>
			<li>&nbsp;</li>
			<input type="hidden" name="lists[]" value="-1">
					<?php mailster( 'lists' )->print_it( null, false, 'lists', esc_html__( 'total', 'mailster' ), $user_settings['lists'] ); ?>
			</ul>
			<?php endif; ?>

				<?php if ( $no_list ) : ?>
			<ul>
				<li><label><input type="hidden" name="nolists" value="0"><input type="checkbox" name="nolists" value="1" <?php checked( $user_settings['nolists'] ); ?>> <?php echo esc_html__( 'subscribers not assigned to a list', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $no_list ) . ' ' . esc_html__( 'total', 'mailster' ) . ')</span>'; ?></label></li>
			</ul>
			<?php endif; ?>
			<h3><?php esc_html_e( 'Conditions', 'mailster' ); ?>:</h3>
				<?php mailster( 'conditions' )->view( $user_settings['conditions'], 'conditions' ); ?>

			<h3><?php esc_html_e( 'Status', 'mailster' ); ?>:</h3>
				<p>
				<input type="hidden" name="status[]" value="-1">
				<?php foreach ( mailster( 'subscribers' )->get_status( null, true ) as $i => $name ) : ?>
				<label><input type="checkbox" name="status[]" value="<?php echo $i; ?>" <?php checked( in_array( $i, $user_settings['status'] ) ); ?>> <?php echo $name; ?> </label>
				<?php endforeach; ?>
				</p>
			<h3><?php esc_html_e( 'Output Options', 'mailster' ); ?>:</h3>
				<p>
					<label><input type="hidden" name="header" value="0"><input type="checkbox" name="header" value="1" <?php checked( $user_settings['header'] ); ?>> <?php esc_html_e( 'Include Header', 'mailster' ); ?> </label>
				</p>
				<p>
					<label><?php esc_html_e( 'Date Format', 'mailster' ); ?>:
					<select name="dateformat">
					<option value="0" <?php selected( $user_settings['dateformat'], 0 ); ?>>timestamp - (<?php echo current_time( 'timestamp' ); ?>)</option>
					<?php $d = mailster( 'helper' )->timeformat(); ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					<?php $d = mailster( 'helper' )->dateformat(); ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					<?php $d = 'Y-m-d H:i:s'; ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					<?php $d = 'Y-m-d'; ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					<?php $d = 'Y-d-m H:i:s'; ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					<?php $d = 'Y-d-m'; ?>
					<option value="<?php echo $d; ?>" <?php selected( $user_settings['dateformat'], $d ); ?>>
					<?php echo $d . ' - (' . date( $d, current_time( 'timestamp' ) ) . ')'; ?>
					</option>
					</select>
					</label>
				</p>
				<p>
					<label><?php esc_html_e( 'Output Format', 'mailster' ); ?>:
					<select name="outputformat">
						<option value="xls" <?php selected( $user_settings['outputformat'], 'xls' ); ?>><?php esc_html_e( 'Excel Spreadsheet', 'mailster' ); ?></option>
						<option value="csv" <?php selected( $user_settings['outputformat'], 'csv' ); ?>><?php esc_html_e( 'CSV', 'mailster' ); ?></option>
						<option value="html" <?php selected( $user_settings['outputformat'], 'html' ); ?>><?php esc_html_e( 'HTML', 'mailster' ); ?></option>
						</select>
					</label>
					<label id="csv-separator"<?php echo 'csv' != $user_settings['outputformat'] ? ' style="display: none;"' : ''; ?>><?php esc_html_e( 'Separator', 'mailster' ); ?>:
					<select name="separator">
						<option value=";" <?php selected( $user_settings['separator'], ';' ); ?>>;</option>
						<option value="," <?php selected( $user_settings['separator'], ',' ); ?>>,</option>
						<option value="|" <?php selected( $user_settings['separator'], '|' ); ?>>|</option>
						<option value="tab" <?php selected( $user_settings['separator'], 'tab' ); ?>><?php esc_html_e( '[Tab]', 'mailster' ); ?></option>
					</select>
					</label>
				</p>
				<p>
					<label><?php esc_html_e( 'CharSet', 'mailster' ); ?>:
					<?php
					$charsets = array(
						'UTF-8'       => 'Unicode 8',
						'ISO-8859-1'  => 'Western European',
						'ISO-8859-2'  => 'Central European',
						'ISO-8859-3'  => 'South European',
						'ISO-8859-4'  => 'North European',
						'ISO-8859-5'  => 'Latin/Cyrillic',
						'ISO-8859-6'  => 'Latin/Arabic',
						'ISO-8859-7'  => 'Latin/Greek',
						'ISO-8859-8'  => 'Latin/Hebrew',
						'ISO-8859-9'  => 'Turkish',
						'ISO-8859-10' => 'Nordic',
						'ISO-8859-11' => 'Latin/Thai',
						'ISO-8859-13' => 'Baltic Rim',
						'ISO-8859-14' => 'Celtic',
						'ISO-8859-15' => 'Western European revision',
						'ISO-8859-16' => 'South-Eastern European',
					);
					?>
					<select name="encoding">
						<?php foreach ( $charsets as $code => $region ) { ?>
						<option value="<?php echo $code; ?>" <?php selected( $user_settings['encoding'], $code ); ?>><?php echo $code; ?> - <?php echo $region; ?></option>
						<?php } ?>
					</select>
					</label>
				</p>
				<p>
					<label><?php esc_html_e( 'MySQL Server Performance', 'mailster' ); ?>:
					<select name="performance" class="performance">
						<option value="100" <?php selected( $user_settings['performance'], '100' ); ?>><?php esc_html_e( 'low', 'mailster' ); ?></option>
						<option value="1000" <?php selected( $user_settings['performance'], '1000' ); ?>><?php esc_html_e( 'normal', 'mailster' ); ?></option>
						<option value="5000" <?php selected( $user_settings['performance'], '5000' ); ?>><?php esc_html_e( 'high', 'mailster' ); ?></option>
						<option value="20000" <?php selected( $user_settings['performance'], '20000' ); ?>><?php esc_html_e( 'super high', 'mailster' ); ?></option>
						<option value="50000" <?php selected( $user_settings['performance'], '50000' ); ?>><?php esc_html_e( 'super extreme high', 'mailster' ); ?></option>
					</select>
					</label>
				</p>
				<h3><?php esc_html_e( 'Define order and included columns', 'mailster' ); ?>:</h3>
					<?php

					$columns = array(
						'ID'        => esc_html__( 'ID', 'mailster' ),
						'email'     => mailster_text( 'email' ),
						'firstname' => mailster_text( 'firstname' ),
						'lastname'  => mailster_text( 'lastname' ),
					);

					$customfields = mailster()->get_custom_fields();
					$customfields = wp_list_pluck( $customfields, 'name' );

					$extra = array(
						'_statuscode' => esc_html__( 'Statuscode', 'mailster' ),
						'_listnames'  => esc_html__( 'Listnames', 'mailster' ),
					);

					$meta = array(
						'hash'       => esc_html__( 'Hash', 'mailster' ),
						'status'     => esc_html__( 'Status', 'mailster' ),
						'added'      => esc_html__( 'Added', 'mailster' ),
						'updated'    => esc_html__( 'Updated', 'mailster' ),
						// 'ip' => __('IP Address', 'mailster'),
						'signup'     => esc_html__( 'Signup Date', 'mailster' ),
						'ip_signup'  => esc_html__( 'Signup IP', 'mailster' ),
						'confirm'    => esc_html__( 'Confirm Date', 'mailster' ),
						'ip_confirm' => esc_html__( 'Confirm IP', 'mailster' ),
						'rating'     => esc_html__( 'Rating', 'mailster' ),
					);

					$meta = $meta + mailster( 'subscribers' )->get_meta_keys();

					$fields = array( '_number' => '#' ) + $columns + $customfields + $extra + $meta;

					$fields = apply_filters( 'mailster_export_fields', $fields );

					?>
				<div class="export-order-wrap">
					<ul class="export-order unselected">
						<?php foreach ( $fields as $id => $data ) : ?>
							<?php
							if ( in_array( $id, $user_settings['column'] ) ) {
								continue;
							}
							?>
							<li><input type="checkbox" name="column[]" value="<?php echo esc_attr( $id ); ?>"> <?php echo esc_html( strip_tags( $data ) ); ?></li>
						<?php endforeach; ?>
					</ul>
					<div class="export-order-middle">
						<button class="export-order-add button-secondary">&#8680;</button><br>
						<button class="export-order-remove button-secondary">&#8678;</button>
					</div>
					<ul class="export-order selected">
					<?php foreach ( $user_settings['column'] as $id ) : ?>
						<?php
						if ( ! isset( $fields[ $id ] ) ) {
							continue;
						}
						?>
						<li><input type="checkbox" name="column[]" value="<?php echo esc_attr( $id ); ?>" checked> <?php echo esc_html( $fields[ $id ] ); ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
				<p>
					<input class="button button-large button-primary" type="submit" value="<?php esc_attr_e( 'Download Subscribers', 'mailster' ); ?>" />
				</p>
			</form>
			</div>

			<div class="step2">
				<h2 class="export-status"></h2>
				<div class="step2-body"></div>
			</div>

		<?php else : ?>

		<p><?php esc_html_e( 'No Subscriber found!', 'mailster' ); ?></p>

		<?php endif; ?>

		<?php elseif ( 'delete' == $currentpage && current_user_can( 'mailster_bulk_delete_subscribers' ) ) : ?>

			<?php

			$lists   = mailster( 'lists' )->get( null, false );
			$no_list = mailster( 'lists' )->count( false );

			?>
			<?php if ( ! empty( $lists ) || $no_list ) : ?>

			<div class="step1">
				<form method="post" id="delete-subscribers">
				<?php wp_nonce_field( 'mailster_nonce' ); ?>

				<h3><?php esc_html_e( 'Lists', 'mailster' ); ?>:</h3>

				<?php if ( ! empty( $lists ) ) : ?>
				<ul>
					<li><label><input type="checkbox" class="list-toggle"> <?php esc_html_e( 'toggle all', 'mailster' ); ?></label></li>
					<li>&nbsp;</li>
					<?php mailster( 'lists' )->print_it( null, false, 'lists', esc_html__( 'total', 'mailster' ) ); ?>
				</ul>
				<?php endif; ?>

				<?php if ( $no_list ) : ?>
				<ul>
					<li><label><input type="checkbox" name="nolists" value="1"> <?php esc_html_e( 'subscribers not assigned to a list', 'mailster' ) . ' <span class="count">(' . number_format_i18n( $no_list ) . ' ' . esc_html__( 'total', 'mailster' ) . ')</span>'; ?></label></li>
				</ul>
				<?php endif; ?>

				<h3><?php esc_html_e( 'Conditions', 'mailster' ); ?>:</h3>

				<?php mailster( 'conditions' )->view( array(), 'conditions' ); ?>

				<h3><?php esc_html_e( 'Status', 'mailster' ); ?>:</h3>
				<p>
					<?php foreach ( mailster( 'subscribers' )->get_status( null, true ) as $i => $name ) { ?>
					<label><input type="checkbox" name="status[]" value="<?php echo $i; ?>" checked> <?php echo $name; ?> </label>
					<?php } ?>
				</p>
				<p>
					<label><input type="checkbox" name="remove_lists" value="1"> <?php esc_html_e( 'Remove selected lists', 'mailster' ); ?> </label>
				</p>
				<p>
					<label><input type="checkbox" name="remove_actions" value="1"> <?php esc_html_e( 'Remove all actions from affected users', 'mailster' ); ?> </label>
				</p>
				<p>
					<input id="delete-subscriber-button" class="button button-large button-primary" type="submit" value="<?php esc_attr_e( 'Delete Subscribers permanently', 'mailster' ); ?>" />
				</p>
				<h2 class="delete-status"></h2>
				</form>
			</div>

			<?php else : ?>

		<p><?php esc_html_e( 'No Subscriber found!', 'mailster' ); ?></p>

	<?php endif; ?>

<?php else : ?>

	<h2><?php esc_html_e( 'You do not have sufficient permissions to access this page.', 'mailster' ); ?></h2>

<?php endif; ?>

	<div id="progress" class="progress hidden"><span class="bar" style="width:0%"><span></span></span></div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
