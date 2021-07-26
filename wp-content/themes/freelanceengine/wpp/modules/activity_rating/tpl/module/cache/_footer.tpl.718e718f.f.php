<?php
/** Fenom template '_footer.tpl' compiled at 2021-07-20 17:31:12 */
return new Fenom\Render( $fenom, function ( $var, $tpl ) {
	?>
    <script>
        var langActiveRat = <?php
		/* _footer.tpl:2: {$lang.js|json_encode} */
		echo json_encode( $var["lang"]["js"] ); ?>
    </script>
    </body>
    </html><?php
}, array(
	'options'   => 128,
	'provider'  => false,
	'name'      => '_footer.tpl',
	'base_name' => '_footer.tpl',
	'time'      => 1588253059,
	'depends'   => array(
		0 =>
			array(
				'_footer.tpl' => 1588253059,
			),
	),
	'macros'    => array(),

) );
