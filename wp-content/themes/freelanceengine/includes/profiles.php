<?php
/**
 * Registers a new post type profile
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string See optional args description above.
 *
 * @return object|WP_Error the registered post type object, or an error object
 * @uses $wp_post_types Inserts new post type object into the list
 *
 */
function fre_register_profile() {
	$labels = [
		'name'               => __( 'Profiles', ET_DOMAIN ),
		'singular_name'      => __( 'Profile', ET_DOMAIN ),
		'add_new'            => _x( 'Add New profile', ET_DOMAIN, ET_DOMAIN ),
		'add_new_item'       => __( 'Add New profile', ET_DOMAIN ),
		'edit_item'          => __( 'Edit profile', ET_DOMAIN ),
		'new_item'           => __( 'New profile', ET_DOMAIN ),
		'view_item'          => __( 'View profile', ET_DOMAIN ),
		'search_items'       => __( 'Search Profiles', ET_DOMAIN ),
		'not_found'          => __( 'No Profiles found', ET_DOMAIN ),
		'not_found_in_trash' => __( 'No Profiles found in Trash', ET_DOMAIN ),
		'parent_item_colon'  => __( 'Parent profile:', ET_DOMAIN ),
		'menu_name'          => __( 'Profiles', ET_DOMAIN ),
	];
	$args   = [
		'labels'              => $labels,
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 6,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => ae_get_option( 'fre_profile_archive', 'profiles' ),
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug' => ae_get_option( 'fre_profile_slug', 'profile' )
		],
		'capability_type'     => 'post',
		'supports'            => [
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
		]
	];
	register_post_type( PROFILE, $args );

	global $ae_post_factory;
	$ae_post_factory->set( PROFILE, new AE_Posts( PROFILE, [ 'project_category' ], [
		'et_professional_title',
		'hour_rate',
		'et_experience',
		'et_receive_mail',
		'currency',
		'country'
	] ) );
}

add_action( 'init', 'fre_register_profile' );
/**
 * Disable button add new of profiles
 */
function disable_button_add_new() {
	// Hide sidebar link
	global $submenu;
	unset( $submenu['edit.php?post_type=fre_profile'][10] );
	// Hide link on listing page
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'fre_profile' ) {
		echo '<style type="text/css">#favorite-actions, .add-new-h2, .tablenav, .page-title-action { display:none; } .admin-bar post-type-fre_profile .tablenav{display:inherit;} </style>';
	}
}

add_action( 'admin_menu', 'disable_button_add_new' );
/**
 * Registers a new post type portfolio
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string See optional args description above.
 *
 * @return object|WP_Error the registered post type object, or an error object
 * @uses $wp_post_types Inserts new post type object into the list
 *
 */
function fre_register_portfolio() {
	$labels = [
		'name'               => __( 'Portfolios', ET_DOMAIN ),
		'singular_name'      => __( 'Portfolio', ET_DOMAIN ),
		'add_new'            => _x( 'Add New portfolio', ET_DOMAIN, ET_DOMAIN ),
		'add_new_item'       => __( 'Add New portfolio', ET_DOMAIN ),
		'edit_item'          => __( 'Edit portfolio', ET_DOMAIN ),
		'new_item'           => __( 'New portfolio', ET_DOMAIN ),
		'view_item'          => __( 'View portfolio', ET_DOMAIN ),
		'search_items'       => __( 'Search portfolio', ET_DOMAIN ),
		'not_found'          => __( 'No portfolio found', ET_DOMAIN ),
		'not_found_in_trash' => __( 'No portfolios found in Trash', ET_DOMAIN ),
		'parent_item_colon'  => __( 'Parent portfolio:', ET_DOMAIN ),
		'menu_name'          => __( 'Portfolios', ET_DOMAIN ),
	];
	$args   = [
		'labels'              => $labels,
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 6,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => ae_get_option( 'fre_portfolio_archive', 'portfolios' ),
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [ 'slug' => ae_get_option( 'fre_portfolio', 'portfolio' ) ],
		'capability_type'     => 'post',
		'supports'            => [
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
		]
	];
	global $ae_post_factory;
	$ae_post_factory->set( PORTFOLIO, new AE_Posts( PORTFOLIO, [ 'project_category' ], [ 'portfolio_link' ] ) );
	//    $ae_post_factory->set(PORTFOLIO, new AE_Posts(PORTFOLIO, array('skill'), array('portfolio_link')));
	register_post_type( PORTFOLIO, $args );
}

add_action( 'init', 'fre_register_portfolio' );


function fre_update_profile_available( $result, $user_data ) {
	if ( ae_user_role( $result ) == FREELANCER ) {
		$user_available = get_user_meta( $result, 'user_available', true );
		$profile_id     = get_user_meta( $result, 'user_profile_id', true );
		if ( $profile_id ) {
			update_post_meta( $profile_id, 'user_available', $user_available );
		}
	}
}

add_action( 'ae_update_user', 'fre_update_profile_available', 10, 2 );

add_filter( 'template_include', 'profile_category_template_include', 1 );
function profile_category_template_include( $template ) {

	if ( strpos( $_SERVER['REQUEST_URI'], '/profile_category' ) !== false ) {
		global $wp_query;


		add_filter( 'wp_title', 'change_profile_seo_title', 1 );
		add_filter( 'aioseop_title', 'change_profile_seo_title', 1 );

		function change_profile_seo_title() {
			$urle   = $_SERVER['REQUEST_URI'];
			$arurle = explode( '/', $urle );
			if ( count( $arurle ) > 2 ) {
				$cattitle = ucfirst( str_replace( '-', ' ', end( $arurle ) ) ) . ' | ' . get_bloginfo( 'name' );
			} else {
				$cattitle = __( 'Profile category' ) . ' | ' . get_bloginfo( 'name' );
			}

			return $cattitle;
		}

		add_filter( 'body_class', function ( $classes ) {
			return array_merge( $classes, [ 'page-template-page-proffessionals page' ] );
		} );
		status_header( 200 );

		$category     = preg_replace( '/\/profile_category/', '', $_SERVER['REQUEST_URI'] );
		$new_template = empty( $category ) ? locate_template( [ 'page-category-profiles.php' ] ) : locate_template( [ 'taxonomy-profile_category.php' ] );

		if ( ! empty( $wp_query->query['category_name'] ) && $wp_query->query['category_name'] == 'profile_category' && ! empty( $wp_query->query['name'] ) ) {
			$wp_query->query['post_type']             = PROFILE;
			$wp_query->query['project_category']      = $wp_query->query['name'];
			$wp_query->query_vars['project_category'] = $wp_query->query['name'];
			$wp_query->query['name']                  = '';
			$wp_query->query['category_name']         = '';
		}

		if ( ! empty( $new_template ) ) {
			return $new_template;

		}
	}

	return $template;
}


