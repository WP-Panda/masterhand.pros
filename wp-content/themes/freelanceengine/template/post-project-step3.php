<?php
global $user_ID;
$step         = 3;
$class_active = '';
$disable_plan = ae_get_option( 'disable_plan', false );

if ( $disable_plan ) {
	$step --;
	$class_active = 'active';
}

if ( $user_ID ) {
	$step --;
}

$post          = '';
$user_currency = get_user_meta( $user_ID, 'currency', true );
$user_currency = $user_currency == '' ? 'USD' : $user_currency;

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<div id="fre-post-project-2"
     class="fre-post-project-step step-wrapper hidden step-post <?php echo $class_active; ?>">
	<?php
	$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;
	if ( $id ) {
		$post = get_post( $id );
		if ( $post ) {
			global $ae_post_factory;
			$post_object  = $ae_post_factory->get( $post->post_type );
			$post_convert = $post_object->convert( $post );
			echo '<script type="data/json"  id="edit_postdata">' . json_encode( $post_convert ) . '</script>';
			$selected_currency = get_post_meta( $post->ID, 'project_currency', true );
		}
	}
	if ( ! $disable_plan ) {
		$total_package = ae_user_get_total_package( $user_ID );
		?>
        <div class="fre-post-project-box">
            <div class="row step-change-package">
                <div class="col-sm-10 col-xs-12">
                    <div class="package_title">
						<?php _e( 'You are selecting the package:', ET_DOMAIN ); ?>
                        <a data-toggle="collapse" href="#packinfo" role="button"><strong></strong></a>
                    </div>
                    <div id="packinfo" class="collapse pack-desk">
                        <p>
							<?php _e( 'The number of posts included in this package will be added to your total post after this project is posted.', ET_DOMAIN ) ?>
                        </p>
                        <p>
							<?php _e( 'Number posts limit and detail of your purchased package.', ET_DOMAIN ); ?>
                        </p>
                        <p class="pack-left">
							<?php printf( __( 'You have <span>%s post(s)</span> left', ET_DOMAIN ), $total_package ); ?>
                        </p>
                    </div>
                </div>
                <div class="col-sm-2 col-xs-12">
                    <a class="fre-post-project-previous-btn fre-btn-previous fre-submit-btn" href="#">
						<?php _e( 'Change package', ET_DOMAIN ); ?>
                    </a>
                </div>
            </div>
        </div>
	<?php } ?>
    <div class="fre-post-project-box">
        <form class="post" role="form">
            <div class="step-post-project" id="fre-post-project">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <label class="fre-field-title"><?php _e( 'What categories do your project work in?', ET_DOMAIN ); ?></label>
                    </div>
					<?php
					$subcategory_project_selected = $category_project_selected = null;
					if ( ! empty( $post_convert->tax_input['project_category'] ) ) {
						foreach ( $post_convert->tax_input['project_category'] as $key => $value ) {
							$tax                            = $value;
							$sub                            = get_term( $tax->term_id, 'project_category' );
							$subcategory_project_selected[] = $sub->slug;
							if ( $key == 0 ) {
								$cat                       = get_term( $tax->parent, 'project_category' );
								$category_project_selected = $cat->slug;
							}
						}
					}
					?>
                    <div class="col-md-12 col-xs-12">
                        <div class="fre-input-field">
                            <input type="hidden" name="type_filter" value="project_category">
                            <input type="hidden" name="crete_project" value="1">
                            <label for="cat" class="fre-field-title"><?php _e( 'Category', ET_DOMAIN ); ?></label>
                            <div class="select_style"><?php ae_tax_dropdown( 'project_category', [
									'attr'            => 'data-selected_slug="' . $category_project_selected . '"',
									'show_option_all' => __( "Select category", ET_DOMAIN ),
									'class'           => 'required',
									'hide_empty'      => false,
									'hierarchical'    => false,
									'id'              => 'cat',
									'value'           => 'slug',
									'parent'          => 0,
									'name'            => 'cat',
								] ); ?></div>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="fre-input-field">
                            <label for="sub" class="fre-field-title"><?php _e( 'Subcategory', ET_DOMAIN ); ?></label>
                            <div class="select_style">
                                <select name="project_category" id="sub"
                                        data-selected_slug="<?php echo $subcategory_project_selected !== null ? implode( ',', $subcategory_project_selected ) : '' ?>"
                                        class="required fre-chosen-category"
                                        data-chosen-width="100%" data-chosen-disable-search="" multiple
                                        data-limit="<?php echo ae_get_option( 'max_cat', 5 ) ?>"
                                        data-placeholder="<?php echo sprintf( __( "Choose maximum %s subcategories", ET_DOMAIN ), ae_get_option( 'max_cat', 5 ) ) ?>">
                                    <option value="">Select category first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fre-input-field">
                    <label class="fre-field-title"
                           for="fre-project-title"><?php _e( 'Your project title', ET_DOMAIN ); ?></label>
                    <input class="input-item text-field" id="fre-project-title" type="text" name="post_title">
                </div>
                <div class="fre-input-field">
                    <label class="fre-field-title"
                           for="fre-project-describe"><?php _e( 'Describe what you need done', ET_DOMAIN ); ?></label>
					<?php wp_editor( '', 'post_content', ae_editor_settings() ); ?>
                </div>
                <div class="fre-input-field" id="gallery_place">
                    <label class="fre-field-title" for=""><?php _e( 'Attachments (optional)', ET_DOMAIN ); ?></label>
                    <div class="edit-gallery-image" id="gallery_container">
                        <ul class="fre-attached-list gallery-image carousel-list" id="image-list"></ul>
                        <div id="carousel_container">
                            <a href="javascript:void(0)" style="display: block"
                               class="img-gallery fre-project-upload-file fre-submit-btn" id="carousel_browse_button">
								<?php _e( "Upload Files", ET_DOMAIN ); ?>
                            </a>
                            <span class="et_ajaxnonce hidden"
                                  id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                        </div>
                        <p class="fre-allow-upload">
							<?php _e( '(Upload maximum 5 files with extensions including png, jpg, pdf, xls, and doc format)', ET_DOMAIN ); ?>
                        </p>
                    </div>
                </div>
                <div class="fre-input-field">
                    <label class="fre-field-title"
                           for="project-budget"><?php _e( 'Your project budget', ET_DOMAIN ) ?></label>
                    <div class="fre-project-budgets">
                        <select name="project_currency">
							<?php
							foreach ( get_currency() as $key => $data ) {
								$is_selected = '';
								if ( ! isset( $selected_currency ) || empty( $selected_currency ) ) {
									if ( $user_currency == $data['code'] ) {
										$is_selected = 'selected';
									}
								} else {
									if ( $selected_currency == $data['code'] ) {
										$is_selected = 'selected';
									}
								} ?>
                                <option data-icon="<?php echo $data['flag'] ?>" <?php echo $is_selected ?>>
									<?php echo $data['code'] ?>
                                </option>
							<?php }
							?>
                        </select>
                        <input id="project-budget" type="number" placeholder="1"
                               class="input-item text-field is_number numberVal"
                               pattern="-?(\d+|\d+.\d+|.\d+)([eE][-+]?\d+)?"
                               onkeydown="if (event.keyCode == 16 || event.keyCode == 69 || event.keyCode == 189) return false"
                               name="et_budget" min="1">
                    </div>
                </div>
                <div class="fre-input-field">
                    <label class="fre-field-title" for="project-location"><?php _e( 'Location', ET_DOMAIN ); ?></label>
                    <div>
						<?php
						if ( ! empty( $post_convert->country ) ) {
							$location['country']['id'] = $post_convert->country;
							if ( ! empty( $post_convert->state ) ) {
								$location['state']['id'] = $post_convert->state;
								if ( ! empty( $post_convert->city ) ) {
									$location['city']['id'] = $post_convert->city;
								}
							}
						} else {
							$location = getLocation( $user_ID );
						}
						include 'dbConfig.php';
						$query_country = $db->query( "SELECT * FROM wp_location_countries ORDER BY name ASC" );
						?>
                        <div class="fre-input-field select">
                            <select name="country" id="country"
                                    data-selected_id="<?php echo ! empty( $location['country']['id'] ) ? $location['country']['id'] : '' ?>"
                                    data_user_country_id="<?php echo ! empty( $location['country']['id'] ) ? $location['country']['id'] : '' ?>">
                                <option value="">Select Country</option>
								<?php if ( $query_country->num_rows > 0 ) {
									while ( $row = $query_country->fetch_assoc() ) {
										if ( ! empty( $location['country'] ) && $location['country']['id'] == $row['id'] ) {
											$flag = 'selected';
										} else {
											$flag = '';
										}
										echo '<option value="' . $row['id'] . '"' . $flag . '>' . $row['name'] . '</option>';
									}
								} else {
									echo '<option value="">Country not available</option>';
								} ?>
                            </select>
                        </div>
                        <div class="fre-input-field select">
                            <select name="state" id="state"
                                    data-selected_id="<?php echo ! empty( $location['state']['id'] ) ? $location['state']['id'] : '' ?>">
                                <option value="">Select country first</option>
                            </select>
                        </div>
                        <div class="fre-input-field select">
                            <select name="city" id="city"
                                    data-selected_id="<?php echo ! empty( $location['city']['id'] ) ? $location['city']['id'] : '' ?>">
                                <option value="">Select state first</option>
                            </select>
                        </div>
                    </div>
                </div>
				<?php get_template_part( 'template/pro-option-for-project' ); ?>
                <div class="fre-input-field">
                    <div class="checkline">
                        <input id="request_quote_company" name="request_quote_company" type="checkbox" value="1">
                        <label for="request_quote_company"><?php _e( 'Request a Quote from businesses in the town on my behalf', ET_DOMAIN ) ?></label>
                        <div class="tooltip_wp">
                            <i>?</i>
                            <div class="tip"><?php _e( 'Check if you want to receive more offers\quotes', ET_DOMAIN ) ?></div>
                        </div>
                    </div>
                </div>
				<?php
				// Add hook: add more field
				echo '<ul class="fre-custom-field">';
				do_action( 'ae_submit_post_form', PROJECT, $post );
				echo '</ul>';
				?>
                <div class="fre-post-project-btn">
                    <button
                            class="fre-btn fre-post-project-next-btn fre-submit-btn"
                            type="submit"><?php _e( "Submit Project", ET_DOMAIN ); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>