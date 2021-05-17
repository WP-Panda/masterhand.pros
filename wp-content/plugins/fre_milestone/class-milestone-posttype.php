<?php
/**
 * Milestone class
 */
if(class_exists( 'AE_Posts' )) :
	class AE_Milestone_Posttype extends AE_Posts {
		public static $instance;

		/**
		 * getInstance method
		 * @param void
		 * @return object $instance
		 *
		 * @since 1.0
		 * @package FREELANCEENGINE
		 * @category MILESTONE
		 * @author tatthien
		 */
		public static function getInstance() {
			if( !self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct method
		 * @param array $taxs
		 * @param array $meta_data
		 * @param array $localize
		 * @return void
		 *
		 * @since 1.0
		 * @package FREELANCEENGINE
		 * @category MILESTONE
		 * @author tatthien
		 */
		public function __construct( $taxs = array(), $meta_data = array(), $localize = array() ) {
			$post_type = 'ae_milesotne';
			parent::__construct( $post_type, $taxs, $meta_data, $localize );

			$this->taxs = array();
			$this->meta_data = array(
				'index',
				'position_order',
				'class_label',
				'status_label'
			);
			$this->convert = array(
				'index',
				'position_order',
				'class_label',
				'status_label'
			);
		}

		/**
		 * Init method for class AE_Milestone_Posttype
		 * @param void
		 * @return void
		 *
		 * @since 1.0
		 * @package FREELANCEENGINE
		 * @category MILESTONE
		 * @author tatthien
		 */
		public function init() {
			$this->ae_register_milestone_posttype();
			$this->ae_register_milestone_post_status();
		}

		/**
		 * Register new ae_milestone posttype
		 * @param void
		 * @return void
		 *
		 * @since 1.0
		 * @package FREELANCEENGINE
		 * @category MILESTONE
		 * @author tatthien
		 */
		public function ae_register_milestone_posttype() {
			$labels = array(
		        'name' => __('Milestones', ET_DOMAIN) ,
		        'singular_name' => __('Milestone', ET_DOMAIN) ,
		        'add_new' => __('Add new', ET_DOMAIN) ,
		        'add_new_item' => __('Add new', ET_DOMAIN) ,
		        'edit_item' => __('Edit milestone', ET_DOMAIN) ,
		        'new_item' => __('New milestone', ET_DOMAIN) ,
		        'view_item' => __('View milestone', ET_DOMAIN) ,
		        'search_items' => __('Search milestones', ET_DOMAIN) ,
		        'not_found' => __('No milestones found', ET_DOMAIN) ,
		        'not_found_in_trash' => __('No milestones found in Trash', ET_DOMAIN) ,
		        'parent_item_colon' => __('Parent milestone:', ET_DOMAIN) ,
		        'menu_name' => __('Milestones', ET_DOMAIN) ,
		    );

		    $args = array(
		        'labels' => $labels,
		        'hierarchical' => true,
		        'public' => false,
		        'show_ui' => true,
		        'show_in_menu' => true,
		        'show_in_admin_bar' => true,
		        'menu_position' => 5,
		        'show_in_nav_menus' => true,
		        'publicly_queryable' => true,
		        'exclude_from_search' => false,
		        'has_archive' => 'milestones',
		        'query_var' => true,
		        'can_export' => true,
		        'rewrite' => array(
		            'slug' => 'milestone'
		        ) ,
		        'capability_type' => 'post',
		        'supports' => array(
		            'title',
		            'editor',
		            'author',
		            'thumbnail',
		            'excerpt',
		            'custom-fields',
		            'trackbacks',
		            'comments',
		            'revisions',
		            'page-attributes',
		            'post-formats'
		        ),
		        'menu_icon' => 'dashicons-yes'
		    );
		    register_post_type( 'ae_milestone', $args );
		    global $ae_post_factory;
		    $ae_post_factory->set( 'ae_milestone', new AE_Posts( 'ae_milestone', $this->taxs, $this->meta_data ) );
		}

		/*
		 * Register milestone post status
		 */
		public function ae_register_milestone_post_status() {
			// register post status: Open
		    register_post_status( 'open', array(
		        'label'                     => __( 'Open', ET_DOMAIN ),
		        'private'                   => true,
		        'public'                    => false,
		        'exclude_from_search'       => false,
		        'show_in_admin_all_list'    => true,
		        'show_in_admin_status_list' => true,
		        'label_count'               => _n_noop('Open <span class="count">(%s)</span>', 'Open <span class="count">(%s)</span>') ,
		    ));

		    // register post status: Resolve - when freelancer resolves a milestone
		    register_post_status( 'resolve', array(
		        'label'                     => __( 'Resolve', ET_DOMAIN ),
		        'private'                   => true,
		        'public'                    => false,
		        'exclude_from_search'       => false,
		        'show_in_admin_all_list'    => true,
		        'show_in_admin_status_list' => true,
		        'label_count'               => _n_noop('Resolve <span class="count">(%s)</span>', 'Resolve <span class="count">(%s)</span>') ,
		    ));

		    // register post status: Finish - when employer finishs a milstone
		    register_post_status( 'done', array(
		        'label'                     => __( 'Done', ET_DOMAIN ),
		        'private'                   => true,
		        'public'                    => false,
		        'exclude_from_search'       => false,
		        'show_in_admin_all_list'    => true,
		        'show_in_admin_status_list' => true,
		        'label_count'               => _n_noop('Done <span class="count">(%s)</span>', 'Done <span class="count">(%s)</span>') ,
		    ));

		    // register post status: Finish - when employer finishs a milstone
		    register_post_status( 'reopen', array(
		        'label'                     => __( 'Reopen', ET_DOMAIN ),
		        'private'                   => true,
		        'public'                    => false,
		        'exclude_from_search'       => false,
		        'show_in_admin_all_list'    => true,
		        'show_in_admin_status_list' => true,
		        'label_count'               => _n_noop('Reopen <span class="count">(%s)</span>', 'Reopen <span class="count">(%s)</span>') ,
		    ));
		}
	}
endif;