<?php

/**
 * SwiftMember API 1.0.2
 * 
 * @author Nick Zarnecki
 * 
 * Pre-requisites:
 * - PHP cURL
 * 
 * Copyright 2012 SwiftMember.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
if ( ! class_exists ( 'SwiftMember_API' ) ) {

    class SwiftMember {

        /**
         * The client authentication key.
         * @var string
         */
        public $auth = '';

        /**
         * The client domain.
         * @var string
         */
        public $domain = '';

        /**
         * The API URL where the requests are sent.
         * 
         * To get this URL navigate to Admin > Products > Software API > API URL
         * @var string
         */
        public $api_url = '';

        /**
         * The request error.
         * @var string
         */
        public $error = '';

        /**
         * Constructor.
         * @param string $api_url The API URL.
         * @param string $auth The client hash key.
         * @param string $domain The client domain.
         */
        public function __construct ( $api_url = '', $auth = '', $domain = '' ) {
            $this->auth = $auth;
            $this->api_url = $api_url;
            $this->domain = $domain;
        }

        /**
         * Send request to API URI.
         * @param array $request The request array.
         * @return boolean|array Returns the request response on success, otherwise false.
         */
        private function _sendRequest ( $request ) {
            $this->error = '';
            $ch = curl_init ( $this->api_url );
            curl_setopt ( $ch, CURLOPT_POST, count ( $request ) );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $request ) );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            if ( ($response = curl_exec ( $ch ) ) ) {
                if ( ($response = json_decode ( $response, true ) ) ) {
                    return $response;
                } else {
                    // response is not valid json
                    $this->error = 'Server did not respond with valid JSON.';
                }
            } else {
                // curl_exec failed
                $this->error = 'cURL exec failed: ' . curl_error ( $ch );
            }
            return false;
        }

        /**
         * Gets the latest product version.
         * 
         * Requires authentication key.
         * 
         * @return string|boolean Returns the version on success (ex: "1.3.5"), otherwise false.
         */
        public function get_version () {
            $this->error = '';
            $fields = array( "action" => "get_version", "auth" => "{$this->auth}" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    return isset ( $response[ "data" ] ) ? $response[ "data" ] : false;
                } else {
                    $this->error = $response[ 'errmsg' ];
                }
            }
            return false;
        }

        /**
         * Validates the license key.
         * 
         * Requires license key.
         * 
         * @param string $license_key The license key.
         * @return boolean Returns true if license is valid, otherwise false.
         */
        public function validate_license ( $license_key ) {
            $this->error = '';
            $fields = array( "action" => "validate_license", "license_key" => "$license_key" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    if ( $response[ "data" ] == "valid" ) {
                        return true;
                        // License is valid
                    } elseif ( $response[ "data" ] == 'invalid' ) {
                        return 0;
                    }
                } else {
                    // output error message found in $response["errmsg"]
                    $this->error = $response[ 'errmsg' ];
                }
            }
            return false;
        }

        /**
         * Validate authentication key.
         * 
         * Requires authentication key.
         * 
         * @param string $auth [Optional] The authentication key, if not already provided in the constructor.
         * @return boolean Returns true if valid, otherwise false.
         */
        public function validate_auth ( $auth = '' ) {
            $this->error = '';
            if ( $auth == '' ) {
                $auth = $this->auth;
            }
            $fields = array( "action" => "validate_auth", "auth" => "$auth" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    if ( $response[ "data" ] == "valid" ) {
                        // Auth code is valid
                        return true;
                    }
                } else {
                    // output error message found in $response["errmsg"]
                    $this->error = $response[ 'errmsg' ];
                }
            } else {
                // response is not valid json
                $this->error = 'Server did not respond with valid JSON.';
            }
            return false;
        }

        /**
         * Get software update info.
         * 
         * Requires authentication key.
         * 
         * Return example:
         * 
         * stdClass('new_version' => '2.4.2', 'package' => 'http://example.com/download.zip');
         * 
         * @return boolean|object Returns an object of options on success, otherwise false.
         */
        public function get_update_info () {
            $this->error = '';
            $fields = array( "action" => "get_update_info", "auth" => "{$this->auth}" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    if ( isset ( $response[ 'data' ] ) ) {
                        if ( gettype ( $response[ 'data' ] ) == 'string' ) {
                            $response[ 'data' ] = json_decode ( $response[ 'data' ] );
                        }
                    }
                    //$new_version = $response[ "data" ]->new_version; // The latest version of the plugin
                    //$package_url = $response[ "data" ]->package; // The automatic update package URL
                    return isset ( $response[ 'data' ] ) ? $response[ 'data' ] : false;
                } else {
                    // output error message found in $response["errmsg"]
                    $this->error = $response[ 'errmsg' ];
                }
            }
            return false;
        }

        /**
         * Remove the license from current installation.
         * 
         * Requires authentication key and domain.
         * 
         * @return boolean Returns true if license was removed, otherwise false.
         */
        public function remove_license () {
            $this->error = '';
            $fields = array( "action" => "remove_license", "auth" => "{$this->auth}", "domain" => "{$this->domain}" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    // License was removed
                    //$message = $response[ "data" ];
                    return true;
                } else {
                    $this->error = $response[ "errmsg" ];
                }
            }
            return false;
        }

        /**
         * Performs a client install.
         * 
         * Requires license key and domain.
         * 
         * Example return:
         * 
         * array('data' => '--client install data--', 'auth' => '--auth key --');
         * 
         * Save this auth in your client installation (example, WordPress options,
         * or registry for Windows applications) and use it to make API calls from
         * the client (verify auth, check version, update, etc)
         * 
         * @param string $license_key The client license key.
         * @return boolean|array Returns array containing install data on success, otherwise false.
         */
        public function client_install ( $license_key ) {
            $this->error = '';
            $fields = array( "action" => "client_install", "license_key" => "$license_key", "domain" => "{$this->domain}" );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    $client_install_data = $response[ "data" ]; // This is the data you entered in the product editor, if any
                    $auth = $response[ "option" ][ "auth" ]; // 
                    if ( $auth ) {
                        return array( 'data' => $client_install_data, 'auth' => $auth );
                    }
                } else {
                    // output error message found in $response["errmsg"]
                    $this->error = $response[ "errmsg" ];
                }
            }
            return false;
        }

        /**
         * Gets the client install data.
         * 
         * Requires authentication key.
         * 
         * @return boolean|string Returns the install data on success, otherwise false.
         */
        public function get_install_data () {
            $this->error = '';
            $fields = array( "action" => "get_install_data", "auth" => $this->auth );
            $response = $this->_sendRequest ( $fields );
            if ( $response ) {
                if ( $response[ "status" ] == "success" ) {
                    return $response[ "data" ];
                } else {
                    // output error message found in $response["errmsg"]
                    $this->error = $response[ "errmsg" ];
                }
            }
            return false;
        }

        /**
         * Get the direct download URL.
         * 
         * Requires authentication key.
         * 
         * @return boolean|string Returns the direct download URL on success, otherwise false.
         */
        public function get_download_url () {
            if ( $this->api_url && $this->auth ) {
                return $this->api_url . '&action=download&auth=' . $this->auth;
            }
            return false;
        }

    }

}