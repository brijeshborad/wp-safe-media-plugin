<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/brijesh2911/
 * @since             1.0.0
 * @package           Safe_Media
 *
 * @wordpress-plugin
 * Plugin Name:       Safe Media Delete Plugin
 * Plugin URI:        https://safe-media.local
 * Description:       This plugin prevents attached images with posts and terms to be deleted from Media Library
 * Version:           1.0.0
 * Author:            Brijesh
 * Author URI:        https://profiles.wordpress.org/brijesh2911/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       safe-media
 * Domain Path:       /languages
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
define( 'SAFE_MEDIA_VERSION', '1.0.0' );

define( 'SAFE_MEDIA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SAFE_MEDIA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SAFE_MEDIA_TEXT_DOMAIN', 'safe-media' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-safe-media-activator.php
 */
function activate_safe_media() {
	require_once SAFE_MEDIA_PLUGIN_DIR . 'includes/class-safe-media-activator.php';
	Safe_Media_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-safe-media-deactivator.php
 */
function deactivate_safe_media() {
	require_once SAFE_MEDIA_PLUGIN_DIR . 'includes/class-safe-media-deactivator.php';
	Safe_Media_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_safe_media' );
register_deactivation_hook( __FILE__, 'deactivate_safe_media' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SAFE_MEDIA_PLUGIN_DIR . 'includes/class-safe-media.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_safe_media() {

	$plugin = new Safe_Media();
	$plugin->run();

}

run_safe_media();
