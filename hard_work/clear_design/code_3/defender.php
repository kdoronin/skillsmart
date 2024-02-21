<?php
/**
 * Plugin Name:       Defender
 * Description:       Defend checkout form
 * Version:           0.1.2
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

use Theme\Integrations\IPAPI;

defined( 'ABSPATH' ) || exit;

/**
 * Assets URL
 * @var string
 */
define( 'HK_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets' );

require __DIR__ . '/frontend/assets_hash.php';

/**
 * Assets hash
 * @var string
 */
define( 'HK_ASSETS_HASH', $assets_hash );

/**
 * Debug
 * @var bool
 */
define( 'HK_DEBUG', true );

/**
 * Telegram Bot Token
 * @var string
 */
define( 'HK_TG_TOKEN', '' );

/**
 * Telegram Chat ID
 * @var int
 */
define( 'HK_TG_CHAT_ID', -1001872408352 );

class Defender {

    /**
     * CS Role/Capabilities version
     *
     * @var string
     */
    private static string $role_version = '1.1';

    /**
     * hello-kitty JS URL
     *
     * @var string
     */
    private string $script_url;

    /**
     * Payment method IDs
     *
     * @var array
     */
    public static $cc_methods = [
        // Checkout.com – Pay by Card
        'wc_checkout_com_cards',
        // Checkout.com – Google Pay
        'wc_checkout_com_google_pay',
        // ! Debug
        'wpfi_test',
    ];

    /**
     * IPAPI data
     *
     * @var array
     */
    private array $ipapi;

    public function __construct()
    {
        $this->script_url = HK_ASSETS_URL . '/js/main.min.js';

        add_filter( 'nonce_life', [ $this, 'checkoutNonceTTL' ], 10, 2 );
        add_action( 'admin_init', [ $this, 'checkRoleVersion' ] );
        add_filter( 'default_option_banned_data', '__return_empty_array' );
        add_action( 'remove_expired_banned_data', [ $this, 'removeExpiredBans' ] );
        add_action( 'woocommerce_before_checkout_process', [ $this, 'checkClient' ], 9 );
        add_action( 'admin_menu', [ $this, 'addBannedWCSubmenu' ], PHP_INT_MAX );
        add_action( 'rest_api_init', [ $this, 'registerRESTRoute' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'registerJS' ], PHP_INT_MIN );
        add_action( 'wp_enqueue_scripts', [ $this, 'queueJS' ], PHP_INT_MIN );
        add_action( 'wp_head', [ $this, 'preloadJS' ], PHP_INT_MIN );
        add_action( 'site_user_banned', [ $this, 'userBannedNotification' ] );
    }

    /**
     * Modify chrckout nonce TTL.
     *
     * @param int        $lifespan Lifespan of nonces in seconds. Default 86,400 seconds, or one day.
     * @param string|int $action   The nonce action, or -1 if none was provided.
     * @return int Lifespan of nonces in seconds. Default 86,400 seconds, or one day.
     */
    public function checkoutNonceTTL( int $lifespan, $action ): int
    {
        if ( 'woocommerce-process_checkout' === $action ) {
            return HOUR_IN_SECONDS;
        }

        return $lifespan;
    }

    /**
     * Plugin activation actions
     *
     * @return void
     */
    public function activation(): void
    {
        // Set role version if not exists
        if ( false === get_option( 'banned_version' ) ) {
            $this->addCSRole();
            update_option( 'banned_version', self::$role_version );
        }

        // Expired bans cron job
        if ( ! wp_next_scheduled ( 'remove_expired_banned_data' ) ) {
            wp_schedule_event( time(), 'daily', 'remove_expired_banned_data' );
        }

        // debug message
        if ( HK_DEBUG ) {
            $this->sendMessage( '<b>Defender</b> activated' );
        }
    }

    /**
     * Plugin deactivation actions
     *
     * @return void
     */
    public function deactivation(): void
    {
        // debug message
        if ( HK_DEBUG ) {
            $this->sendMessage( '<b>Defender</b> deactivated' );
        }
    }

