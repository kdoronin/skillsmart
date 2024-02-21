<?php

namespace Defender;

defined('ABSPATH') || exit;

class RestAPI {
    public function __construct() {
        add_action('rest_api_init', [$this, 'registerRESTRoute']);
    }

    public function registerRESTRoute(): void {
        register_rest_route(
            'wc/def',
            '/bans',
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'sendResponse'],
                'permission_callback' => [$this, 'getPermission']
            ]
        );
    }

    public function sendResponse(\WP_REST_Request $request): \WP_REST_Response {
        // Assume this method fetches and returns the banned IPs information
        // Method needs adaptation to fit the new class structure
    }

    public function getPermission(\WP_REST_Request $request): bool {
        return current_user_can('view_wc_bans');
    }
}