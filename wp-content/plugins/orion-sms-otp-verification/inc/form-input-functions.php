<?php
/**
 * Function to generate Plugin Settings form
 *
 * @package Orion SMS OTP verification
 */

if (  ! function_exists( 'ihs_get_text_input' )  ) {
	/**
	 * Get the input html content.
	 * $input name is the same as the $option_name
	 *
	 * @param {string} $label_name Label name.
	 * @param {string} $input_name Input name.
	 */
	function ihs_get_text_input( $label_name, $input_name, $type = 'text', $required = true, $placeholder = '',
		$tooltip = false, $tooltip_text = '', $max_length = '', $default_val = '' ) {
		$inpt_name = get_option( $input_name );
		$option_val = ( ! empty( $inpt_name ) ) ? esc_attr( $inpt_name ) : $default_val;
		$required_attr = ( $required ) ? 'required' : '';
		$required = ( $required ) ? ' <span class="ihs-otp-red">*</span>' : '';

		// Label
		if ( $tooltip ) {
		    $label_content = '<strong class="d-block text-gray-dark ihs-tooltip-container">
									   ' . $label_name . $required .'
									   <i class="far fa-question-circle"></i>
									   <span class="ihs-tooltip-text">' . $tooltip_text . '</span>
									</strong>';
		} else {
			$label_content = '<strong class="d-block text-gray-dark ">' . $label_name .  $required .'</strong>';
		}

		// Input field
		if ( 'textarea' === $type ) {
			$input_field = '<textarea type="text" class="config-input-class" name="' . $input_name . '" placeholder="' . $placeholder .  '" cols="60" rows="3" ' . $required_attr . ' />' . $option_val . '</textarea>';
		} else if ( 'select' === $type ) {
			$input_field = ihs_get_country_code_content( $input_name, $option_val );
		} else {
			$input_field = '<input type="' . $type . '" class="config-input-class" name="' . $input_name . '" value="' . $option_val . '" placeholder="' . $placeholder .  '" maxlength=" ' . $max_length . ' " ' . $required_attr . ' />';
		}

		$content = '<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
						' . $label_content . '
						<label for="">
							' . $input_field . '
						</label>
					</div>';
		return $content;
	}
}

if ( ! function_exists( 'ihs_get_mobile_input_fields' ) ) {
	/**
	 * Get the mobile input field html content.
	 *
	 * @param {string} $input_name
	 * @return {string} Returns Mobile input field content.
	 */
	function ihs_get_mobile_input_fields( $input_name_req = 'ihs_otp_mobile_input_required', $input_name = 'ihs_otp_mobile_input_name' ) {
		$checked_array = ihs_get_checked_val();
		$hide = ( $checked_array['checked-yes'] ) ? 'ihs-otp-hide' : '';
		$content = '<div class="media text-muted pt-3">
					<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
						<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
							<strong class="d-block text-gray-dark ">CREATE MOBILE INPUT FIELD : <span class="ihs-otp-red">*</span></strong>
							<label for="" class="ihs-mobile-input-label">
								<input type="radio" name="' . $input_name_req . '" class="ihs-otp-mob-input ihs-yes config-input-class" value="Yes" ' . esc_attr( $checked_array['checked-yes'] ) . '/>Yes
								<input type="radio" name="' . $input_name_req . '" class="ihs-otp-mob-input config-input-class ml-1" value="No" ' . esc_attr( $checked_array['checked-no'] ) . '/>No
							</label>
						</div>
					</div>';
		$content .= '<div class="media text-muted pt-3" id="ihs_otp_mobile_input_name">
						<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
							<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray '. esc_html( $hide ) .  '">
							<strong class="d-block text-gray-dark ihs-tooltip-container">
								MOBILE INPUT NAME: <span class="ihs-otp-red">*</span>
								<i class="far fa-question-circle"></i>
								<span class="ihs-tooltip-text">If your form already has an input field enter the input field name here.</span>
							</strong>
							<label for="">
								<input type="text" name="' . $input_name . '" class="ihs_otp_mob_input_name config-input-class" value="' . esc_attr( get_option( $input_name ) ) . '" placeholder="e.g. inputname" required />
							</label>
						</div>
					</div>';

		return $content;

	}
}

