<?php
	require_once 'wpp/init.php';
	require_once 'settings/init.php';
	if ( is_admin() ) {
		/** Absolute path to the WordPress directory. */
		if ( ! defined( 'ABSPATH' ) ) {
			define( 'ABSPATH', dirname( __FILE__ ) . '/' );
		}
		define( 'CONCATENATE_SCRIPTS', false );
	}
	define( "ET_UPDATE_PATH", "http://update.enginethemes.com/?do=product-update" );
	define( "ET_VERSION", '1.8.7' );
	if ( ! defined( 'ET_URL' ) ) {
		define( 'ET_URL', 'http://www.enginethemes.com/' );
	}
	if ( ! defined( 'ET_CONTENT_DIR' ) ) {
		define( 'ET_CONTENT_DIR', WP_CONTENT_DIR . '/et-content/' );
	}
	define( 'TEMPLATEURL', get_template_directory_uri() );
	$theme_name = 'freelanceengine';
	define( 'THEME_NAME', $theme_name );
	define( 'ET_DOMAIN', 'enginetheme' );
	define( 'MOBILE_PATH', TEMPLATEPATH . '/mobile/' );
	define( 'PROFILE', 'fre_profile' );
	define( 'PROJECT', 'project' );
	define( 'ADVERT', 'advert' );
	define( 'COMPANY', 'company' );
	define( 'BID', 'bid' );
	define( 'PORTFOLIO', 'portfolio' );
	define( 'EMPLOYER', 'employer' );
	define( 'FREELANCER', 'freelancer' );
	define( 'PRICE', 'price' );
	define( 'CURRENCY', 'currency' );
	// define( 'ALLOW_UNFILTERED_UPLOADS', true );
	if ( ! defined( 'THEME_CONTENT_DIR ' ) ) {
		define( 'THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . $theme_name );
	}
	if ( ! defined( 'THEME_CONTENT_URL' ) ) {
		define( 'THEME_CONTENT_URL', content_url() . '/et-content' . '/' . $theme_name );
	}
	// theme language path
	if ( ! defined( 'THEME_LANGUAGE_PATH' ) ) {
		define( 'THEME_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang/' );
	}
	if ( ! defined( 'ET_LANGUAGE_PATH' ) ) {
		define( 'ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang' );
	}
	if ( ! defined( 'ET_CSS_PATH' ) ) {
		define( 'ET_CSS_PATH', THEME_CONTENT_DIR . '/css' );
	}
	if ( ! defined( 'USE_SOCIAL' ) ) {
		define( 'USE_SOCIAL', 1 );
	}
	function isConfirmEmail( $user_ID ) {
		//if(!get_user_meta($user_ID, 'paypal_confirmation', true))
		//    wp_send_json(array(
		//        'success' => false,
		//        'msg' => "Paypal account ".get_user_meta($user_ID, 'paypal', true)." is not confirmed, please, confrim your paypal account in your profile and try the operation again"
		//    ));
		return true;
	}

	require_once dirname( __FILE__ ) . '/includes/index.php';
	require_once TEMPLATEPATH . '/customizer/customizer.php';
	if ( ! class_exists( 'AE_Base' ) ) {
		return;
	}

	class ET_FreelanceEngine extends AE_Base{
		function __construct() {
			// disable admin bar if user can not manage options
			if ( ! current_user_can( 'manage_options' ) || et_load_mobile() ) {
				show_admin_bar( false );
			};
			global $wp_roles;
			/**
			 * register wp_role FREELANCER
			 */
			if ( ! isset( $wp_roles->roles[ FREELANCER ] ) ) {
				//all new roles
				add_role( FREELANCER, __( 'Professional', ET_DOMAIN ), [
					'read'         => true,
					// true allows this capability
					'edit_posts'   => true,
					'delete_posts' => false,
					// Use false to explicitly deny
				] );
			}
			/**
			 * add new role EMPLOYER
			 */
			if ( ! isset( $wp_roles->roles[ EMPLOYER ] ) ) {
				add_role( EMPLOYER, __( 'Client', ET_DOMAIN ), [
					'read'         => true,
					// true allows this capability
					'edit_posts'   => true,
					'delete_posts' => false,
					// Use false to explicitly deny
				] );
			}
			$this->add_action( 'init', 'fre_init' );
			// register_nav_menu('et_header', __("Fullscreen Header menu", ET_DOMAIN));
			register_nav_menu( 'et_header_standard', __( "Standard Header menu", ET_DOMAIN ) );
			// register_nav_menu('et_mobile', __("Mobile menu", ET_DOMAIN));
			// register_nav_menu('et_footer', __("Footer menu", ET_DOMAIN));
			/**
			 * Add role for themes
			 */
			$this->add_filter( 'ae_social_auth_support_role', 'add_custom_role' );
			/**
			 * add query vars
			 */
			$this->add_filter( 'query_vars', 'add_query_vars' );
			/**
			 * enqueue front end scripts
			 */
			$this->add_action( 'wp_enqueue_scripts', 'on_add_scripts', 9 );
			$this->add_action( 'admin_enqueue_scripts', 'on_add_scripts_admin', 9 );
			/**
			 * enqueue front end styles
			 */
			$this->add_action( 'wp_print_styles', 'on_add_styles', 10 );
			// $this->add_action( 'wp_footer', 'on_add_styles',10 );
			/**
			 * Filer query pre get post.
			 */
			$this->add_action( 'pre_get_posts', 'pre_get_posts', 10 );
			//$this->add_filter( 'posts_orderby', 'order_by_post_status', 10, 2 );
			/**
			 * call new classes in footer
			 */
			$this->add_action( 'wp_footer', 'script_in_footer', 100 );
			/**
			 * add return url for user after register
			 */
			$this->add_filter( 'ae_after_insert_user', 'filter_link_redirect_register' );
			/**
			 * add return url for user after login
			 */
			$this->add_filter( 'ae_after_login_user', 'filter_link_redirect_login' );
			/**
			 * check role for user when register
			 */
			$this->add_filter( 'ae_pre_insert_user', 'ae_check_role_user' );
			/**
			 * add user default value
			 */
			$this->add_action( 'ae_insert_user', 'add_user_default_values' );
			/**
			 * update user profile title
			 */
			$this->add_filter( 'ae_update_user', 'sync_profile_data' );
			/**
			 * check role for user when register
			 */
			$this->add_filter( 'ae_convert_post', 'add_new_post_fields' );
			/**
			 * add users custom fields
			 */
			$this->add_filter( 'ae_define_user_meta', 'add_user_meta_fields' );
			/**
			 * restrict pages
			 */
			$this->add_action( 'template_redirect', 'restrict_pages' );
			/**
			 * redirect user to home after logout
			 */
			$this->add_filter( 'logout_url', 'logout_home', 10, 2 );
			/**
			 * filter profile link and replace by author post link
			 */
			$this->add_filter( 'post_type_link', 'post_link', 10, 2 );
			$this->add_filter( 'get_terms_orderby', 'order_terms', 10, 3 );
			/**
			 * add comment type filter dropdow
			 */
			$this->add_filter( 'admin_comment_types_dropdown', 'admin_comment_types_dropdown' );
			/**
			 * add action admin menu prevent seller enter admin area
			 */
			$this->add_action( 'admin_menu', 'redirect_seller' );
			//$this->add_action( 'login_init', 'redirect_login' );
			// add theme support.
			add_theme_support( 'automatic-feed-links' );
			//add new image size
			add_image_size( 'portfolio', 230, 170, true );
			/**
			 * user front end control  : edit profile, update avatar
			 */
			$this->user_action = new AE_User_Front_Actions( new AE_Users() );
			/**
			 * init all action control project
			 */
			$this->project_action = new Fre_ProjectAction();
			$this->offer_action   = new Fre_OfferAction();
			$this->compamy_action = new Fre_CompanyAction();
			// init class bid action control bid
			$this->bid_action = new Fre_BidAction();
			// init action related to review
			$this->review_action = new Fre_ReviewAction();
			// init class control profile action
			$this->profile_action = new Fre_ProfileAction();
			// init class control portfolio update option
			$this->portfolio_action   = new Fre_PortfolioAction();
			$this->testimonial_action = new Fre_TestimonialAction();
			/**
			 * init place meta post
			 */
			new AE_Schedule( PROJECT );
			new AE_PostMeta( PROJECT );
		}

		/**
		 * init theme
		 *
		 * @since  1.0
		 * @author Dakachi
		 */
		function fre_init() {
			update_option( 'site_logo', false );
			// update database fix profile
			if ( ! get_option( 'change_profile_namess' ) ) {
				global $wpdb;
				$wpdb->query( "
                UPDATE $wpdb->posts
                SET post_type = 'fre_profile'
                WHERE post_type = 'profile'
                " );
				update_option( 'change_profile_namess', 1 );
				// echo 1;
			}
			// register a post status: Reject (use when a project was rejected)
			register_post_status( 'reject', [
				'label'                     => __( 'Reject', ET_DOMAIN ),
				'private'                   => true,
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>' ),
			] );
			/* a project after expired date will be changed to archive */
			register_post_status( 'archive', [
				'label'                     => __( 'Archive', ET_DOMAIN ),
				'private'                   => true,
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' ),
			] );
			/* after finish a project, project and accepted bid will be changed to complete */
			register_post_status( 'complete', [
				'label'                     => _x( 'complete', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>' ),
			] );
			register_post_status( 'accept', [
				'label'                     => _x( 'accepted', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>' ),
			] );
			register_post_status( 'unaccept', [
				'label'                     => _x( 'unaccepted', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Unaccepted <span class="count">(%s)</span>', 'Unaccepted <span class="count">(%s)</span>' ),
			] );
			/**
			 * when a project was accept a bid, it will be change to close
			 */
			register_post_status( 'close', [
				'label'                     => _x( 'close', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Close <span class="count">(%s)</span>', 'Close <span class="count">(%s)</span>' ),
			] );
			/**
			 * when employer close project or freelancer quit a project, it change to disputing
			 */
			register_post_status( 'disputing', [
				'label'                     => _x( 'disputing', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Disputing <span class="count">(%s)</span>', 'Disputing <span class="count">(%s)</span>' ),
			] );
			/**
			 * when admin resolve a disputing project, it's status change to disputed
			 */
			register_post_status( 'disputed', [
				'label'                     => _x( 'disputed', 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Disputed <span class="count">(%s)</span>', 'Disputed <span class="count">(%s)</span>' ),
			] );
			/**
			 * when a user dont want employer hide/contact him,
			 * he can change his profile to hide, so no one can contact him
			 */
			register_post_status( 'hide', [
				'label'                     => _x( 'hide', 'post' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Hide <span class="count">(%s)</span>', 'Hide <span class="count">(%s)</span>' ),
			] );
			/**
			 * set up social login
			 */
			if ( function_exists( 'init_social_login' ) ) {
				init_social_login();
			};
			/**
			 * override author link
			 */
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				$wp_rewrite->author_base      = ae_get_option( 'author_base', 'author' );
				$wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
			}
		}

		/**
		 * add custom role for theme
		 */
		function add_custom_role() {
			$role = [ FREELANCER => __( 'Professional', ET_DOMAIN ), EMPLOYER => __( 'Client', ET_DOMAIN ) ];

			return $role;
		}

		function add_user_default_values( $result ) {
			if ( ae_user_role( $result ) == FREELANCER ) {
				update_user_meta( $result, 'user_available', 'on' );
			}
		}

		public function sync_profile_data( $result ) {
			$user      = get_user_by( 'id', $result );
			$ae_users  = AE_Users::get_instance();
			$user_data = $ae_users->convert( $user );
			$profile   = get_post( $user_data->user_profile_id );
			if ( ae_user_role( $result ) == FREELANCER && ! empty( $profile ) && $profile->post_type == "profile" ) {
				//sync profile title
				$args = [
					'ID'         => $user_data->user_profile_id,
					'post_title' => $user->display_name
				];
				wp_update_post( $args );
				//sync profile post_status
				global $wpdb;
				if ( ! $profile = get_post( $profile ) ) {
					return;
				}
				$new_status = isset( $user_data->user_available ) && $user_data->user_available == "on" ? "publish" : "hide";
				if ( $new_status == $profile->post_status ) {
					return;
				}
				$wpdb->update( $wpdb->posts, [
					'post_status' => $new_status
				], [
					'ID' => $profile->ID
				] );
				clean_post_cache( $profile->ID );
				$old_status           = $profile->post_status;
				$profile->post_status = $new_status;
				wp_transition_post_status( $new_status, $old_status, $profile );
			}
		}

		/**
		 * filter redirect link after logout
		 *
		 * @param string $logouturl
		 * @param string $redir
		 *
		 * @since  1.0
		 * @author ThaiNt
		 */
		public function logout_home( $logouturl, $redir ) {
			$redir = get_option( 'siteurl' );

			return $logouturl . '&amp;redirect_to=' . urlencode( $redir );
		}

		/**
		 * add query var
		 */
		function restrict_pages() {
			global $current_user;
			if ( is_page_template( 'page-list-notification.php' ) && ! is_user_logged_in() ) {
				wp_redirect( et_get_page_link( 'login', [ 'ae_redirect_url' => urlencode( et_get_page_link( 'list-notification' ) ) ] ) );
				exit();
			}
			//if user is login and access to page auth
			if ( is_page_template( 'page-auth.php' ) || is_page_template( 'page-register.php' ) ) {
				if ( is_user_logged_in() || ( ! fre_check_register() && ! et_load_mobile() ) ) {
					wp_redirect( home_url() );
					exit();
				}
			}
			//if user is not login and access to page upgrade account (purchase bid)
			if ( is_page_template( 'page-my-project.php' ) ) {
				if ( ! is_user_logged_in() ) {
					$re_url = et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'my-project' ) );
					wp_redirect( $re_url );
					exit();
				}
			}
			//if user is not login and access to page submit project
			if ( is_page_template( 'page-submit-project.php' ) ) {
				if ( ! is_user_logged_in() ) {
					$re_url = et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'submit-project' ) );
					wp_redirect( $re_url );
					exit();
				} else if ( ae_user_role() == FREELANCER && ! fre_share_role() ) {
					wp_redirect( home_url() );
					exit;
				}
			}
			//if user is not login and access to page upgrade account (purchase bid)
			if ( is_page_template( 'page-upgrade-account.php' ) ) {
				if ( ! is_user_logged_in() ) {
					$re_url = et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'upgrade-account' ) );
					wp_redirect( $re_url );
					exit();
				} else {
					if ( ae_user_role() != FREELANCER ) {
						wp_redirect( home_url() );
						exit;
					}
				}
			}
			//if user is not login and access to page buy package for employer account (purchase package post project)
			/*if ( is_page_template( 'page-listing-plan.php' ) ) {
            if ( ! is_user_logged_in() ) {
                $re_url = et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'listing-plan' ) );
                wp_redirect( $re_url );
                exit();
            } else {
                if ( ae_user_role() == FREELANCER ) {
                    wp_redirect( home_url() );
                    exit;
                }
            }
        }*/
			//if user is not login and access to page profile
			if ( ! is_user_logged_in() && is_page_template( 'page-profile.php' ) ) {
				$re_url = et_get_page_link( 'login' ) . '?ae_redirect_url=' . urlencode( et_get_page_link( 'profile' ) ) . '#signin';
				wp_redirect( $re_url );
				exit();
			}
			// prevent user enter single profile
			if ( is_singular( PROFILE ) ) {
				global $post;
				wp_redirect( get_author_posts_url( $post->post_author ) );
				exit;
			}
			// prevent freelancer post project
			if ( is_page_template( 'page-submit-project.php' ) ) {
				if ( ! fre_share_role() && ae_user_role() == FREELANCER ) {
					wp_redirect( home_url() );
					exit;
				}
			}
			/**
			 * prevent user try to view a bid details
			 * # when user enter a link to bid redirect to home url
			 */
			if ( is_singular( BID ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}

		/**
		 * filter profile link and change it to author posts link
		 *
		 * @param String $url  The post url
		 * @param Object $post current post object
		 */
		public function post_link( $url, $post ) {
			if ( $post->post_type == PROFILE ) {
				return get_author_posts_url( $post->post_author );
			}

			return $url;
		}

		/**
		 * Filter the ORDERBY clause of the terms query.
		 *
		 * @param string       $orderby    ORDERBY clause of the terms query.
		 * @param array        $args       An array of terms query arguments.
		 * @param string|array $taxonomies A taxonomy or array of taxonomies.
		 *
		 * @since  1.0
		 *
		 * @author Dakachi
		 */
		public function order_terms( $orderby, $args, $taxonomies ) {
			if ( $taxonomies ) {
				$taxonomy = array_pop( $taxonomies );
			}
			// get taxonomies sort from option
			switch ( $taxonomy ) {
				case 'project_category':
					$_orderby = ae_get_option( 'project_category_order', 'name' );
					break;
				case 'project_type':
					$_orderby = ae_get_option( 'project_type_order', 'name' );
					break;
				default:
					return $orderby;
			}
			// $_orderby = strtolower( $args['orderby'] );
			if ( 'count' == $_orderby ) {
				$orderby = 'tt.count';
			} else if ( 'name' == $_orderby ) {
				$orderby = 't.name';
			} else if ( 'slug' == $_orderby ) {
				$orderby = 't.slug';
			} else if ( 'term_group' == $_orderby ) {
				$orderby = 't.term_group';
			} else if ( 'none' == $_orderby ) {
				$orderby = '';
			} elseif ( empty( $_orderby ) || 'id' == $_orderby ) {
				$orderby = 't.term_id';
			} else {
				$orderby = 't.name';
			}

			return $orderby;
		}

		/**
		 * hook to filter comment type dropdown and add review favorite to filter comment
		 *
		 * @param Array $comment_types
		 */
		function admin_comment_types_dropdown( $comment_types ) {
			$comment_types[ 'fre_review' ] = __( "Professional Review", ET_DOMAIN );
			$comment_types[ 'em_review' ]  = __( "Client Review", ET_DOMAIN );
			$comment_types[ 'fre_report' ] = __( "Report", ET_DOMAIN );
			$comment_types[ 'fre_invite' ] = __( "Invite", ET_DOMAIN );

			return $comment_types;
		}

		/**
		 * redirect wp
		 */
		function redirect_seller() {
			if ( ! ( current_user_can( 'manage_options' ) || current_user_can( 'editor' ) ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}

		function redirect_login() {
			//disable from 1.8.3.1
			if ( ae_get_option( 'login_init' ) && ! is_user_logged_in() ) {
				wp_redirect( home_url() );
				exit;
			}
		}

		/**
		 * add query var
		 */
		function add_query_vars( $vars ) {
			array_push( $vars, 'paymentType' );

			return $vars;
		}

		//add new return custom fields for posts
		function add_new_post_fields( $result ) {
			//author name field
			if ( ! isset( $result->author_name ) ) {
				$author              = get_user_by( 'id', $result->post_author );
				$result->author_name = isset( $author->display_name ) ? $author->display_name : __( 'Unnamed', ET_DOMAIN );
			}
			//comments field
			if ( ! isset( $result->comment_number ) ) {
				$num_comments = get_comments_number( $result->ID );
				if ( et_load_mobile() ) {
					$result->comment_number = $num_comments ? $num_comments : 0;
				} else {
					if ( comments_open( $result->ID ) ) {
						if ( $num_comments == 0 ) {
							$comments = __( 'No Comments', ET_DOMAIN );
						} elseif ( $num_comments > 1 ) {
							$comments = $num_comments . __( ' Comments', ET_DOMAIN );
						} else {
							$comments = __( '1 Comment', ET_DOMAIN );
						}
						$write_comments = '<a href="' . get_comments_link() . '">' . $comments . '</a>';
					} else {
						$write_comments = __( 'Comments are off for this post.', ET_DOMAIN );
					}
					$result->comment_number = $write_comments;
				}
			}
			//post excerpt field
			if ( $result->post_excerpt ) {
				ob_start();
				echo apply_filters( 'the_excerpt', $result->post_excerpt );
				$post_excerpt         = ob_get_clean();
				$result->post_excerpt = $post_excerpt;
			}
			//category field
			$categories = get_the_category();
			$separator  = ' - ';
			$output     = '';
			if ( $categories ) {
				foreach ( $categories as $category ) {
					$output .= '<a href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s", ET_DOMAIN ), $category->name ) ) . '">' . $category->cat_name . '</a>' . $separator;
				}
				$result->category_name = trim( $output, $separator );
			}
			//avatar field
			//if(!isset($result->avatar)){
			$result->avatar = get_avatar( $result->post_author, 65 );

			//}
			return $result;
		}

		//redirect user to url after login
		function filter_link_redirect_login( $result ) {
			$re_url = home_url();
			if ( isset( $_REQUEST[ 'ae_redirect_url' ] ) ) {
				$re_url = $_REQUEST[ 'ae_redirect_url' ];
			}
			$result->redirect_url = apply_filters( 'ae_after_login_link', $re_url );
			$result->do           = "login";

			return $result;
		}

		//redirect user to url after register
		function filter_link_redirect_register( $result ) {
			if ( ! is_wp_error( $result ) ) {
				// $user_info = get_userdata($result->ID);
				$role = ae_user_role( $result->ID );
			} else {
				$role = '';
			}
			$redirect_url = ( $role == "employer" && AE_Users::is_activate( $result->ID ) ) ? home_url() : et_get_page_link( 'profile' );
			if ( $role == FREELANCER ) {
				$redirect_url = et_get_page_link( 'profile', [ 'loginfirst' => 'true' ] ) . '#settings';
			} else {
				$redirect_url = home_url();
			}
			$result->redirect_url = apply_filters( 'ae_after_register_link', $redirect_url );
			$result->do           = "register";

			return $result;
		}

		//prevent user add other roles
		function ae_check_role_user( $user_data ) {
			if ( isset( $user_data[ 'role' ] ) && ( $user_data[ 'role' ] != FREELANCER && $user_data[ 'role' ] != EMPLOYER ) ) {
				unset( $user_data[ 'role' ] );
			}

			return $user_data;
		}

		//add custom fields for user
		function add_user_meta_fields( $default ) {
			$default = wp_parse_args( [
				'user_hour_rate',
				'user_profile_id',
				'user_currency',
				'user_skills',
				'user_available'
			], $default );
			if ( ae_get_option( 'use_escrow' ) ) {
				$default[] = 'paypal';
			}

			return $default;
		}

		function on_add_scripts_admin() {
			if ( current_user_can( 'administrator' ) ) {
				wp_enqueue_script( 'fre_admin_js', get_template_directory_uri() . '/assets/js/fre_admin.js', [
					'underscore',
					'backbone',
					'appengine'
				], '1.0', true );
				wp_enqueue_style( 'admin_additional', get_template_directory_uri() . '/assets/css/admin_additional.css' );
				wp_enqueue_script( 'admin_additional', get_template_directory_uri() . '/assets/js/admin_additional.js', [ 'jquery' ] );
			}
		}

		function on_add_scripts() {
			global $user_ID;
			$this->add_existed_script( 'jquery' );
			$this->add_existed_script( 'underscore' );
			$this->add_existed_script( 'backbone' );
			$this->add_existed_script( 'plupload' );
			$this->add_existed_script( 'appengine' );
			$this->add_existed_script( 'chosen' );
			// add script validator
			$this->add_existed_script( 'jquery-validator' );
			$this->add_existed_script( 'bootstrap' );
			$this->add_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.js', [], ET_VERSION, false );
			if ( is_page_template( 'page-list-testimonial.php' ) ) {
				$this->add_script( 'masonry', get_template_directory_uri() . '/assets/js/masonry.min.js', [], ET_VERSION, false );
			}
			/**
			 * bootstrap slider for search form
			 */
			$this->add_existed_script( 'slider-bt' );
			/**
			 *  Add date picker js
			 */
			//$this->add_existed_script('jquery-ui-datepicker');
			wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.min.js', [], true, false );
			/*
         * Adds JavaScript to pages with the comment form to support
         * sites with threaded comments (when in use).
        */
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				$this->add_existed_script( 'comment-reply' );
			}
			$this->add_script( 'fre-lib', get_template_directory_uri() . '/assets/js/fre-lib.js', [], ET_VERSION, true );
			$this->add_script( 'scroll-bar', get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'fre-lib',
			], ET_VERSION, true );
			$this->add_script( 'front', get_template_directory_uri() . '/assets/js/front.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'fre-lib'
			], ET_VERSION, true );
			$this->add_script( 'npost-project', get_template_directory_uri() . '/assets/js/nproject-list.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'fre-lib',
				'front'
			], ET_VERSION, true );
			$this->add_script( 'owl.carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'fre-lib',
				'front',
				'notification'
			], ET_VERSION, true );
			$this->add_script( 'maskedinput', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', [
				'jquery'
			], ET_VERSION, true );
			$this->add_script( 'custom-scripts', get_template_directory_uri() . '/js/custom-scripts.js', [
				'jquery',
				'owl.carousel',
			], time(), true );
			$this->add_script( 'notification', get_template_directory_uri() . '/assets/js/notification.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'fre-lib',
				'front'
			], ET_VERSION, true );
			// add translatable texts
			wp_localize_script( 'front', 'fre_fronts', [
				'portfolio_img'             => __( 'Please select an image!', ET_DOMAIN ),
				'deleted_file_successfully' => __( 'Files are deleted successfully', ET_DOMAIN ),
				'failed_deleted_file'       => __( 'Failed to delete file', ET_DOMAIN ),
				'cannot_deleted_file'       => __( 'You cannot deleted the file since partner locked this section. Please refresh the page.', ET_DOMAIN )
			] );
			/*
         * js for authenticate in page register & submit project
        */
			if ( ! is_user_logged_in() ) {
				// if (is_page_template('page-upgrade-account.php') || is_page_template('page-auth.php') || is_page_template('page-submit-project.php')) {
				$this->add_script( 'authenticate', get_template_directory_uri() . '/assets/js/authenticate.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], time(), true );
			} else {
				//scrit for confirmation an emai it a user account
				wp_enqueue_script( 'confirm_paypal', get_template_directory_uri() . '/assets/js/paypal_confirmation.js', [], time(), false );
			}
			/*
         * script edit profile
        */
			if ( is_page_template( 'page-profile.php' ) || is_author() || et_load_mobile() ) {
				$this->add_script( 'cropper-js', get_template_directory_uri() . '/assets/js/cropper.min.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
				$this->add_script( 'profile', get_template_directory_uri() . '/assets/js/profile.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], time(), true );
			}
			/*
         * script reset pass
        */
			if ( is_page_template( 'page-reset-pass.php' ) ) {
				$this->add_script( 'reset-pass', get_template_directory_uri() . '/assets/js/reset-pass.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
			}
			// Add style css for mobile version.
			if ( et_load_mobile() ) {
				// $this->add_script('classie', get_template_directory_uri() . '/mobile/js/classie.js',
				//     array('jquery'), ET_VERSION, true
				// );
				/*Date picker*/
				$this->add_script( 'moment-js', get_template_directory_uri() . '/assets/js/moment.min.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
				$this->add_script( 'datepicker-js', get_template_directory_uri() . '/assets/js/bootstrap-datetimepicker.min.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front',
					'moment-js'
				], ET_VERSION, true );
				$this->add_script( 'mobile-main', get_template_directory_uri() . '/mobile/js/main.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
				/*
             * js working on single project
            */
				if ( is_singular( 'project' ) ) {
					$this->add_script( 'single-project', get_template_directory_uri() . '/assets/js/single-project.js', [
						'jquery',
						'underscore',
						'backbone',
						'appengine',
						'front'
					], ET_VERSION, true );
					// add this for social like
					wp_enqueue_script( 'heartbeat' );
					wp_localize_script( 'single-project', 'single_text', [
						'agree'     => __( 'Agree', ET_DOMAIN ),
						'accepted'  => __( 'Accepted', ET_DOMAIN ),
						'skip'      => __( 'Skip', ET_DOMAIN ),
						'working'   => __( 'Working', ET_DOMAIN ),
						'complete'  => __( 'Complete', ET_DOMAIN ),
						'completed' => __( 'Completed', ET_DOMAIN ),
					] );
					$this->add_script( 'workspace', get_template_directory_uri() . '/assets/js/project-workspace.js', [
						'jquery',
						'underscore',
						'backbone',
						'appengine',
						'front'
					], ET_VERSION, true );
				}
				if ( is_page_template( 'page-submit-project.php' ) || is_page_template( 'page-upgrade-account.php' ) || is_page_template( 'page-listing-plan.php' ) || is_page_template( 'page-pro-order-payment.php' ) ) {
					do_action( 'ae_payment_script' );
				}

				return;
			}
			if ( is_page_template( 'page-submit-project.php' ) || is_page_template( 'page-upgrade-account.php' ) || is_page_template( 'page-listing-plan.php' ) || is_page_template( 'page-pro-order-payment.php' ) ) {
				do_action( 'ae_payment_script' );
			}
			/*
         * js working on single project
        */
			if ( is_singular( 'project' ) ) {
				$this->add_script( 'single-project', get_template_directory_uri() . '/assets/js/single-project.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
				$this->add_script( 'workspace', get_template_directory_uri() . '/assets/js/project-workspace.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
				wp_enqueue_script( 'heartbeat' );
				// add this for social like
				$this->add_script( 'addthis-script', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ed5eb280d19b26b', [], ET_VERSION, true );
				wp_localize_script( 'single-project', 'single_text', [
					'agree'     => __( 'Agree', ET_DOMAIN ),
					'accepted'  => __( 'Accepted', ET_DOMAIN ),
					'skip'      => __( 'Skip', ET_DOMAIN ),
					'working'   => __( 'Working', ET_DOMAIN ),
					'complete'  => __( 'Complete', ET_DOMAIN ),
					'completed' => __( 'Completed', ET_DOMAIN ),
				] );
			}
			// Adds Masonry to handle vertical alignment of footer widgets.
			if ( is_active_sidebar( 'de-footer-1' ) ) {
				$this->add_existed_script( 'jquery-masonry' );
			}
			// $this->add_script('index', get_template_directory_uri() . '/assets/js/index.js', array(
			//     'jquery',
			//     'underscore',
			//     'backbone',
			//     'appengine',
			//     'front',
			//     'datepicker-js'
			// ) , ET_VERSION, true);
			/*Date picker*/
			$this->add_script( 'moment-js', get_template_directory_uri() . '/assets/js/moment.min.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'front'
			], ET_VERSION, true );
			$this->add_script( 'datepicker-js', get_template_directory_uri() . '/assets/js/bootstrap-datetimepicker.min.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'front',
				'moment-js'
			], ET_VERSION, true );
			// author wow js
			$this->add_script( 'wow-scroll', get_template_directory_uri() . '/assets/js/index.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'front'
			], ET_VERSION, true );
			// if(is_page_template( 'page-submit-project.php' )){
			//     $this->add_script('post-project', get_template_directory_uri().'/js/post-project.js', array('jquery', 'underscore','backbone', 'appengine', 'front'), ET_VERSION, true);
			// }
			// author page js
			if ( is_author() ) {
				$this->add_script( 'author-page', get_template_directory_uri() . '/assets/js/author.js', [
					'jquery',
					'underscore',
					'backbone',
					'appengine',
					'front'
				], ET_VERSION, true );
			}
			$this->add_script( 'my-project-page', get_template_directory_uri() . '/assets/js/my-project.js', [
				'jquery',
				'underscore',
				'backbone',
				'appengine',
				'front'
			], ET_VERSION, true );
			// author profile js
			// if( is_singular( PROFILE ) || is_author() ){
			//     $this->add_script('single-profile', get_template_directory_uri().'/js/single-profile.js', array('jquery', 'underscore','backbone', 'appengine', 'front'), ET_VERSION, true);
			// }
			// UI element page js
			//$this->add_script('ui-element-script', get_template_directory_uri() . '/js/uielement.js', array() , ET_VERSION, true);

			$this->add_script( 'dropzone', get_template_directory_uri() . '/assets/libs/dropzone/dropzone.min.js', [
				'jquery',
			], time(), true );

			$this->add_script( 'quill', get_template_directory_uri() . '/assets/libs/quill/quill.min.js', [
				'jquery',
			], time(), true );

			$this->add_script( 'wpp', get_template_directory_uri() . '/assets/js/wpp-js.js', [
				'jquery',
				'dropzone',
				'underscore',
				'backbone',
				'appengine',
				'front'
			], time(), true );

			wp_localize_script( 'wpp', 'WppJsData', [
				'upload' => admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
				'delete' => admin_url( 'admin-ajax.php?action=handle_deleted_media' )
			] );
		}

		function on_add_styles() {
			/*$this->add_existed_style('bootstrap');*/
			// Font Awesome
			$this->add_style( 'font-icon', get_template_directory_uri() . '/assets/css/font-awesome.min.css', [], ET_VERSION );
			$this->add_style( 'cropper-css', get_template_directory_uri() . '/assets/css/cropper.min.css', [], ET_VERSION );
			// GG Font
			/*$this->add_style('gg-font', '//fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800', array(
            'bootstrap'
        ) , ET_VERSION);*/
			// Chosen
			// $this->add_style( 'chosen', get_template_directory_uri() . '/css/chosen.css', ET_VERSION );
			//datepicker
			$this->add_style( 'datepicker-css', get_template_directory_uri() . '/assets/css/bootstrap-datetimepicker.min.css', ET_VERSION );
			//iOS7 switch button
			/*  $this->add_style('switchery', get_template_directory_uri() . '/css/switchery.css', ET_VERSION);*/
			// Add style css for mobile version.
			// if (et_load_mobile()) {
			// $this->add_style('mobile-style', get_template_directory_uri() . '/mobile/css/custom.css' , ET_VERSION);
			// $this->add_style('mobile-sass-style', get_template_directory_uri() . '/mobile/css/style.css' , ET_VERSION);
			$this->add_style( 'scroll-bar', get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.css', ET_VERSION );
			//     return;
			// }
			// theme custom.css
			$this->add_style( 'main-style', get_template_directory_uri() . '/assets/css/styles.css', ET_VERSION );
			// theme custom.css
			$this->add_style( 'custom1', get_template_directory_uri() . '/css/custom.css', [], ET_VERSION );
			$this->add_style( 'scroll-bar', get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.css', ET_VERSION );
			$this->add_style( 'bb-codes', get_template_directory_uri() . '/assets/js/easy-bbcode-editor/jquery.editor.css', ET_VERSION );
			$this->add_style( 'dropzone', get_template_directory_uri() . '/assets/libs/dropzone/dropzone.min.css', ET_VERSION );
			$this->add_style( 'quill', get_template_directory_uri() . '/assets/libs/quill/quill.snow.css', ET_VERSION );
			$this->add_style( 'dmin', get_template_directory_uri() . '/assets/libs/dropzone/basic.min.css', ET_VERSION );
			// style.css
			$this->add_style( 'freelanceengine-style', get_stylesheet_uri(), ET_VERSION );
			// style.css
			// $this->add_style('style-theme', get_template_directory_uri() .'/css/style-theme.css' , array(
			//     'bootstrap'
			// ) , ET_VERSION);
			//         $this->add_style('ui-element-style', get_template_directory_uri() . '/css/ui-element.css', array(
			//             'bootstrap'
			//         ) , ET_VERSION);
		}

		/*
     * custom query prev query post
    */
		function pre_get_posts( $query ) {
			if ( is_post_type_archive( PROFILE ) ) {
				if ( ! $query->is_main_query() ) {
					return $query;
				}
			}
			if ( is_tax( 'project_category' ) || is_tax( 'project_type' ) ) {
				if ( $query->is_main_query() ) {
					$query->set( 'post_type', PROJECT );
					$query->set( 'post_status', 'publish' );
				}
			}
			//        if (is_tax('skill')) {
			//            if ($query->is_main_query()) {
			//                $query->set('post_type', PROJECT);
			//                $query->set('post_status', 'publish');
			//            }
			//        }
			if ( ( is_post_type_archive( PROJECT ) || is_tax( 'project_category' ) || is_tax( 'project_type' ) || is_tax( 'skill' ) ) && ! is_admin() ) {
				if ( ! $query->is_main_query() ) {
					return $query;
				}
				if ( current_user_can( 'manage_options' ) ) {
					$query->set( 'post_status', [
						'pending',
						'publish'
					] );
					//$query->set ('orderby', 'post_status');
				} else {
					$query->set( 'post_status', 'publish' );
				}
			}
			if ( is_author() && $query->is_main_query() ) {
				$query->set( 'post_status', [
					'publish',
					'close',
					'complete'
				] );
			}

			return $query;
		}

		/*
     * custom order when admin view page-archive-projects
    */
		function order_by_post_status( $orderby, $object ) {
			global $user_ID;
			// if ((is_post_type_archive(PROJECT) || is_tax('project_category') || is_tax('project_type') || is_tax('skill')) && !is_admin() && current_user_can('edit_others_posts')) {
			//     return self::order_by_post_pending($orderby, $object);
			// }
			if ( isset( $object->query_vars[ 'post_status' ] ) && is_array( $object->query_vars[ 'post_status' ] ) && isset( $object->query_vars[ 'author' ] ) && $user_ID == $object->query_vars[ 'author' ] ) {
				return self::order_by_post_pending( $orderby, $object );
			}

			return $orderby;
		}

		static function order_by_post_pending( $orderby, $object ) {
			global $wpdb;
			$orderby = " case {$wpdb->posts}.post_status
                            when 'disputing' then 0
                            when 'reject' then 1
                            when 'pending' then 2
                            when 'publish' then 3
                            when 'close' then 4
                            when 'complete' then 5
                            when 'draft' then 6
                            when 'archive' then 7
                            when 'disputed' then 8
                            end,
                        {$wpdb->posts}.post_date DESC";

			return $orderby;
		}

		function script_in_footer() {
			do_action( 'ae_before_render_script' );
			?>
            <script type="text/javascript" id="frontend_scripts">
                (function ($, Views, Models, AE) {
                    $(document).ready(function () {
                        var currentUser;
                        if ($('#user_id').length > 0) {
                            currentUser = new Models.User(JSON.parse($('#user_id').html()));
                            //currentUser.fetch();
                        } else {
                            currentUser = new Models.User();
                        }
                        // init view front
                        if (typeof Views.Front !== 'undefined') {
                            AE.App = new Views.Front({model: currentUser});
                        }
                        AE.App.user = currentUser;
                        // init view submit project
                        if (typeof Views.SubmitProject !== 'undefined' && $('#post-place').length > 0) {
                            AE.Submit_Project = new Views.SubmitProject({
                                el: '#post-place',
                                user_login: currentUser.get('id'),
                                free_plan_used: 0,
                                limit_free_plan: false,
                                step: 2
                            });
                        }
                        // init view edit project
                        if (typeof Views.EditProject !== 'undefined' && $('#edit_project').length > 0) {
                            AE.Edit_Project = new Views.EditProject({
                                el: '#edit_project'
                            });
                        }
                        if (typeof Views.SubmitBibPlan !== 'undefined' && $('#upgrade-account').length > 0) {
                            new Views.SubmitBibPlan({
                                el: "#upgrade-account",
                                user_login: currentUser.get('id'),
                            });
                        }
                        // init view search form
                        if (typeof Views.SearchForm !== 'undefined') {
                            AE.search = new Views.SearchForm({
                                el: '#search_container'
                            });
                        }
                        //create new auth view
                        if (typeof Views.Auth !== 'undefined') {
                            new Views.Auth({el: 'body'});
                        }
                        //create new user profile view
                        if (typeof Views.Profile !== 'undefined') {
                            new Views.Profile();
                        }
                        //create new single profile view
                        if (typeof Views.Single_Profile !== 'undefined') {
                            new Views.Single_Profile();
                        }
                    });
                })(jQuery, AE.Views, AE.Models, window.AE);
            </script>
			<?php
			do_action( 'ae_after_render_script' );
		}
	}

	global $et_freelance;
	add_action( 'after_setup_theme', 'et_setup_theme' );
	function et_setup_theme() {
		global $et_freelance;
		$et_freelance = new ET_FreelanceEngine();
		if ( is_admin() || current_user_can( 'manage_options' ) ) {
			new ET_Admin();
		}
	}

	// add_action('user_register ' , 'de_new_user_alert');
	// function de_new_user_alert($user_id) {
	//     // $display_name = get_the_author_meta( 'display_name', $user_id );
	//     // $email = get_the_author_meta( 'user_email', $user_id );
	//     wp_mail('admin email', 'new user register ', 'there is a new user register on your site with id' . $user_id );
	// }
	/**
	 * add custom status to wordpress post status
	 */
	function fre_append_post_status_list() {
		if ( ! isset( $_REQUEST[ 'post' ] ) ) {
			return;
		}
		$post      = get_post( $_REQUEST[ 'post' ] );
		$complete  = '';
		$closed    = '';
		$disputing = '';
		$disputed  = '';
		$label     = '';
		if ( $post && ( $post->post_type == BID || $post->post_type == PROJECT ) ) {
			if ( $post->post_status == 'complete' ) {
				$complete = " selected='selected'";
				$label    = '<span id="post-status-display">' . __( "Completed", ET_DOMAIN ) . '</span>';
			}
			if ( $post->post_status == 'close' ) {
				$closed = " selected='selected'";
				$label  = '<span id="post-status-display">' . __( "Close", ET_DOMAIN ) . '</span>';
			}
			if ( $post->post_status == 'disputing' ) {
				$disputing = " selected='selected'";
				$label     = '<span id="post-status-display">' . __( "Disputing", ET_DOMAIN ) . '</span>';
			}
			if ( $post->post_status == 'disputed' ) {
				$disputed = " selected='selected'";
				$label    = '<span id="post-status-display">' . __( "Disputed", ET_DOMAIN ) . '</span>';
			}
			?>
            <script>
                jQuery(document).ready(function ($) {
                    $("select#post_status").append("<option value='complete' <?php
						echo $complete; ?>>Completed</option><option value='close' <?php
						echo $closed; ?>>Close</option><option value='disputing' <?php
						echo $disputing; ?>>Disputing</option><option value='disputed' <?php
						echo $disputed; ?>>Disputed</option>");
                    $(".misc-pub-section label").append('<?php
						echo $label; ?>');
                });
            </script>
			<?php
		}
	}

	add_action( 'admin_footer-post.php', 'fre_append_post_status_list' );
	/**
	 * set default user roles for social login
	 *
	 * @author Tambh
	 */
	add_filter( 'ae_social_login_user_roles_default', 'fre_default_user_roles' );
	if ( ! function_exists( 'fre_default_user_roles' ) ) {
		function fre_default_user_roles( $default_role ) {
			return [
				FREELANCER => __( 'Professional', ET_DOMAIN ),
				EMPLOYER   => __( 'Client', ET_DOMAIN )
			];
		}
	}
	/**
	 * Replace Link Reply
	 *
	 * @author ThanhTu
	 */
	if ( ! function_exists( 'fre_comment_reply_link' ) ) {
		function fre_comment_reply_link( $string, $args, $comment ) {
			if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
				$string = '';
				$string = $args[ 'before' ];
				$string .= sprintf( '<a rel="nofollow" href="#" data-toggle="modal" class="comment-reply-login login-btn">%s</a>', $args[ 'login_text' ] );
				$string .= $args[ 'after' ];
			}

			return $string;
		}
	}
	add_filter( 'comment_reply_link', 'fre_comment_reply_link', 10, 3 );
	/**
	 * WP Link query only post, page, project, profile
	 *
	 * @author Tuandq
	 */
	function fre_wp_link_query_args( $query ) {
		if ( ! current_user_can( 'administrator' ) ) {
			$query[ 'post_type' ] = [ 'post', 'pages', 'project', 'fre_profile' ];
		}

		return $query;
	}

	add_filter( 'wp_link_query_args', 'fre_wp_link_query_args' );
	function posts_groupby_profile( $groupby, $query ) {
		global $wpdb;
		$query_vars = ( isset( $query->query_vars[ 'post_type' ] ) ) ? $query->query_vars : '';
		if ( isset( $query_vars[ 'post_type' ] ) && $query_vars[ 'post_type' ] == 'fre_profile' ) {
			$groupby = "{$wpdb->posts}.post_author";
		}

		return $groupby;
	}

	/**
	 * Update option page_on_front when FrE upgrade to version 1.8
	 * New Homepage
	 *
	 * @author ThanhTu
	 */
	function set_frontpage_theme() {
		$isSet = get_option( 'set_page_front' );
		if ( $isSet == 1 ) {
			return;
		}
		$page_on_front = get_option( 'page_on_front' );
		// List page
		$pages = get_pages( [
			'post_status' => 'publish',
			'meta_key'    => '_wp_page_template',
			'meta_value'  => 'page-home-new.php'
		] );
		if ( empty( $pages ) ) {
			$homepage_new = et_get_page_link( 'home-new' );
			$pages        = get_pages( [
				'post_status' => 'publish',
				'meta_key'    => '_wp_page_template',
				'meta_value'  => 'page-home-new.php'
			] );
			$page         = $pages[ 0 ];
			update_option( 'page_on_front', $page->ID );
			update_option( 'set_page_front', 1 );
		} else {
			foreach ( $pages as $key => $value ) {
				if ( $value->ID == $pages ) {
					return;
				}
			}
			$page = $pages[ 0 ];
			update_option( 'page_on_front', $page->ID );
			update_option( 'set_page_front', 1 );
		}
	}

	add_action( 'wp_before_admin_bar_render', 'set_frontpage_theme' );
	// update database for old version
	function fre_update_db() {
		global $post, $wpdb;
		// updated_bids -> ThanhTu
		$check_fre_updated_bids = ae_get_option( 'fre_updated_bids', false );
		if ( ! $check_fre_updated_bids ) {
			$credits_bid = (int) ae_get_option( 'ae_credit_number', 1 );
			$list_packs  = get_posts( [ 'post_type' => 'bid_plan' ] );
			foreach ( $list_packs as $key => $pack ) {
				$bid_old = get_post_meta( $pack->ID, 'et_number_posts', true );
				$bid_new = round( $bid_old / $credits_bid );
				update_post_meta( $pack->ID, 'et_number_posts', $bid_new, $bid_old );
			}
			$arg_user   = [
				'meta_key' => 'credit_number',
				'number'   => - 1
			];
			$list_users = get_users( $arg_user );
			foreach ( $list_users as $key => $user ) {
				$number_bid_old = get_user_meta( $user->ID, 'credit_number', true );
				$number_bid_new = round( $number_bid_old / $credits_bid );
				$meta_id        = $wpdb->get_var( $wpdb->prepare( "SELECT umeta_id FROM {$wpdb->usermeta} WHERE meta_key = 'credit_number' AND user_id = %s", $user->ID ) );
				$result         = $wpdb->update( $wpdb->usermeta, [
					'meta_value' => $number_bid_new
				], [
					'umeta_id' => $meta_id
				] );
			}
			ae_update_option( 'fre_updated_bids', 1 );
		}
		// updated bids plans -> ThanhTu
		$check_fre_updated_bid_accept = ae_get_option( 'fre_updated_bid_accept', false );
		if ( ! $check_fre_updated_bid_accept ) {
			// List projects have post_status = Close
			$query_project = "SELECT pm.post_id
                        FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm
                        ON p.ID = pm.post_id
                        WHERE p.post_type = 'project'
                            AND p.post_status = 'close'
                            AND pm.meta_key = 'accepted'
                            AND pm.meta_value <> '' ";
			$projects      = $wpdb->get_col( $query_project );
			// List bid had Unacceptable
			if ( ! empty( $projects ) ) {
				$sql_bid_unaccept = "SELECT p.ID
                        FROM {$wpdb->posts} p
                        WHERE p.post_type = 'bid'
                            AND p.post_status = 'publish'
                            AND p.post_parent IN (" . implode( ',', $projects ) . ")
                         ORDER BY p.ID DESC";
				$listBids         = $wpdb->get_col( $sql_bid_unaccept );
				// update post_status 'publish' to 'unaccept'
				if ( ! empty( $listBids ) ) {
					foreach ( $listBids as $key => $value ) {
						$result = $wpdb->update( $wpdb->posts, [ 'post_status' => 'unaccept' ], [ 'ID' => $value ] );
					}
				}
			}
			ae_update_option( 'fre_updated_bid_accept', 1 );
		}
		// update db from 1.8.2
		$update_check_182 = ae_get_option( 'update_db_for_182', false );
		if ( ! ( $update_check_182 ) ) {
			//Update new status for bid -> Quoc
			$projects_postquery = new WP_Query( [
				'post_type'        => PROJECT,
				'post_status'      => [
					'draft',
					'pending',
					'publish',
					'close',
					'archive',
					'complete',
					'reject',
					'disputed',
					'disputing',
					'trash'
				],
				'suppress_filters' => true,
				'posts_per_page'   => - 1
			] );
			if ( $projects_postquery->have_posts() ) {
				while ( $projects_postquery->have_posts() ) {
					$projects_postquery->the_post();
					$project_status = $post->post_status;
					$project_title  = $post->post_title;
					$bid_accepted   = get_post_meta( $post->ID, 'accepted', true );
					$child_args     = [
						'post_parent' => $post->ID,
						'post_type'   => BID,
						'numberposts' => - 1,
						'post_status' => 'any'
					];
					$children       = get_children( $child_args );
					if ( $project_status == 'archive' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							wp_update_post( [
								'ID'          => $child->ID,
								'post_title'  => $project_title,
								'post_status' => 'archive'
							] );
						}
					} else if ( $project_status == 'trash' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							wp_update_post( [
								'ID'          => $child->ID,
								'post_title'  => $project_title,
								'post_status' => 'hide'
							] );
						}
					} else if ( $project_status == 'disputing' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							$args_data = [
								'ID'         => $child->ID,
								'post_title' => $project_title
							];
							if ( $child->ID == $bid_accepted ) {
								$args_data[ 'post_status' ] = 'disputing';
							} else {
								$args_data[ 'post_status' ] = 'hide';
							}
							wp_update_post( $args_data );
						}
					} else if ( $project_status == 'disputed' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							$args_data = [
								'ID'         => $child->ID,
								'post_title' => $project_title
							];
							if ( $child->ID == $bid_accepted ) {
								$args_data[ 'post_status' ] = 'disputed';
							} else {
								$args_data[ 'post_status' ] = 'hide';
							}
							wp_update_post( $args_data );
						}
					} else if ( $project_status == 'complete' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							$args_data = [
								'ID'         => $child->ID,
								'post_title' => $project_title,
							];
							if ( $child->ID == $bid_accepted ) {
								$args_data[ 'post_status' ] = 'complete';
							} else {
								$args_data[ 'post_status' ] = 'hide';
							}
							wp_update_post( $args_data );
						}
					} else if ( $project_status == 'publish' && ! empty( $children ) ) {
						foreach ( $children as $child ) {
							wp_update_post( [
								'ID'         => $child->ID,
								'post_title' => $project_title,
							] );
						}
					}
				}
			}
			//Get all bid accepted -> Quoc
			$accepted_bids = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'accepted' AND meta_value != ''" );
			if ( ! empty( $accepted_bids ) ) {
				foreach ( $accepted_bids as $key => $value ) {
					$post_name          = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE id = $value->post_id" );
					$post_name_accepted = 'bid-on-project-' . $post_name . '-was-accepted';
					$date_accepted      = $wpdb->get_var( "SELECT post_date FROM $wpdb->posts WHERE post_name = '" . $post_name_accepted . "'" );
					$bid_time           = get_post_meta( $value->meta_value, 'bid_time', true );
					$bit_type_time      = get_post_meta( $value->meta_value, 'type_time', true );
					$date               = new DateTime( $date_accepted );
					if ( $bit_type_time == 'day' ) {
						$date->modify( '+' . $bid_time . ' days' );
					} else if ( $bit_type_time == 'week' ) {
						$date->modify( '+' . $bid_time . ' weeks' );
					}
					$deadline_time = $date->format( 'Y-m-d g:i:s' );
					update_post_meta( $value->post_id, 'project_deadline', $deadline_time );
				}
			}
			//Update project worked freelancer -> SyDao
			$list_profile = get_posts( [
				'post_type'      => 'fre_profile',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			] );
			if ( ! empty( $list_profile ) ) {
				foreach ( $list_profile as $p ) {
					$profile_id           = $p->ID;
					$works                = get_posts( [
						'post_status'    => [ 'complete' ],
						'post_type'      => BID,
						'author'         => $p->post_author,
						'posts_per_page' => - 1,
					] );
					$total_project_worked = count( $works );
					update_post_meta( $profile_id, 'total_projects_worked', $total_project_worked );
				}
			}
			ae_update_option( 'update_db_for_182', 1 );
		}
	}

	add_action( 'wp_loaded', 'fre_update_db' );
	function notice_for_update_db() {
		//check theme new active or update
		?>
        <style type="text/css">
            .et-updated {
                background-color: lightYellow;
                border: 1px solid #E6DB55;
                border-radius: 3px;
                webkit-border-radius: 3px;
                moz-border-radius: 3px;
                margin: 20px 15px 0 0;
                padding: 0 10px;
                position: relative;
            }
        </style>
		<?php
		$update_db_for_182        = ae_get_option( 'update_db_for_182' );
		$notice_update_db_for_182 = get_option( 'notice_update_db_for_182' );
		if ( ( $update_db_for_182 ) && ! $notice_update_db_for_182 ) {
			?>
            <div id="notice_update_db_for_182" class="et-updated">
                <p>
					<?php
						$msg = sprintf( __( "Your database is automatically updated. <a href='%s' target='_blank'>Click here</a> for more details.  <a href='%s' style='text-decoration: none'  class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></a>", ET_DOMAIN ), 'https://www.enginethemes.com/update-freelanceengine-1-8-2/', add_query_arg( 'notice_update_db_for_182', '1' ) );
						echo $msg;
					?>
                </p>
            </div>
			<?php
		}
	}

	add_action( 'admin_notices', 'notice_for_update_db' );
	add_action( 'show_user_profile', 'add_extra_social_links' );
	add_action( 'edit_user_profile', 'add_extra_social_links' );
	function add_extra_social_links( $user ) {
		?>
        <h3>Адрес</h3>
        <input type="text" name="country" value="<?php echo esc_attr( get_the_author_meta( 'country', $user->ID ) ); ?>"
               class="regular-text"/>
        <input type="text" name="state" value="<?php echo esc_attr( get_the_author_meta( 'state', $user->ID ) ); ?>"
               class="regular-text"/>
        <input type="text" name="city" value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>"
               class="regular-text"/>
		<?php
	}

	add_action( 'user_register', 'my_user_registration' );
	function my_user_registration( $user_id ) {
		// for company
		$is_type_company = ( ! empty( $_REQUEST[ 'type_prof' ] ) && $_REQUEST[ 'type_prof' ] == COMPANY ) ? true : false;
		$company_name    = $_REQUEST[ 'company_name' ];
		if ( $is_type_company ) {
			add_user_meta( $user_id, 'is_company', 1 );
			if ( ! empty( $company_name ) ) {
				wp_update_user( [ 'ID' => $user_id, 'display_name' => $company_name ] );
			}
		}
		// for company
		update_user_meta( $user_id, 'country', $_POST[ 'country' ] );
		update_user_meta( $user_id, 'state', $_POST[ 'state' ] );
		update_user_meta( $user_id, 'city', $_POST[ 'city' ] );
		//new start for register email
		$user = new WP_User( $user_id );
		update_user_meta( $user_id, 'register_status', 'unconfirm' );
		update_user_meta( $user_id, 'key_confirm', md5( $user->user_email ) );
		//new end
	}

	/**
	 * ============== The custom functional
	 */
	/**
	 * @param $data [type=array]
	 *
	 * @return string|null
	 */
	function baskserg_profile_categories( $data ) {
		$cp_categories = null;
		foreach ( $data as $item ) {
			$cp_categories .= $item->name . ', ';
		}
		$cp_categories = mb_substr( $cp_categories, 0, - 2 );

		return $cp_categories;
	}

	function baskserg_profile_categories2( $data ) {
		$cp_categories = null;
		$coldata       = count( $data );
		for ( $i = 0; $i < $coldata; $i ++ ) {
			$b = $i + 1;
			if ( $b == 1 ) {
				$cp_categories .= '<li><a href="' . get_bloginfo( 'url' ) . '/' . $data[ $i ]->slug . '">' . $data[ $i ]->name . '</a><span>' . $b . '-st</span></li>';
			}
			if ( $b == 2 ) {
				$cp_categories .= '<li><a href="' . get_bloginfo( 'url' ) . '/' . $data[ $i ]->slug . '">' . $data[ $i ]->name . '</a><span>' . $b . '-nd</span></li>';
			}
			if ( $b == 3 ) {
				$cp_categories .= '<li><a href="' . get_bloginfo( 'url' ) . '/' . $data[ $i ]->slug . '">' . $data[ $i ]->name . '</a><span>' . $b . '-d</span></li>';
			}
		}
		$cp_categories = '<ul>' . mb_substr( $cp_categories, 0 ) . '</ul>';

		return $cp_categories;
	}

	function baskserg_profile_categories3( $data ) {
		$cp_categories = null;
		foreach ( $data as $item ) {
			$parentname    = get_term( $item->parent );
			$cp_categories .= '<li>' . $parentname->name . ' / <a href="' . get_bloginfo( 'url' ) . '/' . $item->slug . '">' . $item->name . '</a></li>';
		}
		$cp_categories = '<ul>' . mb_substr( $cp_categories, 0 ) . '</ul>';

		return $cp_categories;
	}

	function baskserg_profile_categories4( $data ) {
		$cp_categories = null;
		foreach ( $data as $item ) {
			$cp_categories .= '<li><a href="' . get_bloginfo( 'url' ) . '/profile_category/' . $item->slug . '">' . $item->name . '</a></li>';
		}
		$cp_categories = '<ul>' . mb_substr( $cp_categories, 0, - 2 ) . '</ul>';

		return $cp_categories;
	}

	add_action( 'wp_ajax_pre_project_cat', 'dataPrepareProjectCategory' );
	add_action( 'wp_ajax_prof_proj_cat', 'saveProjectCategory_inProfile' );
	function dataPrepareProjectCategory() {
		$terms    = get_terms( [ 'taxonomy' => 'project_category', 'hide_empty' => 0 ] );
		$parents  = [];
		$children = [];
		foreach ( $terms as $term ) {
			if ( $term->parent > 0 ) {
				$children[ $term->parent ][] = [
					'id'   => $term->term_id,
					'text' => str_replace( '&amp;', '&', $term->name ),
				];
			} else {
				$parents[] = [
					'id'   => $term->term_id,
					'text' => str_replace( '&amp;', '&', $term->name ),
				];
			}
		}
		$result[ 'data' ]    = [
			'parents'  => $parents,
			'children' => $children,
		];
		$result[ 'success' ] = true;
		wp_send_json( $result );
	}

	function saveProjectCategory_inProfile() {
		global $user_ID, $wpdb;
		$profileId           = (int) get_user_meta( $user_ID, 'user_profile_id', true );
		$result[ 'success' ] = false;
		$result[ 'msg' ]     = __( 'Something went wrong!' );
		$selected            = $_POST[ 'selected' ];
		if ( ! empty( $selected ) && ! empty( $profileId ) ) {
			$result[ '$profileId' ] = $profileId;
			$result[ 'selected' ]   = $_POST[ 'selected' ];
			$ids                    = [];
			foreach ( $selected as $item ) {
				$id = (int) $item;
				if ( $id > 0 ) {
					$ids[] = $id;
				}
			}
			if ( ! empty( $ids ) ) {
				$arr = $wpdb->get_results( "SELECT term_id FROM {$wpdb->terms} WHERE term_id IN (" . implode( ',', $ids ) . ")", ARRAY_A );
				if ( ! empty( $arr ) ) {
					$wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE object_id = {$profileId}" );
					foreach ( $arr as $row ) {
						$ins[ 'object_id' ]        = $profileId;
						$ins[ 'term_taxonomy_id' ] = $row[ 'term_id' ];
						$wpdb->insert( $wpdb->term_relationships, $ins );
					}
					$result[ 'success' ] = true;
					$result[ 'msg' ]     = __( 'Saved successfully!' );
				}
			}
			wp_send_json( $result );
		}
		wp_send_json( $result );
	}

	add_action( 'wp_ajax_doc_create', 'doc_create' );
	function doc_create() {
		$max_image = 5;
		if ( empty( $_POST[ "profile_id" ] ) && ( empty( $_FILES ) || empty( $_POST[ 'delete_file' ] ) ) ) {
			echo false;
			exit();
		}
		$prof_id       = $_POST[ "profile_id" ];
		$post_type     = get_post_type( $prof_id );
		$document_list = get_post_meta( $prof_id, 'document_list', true );
		if ( ! empty( $document_list ) ) {
			$documents        = is_numeric( $document_list ) ? [ $document_list ] : explode( ', ', $document_list );
			$countAttachments = count( $documents );
		} else {
			$documents        = [];
			$countAttachments = null;
		}
		$delete_file = ! empty( $_POST[ 'delete_file' ] ) ? (int) $_POST[ 'delete_file' ] : null;
		if ( ! empty( $delete_file ) ) {
			if ( ! empty( $post_type ) && $post_type == 'fre_profile' && $countAttachments !== null ) {
				wp_delete_attachment( $delete_file );
			}
		}
		//    $max_image = $post_type == 'fre_profile' ? !empty($delete_file) ? count($delete_file) + 1 : 1 : $max_image;
		add_filter( 'upload_dir', function( $data ) {
			$prof_id          = (int) $_POST[ "profile_id" ];
			$data[ 'url' ]    = $data[ 'url' ] . "/post/{$prof_id}";
			$data[ 'path' ]   = $data[ 'path' ] . "/post/{$prof_id}";
			$data[ 'subdir' ] = $data[ 'subdir' ] . "/post/{$prof_id}";

			return $data;
		} );
		$prepare_attach = [];
		if ( ! empty( $_FILES ) ) {
			foreach ( $_FILES[ 'files' ][ 'error' ] as $ind => $error ) {
				if ( $error == 0 && $_FILES[ 'files' ][ 'size' ][ $ind ] <= 5767168 ) {
					if ( isset( $_POST[ 'freelancer_post' ] ) || ( $countAttachments < $max_image ) ) {
						$prepare_attach[ 'error' ]    = $error;
						$prepare_attach[ 'name' ]     = $_FILES[ 'files' ][ 'name' ][ $ind ];
						$prepare_attach[ 'size' ]     = $_FILES[ 'files' ][ 'size' ][ $ind ];
						$prepare_attach[ 'tmp_name' ] = $_FILES[ 'files' ][ 'tmp_name' ][ $ind ];
						$prepare_attach[ 'type' ]     = $_FILES[ 'files' ][ 'type' ][ $ind ];
						$attach_id                    = attach_advert_file( $prepare_attach, $prof_id, [
							'jpg|jpeg' => 'image/jpeg',
							'png'      => 'image/png',
							'pdf'      => 'application/pdf',
							'doc'      => 'application/msword',
							'xls'      => 'application/vnd.ms-excel',
							'docx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
							'xlsx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						] );
						$prepare_attach               = [];
						if ( $attach_id ) {
							$countAttachments ++;
							$documents[] = $attach_id;
						}
					}
				}
			}
		}
		if ( ! empty( $attach_id ) ) {
			$document_list = implode( ', ', $documents );
			update_post_meta( $prof_id, 'document_list', $document_list );
			wp_send_json( [
				'success'  => true,
				'msg'      => _( 'Document has been updated successfully' ),
				'filepath' => wp_get_attachment_url( $attach_id ),
				'file_id'  => $attach_id
			] );
		} elseif ( ! empty( $delete_file ) ) {
			$delete_file_id = array_search( $delete_file, $documents );
			unset( $documents[ $delete_file_id ] );
			if ( count( $documents ) == 0 ) {
				delete_post_meta( $prof_id, 'document_list' );
			} else {
				$document_list = implode( ', ', $documents );
				update_post_meta( $prof_id, 'document_list', $document_list );
			}
			wp_send_json( [
				'success' => true,
				'msg'     => _( 'Document has been removed' ),
			] );
		} else {
			wp_send_json( [
				'success' => false,
				'msg'     => _( 'Limit is exceeded!' ),
			] );
		}
	}

	/**
	 * Get user location on register page
	 */
	function user_location() {
		global $wpdb;
		if ( ! empty( $_POST[ "country_id" ] ) ) {
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `name` FROM {$wpdb->prefix}location_states WHERE `country_id` = %d ORDER BY `name`", $_POST[ "country_id" ] ), OBJECT );
			if ( $results ) {
				$state_list = '<option value="">Select state</option>';
				foreach ( $results as $result ) {
					if ( ! empty( $_POST[ 'state_id' ] ) ) {
						if ( $result->id == $_POST[ 'state_id' ] ) {
							$state_list .= "<option value=' $result->id' selected>$result->name</option>";
						} else {
							$state_list .= "<option value=' $result->id'>$result->name</option>";
						}
					} else {
						$state_list .= "<option value=' $result->id'>$result->name</option>";
					}
				}
			} else {
				$state_list = '<option value="">State not available</option>';
			}
			if ( empty( $_POST[ "state_id" ] ) ) {
				echo $state_list;
			} else {
				$list[ 'state' ] = $state_list;
			}
		}
		if ( ! empty( $_POST[ "state_id" ] ) ) {
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `name` FROM {$wpdb->prefix}location_cities WHERE `state_id` = %d ORDER BY `name`", $_POST[ "state_id" ] ), OBJECT );
			if ( $results ) {
				$city_list = '<option value="">Select city</option>';
				foreach ( $results as $result ) {
					if ( ! empty( $_POST[ 'city_id' ] ) ) {
						if ( $result->id == $_POST[ 'city_id' ] ) {
							$city_list .= "<option value=' $result->id' selected>$result->name</option>";
						} else {
							$city_list .= "<option value=' $result->id'>$result->name</option>";
						}
					} else {
						$city_list .= "<option value=' $result->id'>$result->name</option>";
					}
				}
			} else {
				$city_list = '<option value="">Cities not available</option>';
			}
			if ( empty( $list ) ) {
				echo $city_list;
			} else {
				$list[ 'city' ] = $city_list;
			}
		}
		if ( ! empty( $list ) ) {
			echo json_encode( $list );
		}
		wp_die();
	}

	add_action( 'wp_ajax_nopriv_user_location', 'user_location' ); //для регистрации
	add_action( 'wp_ajax_user_location', 'user_location' );
	function get_sub_cat() {
		if ( empty( $_POST ) || empty( $cat_slug = $_POST[ "cat_slug" ] ) ) {
			echo '';
			exit;
		}
		$type_filter = empty( $_POST[ "type_filter" ] ) ? 'project_category' : $_POST[ "type_filter" ];
		$sub_slug    = empty( $_POST[ "sub_slug" ] ) ?: explode( ',', $_POST[ "sub_slug" ] );
		$type_value  = ! empty( $_POST[ "crete_project" ] ) ? 'id' : 'slug';
		// 20092020 choice
		if ( $type_filter === 'project_category' ) {
			$type_value = 'slug';
		}
		if ( ! empty( $_POST[ 'step_valid' ] ) && $_POST[ 'step_valid' ] == '1' ) {
			$type_value = 'id';
		}
		$res = get_term_by( 'slug', $cat_slug, $type_filter, ARRAY_A );
		if ( ! empty( $res[ "term_id" ] ) ) {
			ae_tax_dropdown( $type_filter, [
				'attr'            => '',
				//'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __( "Select sub1", ET_DOMAIN ) . '"',
				'show_option_all' => __( "Select subcategory", ET_DOMAIN ),
				'class'           => '',
				//'fre-chosen-single',
				'hide_empty'      => false,
				'hierarchical'    => false,
				'selected'        => $sub_slug,
				'id'              => 'sub',
				'parent'          => $res[ "term_id" ],
				'name'            => 'sub',
				'value'           => $type_value
			] );
		}
		exit;
	}

	add_action( 'wp_ajax_nopriv_get_sub_cat', 'get_sub_cat' ); //не авторизирован
	add_action( 'wp_ajax_get_sub_cat', 'get_sub_cat' ); // авторизирован
	function share_banner_to_email() {
		if ( empty( $_POST ) || empty( $img_src = $_POST[ "img_src" ] ) ) {
			echo '';
		}
		global $current_user;
		if ( ! empty( $emails = $_POST[ 'emails' ] ) ) {
			$ae_users  = AE_Users::get_instance();
			$user_data = $ae_users->convert( $current_user->data );
			$ref_link  = $user_data->author_url;
			$message   = '<a href="' . $ref_link . '">Click to view profile</a>';
			$subject   = sprintf( __( '[%s] Share banner', ET_DOMAIN ), wp_specialchars_decode( get_option( 'blogname' ) ) );
			//    имя в заголовке письма
			add_filter( 'wp_mail_from_name', function( $from_name ) {
				return 'MasterHand Pro';
			} );
			add_filter( 'wp_mail_content_type', 'set_html_content_type' );
			$res_send = wp_mail( $emails, $subject, $message, '', [ $img_src ] );
			// Сбросим content-type, чтобы избежать возможного конфликта
			remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
			if ( ! empty( $res_send ) ) {
				echo json_encode( [ 'status' => 'success', 'msg' => 'Message has been sent' ] );
			}
		} else {
			echo $mes = [ 'status' => 'error', 'msg' => 'no emails' ];
		}
		exit;
	}

	add_action( 'wp_ajax_share_banner_to_email', 'share_banner_to_email' );
	function set_html_content_type() {
		return 'text/html';
	}

	add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
	function onMailError( $wp_error ) {
		echo json_encode( [ 'msg' => $wp_error->errors[ 'wp_mail_failed' ] ] );
	}

	function print_filters_for( $hook = '' ) {
		global $wp_filter;
		if ( empty( $hook ) || ! isset( $wp_filter[ $hook ] ) ) {
			return;
		}
		print '<pre>';
		print_r( $wp_filter[ $hook ] );
		print '</pre>';
	}

	// Get User Location Profile Page
	function getLocation( $id = 0, $location_id = [ 'country' => '', 'state' => '', 'city' => '' ] ) {
		global $wpdb;
		//var_dump($location_id);
		$location = [];
		if ( ! empty( $id ) ) {
			$country_id = (int) get_user_meta( $id, 'country', true );
			$state_id   = (int) get_user_meta( $id, 'state', true );
			$city_id    = (int) get_user_meta( $id, 'city', true );
		} else {
			$country_id = $location_id[ 'country' ];
			$state_id   = $location_id[ 'state' ];
			$city_id    = $location_id[ 'city' ];
		}
		if ( $country_id != null ) {
			$result = $wpdb->get_row( "SELECT `id`, `name` FROM {$wpdb->prefix}location_countries WHERE id={$country_id}" );
			if ( empty( $result ) ) {
				$location[ 'country' ] = '';
			} else {
				$location[ 'country' ][ 'id' ]   = $result->id;
				$location[ 'country' ][ 'name' ] = $result->name;
			}
		} else {
			$location[ 'country' ] = '';
		}
		if ( $state_id != null && $location [ 'country' ] != null ) {
			$result = $wpdb->get_row( "SELECT `id`, `name` FROM {$wpdb->prefix}location_states WHERE id={$state_id}" );
			if ( empty( $result ) ) {
				$location[ 'state' ] = '';
			} else {
				$location[ 'state' ][ 'id' ]   = $result->id;
				$location[ 'state' ][ 'name' ] = $result->name;
			}
		} else {
			$location[ 'state' ] = '';
		}
		if ( ! empty( $city_id ) && $city_id != null && $location [ 'country' ] != null ) {
			$result = $wpdb->get_row( "SELECT `id`, `name` FROM {$wpdb->prefix}location_cities WHERE id={$city_id}" );
			if ( empty( $result ) ) {
				$location[ 'city' ] = '';
			} else {
				$location[ 'city' ][ 'id' ]   = $result->id;
				$location[ 'city' ][ 'name' ] = $result->name;
			}
		} else {
			$location[ 'city' ] = '';
		}

		return $location;
	}

	// добавляет ссылку на pro в профиле
	function get_status_in_profile( $userId ) {
		global $wpdb;
		$result      = null;
		$user_status = get_user_pro_status( $userId );
		if ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER ) {
			$q = $wpdb->get_results( $wpdb->prepare( "
                    SELECT ppu.activation_date, ppu.expired_date, ps.status_name
                    FROM {$wpdb->prefix}users as u
                    LEFT JOIN  {$wpdb->prefix}pro_paid_users as ppu ON u.id = ppu.user_id
                    LEFT JOIN {$wpdb->prefix}pro_status as ps ON ppu.status_id = ps.id
                    WHERE u.id = %d
                    GROUP BY u.id;
		        ", $userId ) );
			foreach ( $q as $item ) {
				$date = date_create_from_format( 'Y-m-d H:i:s', $item->expired_date );
				//$result .= "<span class='fre-view-as-others fre-pro-status'>Your account - " . $item->status_name . "</span>";
				//$result .= "<span class='fre-view-as-others fre-pro-date'>Active by - " . date_format($date, 'Y-m-d') . "</span>";
				$result .= "<a href='/pro' class='fre-status'>Change Account Pro</a>";
			}
		} else {
			$result = "<a href='/pro' class='fre-view-as-others fre-pro' style='top:80px;'>Activate Account Pro</a>";
		}

		return $result;
	}

	static $idsProUsers = [];
	function userHaveProStatus( $userId = 0 ) {
		global $idsProUsers;
		if ( isset( $idsProUsers[ $userId ] ) ) {
			return $idsProUsers[ $userId ];
		} else {
			$user_status            = get_user_pro_status( $userId );
			$status                 = ( $user_status && $user_status != PRO_BASIC_STATUS_EMPLOYER && $user_status != PRO_BASIC_STATUS_FREELANCER );
			$idsProUsers[ $userId ] = $status;

			return $status;
		}
	}

	//    new2
	//если не авторизован или базовый статус
	//для первичной фильтрации
	if ( function_exists( 'set_function_for_add_pro_status' ) ) {
		//    add_action('send_headers', 'set_function_for_add_pro_status');
	}
	//add_filter('ae_fetch_project_args', 'ae_fetch_fre_profile_args_ct');
	//    new2
	function baskserg_scripts_method() {
		/* wp_enqueue_script(
         'custom-scripts',
         get_stylesheet_directory_uri() . '/js/custom-scripts.js',
         array('jquery')
     );*/
		wp_enqueue_script( 'jscroll', get_stylesheet_directory_uri() . '/js/jquery.jscrollpane.js', [ 'jquery' ] );
	}

	add_action( 'wp_enqueue_scripts', 'baskserg_scripts_method' );
	add_image_size( 'blogpost', 360, 360, true );
	function get_views( $display = true, $prefix = '', $postfix = '' ) {
		$post_views = intval( get_user_meta( 'views' ) );
		$output     = $prefix . $post_views . $postfix;
		if ( $display ) {
			echo $output;
		} else {
			return $output;
		}
	}

	/*
 * "Хлебные крошки" для WordPress
 * автор: Dimox
 * версия: 2018.10.05
 * лицензия: MIT
*/
	function dimox_breadcrumbs() {
		/* === ОПЦИИ === */
		$text[ 'home' ]     = 'Homepage'; // текст ссылки "Главная"
		$text[ 'category' ] = '%s'; // текст для страницы рубрики
		$text[ 'search' ]   = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
		$text[ 'tag' ]      = 'Записи с тегом "%s"'; // текст для страницы тега
		$text[ 'author' ]   = 'Статьи автора %s'; // текст для страницы автора
		$text[ '404' ]      = 'Ошибка 404'; // текст для страницы 404
		$text[ 'page' ]     = 'Страница %s'; // текст 'Страница N'
		$text[ 'cpage' ]    = 'Страница комментариев %s'; // текст 'Страница комментариев N'
		$wrap_before        = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // открывающий тег обертки
		$wrap_after         = '</div><!-- .breadcrumbs -->'; // закрывающий тег обертки
		$sep                = '<span class="breadcrumbs__separator"> › </span>'; // разделитель между "крошками"
		$before             = '<span class="breadcrumbs__current">'; // тег перед текущей "крошкой"
		$after              = '</span>'; // тег после текущей "крошки"
		$show_on_home       = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
		$show_home_link     = 1; // 1 - показывать ссылку "Главная", 0 - не показывать
		$show_current       = 1; // 1 - показывать название текущей страницы, 0 - не показывать
		$show_last_sep      = 1; // 1 - показывать последний разделитель, когда название текущей страницы не отображается, 0 - не показывать
		/* === КОНЕЦ ОПЦИЙ === */
		global $post;
		$home_url  = home_url( '/' );
		$link      = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$link      .= '<a class="breadcrumbs__link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>';
		$link      .= '<meta itemprop="position" content="%3$s" />';
		$link      .= '</span>';
		$parent_id = ( $post ) ? $post->post_parent : '';
		$home_link = sprintf( $link, $home_url, $text[ 'home' ], 1 );
		if ( is_home() || is_front_page() ) {
			if ( $show_on_home ) {
				echo $wrap_before . $home_link . $wrap_after;
			}
		} else {
			$position = 0;
			echo $wrap_before;
			if ( $show_home_link ) {
				$position += 1;
				echo $home_link;
			}
			if ( is_category() ) {
				$parents = get_ancestors( get_query_var( 'cat' ), 'category' );
				foreach ( array_reverse( $parents ) as $cat ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				}
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					$cat      = get_query_var( 'cat' );
					echo $sep . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_current ) {
						if ( $position >= 1 ) {
							echo $sep;
						}
						echo $before . sprintf( $text[ 'category' ], single_cat_title( '', false ) ) . $after;
					} elseif ( $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_search() ) {
				if ( $show_home_link && $show_current || ! $show_current && $show_last_sep ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . sprintf( $text[ 'search' ], get_search_query() ) . $after;
				}
			} elseif ( is_year() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . get_the_time( 'Y' ) . $after;
				} elseif ( $show_home_link && $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_month() ) {
				if ( $show_home_link ) {
					echo $sep;
				}
				$position += 1;
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ), $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_time( 'F' ) . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_day() ) {
				if ( $show_home_link ) {
					echo $sep;
				}
				$position += 1;
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ), $position ) . $sep;
				$position += 1;
				echo sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ), $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_time( 'd' ) . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_single() && ! is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$position  += 1;
					$post_type = get_post_type_object( get_post_type() );
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->labels->name, $position );
					if ( $show_current ) {
						echo $sep . $before . get_the_title() . $after;
					} elseif ( $show_last_sep ) {
						echo $sep;
					}
				} else {
					$cat       = get_the_category();
					$catID     = $cat[ 0 ]->cat_ID;
					$parents   = get_ancestors( $catID, 'category' );
					$parents   = array_reverse( $parents );
					$parents[] = $catID;
					foreach ( $parents as $cat ) {
						$position += 1;
						if ( $position > 1 ) {
							echo $sep;
						}
						echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
					}
					if ( get_query_var( 'cpage' ) ) {
						$position += 1;
						echo $sep . sprintf( $link, get_permalink(), get_the_title(), $position );
						echo $sep . $before . sprintf( $text[ 'cpage' ], get_query_var( 'cpage' ) ) . $after;
					} else {
						if ( $show_current ) {
							echo $sep . $before . get_the_title() . $after;
						} elseif ( $show_last_sep ) {
							echo $sep;
						}
					}
				}
			} elseif ( is_post_type_archive() ) {
				$post_type = get_post_type_object( get_post_type() );
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->label, $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . $post_type->label . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_attachment() ) {
				$parent    = get_post( $parent_id );
				$cat       = get_the_category( $parent->ID );
				$catID     = $cat[ 0 ]->cat_ID;
				$parents   = get_ancestors( $catID, 'category' );
				$parents   = array_reverse( $parents );
				$parents[] = $catID;
				foreach ( $parents as $cat ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				}
				$position += 1;
				echo $sep . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_title() . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_page() && ! $parent_id ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . get_the_title() . $after;
				} elseif ( $show_home_link && $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_page() && $parent_id ) {
				$parents = get_post_ancestors( get_the_ID() );
				foreach ( array_reverse( $parents ) as $pageID ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position );
				}
				if ( $show_current ) {
					echo $sep . $before . get_the_title() . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_tag() ) {
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					$tagID    = get_query_var( 'tag_id' );
					echo $sep . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . sprintf( $text[ 'tag' ], single_tag_title( '', false ) ) . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_author() ) {
				$author = get_userdata( get_query_var( 'author' ) );
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					echo $sep . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text[ 'author' ], $author->display_name ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . sprintf( $text[ 'author' ], $author->display_name ) . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_404() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . $text[ '404' ] . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( has_post_format() && ! is_singular() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				echo get_post_format_string( get_post_format() );
			}
			echo $wrap_after;
		}
	} // end of dimox_breadcrumbs()
	function blog_breadcrumbs() {
		/* === ОПЦИИ === */
		$text[ 'home' ]     = 'Homepage'; // текст ссылки "Главная"
		$text[ 'category' ] = '%s'; // текст для страницы рубрики
		$text[ 'search' ]   = 'Результаты поиска по запросу "%s"'; // текст для страницы с результатами поиска
		$text[ 'tag' ]      = 'Записи с тегом "%s"'; // текст для страницы тега
		$text[ 'author' ]   = 'Статьи автора %s'; // текст для страницы автора
		$text[ '404' ]      = 'Ошибка 404'; // текст для страницы 404
		$text[ 'page' ]     = 'Страница %s'; // текст 'Страница N'
		$text[ 'cpage' ]    = 'Страница комментариев %s'; // текст 'Страница комментариев N'
		$wrap_before        = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // открывающий тег обертки
		$wrap_after         = '</div><!-- .breadcrumbs -->'; // закрывающий тег обертки
		$sep                = '<span class="breadcrumbs__separator"> › </span>'; // разделитель между "крошками"
		$before             = '<span class="breadcrumbs__current">'; // тег перед текущей "крошкой"
		$after              = '</span>'; // тег после текущей "крошки"
		$show_on_home       = 0; // 1 - показывать "хлебные крошки" на главной странице, 0 - не показывать
		$show_home_link     = 0; // 1 - показывать ссылку "Главная", 0 - не показывать
		$show_current       = 1; // 1 - показывать название текущей страницы, 0 - не показывать
		$show_last_sep      = 1; // 1 - показывать последний разделитель, когда название текущей страницы не отображается, 0 - не показывать
		/* === КОНЕЦ ОПЦИЙ === */
		global $post;
		$home_url  = home_url( '/' );
		$link      = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$link      .= '<a class="breadcrumbs__link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>';
		$link      .= '<meta itemprop="position" content="%3$s" />';
		$link      .= '</span>';
		$parent_id = ( $post ) ? $post->post_parent : '';
		$home_link = sprintf( $link, $home_url, $text[ 'home' ], 1 );
		if ( is_home() || is_front_page() ) {
			if ( $show_on_home ) {
				echo $wrap_before . $home_link . $wrap_after;
			}
		} else {
			$position = 0;
			echo $wrap_before;
			if ( $show_home_link ) {
				$position += 1;
				echo $home_link;
			}
			if ( is_category() ) {
				$parents = get_ancestors( get_query_var( 'cat' ), 'category' );
				foreach ( array_reverse( $parents ) as $cat ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				}
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					$cat      = get_query_var( 'cat' );
					echo $sep . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_current ) {
						if ( $position >= 1 ) {
							echo $sep;
						}
						echo $before . sprintf( $text[ 'category' ], single_cat_title( '', false ) ) . $after;
					} elseif ( $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_search() ) {
				if ( $show_home_link && $show_current || ! $show_current && $show_last_sep ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . sprintf( $text[ 'search' ], get_search_query() ) . $after;
				}
			} elseif ( is_year() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . get_the_time( 'Y' ) . $after;
				} elseif ( $show_home_link && $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_month() ) {
				if ( $show_home_link ) {
					echo $sep;
				}
				$position += 1;
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ), $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_time( 'F' ) . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_day() ) {
				if ( $show_home_link ) {
					echo $sep;
				}
				$position += 1;
				echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ), $position ) . $sep;
				$position += 1;
				echo sprintf( $link, get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), get_the_time( 'F' ), $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_time( 'd' ) . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_single() && ! is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$position  += 1;
					$post_type = get_post_type_object( get_post_type() );
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->labels->name, $position );
					if ( $show_current ) {
						echo $sep . $before . get_the_title() . $after;
					} elseif ( $show_last_sep ) {
						echo $sep;
					}
				} else {
					$cat       = get_the_category();
					$catID     = $cat[ 0 ]->cat_ID;
					$parents   = get_ancestors( $catID, 'category' );
					$parents   = array_reverse( $parents );
					$parents[] = $catID;
					foreach ( $parents as $cat ) {
						$position += 1;
						if ( $position > 1 ) {
							echo $sep;
						}
						echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
					}
					if ( get_query_var( 'cpage' ) ) {
						$position += 1;
						echo $sep . sprintf( $link, get_permalink(), get_the_title(), $position );
						echo $sep . $before . sprintf( $text[ 'cpage' ], get_query_var( 'cpage' ) ) . $after;
					} else {
						if ( $show_current ) {
							echo $sep . $before . get_the_title() . $after;
						} elseif ( $show_last_sep ) {
							echo $sep;
						}
					}
				}
			} elseif ( is_post_type_archive() ) {
				$post_type = get_post_type_object( get_post_type() );
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->label, $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . $post_type->label . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_attachment() ) {
				$parent    = get_post( $parent_id );
				$cat       = get_the_category( $parent->ID );
				$catID     = $cat[ 0 ]->cat_ID;
				$parents   = get_ancestors( $catID, 'category' );
				$parents   = array_reverse( $parents );
				$parents[] = $catID;
				foreach ( $parents as $cat ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position );
				}
				$position += 1;
				echo $sep . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position );
				if ( $show_current ) {
					echo $sep . $before . get_the_title() . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_page() && ! $parent_id ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . get_the_title() . $after;
				} elseif ( $show_home_link && $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_page() && $parent_id ) {
				$parents = get_post_ancestors( get_the_ID() );
				foreach ( array_reverse( $parents ) as $pageID ) {
					$position += 1;
					if ( $position > 1 ) {
						echo $sep;
					}
					echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position );
				}
				if ( $show_current ) {
					echo $sep . $before . get_the_title() . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( is_tag() ) {
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					$tagID    = get_query_var( 'tag_id' );
					echo $sep . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . sprintf( $text[ 'tag' ], single_tag_title( '', false ) ) . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_author() ) {
				$author = get_userdata( get_query_var( 'author' ) );
				if ( get_query_var( 'paged' ) ) {
					$position += 1;
					echo $sep . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text[ 'author' ], $author->display_name ), $position );
					echo $sep . $before . sprintf( $text[ 'page' ], get_query_var( 'paged' ) ) . $after;
				} else {
					if ( $show_home_link && $show_current ) {
						echo $sep;
					}
					if ( $show_current ) {
						echo $before . sprintf( $text[ 'author' ], $author->display_name ) . $after;
					} elseif ( $show_home_link && $show_last_sep ) {
						echo $sep;
					}
				}
			} elseif ( is_404() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				if ( $show_current ) {
					echo $before . $text[ '404' ] . $after;
				} elseif ( $show_last_sep ) {
					echo $sep;
				}
			} elseif ( has_post_format() && ! is_singular() ) {
				if ( $show_home_link && $show_current ) {
					echo $sep;
				}
				echo get_post_format_string( get_post_format() );
			}
			echo $wrap_after;
		}
	} // end of dimox_breadcrumbs()
	add_action( 'widgets_init', 'register_my_widgets' );
	function register_my_widgets() {
		register_sidebar( [
			'name'          => sprintf( __( 'Social links in footer' ) ),
			'id'            => "sidebar-footer",
			'description'   => '',
			'class'         => '',
			'before_widget' => '<li id="%1$s" class="%2$s">',
			'after_widget'  => "</li>\n",
			'before_title'  => '',
			'after_title'   => "",
		] );
	}

	// wp-content/themes/freelanceengine/includes/mailing.php::new_project_of_category()
	function notice_user_new_project( $result = null, $args = [] ) {
		global $wpdb;
		//    file_put_contents(__DIR__ . '/_tmp_notice_user_new_project.log', "\n" . date('H:i:s d-m-Y') . "\n" . json_encode([
		//        $result,
		//        '$result_is' => gettype($result),
		//        '$args_is' => gettype($args),
		//        $args,
		//            'trace' => debug_backtrace(),
		//    ], JSON_PRETTY_PRINT), FILE_APPEND);
		if ( $result->post_status == 'published' && $result->post_type == 'project' ) {
			if ( ! empty( $result->skill ) ) {
				$skills = $result->skill;
			} else {
				$skills = [];
			}
			$currentDate   = ( new DateTime( 'now', new DateTimeZone( 'UTC' ) ) )->getTimestamp();
			$andExpireDate = "AND pp.expired_date > $currentDate";
			//        $andExpireDate = "AND pp.expired_date > UNIX_TIMESTAMP()";
			$data_emails = $wpdb->get_results( "SELECT DISTINCT u.display_name, u.user_email FROM {$wpdb->prefix}posts p
        LEFT JOIN wp_postmeta pm ON pm.post_id = p.ID AND pm.meta_key = 'email_skill'
        LEFT JOIN {$wpdb->prefix}term_relationships tr ON tr.object_id = p.ID
        LEFT JOIN {$wpdb->prefix}users u ON u.ID = p.post_author
        LEFT JOIN {$wpdb->prefix}pro_paid_users u ON u.ID = p.post_author
        LEFT JOIN wp_pro_paid_users pp ON pp.user_id = u.ID
        WHERE p.post_status = 'publish' AND p.post_type = 'fre_profile' AND pm.meta_value = 1
        AND tr.term_taxonomy_id IN (" . implode( ',', $skills ) . ")
        {$andExpireDate}
        ", ARRAY_A );
			if ( ! empty( $data_emails ) ) {
				$data[ 'site_url' ]      = home_url( '/' );
				$data[ 'title_project' ] = $result->post_title;
				$data[ 'link_project' ]  = ! empty( $result->permalink ) ? $result->permalink : $result->gui;
				$emails                  = [];
				foreach ( $data_emails as $email ) {
					if ( ! empty( $data_email[ 'display_name' ] ) && ! empty( $data_email[ 'user_email' ] ) ) {
						$emails[] = "{$data_email['display_name']} <{$data_email['user_email']}>";
					}
				}
				$message = '<p>' . __( 'On website {{site_url}} published new project for your relevant skills:' ) . '</p>' . '<br>' . '<b>{{title_project}}</b>' . '<br>' . __( 'You can view it from this link: ' ) . '<a href="{{link_project}}">{{link_project}}</a>' . '<br><br>' . __( 'Thanks & Regards, Admin' );
				$message = str_replace( array_keys( $data ), array_values( $data ), $message );
				$subject = $_SERVER[ 'HTTP_HOST' ] . ' - ' . __( 'Published new project for your relevant skills' );
				wp_mail( $emails, $subject, $message, 'Content-type : text/html' );
			}
		}
	}

	function get_page_url( $template_name ) {
		$pages = get_posts( [
			'post_type'   => 'page',
			'post_status' => 'publish',
			'meta_query'  => [
				[
					'key'     => '_wp_page_template',
					'value'   => $template_name . '.php',
					'compare' => '='
				]
			]
		] );
		if ( ! empty( $pages ) ) {
			foreach ( $pages as $pages__value ) {
				return get_permalink( $pages__value->ID );
			}
		}

		return get_bloginfo( 'url' );
	}

	//add_action('wp_ajax_check_acc_bid', 'freelancer_edit_advert');
	function check_access_to_bid( $isRequest = true ) {
		global $user_ID, $wpdb;
		if ( $user_ID ) {
			$statusUser    = get_user_pro_status( $user_ID );
			$propAccessBid = getValueByProperty( $statusUser, 'bid_in_project' );
			$accessBid     = ( $propAccessBid == - 1 || $propAccessBid === false ) ? true : false;
			if ( ! $accessBid ) {
				$date      = date( 'd-m-Y' );
				$sql       = "SELECT COUNT(ID) FROM {$wpdb->prefix}posts
            WHERE post_author = {$user_ID} AND post_type = '" . BID . "'
            AND DATE_FORMAT(post_date, '%d-%m-%Y') = '{$date}'";
				$countBid  = $wpdb->get_var( $sql );
				$accessBid = ( $countBid < $propAccessBid ) ? true : false;
			}
			if ( $accessBid ) {
				if ( $isRequest ) {
					wp_send_json( [
						'success' => true
					] );
				} else {
					return true;
				}
			}
		}
		if ( $isRequest ) {
			wp_send_json( [
				'success' => false,
				'msg'     => _( 'Bids limit per day have been reached. Please upgrade your account to PRO for unlimited bids.' )
			] );
		} else {
			return false;
		}
	}

	function setOverrideJqueryCore() {
		if ( ! is_admin() ) {
			//wp_deregister_script( 'jquery-core' );
			//wp_enqueue_script( 'jquery-core', '/wp-content/themes/_for_plugins/js/jquery-2.0.js', [], '2.0', 1 );
			wp_localize_script( 'jquery', 'ajax_var', // добавляем объект с глобальными JS переменными
				[
					'url'   => admin_url( 'admin-ajax.php' ),
					// и суем в него путь до AJAX обработчика
					'nonce' => wp_create_nonce( 'ajax_object' )
					// передадим уникальную строку для механизма проверки аякс запроса, ajax_var.nonce
				] );
		}
	}

	add_action( 'wp_enqueue_scripts', 'setOverrideJqueryCore' );
	/*load more posts*/
	function true_load_posts() {
		$args            = unserialize( stripslashes( $_POST[ 'query' ] ) );
		$args[ 'paged' ] = $_POST[ 'page' ] + 1;
		// обычно лучше использовать WP_Query, но не здесь
		query_posts( $args );
		// если посты есть
		if ( have_posts() ) :
			// запускаем цикл
			while ( have_posts() ): the_post();
				if ( $args[ 'post_type' ] == 'post' ) {
					get_template_part( 'template/blog', 'item4' );
				} else {
					get_template_part( 'template/endors', 'item' );
				}
			endwhile;
		endif;
		die();
	}

	add_action( 'wp_ajax_loadmore', 'true_load_posts' );
	add_action( 'wp_ajax_nopriv_loadmore', 'true_load_posts' );
	/*end load more*/
	function gretathemes_meta_tags_author() {
		echo '<meta name="description" content="MasterHand Pro" />';
	}

	// Add a custom user role
	function create_userrole() {
		add_role( 'company', __( 'Company' ), [] );
	}

	add_action( 'init', 'create_userrole' );
	add_filter( 'ae_pre_insert_user', 'set_user_first_name_by_company' );
	function set_user_first_name_by_company( $user_data ) {
		if ( $user_data[ 'role' ] == FREELANCER && $user_data[ 'type_prof' ] == COMPANY ) {
			if ( empty( $user_data[ 'company_name' ] ) ) {
				return new WP_Error( 'existing_company_name', __( 'Company name is empty!' ) );
			}
			$user_data[ 'first_name' ] = $user_data[ 'company_name' ];
			$user_data[ 'last_name' ]  = '';
		}

		return $user_data;
	}

	/*page contact us*/
	add_filter( 'template_include', 'contact_us_template_include', 1 );
	function contact_us_template_include( $template ) {
		if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/contact-us' ) !== false ) {
			global $wp_query;
			add_filter( 'wp_title', 'change_profile_seo_title', 1 );
			add_filter( 'aioseop_title', 'change_profile_seo_title', 1 );
			function change_profile_seo_title() {
				$urle   = $_SERVER[ 'REQUEST_URI' ];
				$arurle = explode( '/', $urle );
				if ( count( $arurle ) > 2 ) {
					$cattitle = ucfirst( str_replace( '-', ' ', end( $arurle ) ) ) . ' | ' . get_bloginfo( 'name' );
				} else {
					$cattitle = __( 'Contact us' ) . ' | ' . get_bloginfo( 'name' );
				}

				return $cattitle;
			}

			//        add_filter('body_class', function ($classes) { return array_merge($classes, array('page-template-page-proffessionals page')); });
			status_header( 200 );
			$new_template = locate_template( [ 'page-contact-us.php' ] );
			if ( ! empty( $new_template ) ) {
				return $new_template;
			}
		}

		return $template;
	}

	;
	// get currency for select
	function get_currency() {
		$currency_dir  = get_template_directory() . '/assets/currency';
		$currency_arr  = file_get_contents( "{$currency_dir}/currency.json" );
		$currency_arr  = json_decode( $currency_arr, true );
		$countries_arr = file_get_contents( "{$currency_dir}/countries.json" );
		$countries_arr = json_decode( $countries_arr, true );
		$currency_data = [];
		foreach ( $currency_arr as $country => $currency ) {
			$country_code         = array_search( $country, $countries_arr );
			$country_code         = strtolower( $country_code );
			$icon                 = "/country-flags/{$country_code}.svg";
			$icon                 = file_exists( "{$currency_dir}/$icon" ) ? get_template_directory_uri() . "/assets/currency{$icon}" : '';
			$currency[ 'symbol' ] = isset( $currency[ 'symbol' ] ) ? $currency[ 'symbol' ] : '';
			$currency_data[]      = [
				'country' => $country,
				'code'    => $currency[ 'code' ],
				'fa-icon' => $currency[ 'fa-icon' ],
				'symbol'  => $currency[ 'symbol' ],
				'flag'    => $icon,
			];
		}

		return $currency_data;
	}

	function get_user_country() {
		$location = getLocation( get_current_user_id() );
		if ( ! isset( $location[ 'country' ] ) ) {
			return false;
		}
		$country = $location[ 'country' ];

		return $country;
	}

	/**
	 * Cron PRO-paid users check
	 */
	if ( ! wp_next_scheduled( 'pro_paid_hook' ) ) {
		wp_schedule_event( time(), 'hourly', 'pro_paid_hook' );
		//wp_schedule_event(time(), 'every_three_minutes', 'pro_paid_hook');
	}
	//user authorization script by using link in an email
	add_action( 'init', 'checking_email_hash' );
	function checking_email_hash() {
		if ( ! empty( $_GET[ 'email_hash' ] ) && $_GET[ 'user_id' ] > 0 && ! is_user_logged_in() ) {
			$user = get_user_by( 'id', $_GET[ 'user_id' ] );
			if ( $user ) {
				$hash = get_post_meta( $user->ID, "login_from_email" );
				if ( $_GET[ 'email_hash' ] == $hash ) {
					wp_set_auth_cookie( $user->ID );
					wp_redirect( $_SERVER[ 'REQUEST_URI' ] );
					die;
				}
			}
			exit;
		}
	}

	add_action( 'pro_paid_hook', 'pro_paid_func' );
	//add_action( 'init', 'pro_paid_func');
	function pro_paid_func() {
		global $wpdb;
		$headers   = [];
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$today     = current_time( 'timestamp' ); // current time
		$today_p   = date( 'Y-m-d H:i:s', $today ); // converted timestamps to date format
		$tomorrow  = strtotime( "+1 day", $today ); // add timestamps today + 1 day
		//$tomorrow = $today;
		$todayHour            = date( "H:i:s", floor( $today / 3600 ) * 3600 ); // take the current hour
		$todayHourPlusOneHour = date( "H:i:s", floor( strtotime( '+1 hour', $today ) / 3600 ) * 3600 ); // take the current hour + 1
		$args                 = [
			'posts_per_page' => - 1,
			'payment'        => 0,
			'post_status'    => [
				'publish',
				// 'draft'
			],
			'post__in'       => '',
			'post_type'      => 'order',
			'meta_query'     => [
				[
					'key'     => 'mail_sent',
					'compare' => 'NOT EXISTS',
				]
			]
		];
		$order_query          = new WP_Query( $args );
		// $i = 0;
		while ( $order_query->have_posts() ) {
			//global $post;
			$order_query->the_post();
			$order_object = new AE_Order( get_the_ID() );
			$order_data   = $order_object->get_order_data();
			if ( isset( $order_data[ 'products' ] ) ) {
				//  Get Name && types attributes for product
				$names = [];
				$types = [];
				foreach ( $order_data[ 'products' ] as $product ) {
					$names[] = $product[ 'NAME' ];
					$types[] = $product[ 'TYPE' ];
				}
				foreach ( array_unique( $types ) as $type ) {
					if ( $type === 'pack' ) {
						$et_order_product_id = get_post_meta( get_the_ID(), 'et_order_product_id', true );
						//die($et_order_product_id);
						// get timestamps
						$timestamps = [];
						//echo "<pre>";
						//print_r($names);
						//echo "</pre>";
						// Get timestamps meta fields product
						foreach ( $names as $name ) {
							if ( $name === 'create_project_for_all' ) {
								continue;
							}
							$expire_time  = strtotime( get_post_meta( $et_order_product_id, 'et_' . $name )[ 0 ] );
							$timestamps[] = $expire_time;
						}
						$maxTimestamp = max( $timestamps ); // this MAX timestamp value
						if ( ! $maxTimestamp && isset( $timestamps[ 0 ] ) ) {
							$maxTimestamp = $timestamps[ 0 ];
						}
						$maxHour = date( "H:i:s", $maxTimestamp ); // get hour end date
						//die($maxTimestamp);
						if ( $maxTimestamp ) {
							//masterhand_send_teams($today <= $maxTimestamp && $maxTimestamp <= $tomorrow);
							if ( $today <= $maxTimestamp && $maxTimestamp <= $tomorrow ) {
								//if ($maxHour > $todayHour && $maxHour < $todayHourPlusOneHour) {
								$user     = new WP_User( $order_data[ 'payer' ] );
								$message  = ae_get_option( 'options_deadline_paid' );
								$blogname = get_bloginfo( 'name' );
								$hash     = md5( wp_generate_password() );
								//masterhand_send_teams($et_order_product_id.':'.$maxHour);
								update_post_meta( $user->ID, "login_from_email", $hash );
								update_post_meta( get_the_ID(), "mail_sent", 1 );
								$project_url  = get_the_permalink( $et_order_product_id );
								$project_name = get_the_title( $et_order_product_id );
								$params       = [
									'blogname'     => $blogname,
									'display_name' => $user->display_name,
									'project_name' => '<a style="color:#2C33C1" href="' . $project_url . '">' . $project_name . '</a>',
									'pay_link'     => '<a style="color:#2C33C1" href="' . WPP_HOME . '/options-project/?id=' . $et_order_product_id . '&user_id=' . $user->ID . '&email_hash=' . $hash . '">' . __( "link", ET_DOMAIN ) . '</a>',
								];
								//$receiver_email
								foreach ( $params as $key => $value ) {
									$message = str_replace( "[$key]", $value, $message );
								}
								$ae_mailing     = AE_Mailing::get_instance();
								$receiver_email = get_the_author_meta( 'user_email', $order_data[ 'payer' ] );
								$ae_mailing->wp_mail( $receiver_email, 'Extra options in your project have expired', $message );
								// instance for test:
								$params[ 'receiver_email' ] = $receiver_email;
								masterhand_send_teams( $params );
								//}
								// $i++;
							}
						}
					}
				}
			}
		}
	}

	function masterhand_send_teams( $data, $text = 'query db', $c = 'ff0000', $s = 'query db', $t = 'masterhand mail:' ) {
		$url  = 'https://outlook.office.com/webhook/8dce0422-4ee3-4209-a963-ac6fbce52eaa@6c3152c1-c1fa-442b-a03c-5d20f2b6a8bc/IncomingWebhook/a231eb9b55934c03bdebc976c15975aa/7c82b88b-c6d2-4fad-94c5-d892b9543cbd';
		$send = "{ 
            '@type': 'MessageCard',
            '@context': 'http://schema.org/extensions',
            'themeColor': '" . $c . "',
            'summary': '" . $s . "', 
            'sections': [{ 'activityTitle': '" . $t . "',
                                'text': '" . json_encode( $data ) . "',
                                'markdown': 'true',
                                'activityImage': 'https://manu.team/wp-content/uploads/2020/09/galaxy.png',
             }] " . " }";
		$args = [
			'headers' => [ 'Content-Type' => 'application/json; charset=utf-8' ],
			'body'    => $send,
			'method'  => 'POST'
		];

		return wp_remote_request( $url, $args ); //POST to TEAMS
	}

	require_once dirname( __FILE__ ) . '/includes/wp_all_export_functions.php';
	// filter posts in admin by author
	add_action( 'restrict_manage_posts', function() {
		$type = 'post';
		if ( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}
		//only add filter to post type you want
		if ( 'post' == $type ) {
			//change this to the list of values you want to show
			//in 'label' => 'value' format
			$values   = [];
			$testting = get_posts( [ 'posts_per_page' => - 1, 'post_type' => 'edition' ] );
			foreach ( $testting as $post ):
				$values[ $post->post_title ] = $post->ID;
			endforeach;
			$current_value = isset( $_GET[ 'author_type' ] ) ? $_GET[ 'author_type' ] : '';
			?>
            <select name="author_type">
                <option value="any">Select author type</option>
                <option value="users" <?php echo $current_value == 'users' ? 'selected' : '' ?>>Users</option>
                <option value="masterhand" <?php echo $current_value == 'masterhand' ? 'selected' : '' ?>>Masterhand
                </option>
            </select>
		<?php }
	} );
	add_filter( 'parse_query', function( $query ) {
		global $pagenow;
		$author_type = isset( $_GET[ 'author_type' ] ) ? $_GET[ 'author_type' ] : '';
		$type        = isset( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : 'post';
		if ( 'post' == $type && is_admin() && $pagenow == 'edit.php' && $author_type != '' && $query->is_main_query() ) {
			switch ( $author_type ) {
				case 'users':
					$query->query_vars[ 'meta_key' ]   = 'usp_post_by_user';
					$query->query_vars[ 'meta_value' ] = 'true';
					break;
				case 'masterhand':
					$args                              = [
						'key'     => 'usp_post_by_user',
						'compare' => 'NOT EXISTS'
					];
					$meta_query                        = $query->query_vars[ 'meta_query' ];
					$meta_query[]                      = $args;
					$query->query_vars[ 'meta_query' ] = $meta_query;
					break;
				default:
					return true;
					break;
			}
		}
	} );
	add_action( 'load-edit.php', function() {
		add_filter( 'post_class', function() {
			global $post;
			$is_post_by_user = get_post_meta( $post->ID, 'usp_post_by_user', true );
			if ( $is_post_by_user ) {
				$classes[] = 'post-by-user';
			} else {
				$classes[] = '';
			}

			return $classes;
		} );
	} );
	// save freelancer's post meta data
	add_action( 'save_post', function( $post_id ) {
		$is_post_by_user = get_post_meta( $post_id, 'usp_post_by_user', true );
		if ( $is_post_by_user ) {
			update_post_meta( $post_id, 'usp_post_by_user', true );
		}
		if ( isset( $_REQUEST[ 'usp_attaches' ] ) ) {
			update_post_meta( $post_id, 'usp_attaches', $_REQUEST[ 'usp_attaches' ] );
		}
	} );
	/**
	 * Additional code for refactoring
	 * TODO refactoring
	 */
	add_action( 'add_meta_boxes', function() {
		$screens = [ 'post' ];
		add_meta_box( 'usp-attaches__box', __( 'User Attachments', 'freelanceengine' ), 'usp_attaches_metabox', $screens );
	} );
	function usp_attaches_metabox( $post, $meta ) {
		$usp_attaches = get_post_meta( $post->ID, 'usp_attaches', true );
		if ( ! $usp_attaches ) {
			return false;
		}
		$usp_attaches = explode( ',', $usp_attaches ); ?>
        <div class="usp_attaches__container">
            <ul class="freelance-portfolio-list row">
				<?php foreach ( $usp_attaches as $usp_attach ) {
					$doc_data            = get_post( $usp_attach );
					$document            = [
						'id'    => $doc_data->ID,
						'url'   => wp_get_attachment_url( $doc_data->ID ),
						'mime'  => $doc_data->post_mime_type,
						'title' => $doc_data->post_title
					];
					$is_application_mime = stripos( $document[ 'mime' ], 'application' ) !== false ? true : false;
					if ( $is_application_mime ) {
						switch ( $document[ 'mime' ] ) {
							case 'application/pdf':
								$border = '#cc4b4c';
								$icon   = '/wp-content/uploads/2020/08/pdf.svg';
								break;
							case 'application/msword':
								$border = '#1e96e6';
								$icon   = '/wp-content/uploads/2020/08/doc.svg';
								break;
							case 'application/vnd.ms-excel':
								$border = '#91cda0';
								$icon   = '/wp-content/uploads/2020/08/xls.svg';
								break;
							case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
								$border = '#1e96e6';
								$icon   = '/wp-content/uploads/2020/08/docx.svg';
								break;
							case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
								$border = '#91cda0';
								$icon   = '/wp-content/uploads/2020/08/xlsx.svg';
								break;
						}
					} ?>
                    <li class="col-sm-4 col-md-3 col-lg-3 col-sx-12"
                        id="usp_attach_item_<?php echo $document[ 'id' ] ?>">
                        <div class="freelance-portfolio-wrap" id="portfolio_item_<?php echo $document[ 'id' ] ?>"
							<?php if ( $is_application_mime ) { ?>
                                style="border: 2px solid <?php echo $border ?>"
							<?php } ?>
                        >
                            <div class="freelance-portfolio"
                                 style="background:url(<?php echo $document[ 'url' ] ?>) center no-repeat;">
                                <a href="javascript:void(0)" class="fre-view-portfolio-new"
                                   data-id="<?php echo $document[ 'id' ] ?>"></a>
                                <img src="<?= $document[ 'url' ] ?>" style="display:none;">
								<?php if ( $is_application_mime ) { ?>
                                    <img src="<?php echo $icon ?>"
                                         style="width: 50px; position: absolute; bottom: 25px; left: 10px;">
                                    <span style="position: absolute; top: 25px; left: 15px;">
                                    <?php echo $document[ 'title' ] ?>
                                </span>
								<?php } ?>
                            </div>
                            <div class="portfolio-action">
                                <a href="<?= $document[ 'url' ] ?>" target="_blank" class="fre-submit-btn btn-center"
                                   href="<?php echo $document[ 'url' ] ?>"><?php _e( 'Open', ET_DOMAIN ) ?></a>
                                <a href="javascript:void(0)" class="fre-cancel-btn btn-center"
                                   onclick="attach_remove_modal(<?php echo $post->ID ?>, <?php echo $document[ 'id' ] ?>)">
									<?php _e( 'Remove', ET_DOMAIN ) ?>
                                </a>
                            </div>
                        </div>
                    </li>
				<?php } ?>
            </ul>
        </div>
        <div class="modal" id="modal_delete_file" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                onclick="jQuery('#modal_delete_file').hide()"></button>
						<?php _e( 'Are your sure you want to delete this item?', ET_DOMAIN ) ?>
                    </div>
                    <div class="modal-body">
                        <div class="fre-modal-form form_delete_file" id="form_delete_file">
                            <div class="fre-content-confirm">
                                <p><?php _e( "Once the item is deleted, it will be permanently removed from the site and its information won't be recovered.", ET_DOMAIN ) ?></p>
                            </div>
                            <input type="hidden" value="" name="ID">
                            <div class="fre-form-btn">
                                <button class="fre-submit-btn btn-left btn_submit_document" type="button"
                                        onclick="attach_remove()"><?php _e( 'Confirm', ET_DOMAIN ) ?></button>
                                <span class="fre-cancel-btn" onclick="jQuery('#modal_delete_file').hide()"
                                      data-dismiss="modal"><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
						<?php _e( 'Item has been deleted successfully', ET_DOMAIN ) ?>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
	<?php }

	add_action( 'wp_ajax_usp_remove_attach', function() {
		$post_id       = $_REQUEST[ 'post_id' ];
		$attach_id     = $_REQUEST[ 'attach_id' ];
		$post_attaches = get_post_meta( $post_id, 'usp_attaches', true );
		$post_attaches = explode( ',', $post_attaches );
		$post_attaches = array_filter( $post_attaches, function( $value ) use ( $attach_id ) {
			if ( $value == $attach_id ) {
				return false;
			}

			return true;
		}, ARRAY_FILTER_USE_BOTH );
		$post_attaches = array_values( $post_attaches );
		$post_attaches = implode( ',', $post_attaches );
		// first delete post
		wp_delete_post( $attach_id );
		// then update parent post meta without deleted attach
		update_post_meta( $post_id, 'usp_attaches', $post_attaches );
		exit();
	} );
	/**
	 * Export button move to screen
	 */
	add_action( 'admin_footer', 'mh_export_additional_wpae' );
	function mh_export_additional_wpae() {
		$screen = get_current_screen();
		if ( $screen->id != 'all-export_page_pmxe-admin-manage' ) {
			return;
		}
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#import-list .tablenav .actions').append('<div style="display:inline;"><div class="button button-primary import-list-payment-statistic">Export Payments Statistic</div>');
                $('html').on('click', '.import-list-payment-statistic', function () {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '/wp-admin/admin-ajax.php',
                        data: {
                            'action': 'import_payment_statistic',
                            'flag': '1',
                        },
                        complete: function (resp) {
                            console.log(resp);
                            let win = window.open(resp.responseText, '_blank');
                            win.focus();
                        }
                    });
                });
            });
        </script>
		<?php
	}

	if ( wp_doing_ajax() && is_admin() ) {
		add_action( 'wp_ajax_import_payment_statistic', 'import_payment_statistic' );
		if ( ! function_exists( 'import_payment_statistic' ) ) {
			function import_payment_statistic() {
				require __DIR__ . '/libs/xlsx/SimpleXLSXGen.php';
				$xlsx_items = [];
				// The Query
				$args = [
					'posts_per_page' => - 1,
					'payment'        => 0,
					'post_status'    => [
						'pending',
						'publish',
						// 'draft'
					],
					'post__in'       => '',
					'post_type'      => 'order'
				];
				if ( $args[ 'payment' ] ) {
					// $args['meta_key'] = 'et_order_gateway';
					// $args['meta_value'] = $args['payment'];
					$args[ 'meta_query' ] = [
						'relation' => 'AND',
						[
							'key'   => 'et_order_gateway',
							'value' => $args[ 'payment' ]
						]
					];
				}
				unset( $args[ 'payment' ] );
				$order_query = new WP_Query( $args );
				/**
				 * build orders id param
				 */
				$summary_review   = 0;
				$summary_pro      = 0;
				$summary_projects = 0;
				$count_review     = 0;
				$count_pro        = 0;
				$count_projects   = 0;
				while ( $order_query->have_posts() ) {
					$order_query->the_post();
					$order_object = new AE_Order( get_the_ID() );
					$order_data   = $order_object->get_order_data();
					//var_dump($order_data);
					if ( ! empty( $order_data ) ) {
						//var_dump($order_data);
						// 'ID', 'Payer', 'Type', 'SKU', 'Date', 'Total, 'Payment'
						$TYPE = '';
						$NAME = '';
						if ( isset( $order_data[ 'products' ] ) ) {
							foreach ( $order_data[ 'products' ] as $product ) {
								$TYPE = $product[ 'TYPE' ];
								$NAME = $product[ 'NAME' ];
								if ( $TYPE === 'review_payment' ) {
									$count_review = $count_review + 1;
								}
								if ( $TYPE === 'pro_plan' ) {
									$count_pro = $count_pro + 1;
								}
								if ( $TYPE === 'pack' ) {
									$count_projects = $count_projects + 1;
								}
							}
						}
						if ( $TYPE === 'review_payment' ) {
							$summary_review = $summary_review + (int) $order_data[ 'total' ];
						}
						if ( $TYPE === 'pro_plan' ) {
							$summary_pro = $summary_pro + (int) $order_data[ 'total' ];
						}
						if ( $TYPE === 'pack' ) {
							$summary_projects = $summary_projects + (int) $order_data[ 'total' ];
						}
						$user         = new WP_User( $order_data[ 'payer' ] );
						$xlsx_items[] = [
							$order_data[ 'ID' ],
							$user->user_login,
							ae_user_role( $order_data[ 'payer' ] ),
							$TYPE,
							$NAME,
							$order_data[ 'created_date' ],
							$order_data[ 'total' ],
							$order_data[ 'payment' ],
						];
					}
				}
				$type = [];
				foreach ( $xlsx_items as $key => $row ) {
					$type[ $key ] = $row[ 3 ];
				}
				array_multisort( $type, SORT_DESC, $xlsx_items );
				array_unshift( $xlsx_items, [ 'ID', 'Payer', 'Role', 'Type', 'SKU', 'Date', 'Total', 'Payment' ] );
				$xlsx_items[] = [ '', '', '', '', '', '', '', '' ];
				$xlsx_items[] = [ '', '', '', 'Type', '', 'Count', 'Total', '' ];
				$xlsx_items[] = [ '', '', '', 'Reviews', '', $count_review, $summary_review, '' ];
				$xlsx_items[] = [ '', '', '', 'PRO', '', $count_pro, $summary_pro, '' ];
				$xlsx_items[] = [ '', '', '', 'Projects and Options', '', $count_projects, $summary_projects, '' ];
				$xlsx         = SimpleXLSXGen::fromArray( $xlsx_items );
				$uploads_path = wp_upload_dir();
				$xlsx->saveAs( $uploads_path[ 'basedir' ] . '/payment.xlsx' );
				//SimpleXLSXGen::download();
				wp_die( $uploads_path[ 'baseurl' ] . '/payment.xlsx' );
			}
		}
		function u562d_cmp( $a, $b ) {
			return strcmp( $a->display_name, $b->display_name );
		}
	}
	/*FrontG*/
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

	## Отфильтруем ЧПУ произвольного типа
	// фильтр: apply_filters( 'post_type_link', $post_link, $post, $leavename, $sample );
	add_filter( 'post_type_link', 'faq_permalink', 1, 2 );
	function faq_permalink( $permalink, $post ) {
		// выходим если это не наш тип записи: без холдера %products%
		if ( strpos( $permalink, '%faqcat%' ) === false ) {
			return $permalink;
		}
		// Получаем элементы таксы
		$terms = get_the_terms( $post, 'faqcat' );
		// если есть элемент заменим холдер
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) && is_object( $terms[ 0 ] ) ) {
			$term_slug = array_pop( $terms )->slug;
		} // элемента нет, а должен быть...
		else {
			$term_slug = 'no-faqcat';
		}

		return str_replace( '%faqcat%', $term_slug, $permalink );
	}

	/*FrontG*/
	/*
Колонка комментариев на странице пользователей в админке
v 1.0
*/
	add_filter( 'manage_users_columns', 'add_users_comm_column', 4 );
	add_filter( 'manage_users_custom_column', 'fill_users_comm_column', 5, 3 );
	add_filter( 'manage_users_sortable_columns', 'add_users_comm_sortable_column' );
	add_action( 'pre_user_query', 'add_users_comm_sort_query' );
	# создаем новую колонку
	function add_users_comm_column( $columns ) {
		$columns[ 'activated' ]     = 'Activation';
		$columns[ 'register_date' ] = 'Registration date'; // добавляет дату реги
		//unset( $columns['posts'] ); // удаляет колонку посты
		return $columns;
	}

	# заполняем колонку данными
	function fill_users_comm_column( $out, $column_name, $user_id ) {
		$userdata           = get_userdata( $user_id );
		$user_confirm_email = get_user_meta( $user_id, 'register_status', true );
		if ( 'activated' === $column_name ) {
			if ( ( ! empty( $user_confirm_email ) && $user_confirm_email !== 'confirm' ) || ( empty( $user_confirm_email ) ) ) {
				$out = 'Unactivated';
			} else {
				$out = 'Activated';
			}
		} elseif ( 'register_date' === $column_name ) {
			$out = mysql2date( 'j M Y', $userdata->user_registered );
		}

		return $out;
	}

	# добавляем возможность сортировать колонку
	function add_users_comm_sortable_column( $sortable_columns ) {
		$sortable_columns[ 'register_date' ] = 'register_date';
		$sortable_columns[ 'activated' ]     = 'activated';

		return $sortable_columns;
	}

	# сортировка колонки
	function add_users_comm_sort_query( $user_query ) {
		global $wpdb, $current_screen;
		$vars = $user_query->query_vars;
		if ( 'register_date' === $vars[ 'orderby' ] ) {
			$user_query->query_orderby = ' ORDER BY user_registered ' . $vars[ 'order' ];
		}
	}

	function prefix_sort_by_expiration_date( $query ) {
		global $wpdb, $current_screen;
		$vars = $query->query_vars;
		if ( 'activated' == $query->get( 'orderby' ) ) {
			$query->set( 'orderby', [ 'meta_value', 'user_registered' => 'ASC' ] );
			$query->set( 'meta_key', 'register_status' );
		}
	}

	add_action( 'pre_get_users', 'prefix_sort_by_expiration_date' );
	function add_recaptcha() {
		echo '<script src="https://www.google.com/recaptcha/api.js" async defer ></script>';
	}

	add_action( 'wp_head', 'add_recaptcha' );