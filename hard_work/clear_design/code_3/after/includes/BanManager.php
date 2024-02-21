<?php

namespace Defender;

defined('ABSPATH') || exit;

class BanManager {
    public function __construct() {
        add_action('woocommerce_before_checkout_process', [$this, 'checkClient'], 9);
        add_action('remove_expired_banned_data', [$this, 'removeExpiredBans']);
    }

    public function checkClient(): void {
        // Assume getIPAPIData() and isCC() methods are part of this class or adapted accordingly
        $this->getIPAPIData();
        $posted_data = WC()->checkout()->get_posted_data();

        if (is_user_logged_in() || !$this->isCC($posted_data)) {
            return;
        }

        // Include logic for checking the client and possibly banning
        // This method needs adaptation to fit within the new class structure
    }

    private function bannedMessage(): void {
        $support_page = get_page_link(147292);
        throw new Exception('Your payment was declined. Contact <a href="' . $support_page . '">our support</a>.');
    }

    public function removeExpiredBans(): void {
        $banned = get_option('banned_data');
        foreach ($banned as $iphash => $value) {
            if (current_time('U', true) > $value['expired']) {
                unset($banned[$iphash]);
            }
        }
        update_option('banned_data', $banned);
    }
}