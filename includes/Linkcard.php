<?php
namespace love2hina\wordpress\linkcard;

/**
 * プラグイン本体クラス.
 *
 * 依存関係や、actionsなどを管理します。
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class Linkcard
{
    /**
     * 設定値アクセス.
     *
     * @access  public
     * @var     LinkcardConfig  $config
     */
    public readonly object  $config;

    /**
     * DBアクセス.
     *
     * @access  public
     * @var     LinkcardDatabase    $database
     */
    public readonly object  $database;

    /**
     * プラグイン識別子.
     *
     * @access   public
     * @var      string     $name
     */
    public readonly string  $name;

    /**
     * プラグインバージョン.
     *
     * @access   public
     * @var      string     $version
     */
    public readonly string  $version;

    /**
     * プリフィクス.
     *
     * @access   public
     * @var      string     $prefix
     */
    public readonly string  $prefix;

    /**
     * ディレクトリパス.
     *
     * @access  public
     * @var     string      $dir_path
     */
    public readonly string  $dir_path;

    /**
     * ディレクトリURL.
     *
     * @access  public
     * @var     string      $dir_url
     */
    public readonly string  $dir_url;

    /**
     * WordPress Action hooksのキュー.
     *
     * @access  protected
     * @var     array   $actions
     */
    protected array $actions;

    /**
     * WordPress Filter hooksのキュー.
     *
     * @access  protected
     * @var     array   $filters
     */
    protected array $filters;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @param   string  $name
     * @param   string  $version
     * @param   string  $prefix
     * @param   string  $basefile
     */
    public function __construct(string $name, string $version, string $prefix, string $basefile)
    {
        $this->name = $name;
        $this->version = $version;
        $this->prefix = $prefix;
        $this->dir_path = \plugin_dir_path($basefile);
        $this->dir_url = \plugin_dir_url($basefile);
        $this->actions = array();
        $this->filters = array();

        $this->load_dependencies();

        $this->config = new LinkcardConfig($this);
        $this->database = new LinkcardDatabase($this);

        \register_activation_hook($basefile, [$this, 'activate']);
        \register_deactivation_hook($basefile, [$this, 'deactivate']);
    }

    /**
     * モジュール読み込み.
     *
     * @param   string  $file   プラグインディレクトリからの相対パス
     * @param   bool    $once   true - require_onceで読み込む
     */
    public function load_module(string $file, bool $once = true): mixed
    {
        $fullpath = $this->dir_path . $file;
        if ($once) {
            return require_once $fullpath;
        }
        else {
            return require $fullpath;
        }
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Linkcardi18n. Defines internationalization functionality.
     * - LinkcardAdmin. Defines all hooks for the admin area.
     * - LinkcardPublic. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @access   private
     */
    private function load_dependencies(): void
    {
        // ローカライゼーション
        $this->load_module('includes/Linkcardi18n.php');
        // 設定値アクセス
        $this->load_module('includes/LinkcardConfig.php');
        // DBアクセス
        $this->load_module('includes/LinkcardDatabase.php');

        // 管理画面
        $this->load_module('admin/LinkcardAdmin.php');
        // 通常画面
        $this->load_module('public/LinkcardPublic.php');
    }

    /**
     * Action hookを登録します.
     *
     * パラメーターはadd_actionと同じです。
     *
     * @param   string      $hook           アクション名
     * @param   callable    $callback       コールバックエントリ
     * @param   int         $priority       優先度
     * @param   int         $accepted_args  受け入れる引数の数
     * @see https://developer.wordpress.org/reference/functions/add_action
     */
    public function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->add_entry($this->actions, $hook, $callback, $priority, $accepted_args);
    }

    /**
     * Filter hookを登録します.
     *
     * パラメーターはadd_filterと同じです。
     *
     * @param   string      $hook           フィルタ名
     * @param   callable    $callback       コールバックエントリ
     * @param   int         $priority       優先度
     * @param   int         $accepted_args  受け入れる引数の数
     * @see https://developer.wordpress.org/reference/functions/add_filter
     */
    public function add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->add_entry($this->filters, $hook, $callback, $priority, $accepted_args);
    }

    /**
     * 登録キューに追加します.
     *
     * @param   array       &$entries       登録キュー
     * @param   string      $hook           アクション/フィルタ名
     * @param   callable    $callback       コールバックエントリ
     * @param   int         $priority       優先度
     * @param   int         $accepted_args  受け入れる引数の数
     */
    private function add_entry(array &$entries, string $hook, callable $callback, int $priority, int $accepted_args): void
    {
        $entries[] = [
            'hook'          => $hook,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        ];
    }

    /**
     * キューから登録します.
     *
     * @param   array       &$entries   登録キュー
     * @param   callable    $wp_addfunc 呼び出すWordPressの関数
     */
    private function register_entries(array &$entries, callable $wp_addfunc): void
    {
        foreach ($entries as $hook) {
            $wp_addfunc($hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args']);
        }
    }

    /**
     * スタイルシートを登録します.
     *
     * @param   string  $name   識別名
     * @param   string  $file   プラグインディレクトリからの相対パス
     * @param   array   $deps   依存関係
     * @param   string|bool|null    $ver    バージョン
     *  - string:   指定したバージョン
     *  - true:     プラグインのバージョンで補完
     *  - false:    WordPressのバージョンで補完
     *  - null:     バージョンを付与しない
     * @param   string  $media
     */
    public function enqueue_style(string $name, string $file, array $deps = array(), string|bool|null $ver = true, string $media = 'all'): void
    {
        \wp_enqueue_style(
            $this->prefix . $name,
            $this->dir_url . $file,
            $deps,
            ($ver === true)? $this->version : $ver,
            $media
        );
    }

    /**
     * スクリプトを登録します.
     *
     * @param   string  $name   識別名
     * @param   string  $file   プラグインディレクトリからの相対パス
     * @param   array   $deps   依存関係
     * @param   string|bool|null    $ver    バージョン
     *  - string:   指定したバージョン
     *  - true:     プラグインのバージョンで補完
     *  - false:    WordPressのバージョンで補完
     *  - null:     バージョンを付与しない
     * @param   string  $in_footer
     */
    public function enqueue_script(string $name, string $file, array $deps = array(), string|bool|null $ver = true, bool $in_footer = false): void
    {
        \wp_enqueue_script(
            $this->prefix . $name,
            $this->dir_url . $file,
            $deps,
            ($ver === true)? $this->version : $ver,
            $in_footer
        );
    }

    public function activate(): void
    {
        $this->initialize();
    }

    private function initialize(): void
    {
        switch ($this->config->schema_id)
        {
            case null:
                // 未作成
                $this->database->create_table();

                $this->config->schema_id = LinkcardDatabase::DATABASE_SCHEMA_ID;
                $this->config->apply();
                break;

            case LinkcardDatabase::DATABASE_SCHEMA_ID:
                // 初期化済
                // 期限切れキャッシュの削除
                $this->database->truncate_cache($this->config->cache_lifetime);
                break;

            default:
                // 再作成
                $this->database->drop_table();
                $this->database->create_table();

                $this->config->schema_id = LinkcardDatabase::DATABASE_SCHEMA_ID;
                $this->config->apply();
                break;
        }
    }

    public function deactivate(): void
    {
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run(): void
    {
        new Linkcardi18n($this);

        $this->initialize();

        new LinkcardAdmin($this);
        new LinkcardPublic($this);

        $this->register_entries($this->filters, 'add_filter');
        $this->register_entries($this->actions, 'add_action');
    }

}