class Fre_ProfileAction extends AE_PostAction {
	function __construct( $post_type = 'fre_profile' ) {
		$this->post_type = PROFILE;
		// add action fetch profile
		$this->add_ajax( 'ae-fetch-profiles', 'fetch_post' );
		/**
		 * sync profile
		 * # update , insert ...
		 *
		 * @param Array $request
		 *
		 * @since v1.0
		 */
		$this->add_ajax( 'ae-profile-sync', 'sync_post' );
		//        $this->add_ajax('ae-sync-user', 'sync');


		/**
		 * hook convert a profile to add custom meta data
		 *
		 * @param Object $result profile object
		 *
		 * @since v1.0
		 */
		$this->add_filter( 'ae_convert_fre_profile', 'ae_convert_profile' );
		// hook to groupy by, group profile by author
		$this->add_filter( 'posts_groupby', 'posts_groupby', 10, 2 );
		// filter post where to check user professional title
		$this->add_filter( 'posts_search', 'fre_posts_search', 10, 2 );
		// add filter posts join to join post meta and get et professional title
		$this->add_filter( 'posts_join', 'fre_join_post', 10, 2 );
		// Delete profile after admin delete user
		$this->add_action( 'remove_user_from_blog', 'fre_delete_profile_after_delete_user' );
		// delete education, certification, experience
		$this->add_ajax( 'ae-profile-delete-meta', 'deleteMetaProfile' );

		// for page with company
		$this->add_filter( 'posts_where', 'filter_where_profile', 10, 2 );
		$this->add_filter( 'posts_orderby', 'profile_posts_orderby', 10, 2 );
		$this->add_action( 'wp_footer', 'render_template_js_company' );
		$this->add_filter( 'posts_fields', 'fre_posts_fields', 10, 2 );


		//        $this->add_filter('the_posts','filter_the_posts');
	}

	function filter_the_posts( $posts ) {
		file_put_contents( __DIR__ . '/cl.txt', '$posts-' . json_encode( $posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "\r\n", FILE_APPEND );

	}

	/**
	 * convert  profile
	 *
	 * @package FreelanceEngine
	 */
	function ae_convert_profile( $result ) {
		//$this->masterhand_send_teams($result->project_category);
		if ( $result->post_type == PROFILE ) {
			$result->et_avatar     = get_avatar( $result->post_author, 70 );
			$result->et_avatar_url = get_avatar_url( $result->post_author );
			$result->author_link   = get_author_posts_url( $result->post_author );
			$result->author_excp   = mb_substr( strip_tags( get_the_content() ), 0, 130 ) . '...';
			$result->author_excp2  = mb_substr( get_the_content(), 0, 270 ) . '... <a class="more-info">' . __( 'More info', ET_DOMAIN ) . '</a>';

			$result->portfolio_img = '';

			$et_experience = (int) $result->et_experience;
			if ( $et_experience == 1 ) {
				$result->experience = sprintf( __( "<span>%d</span> year experience", ET_DOMAIN ), $et_experience );
			} else {
				$result->experience = sprintf( __( "<span>%d</span> years experience", ET_DOMAIN ), $et_experience );
			}
			// override profile ling
			$result->permalink   = $result->author_link;
			$result->author_name = get_the_author_meta( 'display_name', $result->post_author );

			$result->hourly_rate_price = '';
			if ( (int) $result->hour_rate > 0 ) {
				$result->hourly_rate_price = sprintf( __( "<span>%s/hr</span>", ET_DOMAIN ), fre_price_format( $result->hour_rate ) );
			}

			$user_status = get_user_pro_status( $result->post_author );

			$result->user_available = get_user_meta( $result->post_author, 'user_available', true );
			$project_worked         = (int ) get_post_meta( $result->ID, 'total_projects_worked', true );
			$result->project_worked = sprintf( __( '%d projects worked', ET_DOMAIN ), $project_worked );
			if ( $project_worked == 1 ) {
				$result->project_worked = sprintf( __( '%d project worked', ET_DOMAIN ), $project_worked );
			}
			$email_skill         = get_post_meta( $result->ID, 'email_skill', true );
			$result->email_skill = ! empty( $email_skill ) ? $email_skill : 0;
			$earned              = fre_count_total_user_earned( $result->post_author );
			$result->earned      = price_about_format( $earned ) . ' ' . __( 'earned', ET_DOMAIN );
			$result->excerpt     = fre_trim_words( $result->post_content, 80 ); // 1.8.3.1

			$installmentPlan         = get_post_meta( $result->ID, 'installmentPlan', true );
			$result->installmentPlan = ! empty( $installmentPlan ) ? 1 : 0;

			include $_SERVER['DOCUMENT_ROOT'] . '/dbConfig.php';
			$location = getLocation( $result->post_author );
			if ( ! empty( $location['country'] ) ) {
				$str = [];
				foreach ( $location as $key => $item ) {
					if ( ! empty( $item['name'] ) ) {
						$str[] = $item['name'];
					}
				}
				$str = ! empty( $str ) ? implode( ' - ', $str ) : 'Error';
			} else {
				$str = '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
			}
			$result->str_location = $str;

			if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
				$str = translate( 'PRO', ET_DOMAIN );
			} else {
				$str = '';
			}
			$result->str_pro_account = $str;

			//master-expert pro
			$stat       = '';
			$visualFlag = getValueByProperty( $user_status, 'visual_flag' );
			if ( $visualFlag ) {
				$visualFlagNumber = get_user_meta( $result->post_author, 'visual_flag', true );
				switch ( $visualFlagNumber ) {
					case 1:
						$stat = '<span class="status">' . translate( 'Master', ET_DOMAIN ) . '</span>';
						break;
					case 2:
						$stat = '<span class="status">' . translate( 'Creator', ET_DOMAIN ) . '</span>';
						break;
					case 3:
						$stat = '<span class="status">' . translate( 'Expert', ET_DOMAIN ) . '</span>';
						break;
				}
			}
			$result->str_status = $stat;

			// Get users categories
			$rpc      = $result->project_category;
			$term_ids = [];
			if ( $rpc ) {
				foreach ( $rpc as $item ) {
					$term_ids[] = $item;
				}
			}
			$cp_categories = '';
			foreach ( $term_ids as $current_term_id ) {
				// Get term by id (''term_id'') in Categories taxonomy.
				$taxonomy_terms = get_term_by( 'id', $current_term_id, 'project_category' );
				$cp_categories  .= $taxonomy_terms->name . ', ';
			}
			$result->profile_categories = mb_substr( $cp_categories, 0, - 2 );

			$result->activity_rating = wpp_get_user_rating( $result->post_author );
			$result->reviews_rating  = HTML_review_rating_user( $result->post_author, 1 );
		}
		if ( $result->post_type == COMPANY ) {
			global $user_ID;

			$result->phone   = ! empty( get_post_meta( $result->ID, 'phone', true ) ) ? get_post_meta( $result->ID, 'phone', true ) : '';
			$result->adress  = ! empty( get_post_meta( $result->ID, 'adress', true ) ) ? get_post_meta( $result->ID, 'adress', true ) : '';
			$result->raiting = ! empty( get_post_meta( $result->ID, 'raiting', true ) ) ? get_post_meta( $result->ID, 'raiting', true ) : 0;
			$result->site    = ! empty( get_post_meta( $result->ID, 'site', true ) ) ? get_post_meta( $result->ID, 'site', true ) : '';

			$rate            = str_replace( ',', '.', $result->raiting );
			$result->percent = $rate / 0.05;

			if ( ! empty( get_post_meta( $result->ID, 'cat', true ) ) ) {
				$cat = get_post_meta( $result->ID, 'cat', true );
				if ( ! empty( get_post_meta( $result->ID, 'sub', true ) ) ) {
					$cat = get_post_meta( $result->ID, 'sub', true );
				}
				$term            = get_term_by( 'id', $cat, 'project_category', ARRAY_A );
				$result->str_cat = $term['name'];
			} else {
				$result->str_cat = '';
			}

			$country = ! empty( get_post_meta( $result->ID, 'country', true ) ) ? get_post_meta( $result->ID, 'country', true ) : '';
			$state   = ! empty( get_post_meta( $result->ID, 'state', true ) ) ? get_post_meta( $result->ID, 'state', true ) : '';
			$city    = ! empty( get_post_meta( $result->ID, 'city', true ) ) ? get_post_meta( $result->ID, 'city', true ) : '';

			$location = getLocation( 0, [ 'country' => $country, 'state' => $state, 'city' => $city ] );
			if ( ! empty( $location['country'] ) ) {
				$str_location = [];
				foreach ( $location as $key => $item ) {
					if ( ! empty( $item['name'] ) ) {
						$str_location[] = $item['name'];
					}
				}
				$str_location = ! empty( $str_location ) ? implode( ' - ', $str_location ) : 'Error';
			} else {
				$str_location = '<i>' . __( 'No country information', ET_DOMAIN ) . '</i>';
			}
			$result->str_location = $str_location;

			if ( ! empty( get_post_meta( $result->ID, 'email', true ) ) && ( userRole( $user_ID ) == EMPLOYER ) ) {
				$result->button = '<input class="btn-get-quote" type="button" value="' . __( 'Get a Quote', ET_DOMAIN ) . '">';
			} else {
				$result->button = '';
			}
		}

		return $result;
	}