if ( ! function_exists( 'ihs_get_woo_mobile_input_fields' ) ) {
	/**
	 * Get the mobile input field html content.
	 *
	 * @param {string} $input_name
	 * @return {string} Returns Mobile input field content.
	 */
	function ihs_get_woo_mobile_input_fields( $input_name_req , $input_name ) {
		// Hidden the Create mobile input field for woo-commerce settings using .d-none class.
		$content = '<div class="media text-muted pt-3 d-none">
					<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
						<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
							<strong class="d-block text-gray-dark ">CREATE MOBILE INPUT FIELD : <span class="ihs-otp-red">*</span></strong>
							<label for="" class="ihs-mobile-input-label">
								<input type="radio" name="' . $input_name_req . '" class="ihs-otp-mob-input config-input-class ml-1" value="No" checked />No
							</label>
						</div>
					</div>';
		$inpt_name = get_option( $input_name );
		$mobile_input_name_val = ( ! empty( $inpt_name ) ) ? $inpt_name : 'billing_phone' ;
		$content .= '<div class="media text-muted pt-3" id="ihs_otp_mobile_input_name">
						<div class="ihs-input-icon ihs-bg-purple d-flex"><i class="ihs-my-icons fas fa-phone-square" aria-hidden="true"></i></div>
							<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
							<strong class="d-block text-gray-dark ihs-tooltip-container">
								MOBILE INPUT NAME: <span class="ihs-otp-red">*</span>
								<i class="far fa-question-circle"></i>
								<span class="ihs-tooltip-text">If your form already has an input field enter the input field name here.</span>
							</strong>
							<label for="">
								<input type="text" name="' . $input_name . '" class="ihs_otp_mob_input_name config-input-class" value="' . esc_attr( $mobile_input_name_val ) . '" placeholder="e.g. billing_phone " required />
							</label>
						</div>
					</div>';

		return $content;

	}
}

if ( ! function_exists( 'ihs_get_tell_me_how_link' ) ) {
	/**
	 * Get the tell me how link.
	 *
	 * @param {string} $link_text Link text.
	 * @param {string} $link Link.
	 *
	 * @return {string} $content Html link for the you tube tutorial.
	 */
	function ihs_get_tell_me_how_link( $link_text, $link ) {
		$content = ' <a href="' . esc_url( $link ) . '" target="_blank" class="tell-me-hw-link">
						<i class="fab fa-youtube ihs-you-tube-icon"></i>
						' . $link_text . '
						<i class="far fa-question-circle"></i>
					</a>';
		return $content;
	}
}

if ( ! function_exists( 'ihs_get_video_cards' ) ) {
	/**
	 * Display the Video html content.
	 *
	 * @param {string} $title Title.
	 * @param {string} $description Description.
	 * @param {string} $link Link.
	 */
    function ihs_get_video_cards( $title, $description,  $link ) {
    	?>
	    <!-- Card -->
	    <div class="ihs-video-card card">
		    <!-- Card image -->
		    <div class="view overlay">
			    <div class="embed-responsive embed-responsive-16by9">
				    <iframe class="embed-responsive-item" src="<?php echo $link; ?>" allowfullscreen></iframe>
			    </div>
			    <a>
				    <div class="mask rgba-white-slight"></div>
			    </a>
		    </div>
		    <!-- Social buttons -->
		    <div class="card-share">
			    <!-- Button action -->
			    <a class="btn-floating btn-action ihs-video-share-link share-toggle indigo ml-auto mr-4 float-right"><i class="fab ihs-share-icon fa-youtube"></i></a>
		    </div>
		    <!-- Card content -->
		    <div class="card-body pt-0">
			    <!-- Title -->
			    <h4 class="card-title mb-0"><?php echo $title; ?></h4>
			    <hr>
			    <!-- Text -->
			    <p class="card-text"><?php echo $description; ?></p>
			    <a href="<?php echo $link; ?>" target="_blank"><button class="ihs-video-read-mr-btn btn btn-indigo btn-rounded btn-md">More</button></a>
		    </div>
	    </div>
	    <!-- Card -->
	<?php

    }
}

