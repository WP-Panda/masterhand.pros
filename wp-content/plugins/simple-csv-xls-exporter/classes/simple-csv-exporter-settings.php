<?php
	/**
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * @project Simple CSV Exporter
	 */

	/** Prevents this file from being called directly */
	if(!function_exists("add_action")) {
		return;
	}

	if(!class_exists('Simple_CSV_Exporter_Settings')) {
		/**
		 * Class Simple_CSV_Exporter_Settings
		 */
		class Simple_CSV_Exporter_Settings {
			public function __construct() {
				add_action('admin_init', array(&$this, 'admin_init'));
				add_action('admin_menu', array(&$this, 'add_menu'));
			}

			public function admin_init() {
				register_setting('wp_ccsve-group', 'ccsve_delimiter');
				register_setting('wp_ccsve-group', 'ccsve_admin_only');
				register_setting('wp_ccsve-group', 'ccsve_post_type');
				register_setting('wp_ccsve-group', 'ccsve_specified_posts');
				register_setting('wp_ccsve-group', 'ccsve_post_status');
				register_setting('wp_ccsve-group', 'ccsve_std_fields');
				register_setting('wp_ccsve-group', 'ccsve_tax_terms');
				register_setting('wp_ccsve-group', 'ccsve_custom_fields');
				register_setting('wp_ccsve-group', 'ccsve_woocommerce_fields');
				//since 1.5.4.2 - July 11, 2020
				register_setting('wp_ccsve-group', 'ccsve_date_min');

				add_settings_section(
					'simple_csv_exporter_settings-section',
					__("CSV/XLS Exporter Settings", TEXTDOMAIN),
					array(&$this, 'settings_section_simple_csv_exporter_settings'),
					'Simple_CSV_Exporter_Settings'
				);

				add_settings_field(
					'ccsve_delimiter',
					__("CSV delimiter", TEXTDOMAIN),
					array(&$this, 'settings_field_input_delimiter'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_admin_only',
					__("Only allow export from backend (ALL logged in users)", TEXTDOMAIN),
					array(&$this, 'settings_field_select_admin'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_post_type',
					__('Post type to export', TEXTDOMAIN),
					array(&$this, 'settings_field_select_post_type'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_specified_posts',
					__("Export specified posts", TEXTDOMAIN),
					array(&$this, 'settings_field_select_specified_posts'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_post_status',
					__("Post statuses to export", TEXTDOMAIN),
					array(&$this, 'settings_field_select_post_status'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_std_fields',
					__("Standard WP fields to export", TEXTDOMAIN),
					array(&$this, 'settings_field_select_std_fields'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_custom_fields',
					__("Custom fields to export", TEXTDOMAIN),
					array(&$this, 'settings_field_select_custom_fields'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				add_settings_field(
					'ccsve_tax_terms',
					__("Taxonomy terms to export", TEXTDOMAIN),
					array(&$this, 'settings_field_select_tax_terms'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);

				// WOO COMMERCE
				/*add_settings_field(
					 'ccsve_woocommerce_fields',
					 'WooCommerce Fields to Export',
					 array(&$this, 'settings_field_select_woocommerce_fields'),
					 'simple_csv_exporter_settings',
					 'simple_csv_exporter_settings-section'
				);*/
				
				//since 1.5.4.2 - July 11, 2020
				add_settings_field(
					'ccsve_date_min',
					__("Minimum Created Date", TEXTDOMAIN),
					array(&$this, 'settings_field_select_date_min'),
					'Simple_CSV_Exporter_Settings',
					'simple_csv_exporter_settings-section'
				);
				
			}

			/**
			 *
			 */
			public function settings_field_input_delimiter() {
				$csv_delimiter = get_option('ccsve_delimiter');

				if(!$csv_delimiter) {
					$csv_delimiter = '|';
				}
				?>
            <p>
               <input type="text" id="csv_delimiter" placeholder="<?php _e("Enter delimiter...", TEXTDOMAIN); ?>" class="widefat" name="ccsve_delimiter" value="<?php echo $csv_delimiter; ?>"/>
            </p>
            <p class="description">
					<?php _e("Enter the delimiter that is being used to separate each column in .csv files.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			public function settings_field_select_admin() {
				$admin_only = get_option('ccsve_admin_only');

				if(empty($admin_only)) {
					$admin_only = 'No';
				}

				$admin_options = array(
					__("No", TEXTDOMAIN)  => "No",
					__("Yes", TEXTDOMAIN) => "Yes"
				);

				foreach($admin_options as $label => $admin_option) {
					$checked = checked($admin_option, $admin_only, false);
					?>
               <p>
                  <input type="radio" id="admin_only radio-<?php echo $admin_option; ?>" name="ccsve_admin_only" value="<?php echo $admin_option; ?>" <?php echo $checked; ?>>
                  <label for="admin_only radio-<?php echo $admin_option; ?>">
							<?php echo $label; ?>
                  </label>
               </p>
					<?php
				}
			}

			public function settings_field_select_post_type() {
				$args = array(
					'public'              => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => true
				);

				/** Get the field name from the $args array */
				$post_types = get_post_types(
					$args,
					'objects',
					'or'
				);

				/** Get the value of this setting */
				$options = get_option('ccsve_post_type');

				foreach($post_types as $post_type) {
					$checked            = checked($post_type->name, $options, false);
					$posts_in_post_type = get_posts("showposts=-1&post_type=" . $post_type->name);
					$posts_in_post_type = count($posts_in_post_type);
					?>
               <p>
                  <input type="radio" id="post_type_<?php echo $post_type->name; ?>" name="ccsve_post_type" value="<?php echo $post_type->name; ?>" <?php echo $checked; ?>>
                  <label for="post_type_<?php echo $post_type->name; ?>">
							<?php echo $post_type->label ?>
                     <span class="small">
                        (<?php echo sprintf(_n("1 post", "%s posts", $posts_in_post_type), $posts_in_post_type); ?>)
                     </span>
                  </label>
               </p>
					<?php
				}
			}

			public function settings_field_select_specified_posts() {
				$specific_posts = get_option('ccsve_specified_posts');
				if(!$specific_posts) {
					$specific_posts = '';
				}

				$placeholder = __("Please enter the IDs of the posts you would like to export...", TEXTDOMAIN)
				?>
            <p>
               <input type="text" class="widefat" placeholder="<?php echo $placeholder; ?>" id="specific_posts" name="ccsve_specified_posts" value="<?php echo $specific_posts; ?>">
            </p>
            <p class="description">
					<?php _e("Only export posts with the inserted post IDs.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			public function settings_field_select_post_status() {
				$args              = array();
				$ccsve_post_status = get_option('ccsve_post_status');

				// If Status selection is empty
				if($ccsve_post_status == '' || is_null($ccsve_post_status)) {
					/*$ccsve_post_status = array();
					$ccsve_post_status = array('selectinput' => array(0 => 'any'));*/
				}

				$statuses              = get_post_stati($args, 'object', 'or');
				$ccsve_post_status_num = count($statuses) + 1;

				if($ccsve_post_status_num > 15) {
					$ccsve_post_status_num = 15;
				}

				$selected_post_statuses = get_option('ccsve_post_status');

				if(!isset($selected_post_statuses["selectinput"])) {
					$selected_post_statuses["selectinput"] = array();
				}

				$selected_post_statuses = $selected_post_statuses["selectinput"];
				$any_selected           = '';

				if(!count($selected_post_statuses) || in_array("any", $selected_post_statuses, true)) {
					$any_selected = ' selected="selected"';
				}
				?>
            <select multiple="multiple" class="widefat" name="ccsve_post_status[selectinput][]" id="specified-post-ids" size="<?php echo $ccsve_post_status_num; ?>">
               <option value="any"<?php echo $any_selected; ?>>
						<?php _e("- Any -", TEXTDOMAIN); ?>
               </option>
					<?php
						foreach($statuses as $status) {
							$selected = '';

							if(in_array($status->name, $selected_post_statuses, true)) {
								$selected = ' selected="selected"';
							}
							?>
                     <option value="<?php echo $status->name; ?>"<?php echo $selected; ?>>
								<?php echo $status->label; ?>
                     </option>
							<?php
						}
					?>
            </select>
            <p class="description">
					<?php _e("Select multiple entries by holding your CTRL (Windows) or &#8984; (macOS) key.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			public function settings_field_select_std_fields() {
				$ccsve_post_type   = get_option('ccsve_post_type');
				$ccsve_post_status = get_option('ccsve_post_status');

				// If Status selection is empty
				if($ccsve_post_status == '' || is_null($ccsve_post_status)) {
					$ccsve_post_status = array();
					$ccsve_post_status = array('selectinput' => array(0 => 'any'));
				}

				$fields               = generate_std_fields($ccsve_post_type, $ccsve_post_status);
				$ccsve_std_fields     = get_option('ccsve_std_fields');
				$ccsve_std_fields_num = count($fields);

				if($ccsve_std_fields_num > 15) {
					$ccsve_std_fields_num = 15;
				}

				echo '<select multiple="multiple" class="widefat" size="' . $ccsve_std_fields_num . '" name="ccsve_std_fields[selectinput][]">';
				foreach($fields as $field) {
					if($ccsve_std_fields['selectinput'] != null && in_array($field, $ccsve_std_fields['selectinput'])) {
						echo '\n\t<option selected="selected" value="' . $field . '">' . $field . '</option>';
					}
					else {
						echo '\n\t\<option value="' . $field . '">' . $field . '</option>';
					}
				}
				?>
            </select>
            <p class="description">
					<?php _e("Select multiple entries by holding your CTRL (Windows) or &#8984; (macOS) key.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			public function settings_field_select_tax_terms() {
				$ccsve_post_type     = get_option('ccsve_post_type');
				$object_tax          = get_object_taxonomies($ccsve_post_type, 'names');
				$ccsve_tax_terms     = get_option('ccsve_tax_terms');
				$ccsve_tax_terms_num = count($object_tax);

				if($ccsve_tax_terms_num > 15) {
					$ccsve_tax_terms_num = 15;
				}

				echo '<select multiple="multiple" class="widefat" size="' . $ccsve_tax_terms_num . '" name="ccsve_tax_terms[selectinput][]">';
				foreach($object_tax as $tax) {
					if(in_array($tax, $ccsve_tax_terms['selectinput'])) {
						echo '\n\t<option selected="selected" value="' . $tax . '">' . $tax . '</option>';
					}
					else {
						echo '\n\t\<option value="' . $tax . '">' . $tax . '</option>';
					}
				}
				?>
            </select>
            <p class="description">
					<?php _e("Select multiple entries by holding your CTRL (Windows) or &#8984; (macOS) key.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			public function settings_field_select_custom_fields() {
				$ccsve_post_type     = get_option('ccsve_post_type');
				$meta_keys           = generate_post_meta_keys($ccsve_post_type);
				$ccsve_custom_fields = get_option('ccsve_custom_fields');
				$ccsve_meta_keys_num = count($meta_keys);

				if($ccsve_meta_keys_num > 15) {
					$ccsve_meta_keys_num = 15;
				}

				// Todo:

				echo '<select multiple="multiple" class="widefat" size="' . $ccsve_meta_keys_num . '" name="ccsve_custom_fields[selectinput][]">';
				foreach($meta_keys as $meta_key) {
					if(in_array($meta_key, $ccsve_custom_fields['selectinput'])) {
						echo '\n\t<option selected="selected" value="' . $meta_key . '">' . $meta_key . '</option>';
					}
					else {
						echo '\n\t\<option value="' . $meta_key . '">' . $meta_key . '</option>';
					}
				}
				?>
            </select>
            <p class="description">
					<?php _e("Select multiple entries by holding your CTRL (Windows) or &#8984; (macOS) key.", TEXTDOMAIN); ?>
            </p>
				<?php
			}

			// WOO COMMERCE
			/*public function settings_field_select_woocommerce_fields()   {
				 $ccsve_post_type = get_option('ccsve_post_type');

				 if($ccsve_post_type == 'product' && class_exists('WC_Product')) {

					  global $woocommerce;

					  //$product = wc_get_product( $post->ID );

					  //$meta_keys = generate_post_meta_keys($ccsve_post_type);
					  $meta_keys = array(
							'sku',
							'regular_price',
							'sale_price',
							'manage_stock',
							'stock_status',
							'backorders',
							'stock',
							'featured',
							'featured_image',
							'product_gallery'
					  );
					  $ccsve_woocommerce_fields = get_option('ccsve_woocommerce_fields');
					  $ccsve_meta_keys_num = count($meta_keys);

					  echo '<select multiple="multiple" size="'.$ccsve_meta_keys_num.'" name="ccsve_woocommerce_fields[selectinput][]">';
					  foreach ($meta_keys as $meta_key) {
							if (in_array($meta_key, $ccsve_woocommerce_fields['selectinput'])){
								 echo '\n\t<option selected="selected" value="'. $meta_key . '">'.$meta_key.'</option>';
							} else {
								 echo '\n\t\<option value="'.$meta_key .'">'.$meta_key.'</option>';
							}
					  }

				 } // if class exists
			}*/

			//since 1.5.4.2 - July 11, 2020
			public function settings_field_select_date_min() {
				$ccsve_date_min = get_option('ccsve_date_min');
				
				if(!$ccsve_date_min) {
					$ccsve_date_min = '';
					$date_placeholder = '';
				} else {
					$date_placeholder = date('m/d/yy', strtotime($ccsve_date_min));
				}

				?>
				<p><input type="text" id="ccsve_date_min" class="" name="ccsve_date_min" value="<?php echo $date_placeholder; ?>"/></p>
				<p class="description"><?php _e("Export only content created from this date (format: <em>mm/dd/yyyy</em>)", TEXTDOMAIN); ?></p>

				<script>
				jQuery(document).ready(function(){
					jQuery('#ccsve_date_min').datepicker({
						dateFormat: 'mm/dd/yy',
						changeMonth: true,
            			changeYear: true,
						}); 
				});
				</script>

				
				<?php
			}

			// ADD MENU
			public function add_menu() {
				// Add a page to manage this plugin's settings
				add_submenu_page(
					'tools.php',
					'CSV/XLS Export Settings',
					'CSV/XLS Export',
					'manage_options',
					'Simple_CSV_Exporter_Settings',
					array(&$this, 'plugin_settings_page')
				);
			} // END public function add_menu()

			// Settings Page contents
			public function settings_section_simple_csv_exporter_settings() {
				?>
            <div class="boxes">
               <div class="box">
                  <div class="box-title-container">
                     <h2 class="box-title">
								<?php _e("Instructions", TEXTDOMAIN); ?>
                     </h2>
                  </div>
                  <div class="box-content">
                     <p>
								<?php
									//$export_url = get_admin_url("", "tools.php?page=Simple_CSV_Exporter_Settings&export=");
									$export_url = get_bloginfo('url').'/?export=';

									_e("This page allows you to specify what posts are being exported. You can specify them via post types, taxonomies, custom fields or individual post IDs. After saving the settings, you will be able to export the data in CSV or XLS format by using the buttons on the bottom or by clicking the links below:", TEXTDOMAIN);
								?>
                     </p>
                     <ul class="download-links-list">
                        <li>
                           <strong>XLS:</strong>
                           <a class="download-link" href="<?php echo $export_url ?>xls" title="<?php _e("Click to start the download", TEXTDOMAIN); ?>">
										<?php echo sprintf(__("Download in %s format", TEXTDOMAIN), "XLS"); ?>
                           </a>
                        </li>
                        <li>
                           <strong>CSV:</strong>
                           <a class="download-link" href="<?php echo $export_url ?>csv" title="<?php _e("Click to start the download", TEXTDOMAIN); ?>">
										<?php echo sprintf(__("Download in %s format", TEXTDOMAIN), "CSV"); ?>
                           </a>
                        </li>
                     </ul>
                     <p>
                        <em>
									<?php _e("<strong>Note:</strong> You must choose the post type and save the settings <strong>before</strong> you can see the taxonomies or custom fields for a custom post type. Once the page reloads, you will see the connected taxonomies and custom fields for the post type.", TEXTDOMAIN); ?>
                        </em>
                     </p>
                  </div>
               </div>
               <div class="box">
                  <div class="box-title-container">
                     <h2 class="box-title">
								<?php _e("Custom Data Download", TEXTDOMAIN); ?>
                     </h2>
                  </div>
                  <div class="box-content">
                     <p>
								<?php _e("You can overwrite the settings by using the export URL and manually entering the post type you would like to export, regardless of what post type have been set on this page. Here are two examples:", TEXTDOMAIN); ?>
                     </p>
                     <ul>
                        <li>
                           <p>
                              <strong>
								<?php _e('XLS and post type "page":', TEXTDOMAIN); ?>
                              </strong>
                           </p>
                           <p>
                              <input type="url" class="widefat" readonly value="<?php echo $export_url . "xls&post_type=page"; ?>">
                           </p>
                        </li>
                        <li>
                           <p>
                              <strong>
											<?php _e('CSV and custom post type "book":', TEXTDOMAIN); ?>
                              </strong>
                           </p>
                           <p>
                              <input type="url" class="widefat" readonly value="<?php echo $export_url . "csv&post_type=book"; ?>">
                           </p>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="compatibility-info">
               <p class="description">
               <p>
						<?php
							echo sprintf(__("<strong>Please note:</strong> When opening the exported .XLS file with Microsoft Excel, the software <a href=\"%s\" target=\"_blank\">will display a warning</a>. However, the file is perfectly fine and can then be opened.", TEXTDOMAIN), 'http://blogs.msdn.com/b/vsofficedeveloper/archive/2008/03/11/excel-2007-extension-warning.aspx');
						?>
               </p>
            </div>
				<?php
			}

			public function plugin_settings_page() {
				if(!current_user_can('manage_options')) {
					wp_die(__('You do not have sufficient permissions to access this page.'));
				}

				ini_set('display_errors', 0);
				?>
            <div class="wrap simple_csv_exporter_wrap">
               <form method="post" action="options.php">
						<?php settings_fields('wp_ccsve-group'); ?>
						<?php do_settings_fields('wp_ccsve-group', 'simple_csv_exporter_settings-section'); ?>
						<?php do_settings_sections('Simple_CSV_Exporter_Settings'); ?>
						<?php submit_button(); ?>

                  <a class="ccsve_button button button-success" href="options-general.php?page=simple_csv_exporter_settings&export=csv">Export to CSV</a>
                  <a class="ccsve_button button button-success" href="options-general.php?page=simple_csv_exporter_settings&export=xls">Export to XLS</a>
               </form>
               <div class="sidebar">
                  <div class="block">
                     <p>
                     <h3>Plugin developed by
                        <a href="http://www.shambix.com" target="blank">Shambix</a>
                     </h3>
                     </p>
                     <p>
                        <strong>Need to customize it or want to speed up a certain feature developement?
                           <br>
                           <a href="mailto:info@shambix.com">Email me</a>
                           !
                        </strong>
                     </p>
                  </div>
                  <div class="block">
                     <p>
                        <strong>Documentation</strong>
                        <br>
                        Check the
                        <a href="https://wordpress.org/plugins/simple-csv-xls-exporter/faq/" target="_blank">FAQ</a>
                     </p>
                     <p>
                        <strong>Bugs? Questions?</strong>
                        <br>
                        Let me know in the
                        <a href="https://wordpress.org/support/plugin/simple-csv-xls-exporter/" target="_blank">forum</a>
                     </p>
                     <p>
                        <strong>Like the plugin?</strong>
                        <br>
                        Give it a good
                        <a href="https://wordpress.org/support/plugin/simple-csv-xls-exporter/reviews#new-topic-0" target="=_blank">review</a>
                        , so other people can find it &amp; enjoy it too!
                     </p>
                  </div>
                  <div class="block donate">
                     <p>This plugin has been developed in my (almost none) spare time and
                        <strong>it's free</strong>
                        , hence
                        <i>support is not guaranteed</i>
                        , nor I'll be able to reply to questions, fix bugs or accomodate requests, fast.
                        <br>
                        If you need a custom feature, or you need me to expedite some improvement or fix, I'll be happy to do it for a fee.
                        <br>
                        <br>
                        Even a tiny donation will help me making it better, by updating it more often without switching to a commercial license.
                     </p>
                     <p>
                        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3UR3E5PL3TW3E" target="_blank">
                           <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif">
                        </a>
                     </p>
                  </div>

               </div>

               <div class="footer">
                  <br/>
                  <a href="http://www.shambix.com" target="blank">
                     <img src="https://preview.ibb.co/dxWxUv/shambix_banner_918x104.jpg">
                  </a>
               </div>

            </div>
				<?php
			} // END public function plugin_settings_page()

		} // END class simple_csv_exporter_settings_Settings

	} // END if(!class_exists('simple_csv_exporter_settings_Settings'))