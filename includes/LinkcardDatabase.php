<?php
namespace love2hina\wordpress\linkcard;

/**
 * DBアクセスヘルパークラス.
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class LinkcardDatabase
{
    /** スキーマID */
    const DATABASE_SCHEMA_ID = '8666c73b-ce75-4659-9adf-f4e9c5985873';

    /**
     * プラグイン本体クラス.
     *
     * @access  protected
     * @var     Linkcard    $plugin
     */
    protected readonly object   $plugin;

    /**
     * テーブル名.

     * @access  protected
     * @var     string      $table_cache
     */
    protected readonly string   $table_cache;

    public function __construct(object $plugin)
    {
        global $wpdb;

        $this->plugin = $plugin;
        $this->table_cache = "{$wpdb->base_prefix}{$plugin->prefix}OpenGraphCache";
    }

    public function create_table(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $result = $wpdb->query(<<<"SQL"
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
            ) $charset_collate
            SQL
        );
        if ($result !== true) {
            \trigger_error("Create a table was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
    }

    public function drop_table(): void
    {
        global $wpdb;

        $result = $wpdb->query("DROP TABLE IF EXISTS {$this->table_cache}");
        if ($result !== true) {
            \trigger_error("Delete a table was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
    }

    public function truncate_cache(int $days): int
    {
        global $wpdb;

        $result = $wpdb->query(<<<"SQL"
            DELETE FROM {$this->table_cache}
            WHERE ADDDATE(creation_time, INTERVAL {$days} DAY) < NOW()
            SQL
        );
        if ($result === false) {
            \trigger_error("Delete caches was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
        return $result;
    }

    public function query_cache(string $url): ?array
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(<<<"SQL"
                SELECT * FROM {$this->table_cache}
                WHERE url = %s
                ORDER BY creation_time DESC
                LIMIT 1
                SQL,
                [$url]
            ),
            \ARRAY_A
        );
    }

    public function insert_cache(array $data): void
    {
        global $wpdb;

        $result = $wpdb->query(
            $wpdb->prepare(<<<"SQL"
                INSERT INTO {$this->table_cache} (
                    creation_time,
                    url,
                    title,
                    description,
                    image,
                    site)
                VALUES (
                    NOW(),
                    %s,
                    %s,
                    %s,
                    %s,
                    %s
                )
                SQL,
                [
                    $data['url'],
                    $data['title'],
                    $data['description'],
                    $data['image'],
                    $data['site']
                ]
            )
        );
        if ($result === false) {
            \trigger_error("Add caches was failed. table: {$this->table_cache}", E_USER_ERROR);
        }
    }

}
