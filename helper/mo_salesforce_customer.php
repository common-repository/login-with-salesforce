<?php

/** Login with Salesforce enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
 Copyright (C) 2015  miniOrange

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 * @package 		Login with Salesforce
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
/**
 * This library is miniOrange Authentication Service.
 *
 * Contains Request Calls to Customer service.
 */
require_once dirname(__DIR__) . '/includes/lib/mo-options-enum.php';
class mo_salesforce_customer
{
    public $email;
    public $phone;
    /*
     * * Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
     * * We need the default value for creating the first time,
     * * As we don't have the Default keys available before registering the user to our server.
     * * This default values are only required for sending an One Time Passcode at the user provided email address.
     */
    private $defaultCustomerKey = "16555";
    private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
    function mo_salesforce_create_customer()
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/rest/customer/add';
        $ch = curl_init($url);
        $current_user = wp_get_current_user();
        $this->email = get_option('mo_saml_admin_email');
        $password = get_option('mo_saml_admin_password');
        $fields = array('areaOfInterest' => 'Login With Salesforce', 'email' => $this->email, 'password' => $password);
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_get_customer_key()
    {
        $url = mo_options_plugin_constants::HOSTNAME . "/moas/rest/customer/key";
        $ch = curl_init($url);
        $email = get_option("mo_saml_admin_email");
        $password = get_option("mo_saml_admin_password");
        $fields = array('email' => $email, 'password' => $password);
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_check_customer()
    {
        $url = mo_options_plugin_constants::HOSTNAME . "/moas/rest/customer/check-if-exists";
        $ch = curl_init($url);
        $email = get_option("mo_saml_admin_email");
        $fields = array('email' => $email);
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF - 8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_send_otp_token($email, $phone, $sendToEmail = TRUE, $sendToPhone = FALSE)
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/api/auth/challenge';
        $ch = curl_init($url);
        $customerKey = $this->defaultCustomerKey;
        $apiKey = $this->defaultApiKey;
        //$username = get_option ( 'mo_saml_admin_email' );
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        //$currentTimeInMillis = round ( microtime ( true ) * 1000 );
        $currentTimeInMillis = self::mo_salesforce_get_timestamp();
        /* Creating the Hash using SHA-512 algorithm */
        //$stringToHash = $customerKey . number_format ( $currentTimeInMillis, 0, '', '' ) . $apiKey;
        $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        //$timestampHeader = "Timestamp: " . number_format ( $currentTimeInMillis, 0, '', '' );
        $timestampHeader = "Timestamp: " . $currentTimeInMillis;
        $authorizationHeader = "Authorization: " . $hashValue;
        if ($sendToEmail) {
            $fields = array('customerKey' => $customerKey, 'email' => $email, 'authType' => 'EMAIL', 'transactionName' => 'WP miniOrange SAML 2.0 SSO Plugin');
        } else {
            $fields = array('customerKey' => $customerKey, 'phone' => $phone, 'authType' => 'SMS', 'transactionName' => 'WP miniOrange SAML 2.0 SSO Plugin');
        }
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_validate_otp_token($transactionId, $otpToken)
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/api/auth/validate';
        $ch = curl_init($url);
        $customerKey = $this->defaultCustomerKey;
        $apiKey = $this->defaultApiKey;
        $username = get_option('mo_saml_admin_email');
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        //$currentTimeInMillis = round ( microtime ( true ) * 1000 );
        $currentTimeInMillis = self::mo_salesforce_get_timestamp();
        /* Creating the Hash using SHA-512 algorithm */
        //$stringToHash = $customerKey . number_format ( $currentTimeInMillis, 0, '', '' ) . $apiKey;
        $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        //$timestampHeader = "Timestamp: " . number_format ( $currentTimeInMillis, 0, '', '' );
        $timestampHeader = "Timestamp: " . $currentTimeInMillis;
        $authorizationHeader = "Authorization: " . $hashValue;
        $fields = '';
        // *check for otp over sms/email
        $fields = array('txId' => $transactionId, 'token' => $otpToken);
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_submit_contact_us($email, $phone, $query)
    {
        $current_user = wp_get_current_user();
        $query = '[Login with Salesforce] ' . $query;
        $fields = array('firstName' => $current_user->user_firstname, 'lastName' => $current_user->user_lastname, 'company' => $_SERVER['SERVER_NAME'], 'email' => $email, 'phone' => $phone, 'query' => $query);
        $field_string = json_encode($fields);
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/rest/customer/contact-us';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'charset: UTF-8', 'Authorization: Basic'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            //		echo 'Error in sending curl Request';
            return false;
        }
        // echo " Content: " . $content;
        curl_close($ch);
        return true;
    }

    function mo_salesforce_send_email_alert($email, $phone, $message)
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/api/notify/send';
        $ch = curl_init($url);
        $customerKey = $this->defaultCustomerKey;
        $apiKey = $this->defaultApiKey;
        $currentTimeInMillis = self::mo_salesforce_get_timestamp();
        $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . $currentTimeInMillis;
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail = $email;
        $subject = "Feedback: Login with Salesforce";
        $site_url = site_url();
        global $user;
        $user = wp_get_current_user();
        $query = '[WordPress SAML SSO 2.0 Plugin: ]: ' . $message;
        $content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>Query :' . $query . '</div>';
        $fields = array('customerKey' => $customerKey, 'sendEmail' => true, 'email' => array('customerKey' => $customerKey, 'fromEmail' => $fromEmail, 'bccEmail' => $fromEmail, 'fromName' => 'Xecurify', 'toEmail' => 'samlsupport@xecurify.com', 'toName' => 'samlsupport@xecurify.com', 'subject' => $subject, 'content' => $content));
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        # required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            return json_encode(array("status" => 'ERROR', 'statusMessage' => curl_error($ch)));
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_forgot_password($email)
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/rest/customer/password-reset';
        $ch = curl_init($url);
        /* The customer Key provided to you */
        $customerKey = get_option('mo_saml_admin_customer_key');
        /* The customer API Key provided to you */
        $apiKey = get_option('mo_saml_admin_api_key');
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);
        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fields = '';
        // *check for otp over sms/email
        $fields = array('email' => $email);
        $field_string = json_encode($fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request';
            exit;
        }
        curl_close($ch);
        return $content;
    }
    function mo_salesforce_get_timestamp()
    {
        $url = mo_options_plugin_constants::HOSTNAME . '/moas/rest/mobile/get-timestamp';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // required for https urls
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        /** Check if the WordPress site is setup behind a proxy and use the proxy settings set by the user */
        if (defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
        }
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error in sending curl Request. Please check your connection.';
            exit;
        }
        curl_close($ch);
        return $content;
    }

}