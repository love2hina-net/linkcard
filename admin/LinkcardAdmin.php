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
        $this->plugin->get_loader()->add_action('admin_init', $this, 'admin_init');
        $this->plugin->get_loader()->add_action('admin_menu', $this, 'admin_menu');
    }

    protected function get_option_key(): string
    {
        return $this->plugin->get_config()->get_option_key();
    }

    public function admin_init(): void
    {
        \register_setting($this->plugin->get_plugin_name(), $this->get_option_key());
        \add_settings_section(
            'wporg_section_developers',
            __( 'The Matrix has you.', 'wporg' ),
            [$this, 'wporg_section_developers_callback'],
            $this->plugin->get_plugin_name()
        );
        \add_settings_field(
            'wporg_field_pill',
            __( 'Pill', 'wporg' ),
            [$this, 'wporg_field_pill_cb'],
            $this->plugin->get_plugin_name(),
            'wporg_section_developers',
            array(
                'label_for'         => 'wporg_field_pill',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            )
        );
    }

    public function wporg_section_developers_callback( $args ): void
    {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'wporg' ); ?></p>
        <?php
    }

    public function wporg_field_pill_cb( $args )
    {
        // Get the value of the setting we've registered with register_setting()
        $options = get_option($this->get_option_key());
        ?>
        <select
                id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['wporg_custom_data'] ); ?>"
                name="<?php echo $this->get_option_key(); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]">
            <option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'red pill', 'wporg' ); ?>
            </option>
            <option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
                <?php esc_html_e( 'blue pill', 'wporg' ); ?>
            </option>
        </select>
        <p class="description">
            <?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'wporg' ); ?>
        </p>
        <p class="description">
            <?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'wporg' ); ?>
        </p>
        <?php
    }

    public function setting_field_callback(array $args): void
    {
        echo '<input id="cache_lifetime" name="cache_lifetime[cache_lifetime]" type="number" value="';
        \form_option('cache_lifetime');
        echo '" />' . "\n";
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
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
        }

        // show error/update messages
        settings_errors( 'wporg_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( $this->plugin->get_plugin_name() );
                do_settings_sections( $this->plugin->get_plugin_name() );
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
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
