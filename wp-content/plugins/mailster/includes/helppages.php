<?php

$pages = apply_filters(
	'mailster_help_pages',
	array(
		// newsletter edit page
		'newsletter'                                  => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_mailster_subscribers'        => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_mailster_lists'              => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_mailster_manage_subscribers' => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
		'newsletter_page_mailster_dashboard'          => array(
			'tabs'    => array( array( 'title' => 'More' ) ),
			'sidebar' => 'sidebar',
		),
	)
);