    /**
     * Is CC payment method?
     *
     * @param array $posted_data
     * @return boolean
     */
    public function isCC( array $posted_data ): bool
    {
        return isset( $posted_data['payment_method'] ) &&
               in_array( $posted_data['payment_method'], self::$cc_methods, true );
    }

    /**
     * Check Role/Capabilities version & update if needed
     *
     * @return void
     */
    public function checkRoleVersion(): void
    {
        if ( version_compare( (string) get_option( 'banned_version' ), self::$role_version ) < 0 ) {
            $this->removeCSRole();
            $this->addCSRole();
            update_option( 'banned_version', self::$role_version );
        }
    }

    /**
     * Remove expired bans (cron)
     *
     * @return void
     */
    public function removeExpiredBans(): void
    {
        $banned = get_option( 'banned_version' );
        foreach ( $banned as $iphash => $value ) {
            if ( current_time( 'U', true ) > $value['expired'] ) {
                unset( $banned[$iphash] );
            }
        }
        update_option( 'banned_data', $banned );
    }

    /**
     * Add Customer Support role & capabilities
     *
     * @return void
     */
    public function addCSRole(): void
    {
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

        $adm_roles = [ 'administrator', 'shop_manager', 'customer_support' ];

        foreach ( $adm_roles as $adm_role ) {
            wp_roles()->add_cap( $adm_role, 'view_wc_bans' );
            wp_roles()->add_cap( $adm_role, 'edit_wc_bans' );
        }
    }

    /**
     * Remove Customer Support role & capabilities
     *
     * @return void
     */
    private function removeCSRole(): void
    {
        remove_role( 'customer_support' );
        foreach ( wp_roles()->roles as $role => $user_roles ) {
            wp_roles()->remove_cap( $role, 'view_wc_bans' );
            wp_roles()->remove_cap( $role, 'edit_wc_bans' );
        }
    }

    /**
     * Get IPAPI data
     *
     * @param  string     $ip
     * @return void
     */
    private function getIPAPIData( string $ip = '' ): void
    {
        if ( ! $ip ) {
            $ip = \WC_Geolocation::get_ip_address();
        }

        if ( ! class_exists( 'Theme\Integrations\IPAPI' ) ) {
            $this->ipapi = [ 'query' => $ip ];
            return;
        }

        static $fields = [
            'city',
            'region',
            'regionName',
            'country',
            'org',
            'isp',
            'timezone',
            'hosting',
            'mobile',
            'proxy',
            'query',
        ];

        $this->ipapi = IPAPI::get( $fields, $ip ) ?? [ 'query' => $ip ];
    }

    /**
     * Get cart total and currency
     *
     * @return string
     */
    private function getTotalAndCurrency(): string
    {
        return get_woocommerce_currency_symbol() . WC()->cart->get_cart_contents_total();
    }

    /**
     * Get cart items
     *
     * @return string
     */
    private function getItemsList(): string
    {
        $names = array_map( static fn ( $p ) => $p['data']->get_name(), WC()->cart->get_cart() );

        return implode( ", ", $names );
    }

    private function formatMessage( string $reason ): string
    {
        $user_id    = get_current_user_id();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $message    = <<<MSG
        <b>Reason:</b> {$reason}\n
        <b>User ID:</b> <code>{$user_id}</code>
        <b>User Agent:</b> <code>{$user_agent}</code>
        <b>Total:</b> {$this->getTotalAndCurrency()}
        <b>Items:</b> {$this->getItemsList()}
        \n<b>IPAPI:</b> \n{$this->formatIPAPI()}
        \n<b>Checkout form data:</b> \n{$this->formatPostedData()}
        MSG;

        return $message;
    }

    /**
     * Format IPAPI Data
     *
     * @return string
     */
    private function formatIPAPI(): string
    {
        $result = '';
        if ( $this->ipapi ) {
            foreach ( $this->ipapi as $key => $val ) {
                $key = ucfirst( $key );
                if ( true === $val ) {
                    $val = '✅';
                }
                elseif ( false === $val ) {
                    $val = '❌';
                }
                else {
                    $val = "<code>$val</code>";
                }
                $result .= "\t<b>{$key}:</b> {$val}\n";
            }
        }

        return $result;
    }

