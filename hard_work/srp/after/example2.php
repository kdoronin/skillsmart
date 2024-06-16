<?php
function enqueue_mini_game_styles() {
    wp_enqueue_style('minigame-style', plugin_dir_url(__FILE__) . 'css/style.css');
}

function enqueue_mini_game_scripts() {
    wp_enqueue_script('minigame-app', plugins_url('js/app.js', __FILE__), array(), '1.0.0', true);
    wp_enqueue_script('minigame-script', plugins_url('js/script.js', __FILE__), array('minigame-app', 'jquery'), '1.0.0', true);
    wp_enqueue_script('live-refresh', plugins_url('js/live-refresh.js', __FILE__), array('jquery'), '1.0.0', true);
}

function localize_mini_game_scripts() {
    wp_localize_script('minigame-script', 'gameData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('game_nonce')
    ));

    wp_localize_script('live-refresh', 'refreshData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('refresh_nonce')
    ));
}

function mini_game_enqueue_assets() {
    enqueue_mini_game_styles();
    enqueue_mini_game_scripts();
    localize_mini_game_scripts();
}

add_action('wp_enqueue_scripts', 'mini_game_enqueue_assets');