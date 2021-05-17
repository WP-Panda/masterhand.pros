<?php
/**
 * Backend Report Exporter.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * ME Report CSV Export class
 *
 * Generates report data and exports it in CSV format.
 *
 * @author   EngineThemes
 * @category Classes
 * @package  Admin/Reports
 * @since    1.0.0
 */
class ME_Report_CSVExport
{
    /**
     * Constructor
     *
     * Check type of report, generates data, names file and export it.
     */
    public function __construct()
    {
        if (isset($_GET['export']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'me-export')) {
            $csv = $this->generate_csv();

            if (empty($_GET['tab']) || $_GET['tab'] == 'listings') {
                $filename = __("Report Listings", "enginethemes");
            } else {
                switch ($_GET['tab']) {
                    case 'orders':
                        $filename = __("Report Orders", "enginethemes");
                        break;
                    case 'inquiries':
                        $filename = __("Report Inquiries", "enginethemes");
                        break;
                    default:
                        $filename = __("Report Members", "enginethemes");
                        break;
                }
            }

            if (!empty($_GET['from_date'])) {
                $filename .= '_' . sanitize_file_name( $_GET['from_date'] );
            }

            if (!empty($_GET['to_date'])) {
                $filename .= '_' . sanitize_file_name( $_GET['to_date'] );
            }

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"{$filename}.csv\";");
            header("Content-Transfer-Encoding: binary");

            echo $csv;
            exit;
        }
    }

    /**
     * Converting data to CSV
     *
     * @return data to export
     */
    public function generate_csv()
    {
        if (empty($_GET['tab']) || $_GET['tab'] == 'listings') {
            return $this->generate_listings();
        }

        switch ($_GET['tab']) {
            case 'orders':
                return $this->generate_orders();
                break;
            case 'inquiries':
                return $this->generate_inquiries();
                break;
            default:
                return $this->generate_members();
                break;
        }

    }

    /**
     * Generate CSV row
     * @param array $headings
     * @param array $data
     * @param array $quant
     *
     * @return string $csv_output
     */
    public function generate_rows($headings, $data, $quant)
    {
        $csv_output = '';
        foreach ($headings as $key => $heading) {
            if($key == 'quant' && $quant != 'day' && $quant != 'year') {
                $csv_output = $csv_output . __("From Date", "enginethemes") . ",";
                $csv_output = $csv_output . __("To Date", "enginethemes") . ",";
            }else {
                $csv_output = $csv_output . $heading . ',';
            }

        }
        $csv_output .= "\n";

        foreach ($data as $key => $item) {
            foreach ($headings as $key => $heading) {
                if ($key == 'quant') {
                    $time = marketengine_get_start_and_end_date($quant, $item->quant, $item->year, 'Y/m/d');
                    $time = explode('-', $time);

                    foreach ($time as $value) {
                        $csv_output .= str_replace(',', '-', trim($value)) . ",";
                    }
                } else {
                    $csv_output .= $item->$key . ",";
                }
            }
            $csv_output .= "\n";
        }
        return $csv_output;
    }

    /**
     * Generates listing data.
     *
     * @return string $csv_output listing data
     */
    public function generate_listings()
    {

        $args              = array_map('esc_sql', $_REQUEST);
        $args['showposts'] = 300000;
        $args['paged']     = 1;
        $query             = marketengine_listing_report($args);

        $quant          = empty($args['quant']) ? 'day' : $args['quant'];
        $active_section = empty($args['section']) ? '' : $args['section'];
        $listings       = $query['posts'];

        $csv_output = '';
        if ($quant == 'day' || $quant == 'year') {
            $csv_output = $csv_output . __("Date", "enginethemes") . ",";
        }else {
            $csv_output = $csv_output . __("From Date", "enginethemes") . ",";
            $csv_output = $csv_output . __("To Date", "enginethemes") . ",";
        }

        if ($active_section == '') {
            $csv_output = $csv_output . __("Total Listings", "enginethemes") . ",";
        }

        if ($active_section == '' || $active_section == 'purchase') {
            $csv_output = $csv_output . __("Purchase", "enginethemes") . ",";
        }

        if ($active_section == '' || $active_section == 'contact') {
            $csv_output = $csv_output . __("Contact", "enginethemes") . ",";
        }

        $csv_output .= "\n";

        foreach ($listings as $key => $listing) {
            $time = marketengine_get_start_and_end_date($quant, $listing->quant, $listing->year, 'Y/m/d');
            $time = explode('-', $time);

            foreach ($time as $value) {
                $csv_output .= str_replace(',', '-', trim($value)) . ",";
            }

            if ($active_section == '') {
                $csv_output .= $listing->count . ",";
            }
            if ($active_section == '' || $active_section == 'purchase') {
                $csv_output .= $listing->purchase_type . ",";
            }
            if ($active_section == '' || $active_section == 'contact') {
                $csv_output .= $listing->contact_type . ",";
            }

            $csv_output .= "\n";
        }
        return $csv_output;
    }

    /**
     * Generates orders.
     *
     * @return string of orders.
     */
    public function generate_orders()
    {

        $args              = array_map('esc_sql', $_REQUEST);
        $args['showposts'] = 300000;
        $args['paged']     = 1;
        $query             = marketengine_orders_report($args);

        $quant = empty($args['quant']) ? 'day' : $args['quant'];

        $orders = $query['posts'];

        $headings = array(
            'quant' => __("Date", "enginethemes"),
            'count' => __("Total Orders", "enginethemes"),
            'total' => __("Income", "enginethemes") . '(' . marketengine_option('payment-currency-sign') . ')',
        );

        return $this->generate_rows($headings, $orders, $quant);
    }

    /**
     * Generates inquiries.
     *
     * @return string of inquiries
     */
    public function generate_inquiries()
    {

        $args              = array_map('esc_sql', $_REQUEST);
        $args['showposts'] = 300000;
        $args['paged']     = 1;
        $query             = marketengine_inquiries_report($args);

        $quant = empty($args['quant']) ? 'day' : $args['quant'];

        $inquiries = $query['posts'];

        $headings = array(
            'quant' => __("Date", "enginethemes"),
            'count' => __("Total Inquiries", "enginethemes"),
        );

        return $this->generate_rows($headings, $inquiries, $quant);

    }

    /**
     * Generates members.
     *
     * @return string of rows of members
     */
    public function generate_members()
    {

        $args              = array_map('esc_sql', $_REQUEST);
        $args['showposts'] = 300000;
        $args['paged']     = 1;
        $query             = marketengine_members_report($args);

        $quant = empty($args['quant']) ? 'day' : $args['quant'];

        $members = $query['posts'];

        $headings = array(
            'quant' => __("Registration Date", "enginethemes"),
            'count' => __("Total Members", "enginethemes"),
        );

        return $this->generate_rows($headings, $members, $quant);

    }

}



/**
 * Create an instance of SCV exporter when an user accesses the admin area.
 *
 * @since 1.0.0
 */
function marketengine_export_reports()
{
    // Instantiate a singleton of this plugin
    $csvExport = new ME_Report_CSVExport();
}
add_action('admin_init', 'marketengine_export_reports');

