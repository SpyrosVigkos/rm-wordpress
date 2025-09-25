<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('rest_api_init', function(){
    register_rest_route('rm/v1','/oidc-info', array(
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function( $req ){
            $opt = function_exists('rm_okta_get_option') ? rm_okta_get_option() : get_option('rm_okta_auth');
            if ( ! is_array($opt) ) $opt = array();
            $payload = array(
                'oidc_base_url' => $opt['base_url'] ?? '',
                'oidc_issuer' => $opt['issuer_id'] ?? 'default',
                'oidc_identifier' => $opt['client_id'] ?? '',
                'oidc_redirect_uri' => $opt['redirect_uri'] ?? '',
                'oidc_forgot_password_url' => $opt['forgot_password_url'] ?? '',
            );
            return rest_ensure_response( $payload );
        }
    ));
});


