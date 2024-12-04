<?php
/*
Plugin Name: ReelAuth
Plugin URI: https://rmplayground.com
Description: Integrates Okta sign-in widget for seamless authentication across ReelMetrics infrastructure.
Version: 1.0
Author: ReelMetrics
Author URI: https://reelmetrics.com/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Enqueue scripts and styles
function reelauth_enqueue_scripts() {
    wp_enqueue_script('okta-auth-js', 'https://global.oktacdn.com/okta-auth-js/7.4.1/okta-auth-js.min.js', array(), null, true);
    wp_enqueue_script('reelauth-login', plugins_url('assets/js/login.js', __FILE__), array('jquery'), '1.0', true);

    wp_enqueue_style('reelauth-style', plugins_url('assets/css/reelauth.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'reelauth_enqueue_scripts');

// Add modal HTML to footer
function reelauth_add_modal_html() {
    ?>
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="loginForm">
                <input type="text" id="username" name="username" placeholder="Username" oninput="handleUsernameChange(event)">
                <input type="password" id="password" name="password" placeholder="Password" oninput="handlePasswordChange(event)">
                <button type="submit" id="loginButton" onclick="handleSubmit(event)">Login</button>
            </form>
            <div id="loginError"></div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'reelauth_add_modal_html');

