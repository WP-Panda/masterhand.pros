<?php
	/** Fenom template '_footer.tpl' compiled at 2020-05-15 12:08:30 */
	return new Fenom\Render( $fenom, function( $var, $tpl ) {
		?>
        <script>
            var langActiveRat = <?php
			/* _footer.tpl:2: {$lang.js|json_encode} */
			echo json_encode( $var[ "lang" ][ "js" ] ); ?>
        </script>
        </body>
        </html><?php
	}, [
		'options'   => 128,
		'provider'  => false,
		'name'      => '_footer.tpl',
		'base_name' => '_footer.tpl',
		'time'      => 1588253059,
		'depends'   => [
			0 => [
				'_footer.tpl' => 1588253059,
			],
		],
		'macros'    => [],

	] );
