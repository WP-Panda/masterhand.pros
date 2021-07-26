<?php
function fre_register_offer() {
	$labels = [
		'name'          => _x( 'Ad', 'post type general name' ),
		'singular_name' => _x( 'Ad', 'post type singular name' ),
		'add_new'       => null, //_x('Add New', 'author'),
		'add_new_item'  => null, //__('Add New Ad'),
		'new_item'      => null, //__('New Ad'),
		'edit_item'     => __( 'Edit Ad' ),
		'all_items'     => __( 'All Ads' ),
		'view_item'     => __( 'View Ad' ),
		'search_items'  => __( 'Search Ad' ),
		'not_found'     => __( 'No ad found' ),
		'menu_name'     => __( 'Ads of freelancers' )
	];
	$args   = [
		'labels'              => $labels,
		'menu_position'       => 5,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => [ 'slug' => 'advert' ],
		'capability_type'     => 'post',
		'has_archive'         => true,
		'hierarchical'        => true,
		'exclude_from_search' => true,
		'can_export'          => true,
		'supports'            => [ 'title', 'editor', 'author', 'thumbnail' ]
	];

	flush_rewrite_rules( false );
	register_post_type( 'advert', $args );

	global $ae_post_factory;
	$ae_post_factory->set( ADVERT, new AE_Posts( ADVERT, [], [
		'country',
		'state',
		'city'
	] ) );
}

add_action( 'init', 'fre_register_offer' );

add_filter( 'template_include', 'special_offers_template_include', 1 );
function special_offers_template_include( $template ) {
	if ( strpos( $_SERVER['REQUEST_URI'], '/special-offers/' ) !== false ) {
		add_filter( 'wp_title', function () {
			return __( 'Special Offers' ) . ' | ';
		}, 1 );
		add_filter( 'body_class', function ( $classes ) {
			return array_merge( $classes, [ 'page-template-special_offers' ] );
		} );
		status_header( 200 );

		$category     = $_SERVER['REQUEST_URI'];
		$new_template = empty( $category ) ? $template : locate_template( [ 'page-special-offers.php' ] );
		if ( ! empty( $new_template ) ) {
			return $new_template;
		}
	}
	if ( strpos( $_SERVER['REQUEST_URI'], '/my-adverts/' ) !== false ) {
		add_filter( 'wp_title', function () {
			return __( 'My Special Offers' ) . ' | ';
		}, 1 );
		status_header( 200 );

		$category     = $_SERVER['REQUEST_URI'];
		$new_template = empty( $category ) ? $template : locate_template( [ 'page-my-special-offers.php' ] );
		if ( ! empty( $new_template ) ) {
			return $new_template;
		}
	}

	return $template;
}

;

class Fre_OfferAction extends AE_PostAction {
	function __construct( $post_type = 'advert' ) {
		$this->post_type = ADVERT;
		$this->add_ajax( 'ae-fetch-offers', 'fetch_post' );

		$this->add_action( 'pre_get_posts', 'pre_get_offer' );
		$this->add_action( 'wp_footer', 'render_template_js' );

		$this->add_action( 'wp_ajax_fre_create_ad', 'freelancer_create_advert' );
		$this->add_action( 'wp_ajax_fre_edit_ad', 'freelancer_edit_advert' );
		$this->add_action( 'wp_ajax_fre_cancel_ad', 'freelancer_cancel_advert' );
		$this->add_action( 'wp_ajax_fre_ad_attach', 'freelancer_advert_attached' );

		$this->add_filter( 'ae_convert_advert', 'convert_offer' );
	}

	function pre_get_offer( $query ) {
		if ( ! empty( $query->query['category_name'] ) && $query->query['category_name'] == 'special-offers' ) {
			$query->query['category_name'] = $query->query_vars['category_name'] = '';
			$query->query['post_type']     = $query->query_vars['post_type'] = ADVERT;
			$query->query['post_status']   = $query->query_vars['post_status'] = 'publish';
		}

		return $query;
	}

	/**
	 * Override filter_query_args for action fetch_post.
	 */
	public function filter_query_args( $query_args ) {
		global $user_ID;
		$query = $_REQUEST['query'];

		if ( isset( $query['meta_key'] ) ) {
			$query_args['meta_key'] = $query['meta_key'];
			if ( isset( $query['meta_value'] ) ) {
				$query_args['meta_value'] = $query['meta_value'];
			}
		}

		if ( isset( $query['country'] ) && $query['country'] != '' ) {
			$query_args['meta_query'][] = [
				"key"     => "country",
				"value"   => (int) $query['country'],
				"type"    => "numeric",
				"compare" => "=",
			];
			if ( isset( $query['state'] ) && $query['state'] != '' ) {
				$query_args['meta_query'][] = [
					"key"     => "state",
					"value"   => (int) $query['state'],
					"type"    => "numeric",
					"compare" => "=",
				];
				if ( isset( $query['city'] ) && $query['city'] != '' ) {
					$query_args['meta_query'][] = [
						"key"     => "city",
						"value"   => (int) $query['city'],
						"type"    => "numeric",
						"compare" => "=",
					];
				}
			}
		}

		if ( isset( $query['orderby'] ) && $query['orderby'] == 'date' ) {
			$query_args['orderby'] = 'date';
		}

		return apply_filters( 'fre_offer_query_args', $query_args, $query );
	}

