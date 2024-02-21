<?php

namespace WowvendorLinkShortener\Includes;

defined('ABSPATH') || exit();

class LinkShortener
{
    /**
     * DB table name
     * @var string
     */
    private string $tableName;

    /**
     * Links storage time is one month
     */
    const LINK_STORAGE_TIME = '1 MONTH';

    /**
     * A postfix for links to generate it or to redirect by short value
     */
    private const LINK_POSTFIX = 'ls';

    /**
     * A site path with plugin postfix
     * @var string
     */
    private string $path;

    /**
     * @var \QM_DB|\wpdb
     */
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->tableName = $this->wpdb->prefix . 'wv_short_links';
        $this->path = 'https://' . $_SERVER['HTTP_HOST'] . '/' . self::LINK_POSTFIX . '/';
    }

    public function init()
    {
        register_activation_hook(LC_PLUGIN, array($this, 'activatePlugin'));
        register_deactivation_hook(LC_PLUGIN, array($this, 'deactivatePlugin'));

        add_action('init', array($this, 'rewriteRules'));
        add_filter('query_vars', array($this, 'enableQueryVar'));
        add_action('template_redirect', array($this, 'lsEndpoint'));
    }

    /**
     * Add rewrite rules
     */
    public function rewriteRules(): void
    {
        add_rewrite_rule('(?:^|/)' . self::LINK_POSTFIX . '/([^/]+)/?$', 'index.php?' . self::LINK_POSTFIX . '=$matches[1]', 'top');
        if (get_option('wvls_flushed')) {
            add_action('shutdown', static function () {
                delete_option('wvls_flushed');
                flush_rewrite_rules(false);
            });
        }
    }

    /**
     * Filters the query variables allowed before processing
     *
     * @param array $public_query_vars
     * @return array
     */
    public function enableQueryVar(array $public_query_vars): array
    {
        $public_query_vars[] = self::LINK_POSTFIX;
        return $public_query_vars;
    }

    /**
     * Redirect user if endpoint
     *
     * @return void
     */
    public function lsEndpoint(): void
    {
        if (get_query_var(self::LINK_POSTFIX)) {
            header('X-Robots-Tag: none');
            global $wp_query;
            $key = (string)$wp_query->query_vars[self::LINK_POSTFIX];

            if (!empty($link = $this->getLinkByKey($key))) {
                wp_redirect($link);
            } else {
                wp_safe_redirect(get_home_url());
            }
            exit();
        }
    }

    /**
     * Create a table
     */
    public function activatePlugin(): void
    {
        if (!current_user_can('activate_plugins')) {
            deactivate_plugins(plugin_basename(__FILE__));
            return;
        }
        update_option('wvls_flushed', 1, true);
    }

    /**
     * @return void
     */
    public function deactivatePlugin(): void
    {
        delete_option('wvls_flushed');
        add_action('shutdown', 'flush_rewrite_rules');
    }
}