if ( ! function_exists( 'ihs_get_api_type' ) ) {
	/**
	 * Display the Api Type( msg91 or twilio
	 *
	 * @param {string} $label_name Label.
	 * @param {string} $input_name Input Name.
	 * @param {bool} $required Required.
	 * @param {bool} $tooltip Tooltip.
	 * @param {string} $tooltip_text Tooltip text.
	 *
	 * @return string
	 */
	function ihs_get_api_type( $label_name, $input_name, $required = true, $tooltip = false, $tooltip_text = '' ) {

		$option_val = esc_attr( get_option( $input_name ) );
		$required = ( $required ) ? ' <span class="ihs-otp-red">*</span>' : '';

		// Label
		if ( $tooltip ) {
			$label_content = '<strong class="d-block text-gray-dark ihs-tooltip-container">
									   ' . $label_name . $required .'
									   <i class="far fa-question-circle"></i>
									   <span class="ihs-tooltip-text">' . $tooltip_text . '</span>
									</strong>';
		} else {
			$label_content = '<strong class="d-block text-gray-dark ">' . $label_name .  $required .'</strong>';
		}

		$selected_msg91_api = ( 'msg91' === $option_val ) ? 'selected' : '';
		$selected_twilio_api = ( 'twilio' === $option_val ) ? 'selected' : '';

		$content = '<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
						' . $label_content . '
						<label for="">
							<select name="' . $input_name . '"  class="config-input-class" id="ihs-select-api-type">
							<option value="">' . __( 'Select API', 'orion-sms-orion-sms-otp-verification' ) . '</option>
							<option class="ihs-msg91-option" value="msg91" ' . $selected_msg91_api . '>MSG91</option>
							<option class="ihs-twilio-option" value="twilio" ' . $selected_twilio_api . ' >Twilio</option>
							</select>
						</label>
					</div>';
		return $content;
	}
}

if ( ! function_exists( 'ihs_get_route_drop_down' ) ) {
	/**
	 * Display the Route Dropdown html content.
	 *
	 * @param {string} $label_name Label.
	 * @param {string} $input_name Input Name.
	 * @param {bool} $required Required.
	 * @param {bool} $tooltip Tooltip.
	 * @param {string} $tooltip_text Tooltip text.
	 *
	 * @return string
	 */
	function ihs_get_route_drop_down( $label_name, $input_name, $required = true, $tooltip = false, $tooltip_text = '' ) {

		$option_val = esc_attr( get_option( $input_name ) );
		$required = ( $required ) ? ' <span class="ihs-otp-red">*</span>' : '';

		// Label
		if ( $tooltip ) {
			$label_content = '<strong class="d-block text-gray-dark ihs-tooltip-container">
									   ' . $label_name . $required .'
									   <i class="far fa-question-circle"></i>
									   <span class="ihs-tooltip-text">' . $tooltip_text . '</span>
									</strong>';
		} else {
			$label_content = '<strong class="d-block text-gray-dark ">' . $label_name .  $required .'</strong>';
		}

		$selected_otp_route = ( 'otp-route' === $option_val ) ? 'selected' : '';
		$selected_transactional_route = ( 'transactional-route' === $option_val ) ? 'selected' : '';

		$content = '<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
						' . $label_content . '
						<label for="">
							<select name="' . $input_name . '"  class="config-input-class" id="">
							<option value="otp-route" ' . $selected_otp_route . '>OTP Route</option>
							<option value="transactional-route" ' . $selected_transactional_route . ' >Transactional Route</option>
							</select>
						</label>
					</div>';
		return $content;
	}
}