	/**
	 * group profile by user id if user can not edit other profils
	 *
	 * @param string $groupby
	 * @param object $groupby Wp_Query object
	 *
	 * @since  1.0
	 * @author Dakachi
	 */
	function posts_groupby( $groupby, $query ) {
		global $wpdb;
		$query_vars = ( isset( $query->query_vars['post_type'] ) ) ? $query->query_vars : '';
		if ( isset( $query_vars['post_type'] ) && $query_vars['post_type'] == $this->post_type ) {
			$groupby = "{$wpdb->posts}.post_author";
		}
		if ( ! empty( $query->query['with_companies'] ) ) {
			$groupby = $wpdb->posts . '.ID';
		}

		return $groupby;
	}

	function profile_posts_orderby( $orderby, $query ) {
		global $wpdb;
		if ( ! empty( $query->query['with_companies'] ) ) {
			$str_orderby = $wpdb->posts . '.post_type DESC, ' . $orderby;
			$orderby     = $str_orderby;
		}

		return $orderby;
	}

	/**
	 * add post where when user search, check professional title
	 *
	 * @param String $where SQL where string
	 *
	 * @since  1.4
	 * @author Dakachi
	 */
	function fre_posts_search( $post_search, $query ) {

		global $wpdb;
		if ( isset( $_REQUEST['query']['s'] ) && $_REQUEST['query']['s'] != '' && $query->query_vars['post_type'] == 'fre_profile' ) {
			$post_search_is_user_logged_in = "AND (wp_posts.post_password = '')";
			$pos                           = stripos( $post_search, $post_search_is_user_logged_in );

			if ( $pos === false ) {
				$post_search                   = substr( $post_search, 0, - 2 );
				$post_search_is_user_logged_in = '';

			} else { // значит юзер авторизирован
				$len         = strlen( $post_search_is_user_logged_in ) + 4;
				$post_search = substr( $post_search, 0, - $len );
			}

			$search = $_REQUEST['query']['s'];
			$q      = [];
			$q['s'] = strtolower( $search );
			// there are no line breaks in <input /> fields
			$search                  = str_replace( [ "\r", "\n" ], '', esc_sql( $search ) );
			$q['search_terms_count'] = 1;

			if ( ! empty( $q['sentence'] ) ) {
				$q['search_terms'] = [ $q['s'] ];
			} else {
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
					$q['search_terms_count'] = count( $matches[0] );
					$q['search_terms']       = $matches[0];
					// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
					if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 ) {
						$q['search_terms'] = [ $q['s'] ];
					}
				} else {
					$q['search_terms'] = [ $q['s'] ];
				}

			}
			foreach ( $q['search_terms'] as $term ) {
				$terms_query = get_terms( [
					'taxonomy'   => [ 'project_category' ],
					'hide_empty' => 0,
					'search'     => $term,
				] );

				$post_search .= " OR wp_skill.title LIKE '%" . $term . "%'";
				//$post_search .= " OR wp_referral_code.referral_code LIKE '%" . $term . "%'";
				$post_search .= " OR wp_users.display_name = '%" . $term . "%'";

				if ( ! empty( $terms_query ) ) {

					foreach ( $terms_query as $t ) {
						$post_search .= " OR wp_term_relationships.term_taxonomy_id = $t->term_id";
					}
				}
				$post_search .= " OR (wp_usermeta.meta_key = 'first_name' AND wp_usermeta.meta_value LIKE '%" . $term . "%')";
				$post_search .= " OR (wp_usermeta.meta_key = 'last_name' AND wp_usermeta.meta_value LIKE '%" . $term . "%')";
				//$post_search .= " OR wp_term_relationships.term_taxonomy_id = 10110";
			}
			$post_search .= ") ";
			$post_search .= $post_search_is_user_logged_in;
		}

		return $post_search;
	}

	/**
	 * join postmeta table to get et_professional_title
	 *
	 * @param String $join SQL join string
	 *
	 * @since  1.4
	 * @author Dakachi
	 */
	function fre_join_post( $join, $query ) {
		global $wpdb;
		if ( isset( $_REQUEST['query']['s'] ) && $_REQUEST['query']['s'] != '' && $query->query_vars['post_type'] == PROFILE ) {
			/*$join .= " INNER JOIN wp_users ON (wp_posts.post_author = wp_users.ID) INNER JOIN wp_referral_code ON ($wpdb->posts.post_author = wp_referral_code.user_id) INNER JOIN wp_usermeta ON ($wpdb->posts.post_author = wp_usermeta.user_id) LEFT JOIN wp_skill ON $wpdb->posts.post_author = wp_skill.user_id ";*/
			$join .= " INNER JOIN wp_users ON (wp_posts.post_author = wp_users.ID) INNER JOIN wp_usermeta ON ($wpdb->posts.post_author = wp_usermeta.user_id) LEFT JOIN wp_skill ON ($wpdb->posts.post_author = wp_skill.user_id) LEFT JOIN wp_term_relationships ON ($wpdb->posts.ID = wp_term_relationships.object_id) ";
		}

		if ( isset( $_REQUEST['query']['earning'] ) && ( $_REQUEST['query']['earning'] ) ) {
			$join .= " LEFT JOIN $wpdb->posts as prof_post_bid ON prof_post_bid.post_author =  $wpdb->posts.post_author AND prof_post_bid.post_type='bid'
             AND prof_post_bid.post_status='complete'";
			$join .= " LEFT JOIN $wpdb->postmeta as prof_post_bid_meta ON prof_post_bid.ID =  prof_post_bid_meta.post_id
            AND prof_post_bid_meta.meta_key = 'bid_budget'";
		}

		if ( isset( $query->query['orderby'] ) && $query->query['orderby'] == 'rating' ) {
			$join .= " LEFT JOIN wp_activity_rating ON ( wp_activity_rating.user_id = wp_posts.post_author ) ";
		}

		return $join;
	}

	function fre_posts_fields( $fields, $query ) {
		if ( isset( $query->query['orderby'] ) && $query->query['orderby'] == 'rating' ) {
			//            $fields .= ' , wp_activity_rating.rating+wp_activity_rating.pro_rating as rating ';
			$fields .= " , (select
                             case wp_pro_paid_users.status_id
                                    when null THEN wp_activity_rating.rating
                                    else wp_activity_rating.rating+(wp_activity_rating_config.value/100*wp_activity_rating.rating)
                             END
                              from wp_activity_rating_config
                             where wp_activity_rating_config.name='coefficient.proStatus') as rating ";
		}

		return $fields;
	}

	function filter_where_profile( $where, $query ) {
		global $wpdb;

		if ( ! empty( $query->query['with_companies'] ) ) {
			$str_where = 'and (';
			$str_where .= stristr( $where, '(' );
			$str_where .= ") or (wp_posts.post_type = 'company' AND wp_posts.post_status = 'publish' ";

			$meta = [];

			$category_meta = [];
			if ( isset( $query->query['cat'] ) && $query->query['cat'] != '' ) {
				$term          = get_term_by( 'slug', $query->query['cat'], 'project_category', ARRAY_A );
				$category_meta = [
					'type'       => 'category',
					'meta_key'   => 'cat',
					'meta_value' => $term['term_id']
				];
				if ( isset( $query->query['sub'] ) && $query->query['sub'] != '' ) {
					// 200920 choice
					$term          = get_term_by( 'slug', $query->query['sub'], 'project_category', ARRAY_A );
					$category_meta = [
						'type'       => 'category',
						'meta_key'   => 'sub',
						'meta_value' => $term['term_id']
					];
				}
				$meta[] = $category_meta;
			}

			if ( isset( $query->query['country'] ) && $query->query['country'] != '' ) {
				$location_type = "country";
				$location[]    = $location_type;
				if ( isset( $query->query['state'] ) && $query->query['state'] != '' ) {
					$location_type = "state";
					array_unshift( $location, $location_type );
					if ( isset( $query->query['city'] ) && $query->query['city'] != '' ) {
						$location_type = "city";
						array_unshift( $location, $location_type );
					}
				}

				if ( ! empty( $category_meta ) && $location_type !== 'country' ) {
					global $wpdb;

					foreach ( $location as $item_location ) {
						$location_meta = [
							'type'       => 'location',
							'meta_key'   => $item_location,
							'meta_value' => (int) $query->query[ $item_location ]
						];
						$sql           = "SELECT count(*)
                            FROM wp_posts
                            INNER JOIN wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id)
                            INNER JOIN wp_postmeta as mt1 ON (wp_posts.ID = mt1.post_id)
                            WHERE (wp_posts.post_type = 'company' AND wp_posts.post_status = 'publish' 
                            and (wp_postmeta.meta_key = '{$category_meta['meta_key']}' AND CAST(wp_postmeta.meta_value AS SIGNED) = '{$category_meta['meta_value']}')
                            and (mt1.meta_key = '{$location_meta['meta_key']}' AND CAST(mt1.meta_value AS SIGNED) = '{$location_meta['meta_value']}'))";
						$result        = $wpdb->get_var( $sql );

						if ( $result != 0 ) {
							break;
						}
					}
				} else {
					$location_meta = [
						'type'       => 'location',
						'meta_key'   => $location_type,
						'meta_value' => (int) $query->query[ $location_type ]
					];
				}
				$meta[] = $location_meta;
			}

			if ( isset( $query->query['s'] ) && $query->query['s'] != '' ) {
				$meta[] = [
					'type'       => 'search',
					'meta_value' => $query->query['s']
				];
			}

			$table_meta = [ 'wp_postmeta', 'mt1', 'mt2' ];
			foreach ( $meta as $key => $meta_item ) {
				$table = $table_meta[ $key ];
				if ( $meta_item['type'] == 'category' ) {
					$str_where .= " and ({$table}.meta_key = '{$meta_item['meta_key']}' AND CAST({$table}.meta_value AS SIGNED) = '{$meta_item['meta_value']}') ";
				} elseif ( $meta_item['type'] == 'location' ) {
					$str_where .= " and ({$table}.meta_key = '{$meta_item['meta_key']}' AND CAST({$table}.meta_value AS SIGNED) = '{$meta_item['meta_value']}') ";
				} elseif ( $meta_item['type'] == 'search' ) {
					$str_where .= " and ( ({$table}.meta_key in ('adress','phone','site') and {$table}.meta_value LIKE '%{$meta_item['meta_value']}%') ";
					$str_where .= "or (wp_posts.post_title LIKE '%{$meta_item['meta_value']}%') ) ";
				}
			}

			$where = $str_where . " ) ";
		}

		return $where;
	}

	/**
	 * filter query args before query
	 *
	 * @package FreelanceEngine
	 */
	public function filter_query_args( $query_args ) {
		if ( isset( $_REQUEST['query'] ) ) {
			$query      = $_REQUEST['query'];
			$query_args = wp_parse_args( $query_args, $query );

			// list featured profile
			if ( isset( $query['meta_key'] ) ) {
				$query_args['meta_key'] = $query['meta_key'];
				if ( isset( $query['meta_value'] ) ) {
					$query_args['meta_value'] = $query['meta_value'];
				}
			}

			if ( isset( $query['country'] ) && $query['country'] != '' ) {
				$location = 'country';
				if ( isset( $query['state'] ) && $query['state'] != '' ) {
					$location = 'state';
					if ( isset( $query['city'] ) && $query['city'] != '' ) {
						$location = 'city';
					}
				}
				$query_args['meta_query'][] = [
					"key"     => $location,
					"value"   => (int) $query[ $location ],
					"type"    => "numeric",
					"compare" => "=",
				];
			}

			if ( isset( $query['cat'] ) ) {
				if ( $query['cat'] != '' ) {
					$query_args['project_category'] = $query['cat'];
					if ( isset( $query['sub'] ) && $query['sub'] != '' ) {
						$res_cat = get_term_by( 'slug', $query['cat'], 'project_category', ARRAY_A );
						$res_sub = get_term_by( 'slug', $query['sub'], 'project_category', ARRAY_A );
						if ( $res_sub['parent'] == $res_cat['term_id'] ) {
							$query_args['project_category'] = $query['sub'];
						}
					}
				} else {
					$query_args['project_category'] = '';
				}
			} elseif ( isset( $query['sub'] ) ) {
				if ( $query['sub'] != '' ) {
					$query_args['project_category'] = $query['sub'];
				} else {
					$res = get_term_by( 'slug', $query['project_category'], 'project_category', ARRAY_A );
					if ( $res['parent'] != 0 ) {
						$res_cat                        = get_term_by( 'id', $res['parent'], 'project_category', ARRAY_A );
						$query_args['project_category'] = $res_cat['slug'];
					}
				}
			} elseif ( isset( $query['project_category'] ) && $query['project_category'] != '' ) {
				$query_args['project_category'] = $query['project_category'];
			} else {
				$query_args['project_category'] = '';
			}

			// Order
			if ( isset( $query['orderby'] ) ) {
				$orderby = $query['orderby'];
				switch ( $orderby ) {
					case 'date':
						$query_args['orderby'] = 'date';
						break;
					case 'hour_rate':
						$query_args['meta_key'] = 'hour_rate';
						$query_args['orderby']  = 'meta_value_num date';
						$query_args['order']    = 'DESC';
						break;
					case 'projects_worked':
						$query_args['meta_key'] = 'total_projects_worked';
						$query_args['orderby']  = 'meta_value_num date';
						$query_args['order']    = 'DESC';
						break;

				}
			}
			//check query projects worked
			if ( isset( $query['total_projects_worked'] ) && $query['total_projects_worked'] ) {
				$total_projects_worked = $query['total_projects_worked'];
				switch ( $total_projects_worked ) {
					case '10':
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '10',
							'type'    => 'numeric',
							'compare' => '<=',
						];
						break;
					case '20':
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '11',
							'type'    => 'numeric',
							'compare' => '>=',
						];
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '20',
							'type'    => 'numeric',
							'compare' => '<=',
						];
						break;
					case '30':
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '21',
							'type'    => 'numeric',
							'compare' => '>=',
						];
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '30',
							'type'    => 'numeric',
							'compare' => '<=',
						];
						break;
					case '40':
						$query_args['meta_query'][] = [
							'key'     => 'total_projects_worked',
							'value'   => '30',
							'type'    => 'numeric',
							'compare' => '>',
						];
						break;
				}
			}
		}

		return apply_filters( 'fre_profile_query_args', $query_args, $query );
	}


	function render_template_js_company() {
		get_template_part( 'template-js/company', 'title' );
		get_template_part( 'template-js/company_page_profile', 'item' );
	}

	/**
	 * hanlde profile action
	 *
	 * @package FreelanceEngine
	 */
	function sync_post() {

		global $ae_post_factory, $user_ID, $current_user;
		$request    = $_REQUEST;
		$ae_users   = new AE_Users();
		$user_data  = $ae_users->convert( $current_user );
		$profile    = $ae_post_factory->get( $this->post_type );
		$profile_id = get_user_meta( $user_ID, 'user_profile_id', true );

		/**
		 * @todo  ТУТ НАДО БУДЕТ ЗАМЕНИТЬ НА wp_send_json error пото запретить ответы
		 *
		 *
		 */
		if ( ! AE_Users::is_activate( $user_ID ) ) {
			wp_send_json( [
				'success' => false,
				'msg'     => __( "Your account is pending. You have to activate your account to create profile.", ET_DOMAIN )
			] );
		}


		// set status for profile
		if ( ! isset( $request['post_status'] ) ) {
			$request['post_status'] = 'publish';
		}

		// version 1.8.2 set display name when update profile
		if ( ! empty( $request['display_name'] ) ) {
			wp_update_user( [ 'ID' => $user_ID, 'display_name' => $request['display_name'] ] );
		}

		#social Fields
		$soc_data = apply_filters( 'wpp_social_fields_array', [] );


		foreach ( $soc_data as $one_field ) {

			if ( ! empty( $request[ $one_field['id'] ] ) ) {

				update_user_meta( $user_ID, $one_field['id'], esc_attr( $request[ $one_field['id'] ] ) );

			} else {
				delete_user_meta( $user_ID, $one_field['id'] );
			}

		}

		if ( ! empty( $request['work_experience'] ) && is_array( $request['work_experience'] ) ) {


			$experience = $request['work_experience'];

			if ( ! empty( $experience['title'] ) && ! empty( $experience['subtitle'] ) ) {

				if ( ! empty( $experience['id'] ) ) {

					$meta_id = $experience['id'];
					unset( $experience['id'] );
					global $wpdb;
					$update = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => serialize( $experience ) ], [ 'meta_id' => $meta_id ] );
				} else {
					$update = add_post_meta( $profile_id, 'work_experience', serialize( $experience ) );
				}
				if ( $update === false ) {
					wp_send_json( [
						'success' => false,
						'msg'     => __( "Edit fail.", ET_DOMAIN )
					] );
				}
			}
		}

		if ( ! empty( $request['certification'] ) && is_array( $request['certification'] ) ) {

			$certification = $request['certification'];
			if ( ! empty( $certification['title'] ) && ! empty( $certification['subtitle'] ) ) {
				if ( ! empty( $certification['id'] ) ) {

					$meta_id = $certification['id'];
					unset( $certification['id'] );

					global $wpdb;
					$update = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => serialize( $certification ) ], [ 'meta_id' => $meta_id ] );

				} else {
					$update = add_post_meta( $profile_id, 'certification', serialize( $certification ) );
				}
				if ( false === $update ) {
					wp_send_json( [
						'success' => false,
						'msg'     => __( "Edit fail.", ET_DOMAIN )
					] );
				}
			}

		}

		if ( ! empty( $request['education'] ) && is_array( $request['education'] ) ) {

			$education = $request['education'];
			if ( ! empty( $education['title'] ) && ! empty( $education['subtitle'] ) ) {
				if ( ! empty( $education['id'] ) ) {
					$meta_id = $education['id'];
					unset( $education['id'] );
					global $wpdb;
					$update = $wpdb->update( $wpdb->postmeta, [ 'meta_value' => serialize( $education ) ], [ 'meta_id' => $meta_id ] );
				} else {
					$update = add_post_meta( $profile_id, 'education', serialize( $education ) );
				}
				if ( $update === false ) {
					wp_send_json( [
						'success' => false,
						'msg'     => __( "Edit fail.", ET_DOMAIN )
					] );
				}
			}

		}

		// set profile title
		$request['post_title'] = ! empty( $request['display_name'] ) ? $request['display_name'] : $user_data->display_name;
		if ( 'create' === $request['method'] ) {


			$profile_post = get_post( $profile_id );
			if ( $profile_post && $profile_post->post_status != 'draft' ) {
				wp_send_json( [
					'success' => false,
					'msg'     => __( "You only can have on profile.", ET_DOMAIN )
				] );
			}

		}

		$email_skill = 0;
		if ( isset( $request['email_skill'] ) ) {

			if ( ! empty( $request['email_skill'] ) ) {

				if ( is_array( $request['email_skill'] ) ) {
					$email_skill = ! empty( $request['email_skill'][0] ) ? $request['email_skill'][0] : 0;
				} else {
					$email_skill = $request['email_skill'];
				}
			} else {
				$email_skill = 0;
			}

			update_post_meta( $profile_id, 'email_skill', $email_skill );
		}

		$installmentPlan = ! empty( $request['installmentPlan'] ) ? 1 : 0;
		$profile_id      = get_user_meta( $user_ID, 'user_profile_id', true );
		update_post_meta( $profile_id, 'installmentPlan', $installmentPlan );

		//new start
		if ( 'update' === $request['method'] ) {

			if ( isset( $request['country'] ) ) {
				$user_country = get_user_meta( $user_ID, 'country', true );
				if ( $request['country'] != $user_country ) {
					update_post_meta( $profile_id, 'country', $request['country'] );
					update_user_meta( $user_ID, 'country', $request['country'] );
				}
			}


			if ( isset( $request['state'] ) ) {
				$user_state = get_user_meta( $user_ID, 'state', true );
				if ( $request['state'] != $user_state ) {
					update_post_meta( $profile_id, 'state', $request['state'] );
					update_user_meta( $user_ID, 'state', $request['state'] );
				}
			}

			if ( isset( $request['city'] ) ) {
				$user_city = get_user_meta( $user_ID, 'city', true );
				if ( $request['city'] != $user_city ) {
					update_post_meta( $profile_id, 'city', $request['city'] );
					update_user_meta( $user_ID, 'city', $request['city'] );
				}
			}

			$pro_status = get_post_meta( $profile_id, 'pro_status', true );
			if ( empty( $pro_status ) ) {
				update_post_meta( $profile_id, 'pro_status', 1 );
			}

			if ( isset( $request['visual_flag'] ) ) {
				$visual_flag = get_user_meta( $user_ID, 'visual_flag', true );
				if ( $request['visual_flag'] == 0 ) {
					delete_user_meta( $user_ID, 'visual_flag' );
				} elseif ( $request['visual_flag'] != $visual_flag ) {
					update_user_meta( $user_ID, 'visual_flag', $request['visual_flag'] );
				}
			}

		}

		if ( ! empty( $request['user_paypal'] ) && ! filter_var( $request['user_paypal'], FILTER_VALIDATE_EMAIL ) ) {

			wp_send_json( [
				'success' => false,
				'msg'     => __( "This paypal account must be in an email format", ET_DOMAIN )
			] );

		}
		//new end

		do_action( 'activityRating_oneFieldProfile' );


		// sync profile
		$result = $profile->sync( $request );
		if ( ! is_wp_error( $result ) ) {
			$result->redirect_url = $result->permalink;
			//            $rating_score = get_post_meta($result->ID, 'rating_score', true);
			//            if (!$rating_score) {
			//                update_post_meta($result->ID, 'rating_score', 0);
			//            }
			$user_available = get_user_meta( $user_ID, 'user_available', true );
			update_post_meta( $result->ID, 'user_available', $user_available );
			if ( $request['user_paypal'] ) {
				$user_paypal_field = get_user_meta( $user_ID, 'paypal', true );
				if ( $user_paypal_field && $request['user_paypal'] != $user_paypal_field ) {
					update_user_meta( $user_ID, 'paypal_confirmation', 0 );
				}
			}
			// action create profile
			if ( $request['method'] == 'create' ) {
				//update_post_meta( $result->ID,'hour_rate', 0);//@author: danng  fix query meta in page profiles search in version 1.8.4
				update_post_meta( $result->ID, 'total_projects_worked', 0 );

				$profile_id = get_user_meta( $user_ID, 'user_profile_id', true ); // 1.8.6.1
				update_post_meta( $profile_id, 'email_skill', $email_skill );  // 1.8.6.1

				if ( isset( $request['installmentPlan'] ) ) {
					$installmentPlan = ! empty( $request['installmentPlan'] ) ? 1 : 0;
					update_post_meta( $profile_id, 'installmentPlan', $installmentPlan );
				}

				//new start
				if ( $request['country'] ) {
					update_post_meta( $profile_id, 'country', $request['country'] );
				}
				if ( $request['state'] ) {
					update_post_meta( $profile_id, 'state', $request['state'] );
				}
				if ( $request['city'] ) {
					update_post_meta( $profile_id, 'city', $request['city'] );
				}

				if ( $request['user_paypal'] ) {
					update_user_meta( $user_ID, 'paypal', $request['user_paypal'] );
				}

				if ( $request['project_currency'] ) {
					update_user_meta( $user_ID, 'currency', $request['project_currency'] );
				}
				//new end

				do_action( 'activityRating_oneFieldProfile' );

				// store profile id to user meta
				$response = [
					'success' => true,
					'data'    => $result,
					'msg'     => __( "Your profile has been created successfully.", ET_DOMAIN )
				];

				wp_send_json( $response );
				//action update profile

			} else if ( $request['method'] == 'update' ) {
				if ( $request['user_email'] ) {
					global $current_user;

					if ( $user_ID == $request['post_author'] && $request['user_email'] != $current_user->user_email && $request['user_email'] != $current_user->user_new_email ) {
						if ( email_exists( $request['user_email'] ) ) {
							wp_send_json( [
								'success' => false,
								'data'    => $result,
								'msg'     => __( "This email is already used. Please enter a new email.", ET_DOMAIN )
							] );
						}

						$user_data = [];

						update_user_meta( $request['post_author'], 'user_new_email', $request['user_email'] );
						update_user_meta( $request['post_author'], 'register_status', 'unconfirmnew' );
						$user_data['ID']         = $request['post_author'];
						$user_data['user_email'] = $current_user->user_email;
						$result                  = wp_update_user( $user_data );
						if ( $result != false && ! is_wp_error( $result ) ) {
							fre_update_user_new_email( [
								'user_email' => $current_user->user_email,
								'ID'         => $request['post_author']
							] );
						}
					}
				}
				if ( $request['user_paypal'] && $request['user_paypal'] != $current_user->paypal ) {
					update_user_meta( $request['post_author'], 'paypal', $request['user_paypal'] );
				}

				if ( $request['project_currency'] ) {
					update_user_meta( $request['post_author'], 'currency', $request['project_currency'] );
				}

				if ( empty( $request['hour_rate'] ) && ae_user_role( $user_ID ) !== FREELANCER ) {
					delete_post_meta( $profile_id, 'hour_rate' );
				}

				$response = [
					'success' => true,
					'data'    => $result,
					'msg'     => __( "Your profile has been updated successfully.", ET_DOMAIN )
				];

				wp_send_json( $response );
			}

		} else {
			wp_send_json( [
				'success' => false,
				'data'    => $result,
				'msg'     => $result->get_error_message()
			] );
		}
	}

	/**
	 * Delete profile after delete user
	 *
	 * @param integer $user_id the id of user to delete
	 *
	 * @return void
	 * @since    1.7
	 * @package  freelanceengine
	 * @category PROFILE
	 * @author   Tambh
	 */
	function fre_delete_profile_after_delete_user( $user_id ) {
		if ( current_user_can( 'manage_options' ) ) {
			$profile_ids = $this->fre_get_profile_id( [ 'author' => $user_id ] );
			foreach ( $profile_ids as $key => $value ) {
				wp_trash_post( $value );
			}
		}
	}

	/**
	 * Get profile id
	 *
	 * @param array $args parameter of profile
	 *
	 * @return array $id of profile
	 * @since   1.7
	 * @package freelanceengine
	 * @category
	 * @author  Tambh
	 */
	public function fre_get_profile_id( $args = [] ) {
		global $user_ID;
		$default  = [
			'post_type'      => PROFILE,
			'posts_per_page' => - 1,
			'post_status'    => [ 'publish', 'pending' ]
		];
		$args     = wp_parse_args( $args, $default );
		$result   = get_posts( $args );
		$post_ids = [];
		foreach ( $result as $key => $value ) {
			array_push( $post_ids, $value->ID );
		}

		return $post_ids;
	}

	public function deleteMetaProfile() {
		global $wpdb;
		$request    = $_REQUEST;
		$response   = [
			'success' => false,
			'msg'     => __( "An error, please try again.", ET_DOMAIN )
		];
		$profile_id = get_user_meta( get_current_user_id(), 'user_profile_id', true );
		if ( ! empty( $request['ID'] ) ) {
			$meta_id = $request['ID'];
			$meta    = get_post_meta_by_id( $meta_id );
			if ( $profile_id == $meta->post_id ) {
				$delete = $wpdb->delete( $wpdb->postmeta, [ 'meta_id' => $meta_id ] );
				if ( $delete ) {
					$response = [
						'success' => true,
						'msg'     => __( "Deleted successfully.", ET_DOMAIN )
					];
				}
			} else {
				$response = [
					'success' => false,
					'msg'     => __( "You do not have permission to delete post.", ET_DOMAIN )
				];
			}
		}
		wp_send_json( $response );
	}
}

