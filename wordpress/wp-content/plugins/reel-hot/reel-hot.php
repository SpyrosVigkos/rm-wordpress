<?php
/*
Plugin Name: ReelHot
Plugin URI: https://rmplayground.com
Description: Fetches and displays the latest ReelHot indices from ReelMetrics.
Version: 1.0
Author: ReelMetrics
Author URI: https://reelmetrics.com/
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue scripts and styles
function reelhot_enqueue_scripts() {
    // Include the shared utilities
    include_once WP_CONTENT_DIR . '/shared-libraries/plugin-utils.php';

    // Enqueue plugin-specific scripts and styles
    wp_enqueue_script('reelhot-js', plugins_url('assets/js/reelhot.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_style('reelhot-style', plugins_url('assets/css/reelhot.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'reelhot_enqueue_scripts');

function reelhot_index_list_shortcode($atts) {
    $atts = shortcode_atts(array(
    ), $atts, 'reelhot_index_list');

    // Fetch the data from ButterCMS API
    $response = wp_remote_get('https://api.buttercms.com/v2/content/?keys=reelhot_indexes&auth_token=bd76356ad261a2c45a957a9b773f53e569443648');
    // console the response

    // Check for error in the response and proceed if none
    if (is_wp_error($response)) {
        return '<p>Unable to retrieve data at this time.</p>';
    }

    // Decode the JSON body from the API response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    // Start buffering the output
    ob_start();
    ?>
    <div id="reelhot-indexes">
        <h2>ReelHot Indexes</h2>
        <ul>
            <?php 
            if (!empty($data['data']['reelhot_indexes'])):
                $reversed_indexes = array_reverse($data['data']['reelhot_indexes']);
                foreach ($reversed_indexes as $index): ?>
                    <li>
                        <a href="<?php echo esc_url($index['link']); ?>" target="_blank">
                            ReelHot Index for <?php echo date('F Y', strtotime($index['date'])); ?>
                        </a>
                    </li>
                <?php endforeach;
            else: ?>
                <p>No data found.</p>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    // Get the content from the buffer and clean the buffer
    return ob_get_clean();
}
add_shortcode('reelhot_index_list', 'reelhot_index_list_shortcode');