    /**
     * Format checkout POST data
     *
     * @return string
     */
    private function formatPostedData(): string
    {
        $data = WC()->checkout()->get_posted_data();
        $result = '';
        foreach ( array_filter( $data ) as $key => $val ) {
            $key = ucfirst( $key );
            $result .= "\t<b>{$key}:</b> <code>{$val}</code>\n";
        }

        return $result;
    }

    /**
     * Check client's previous orders while processing current
     *
     * @return void
     */
    public function checkClient(): void
    {
        $this->getIPAPIData();
        $posted_data = WC()->checkout()->get_posted_data();

        if ( is_user_logged_in() || ! $this->isCC( $posted_data ) ) {
            return;
        }

        // headless check
        $this->checkFP( $posted_data );

        $ip          = $this->ipapi['query'];
        $iphash      = md5( $ip );
        $banned      = get_option( 'banned_data' );
        $pm_trigger  = $this->isCC( $posted_data );

        // already banned
        if ( $pm_trigger && isset( $banned[ $iphash ] ) && current_time( 'U', true ) < $banned[ $iphash ]['expired'] ) {
            if ( HK_DEBUG ) {
                $message = $this->formatMessage( "Banned user rejected!" );
                $this->sendMessage( $message );
            }
            $this->bannedMessage();
        }

        // check previous orders
        $args = [
            'customer_ip_address' => $ip,
            'status'              => [ 'wc-failed', 'wc-flagged', 'wc-pending' ],
            'date_created'        => '>' . ( current_time( 'U', true ) - HOUR_IN_SECONDS ),
            'payment_method'      => self::$cc_methods,
            'limit'               => -1,
            'return'              => 'ids',
        ];

        $failed_orders = wc_get_orders( $args );

        if ( count( $failed_orders ) >= $this->calcClientScoring() ) {
            $banned[ $iphash ] = [
                'ip'      => $ip,
                'expired' => current_time( 'U', true ) + HOUR_IN_SECONDS,
                'ipapi'   => true,
                'orders'  => $failed_orders,
            ];

            do_action( 'site_user_banned' );
            update_option( 'banned_data', $banned );

            // only specific payment systems
            if ( $pm_trigger ) {
                $this->bannedMessage();
            }
        }
    }

    /**
     * Check FP
     *
     * @param array $posted_data WC checkout POST data
     * @return void
     */
    private function checkFP( array $posted_sata ): void
    {
        if ( empty( $_REQUEST['wp_checkout'] ) || ! is_string( $_REQUEST['wp_checkout'] ) ) {
            WC()->session->set( 'reload_checkout', true );
            if ( HK_DEBUG ) {
                $message = $this->formatMessage( '<code>wp_checkout</code> field is empty (no JS)' );
                $this->sendMessage( $message );
            }
            $this->bannedMessage();
        }

        $data = $this->decryptFPData( $_REQUEST['wp_checkout'] );

        // wtf?
        if ( ! isset( $data['d']['error'] ) && ! isset( $data['d']['bot'] ) ) {
            $request     = '<code>' . htmlspecialchars( var_export( $_REQUEST, true ) ) . '</code>';
            $data_export = var_export( $data, true );
            $message     = $this->formatMessage( "Unknown error!\n<b>Data:</b>\n{$data_export}\n<b>Request:</b>\n{$request}" );
            $this->sendMessage( $message );
            $this->bannedMessage();
        }

        // error
        elseif ( isset( $data['d']['error'] ) ) {
            $data_export = var_export( $data['d']['error'], true );
            $message     = $this->formatMessage( "Client JS error!\n<b>Error:</b>\n{$data_export}" );
            $this->sendMessage( $message );
            $this->bannedMessage();
        }

        // it's a bot (headless browser)
        elseif ( $this->isCC( $posted_sata ) && isset( $data['d']['bot'] ) && $data['d']['bot'] ) {
            $headless_type  = $data['d']['botKind'] ?? 'error';
            $is_strict_mode = get_option( 'wv_strict_mode' );
            $prefix_message = $is_strict_mode ? 'Banned (strict mode)' : 'Info (not banned)';
            $message        = $this->formatMessage( "{$prefix_message}\nHeadless browser detected!\n<b>Type:</b>\n{$headless_type}" );
            $this->sendMessage( $message );
            if ( ! HK_DEBUG ) {
                // ban
                $iphash            = md5( $this->ipapi['query'] );
                $banned            = get_option( 'banned_data' );
                $banned[ $iphash ] = [
                    'ip'      => $this->ipapi['query'],
                    'expired' => current_time( 'U', true ) + HOUR_IN_SECONDS,
                    'ipapi'   => $this->ipapi,
                    'orders'  => [],
                ];

                do_action( 'site_user_banned' );
                update_option( 'banned_data', $banned );
            }

            if ( $is_strict_mode ) {
                $this->bannedMessage();
            }
        }
    }

