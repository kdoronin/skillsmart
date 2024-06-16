<?php
function fetch_latest_game_results()
{
    check_ajax_referer('refresh_nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'game_results';

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

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
    $table_html = ob_get_clean();
    wp_send_json_success($table_html);
}