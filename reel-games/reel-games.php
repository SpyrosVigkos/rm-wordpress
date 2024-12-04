<?php
/*
Plugin Name: Reel Games
Plugin URI: https://rmplayground.link/
Description: Fetches games from ReelMetrics API and displays them in a WordPress page.
Version: 1.0
Author: ReelMetrics
Author URI: https://reelmetrics.com/
*/

// Include necessary files
include plugin_dir_path(__FILE__) . 'includes/api-functions.php';
include plugin_dir_path(__FILE__) . 'includes/shortcodes.php';

// Enqueue necessary styles and scripts
function enqueue_reel_games_assets() {
    // Enqueue jQuery if not already done
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), '3.7.1', false);

        // Enqueue Bootstrap
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css', array(), '4.6.2');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '4.6.2', true);    

    // Enqueue DevExtreme styles and scripts
    wp_enqueue_style('devextreme-light', 'https://cdn3.devexpress.com/jslib/23.1.4/css/dx.light.css', array(), '23.1.4');
    wp_enqueue_script('devextreme-all', 'https://cdn3.devexpress.com/jslib/23.1.4/js/dx.all.js', array('jquery'), '23.1.4', true);

    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.4.2');
    
    // Enqueue plugin specific styles and scripts
    wp_enqueue_style('reel-games', plugins_url('assets/css/reel-games.css', __FILE__), array(), '1.0.0', 'all');
    wp_enqueue_script('reel-games-js', plugins_url('assets/js/reel-games.js', __FILE__), array('jquery'), '1.0.0', true);

    $game_data = fetch_reel_game_api_response();
    wp_localize_script('reel-games-js', 'reelGamesData', array(
        'gameData' => $game_data
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_reel_games_assets');

// Debugging function to view the API response
function debug_api_response() {
    if (isset($_GET['debug_api'])) {
        $data = fetch_reel_game_api_response();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }
}
add_action('init', 'debug_api_response');

add_action('wp_ajax_fetch_game_data', 'fetch_reel_game_api_response');
add_action('wp_ajax_nopriv_fetch_game_data', 'fetch_reel_game_api_response');

// Add rewrite rules for custom game URLs
function reel_games_rewrite_rule() {
    add_rewrite_rule('^games/([^/]*)/?', 'index.php?pagename=games&uuid=$matches[1]', 'top');
}
add_action('init', 'reel_games_rewrite_rule');


// Add custom query vars for UUID and name
function reel_games_query_vars($vars) {
    $vars[] = 'uuid';
    $vars[] = 'title';
    return $vars;
}

add_filter('query_vars', 'reel_games_query_vars');


?>
