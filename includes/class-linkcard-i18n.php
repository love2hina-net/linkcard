<?php
namespace love2hina\wordpress\linkcard;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.love2hina.net/
 * @since      1.0.0
 *
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    love2hina_Linkcard
 * @subpackage love2hina_Linkcard/includes
 * @author     webmaster@love2hina.net
 */
class Linkcard_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			LINKCARD_UID,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
