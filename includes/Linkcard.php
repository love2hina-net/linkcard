<?php
namespace love2hina\wordpress\linkcard;

/**
 * プラグイン本体クラス.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
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
     * プラグインローダー.
     *
     * @access   public
     * @var      LinkcardLoader $loader
     */
    public readonly object  $loader;

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
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @param   string  $name
     * @param   string  $version
     * @param   string  $prefix
     */
    public function __construct(string $name, string $version, string $prefix)
    {
        $this->name = $name;
        $this->version = $version;
        $this->prefix = $prefix;

        $this->load_dependencies();

        $this->config = new LinkcardConfig($this);
        $this->database = new LinkcardDatabase($this);
        $this->loader = new LinkcardLoader();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - LinkcardLoader. Orchestrates the hooks of the plugin.
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
        require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/Linkcardi18n.php');

        // 設定値アクセス
        require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/LinkcardConfig.php');
        // DBアクセス
        require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/LinkcardDatabase.php');

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/LinkcardLoader.php');

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/LinkcardAdmin.php');

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once(plugin_dir_path(dirname( __FILE__ )) . 'public/LinkcardPublic.php');
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
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Linkcardi18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Linkcardi18n($this);
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_admin_hooks(): void
    {
        $plugin_admin = new LinkcardAdmin($this);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access   private
     */
    private function define_public_hooks(): void
    {
        $plugin_public = new LinkcardPublic($this);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run(): void
    {
        $this->set_locale();

        $this->initialize();

        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->loader->run();
    }

}
