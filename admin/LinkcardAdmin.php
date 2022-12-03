<?php
namespace love2hina\wordpress\linkcard;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/admin
 * @author     webmaster@love2hina.net
 */
class LinkcardAdmin
{

    /**
     * プラグイン本体クラス.
     *
     * @access  protected
     * @var     Linkcard    $plugin
     */
    protected readonly Linkcard $plugin;

    /** メニューSLUG */
    protected readonly string   $menu_slug;

    /** オプショングループ名 */
    protected readonly string   $option_group;

    public function __construct(Linkcard $plugin)
    {
        $this->plugin = $plugin;
        $this->menu_slug = $this->plugin->prefix . 'settings';
        $this->option_group = $this->plugin->name;

        $this->plugin->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $this->plugin->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
        $this->plugin->loader->add_action('admin_init', $this, 'admin_init');
        $this->plugin->loader->add_action('admin_menu', $this, 'admin_menu');
    }

    public function admin_init(): void
    {
        $this->plugin->config->admin_register_settings([
            'option_group' => $this->option_group,
            'section_callback_func' => [$this, 'option_section_callback']
        ]);
    }

    public function option_section_callback(array $args): void
    {
        echo '<p id="' . esc_attr($args['id']) . '">' . esc_html__('Follow the white rabbit.', 'wporg') . '</p>';
    }

    public function admin_menu(string $context): void
    {
        $pagename = __('LinkCard Settings');
        \add_options_page(
            $pagename,
            $pagename,
            'manage_options',
            $this->menu_slug,
            [$this, 'options_callback']
        );
    }

    public function options_callback(): void
    {
        // check user capabilities
        if (!\current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            // \add_settings_error('wporg_messages', 'wporg_message', __('Settings Saved'), 'updated');
        }

        // show error/update messages
        \settings_errors('wporg_messages');

        $title = \esc_html(\get_admin_page_title());
        echo <<< "HEADER"
            <div class="wrap">
                <h1>{$title}</h1>
                <form class="{$this->menu_slug}" action="options.php" method="post">
            HEADER;
        \settings_fields($this->option_group);
        \do_settings_sections($this->option_group);
        \submit_button();
        echo <<< "FOOTER"
                </form>
            </div>
            FOOTER;
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles(): void
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in LinkcardLoader as all of the hooks are defined
         * in that particular class.
         *
         * The LinkcardLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        \wp_enqueue_style($this->plugin->name, plugin_dir_url(__FILE__) . 'css/linkcard-admin.css', array(), $this->plugin->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts(): void
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in LinkcardLoader as all of the hooks are defined
         * in that particular class.
         *
         * The LinkcardLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        \wp_enqueue_script($this->plugin->name, plugin_dir_url(__FILE__) . 'js/linkcard-admin.js', array('jquery'), $this->plugin->version, false);

    }

}
