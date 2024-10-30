<?php


/**
 * Check if Curl is installed
 * @return int
 */
function mo_is_curl_installed() {
    if ( in_array( 'curl', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Check if Openssl is installed
 * @return int
 */
function mo_is_openssl_installed() {

    if ( in_array( 'openssl', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Check if Dom is installed
 * @return int
 */
function mo_is_dom_installed(){

    if ( in_array( 'dom', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * Check if Iconv is installed
 * @return int
 */
function mo_is_iconv_installed(){

    if ( in_array( 'iconv', get_loaded_extensions() ) ) {
        return 1;
    } else {
        return 0;
    }
}