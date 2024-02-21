<?php

namespace Defender;

defined('ABSPATH') || exit;

class Scripts {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'registerJS'], PHP_INT_MIN);
        add_action('wp_enqueue_scripts', [$this, 'queueJS'], PHP_INT_MIN);
        add_action('wp_head', [$this, 'preloadJS'], PHP_INT_MIN);
    }

    public function registerJS(): void {
        wp_register_script(
            'hello-kitty',
            HK_ASSETS_URL . '/js/main.min.js',
            [],
            HK_ASSETS_HASH
        );
    }

    public function preloadJS(): void {
        if (is_checkout()) {
            $url = esc_attr(HK_ASSETS_URL . '/js/main.min.js') . '?' . build_query(['version' => HK_ASSETS_HASH]);
            echo '<link rel="preload" href="' . $url . '" as="script" />';
        }
    }

    public function queueJS(): void {
        if (is_checkout()) {
            wp_enqueue_script('hello-kitty');
        }
    }
}