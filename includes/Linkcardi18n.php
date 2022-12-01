<?php
namespace love2hina\wordpress\linkcard;

$loaded_domain = null;

/**
 * 名前空間内で上書きする。
 */
function __(string $text): string
{
    global $loaded_domain;

    if ($loaded_domain !== null) {
        return \translate($text, $loaded_domain);
    }
    else {
        \trigger_error("Not loaded text domain.", E_USER_WARNING);
        return $text;
    }
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class Linkcardi18n
{

    /** ドメイン */
    protected readonly string   $domain;

    public function __construct(object $plugin)
    {
        $this->domain = $plugin->name;

        $plugin->loader->add_action('plugins_loaded', $this, 'load_plugin_textdomain');
    }

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain(): void
    {
        global $loaded_domain;

        \load_plugin_textdomain(
            $this->domain,
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

        $loaded_domain = $this->domain;
    }

}
