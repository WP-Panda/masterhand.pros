<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_PANDA
	 * Date: 09.03.2019
	 * Time: 12:08
	 */


	$files = [ 'Class_Wpp_Console_Log', 'conditional-tags', 'show-debug', 'helperes', 'wpp-debug-panel' ];

	foreach ( $files as $file ) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';
	}