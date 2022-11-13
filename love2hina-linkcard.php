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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const LINKCARD_VERSION = '1.0.0';

/** Wordpress plugin id */
const LINKCARD_UID = 'love2hina-linkcard';

/** prefix */
const LINKCARD_PREFIX = 'love2hina_linkcard_';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-linkcard-activator.php
 */
function activate_linkcard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkcard-activator.php';
	Linkcard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-linkcard-deactivator.php
 */
function deactivate_linkcard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkcard-deactivator.php';
	Linkcard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'love2hina\wordpress\linkcard\activate_linkcard' );
register_deactivation_hook( __FILE__, 'love2hina\wordpress\linkcard\deactivate_linkcard' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-linkcard.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_linkcard() {

	$plugin = new Linkcard();
	$plugin->run();

}
run_linkcard();
