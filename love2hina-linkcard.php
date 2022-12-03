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

require_once \plugin_dir_path(__FILE__) . 'includes/Linkcard.php';
$plugin = new Linkcard(
    'love2hina-linkcard',   // プラグインID
    '1.0.0',                // プラグインバージョン
    'love2hina_linkcard_',  // prefix
    __FILE__                // このファイル名
);

$plugin->run();