class Fre_PortfolioAction extends AE_PostAction {
	function __construct( $post_type = 'portfolio' ) {
		$this->post_type = PORTFOLIO;
		$this->add_ajax( 'ae-fetch-portfolios', 'fetch_post' );
		$this->add_ajax( 'ae-fetch-info-portfolio', 'fetch_info_portfolio' );
		$this->add_ajax( 'ae-portfolio-sync', 'sync_post' );
		$this->add_filter( 'ae_convert_portfolio', 'ae_convert_portfolio' );
	}

	/**
	 * filter query args before query
	 *
	 * @package FreelanceEngine
	 */
	public function filter_query_args( $query_args ) {

		if ( isset( $_REQUEST['query'] ) ) {
			$query = $_REQUEST['query'];
			if ( isset( $query['project_category'] ) && $query['project_category'] != '' ) {
				$query_args['project_category'] = $query['project_category'];
			}
		}

		return $query_args;
	}

	function ae_convert_portfolio( $result ) {
		$thumbnail_full_src              = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'full' );
		$thumbnail_src                   = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'portfolio' );
		$result->the_post_thumbnail_full = $thumbnail_full_src[0];
		$result->the_post_thumbnail      = $thumbnail_src[0];
		$list_image_portfolio            = get_post_meta( $result->ID, 'image_portfolio' );
		if ( ! empty( $list_image_portfolio ) ) {
			foreach ( $list_image_portfolio as $ip ) {
				$img = wp_get_attachment_image_src( $ip, 'full' );
				if ( ! empty( $img[0] ) ) {
					$result->list_image_portfolio[] = [
						'id'    => $ip,
						'image' => $img[0]
					];
				}
			}
		} else {
			$result->list_image_portfolio[] = [
				'id'    => get_post_thumbnail_id( $result->ID ),
				'image' => $thumbnail_full_src[0]
			];
		}
		// get for edit portfolio

		$best_work = get_post_meta( $result->ID, 'best_work' );
		if ( $best_work ) {
			$result->html_edit_best_work = 1;
		}
		$client = get_post_meta( $result->ID, 'client' );
		if ( $client ) {
			$result->html_edit_client = 1;
		}


		$et_ajaxnonce         = wp_create_nonce( 'portfolio_img_' . $result->ID . '_et_uploader' );
		$result->et_ajaxnonce = $et_ajaxnonce;


		//get html select project_category for edit portfolio
		$text_option                       = __( 'Select an option', ET_DOMAIN );
		$html_edit_select_profile_category = '<select class="fre-chosen-multi" name="project_category"' . ' multiple data-first_click="true" data-placeholder="' . $text_option . '">';
		$profile_id                        = get_user_meta( $result->post_author, 'user_profile_id', true );
		if ( $profile_id ) {
			$profile_categories                = wp_get_object_terms( $profile_id, 'project_category' );
			$html_edit_select_profile_category .= json_encode( $profile_categories );
		} else {
			$profile_categories = get_terms( 'project_category', [ 'hide_empty' => false ] );
		}
		if ( ! empty( $profile_categories ) ) {
			$value = 'term_id';
			foreach ( $profile_categories as $category ) {
				$selected = '';
				if ( ! empty( $result->project_category ) && in_array( $category->$value, $result->project_category ) ) {
					$selected = 'selected';
				}
				$html_edit_select_profile_category .= '<option value="' . $category->$value . '" ' . $selected . '>' . $category->name . '</option>';
			}
		}
		$html_edit_select_profile_category         .= '</select>';
		$result->html_edit_select_profile_category = $html_edit_select_profile_category;


		return $result;
	}

	/**
	 * hanlde portfolio action
	 *
	 * @package FreelanceEngine
	 */
	function sync_post() {

		global $ae_post_factory, $user_ID, $current_user, $post;

		//	wp_send_json_error( [ 'ssssssssss' ] );

		//wpp_d_log($_REQUEST);

		$request   = $_REQUEST;
		$ae_users  = new AE_Users();
		$user_data = $ae_users->convert( $current_user );
		$portfolio = $ae_post_factory->get( $this->post_type );


		// set status for profile
		if ( ! isset( $request['post_status'] ) ) {
			$request['post_status'] = 'publish';
		}

		// set default post content
		//$request['post_content'] = '';
		if ( ! empty( $request['ID'] ) && $request['method'] == 'create' ) {
			$request['method'] = 'update';
		}

		if ( empty( $request['post_thumbnail'] ) && $request['method'] != 'remove' ) {
			wp_send_json( [
				'success' => false,
				'msg'     => __( 'Please upload images in your portfolio', ET_DOMAIN )
			] );
		}


		// sync place
		$result = $portfolio->sync( $request );
		if ( ! is_wp_error( $result ) ) {
			//update post thumbnail
			if ( isset( $request['post_thumbnail'] ) ) {
				if ( is_array( $request['post_thumbnail'] ) ) {
					delete_post_meta( $result->ID, 'image_portfolio' );
					foreach ( $request['post_thumbnail'] as $v ) {
						add_post_meta( $result->ID, 'image_portfolio', $v );
					}
					$thumb_id = array_shift( $request['post_thumbnail'] );
					set_post_thumbnail( $result, $thumb_id );
				} else {
					$thumb_id = $request['post_thumbnail'];
					set_post_thumbnail( $result, $thumb_id );
				}
			}

			if ( ! empty( $request['best_work'] ) || ! empty( $request['best_work_edit'] ) ) {
				update_post_meta( $result->ID, 'best_work', 1 );
			} else {
				delete_post_meta( $result->ID, 'best_work' );
			}
			if ( ! empty( $request['client'] ) || ! empty( $request['client_edit'] ) ) {
				update_post_meta( $result->ID, 'client', 1 );
			} else {
				delete_post_meta( $result->ID, 'client' );
			}

			do_action( 'activityRating_onePortfolio' );
			// action create profile
			if ( $request['method'] == 'create' ) {
				$convert = $portfolio->convert( $result );
				//                if (is_array($request['skill'])) {
				//                    foreach ($request['skill'] as $sk) {
				//                        $term = get_term_by('slug', $sk, 'skill');
				//                        wp_set_post_terms($result->ID, $term->term_id, 'skill', true);
				//                    }
				//                } else {
				//                    $term = get_term_by('slug', $request['skill'], 'skill');
				//                    wp_set_post_terms($result->ID, $term, 'skill', true);
				//                }

				if ( ! empty( $request['project_category'] ) ) {
					if ( is_array( $request['project_category'] ) ) {
						foreach ( $request['project_category'] as $sk ) {
							$term = get_term_by( 'project_category', $sk, 'project_category' );
							wp_set_post_terms( $result->ID, $term->term_id, 'project_category', true );
						}
					} else {
						$term = get_term_by( 'project_category', $request['project_category'], 'project_category' );
						wp_set_post_terms( $result->ID, $term, 'project_category', true );
					}
				}
				$response = [
					'success' => true,
					'data'    => $convert,
					'msg'     => __( "Portfolio has been created successfully.", ET_DOMAIN )
				];
				wp_send_json( $response );
			} else if ( $request['method'] == 'delete' || $request['method'] == 'remove' ) {
				$response = [
					'success' => true,
					'msg'     => __( "Portfolio has been deleted", ET_DOMAIN )
				];
				wp_send_json( $response );
				//action update profile
			} else if ( $request['method'] == 'update' ) {
				$response = [
					'success' => true,
					'data'    => [
						'redirect_url' => $result->permalink
					],
					'msg'     => __( "Portfolio has been updated successfully", ET_DOMAIN )
				];
				wp_send_json( $response );
			}
		} else {
			wp_send_json( [
				'success' => false,
				'data'    => $result,
				'msg'     => $result->get_error_message()
			] );
		}
	}

	function fetch_info_portfolio() {
		$request  = $_REQUEST;
		$response = [
			'success' => false,
		];
		if ( ! empty( $request['portfolio_id'] ) ) {
			$portfolio = get_post( $request['portfolio_id'] );
			if ( ! empty( $portfolio ) ) {
				$AE_PostAction = AE_Posts::get_instance();
				//                $AE_PostAction->__construct(PORTFOLIO, array('skill'));
				$AE_PostAction->__construct( PORTFOLIO, [ 'project_category' ] );
				$portfolio_info = $AE_PostAction->convert( $portfolio, 'thumbnail' );
				$response       = [
					'success' => true,
					'data'    => $portfolio_info,
				];
			}
		}
		wp_send_json( $response );
	}
}


