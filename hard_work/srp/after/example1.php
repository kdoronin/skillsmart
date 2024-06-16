<?php
// это – в файл functions.php
if (!function_exists('wp_dropdown_posts')) {
    function wp_dropdown_posts($args = '') {
        $defaults = array(
            'post_type'        => 'post',
            'selected'         => 0,
            'name'             => 'post_id',
            'id'               => '',
            'show_option_none' => esc_html__('None', 'donut_game'),
            'option_none_value'=> '0',
            'echo'             => 1,
        );

        $r = wp_parse_args($args, $defaults);
        $posts = get_posts(array('post_type' => $r['post_type'], 'numberposts' => -1));

        $output = "<select name='" . esc_attr($r['name']) . "' id='" . esc_attr($r['id']) . "'>";
        if ($r['show_option_none']) {
            $output .= "<option value='" . esc_attr($r['option_none_value']) . "'>" . esc_html($r['show_option_none']) . "</option>";
        }

        foreach ($posts as $post) {
            $output .= "<option value='" . esc_attr($post->ID) . "' " . selected($r['selected'], $post->ID, false) . ">" . esc_html($post->post_title) . "</option>";
        }

        $output .= "</select>";

        if ($r['echo']) {
            echo $output;
        } else {
            return $output;
        }
    }
}

// это – в файл donut-mini-game.php
class DonutMiniGame
{
    /**
     * Конструктор для класса DonutMiniGame.
     *
     * Регистрирует действия и хуки, включая добавление мини игры в контент,
     * отправку результатов игры, очистку результатов игры и создание таблицы результатов при активации плагина.
     * Также определяет функцию для создания выпадающего списка страниц и постов если они не существуют.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('the_content', [$this, 'display_mini_game_before_content']);
        add_action('wp_ajax_nopriv_submit_game_results', [$this, 'submit_game_results']);
        add_action('wp_ajax_submit_game_results', [$this, 'submit_game_results']);
        add_action('wp_ajax_clear_game_results', [$this, 'clear_game_results']);
        add_action('wp_ajax_nopriv_clear_game_results', [$this, 'clear_game_results']);
    }
}


// файл Activator.php
class Activator
{
    public static function activate()
    {
        register_activation_hook(__FILE__, [$this, 'create_results_table']);
    }

    private function create_results_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'donut_mini_game_results';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            score mediumint(9) NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}