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
 * @since      1.0.0
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class Linkcard
{
    /**
     * 設定値アクセス.
     *
     * @since   1.0.0
     * @access  protected
     * @var     LinkcardConfig  $config
     */
    protected readonly object   $config;

    /**
     * DBアクセス.
     *
     * @since   1.0.0
     * @access  protected
     * @var     LinkcardDatabase    $database
     */
    protected readonly object   $database;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      LinkcardLoader $loader
     */
    protected readonly object   $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string         $plugin_name
     */
    protected readonly string   $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string         $version
     */
    protected readonly string   $version;

    /**
     * プリフィクス.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string         $prefix
     */
    protected readonly string   $prefix;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since   1.0.0
     * @param   string  $plugin_name
     * @param   string  $version
     * @param   string  $prefix
     */
    public function __construct(string $plugin_name, string $version, string $prefix)
    {
        $this->plugin_name = $plugin_name;
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
     * - Linkcard_i18n. Defines internationalization functionality.
     * - LinkcardAdmin. Defines all hooks for the admin area.
     * - Linkcard_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies(): void
    {
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
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/class-linkcard-i18n.php');

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once(plugin_dir_path(dirname(__FILE__)) . 'admin/LinkcardAdmin.php');

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once(plugin_dir_path(dirname( __FILE__ )) . 'public/class-linkcard-public.php');
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
                // TODO: 期限切れパージ
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
     * Uses the Linkcard_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Linkcard_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new LinkcardAdmin($this);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Linkcard_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run(): void
    {
        $this->initialize();

        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name(): string
    {
        return $this->plugin_name;
    }

    public function get_config(): object
    {
        return $this->config;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    LinkcardLoader    Orchestrates the hooks of the plugin.
     */
    public function get_loader(): object
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version(): string
    {
        return $this->version;
    }

    public function get_prefix(): string
    {
        return $this->prefix;
    }

}
