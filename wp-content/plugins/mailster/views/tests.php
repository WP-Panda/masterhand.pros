<?php wp_nonce_field( 'mailster_nonce', 'mailster_nonce', false ); ?>
<?php $classes = array( 'wrap', 'mailster-tests' ); ?>

<?php
	$heading = 'Test @ ' . date( 'r' ) . ' from ' . site_url();

	$textoutput = str_repeat( '=', strlen( $heading ) ) . "\n" . $heading . "\n" . str_repeat( '=', strlen( $heading ) ) . "\n";
?>
<div class="<?php echo implode( ' ', $classes ); ?>">
<h1><?php esc_html_e( 'Mailster Tests', 'mailster' ); ?></h1>

<p><?php esc_html_e( 'Mailster will now run some tests to ensure everything is running smoothly. Please keep this browser window open until all tests are finished.', 'mailster' ); ?></p>

<div class="tests-wrap no-success">
	<a class="button button-primary button-hero start-test"><?php esc_html_e( 'Start Tests', 'mailster' ); ?></a>
	<input type="hidden" id="singletest" value="<?php echo isset( $_GET['test'] ) ? esc_attr( $_GET['test'] ) : ''; ?>">
	<div id="progress" class="progress"><span class="bar" style="width:0%"><span></span></span></div>
	<h4 class="test-info"><?php esc_html_e( 'Click the button to start test', 'mailster' ); ?></h4>
	<div id="outputnav" class="nav-tab-wrapper hide-if-no-js">
		<a class="nav-tab nav-tab-active" href="#selftest"><?php esc_html_e( 'Output', 'mailster' ); ?></a>
		<a class="nav-tab" href="#textoutput"><?php esc_html_e( 'Text Output', 'mailster' ); ?></a>
		<a class="nav-tab" href="#systeminfo"><?php esc_html_e( 'System Info', 'mailster' ); ?></a>
	</div>
	<div class="subtab" id="subtab-selftest">
		<p class="tests-toggles">
			<?php esc_html_e( 'Show', 'mailster' ); ?>:
			<label class="label-error" title="<?php esc_attr_e( 'Errors must be fixed in order to make Mailster work correctly.', 'mailster' ); ?>"> <input type="checkbox" name="" data-type="error" checked><i></i><?php esc_html_e( 'Errors', 'mailster' ); ?></label>
			<label class="label-warning" title="<?php esc_attr_e( 'Warnings are recommended to get fixed but not required to make Mailster work.', 'mailster' ); ?>"> <input type="checkbox" name="" data-type="warning" checked><i></i><?php esc_html_e( 'Warnings', 'mailster' ); ?></label>
			<label class="label-notice" title="<?php esc_attr_e( 'Notices normally don\'t require any action.', 'mailster' ); ?>"> <input type="checkbox" name="" data-type="notice" checked><i></i><?php esc_html_e( 'Notices', 'mailster' ); ?></label>
			<label class="label-success" title="<?php esc_attr_e( 'Best requirements for Mailster to work.', 'mailster' ); ?>"> <input type="checkbox" name="" data-type="success"><i></i><?php esc_html_e( 'Success', 'mailster' ); ?></label>
		</p>
		<div class="tests-output"></div>
	</div>
	<div class="subtab" id="subtab-textoutput">
		<div class="tests-textoutput-wrap"><textarea class="tests-textoutput code" data-pretext="<?php echo esc_attr( $textoutput ); ?>"></textarea></div>
		<a class="clipboard" data-clipboard-target=".tests-textoutput"><?php esc_html_e( 'Copy Info to Clipboard', 'mailster' ); ?></a>
	</div>
	<div class="subtab" id="subtab-systeminfo">
		<div class="tests-textoutput-wrap"><textarea id="system_info_content" readonly class="code">
		</textarea></div>
		<a class="clipboard" data-clipboard-target="#system_info_content"><?php esc_html_e( 'Copy Info to Clipboard', 'mailster' ); ?></a>
	</div>

</div>

<div id="ajax-response"></div>
<br class="clear">
</div>