    /**
     * Decrypt FP data
     *
     * @param  string $data
     * @return mixed
     */
    private function decryptFPData( string $data )
    {
        try {
            $secret  = hash( 'sha256', $_REQUEST['woocommerce-process-checkout-nonce'] );
            $key     = hex2bin( substr( $secret, 0, 32 ) );
            $iv      = hex2bin( substr( $secret, 32 ) );
            $decrypt = openssl_decrypt( $data, 'AES-128-CBC', $key, 0, $iv );
            $result  = trim( $decrypt );

            return json_decode( $result, true );
        } catch ( \Throwable $th ) {
            $error   = $th->getMessage();
            $data    = htmlspecialchars( $data );
            $message = $this->formatMessage(
                "Decryption failed!\n<b>Error:</b>\n<pre>{$error}</pre>\n<b>Data:</b>\n<pre>{$data}</pre>\n"
            );
            $this->sendMessage( $message );
            $this->bannedMessage();
        }
    }

    /**
     * Throw exception for banned users
     *
     * @throws Exception
     */
    private function bannedMessage()
    {
        $support_page = get_page_link( 147292 );
        throw new Exception( 'Your payment was declined. Contact <a href="' . $support_page . '">our support</a>.' );
    }

    /**
     * Get client scroring based on IPAPI data
     *
     * @return int
     */
    private function calcClientScoring(): int
    {
        $ipapi = $this->ipapi;

        if ( isset( $ipapi['hosting'], $ipapi['proxy'] ) && ( $ipapi['hosting'] || $ipapi['proxy'] ) ) {
            return 2;
        }

        if ( isset( $ipapi['mobile'] ) && $ipapi['mobile'] ) {
            return 6;
        }

        return 5;
    }

    /**
     * Add "Bans" submenu
     *
     * @return void
     */
    public function addBannedWCSubmenu(): void
    {
        add_submenu_page(
            'woocommerce',
            'Banned IPs',
            'Bans',
            'view_wc_bans',
            'wc-banned',
            [ $this, 'bannedPageOutput' ],
            90
        );
    }

