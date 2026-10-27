<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add admin submenu under Tools
add_action( 'admin_menu', function() {
    add_submenu_page(
        'tools.php',
        'RM: Migrate Notifications',
        'RM: Notifications',
        'manage_options',
        'rm-notification-migration',
        'rm_render_notification_migration_page'
    );
} );

function rm_render_notification_migration_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $done_msg  = '';
    $error_msg = '';

    if ( isset( $_POST['rm_migrate_notifications'] ) ) {
        check_admin_referer( 'rm_migrate_notifications', 'rm_migrate_nonce' );

        $selector_type = isset( $_POST['selector_type'] ) ? sanitize_text_field( $_POST['selector_type'] ) : 'category';
        $selector_slug = isset( $_POST['selector_slug'] ) ? sanitize_title( $_POST['selector_slug'] ) : '';
        $dry_run       = ! empty( $_POST['dry_run'] );
        $limit         = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 0;

        if ( empty( $selector_slug ) ) {
            $error_msg = 'Please provide a slug.';
        } else {
            // Ensure notification CPT has all post taxonomies
            foreach ( get_object_taxonomies( 'post' ) as $tax ) {
                register_taxonomy_for_object_type( $tax, 'notification' );
            }

            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => $limit > 0 ? $limit : -1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            );

            if ( $selector_type === 'category' ) {
                $args['tax_query'] = array( array(
                    'taxonomy'         => 'category',
                    'field'            => 'slug',
                    'terms'            => array( $selector_slug ),
                    'include_children' => true,
                ) );
            } elseif ( $selector_type === 'tag' ) {
                $args['tax_query'] = array( array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'terms'    => array( $selector_slug ),
                ) );
            }

            $q   = new WP_Query( $args );
            $ids = $q->posts;

            if ( $dry_run ) {
                $done_msg = sprintf( 'Dry run: %d posts would be migrated to Notification CPT.', count( $ids ) );
            } else {
                $migrated = 0;
                foreach ( $ids as $pid ) {
                    wp_update_post( array( 'ID' => $pid, 'post_type' => 'notification' ) );
                    $migrated++;
                }
                flush_rewrite_rules( false );
                $done_msg = sprintf( 'Migrated %d posts to Notification CPT. Permalinks flushed.', $migrated );
            }
        }
    }

    ?>
    <div class="wrap">
        <h1>RM: Migrate Notifications</h1>
        <p>This tool moves blog posts into the <strong>Notification</strong> post type while keeping their existing taxonomies (names and assignments) unchanged.</p>

        <?php if ( $error_msg ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $error_msg ); ?></p></div>
        <?php endif; ?>
        <?php if ( $done_msg ) : ?>
            <div class="notice notice-success"><p><?php echo esc_html( $done_msg ); ?></p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field( 'rm_migrate_notifications', 'rm_migrate_nonce' ); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="selector_type">Select by</label></th>
                        <td>
                            <select name="selector_type" id="selector_type">
                                <option value="category" <?php selected( isset( $_POST['selector_type'] ) ? $_POST['selector_type'] : 'category', 'category' ); ?>>Category slug</option>
                                <option value="tag" <?php selected( isset( $_POST['selector_type'] ) ? $_POST['selector_type'] : '', 'tag' ); ?>>Tag slug</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="selector_slug">Slug</label></th>
                        <td>
                            <input type="text" name="selector_slug" id="selector_slug" class="regular-text" placeholder="e.g. notifications" value="<?php echo isset( $_POST['selector_slug'] ) ? esc_attr( $_POST['selector_slug'] ) : ''; ?>" required>
                            <p class="description">All posts matching this slug will be migrated.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Dry run</th>
                        <td>
                            <label><input type="checkbox" name="dry_run" <?php checked( ! empty( $_POST['dry_run'] ) ); ?>> List how many would be migrated, but do not change anything.</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="limit">Limit (optional)</label></th>
                        <td>
                            <input type="number" name="limit" id="limit" min="0" step="1" value="<?php echo isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 0; ?>">
                            <p class="description">0 = no limit</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button( 'Migrate to Notifications', 'primary', 'rm_migrate_notifications' ); ?>
        </form>
    </div>
    <?php
}


