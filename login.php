<?php

/*
Plugin Name: Login with Salesforce
Plugin URI: http://miniorange.com/
Description: Login into WordPress Salesforce communities
Version: 1.0.2
Author: miniOrange
Author URI: http://miniorange.com/
*/
include_once dirname(__FILE__) . '/mo_salesforce_sso_widget.php';
require 'helper/mo_salesforce_customer.php';
require 'pages/mo_salesforce_settings_page.php';
require 'helper/MetadataReader.php';
require 'pages/feedback_form.php';
require_once "helper/PointersManager.php";
require_once dirname(__FILE__) . '/includes/lib/MoPointer.php';
class mo_salesforce_mo_login
{
    function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'mo_plugin_activate'));
        register_deactivation_hook(__FILE__, array($this, 'mo_deactivate'));
        add_action('admin_menu', array($this, 'miniorange_sso_menu'));
        add_action('admin_init', array($this, 'miniorange_login_widget_saml_save_settings'));
        add_action('admin_enqueue_scripts', array($this, 'mo_plugin_settings_style'));
        add_action('admin_enqueue_scripts', array($this, 'mo_plugin_settings_script'));
        add_action('wp_authenticate', array($this, 'mo_authenticate'));
        add_action('login_form', array($this, 'mo_modify_login_form'));
        add_action('admin_footer', array($this, 'mo_feedback_request'));
        add_action('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'mo_plugin_action_links'));
        remove_action('admin_notices', array($this, 'mo_success_message'));
        remove_action('admin_notices', array($this, 'mo_error_message'));
    }
    function mo_feedback_request()
    {
        display_saml_feedback_form();
    }
    function mo_login_widget_saml_options()
    {
        global $wpdb;
        $host_name = mo_options_plugin_constants::HOSTNAME;
        $brokerService = get_option('mo_saml_enable_cloud_broker');
        $token = get_option('saml_x509_certificate');
        if (empty($brokerService)) {
            update_option('mo_saml_enable_cloud_broker', 'false');
        }
        mo_register_saml_sso();
    }
    function mo_success_message()
    {
        $class = "error";
        $message = get_option('mo_saml_message');
        echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
    }
    function mo_error_message()
    {
        $class = "updated";
        $message = get_option('mo_saml_message');
        echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
    }
    public function mo_deactivate()
    {
        if (mo_is_customer_registered_saml(false)) {
            return;
        }
        if (!mo_is_curl_installed()) {
            return;
        }

        wp_redirect('plugins.php');
    }
    public function mo_salesforce_remove_account()
    {
        if (!is_multisite()) {
            //delete all customer related key-value pairs
            delete_option('mo_saml_host_name');
            delete_option('mo_saml_new_registration');
            delete_option('mo_saml_admin_phone');
            delete_option('mo_saml_admin_password');
            delete_option('mo_saml_verify_customer');
            delete_option('mo_saml_admin_customer_key');
            delete_option('mo_saml_admin_api_key');
            delete_option('mo_saml_customer_token');
            delete_option('mo_saml_admin_email');
            delete_option('mo_saml_message');
            delete_option('mo_saml_registration_status');
            delete_option('mo_saml_idp_config_complete');
            delete_option('mo_saml_transactionId');
            delete_option('mo_proxy_host');
            delete_option('mo_proxy_username');
            delete_option('mo_proxy_port');
            delete_option('mo_proxy_password');
            delete_option('mo_saml_show_mo_idp_message');
        } else {
            global $wpdb;
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
            $original_blog_id = get_current_blog_id();
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                //delete all your options
                //E.g: delete_option( {option name} );
                delete_option('mo_saml_host_name');
                delete_option('mo_saml_new_registration');
                delete_option('mo_saml_admin_phone');
                delete_option('mo_saml_admin_password');
                delete_option('mo_saml_verify_customer');
                delete_option('mo_saml_admin_customer_key');
                delete_option('mo_saml_admin_api_key');
                delete_option('mo_saml_customer_token');
                delete_option('mo_saml_message');
                delete_option('mo_saml_registration_status');
                delete_option('mo_saml_idp_config_complete');
                delete_option('mo_saml_transactionId');
                delete_option('mo_saml_show_mo_idp_message');
                delete_option('mo_saml_admin_email');
            }
            switch_to_blog($original_blog_id);
        }
    }
    function mo_plugin_settings_style($page)
    {
        if ($page != 'toplevel_page_mo_saml_settings') {
            return;
        }
        if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'licensing') {
            wp_enqueue_style('mo_saml_bootstrap_css', plugins_url('includes/css/bootstrap/bootstrap.min.css', __FILE__));
        }
        wp_enqueue_style('mo_saml_admin_settings_jquery_style', plugins_url('includes/css/jquery.ui.css', __FILE__));
        wp_enqueue_style('mo_saml_admin_settings_style', plugins_url('includes/css/style_settings.css?ver=4.8.60', __FILE__));
        wp_enqueue_style('mo_saml_admin_settings_phone_style', plugins_url('includes/css/phone.css', __FILE__));
        wp_enqueue_style('mo_saml_wpb-fa', plugins_url('includes/css/font-awesome.min.css', __FILE__));
        $file = plugin_dir_path(__FILE__) . 'helper/pointers.php';
        // Arguments: pointers php file, version (dots will be replaced), prefix
        $manager = new PointersManager($file, '1.0.0', 'custom_admin_pointers');
        $manager->parse();
        $pointers = $manager->filter($page);
        if (empty($pointers)) {
            // nothing to do if no pointers pass the filter
            return;
        }
        wp_enqueue_style('wp-pointer');
        $js_url = plugins_url('includes' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'pointers.js', __FILE__);
        wp_enqueue_script('custom_admin_pointers', $js_url, array('wp-pointer'), NULL, TRUE);
        // data to pass to javascript
        $data = array('next_label' => __('Next'), 'close_label' => __('Close'), 'pointers' => $pointers);
        wp_localize_script('custom_admin_pointers', 'MyAdminPointers', $data);
    }
    function mo_plugin_settings_script($page)
    {
        if ($page != 'toplevel_page_mo_saml_settings') {
            return;
        }
        wp_enqueue_script('jquery');
        wp_enqueue_script('mo_saml_admin_settings_script', plugins_url('includes/js/settings.js', __FILE__));
        wp_enqueue_script('mo_saml_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__));
        if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'licensing') {
            wp_enqueue_script('mo_saml_modernizr_script', plugins_url('includes/js/modernizr.js', __FILE__));
            wp_enqueue_script('mo_saml_popover_script', plugins_url('includes/js/bootstrap/popper.min.js', __FILE__));
            wp_enqueue_script('mo_saml_bootstrap_script', plugins_url('includes/js/bootstrap/bootstrap.min.js', __FILE__));
        }
    }
    public function mo_plugin_activate()
    {

        if (is_multisite()) {
            global $wpdb;
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
            $original_blog_id = get_current_blog_id();
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                update_option('mo_saml_guest_log', true);
                update_option('mo_saml_guest_enabled', true);
                update_option('mo_saml_free_version', 1);
            }
            switch_to_blog($original_blog_id);
        } else {
            update_option('mo_saml_guest_log', true);
            update_option('mo_saml_guest_enabled', true);
            update_option('mo_saml_free_version', 1);
        }
    }

    function miniorange_login_widget_saml_save_settings()
    {
        if (current_user_can('manage_options')) {
            if (isset($_POST['option']) and $_POST['option'] == "clear_pointers") {


                $uid = get_current_user_id();
                $array_dissmised_pointers = explode(',', (string) get_user_meta($uid, 'dismissed_wp_pointers', TRUE));

                if (isset($_GET['tab'])) {
                    $active_tab = $_GET['tab'];
                } else {
                    $active_tab = 'sp_setup';
                }
                if ($active_tab == 'sp_setup') {
                    $array_dissmised_pointers = array_diff($array_dissmised_pointers, mo_options_enum_pointers::$SERVICE_PROVIDER);
                } elseif ($active_tab == 'sp_metadata') {
                    $array_dissmised_pointers = array_diff($array_dissmised_pointers, mo_options_enum_pointers::$IDENTITY_PROVIDER);
                } elseif ($active_tab == 'attribute_role') {
                    $array_dissmised_pointers = array_diff($array_dissmised_pointers, mo_options_enum_pointers::$ATTRIBUTE_MAPPING);
                } elseif ($active_tab == 'general') {
                    $array_dissmised_pointers = array_diff($array_dissmised_pointers, mo_options_enum_pointers::$REDIRECTION_LINK);
                }
                update_user_meta($uid, 'dismissed_wp_pointers', implode(",", $array_dissmised_pointers));
                return;
            }
            if (isset($_POST['option']) and $_POST['option'] == "mo_saml_mo_idp_message") {
                update_option('mo_saml_show_mo_idp_message', 1);
                return;
            }
            if (isset($_POST['option']) and $_POST['option'] == "mo_continue_as_guest") {

                update_option('mo_saml_guest_enabled', true);
                update_option('mo_saml_message', 'Logged in as Guest.');
                $this->mo_salesforce_show_success_message();
                return;
            }
            if (isset($_POST['option']) and $_POST['option'] == "change_miniorange") {
                self::mo_salesforce_remove_account();
                update_option('mo_saml_guest_enabled', true);
                //update_option( 'mo_saml_message', 'Logged out of miniOrange account' );
                //$this->mo_saml_show_success_message();
                return;
            }
            if (isset($_POST['option']) and $_POST['option'] == "login_widget_saml_save_settings") {
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Identity Provider Configuration failed.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                //validation and sanitization
                $saml_identity_name = '';
                $saml_login_url = '';
                $saml_issuer = '';
                $saml_x509_certificate = '';
                if ($this->mo_salesforce_check_empty_or_null($_POST['saml_identity_name']) || $this->mo_salesforce_check_empty_or_null($_POST['saml_login_url']) || $this->mo_salesforce_check_empty_or_null($_POST['saml_issuer'])) {
                    update_option('mo_saml_message', 'All the fields are required. Please enter valid entries.');
                    $this->mo_salesforce_show_error_message();
                    return;
                } else {
                    if (!preg_match("/^\\w*\$/", $_POST['saml_identity_name'])) {
                        update_option('mo_saml_message', 'Please match the requested format for Identity Provider Name. Only alphabets, numbers and underscore is allowed.');
                        $this->mo_salesforce_show_error_message();
                        return;
                    } else {
                        $saml_identity_name = trim($_POST['saml_identity_name']);
                        $saml_login_url = trim($_POST['saml_login_url']);
                        $saml_issuer = trim($_POST['saml_issuer']);
                        $saml_identity_provider_guide_name = trim($_POST['saml_identity_provider_guide_name']);
                        $saml_x509_certificate = $_POST['saml_x509_certificate'];
                    }
                }
                update_option('saml_identity_name', $saml_identity_name);
                update_option('saml_login_url', $saml_login_url);
                update_option('saml_issuer', $saml_issuer);
                update_option('saml_identity_provider_guide_name', $saml_identity_provider_guide_name);
                //update_option('saml_x509_certificate', $saml_x509_certificate);
                if (isset($_POST['saml_response_signed'])) {
                    update_option('saml_response_signed', 'checked');
                } else {
                    update_option('saml_response_signed', 'Yes');
                }
                foreach ($saml_x509_certificate as $key => $value) {
                    if (empty($value)) {
                        unset($saml_x509_certificate[$key]);
                    } else {
                        $saml_x509_certificate[$key] = Utilities::sanitize_certificate($value);
                        if (!@openssl_x509_read($saml_x509_certificate[$key])) {
                            update_option('mo_saml_message', 'Invalid certificate: Please provide a valid certificate.');
                            $this->mo_salesforce_show_error_message();
                            delete_option('saml_x509_certificate');
                            return;
                        }
                    }
                }
                if (empty($saml_x509_certificate)) {
                    update_option("mo_saml_message", 'Invalid Certificate: Please provide a certificate');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                update_option('saml_x509_certificate', maybe_serialize($saml_x509_certificate));
                if (isset($_POST['saml_assertion_signed'])) {
                    update_option('saml_assertion_signed', 'checked');
                } else {
                    update_option('saml_assertion_signed', 'Yes');
                }
                update_option('mo_saml_message', 'Identity Provider details saved successfully.');
                $this->mo_salesforce_show_success_message();
            }
            //Save Attribute Mapping
            if (isset($_POST['option']) and $_POST['option'] == "login_widget_saml_attribute_mapping") {
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Attribute Mapping failed.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                if (isset($_POST['saml_am_first_name']) && !empty($_POST['saml_am_first_name'])) {
                    update_option('saml_am_first_name', stripslashes($_POST['saml_am_first_name']));
                }
                if (isset($_POST['saml_am_last_name']) && !empty($_POST['saml_am_last_name'])) {
                    update_option('saml_am_last_name', stripslashes($_POST['saml_am_last_name']));
                }
                update_option('mo_saml_message', 'Attribute Mapping details saved successfully');
                $this->mo_salesforce_show_success_message();
            }
            //Save Role Mapping
            if (isset($_POST['option']) and $_POST['option'] == "login_widget_saml_role_mapping") {
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Save Role Mapping failed.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                update_option('saml_am_default_user_role', $_POST['saml_am_default_user_role']);
                update_option('mo_saml_message', 'Role Mapping details saved successfully.');
                $this->mo_salesforce_show_success_message();
            }
            if (isset($_POST['option']) and $_POST['option'] == "saml_upload_metadata") {
                if (!function_exists('wp_handle_upload')) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                }
                $this->_handle_upload_metadata();
            }
            if (isset($_POST['option']) and $_POST['option'] == "mo_saml_register_customer") {
                //register the admin to miniOrange
                $user = wp_get_current_user();
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Registration failed.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                //validation and sanitization
                $email = '';
                $password = '';
                $confirmPassword = '';
                if ($this->mo_salesforce_check_empty_or_null($_POST['email']) || $this->mo_salesforce_check_empty_or_null($_POST['password']) || $this->mo_salesforce_check_empty_or_null($_POST['confirmPassword'])) {
                    update_option('mo_saml_message', 'Please enter the required fields.');
                    $this->mo_salesforce_show_error_message();
                    return;
                } else {
                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        update_option('mo_saml_message', 'Please enter a valid email address.');
                        $this->mo_salesforce_show_error_message();
                        return;
                    } else {
                        if ($this->checkPasswordpattern(strip_tags($_POST['password']))) {
                            update_option('mo_saml_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.');
                            $this->mo_salesforce_show_error_message();
                            return;
                        } else {
                            $email = sanitize_email($_POST['email']);
                            $password = stripslashes(strip_tags($_POST['password']));
                            $confirmPassword = stripslashes(strip_tags($_POST['confirmPassword']));
                        }
                    }
                }
                update_option('mo_saml_admin_email', $email);
                if (strcmp($password, $confirmPassword) == 0) {
                    update_option('mo_saml_admin_password', $password);
                    $email = get_option('mo_saml_admin_email');
                    $customer = new mo_salesforce_customer();
                    $content = json_decode($customer->mo_salesforce_check_customer(), true);
                    //print_r($content);exit;
                    if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') == 0) {
                        //
                        $response = $this->mo_salesforce_create_customer();
                        if (is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success') {
                            wp_redirect(admin_url('/admin.php?page=mo_saml_settings&tab=licensing'), 301);
                            exit;
                        }
                    } else {
                        $response = $this->mo_salesforce_get_current_customer();
                        if (is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success') {
                            wp_redirect(admin_url('/admin.php?page=mo_saml_settings&tab=licensing'), 301);
                            exit;
                        }
                        //$this->mo_saml_show_error_message();
                    }
                } else {
                    update_option('mo_saml_message', 'Passwords do not match.');
                    delete_option('mo_saml_verify_customer');
                    $this->mo_salesforce_show_error_message();
                }
                return;
                //new starts here
            }
            if (isset($_POST['option']) and $_POST['option'] == "mo_saml_verify_customer") {
                //register the admin to miniOrange
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Login failed.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                //validation and sanitization
                $email = '';
                $password = '';
                if ($this->mo_salesforce_check_empty_or_null($_POST['email']) || $this->mo_salesforce_check_empty_or_null($_POST['password'])) {
                    update_option('mo_saml_message', 'All the fields are required. Please enter valid entries.');
                    $this->mo_salesforce_show_error_message();
                    return;
                } else {
                    if ($this->checkPasswordpattern(strip_tags($_POST['password']))) {
                        update_option('mo_saml_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.');
                        $this->mo_salesforce_show_error_message();
                        return;
                    } else {
                        $email = sanitize_email($_POST['email']);
                        $password = stripslashes(strip_tags($_POST['password']));
                    }
                }
                update_option('mo_saml_admin_email', $email);
                update_option('mo_saml_admin_password', $password);
                $customer = new mo_salesforce_customer();
                $content = $customer->mo_salesforce_get_customer_key();
                $customerKey = json_decode($content, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    update_option('mo_saml_admin_customer_key', $customerKey['id']);
                    update_option('mo_saml_admin_api_key', $customerKey['apiKey']);
                    update_option('mo_saml_customer_token', $customerKey['token']);
                    update_option('mo_saml_admin_phone', $customerKey['phone']);
                    $certificate = get_option('saml_x509_certificate');
                    if (empty($certificate)) {
                        update_option('mo_saml_free_version', 1);
                    }
                    update_option('mo_saml_admin_password', '');
                    update_option('mo_saml_message', 'Customer retrieved successfully');
                    update_option('mo_saml_registration_status', 'Existing User');
                    delete_option('mo_saml_verify_customer');
                    $this->mo_salesforce_show_success_message();
                    //if(is_array($response) && array_key_exists('status', $response) && $response['status'] == 'success'){
                    wp_redirect(admin_url('/admin.php?page=mo_saml_settings&tab=licensing'), 301);
                    exit;
                    //}
                } else {
                    update_option('mo_saml_message', 'Invalid username or password. Please try again.');
                    $this->mo_salesforce_show_error_message();
                }
                update_option('mo_saml_admin_password', '');
            } else {
                if (isset($_POST['option']) and $_POST['option'] == "mo_saml_contact_us_query_option") {
                    if (!mo_is_curl_installed()) {
                        update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Query submit failed.');
                        $this->mo_salesforce_show_error_message();
                        return;
                    }
                    // Contact Us query
                    $email = $_POST['mo_saml_contact_us_email'];
                    $phone = $_POST['mo_saml_contact_us_phone'];
                    $query = $_POST['mo_saml_contact_us_query'];
                    $customer = new mo_salesforce_customer();
                    if ($this->mo_salesforce_check_empty_or_null($email) || $this->mo_salesforce_check_empty_or_null($query)) {
                        update_option('mo_saml_message', 'Please fill up Email and Query fields to submit your query.');
                        $this->mo_salesforce_show_error_message();
                    } else {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            update_option('mo_saml_message', 'Please enter a valid email address.');
                            $this->mo_salesforce_show_error_message();
                        } else {
                            $submited = $customer->mo_salesforce_submit_contact_us($email, $phone, $query);
                            if ($submited == false) {
                                update_option('mo_saml_message', 'Your query could not be submitted. Please try again.');
                                $this->mo_salesforce_show_error_message();
                            } else {
                                update_option('mo_saml_message', 'Thanks for getting in touch! We shall get back to you shortly.');
                                $this->mo_salesforce_show_success_message();
                            }
                        }
                    }
                } else {
                    if (isset($_POST['option']) and $_POST['option'] == "mo_saml_go_back") {
                        update_option('mo_saml_registration_status', '');
                        update_option('mo_saml_verify_customer', '');
                        delete_option('mo_saml_new_registration');
                        delete_option('mo_saml_admin_email');
                        delete_option('mo_saml_admin_phone');
                    } else {
                        if (isset($_POST['option']) and $_POST['option'] == "mo_saml_goto_login") {
                            delete_option('mo_saml_new_registration');
                            update_option('mo_saml_verify_customer', 'true');
                        } else {
                            if (isset($_POST['option']) and $_POST['option'] == "mo_saml_register_with_phone_option") {
                                if (!mo_is_curl_installed()) {
                                    update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Resend OTP failed.');
                                    $this->mo_salesforce_show_error_message();
                                    return;
                                }
                                $phone = sanitize_text_field($_POST['phone']);
                                $phone = str_replace(' ', '', $phone);
                                $phone = str_replace('-', '', $phone);
                                update_option('mo_saml_admin_phone', $phone);
                                $customer = new mo_salesforce_customer();
                                $content = json_decode($customer->mo_salesforce_send_otp_token('', $phone, false, true), true);
                                if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                                    update_option('mo_saml_message', ' A one time passcode is sent to ' . get_option('mo_saml_admin_phone') . '. Please enter the otp here to verify your email.');
                                    update_option('mo_saml_transactionId', $content['txId']);
                                    update_option('mo_saml_registration_status', 'MO_OTP_DELIVERED_SUCCESS_PHONE');
                                    $this->mo_salesforce_show_success_message();
                                } else {
                                    update_option('mo_saml_message', 'There was an error in sending SMS. Please click on Resend OTP to try again.');
                                    update_option('mo_saml_registration_status', 'MO_OTP_DELIVERED_FAILURE_PHONE');
                                    $this->mo_salesforce_show_error_message();
                                }
                            } else {
                                if (isset($_POST['option']) and $_POST['option'] == "mo_saml_force_authentication_option") {
                                    if (mo_is_sp_configured()) {
                                        if (array_key_exists('mo_saml_force_authentication', $_POST)) {
                                            $enable_redirect = $_POST['mo_saml_force_authentication'];
                                        } else {
                                            $enable_redirect = 'false';
                                        }
                                        if ($enable_redirect == 'true') {
                                            update_option('mo_saml_force_authentication', 'true');
                                        } else {
                                            update_option('mo_saml_force_authentication', '');
                                        }
                                        update_option('mo_saml_message', 'Sign in options updated.');
                                        $this->mo_salesforce_show_success_message();
                                    } else {
                                        update_option('mo_saml_message', 'Please complete <a href="' . add_query_arg(array('tab' => 'save'), $_SERVER['REQUEST_URI']) . '" />Service Provider</a> configuration first.');
                                        $this->mo_salesforce_show_error_message();
                                    }
                                } else {
                                    if (isset($_POST['option']) and $_POST['option'] == "mo_saml_enable_login_redirect_option") {
                                        if (mo_is_sp_configured()) {
                                            if (array_key_exists('mo_saml_enable_login_redirect', $_POST)) {
                                                $enable_redirect = $_POST['mo_saml_enable_login_redirect'];
                                            } else {
                                                $enable_redirect = 'false';
                                            }
                                            if ($enable_redirect == 'true') {
                                                update_option('mo_saml_enable_login_redirect', 'true');
                                            } else {
                                                update_option('mo_saml_enable_login_redirect', '');
                                                update_option('mo_saml_allow_wp_signin', '');
                                            }
                                            update_option('mo_saml_message', 'Sign in options updated.');
                                            $this->mo_salesforce_show_success_message();
                                        } else {
                                            update_option('mo_saml_message', 'Please complete <a href="' . add_query_arg(array('tab' => 'save'), $_SERVER['REQUEST_URI']) . '" />Service Provider</a> configuration first.');
                                            $this->mo_salesforce_show_error_message();
                                        }
                                    } else {
                                        if (isset($_POST['option']) && $_POST['option'] == 'mo_saml_forgot_password_form_option') {
                                            if (!mo_is_curl_installed()) {
                                                update_option('mo_saml_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Resend OTP failed.');
                                                $this->mo_salesforce_show_error_message();
                                                return;
                                            }
                                            $email = get_option('mo_saml_admin_email');
                                            $customer = new mo_salesforce_customer();
                                            $content = json_decode($customer->mo_salesforce_forgot_password($email), true);
                                            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                                                update_option('mo_saml_message', 'Your password has been reset successfully. Please enter the new password sent to ' . $email . '.');
                                                $this->mo_salesforce_show_success_message();
                                            } else {
                                                update_option('mo_saml_message', 'An error occured while processing your request. Please Try again.');
                                                $this->mo_salesforce_show_error_message();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            /**
             * Added for feedback mechanisms
             */
            if (isset($_POST['option']) and $_POST['option'] == 'mo_skip_feedback') {
                update_option('mo_saml_message', 'Plugin deactivated successfully');
                $this->mo_salesforce_show_success_message();
                deactivate_plugins(__FILE__);
            }
            if (isset($_POST['mo_feedback']) and $_POST['mo_feedback'] == 'mo_feedback') {
                $user = wp_get_current_user();
                $message = 'Plugin Deactivated';
                $deactivate_reason_message = array_key_exists('query_feedback', $_POST) ? $_POST['query_feedback'] : false;
                if (isset($deactivate_reason_message)) {
                    $message .= ':' . $deactivate_reason_message;
                }
                $email = get_option("saml_am_email");
                if ($email == '') {
                    $email = $user->user_email;
                }
                $phone = get_option('mo_saml_admin_phone');
                //only reason
                $feedback_reasons = new mo_salesforce_customer();
                if (!mo_is_curl_installed()) {
                    deactivate_plugins(__FILE__);
                    wp_redirect('plugins.php');
                } else {
                    $submited = json_decode($feedback_reasons->mo_salesforce_send_email_alert($email, $phone, $message), true);
                    if (json_last_error() == JSON_ERROR_NONE) {
                        if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                            update_option('mo_saml_message', $submited['message']);
                            $this->mo_salesforce_show_error_message();
                        } else {
                            if ($submited == false) {
                                update_option('mo_saml_message', 'Error while submitting the query.');
                                $this->mo_salesforce_show_error_message();
                            }
                        }
                    }
                    deactivate_plugins(__FILE__);
                    update_option('mo_saml_message', 'Thank you for the feedback.');
                    $this->mo_salesforce_show_success_message();
                }
            }
            if (isset($_POST['option']) and $_POST['option'] == "molicensingplanselection") {
                $env_type = $_POST['envtype'];
                $idp_num = $_POST['idpnum'];
                $auto_redirect = $_POST['autoredirect'];
                $attr_map = $_POST['attrmap'];
                $role_map = $_POST['rolemap'];
                $slo = $_POST['slo'];
                $addon = $_POST['addon'];
                $licensing_plan = "Single Site - Standard";
                if ($env_type == 'multisite') {
                    $licensing_plan = "Multisite Network - Premium";
                    if ($addon == 'yes' && $idp_num == '1+') {
                        $licensing_plan = "Multisite Network - Business";
                    } else {
                        if ($idp_num == '1+') {
                            $licensing_plan = "Multisite Network - Business";
                        } else {
                            if ($addon == 'yes') {
                                $licensing_plan = "Multisite Network - Enterprise";
                            }
                        }
                    }
                } else {
                    if ($addon == 'yes' || $idp_num == '1+') {
                        $licensing_plan = "Single Site - Enterprise";
                    } else {
                        if ($slo == 'yes' || $role_map == 'yes' || $auto_redirect == 'yes' || $attr_map == 'yes') {
                            $licensing_plan = "Single Site - Premium";
                        }
                    }
                }
                update_option('mo_license_plan_from_feedback', $licensing_plan);
                update_option('mo_saml_license_message', $licensing_plan . ' Plan (highlighted with red border) will be the best suitable licensing plan as per the SSO details provided by you. If you still have any conern, please write us at info@xecurify.com.');
            }
        }
    }
    function mo_salesforce_show_error_message()
    {
        remove_action('admin_notices', array($this, 'mo_error_message'));
        add_action('admin_notices', array($this, 'mo_success_message'));
    }
    public function mo_salesforce_check_empty_or_null($value)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        return false;
    }
    private function mo_salesforce_show_success_message()
    {
        remove_action('admin_notices', array($this, 'mo_success_message'));
        add_action('admin_notices', array($this, 'mo_error_message'));
    }
    function _handle_upload_metadata()
    {
        if (isset($_FILES['metadata_file']) || isset($_POST['metadata_url'])) {
            if (!empty($_FILES['metadata_file']['tmp_name'])) {
                $file = @file_get_contents($_FILES['metadata_file']['tmp_name']);
            } else {
                if (!mo_is_curl_installed()) {
                    update_option('mo_saml_message', 'PHP cURL extension is not installed or disabled. Cannot fetch metadata from URL.');
                    $this->mo_salesforce_show_error_message();
                    return;
                }
                $url = filter_var($_POST['metadata_url'], FILTER_SANITIZE_URL);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $file = curl_exec($ch);
                curl_close($ch);
            }
            $this->mo_salesforce_upload_metadata($file);
        }
    }
    function mo_salesforce_upload_metadata($file)
    {
        $old_error_handler = set_error_handler(array($this, 'handleXmlError'));
        $document = new DOMDocument();
        $document->loadXML($file);
        restore_error_handler();
        $first_child = $document->firstChild;
        if (!empty($first_child)) {
            $metadata = new IDPMetadataReader($document);
            $identity_providers = $metadata->getIdentityProviders();
            if (empty($identity_providers) && !empty($_FILES['metadata_file']['tmp_name'])) {
                update_option('mo_saml_message', 'Please provide a valid metadata file.');
                $this->mo_salesforce_show_error_message();
                return;
            }
            if (empty($identity_providers) && !empty($_POST['metadata_url'])) {
                update_option('mo_saml_message', 'Please provide a valid metadata URL.');
                $this->mo_salesforce_show_error_message();
                return;
            }
            foreach ($identity_providers as $key => $idp) {
                //$saml_identity_name = preg_match("/^[a-zA-Z0-9-\._ ]+/", $idp->getIdpName()) ? $idp->getIdpName() : "";
                $saml_identity_name = $_POST['saml_identity_metadata_provider'];
                $saml_login_url = $idp->getLoginURL('HTTP-Redirect');
                $saml_issuer = $idp->getEntityID();
                $saml_x509_certificate = $idp->getSigningCertificate();
                update_option('saml_identity_name', $saml_identity_name);
                update_option('saml_login_url', $saml_login_url);
                update_option('saml_issuer', $saml_issuer);
                //certs already sanitized in Metadata Reader
                update_option('saml_x509_certificate', maybe_serialize($saml_x509_certificate));
                break;
            }
            update_option('mo_saml_message', 'Identity Provider details saved successfully.');
            $this->mo_salesforce_show_success_message();
        } else {
            if (!empty($_FILES['metadata_file']['tmp_name'])) {
                update_option('mo_saml_message', 'Please provide a valid metadata file.');
                $this->mo_salesforce_show_error_message();
            }
            if (!empty($_POST['metadata_url'])) {
                update_option('mo_saml_message', 'Please provide a valid metadata URL.');
                $this->mo_salesforce_show_error_message();
            }
        }
    }
    function mo_salesforce_get_current_customer()
    {
        $customer = new mo_salesforce_customer();
        $content = $customer->mo_salesforce_get_customer_key();
        $customerKey = json_decode($content, true);
        //var_dump($customerKey);exit;
        $response = array();
        if (json_last_error() == JSON_ERROR_NONE) {
            update_option('mo_saml_admin_customer_key', $customerKey['id']);
            update_option('mo_saml_admin_api_key', $customerKey['apiKey']);
            update_option('mo_saml_customer_token', $customerKey['token']);
            update_option('mo_saml_admin_password', '');
            $certificate = get_option('saml_x509_certificate');
            if (empty($certificate)) {
                update_option('mo_saml_free_version', 1);
            }
            delete_option('mo_saml_verify_customer');
            delete_option('mo_saml_new_registration');
            $response['status'] = "success";
            return $response;
        } else {
            update_option('mo_saml_message', 'You already have an account with miniOrange. Please enter a valid password.');
            $this->mo_salesforce_show_error_message();
            //update_option( 'mo_saml_verify_customer', 'true' );
            //delete_option( 'mo_saml_new_registration' );
            $response['status'] = "error";
            return $response;
        }
    }
    function mo_salesforce_create_customer()
    {
        $customer = new mo_salesforce_customer();
        $customerKey = json_decode($customer->mo_salesforce_create_customer(), true);
        $response = array();
        //print_r($customerKey);
        if (strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {
            $api_response = $this->mo_salesforce_get_current_customer();
            //print_r($api_response);exit;
            if ($api_response) {
                $response['status'] = "success";
            } else {
                $response['status'] = "error";
            }
        } else {
            if (strcasecmp($customerKey['status'], 'SUCCESS') == 0) {
                update_option('mo_saml_admin_customer_key', $customerKey['id']);
                update_option('mo_saml_admin_api_key', $customerKey['apiKey']);
                update_option('mo_saml_customer_token', $customerKey['token']);
                update_option('mo_saml_free_version', 1);
                update_option('mo_saml_admin_password', '');
                update_option('mo_saml_message', 'Thank you for registering with miniorange.');
                update_option('mo_saml_registration_status', '');
                delete_option('mo_saml_verify_customer');
                delete_option('mo_saml_new_registration');
                $response['status'] = "success";
                return $response;
            }
        }
        update_option('mo_saml_admin_password', '');
        return $response;
    }
    function miniorange_sso_menu()
    {
        //Add miniOrange SAML SSO
        $slug = 'mo_saml_settings';
        add_menu_page('MO SAML Settings ' . __('Configure SAML Identity Provider for SSO', 'mo_saml_settings'), 'Login into Salesforce', 'administrator', $slug, array($this, 'mo_login_widget_saml_options'), plugin_dir_url(__FILE__) . 'images/miniorange.png');
        add_submenu_page($slug, 'Login into Salesforce', 'Plugin Configuration', 'manage_options', 'mo_saml_settings', array($this, 'mo_login_widget_saml_options'));
        add_submenu_page($slug, 'Login into Salesforce', 'Licensing Plans', 'manage_options', 'mo_saml_settings&amp;tab=licensing', array($this, 'mo_saml_show_licensing_page'));
    }
    function mo_authenticate()
    {
        $redirect_to = '';
        if (isset($_REQUEST['redirect_to'])) {
            $redirect_to = htmlentities($_REQUEST['redirect_to']);
        }
        if (is_user_logged_in()) {
            if (!empty($redirect_to)) {
                header('Location: ' . $redirect_to);
            } else {
                header('Location: ' . site_url());
            }
            exit;
        }
        if (get_option('mo_saml_enable_login_redirect') == 'true') {
            if (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true') {
                header('Location: ' . site_url());
                exit;
            } elseif (get_option('mo_saml_allow_wp_signin') == 'true') {
                if (isset($_GET['saml_sso']) && $_GET['saml_sso'] == 'false' || isset($_POST['saml_sso']) && $_POST['saml_sso'] == 'false') {
                    return;
                } elseif (isset($_REQUEST['redirect_to'])) {
                    $redirect_to = $_REQUEST['redirect_to'];
                    if (strpos($redirect_to, 'wp-admin') !== false && strpos($redirect_to, 'saml_sso=false') !== false) {
                        return;
                    }
                }
            }
            $this->mo_salesforce_redirect_for_authentication($redirect_to);
        }
    }
    function mo_salesforce_redirect_for_authentication($relay_state)
    {
        if (get_option('mo_saml_enable_cloud_broker') == 'false') {
            if (mo_is_sp_configured() && !is_user_logged_in()) {
                $sendRelayState = $relay_state;
                $ssoUrl = get_option("saml_login_url");
                $force_authn = get_option('mo_saml_force_authentication');
                $acsUrl = site_url() . "/";
                $issuer = site_url() . '/wp-content/plugins/miniorange-saml-20-single-sign-on/';
                $samlRequest = Utilities::createAuthnRequest($acsUrl, $issuer, $force_authn);
                $redirect = $ssoUrl;
                if (strpos($ssoUrl, '?') !== false) {
                    $redirect .= '&';
                } else {
                    $redirect .= '?';
                }
                $redirect .= 'SAMLRequest=' . $samlRequest . '&RelayState=' . urlencode($sendRelayState);
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $mo_redirect_url = mo_options_plugin_constants::HOSTNAME . "/moas/rest/saml/request?id=" . get_option('mo_saml_admin_customer_key') . "&returnurl=" . urlencode(site_url() . "/?option=readsamllogin&redirect_to=" . urlencode($relay_state));
            header('Location: ' . $mo_redirect_url);
            exit;
        }
    }
    function mo_modify_login_form()
    {
        echo '<input type="hidden" name="saml_sso" value="false">' . "\n";
    }
    function handleXmlError($errno, $errstr, $errfile, $errline)
    {
        if ($errno == E_WARNING && substr_count($errstr, "DOMDocument::loadXML()") > 0) {
            return;
        } else {
            return false;
        }
    }
    function mo_plugin_action_links($links)
    {
        $links = array_merge(array('<a href="' . esc_url(admin_url('admin.php?page=mo_saml_settings')) . '">' . __('Settings', 'textdomain') . '</a>'), $links);
        return $links;
    }
    function checkPasswordpattern($password)
    {
        $pattern = '/^[(\\w)*(\\!\\@\\#\\$\\%\\^\\&\\*\\.\\-\\_)*]+$/';
        return !preg_match($pattern, $password);
    }
}
new mo_salesforce_mo_login();