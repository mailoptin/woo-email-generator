<?php
/**
 * @package   Woo_Email_Generator
 * @author    Max Kostinevich <contact@maxkostinevich.com>
 * @license   GPL-2.0+
 * @link      https://maxkostinevich.com
 * @copyright 2015 Max Kostinevich
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Email Generator
 * Plugin URI:        https://maxkostinevich.com/projects/woo-email-generator/
 * Description:       Email templates generator for WooCommerce
 * Version:           1.0.0
 * Author:            Max Kostinevich
 * Author URI:        https://maxkostinevich.com
 * Text Domain:       woo-email-generator
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-woo-email-generator.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Woo_Email_Generator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Woo_Email_Generator', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'Woo_Email_Generator', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-woo-email-generator-admin.php' );
	add_action( 'plugins_loaded', array( 'Woo_Email_Generator_Admin', 'get_instance' ) );

}
