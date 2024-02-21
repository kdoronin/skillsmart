<?php

namespace Defender;

defined('ABSPATH') || exit;

class Telegram {
    public function __construct() {
        add_action('site_user_banned', [$this, 'userBannedNotification']);
    }

    public function userBannedNotification(): void {
        // Logic to send a message to Telegram when a user is banned
        // This would utilize the sendMessage() method detailed in the original class
    }

    private function sendMessage(string $text): void {
        if ('production' !== WP_ENV) return;
        $url = 'https://api.telegram.org/bot' . HK_TG_TOKEN . '/sendMessage';
        $message_raw = [
            'text'       => $text,
            'chat_id'    => HK_TG_CHAT_ID,
            'parse_mode' => 'HTML'
        ];

        $message = json_encode($message_raw, JSON_INVALID_UTF8_SUBSTITUTE);

        $args = [
            'blocking' => false,
            'compress' => true,
            'headers'  => [
                'Content-Type' => 'application/json'
            ],
            'body'     => $message,
        ];

        wp_remote_post($url, $args);
    }
}