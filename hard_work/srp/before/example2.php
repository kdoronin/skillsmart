<?php
function mini_game_enqueue_scripts()
{
    wp_enqueue_style('minigame-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('minigame-app', plugins_url('js/app.js', __FILE__), array(), '1.0.0', true);
    wp_enqueue_script('minigame-script', plugins_url('js/script.js', __FILE__), array('minigame-app', 'jquery'), '1.0.0', true);

    wp_localize_script('minigame-script', 'gameData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('game_nonce')
    ));

    wp_enqueue_script('live-refresh', plugins_url('js/live-refresh.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_localize_script('live-refresh', 'refreshData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('refresh_nonce')
    ));
}