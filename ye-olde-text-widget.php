<?php
/**
 * Plugin Name: Ye Olde Text Widget
 * Plugin URI: https://github.com/gitlost/ye-olde-text-widget
 * Description: Restores the Text Widget to as it was prior to WordPress 4.8.0.
 * Version: 1.0.0
 * Author: gitlost
 * Author URI: https://profiles.wordpress.org/gitlost
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ye-olde-text-widget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// These need to be synced with "readme.txt".
define( 'YOTW_PLUGIN_WP_AT_LEAST_VERSION', '4.8.0' );
define( 'YOTW_PLUGIN_WP_UP_TO_VERSION', '4.8.0' );

class Ye_Olde_Text_Widget {
	static function activation_hook() {
		if ( $msg = self::incompatible() ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( $msg );
		}
	}

	static function incompatible() {
		global $wp_version;
		$stripped_wp_version = substr( $wp_version, 0, strspn( $wp_version, '0123456789.' ) ); // Remove any trailing stuff.
		if ( preg_match( '/^[0-9]+\.[0-9]+$/', $stripped_wp_version ) ) {
			$stripped_wp_version .= '.0'; // Make WP version x.y.z compat.
		}
		if ( version_compare( $stripped_wp_version, YOTW_PLUGIN_WP_AT_LEAST_VERSION, '<' ) ) {
			return sprintf(
				/* translators: %1$s: lowest compatible WordPress version; %2$s: user's current WordPress version; %3$s: url to admin plugins page. */
				__( 'The plugin "Ye Olde Text Widget" cannot be activated as it requires WordPress %1$s to work and you have WordPress %2$s. <a href="%3$s">Return to Plugins page.</a>', 'ye-olde-text-widget' ),
				YOTW_PLUGIN_WP_AT_LEAST_VERSION, $wp_version, esc_url( self_admin_url( 'plugins.php' ) )
			);
		}
		if ( version_compare( $stripped_wp_version, YOTW_PLUGIN_WP_UP_TO_VERSION, '>' ) ) {
			return sprintf(
				/* translators: %1$s: highest compatible WordPress version; %2$s: user's current WordPress version; %3$s: url to admin plugins page. */
				__( 'The plugin "Ye Olde Text Widget" cannot be activated as it only works up to WordPress %1$s and you have WordPress %2$s. <a href="%3$s">Return to Plugins page.</a>', 'ye-olde-text-widget' ),
				YOTW_PLUGIN_WP_UP_TO_VERSION, $wp_version, esc_url( self_admin_url( 'plugins.php' ) )
			);
		}
		return null; // Compatible.
	}

	static function init() {
		// To be sure to be sure - these shouldn't get called anyway with 'filter' now restored to be boolean.
		remove_filter( 'widget_text_content', 'capital_P_dangit', 11 );
		remove_filter( 'widget_text_content', 'wptexturize'          );
		remove_filter( 'widget_text_content', 'convert_smilies',  20 );
		remove_filter( 'widget_text_content', 'wpautop'              );

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	static function admin_init() {
		global $wp_scripts;
		if ( $wp_scripts ) {
			$wp_scripts->remove( 'text-widgets' );
		}
	}

	static function widgets_init() {
		require dirname( __FILE__ ) . '/includes/class-yotw-widget-text.php';
		unregister_widget( 'WP_Widget_Text' );
		register_widget( 'YOTW_Widget_Text' );
	}
}

register_activation_hook( __FILE__, array( 'Ye_Olde_Text_Widget', 'activation_hook' ) );

if ( ! Ye_Olde_Text_Widget::incompatible() ) {
	add_action( 'widgets_init', array( 'Ye_Olde_Text_Widget', 'widgets_init' ) );
	add_action( 'init', array( 'Ye_Olde_Text_Widget', 'init' ) );
}
