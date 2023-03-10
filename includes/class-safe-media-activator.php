<?php

/**
 * Fired during plugin activation
 *
 * @link       https://profiles.wordpress.org/brijesh2911/
 * @since      1.0.0
 *
 * @package    Safe_Media
 * @subpackage Safe_Media/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Safe_Media
 * @subpackage Safe_Media/includes
 * @author     Brijesh <brijeshborad29@gmail.com>
 */
class Safe_Media_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if( !class_exists( 'CMB2' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Please install and activate CMB2 plugin.', SAFE_MEDIA_TEXT_DOMAIN ), 'Plugin dependency check', array( 'back_link' => true ) );
		}
	}

}
