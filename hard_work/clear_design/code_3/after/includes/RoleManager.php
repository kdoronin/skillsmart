<?php

namespace Defender;

defined('ABSPATH') || exit;

class RoleManager {
    private static string $role_version = '1.1';

    public function __construct() {
        add_action('admin_init', [$this, 'checkRoleVersion']);
    }

    public function checkRoleVersion(): void {
        if (version_compare((string) get_option('banned_version'), self::$role_version) < 0) {
            $this->removeCSRole();
            $this->addCSRole();
            update_option('banned_version', self::$role_version);
        }
    }

    private function addCSRole(): void {
        add_role(
            'customer_support',
            'Customer Support',
            [
                'read'                    => true,
                'read_shop_order'         => true,
                'edit_shop_order'         => true,
                'edit_shop_orders'        => true,
                'edit_others_shop_orders' => true,
                'view_admin_dashboard'    => true,
            ]
        );

        $adm_roles = ['administrator', 'shop_manager', 'customer_support'];

        foreach ($adm_roles as $adm_role) {
            wp_roles()->add_cap($adm_role, 'view_wc_bans');
            wp_roles()->add_cap($adm_role, 'edit_wc_bans');
        }
    }

    private function removeCSRole(): void {
        remove_role('customer_support');
        foreach (wp_roles()->roles as $role => $user_roles) {
            wp_roles()->remove_cap($role, 'view_wc_bans');
            wp_roles()->remove_cap($role, 'edit_wc_bans');
        }
    }
}