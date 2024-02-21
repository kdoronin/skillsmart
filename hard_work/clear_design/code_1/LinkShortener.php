<?php

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


        $sql = 'CREATE TABLE ' . $this->tableName . ' (
            id bigint(20) unsigned NOT NULL auto_increment UNIQUE,
            link_key varchar(6) NOT NULL,
            link_value longtext NOT NULL,
            creation_date timestamp NOT NULL DEFAULT current_timestamp,
            PRIMARY KEY (id)
            ) ' . $this->wpdb->get_charset_collate();
        if (!function_exists('maybe_create_table')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }
        maybe_create_table($this->tableName, $sql);
    }

    /**
     * @return void
     */
    public function deactivatePlugin(): void
    {
        delete_option('wvls_flushed');
        add_action('shutdown', 'flush_rewrite_rules');
    }

    /**
     * Find link original value by its short key or return empty string
     *
     * @param string $key
     * @return string
     */
    public function getLinkByKey(string $key): string
    {
        if (empty($key)) {
            return '';
        }
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                'SELECT link_value FROM ' . $this->tableName . ' WHERE link_key = %s',
                $key
            )
        );
        return $result ?? '';
    }

    /**
     * Get short link, insert its key to the table
     *
     * @param string $link
     * @return string
     */
    public function getShortLink(string $link): string
    {
        if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
            return '';
        }

        $this->removeExpiredLinks();

        if (!empty($oldKey = $this->findLinkKeyByValue($link))) {
            return $this->path . $oldKey . '/';
        }

        while ($this->isKeyEnabled($key = $this->generateRandomKey())) {
            $key = $this->generateRandomKey();
        }

        $this->wpdb->query(
            $this->wpdb->prepare(
                'INSERT INTO ' . $this->tableName . '  (link_key, link_value, creation_date) VALUES (%s, %s, DEFAULT)',
                $key, $link
            )
        );

        return $this->path . $key . '/';
    }

    /**
     * Remove old links from the table
     *
     * @return void
     */
    private function removeExpiredLinks(): void
    {
        $this->wpdb->query(
            $this->wpdb->prepare(
                'DELETE FROM ' . $this->tableName . ' WHERE creation_date <= CURRENT_DATE() - INTERVAL %s',
                self::LINK_STORAGE_TIME)
        );
    }

    /**
     * Generate 6 chars random string
     *
     * @return string
     */
    private function generateRandomKey(): string
    {
        $randomKey = '';
        $length = 6;
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $count = strlen($charset);

        while ($length--) {
            $randomKey .= $charset[mt_rand(0, $count - 1)];
        }

        return $randomKey;
    }

    /**
     * Check if generated key is valid
     *
     * @param string $key
     * @return bool
     */
    private function isKeyEnabled(string $key): bool
    {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare(
                'SELECT COUNT(*) FROM ' . $this->tableName . ' WHERE link_key = %s',
                $key
            )
        );
        return $count === 0;
    }

    /**
     * Try to find link key by its value
     *
     * @param string $link
     * @return string
     */
    private function findLinkKeyByValue(string $link): string
    {
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                'SELECT link_key FROM ' . $this->tableName . ' WHERE link_value = %s',
                $link
            )
        );
        return $result ?? '';
    }
}