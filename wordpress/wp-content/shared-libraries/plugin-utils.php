<?
// Prevent direct access to the file
if (!defined('ABSPATH')) exit;

// In a shared utilities file, e.g., plugin-utils.php
function enqueue_shared_assets() {
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), '3.7.1', false);
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css', array(), '4.6.2');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '4.6.2', true);
    wp_enqueue_style('devextreme-light', 'https://cdn3.devexpress.com/jslib/23.1.4/css/dx.light.css', array(), '23.1.4');
    wp_enqueue_script('devextreme-all', 'https://cdn3.devexpress.com/jslib/23.1.4/js/dx.all.js', array('jquery'), '23.1.4', true);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.4.2');
}

add_action('wp_enqueue_scripts', 'enqueue_shared_assets');
?>
