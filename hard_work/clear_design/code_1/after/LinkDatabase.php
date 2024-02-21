<?php

namespace WowvendorLinkShortener\Includes;

class LinkDatabase
{
    private $tableName;
    private $wpdb;

    const LINK_STORAGE_TIME = '1 MONTH';
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->tableName = $this->wpdb->prefix . 'short_links';
    }

    public function init(): void
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = 'CREATE TABLE ' . $this->tableName . ' (
            id bigint(20) unsigned NOT NULL auto_increment UNIQUE,
            link_key varchar(6) NOT NULL,
            link_value longtext NOT NULL,
            creation_date timestamp NOT NULL DEFAULT current_timestamp,
            PRIMARY KEY (id)
            ) ' . $this->wpdb->get_charset_collate();
        dbDelta($sql);
    }

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

    public function getAllValidKeys(): array
    {
        $this->removeExpiredLinks();
        $result = $this->wpdb->get_results(
            'SELECT link_key FROM ' . $this->tableName
        );
        return $result ?? [];
    }

    public function getKeyByValue(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                'SELECT link_key FROM ' . $this->tableName . ' WHERE link_value = %s',
                $value
            )
        );
        return $result ?? '';
    }

    public function addShortlink(string $link, string $key) : bool
    {
        if (empty($link) || empty($key)) {
            return false;
        }
        $result = $this->wpdb->insert(
            $this->tableName,
            [
                'link_key' => $key,
                'link_value' => $link,
                'creation_date' => current_time('mysql')
            ]
        );
        return $result;
    }

    public function removeExpiredLinks() : void
    {
        $this->wpdb->query(
            $this->wpdb->prepare(
                'DELETE FROM ' . $this->tableName . ' WHERE creation_date < %s',
                date('Y-m-d H:i:s', strtotime('-' . self::LINK_STORAGE_TIME))
            )
        );
    }

    public function deleteLinkByKey(string $key) : bool
    {
        if (empty($key)) {
            return false;
        }
        $result = $this->wpdb->delete(
            $this->tableName,
            [
                'link_key' => $key
            ]
        );
        return $result;
    }

    public function isKeyValid(string $key): bool
    {
        if (empty($key)) {
            return false;
        }
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                'SELECT link_key FROM ' . $this->tableName . ' WHERE link_key = %s',
                $key
            )
        );
        return !empty($result);
    }

}