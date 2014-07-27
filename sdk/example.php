<?php

require 'SwiftMember_API.php';

$license_key = ''; // Provide the license key during client install. Do not store it anywhere on the client's installation.
$api_url = 'http://your-domain.com/?smb_action=api';
$auth = ''; // The auth stored by your software (as you recieved from client_install). Leave blank if you did not do client_install yet.
//$auth = get_option('my_auth_key');
$domain = ''; // The domain

$swiftmember_api = new SwiftMember_API ( $api_url, $auth, $domain );

/**
 * Client Install API Call
 */
/*
$client_install = $swiftmember_api->client_install ( $license_key );

if ( $client_install ) {
    $client_install_data = $client_install[ 'data' ];
    $auth = $client_install[ 'auth' ];
    // Insert auth into options to use for other API calls.
} else {
    echo $swiftmember_api->error;
}
 */

/**
 * Get Download URL
 */
/*
$download_url = $swiftmember_api->get_download_url ();
 */

/**
 * Get Install Data API Call
 */
/*
$install_data = $swiftmember_api->get_install_data ();
*/

/**
 * Get Version API Call
 */
/*
$version = $swiftmember_api->get_version (); // Example: 1.5.2
*/
/**
 * Get Update Info API Call
 */
/*
$update_info = $swiftmember_api->get_update_info ();
if ( $update_info ) {
    $new_version = $update_info[ 'new_version' ];
    $package = $update_info[ 'package' ]; // The download URL
} else {
    echo $swiftmember_api->error;
}
*/
/**
 * Validate License API Call
 */
/*
$license_valid = $swiftmember_api->validate_license ( $license_key );
if ( $license_valid ) {
    echo "License is valid!";
}
*/
/**
 * Validate Auth API Call
 */
/*
$auth_valid = $swiftmember_api->validate_auth ( $auth );
if ( $auth_valid ) {
    echo "Auth is valid!";
}
*/
/**
 * Remove License API Call
 */
/*
$remove_license = $swiftmember_api->remove_license ();
if ( $remove_license ) {
    echo "License has been removed.";
    // At this point remove auth from options
}
*/