	/**
	 * render js template for notification
	 *
	 * @since  1.2
	 * @author Dakachi
	 */
	function render_template_js() {
		get_template_part( 'template-js/offer', 'item' );
	}

	function convert_offer( $result ) {
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

		if ( strlen( $result->post_content ) > 50 ) {
			$result->post_content_trim = substr( $result->post_content, 0, 49 ) . '...';
		} else {
			$result->post_content_trim = $result->post_content;
		}

		$result->post_author_url = get_author_posts_url( $result->post_author );
		$result->display_name    = get_the_author_meta( 'display_name', $result->post_author );

		return $result;
	}

	function freelancer_create_advert() {
		global $wpdb;

		$args = $_POST;
		unset( $args['action'] );

		if ( empty( $args['post_title'] ) || empty( $args['post_content'] ) ) {
			wp_send_json( [
				'success' => false,
				'msg'     => __( 'All fields are required' )
			] );
		}

		$args['post_status'] = 'publish';
		$args['post_type']   = 'advert';

		$countAds          = (int) $wpdb->get_var( "SELECT MAX(ID) FROM {$wpdb->prefix}posts" );
		$args['post_name'] = 'ad-' . ( $countAds + 1 );
		$result            = wp_insert_post( $args, true );

		if ( $result ) {
			update_post_meta( $result, 'country', $args['country'] );
			update_post_meta( $result, 'state', $args['state'] );
			update_post_meta( $result, 'city', $args['city'] );

			wp_send_json( [
				'success' => true,
				'post_id' => $result,
				'link'    => get_permalink( $result ),
				'msg'     => __( "Special Offer has been created successfully.", ET_DOMAIN )
			] );
		} else {
			// notice if false
			wp_send_json( [
				'success' => false,
				'msg'     => $result->get_error_message()
			] );
		}
	}

	function freelancer_edit_advert() {
		global $wpdb;

		$args = $_POST;
		unset( $args['action'] );

		if ( empty( $args['post_title'] ) || empty( $args['post_content'] ) ) {
			wp_send_json( [
				'success' => false,
				'msg'     => __( 'All fields are required' )
			] );
		}
		$args['ID'] = (int) $args['post_id'];
		$result     = wp_update_post( $args, true );

		if ( $result ) {
			update_post_meta( $result, 'country', $args['country'] );
			update_post_meta( $result, 'state', $args['state'] );
			update_post_meta( $result, 'city', $args['city'] );

			wp_send_json( [
				'success' => true,
				'post_id' => $result,
				'link'    => get_permalink( $result ),
				'msg'     => __( "Special Offer has been changed", ET_DOMAIN )
			] );
		} else {
			// notice if false
			wp_send_json( [
				'success' => false,
				'msg'     => $result->get_error_message()
			] );
		}
	}

	function freelancer_cancel_advert() {
		global $wpdb;

		if ( empty( $_POST['post_id'] ) ) {
			wp_send_json( [
				'success' => false,
				'msg'     => __( 'Post Id not found!' )
			] );
		}
		$post['ID']          = (int) $_POST['post_id'];
		$post['post_status'] = 'archive';
		$result              = wp_update_post( $post, true );

		if ( $result ) {
			wp_send_json( [
				'success' => true,
				'data'    => $result,
				'link'    => get_permalink( $result ),
				'msg'     => __( "Update successfully.", ET_DOMAIN )
			] );
		} else {
			// notice if false
			wp_send_json( [
				'success' => false,
				'msg'     => $result->get_error_message()
			] );
		}
	}

