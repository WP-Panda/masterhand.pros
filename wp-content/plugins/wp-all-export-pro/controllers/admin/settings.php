<?php

/**
 * Admin Statistics page
 *
 * @author Pavel Kulbakin <p.kulbakin@gmail.com>
 */
class PMXE_Admin_Settings extends PMXE_Controller_Admin
{
    public $slug = 'wp-all-export-pro';

    /** @var  \Wpae\App\Service\License\LicenseActivator */
    private $licenseActivator;

    protected function init()
    {
        if(!current_user_can(PMXE_Plugin::$capabilities)) {
            die('Security check');
        }

        $this->licenseActivator = new \Wpae\App\Service\License\LicenseActivator();
    }

    public function index_action()
    {
        global $wp_roles;

        $this->data['post'] = $post = $this->input->post(PMXE_Plugin::getInstance()->getOption());

        $this->data['roles'] = $wp_roles->roles;

        if ($this->input->post('is_settings_submitted')) { // save settings form

            $this->save_settings($post);
        }

        if ($this->input->post('is_license_submitted')) {

            $post = $this->save_license($post);
        }

        if ($this->input->post('is_scheduling_license_submitted')) {

            $post = $this->save_scheduling_license($post);
        }

        if ($this->input->post('is_client_mode_submitted')) {
            $post = $this->save_client_mode_settings($post);
        }

        $post['scheduling_license_status'] = $this->check_scheduling_license();
        $this->data['is_license_active'] = false;
        if (!empty($post['license_status']) && $post['license_status'] == 'valid') {
            $this->data['is_license_active'] = true;
        }

        $this->data['is_scheduling_license_active'] = false;
        if (!empty($post['scheduling_license_status']) && $post['scheduling_license_status'] == 'valid') {
            $this->data['is_scheduling_license_active'] = true;
        }

        if ($this->input->post('is_templates_submitted')) { // delete templates form

            $this->save_templates();
        }

        $this->render();

    }

    public function dismiss_action()
    {

        PMXE_Plugin::getInstance()->updateOption("dismiss", 1);

        exit('OK');
    }

    /*
    *
    * Check plugin's license
    *
    */
    private function check_license()
    {
        global $wpdb;

        delete_transient(PMXE_Plugin::$cache_key);

        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_' . PMXE_Plugin::$cache_key));
        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_timeout_' . PMXE_Plugin::$cache_key));

        delete_site_transient('update_plugins');

        $options = PMXE_Plugin::getInstance()->getOption();

        return $this->licenseActivator->checkLicense(PMXE_Plugin::getEddName(), $options, \Wpae\App\Service\License\LicenseActivator::CONTEXT_PMXE);
    }

    private function check_scheduling_license()
    {
        $options = PMXE_Plugin::getInstance()->getOption();

        global $wpdb;

        delete_transient(PMXE_Plugin::$cache_key);

        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_' . PMXE_Plugin::$cache_key));
        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_timeout_' . PMXE_Plugin::$cache_key));

        return $this->licenseActivator->checkLicense(PMXE_Plugin::getSchedulingName(), $options, \Wpae\App\Service\License\LicenseActivator::CONTEXT_SCHEDULING);
    }

    /**
     * @param $post
     * @return mixed
     */
    private function save_scheduling_license($post)
    {
        check_admin_referer('edit-license', '_wpnonce_edit-scheduling-license');

        if (!$this->errors->get_error_codes()) { // no validation errors detected

            PMXE_Plugin::getInstance()->updateOption($post);
            if (empty($_POST['pmxe_scheduling_license_activate']) and empty($_POST['pmxe_scheduling_license_deactivate'])) {
                $post['scheduling_license_status'] = $this->check_scheduling_license();
                if ($post['scheduling_license_status'] == 'valid') {

                    $this->data['scheduling_license_message'] = __('License activated.', 'wp_all_import_plugin');
                }

                PMXE_Plugin::getInstance()->updateOption($post);

                if (class_exists('PMXI_Plugin')) {
                    if (method_exists('PMXI_Plugin', 'getSchedulingName')) {

                        if (!empty($post['scheduling_license'])) {
                            $schedulingLicenseData = array();
                            $schedulingLicenseData['scheduling_license_status'] = $post['scheduling_license_status'];
                            $schedulingLicenseData['scheduling_license'] = $post['scheduling_license'];

                            PMXI_Plugin::getInstance()->updateOption($schedulingLicenseData);
                        }
                    }
                }
                $this->activate_scheduling_licenses();

            }
        }

        $this->data['post'] = $post = PMXE_Plugin::getInstance()->getOption();
        return $post;
    }

