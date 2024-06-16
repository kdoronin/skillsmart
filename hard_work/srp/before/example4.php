<?php
// Function to fetch and display game statistics
function mini_game_statistics()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'game_results';

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    ob_start();
    ?>
    <h2 style='text-align:center;'>Game Statistics</h2>
    <div id="game-statistics">
        <!-- AJAX call -->
    </div>
    <?php
    return ob_get_clean();
}

function mini_game_statistics_shortcode()
{
    return mini_game_statistics();
}