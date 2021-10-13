<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

//register FAQ posts
add_action( 'init', 'register_faq_post_type' );
function register_faq_post_type() {
	// Раздел вопроса - faqcat
	register_taxonomy( 'faqcat', [ 'faq' ], [
		'label'             => 'Question section',
		// определяется параметром $labels->name
		'labels'            => [
			'name'              => 'Question Sections',
			'singular_name'     => 'Question section',
			'search_items'      => 'Search Question section',
			'all_items'         => 'All Question Sections',
			'parent_item'       => 'Parental question section',
			'parent_item_colon' => 'Parental question section:',
			'edit_item'         => 'Edit Question section',
			'update_item'       => 'Refresh Question Section',
			'add_new_item'      => 'Add Question Section',
			'new_item_name'     => 'New Question Section',
			'menu_name'         => 'Question section',
		],
		'description'       => 'Headings for the question section',
		// описание таксономии
		'public'            => true,
		'show_in_nav_menus' => false,
		// равен аргументу public
		'show_ui'           => true,
		// равен аргументу public
		'show_tagcloud'     => false,
		// равен аргументу show_ui
		'hierarchical'      => true,
		'rewrite'           => [ 'slug' => 'faq', 'hierarchical' => false, 'with_front' => false, 'feed' => false ],
		'show_admin_column' => true,
		// Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
	] );
	// тип записи - вопросы - faq
	register_post_type( 'faq', [
		'label'               => 'Questions',
		'labels'              => [
			'name'          => 'Questions',
			'singular_name' => 'Question',
			'menu_name'     => 'Questions archive',
			'all_items'     => 'All questions',
			'add_new'       => 'Add a question',
			'add_new_item'  => 'Add new question',
			'edit'          => 'Edit',
			'edit_item'     => 'Edit question',
			'new_item'      => 'New question',
		],
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'rest_base'           => '',
		'show_in_menu'        => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'menu_icon'           => 'dashicons-category',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => [
			'slug'       => 'help/%faqcat%',
			'with_front' => false,
			'pages'      => false,
			'feeds'      => false,
			'feed'       => false
		],
		'has_archive'         => 'faq',
		'query_var'           => true,
		'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
		'taxonomies'          => [ 'faqcat' ],
	] );
}