if ( ! function_exists( 'ihs_is_saved_with_country_code' ) ) {
	function ihs_is_saved_with_country_code( $label_name, $input_name, $required = true, $tooltip = false, $tooltip_text = '' ) {
		$option_val = esc_attr( get_option( $input_name ) );
		$required = ( $required ) ? ' <span class="ihs-otp-red">*</span>' : '';

		// Label
		if ( $tooltip ) {
			$label_content = '<strong class="d-block text-gray-dark ihs-tooltip-container">
									   ' . $label_name . $required .'
									   <i class="far fa-question-circle"></i>
									   <span class="ihs-tooltip-text">' . $tooltip_text . '</span>
									</strong>';
		} else {
			$label_content = '<strong class="d-block text-gray-dark ">' . $label_name .  $required .'</strong>';
		}

		$selected_yes = ( 'Yes' === $option_val ) ? 'selected' : '';
		$selected_no = ( 'No' === $option_val ) ? 'selected' : '';

		$content = '<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
						' . $label_content . '
						<label for="">
							<select name="' . $input_name . '"  class="config-input-class" id="">
							<option value="Yes" ' . $selected_yes . '>Yes</option>
							<option value="No" ' . $selected_no . ' >No</option>
							</select>
						</label>
					</div>';
		return $content;
	}
}

if ( ! function_exists( 'ihs_order_sms_checkbox_template' ) ) {
	/**
	 * Return the checkbox for Order SMS
	 *
	 * @param $label_name
	 * @param $input_name
	 *
	 * @return string
	 */
    function ihs_order_sms_checkbox_template( $label_name, $input_name ) {
    	$admin_input_name = $input_name . "[admin]";
	    $customer_input_name = $input_name . "[customer]";
		$option_val = get_option( $input_name );
		
    	$admin_option_val = ( isset($option_val['admin']) ) ? esc_attr( $option_val['admin'] ) : '';
	    $customer_option_val = ( isset($option_val['customer']) ) ? esc_attr( $option_val['customer'] ) : '';
    	$admin_selected = ( ! empty( $admin_option_val ) && 'admin' === $admin_option_val ) ? 'checked' : '';
	    $customer_selected = ( ! empty( $customer_option_val ) && 'customer' === $customer_option_val ) ? 'checked' : '';

		$content =
			'<div class="media text-muted pt-3">
				<div class="ihs-input-icon ihs-bg-purple d-flex" style="margin-top: -6px;"><i class="fas fa-bell ihs-my-icons"></i></div>
				<div class="d-sm-flex media-body ihs-input-wrap pb-3 mb-0 small lh-125 border-bottom border-gray">
					<strong class="d-block text-gray-dark ihs-tooltip-container">
						ORDER ' . $label_name . ' SMS 
						<i class="far fa-question-circle"></i>
						<span class="ihs-tooltip-text">Please check who should receive the sms when the ORDER IS ' . $label_name . '</span>
					</strong>
					<label class="mr-3"><input class="" type="checkbox" name="' . $admin_input_name . '" value="admin" ' . $admin_selected . '>Admin</label>
					<label class=""><input class="" type="checkbox" name="' . $customer_input_name . '" value="customer" ' . $customer_selected . '>Customer</label>
				</div>
			</div>';
		return $content;
    }
}

if ( ! function_exists( 'ihs_order_sms_template' ) ) {
	/**
	 * Return the template for Order SMS
	 *
	 * @param $label_name
	 * @param $input_name
	 *
	 * @return string
	 */
	function ihs_order_sms_template( $label_name, $input_name, $placeholder ) {
		$label = 'ORDER ' . $label_name . ' TEMPLATE';
		$tooltip_text = 'Please enter the message here that customer will receive on when the order is ' . $label_name;
		$content =
			'<div class="media text-muted pt-3">
				<div class="ihs-input-icon ihs_otp_template_textarea ihs-bg-pink d-flex"><i class="ihs-my-icons fas fa-envelope" aria-hidden="true"></i></div>
				' . ihs_get_text_input( $label, $input_name,
					'textarea', false, $placeholder,
					true, $tooltip_text ) . '
			</div>';

		return $content;
	}
}