	function freelancer_advert_attached() {
		global $wpdb, $user_ID;

		$advert_id = (int) $_POST['post_id'];
		if ( ! empty( $advert_id ) ) {
			$countAttachments = (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->prefix}posts WHERE post_parent = {$advert_id}" );
			$post_type        = get_post_type( $advert_id );
		} else {
			$countAttachments = null;
		}

		$delete_file = ! empty( $_POST['delete_file'] ) && is_array( $_POST['delete_file'] ) ? $_POST['delete_file'] : [];
		if ( ! empty( $delete_file ) ) {
			foreach ( $delete_file as $item ) {
				if ( ! empty( $post_type ) && $post_type == 'fre_profile' && $countAttachments !== null ) {
					delete_user_meta( $user_ID, 'cover', (int) $item );
					$post_img = get_post( $item, OBJECT );
					delete_user_meta( $user_ID, 'cover_url', $post_img->guid );
				}

				wp_delete_attachment( (int) $item );
			}
		}

		$max_image = 10;
		if ( ! empty( $_FILES ) ) {

			//        file_put_contents(__DIR__.'/f.txt',"\r\n".'$Attachments-'.json_encode($countAttachments,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),FILE_APPEND);
			//        file_put_contents(__DIR__.'/f.txt',"\r\n".'$delete_file-'.json_encode(count($delete_file),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),FILE_APPEND);

			//        $countAttachments = $Attachments['count_img'];
			$max_image = $post_type == 'fre_profile' ? ! empty( $delete_file ) ? count( $delete_file ) + 1 : 1 : $max_image;

			add_filter( 'upload_dir', function ( $data ) {
				$advert_id      = (int) $_POST['post_id'];
				$data['url']    = $data['url'] . "/post/{$advert_id}";
				$data['path']   = $data['path'] . "/post/{$advert_id}";
				$data['subdir'] = $data['subdir'] . "/post/{$advert_id}";

				return $data;
			} );
			//        $attach_id = [];
			$prepare_attach = [];

			foreach ( $_FILES['files']['error'] as $ind => $error ) {
				if ( $error == 0 && $_FILES['files']['size'][ $ind ] <= 2100000 ) {
					if ( $countAttachments < $max_image ) {
						$prepare_attach['error']    = $error;
						$prepare_attach['name']     = $_FILES['files']['name'][ $ind ];
						$prepare_attach['size']     = $_FILES['files']['size'][ $ind ];
						$prepare_attach['tmp_name'] = $_FILES['files']['tmp_name'][ $ind ];
						$prepare_attach['type']     = $_FILES['files']['type'][ $ind ];
						$attach_id                  = attach_advert_file( $prepare_attach, $advert_id, [
							'jpg|jpeg' => 'image/jpeg',
							'png'      => 'image/png',
						] );

						//                    file_put_contents(__DIR__.'/f.txt',"\r\n".'$attach_id1-'.json_encode($attach_id,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),FILE_APPEND);

						$prepare_attach = [];

						if ( $attach_id ) {
							$countAttachments ++;
						}
					}
				}
			}
		}

		if ( ! empty( $post_type ) && $post_type == 'fre_profile' && ! empty( $attach_id ) ) {
			update_user_meta( $user_ID, 'cover', $attach_id );
			$post_img = get_post( $attach_id, OBJECT );
			update_user_meta( $user_ID, 'cover_url', $post_img->guid );
		}

		wp_send_json( [
			'success' => true,
			'msg'     => _( 'Success img' ),
		] );
	}
}

function get_total_adverts_per_month_user( $userId ) {
	global $wpdb;
	$month  = (int) date( 'm' );
	$userId = (int) $userId;
	$cPosts = $wpdb->get_var( "SELECT COUNT(p.ID) FROM {$wpdb->prefix}posts p
    WHERE p.post_author = {$userId} AND p.post_type = 'advert'
    AND DATE_FORMAT(p.post_date, '%m') = {$month}
    " );

	return (int) $cPosts;
}

function attach_advert_file( $file, $parent = 0, $mimes = [], $author = 0 ) {
	global $user_ID;
	$author = ( 0 == $author || ! is_numeric( $author ) ) ? $user_ID : $author;

	if ( isset( $file['name'] ) && $file['size'] > 0 ) {

		// setup the overrides
		$overrides['test_form'] = false;
		if ( ! empty( $mimes ) && is_array( $mimes ) ) {
			$overrides['mimes'] = $mimes;
		}

		// this function also check the filetype & return errors if having any
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$uploaded_file = wp_handle_upload( $file, $overrides );

		//if there was an error quit early
		if ( isset( $uploaded_file['error'] ) ) {
			return new WP_Error( 'upload_error', $uploaded_file['error'] );
		} elseif ( isset( $uploaded_file['file'] ) ) {

			// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
			$file_name_and_location = $uploaded_file['file'];

			$file_title_for_media_library = sanitize_file_name( $file['name'] );

			$wp_upload_dir = wp_upload_dir();

			// Set up options array to add this file as an attachment
			$attachment = [
				'guid'           => $uploaded_file['url'],
				'post_mime_type' => $uploaded_file['type'],
				'post_title'     => $file_title_for_media_library,
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_author'    => $author
			];

			$attach_id = wp_insert_attachment( $attachment, $file_name_and_location, $parent );

			return $attach_id;
		} else {
			return new WP_Error( 'upload_error', __( 'There was a problem with your upload.', ET_DOMAIN ) );
		}
	} else {
		return new WP_Error( 'upload_error', __( 'Where is the file?', ET_DOMAIN ) );
	}
}