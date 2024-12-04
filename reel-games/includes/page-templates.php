function reel_game_page_template($template) {
    global $post;

    if ($post->post_type == 'page' && get_post_meta($post->ID, 'reel_game_uuid', true)) {
        return plugin_dir_path(__FILE__) . 'game-profile-template.php';
    }

    return $template;
}
add_filter('template_include', 'reel_game_page_template');
