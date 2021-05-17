<?php

if (!class_exists('XmlExportWooCommerceReview')) {
    final class XmlExportWooCommerceReview
    {
        private $init_fields = array(
            array(
                'label' => 'comment_ID',
                'name' => 'ID',
                'type' => 'comment_ID'
            ),

            array(
                'label' => 'comment_content',
                'name' => 'Content',
                'type' => 'comment_content'
            ),
            array(
                'label' => 'rating',
                'name' => 'Rating',
                'type' => 'cf'
            ),
            array(
                'label' => 'approved',
                'name' => 'Approved',
                'type' => 'comment_approved'
            ),
            array(
                'label' => 'cf_review_verified',
                'name' => 'Verified',
                'type' => 'cf'
            ),
            array(
                'label' => 'comment_date',
                'name' => 'Date',
                'type' => 'comment_date'
            ),
            array(
                'label' => 'comment_author_email',
                'name' => 'Author Email',
                'type' => 'comment_author_email'
            ),

        );

        private $default_fields = array(
            array(
                'label' => 'comment_ID',
                'name' => 'ID',
                'type' => 'comment_ID'
            ),
            array(
                'label' => 'comment_post_ID',
                'name' => 'Product ID',
                'type' => 'comment_post_ID'
            ),
            array(
                'label' => 'comment_content',
                'name' => 'Content',
                'type' => 'comment_content'
            ),
            array(
                'label' => 'rating',
                'name' => 'Rating',
                'type' => 'cf'
            ),
            array(
                'label' => 'Approved',
                'value' => 'approved',
                'name' => 'Approved',
                'type' => 'comment_approved'
            ),
            array(
                'label' => 'cf_review_verified',
                'name' => 'Verified',
                'type' => 'cf'
            ),
            array(
                'label' => 'comment_date',
                'name' => 'Review Date (Server Time)',
                'type' => 'comment_date'
            ),
            array(
                'label' => 'comment_date',
                'name' => 'Review Date (GMT)',
                'type' => 'comment_date_gmt'
            )

        );

        private $author_fields = array(
            array(
                'label' => 'comment_author',
                'name' => 'Author Name',
                'type' => 'comment_author'
            ),
            array(
                'label' => 'comment_author_email',
                'name' => 'Author Email',
                'type' => 'comment_author_email'
            ),
            array(
                'label' => 'comment_author_url',
                'name' => 'Author URL',
                'type' => 'comment_author_url'
            ),
            array(
                'label' => 'comment_author_IP',
                'name' => 'Author IP',
                'type' => 'comment_author_IP'
            ),
            array(
                'label' => 'comment_agent',
                'name' => 'Agent',
                'type' => 'comment_agent'
            ),
            array(
                'label' => 'user_id',
                'name' => 'User ID',
                'type' => 'user_id'
            )
        );


        private $other_fields = array(
            array(
                'label' => 'comment_karma',
                'name' => 'Karma',
                'type' => 'comment_karma'
            ),
        );


        private $parent_fields = array(
            array(
                'label' => 'parent_post_slug',
                'name' => 'Parent Product Slug',
                'type' => 'comment_parent_post_slug'
            ),
            array(
                'label' => 'parent_post_title',
                'name' => 'Parent Product Title',
                'type' => 'comment_parent_post_title'
            ),
            array(
                'label' => 'parent_post_id',
                'name' => 'Parent Product ID',
                'type' => 'comment_post_ID'
            ),
            array(
                'label' => 'comment_parent',
                'name' => 'Parent Review ID',
                'type' => 'comment_parent'
            ),
            array(
                'label' => 'comment_parent_date',
                'name' => 'Parent Review Date (Server Time)',
                'type' => 'comment_parent_date'
            ),
            array(
                'label' => 'comment_parent_date_gmt',
                'name' => 'Parent Review Date (GMT)',
                'type' => 'comment_parent_date_gmt'
            ),
        );

        private $advanced_fields = array();

        public static $is_active = true;

        public function __construct()
        {

            if ((XmlExportEngine::$exportOptions['export_type'] == 'specific' and !in_array('shop_review', XmlExportEngine::$post_types))
                or (XmlExportEngine::$exportOptions['export_type'] == 'advanced' and XmlExportEngine::$exportOptions['wp_query_selector'] != 'wp_comment_query')
            ) {
                self::$is_active = false;
                return;
            }

            add_filter("wp_all_export_available_sections", array(&$this, "filter_available_sections"), 10, 1);
            add_filter("wp_all_export_init_fields", array(&$this, "filter_init_fields"), 10, 1);
            add_filter("wp_all_export_default_fields", array(&$this, "filter_default_fields"), 10, 1);
            add_filter("wp_all_export_available_data", array(&$this, "filter_available_data"), 10, 1);
        }

        // [FILTERS]

        /**
         *
         * Filter Init Fields
         *
         */
        public function filter_init_fields($init_fields)
        {
            return $this->init_fields;
        }

        /**
         *
         * Filter Default Fields
         *
         */
        public function filter_default_fields($default_fields)
        {
            return $this->default_fields;
        }


        /**
         *
         * Filter Sections in Available Data
         *
         * @param array[] $sections
         * @return array[]
         *
         */
        public function filter_available_sections($sections)
        {

            unset($sections['cats']);
            unset($sections['media']);
            //unset($sections['other']);
            unset($sections['cf']);

            $sections['author_info']['title'] = __('Author Info', PMXE_Plugin::LANGUAGE_DOMAIN);
            $sections['author_info']['content'] = 'author_fields';

            //$sections['cf']['title'] = __("Other", PMXE_Plugin::LANGUAGE_DOMAIN);

            $sections['parent']['title'] = __('Parent', PMXE_Plugin::LANGUAGE_DOMAIN);
            $sections['parent']['content'] = 'parent_fields';

            $sections['default']['title'] = __("Review Data", PMXE_Plugin::LANGUAGE_DOMAIN);

            $sections['other']['title'] = __('Other', PMXE_Plugin::LANGUAGE_DOMAIN);
            $sections['other']['content'] = 'other_fields';

            $other = $sections['other'];
            unset($sections['other']);
            $sections['other'] = $other;

            return $sections;
        }

        function filter_available_data($available_data) {

            $available_data['author_fields'] = $this->author_fields;
            $available_data['parent_fields'] = $this->parent_fields;

            $available_data['other_fields'] = array_merge($this->other_fields, $available_data['existing_meta_keys']);

            return $available_data;
        }

        // [\FILTERS]

        public function init(& $existing_meta_keys = array())
        {
            if (!self::$is_active) return;

            global $wp_version;

            if(PMXE_Plugin::$session->get('exportQuery') && !XmlExportEngine::$exportQuery) {
                XmlExportEngine::$exportQuery = PMXE_Plugin::$session->get('exportQuery');
            }

            if (!empty(XmlExportEngine::$exportQuery)) {
                if (version_compare($wp_version, '4.2.0', '>=')) {
                    $reviews = XmlExportEngine::$exportQuery->get_comments();
                } else {
                    $reviews = XmlExportEngine::$exportQuery;
                }
            }

            if (!empty($reviews)) {
                foreach ($reviews as $review) {
                    $review_meta = get_comment_meta($review->comment_ID, '');
                    if (!empty($review_meta)) {
                        foreach ($review_meta as $record_meta_key => $record_meta_value) {
                            if($record_meta_key == 'rating' || $record_meta_key == 'verified') {
                                continue;
                            }
                            if (!in_array($record_meta_key, $existing_meta_keys)) {
                                $to_add = true;
                                foreach ($this->default_fields as $default_value) {
                                    if ($record_meta_key == $default_value['name'] || $record_meta_key == $default_value['type']) {
                                        $to_add = false;
                                        break;
                                    }
                                }
                                if ($to_add) {
                                    foreach ($this->advanced_fields as $advanced_value) {
                                        if ($record_meta_key == $advanced_value['name'] || $record_meta_key == $advanced_value['type']) {
                                            $to_add = false;
                                            break;
                                        }
                                    }
                                }
                                if ($to_add) $existing_meta_keys[] = $record_meta_key;
                            }
                        }
                    }
                }
            }
        }

        public static function prepare_data($review, $exportOptions, $xmlWriter = false, $implode_delimiter, $preview)
        {
            $article = array();

            // associate exported comment with import
            if (wp_all_export_is_compatible() && isset($exportOptions['is_generate_import']) && $exportOptions['is_generate_import'] && $exportOptions['import_id']) {
                $postRecord = new PMXI_Post_Record();
                $postRecord->clear();
                $postRecord->getBy(array(
                    'post_id' => $review->comment_ID,
                    'import_id' => $exportOptions['import_id'],
                ));

                if ($postRecord->isEmpty()) {
                    $postRecord->set(array(
                        'post_id' => $review->comment_ID,
                        'import_id' => $exportOptions['import_id'],
                        'unique_key' => $review->comment_ID
                    ))->save();
                }
                unset($postRecord);
            }

            $is_xml_export = false;

            if (!empty($xmlWriter) and $exportOptions['export_to'] == 'xml' and !in_array($exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) {
                $is_xml_export = true;
            }

            foreach ($exportOptions['ids'] as $ID => $value) {
                $fieldName = apply_filters('wp_all_export_field_name', wp_all_export_parse_field_name($exportOptions['cc_name'][$ID]), $ID);
                $fieldValue = $exportOptions['cc_value'][$ID];
                $fieldLabel = $exportOptions['cc_label'][$ID];
                $fieldSql = $exportOptions['cc_sql'][$ID];
                $fieldPhp = $exportOptions['cc_php'][$ID];
                $fieldCode = $exportOptions['cc_code'][$ID];
                $fieldType = $exportOptions['cc_type'][$ID];
                $fieldOptions = $exportOptions['cc_options'][$ID];
                $fieldSettings = empty($exportOptions['cc_settings'][$ID]) ? $fieldOptions : $exportOptions['cc_settings'][$ID];

                if (empty($fieldName) or empty($fieldType) or !is_numeric($ID)) continue;

                $element_name = (!empty($fieldName)) ? $fieldName : 'untitled_' . $ID;

                $element_name_ns = '';

                if ($is_xml_export) {
                    $element_name = (!empty($fieldName)) ? preg_replace('/[^a-z0-9_:-]/i', '', $fieldName) : 'untitled_' . $ID;

                    if (strpos($element_name, ":") !== false) {
                        $element_name_parts = explode(":", $element_name);
                        $element_name_ns = (empty($element_name_parts[0])) ? '' : $element_name_parts[0];
                        $element_name = (empty($element_name_parts[1])) ? 'untitled_' . $ID : preg_replace('/[^a-z0-9_-]/i', '', $element_name_parts[1]);
                    }
                }

                $fieldSnipped = (!empty($fieldPhp) and !empty($fieldCode)) ? $fieldCode : false;

                if ($exportOptions['cc_combine_multiple_fields'][$ID]) {

                    $combineMultipleFieldsValue = $exportOptions['cc_combine_multiple_fields_value'][$ID];

                    $combineMultipleFieldsValue = stripslashes($combineMultipleFieldsValue);
                    $snippetParser = new \Wpae\App\Service\SnippetParser();
                    $snippets = $snippetParser->parseSnippets($combineMultipleFieldsValue);
                    $engine = new XmlExportEngine(XmlExportEngine::$exportOptions);
                    $engine->init_available_data();
                    $engine->init_additional_data();
                    $snippets = $engine->get_fields_options($snippets);

                    $articleData = self::prepare_data($review, $snippets, $xmlWriter = false, $implode_delimiter, $preview);

                    $functions = $snippetParser->parseFunctions($combineMultipleFieldsValue);
                    $combineMultipleFieldsValue = \Wpae\App\Service\CombineFields::prepareMultipleFieldsValue($functions, $combineMultipleFieldsValue, $articleData);

                    if($preview) {
                        $combineMultipleFieldsValue = trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($combineMultipleFieldsValue)));
                    }
                    
                    wp_all_export_write_article($article, $element_name, pmxe_filter($combineMultipleFieldsValue, $fieldSnipped));

                } else {

                    $parent_comment = false;

                    $review_product = get_post($review->comment_post_ID);

                    if($review->comment_parent) {
                        $parent_comment = self::get_comment($review->comment_parent);
                    }

                    switch ($fieldType) {
                        case 'comment_ID':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_id', pmxe_filter($review->comment_ID, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_post_ID':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_post_id', pmxe_filter($review->comment_post_ID, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_parent_post_slug':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_post_id', pmxe_filter($review_product->post_name, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_parent_post_title':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_post_id', pmxe_filter($review_product->post_title, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_author':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_author', pmxe_filter($review->comment_author, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_author_email':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_author_email', pmxe_filter($review->comment_author_email, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_author_url':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_author_url', pmxe_filter($review->comment_author_url, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_author_IP':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_author_ip', pmxe_filter($review->comment_author_IP, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_karma':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_karma', pmxe_filter($review->comment_karma, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_content':
                            $val = apply_filters('pmxe_comment_content', pmxe_filter($review->comment_content, $fieldSnipped), $review->comment_ID);
                            wp_all_export_write_article($article, $element_name, ($preview) ? trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($val))) : $val);
                            break;
                        case 'comment_date':

                            $post_date = prepare_date_field_value($fieldSettings, strtotime($review->comment_date), "Y-m-d H:i:s");

                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_date_gmt':

                            $post_date = prepare_date_field_value($fieldSettings, strtotime($review->comment_date_gmt), "Y-m-d H:i:s");

                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_parent_date':

                            if($parent_comment) {
                                $post_date = prepare_date_field_value($fieldSettings, strtotime($parent_comment->comment_date), "Y-m-d H:i:s");
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $review->comment_ID));
                            } else {
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter('', $fieldSnipped), $review->comment_ID));
                            }
                            break;
                        case 'comment_parent_date_gmt':

                            if($parent_comment) {
                                $post_date = prepare_date_field_value($fieldSettings, strtotime($parent_comment->comment_date_gmt), "Y-m-d H:i:s");
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter($post_date, $fieldSnipped), $review->comment_ID));
                            } else {
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_date', pmxe_filter('', $fieldSnipped), $review->comment_ID));
                            }

                            break;
                        case 'comment_approved':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_approved', pmxe_filter($review->comment_approved, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_agent':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_agent', pmxe_filter($review->comment_agent, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_type':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_type', pmxe_filter($review->comment_type, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'comment_parent':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_comment_parent', pmxe_filter($review->comment_parent, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'user_id':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_user_id', pmxe_filter($review->user_id, $fieldSnipped), $review->comment_ID));
                            break;
                        case 'other':
                            if (!empty($fieldValue)) {
                                $cur_meta_values = get_comment_meta($review->comment_ID, $fieldValue);
                                if (!empty($cur_meta_values) and is_array($cur_meta_values)) {
                                    $val = "";
                                    foreach ($cur_meta_values as $key => $cur_meta_value) {
                                        if (empty($val)) {
                                            $val = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $review->comment_ID);
                                        } else {
                                            $val = apply_filters('pmxe_custom_field', pmxe_filter($val . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $review->comment_ID);
                                        }
                                    }
                                    wp_all_export_write_article($article, $element_name, $val);
                                }

                                if (empty($cur_meta_values)) {
                                    if (empty($article[$element_name])) {
                                        wp_all_export_write_article($article, $element_name, apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $fieldValue, $review->comment_ID));
                                    }
                                }
                            }
                            break;
                        case 'sql':
                            if (!empty($fieldSql)) {
                                global $wpdb;
                                $val = $wpdb->get_var($wpdb->prepare(stripcslashes(str_replace("%%ID%%", "%d", $fieldSql)), $review->comment_ID));
                                if (!empty($fieldPhp) and !empty($fieldCode)) {
                                    // if shortcode defined
                                    if (strpos($fieldCode, '[') === 0) {
                                        $val = do_shortcode(str_replace("%%VALUE%%", $val, $fieldCode));
                                    } else {
                                        $val = eval('return ' . stripcslashes(str_replace("%%VALUE%%", $val, $fieldCode)) . ';');
                                    }
                                }
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_sql_field', $val, $element_name, $review->comment_ID));
                            }
                            break;
                        default:
                            break;
                    }
                }

                if ($is_xml_export and isset($article[$element_name])) {
                    $element_name_in_file = XmlCsvExport::_get_valid_header_name($element_name);

                    $xmlWriter = apply_filters('wp_all_export_add_before_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $review->comment_ID);

                    $xmlWriter->beginElement($element_name_ns, $element_name_in_file, null);
                    $xmlWriter->writeData($article[$element_name], $element_name_in_file);
                    $xmlWriter->closeElement();

                    $xmlWriter = apply_filters('wp_all_export_add_after_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $review->comment_ID);
                }
            }
            return $article;
        }

        public static function prepare_import_template( $exportOptions, &$templateOptions, $element_name, $ID) {

            $options = $exportOptions;

            $element_type = $options['cc_type'][$ID];

            $is_xml_template = $options['export_to'] == 'xml';

            $implode_delimiter = XmlExportEngine::$implode;

            switch ($element_type) {
                case 'comment_ID':
                    $templateOptions['unique_key'] = '{'. $element_name .'[1]}';
                    break;
                case 'comment_parent_post_title':
                    $templateOptions['comment_post'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_post_id'] = 1;
                    break;
                case 'comment_author':
                    $templateOptions['comment_author'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_author'] = 1;
                    break;
                case 'comment_author_email':
                    $templateOptions['comment_author_email'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_author_email'] = 1;
                    break;
                case 'comment_author_url':
                    $templateOptions['comment_author_url'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_author_url'] = 1;
                    break;
                case 'comment_author_IP':
                    $templateOptions['comment_author_IP'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_author_IP'] = 1;
                    break;
                case 'comment_karma':
                    $templateOptions['comment_karma'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_karma'] = 1;
                    break;
                case 'comment_content':
                    $templateOptions['content'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_content'] = 1;
                    break;
                case 'comment_date_gmt':
                    $templateOptions['date'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_dates'] = 1;
                    break;
                case 'comment_approved':
                    $templateOptions['comment_approved'] = 'xpath';
                    $templateOptions['comment_approved_xpath'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_approved'] = 1;
                    break;
                case 'comment_agent':
                    $templateOptions['comment_agent'] = '{'. $element_name .'[1]}';
                    $templateOptions['is_update_comment_agent'] = 1;
                    break;
                case 'cf':
                    if ( ! empty($options['cc_value'][$ID]) && $options['cc_value'][$ID] == 'rating') {
                        $templateOptions['comment_rating'] = '{'. $element_name .'[1]}';
                        $templateOptions['is_update_comment_rating'] = 1;
                    }
                    if ( ! empty($options['cc_value'][$ID]) && $options['cc_value'][$ID] == 'cf_review_verified') {
                        $templateOptions['comment_verified'] = 'xpath';
                        $templateOptions['comment_verified_xpath'] = '{'. $element_name .'[1]}';
                        $templateOptions['is_update_comment_verified'] = 1;
                    }
                    break;
                case 'other':
                    $templateOptions['custom_name'][] = $options['cc_value'][$ID];
                    $templateOptions['custom_value'][] = '{'. $element_name .'[1]}';
                    $templateOptions['custom_format'][] = 0;
                    break;
                default:
                    break;
            }
        }


        /**
         * __get function.
         *
         * @access public
         * @param mixed $key
         * @return mixed
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * Get a session variable
         *
         * @param string $key
         * @param  mixed $default used if the session variable isn't set
         * @return mixed value of session variable
         */
        public function get($key, $default = null)
        {
            return isset($this->{$key}) ? $this->{$key} : $default;
        }

        public static function get_comment($comment_id)
        {
            return get_comment($comment_id);
        }
    }
}
