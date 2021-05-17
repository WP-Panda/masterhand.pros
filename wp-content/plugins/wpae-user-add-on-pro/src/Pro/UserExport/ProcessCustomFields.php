<?php

namespace Pmue\Pro\UserExport;


class ProcessCustomFields
{
    /**
     * @param $userId
     * @param $implode_delimiter
     * @param $fieldLabel
     * @param $fieldValue
     * @param $fieldSnipped
     * @param $article
     * @param $element_name
     * @return array
     */
    public function processCustomFields($userId, $implode_delimiter, $fieldLabel, $fieldValue, $fieldSnipped, $article, $element_name)
    {
        $specialCf = array('_order_count', '_money_spent');

        if (!in_array($fieldLabel, $specialCf)) {
            if (!empty($fieldValue)) {

                $val = "";

                $cur_meta_values = get_user_meta($userId, $fieldValue);

                if (!empty($cur_meta_values) and is_array($cur_meta_values)) {
                    foreach ($cur_meta_values as $key => $cur_meta_value) {
                        if (empty($val)) {
                            $val = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $userId);
                        } else {
                            $val = apply_filters('pmxe_custom_field', pmxe_filter($val . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $userId);
                        }
                    }
                    wp_all_export_write_article($article, $element_name, $val);
                }

                if (empty($cur_meta_values)) {
                    if (empty($article[$element_name])) {
                        wp_all_export_write_article($article, $element_name, apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $fieldValue, $userId));
                    }
                }
            }

        } else {

            $val = "";

            if ($fieldLabel == '_order_count') {
                if(function_exists('wc_get_customer_order_count')) {
                    $val = wc_get_customer_order_count($userId);
                }


            } else if ($fieldLabel == '_money_spent') {
                if(function_exists('wc_get_customer_total_spent')) {
                    $val = wc_get_customer_total_spent($userId);
                }
            }

            if (empty($val)) {
                $val = 0;
            }

            $val = apply_filters('pmxe_custom_field', pmxe_filter($val, $fieldSnipped), $fieldValue, $userId);

            wp_all_export_write_article($article, $element_name, $val);

        }
        return $article;
    }

}