    private function save_templates()
    {
        check_admin_referer('delete-templates', '_wpnonce_delete-templates');

        if ($this->input->post('import_templates')) {

            if (!empty($_FILES)) {
                $file_name = $_FILES['template_file']['name'];
                $file_size = $_FILES['template_file']['size'];
                $tmp_name = $_FILES['template_file']['tmp_name'];

                if (isset($file_name)) {

                    $filename = stripslashes($file_name);
                    $extension = strtolower(pmxe_getExtension($filename));

                    if (($extension != "txt")) {
                        $this->errors->add('form-validation', __('Unknown File extension. Only txt files are permitted', 'wp_all_export_plugin'));
                    } else {
                        $import_data = @file_get_contents($tmp_name);
                        if (!empty($import_data)) {
                            $templates_data = json_decode($import_data, true);

                            if (!empty($templates_data)) {
                                $templateOptions = empty($templates_data[0]['options']) ? false : unserialize($templates_data[0]['options']);
                                if (empty($templateOptions)) {
                                    $this->errors->add('form-validation', __('The template is invalid. Options are missing.', 'wp_all_export_plugin'));
                                } else {
                                    if (!isset($templateOptions['is_user_export'])) {
                                        $this->errors->add('form-validation', __('The template you\'ve uploaded is intended to be used with WP All Import plugin.', 'wp_all_export_plugin'));
                                    } else {
                                        $template = new PMXE_Template_Record();
                                        foreach ($templates_data as $template_data) {
                                            unset($template_data['id']);
                                            $template->clear()->set($template_data)->insert();
                                        }
                                        wp_redirect(add_query_arg('pmxe_nt', urlencode(sprintf(_n('%d template imported', '%d templates imported', count($templates_data), 'wp_all_export_plugin'), count($templates_data))), $this->baseUrl));
                                        die();
                                    }
                                }
                            } else $this->errors->add('form-validation', __('Wrong imported data format', 'wp_all_export_plugin'));
                        } else $this->errors->add('form-validation', __('File is empty or doesn\'t exests', 'wp_all_export_plugin'));
                    }
                } else $this->errors->add('form-validation', __('Undefined entry!', 'wp_all_export_plugin'));
            } else $this->errors->add('form-validation', __('Please select file.', 'wp_all_export_plugin'));

        } else {
            $templates_ids = $this->input->post('templates', array());
            if (empty($templates_ids)) {
                $this->errors->add('form-validation', __('Templates must be selected', 'wp_all_export_plugin'));
            }

            if (!$this->errors->get_error_codes()) { // no validation errors detected
                if ($this->input->post('delete_templates')) {
                    $template = new PMXE_Template_Record();
                    foreach ($templates_ids as $template_id) {
                        $template->clear()->set('id', $template_id)->delete();
                    }
                    wp_redirect(add_query_arg('pmxe_nt', urlencode(sprintf(_n('%d template deleted', '%d templates deleted', count($templates_ids), 'wp_all_export_plugin'), count($templates_ids))), $this->baseUrl));
                    die();
                }
                if ($this->input->post('export_templates')) {
                    $export_data = array();
                    $template = new PMXE_Template_Record();
                    foreach ($templates_ids as $template_id) {
                        $export_data[] = $template->clear()->getBy('id', $template_id)->toArray(TRUE);
                    }

                    $uploads = wp_upload_dir();
                    $targetDir = $uploads['basedir'] . DIRECTORY_SEPARATOR . PMXE_Plugin::TEMP_DIRECTORY;
                    $export_file_name = "templates_" . uniqid() . ".txt";
                    file_put_contents($targetDir . DIRECTORY_SEPARATOR . $export_file_name, json_encode($export_data));

                    PMXE_download::csv($targetDir . DIRECTORY_SEPARATOR . $export_file_name);

                }
            }
        }
    }