    /**
     * "Banned IP addresses" page output
     *
     * @return void
     */
    public function bannedPageOutput(): void
    {
        // Handle POST request
        if ( isset( $_REQUEST['users'] ) && is_array( $_REQUEST['users'] ) ) {
            check_admin_referer( 'unban_users' );
            $this->removeBans( $_REQUEST['users'] );
        }

        if ( isset( $_REQUEST['submit_strict_mode'] ) ) {
            check_admin_referer( 'enable_strict_mode' );
            update_option( 'wv_strict_mode', ! empty( $_REQUEST['wv_strict_mode'] ) );
        }
        $is_strict_mode = get_option( 'wv_strict_mode' );
        ?>
        <h1 class="wp-heading-inline">Banned IP addresses</h1>
        <hr class="wp-header-end">
        <form action="" method="post">
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" id="ip" class="manage-column column-ip column-primary">
                        <span>IP Address</span>
                    </th>
                    <th scope="col" id="failed-orders" class="manage-column column-failed-orders">
                        <span>Affected Orders</span>
                    </th>
                    <th scope="col" id="banned-until" class="manage-column column-banned-until">
                        <span>Banned until</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ( get_option( 'banned_data' ) as $iphash => $data ) {
                    if ( current_time( 'U', true ) < $data['expired'] ) {
                        echo $this->tableRow( $iphash, $data );
                    }
                }
                ?>
                </tbody>
            </table>
            <?php
            if ( current_user_can( 'edit_wc_bans' ) ) {
                wp_nonce_field( 'unban_users' );
                submit_button( 'Unban selected' );
            }
            ?>
        </form>
        <h2>Strict mode</h2>
        <form action="" method="post">
            <div>
                <fieldset>
                    <legend class="screen-reader-text"><span>Strict mode</span></legend>
                    <label for="wv_strict_mode">
                        <input name="wv_strict_mode" type="checkbox" id="wv_strict_mode" <?= checked( $is_strict_mode ) ?>>
                        Enable strict mode
                    </label>
                    <p class="description">In this mode all headless browsers will be cancelled.</p>
                </fieldset>
                <?php
                if ( current_user_can( 'edit_wc_bans' ) ) {
                    wp_nonce_field( 'enable_strict_mode' );
                    submit_button( 'Save', 'primary', 'submit_strict_mode' );
                }
                ?>
        </form>
        <?php
    }

    /**
     * Table row HTML output
     *
     * @param  string $iphash
     * @param  array  $data
     * @return string
     */
    private function tableRow( string $iphash, array $data ): string
    {
        static $row = <<<'HTML'
            <tr valign="top">
                <th scope="row" class="check-column">
                    <label class="screen-reader-text" for="ip_%1$s">Select alexanderbulanov</label>
                    <input type="checkbox" name="users[]" id="user_%1$s" class="ip" value="%1$s">
                </th>
                <td class="ip column-ip column-primary" data-colname="IP">
                    <code>%2$s</code>
                </td>
                <td class="failed-orders column-failed-orders" data-colname="Failed Orders">
                    %3$s
                </td>
                <td class="until column-until" data-colname="Banned Until">
                    <time datetime="%4$s">%5$s</time>
                </td>
            </tr>
        HTML;

        return sprintf(
            $row,
            $iphash,
            $data['ip'],
            $this->formatOrders( $data['orders'] ),
            wp_date( 'c', $data['expired'] ),
            wp_date( 'r', $data['expired'] ),
        );
    }

    /**
     * Format order links
     *
     * @param  array  $orders
     * @return string
     */
    private function formatOrders( array $orders ): string
    {
        $orders_html = array_map(
            static fn ( $id ) => '<div><a href="' . admin_url( "post.php?post={$id}&action=edit" ) . '">#' . $id . '</a></div>', $orders
        );

        return implode( "\n", $orders_html );
    }

    /**
     * Remove bans
     *
     * @param  array $iphashes
     * @return int
     */
    private function removeBans( array $iphashes = [] ): int
    {
        $banned = get_option( 'banned_data' );

        if ( empty( $iphashes ) ) {
            $count = count( $banned  );
            update_option( 'banned_data', [] );
        } else {
            $count = 0;
            foreach ( $iphashes as $iphash ) {
                if ( isset( $banned[ $iphash ] ) ) {
                    unset( $banned[ $iphash ] );
                    $count++;
                }
            }
            if ( $count ) {
                update_option( 'banned_data', $banned );
            }
        }

        return $count;
    }

    /**
     * Fires when preparing to serve a REST API request.
     *
     * @param WP_REST_Server $wp_rest_server Server object.
     */
    function registerRESTRoute( WP_REST_Server $wp_rest_server ): void
    {
        static $base = 'wc/def';

        register_rest_route(
            $base,
            '/bans',
            [
                'methods'             => [ WP_REST_Server::READABLE, WP_REST_Server::DELETABLE ],
                'callback'            => [ $this, 'sendResponce' ],
                'permission_callback' => [ $this, 'getPermission' ],
                'show_in_index'       => false,
                'args'                => [
                    'ip' => [
                        'required'          => false,
                        'type'              => 'string',
                        'format'            => 'ip',
                        'validate_callback' => 'rest_is_ip_address',
                    ],
                ],
            ],
        );
    }

