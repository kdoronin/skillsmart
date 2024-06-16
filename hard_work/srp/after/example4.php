<?php

// Function to fetch and display game statistics
function get_game_statistics_data()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'game_results';

    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
}

function mini_game_statistics_shortcode()
{
    $data = get_game_statistics_data();

    ob_start();
    ?>
    <h2 style='text-align:center;'>Game Statistics</h2>
    <div id="game-statistics">
        <!-- AJAX call -->
    </div>
    <?php
    return ob_get_clean();
}