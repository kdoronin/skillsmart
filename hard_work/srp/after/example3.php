<?php

function get_latest_game_results() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'game_results';
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
}

function generate_results_table($results) {
    ob_start();
    ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>result</th>
            <th>rock position</th>
            <th>run time (ms)</th>
            <th>jump distance (px)</th>
            <th>rock size (px)</th>
            <th>date & time</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($results) : ?>
            <?php foreach ($results as $result) : ?>
                <tr>
                    <td><?php echo esc_html($result->id); ?></td>
                    <td><?php echo esc_html($result->result); ?></td>
                    <td><?php echo esc_html($result->rock_position); ?></td>
                    <td><?php echo esc_html($result->run_time); ?></td>
                    <td><?php echo esc_html($result->jump_distance); ?></td>
                    <td><?php echo esc_html($result->rock_size); ?></td>
                    <td><?php echo esc_html($result->created_at); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">No game results found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}

function handle_latest_game_results_ajax() {
    check_ajax_referer('nonce', 'refresh_nonce');
    $results = get_latest_game_results();
    $table_html = generate_results_table($results);
    wp_send_json_success($table_html);
}

add_action('wp_ajax_handle_latest_game_results', 'handle_latest_game_results_ajax');
add_action('wp_ajax_nopriv_handle_latest_game_results', 'handle_latest_game_results_ajax');