    /**
     * REST permission callback
     *
     * @param  WP_REST_Request $request
     * @return bool
     */
    public function getPermission( WP_REST_Request $request ): bool
    {
        if ( WP_REST_Server::READABLE === $request->get_method() ) {
            return current_user_can( 'view_wc_bans' );
        }
        elseif ( WP_REST_Server::DELETABLE === $request->get_method() ) {
            return current_user_can( 'edit_wc_bans' );
        }

        return false;
    }

    /**
     * Rest responce
     *
     * @param  WP_REST_Request  $request
     * @return WP_REST_Response
     */
    public function sendResponce( WP_REST_Request $request ): WP_REST_Response
    {
        $banned = get_option( 'banned_data' );
        $iphash = $request->has_param( 'ip' ) ? md5( $request->get_param( 'ip' ) ) : '';

        if ( $iphash ) {
            if ( isset( $banned[ $iphash ] ) && WP_REST_Server::READABLE === $request->get_method() ) {
                return new WP_REST_Response(
                    [ 'data' => [ $iphash => $banned[ $iphash ] ] ]
                );
            }
            elseif ( isset( $banned[ $iphash ] ) && WP_REST_Server::DELETABLE === $request->get_method() ) {
                $count = $this->removeBans( [ $iphash ] );
                return new WP_REST_Response(
                    [ 'message' => 'Removed', 'data' => [ 'count' => $count ] ]
                );
            }
            else {
                return new WP_REST_Response(
                    [ 'message' => 'IP not found' ],
                    404
                );
            }
        }
        elseif ( WP_REST_Server::READABLE === $request->get_method() ) {
            return new WP_REST_Response(
                [ 'data' => $banned ]
            );
        }
        elseif ( WP_REST_Server::DELETABLE === $request->get_method() ) {
            $count = $this->removeBans();
            return new WP_REST_Response(
                [ 'message' => 'Removed', 'data' => [ 'count' => $count ] ]
            );
        }

        return new WP_REST_Response( 'Gone', 403 );
    }

    /**
     * Register JS
     *
     * @return void
     */
    public function registerJS(): void
    {
        wp_register_script(
            'hello-kitty',
            $this->script_url,
            [],
            HK_ASSETS_HASH
        );
    }

    /**
     * Preload JS
     *
     * @return void
     */
    public function preloadJS(): void
    {
        if ( is_checkout() ) {
            $url = esc_attr( $this->script_url ) . '?' .  build_query( ['version' => HK_ASSETS_HASH] );
            echo '<link rel="preload" href="' . $url . '" as="script" />';
        }
    }

    /**
     * Enqueue JS
     *
     * @return void
     */
    public function queueJS(): void
    {
        if ( is_checkout() ) {
            wp_enqueue_script( 'hello-kitty' );
        }
    }

    /**
     * Send message to Telegram
     *
     * @param  string $text
     * @return void
     */
    public function sendMessage( string $text ): void
    {
        if ( 'production' !== WP_ENV ) return;
        $url         = 'https://api.telegram.org/bot' . HK_TG_TOKEN . '/sendMessage';
        $message_raw = [
            'text'       => $text,
            'chat_id'    => HK_TG_CHAT_ID,
            'parse_mode' => 'HTML'
        ];

        $message = json_encode( $message_raw, JSON_INVALID_UTF8_SUBSTITUTE );

        $args = [
            'blocking' => false,
            'compress' => true,
            'headers'  => [
                'Content-Type' => 'application/json'
            ],
            'body'     => $message,
        ];

        wp_remote_post( $url, $args );
    }

    /**
     * Ban notification
     *
     * @param  array $data
     * @return void
     */
    public function userBannedNotification(): void
    {
        $message = $this->formatMessage( 'User banned' );
        $this->sendMessage( $message );
    }
}

$wv_def = new Defender();

register_activation_hook( __FILE__, [ $wv_def, 'activation' ] );
register_deactivation_hook( __FILE__, [ $wv_def, 'deactivation' ] );