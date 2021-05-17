<?php

/**
 * Class FilteringCustomers
 * @package Wpae\Pro\Filtering
 */
class FilteringCustomers extends FilteringUsers
{
    /**
     * @return bool
     */
    public function parse(){
        if ( $this->isFilteringAllowed()){
            $this->checkNewStuff();

            // No Filtering Rules defined
            if ( empty($this->filterRules)) {

                if ($this->isExportOnlyCustomersWithPurchases()) {
                    $in_customer_meta = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
                    $this->queryWhere .= " AND {$this->wpdb->users}.ID IN (" . $in_customer_meta . ") GROUP BY {$this->wpdb->users}.ID";
                } else {
                    $in_customer_meta = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
                    $in_customers = $this->wpdb->prepare("SELECT DISTINCT user_id FROM {$this->wpdb->usermeta} WHERE meta_key = %s AND meta_value REGEXP %s", 'wp_capabilities', 'customer');
                    $this->queryWhere .= " AND ({$this->wpdb->users}.ID IN (" . $in_customer_meta . ") OR {$this->wpdb->users}.ID IN (" . $in_customers . ")) GROUP BY {$this->wpdb->users}.ID";
                }

                return FALSE;
            }

            $this->queryWhere = $this->isExportNewStuff() ? $this->queryWhere . " AND (" : " AND (";

            // Apply Filtering Rules
            foreach ($this->filterRules as $rule) {
                if ( is_null($rule->parent_id) ) {
                    $this->parse_single_rule($rule);
                }
            }

            if ($this->isExportOnlyCustomersWithPurchases()) {
                $in_customer_meta = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
                var_dump($in_customer_meta); die;
                $this->queryWhere .= " ) AND ({$this->wpdb->users}.ID IN (" . $in_customer_meta . "))";
            } else {
                $in_customer_meta = $this->wpdb->prepare("SELECT DISTINCT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0');
                $in_customers = $this->wpdb->prepare("SELECT DISTINCT user_id FROM {$this->wpdb->usermeta} WHERE meta_key = %s AND meta_value REGEXP %s", 'wp_capabilities', 'customer');
                $this->queryWhere .= " AND ({$this->wpdb->users}.ID IN (" . $in_customer_meta . ") OR {$this->wpdb->users}.ID IN (" . $in_customers . "))";
            }

            if ( $this->isExportOnlyCustomersWithPurchases() ) {
                // Don't add a closing ) to the query in this case.
            } else {
                // Do it.
                $this->queryWhere .= ")";
            }

            if ($this->meta_query || $this->tax_query) {
                $this->queryWhere .= " GROUP BY {$this->wpdb->users}.ID";
            }

        }
    }

    /**
     * @return bool
     */
    protected function isExportOnlyCustomersWithPurchases(){
        return ( ! empty(\XmlExportEngine::$exportOptions['export_only_customers_that_made_purchases']));
    }

}