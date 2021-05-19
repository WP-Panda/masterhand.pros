<?php
	$_cats = wpp_get_company_category();


	$array = [];

	foreach ( $_cats as $cat_id => $cat_name ):

		$array[ $cat_id ] = [
			'name' => $cat_name,
		];

		$data_sub = wpp_get_company_category( $cat_id );

		foreach ( $data_sub as $sub_id => $sub_name ) {
			$array[ $cat_id ][ 'sub_cat' ][ $sub_id ][ 'name' ] = $sub_name;
		}

	endforeach;

	$countries = wpp_get_company_location( '', 'country' );

	foreach ( $countries as $country_id => $country_name ) :
		$data_state = wpp_get_company_location( $country_id, 'state' );

		$array[ $country_id ] = [
			'name' => $country_name,
		];

		foreach ( $data_state as $state_id => $state_name ) {
			$array[ $country_id ][ 'sub_cat' ][ $state_id ][ 'name' ] = $state_name;

			$data_city = wpp_get_company_location( $state_id, 'city' );

			foreach ( $data_city as $city_id => $city_name ) {
				$array[ $country_id ][ 'sub_cat' ][ $state_id ][ 'sub_cat' ][ $city_id ][ 'name' ] = $city_name;
			}

		}

	endforeach;

	$data_category = wp_json_encode( $array, JSON_PARTIAL_OUTPUT_ON_ERROR );

	if ( ! empty( $_GET[ 'country' ] ) ) {
		$states = wpp_list_pluck_states( $_GET[ 'country' ] );
	} else {
		$states = wpp_list_pluck_states( (int) wpp_get_country( get_post_meta( get_queried_object_id(), 'company_in_country', true ) ) );
	}

	if ( ! empty( $_GET[ 'state' ] ) ) {
		$cities = wpp_list_pluck_cities( $_GET[ 'state' ] );
	}

	if ( ! empty( $_GET[ 'state' ] ) ) {
		$subs = wpp_get_company_category( $_GET[ 'cat' ] );
	}

?>
<script type="text/javascript">var CompanyFilter = <?php echo $data_category; ?></script>
<div class="fre-project-filter-box">

    <div class="project-filter-header visible-sm visible-xs">
        <a class="project-filter-title" href=""><?php _e( 'Company search', ET_DOMAIN ); ?></a>
    </div>
    <div class="fre-company-list-filter">
        <form id="wpp-filter-form" onsubmit="return false;">
            <input type="hidden" value="all" name="all" id="all" disabled>
            <div class="row">

                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="keywords" class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                        <input class="keyword search"
                               id="string"
                               type="text"
                               name="string"
                               value="<?php echo ! empty( $_GET[ 'string' ] ) ? esc_html( $_GET[ 'string' ] ) : ''; ?>"
                               placeholder="<?php _e( 'Search company by keyword', ET_DOMAIN ); ?>"
                        >
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="cat"
                               class="fre-field-title"><?php _e( 'Category', ET_DOMAIN ); ?></label>
                        <div class="select_style">
                            <select
                                    name="cat"
                                    id="cat"
                                    data-target-select="sub_cat"
                                    data-empty="Select Category"
                                    data-group="cat">
                                <option value="">Select category</option>
								<?php foreach ( $_cats as $cat_id => $cat_name ) :

									$selected = ! empty( $_GET[ 'cat' ] ) ? selected( $cat_id, $_GET[ 'cat' ], false ) : '';

									printf( '<option class="%s" value="%s"%s>%s</option>', sanitize_title( $cat_name ), $cat_id, $selected, $cat_name );
								endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="fre-input-field">
                        <label for="sub"
                               class="fre-field-title"><?php _e( 'Subcategory', ET_DOMAIN ); ?></label>
                        <div class="select_style">
                            <select name="sub"
                                    id="sub"
                                    data-parent-select="sub_cat"
                                    data-empty="Select category first"
                                    data-group="cat"
								<?php echo ! empty( $_GET[ 'cat' ] ) ? ' data-flag="' . (int) $_GET[ 'cat' ] . '"' : '' ?>
								<?php echo empty( $subs ) ? ' disabled' : ''; ?>
                            >
								<?php if ( $subs ) {
									echo '<option value="">Select category first</option>';
									foreach ( $subs as $sub_id => $sub_name ) :
										$selected = ! empty( $_GET[ 'sub' ] ) ? selected( $_GET[ 'sub' ], $sub_id, false ) : '';
										printf( '<option class="%s" value="%s"%s>%s</option>', sanitize_title( $sub_name ), $sub_id, $selected, $sub_name );
									endforeach;
								} else {
									echo '<option value="">First Category not available</option>';
								} ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="location-fields">
                    <div class="fre-input-field col-md-12">
                        <label for="country" class="fre-field-title"><?php _e( 'Location', ET_DOMAIN ); ?></label>
                    </div>
                    <div class="col-md-4">
                        <div class="fre-input-field">
                            <select name="country" id="country" data-target-select="state" data-empty="Select Country"
                                    data-group="location">
                                <option value="">Select Country</option>
								<?php
									if ( $countries ) {
										foreach ( $countries as $country_id => $country_name ) :
											$selected = ! empty( $_GET[ 'country' ] ) ? selected( $_GET[ 'country' ], $country_id, false ) : '';
											if ( empty( $selected ) && empty( $_GET[ 'all' ] ) ) {
												$selected = selected( (int) $country_id, (int) wpp_get_country( get_post_meta( get_queried_object_id(), 'company_in_country', true ) ), false );
											}
											printf( '<option class="%s" value="%s"%s>%s</option>', sanitize_title( $country_name ), $country_id, $selected, $country_name );
										endforeach;
									} else {
										echo '<option>Country not available</option>';
									}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fre-input-field">
                            <select name="state"
                                    id="state"
                                    data-target-select="city"
                                    data-parent-select="state"
                                    data-empty="Select State"
                                    data-group="location"
								<?php echo ! empty( $_GET[ 'country' ] ) ? ' data-flag="' . (int) $_GET[ 'country' ] . '"' : '' ?>
								<?php echo empty( $states ) ? ' disabled' : ''; ?>
                            >
								<?php
									if ( $states ) {
										echo '<option value="">Select State</option>';
										foreach ( $states as $state_id => $state_name ) :
											$selected = ! empty( $_GET[ 'state' ] ) ? selected( $_GET[ 'state' ], $state_id, false ) : '';
											printf( '<option class="%s" value="%s"%s>%s</option>', sanitize_title( $state_name ), $state_id, $selected, $state_name );
										endforeach;
									} else {
										echo '<option value="">Country not available</option>';
									}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fre-input-field">
                            <select name="city"
                                    id="city"
                                    data-parent-select="city"
                                    data-empty="Select City"
                                    data-group="location"
								<?php echo ! empty( $_GET[ 'state' ] ) ? ' data-flag="' . (int) $_GET[ 'state' ] . '"' : '' ?>
								<?php echo empty( $cities ) ? ' disabled' : ''; ?>
                            >
								<?php
									if ( $cities ) {
										echo '<option value="">Select City</option>';
										foreach ( $cities as $city_id => $city_name ) :
											$selected = ! empty( $_GET[ 'city' ] ) ? selected( $_GET[ 'city' ], $city_id, false ) : '';
											printf( '<option class="%s" value="%s"%s>%s</option>', sanitize_title( $city_name ), $city_id, $selected, $city_name );
										endforeach;
									} else {
										echo '<option value="">State not available</option>';
									}
								?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <a class="project-filter-clear clear-filter secondary-color"
               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
        </form>
    </div>
</div>