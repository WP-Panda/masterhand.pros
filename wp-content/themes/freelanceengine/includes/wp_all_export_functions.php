<?php
	function WPAE_getUserConverted( $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return false;
		}
		global $ae_post_factory;
		$post_object = $ae_post_factory->get( $post->post_type );
		var_dump( $post_object );
		$post_convert = $post_object->convert( $post );

		return $post_convert;
	}

	function WPAE_getCompanyCategories( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( " SELECT `name` FROM `wp_terms` WHERE `term_id` = '{$id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	function WPAE_getCompanySubcategories( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( " SELECT `name` FROM `wp_terms` WHERE `term_id` = '{$id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	function WPAE_userRole( $user_id ) {
		return ae_user_role( $user_id );
	}

	function WPAE_userReferral( $user_id ) {
		return get_referral_code_by_user( $user_id );
	}

	function WPAE_userFB( $user_id ) {
		return get_post_meta( $user_id, 'facebook', true );
	}

	function WPAE_userLN( $user_id ) {
		return get_post_meta( $user_id, 'linkedin', true );
	}

	function WPAE_userSkype( $user_id ) {
		return get_post_meta( $user_id, 'skype', true );
	}

	function WPAE_userViber( $user_id ) {
		return get_post_meta( $user_id, 'viber', true );
	}

	function WPAE_userWhatsapp( $user_id ) {
		return get_post_meta( $user_id, 'whatsapp', true );
	}

	function WPAE_userTelegram( $user_id ) {
		return get_post_meta( $user_id, 'telegram', true );
	}

	function WPAE_userWechat( $user_id ) {
		return get_post_meta( $user_id, 'wechat', true );
	}

	function WPAE_userInstallmentPlan( $user_id ) {
		return ( get_post_meta( $user_id, 'installmentPlan', true ) ) ? 'on' : 'off';
	}

	function WPAE_getCountry( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( " SELECT `name` FROM `wp_location_countries` WHERE `id` = '{$id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	function WPAE_getState( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( " SELECT `name` FROM `wp_location_states` WHERE `id` = '{$id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	function WPAE_getCity( $id ) {
		global $wpdb;
		$result = $wpdb->get_row( " SELECT `name` FROM `wp_location_cities` WHERE `id` = '{$id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	// Users
	// TODO: if user has more than 1 category, need to rewrite this function
	function WPAE_getUserCategories( $user_id ) {
		global $wpdb;
		// get user's subcategories (now only 1 subcategory)
		$result = $wpdb->get_row( " SELECT `term_taxonomy_id` FROM `wp_term_relationships` WHERE `object_id` = '{$user_id}' " );
		if ( empty( $result ) || $result->term_taxonomy_id == '' ) {
			return false;
		}
		// get subcategory parent (category)
		$tax_id = $result->term_taxonomy_id;
		$result = $wpdb->get_row( " SELECT `parent` FROM `wp_term_taxonomy` WHERE `term_taxonomy_id` = '{$tax_id}' " );
		if ( empty( $result ) || $result->parent == '' ) {
			return false;
		}
		// get category name
		$category_id = $result->parent;
		$result      = $wpdb->get_row( " SELECT `name` FROM `wp_terms` WHERE `term_id` = '{$category_id}' " );
		if ( empty( $result ) || $result->name == '' ) {
			return false;
		}

		return $result->name;
	}

	function WPAE_getUserCategoryID( $user_id ) {
		global $wpdb;
		// get user's subcategories (now only 1 subcategory)
		$result = $wpdb->get_row( " SELECT `term_taxonomy_id` FROM `wp_term_relationships` WHERE `object_id` = '{$user_id}' " );
		if ( empty( $result ) || $result->term_taxonomy_id == '' ) {
			return false;
		}
		// get subcategory parent (category)
		$tax_id = $result->term_taxonomy_id;
		$result = $wpdb->get_row( " SELECT `parent` FROM `wp_term_taxonomy` WHERE `term_taxonomy_id` = '{$tax_id}' " );
		if ( empty( $result ) || $result->parent == '' ) {
			return false;
		}

		return $result->parent;
	}

	function WPAE_getUserSubcategories( $user_id ) {
		global $wpdb;
		// get user's subcategories
		$subcategories       = [];
		$subcategories_array = $wpdb->get_results( " SELECT `term_taxonomy_id` FROM `wp_term_relationships` WHERE `object_id` = '{$user_id}' " );
		if ( empty( $subcategories_array ) ) {
			return false;
		}
		// fill subcategories array
		foreach ( $subcategories_array as $subcategory ) {
			$result = $wpdb->get_row( " SELECT `name` FROM `wp_terms` WHERE `term_id` = '{$subcategory->term_taxonomy_id}' " );
			if ( empty( $result ) || $result->name == '' ) {
				continue;
			}
			array_push( $subcategories, $result->name );
		}
		$subcategories = implode( '|', $subcategories );

		return $subcategories;
	}

	function WPAE_getUserEmail( $user_id ) {
		global $wpdb;
		// get user's subcategories (now only 1 subcategory)
		$result = $wpdb->get_row( " SELECT `user_email` FROM `wp_users` WHERE `ID` = '{$user_id}' " );
		if ( empty( $result ) || $result->user_email == '' ) {
			return false;
		}

		return $result->user_email;
	}

	function WPAE_getUserPhone( $user_id ) {
		global $wpdb;
		// get user's subcategories (now only 1 subcategory)
		$result = $wpdb->get_row( " SELECT `meta_value` FROM `wp_usermeta` WHERE `user_id` = '{$user_id}' AND `meta_key` = 'user_phone' " );
		if ( empty( $result ) || $result->meta_value == '' ) {
			return false;
		}

		return $result->meta_value;
	}

	function WPAE_getUserReviews( $user_id ) {
		global $wpdb;
		// get user's subcategories (now only 1 subcategory)
		$result = $wpdb->get_row( " SELECT * FROM `wp_pro_paid_users` WHERE `user_id` = '{$user_id}' " );
		if ( empty( $result ) || $result->txn_id == '' ) {
			return false;
		}

		return json_encode( $result );
	}

	function WPAE_getUserRating( $userId = 0 ) {
		$result                 = review_rating_init()->getRating( $userId );
		$data[ 'percent_vote' ] = 0;
		$data[ 'title' ]        = '';
		if ( $result[ 'votes' ] > 2 ) {
			$data[ 'percent_vote' ] = number_format( ( floatval( $result[ 'rating' ] ) * 20 ), 2 );
			$data[ 'title' ]        = "{$result['rating']}/" . review_rating_init()->getMaxScore() . " ({$data['percent_vote']}%)";
		} else {
			if ( userHaveProStatus( $userId ) ) {
				$data[ 'percent_vote' ] = number_format( ( floatval( 5 ) * 20 ), 2 );
				$data[ 'title' ]        = "{$result['rating']}/" . review_rating_init()->getMaxScore() . " ({$data['percent_vote']}%)";
			}
		}

		return $data[ 'title' ];
	}