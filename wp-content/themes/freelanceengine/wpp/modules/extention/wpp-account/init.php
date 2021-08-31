<?php
	/**
	 * Created by PhpStorm.
	 * User: WP_Panda
	 * Time: 6:52
	 */

	require_once 'functions/template-functions.php';
	require_once 'functions/template-actions.php';
	require_once  'functions/user-functions.php';

	add_filter( 'wpp_mb_meta_boxes', function( $meta_boxes ) {
		$meta_boxes[] = [
			'title'  => 'Default Fields',
			'id'     => 'default-fields',
			'type'   => 'user', // NOTICE THIS
			'fields' => [
				[
					'id'   => 'first_name', // THIS
					'name' => 'First Name',
					'type' => 'text',
				],
				[
					'id'   => 'last_name', // THIS
					'name' => 'Last Name',
					'type' => 'text',
				],
				[
					'id'   => 'description', // THIS
					'name' => 'Biography',
					'type' => 'textarea',
				],
				[
					'id'   => 'email', // THIS
					'name' => 'email',
					'type' => 'text',
				],
				[
					'id'               => 'image',
					'name'             => 'Image Advanced',
					'type'             => 'image_advanced',
					'force_delete'     => false,
					'max_file_uploads' => 2,
					'max_status'       => 'false',
					'image_size'       => 'thumbnail',
				]
			],
		];
		return $meta_boxes;
	} );