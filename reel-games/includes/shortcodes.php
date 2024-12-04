<?php
/**
 * Shortcode to display the game data.
 */
function reel_game_shortcode_display() {
    ob_start();
    include plugin_dir_path(dirname(__FILE__)) . 'game-profile-template.php';
    $content = ob_get_clean();
    return $content;
}

// Register the shortcode
add_shortcode('reel_game_data', 'reel_game_shortcode_display');
?>