    /**
     * @param $post
     * @return mixed
     */
    private function save_license($post)
    {
        check_admin_referer('edit-license', '_wpnonce_edit-license');

        if (!$this->errors->get_error_codes()) { // no validation errors detected

            PMXE_Plugin::getInstance()->updateOption($post);

            if (empty($_POST['pmxe_license_activate']) and empty($_POST['pmxe_license_deactivate'])) {
                $post['license_status'] = $this->check_license();
                if ($post['license_status'] == 'valid') {
                    $this->data['license_message'] = __('License activated.', 'wp_all_import_plugin');
                }
                PMXE_Plugin::getInstance()->updateOption($post);

            }
            isset($_POST['pmxe_license_activate']) and $this->activate_licenses();
        }

        $this->data['post'] = $post = PMXE_Plugin::getInstance()->getOption();
        return $post;
    }

    /**
     * @param $post
     */
    private function save_settings($post)
    {
        check_admin_referer('edit-settings', '_wpnonce_edit-settings');

        if (!$this->errors->get_error_codes()) { // no validation errors detected

            PMXE_Plugin::getInstance()->updateOption($post);

            if (empty($_POST['pmxe_license_activate']) and empty($_POST['pmxe_license_deactivate'])) {

                $post['license_status'] = $this->check_license();
                PMXE_Plugin::getInstance()->updateOption($post);
            }

            isset($_POST['pmxe_license_activate']) and $this->activate_licenses();

            wp_redirect(add_query_arg('pmxe_nt', urlencode(__('Settings saved', 'wp_all_export_plugin')), $this->baseUrl));
            die();
        }
    }

    private function save_client_mode_settings($post)
    {
        check_admin_referer('edit-client-mode-settings', '_wpnonce_edit-client_mode_settings');

        if (!$this->errors->get_error_codes()) { // no validation errors detected

            global $wp_roles;

            $all_roles = array_keys($wp_roles->roles);

            $client_mode_roles = $this->input->post('client_mode_roles');

            foreach ($all_roles as $role_id) {

                $role = get_role($role_id);

                if ($role->has_cap('manage_options')) {
                    continue;
                }

                if (in_array($role_id, $client_mode_roles)) {
                    $role->add_cap(PMXE_Plugin::CLIENT_MODE_CAP);
                } else {
                    $role->remove_cap(PMXE_Plugin::CLIENT_MODE_CAP);
                }

            }

            PMXE_Plugin::getInstance()->updateOption(array('client_mode_roles' => $client_mode_roles));

            wp_redirect(add_query_arg('pmxe_nt', urlencode(__('Settings saved', 'wp_all_export_plugin')), $this->baseUrl));

            die();
        }
    }

    /*
     * Activate licenses for main plugin and all premium addons
     */
    private function activate_licenses()
    {

        // listen for our activate button to be clicked
        if (isset($_POST['pmxe_license_activate'])) {

            // retrieve the license from the database
            $options = PMXE_Plugin::getInstance()->getOption();

            global $wpdb;

            delete_transient(PMXE_Plugin::$cache_key);

            $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_' . PMXE_Plugin::$cache_key));
            $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_timeout_' . PMXE_Plugin::$cache_key));

            delete_site_transient('update_plugins');

            $this->licenseActivator->activateLicense(PMXE_Plugin::getEddName(), \Wpae\App\Service\License\LicenseActivator::CONTEXT_PMXE);
        }
    }

    /*
    *
    * Activate licenses for main plugin and all premium addons
    *
    */
    private function activate_scheduling_licenses()
    {
        // listen for our activate button to be clicked

        global $wpdb;

        delete_transient(PMXE_Plugin::$cache_key);

        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_' . PMXE_Plugin::$cache_key));
        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $this->slug . '_timeout_' . PMXE_Plugin::$cache_key));

        delete_site_transient('update_plugins');

        // retrieve the license from the database
        return $this->licenseActivator->activateLicense(PMXE_Plugin::getSchedulingName(), \Wpae\App\Service\License\LicenseActivator::CONTEXT_SCHEDULING);

        return false;
    }
}