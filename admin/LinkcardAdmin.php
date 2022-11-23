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

    /** プラグイン本体クラス */
    protected readonly object   $plugin;

    /** メニューSLUG */
    protected readonly string   $menu_slug;

    public function __construct(object $plugin)
    {
        $this->plugin = $plugin;
        $this->menu_slug = $this->plugin->get_prefix() . 'settings';

        $this->plugin->get_loader()->add_action('admin_enqueue_scripts', $this, 'enqueue_styles');
        $this->plugin->get_loader()->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
        $this->plugin->get_loader()->add_action('admin_menu', $this, 'admin_menu');
    }

    public function admin_menu(string $context): void
    {
        $pagename = 'LinkCard Settings'; // __( '[HCB] Settings', 'loos-hcb' );
        \add_options_page(
            $pagename,
            $pagename,
            'manage_options',
            $this->menu_slug,
            [$this, 'settings_callback']
        );
    }

    public function settings_callback(): void
    {
        echo 'TESTING!';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
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

        wp_enqueue_style($this->plugin->get_plugin_name(), plugin_dir_url(__FILE__) . 'css/linkcard-admin.css', array(), $this->plugin->get_version(), 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
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

        wp_enqueue_script($this->plugin->get_plugin_name(), plugin_dir_url(__FILE__) . 'js/linkcard-admin.js', array('jquery'), $this->plugin->get_version(), false);

    }

}
