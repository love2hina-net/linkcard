<?php
namespace love2hina\wordpress\linkcard;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           love2hina_Linkcard
 * @author		  	  webmaster@love2hina.net
 * @copyright         Copyright (C) 2022 webmaster@love2hina.net
 * @link              https://www.love2hina.net/
 * @license           GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name:       love2hina Linkcard
 * Plugin URI:
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            webmaster@love2hina.net
 * Author URI:        https://www.love2hina.net/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       love2hina-linkcard
 * Domain Path:       /languages
 * Requires PHP:      8.1.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/** プラグインバージョン */
const LINKCARD_VERSION = '1.0.0';

/** プラグインID */
const LINKCARD_UID = 'love2hina-linkcard';

/** prefix */
const LINKCARD_PREFIX = 'love2hina_linkcard_';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require(plugin_dir_path(__FILE__) . 'includes/Linkcard.php');
$plugin = new Linkcard(LINKCARD_UID, LINKCARD_VERSION, LINKCARD_PREFIX);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-linkcard-activator.php
 */
function activate_linkcard()
{
    $plugin->activate();
}
register_activation_hook(__FILE__, 'love2hina\wordpress\linkcard\activate_linkcard');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-linkcard-deactivator.php
 */
function deactivate_linkcard()
{
    $plugin->deactivate();
}
register_deactivation_hook(__FILE__, 'love2hina\wordpress\linkcard\deactivate_linkcard');

// Begins execution of the plugin.
$plugin->run();
