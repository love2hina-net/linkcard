<?php
namespace love2hina\wordpress\linkcard;

/**
 * DBアクセスヘルパークラス.
 *
 * @since      1.0.0
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class LinkcardDatabase
{
    /** スキーマID */
    const DATABASE_SCHEMA_ID = '8666c73b-ce75-4659-9adf-f4e9c5985873';

    /** プラグイン本体クラス */
    protected readonly object $plugin;

    /** テーブル名 */
    protected readonly string $table_cache;

    public function __construct(object $plugin)
    {
        global $wpdb;

        $this->plugin = $plugin;
        $this->table_cache = "{$wpdb->base_prefix}{$plugin->get_prefix()}OpenGraphCache";
    }

    public function create_table(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $result = $wpdb->query(<<<"EOF"
            CREATE TABLE {$this->table_cache} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                creation_time TIMESTAMP NOT NULL,
                url TEXT NOT NULL,
                title TEXT,
                description TEXT,
                image TEXT,
                site TEXT,
                INDEX url (url),
                INDEX creation_time (creation_time ASC)
            ) $charset_collate;
            EOF);
        if ($result !== true) {
            \trigger_error("Create a table was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
    }

    public function drop_table(): void
    {
        global $wpdb;

        $result = $wpdb->query("DROP TABLE IF EXISTS {$this->table_cache};");
        if ($result !== true) {
            \trigger_error("Delete a table was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
    }

}
