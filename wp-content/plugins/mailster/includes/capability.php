<?php

$mailster_capabilities = array(

	'edit_newsletters'                    => array(
		'title' => esc_html__( 'edit campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
	),

	'publish_newsletters'                 => array(
		'title' => esc_html__( 'send campaigns', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
	),

	'delete_newsletters'                  => array(
		'title' => esc_html__( 'delete campaigns', 'mailster' ),
		'roles' => array( 'contributor', 'author', 'editor' ),
	),

	'edit_others_newsletters'             => array(
		'title' => esc_html__( 'edit others campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'delete_others_newsletters'           => array(
		'title' => esc_html__( 'delete others campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'duplicate_newsletters'               => array(
		'title' => esc_html__( 'duplicate campaigns', 'mailster' ),
		'roles' => array( 'author', 'editor' ),
	),

	'duplicate_others_newsletters'        => array(
		'title' => esc_html__( 'duplicate others campaigns', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_edit_autoresponders'        => array(
		'title' => esc_html__( 'edit autoresponders', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_edit_others_autoresponders' => array(
		'title' => esc_html__( 'edit others autoresponders', 'mailster' ),
		'roles' => array( 'editor' ),
	),


	'mailster_change_template'            => array(
		'title' => esc_html__( 'change template', 'mailster' ),
		'roles' => array( 'editor' ),
	),
	'mailster_save_template'              => array(
		'title' => esc_html__( 'save template', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_see_codeview'               => array(
		'title' => esc_html__( 'see codeview', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_change_plaintext'           => array(
		'title' => esc_html__( 'change text version', 'mailster' ),
		'roles' => array( 'editor' ),
	),


	'mailster_edit_subscribers'           => array(
		'title' => esc_html__( 'edit subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_add_subscribers'            => array(
		'title' => esc_html__( 'add subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_delete_subscribers'         => array(
		'title' => esc_html__( 'delete subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_edit_forms'                 => array(
		'title' => esc_html__( 'edit forms', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_add_forms'                  => array(
		'title' => esc_html__( 'add forms', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_delete_forms'               => array(
		'title' => esc_html__( 'delete forms', 'mailster' ),
		'roles' => array( 'editor' ),
	),


	'mailster_manage_subscribers'         => array(
		'title' => esc_html__( 'manage subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_import_subscribers'         => array(
		'title' => esc_html__( 'import subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_import_wordpress_users'     => array(
		'title' => esc_html__( 'import WordPress Users', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_export_subscribers'         => array(
		'title' => esc_html__( 'export subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_bulk_delete_subscribers'    => array(
		'title' => esc_html__( 'bulk delete subscribers', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_add_lists'                  => array(
		'title' => esc_html__( 'add lists', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_edit_lists'                 => array(
		'title' => esc_html__( 'edit lists', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_delete_lists'               => array(
		'title' => esc_html__( 'delete lists', 'mailster' ),
		'roles' => array( 'editor' ),
	),



	'mailster_manage_addons'              => array(
		'title' => esc_html__( 'manage addons', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_manage_templates'           => array(
		'title' => esc_html__( 'manage templates', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_edit_templates'             => array(
		'title' => esc_html__( 'edit templates', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_delete_templates'           => array(
		'title' => esc_html__( 'delete templates', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_upload_templates'           => array(
		'title' => esc_html__( 'upload templates', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_update_templates'           => array(
		'title' => esc_html__( 'update templates', 'mailster' ),
		'roles' => array(),
	),


	'mailster_dashboard'                  => array(
		'title' => esc_html__( 'access dashboard', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_dashboard_widget'           => array(
		'title' => esc_html__( 'see dashboard widget', 'mailster' ),
		'roles' => array( 'editor' ),
	),

	'mailster_manage_capabilities'        => array(
		'title' => esc_html__( 'manage capabilities', 'mailster' ),
		'roles' => array(),
	),

	'mailster_manage_licenses'            => array(
		'title' => esc_html__( 'manage licenses', 'mailster' ),
		'roles' => array(),
	),

);

$mailster_capabilities = apply_filters( 'mymail_capabilities', apply_filters( 'mailster_capabilities', $mailster_capabilities ) );