//new start
function fre_update_user_new_email( $user_data ) {
	global $user_ID, $current_user;

	//    if (!isset($_REQUEST['do'])) {
	//        return;
	//    }
	if ( empty( $user_data['user_email'] ) ) {
		return;
	}
	// if ($user_ID == $user_data['ID'] && $user_data['user_email'] == $current_user->user_email) {
	//     return;
	// }
	$hash = md5( $user_data['user_email'] );

	// update_user_meta($user_data['ID'], 'register_status', 'unconfirmnew');
	// update_user_meta($user_data['ID'], 'user_new_email', $user_data['user_email']);
	update_user_meta( $user_data['ID'], 'key_confirm', $hash );

	$email_text = __( 'Hi ###USERNAME###,
    Your email ###OLDEMAIL### has just been changed on ###NEWEMAIL###.
    If it is correct, please click on the following link to confirm your change:
    ###LINK###
    Otherwise, you are free to ignore this email.
    Regards,
    All at ###SITENAME###
    ###SITEURL###', ET_DOMAIN );
	// $result = wp_update_user(array(
	//     'ID' => $user_data['ID'],
	//     'user_email' => $current_user->user_email
	// ));

	$confirm_link = add_query_arg( [
		'act' => 'confirm',
		'key' => $hash
	], home_url() );

	$new_email = get_user_meta( $user_data['ID'], 'user_new_email' );

	$content = str_replace( '###USERNAME###', $current_user->user_login, $email_text );
	$content = str_replace( '###OLDEMAIL###', $user_data['user_email'], $content );
	$content = str_replace( '###NEWEMAIL###', $new_email[0], $content );
	$content = str_replace( '###SITENAME###', get_site_option( 'blogname' ), $content );
	$content = str_replace( '###LINK###', $confirm_link, $content );
	$content = str_replace( '###SITEURL###', network_home_url(), $content );
	wp_mail( $user_data['user_email'], sprintf( __( '[%s]Email Change Confirmation', ET_DOMAIN ), wp_specialchars_decode( get_option( 'blogname' ) ) ), $content );
}

//add_action('ae_update_user', 'fre_update_user_new_email', 10, 2);
//new end

if ( ! class_exists( 'fre_notice_user_new' ) ) {
	function fre_notice_user_new() {
		global $pagenow;
		if ( $pagenow == 'user-new.php' ) {
			if ( isset( $_GET['update'] ) && ( $_GET['update'] == 'addnoconfirmation' || $_GET['update'] == 'add' || $_GET['update'] == 'newuserconfirmation' ) ) {
				echo '<div class="notice-warning notice ">';
				echo '<p>';
				_e( 'Please complete your profile information and enable "Available for hire" function at page Profile!', ET_DOMAIN );
				echo '</p>';
				echo '</div>';
			}
		}
	}

	add_action( 'admin_notices', 'fre_notice_user_new' );
}