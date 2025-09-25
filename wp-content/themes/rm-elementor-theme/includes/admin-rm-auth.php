<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Option name
define( 'RM_OKTA_OPTION', 'rm_okta_auth' );

function rm_okta_get_option() {
    $opt = get_option( RM_OKTA_OPTION );
    if ( ! is_array( $opt ) ) { $opt = array(); }
    $defaults = array(
        'base_url'            => '',
        'issuer_id'           => 'default',
        'client_id'           => '',
        'redirect_uri'        => '',
        'forgot_password_url' => 'https://operators.reelmetrics.com/password_reset/new',
        'request_access_url'  => 'https://operators.reelmetrics.com/member_request',
        'wp_origins'          => array( preg_replace('#/$#','',home_url()) ),
        'use_pkce'            => true,
    );
    return wp_parse_args( $opt, $defaults );
}

add_action( 'admin_menu', function() {
    add_menu_page(
        'RM Auth', 'RM Auth', 'manage_options', 'rm-auth', 'rm_auth_settings_page', 'dashicons-lock', 60
    );
} );

function rm_auth_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $msg = '';

    if ( isset($_POST['rm_auth_save']) ) {
        check_admin_referer('rm_auth_save','rm_auth_nonce');
        $data = array(
            'base_url'            => esc_url_raw( trim($_POST['base_url'] ?? '') ),
            'issuer_id'           => sanitize_text_field( trim($_POST['issuer_id'] ?? 'default') ),
            'client_id'           => sanitize_text_field( trim($_POST['client_id'] ?? '') ),
            'redirect_uri'        => esc_url_raw( trim($_POST['redirect_uri'] ?? '') ),
            'forgot_password_url' => esc_url_raw( trim($_POST['forgot_password_url'] ?? '') ),
            'request_access_url'  => esc_url_raw( trim($_POST['request_access_url'] ?? '') ),
            'use_pkce'            => ! empty($_POST['use_pkce']) ? true : false,
        );
        $origins = array_map( 'trim', explode( "\n", (string)($_POST['wp_origins'] ?? '') ) );
        $origins = array_values( array_filter( array_map( function($o){ return $o ? preg_replace('#/$#','',$o) : ''; }, $origins ) ) );
        if ( empty($origins) ) { $origins = array( preg_replace('#/$#','',home_url()) ); }
        $data['wp_origins'] = $origins;
        update_option( RM_OKTA_OPTION, $data, false );
        $msg = 'Settings saved.';
    }

    if ( isset($_POST['rm_auth_import']) ) {
        check_admin_referer('rm_auth_save','rm_auth_nonce');
        $source = esc_url_raw( trim($_POST['source_url'] ?? '') );
        if ( $source ) {
            $res = wp_remote_get( $source, array( 'timeout' => 10 ) );
            if ( ! is_wp_error($res) && wp_remote_retrieve_response_code($res) === 200 ) {
                $json = json_decode( wp_remote_retrieve_body($res), true );
                if ( is_array($json) ) {
                    $opt = rm_okta_get_option();
                    $opt['base_url']            = esc_url_raw( $json['oidc_base_url'] ?? $opt['base_url'] );
                    $opt['issuer_id']           = sanitize_text_field( $json['oidc_issuer'] ?? $opt['issuer_id'] );
                    $opt['client_id']           = sanitize_text_field( $json['oidc_identifier'] ?? $opt['client_id'] );
                    $opt['redirect_uri']        = esc_url_raw( $json['oidc_redirect_uri'] ?? $opt['redirect_uri'] );
                    $opt['forgot_password_url'] = esc_url_raw( $json['oidc_forgot_password_url'] ?? $opt['forgot_password_url'] );
                    update_option( RM_OKTA_OPTION, $opt, false );
                    $msg = 'Imported from source.';
                }
            }
        }
    }

    $opt = rm_okta_get_option();
    ?>
    <div class="wrap">
        <h1>RM Auth (Okta)</h1>
        <?php if ($msg): ?><div class="notice notice-success"><p><?php echo esc_html($msg); ?></p></div><?php endif; ?>
        <form method="post">
            <?php wp_nonce_field('rm_auth_save','rm_auth_nonce'); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr><th scope="row"><label for="base_url">Okta Base URL</label></th>
                        <td><input type="url" class="regular-text" name="base_url" id="base_url" value="<?php echo esc_attr($opt['base_url']); ?>" placeholder="https://YOUR.okta.com"></td>
                    </tr>
                    <tr><th scope="row"><label for="issuer_id">Issuer ID</label></th>
                        <td><input type="text" class="regular-text" name="issuer_id" id="issuer_id" value="<?php echo esc_attr($opt['issuer_id']); ?>" placeholder="default"></td>
                    </tr>
                    <tr><th scope="row"><label for="client_id">Client ID</label></th>
                        <td><input type="text" class="regular-text" name="client_id" id="client_id" value="<?php echo esc_attr($opt['client_id']); ?>"></td>
                    </tr>
                    <tr><th scope="row"><label for="redirect_uri">Redirect URI</label></th>
                        <td><input type="url" class="regular-text" name="redirect_uri" id="redirect_uri" value="<?php echo esc_attr($opt['redirect_uri']); ?>" placeholder="https://operators.reelmetrics.com/callback"></td>
                    </tr>
                    <tr><th scope="row"><label for="forgot_password_url">Forgot password URL</label></th>
                        <td><input type="url" class="regular-text" name="forgot_password_url" id="forgot_password_url" value="<?php echo esc_attr($opt['forgot_password_url']); ?>"></td>
                    </tr>
                    <tr><th scope="row"><label for="request_access_url">Request access URL</label></th>
                        <td><input type="url" class="regular-text" name="request_access_url" id="request_access_url" value="<?php echo esc_attr($opt['request_access_url']); ?>"></td>
                    </tr>
                    <tr><th scope="row"><label for="wp_origins">WordPress Origin(s)</label></th>
                        <td><textarea name="wp_origins" id="wp_origins" rows="3" class="large-text"><?php echo esc_textarea( implode("\n", $opt['wp_origins']) ); ?></textarea>
                        <p class="description">Origins to add in Okta Trusted Origins (one per line).</p></td>
                    </tr>
                    <tr><th scope="row">PKCE</th>
                        <td><label><input type="checkbox" name="use_pkce" value="1" <?php checked( $opt['use_pkce'], true ); ?>> Use PKCE</label></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button('Save Settings','primary','rm_auth_save'); ?>

            <h2>Import from URL</h2>
            <p class="description">Fetch Okta OIDC values from an endpoint like https://reelmetrics.com/api/oidc_info</p>
            <input type="url" class="regular-text" name="source_url" value="https://reelmetrics.com/api/oidc_info">
            <?php submit_button('Import','secondary','rm_auth_import', false); ?>
        </form>
    </div>
    <?php
}


