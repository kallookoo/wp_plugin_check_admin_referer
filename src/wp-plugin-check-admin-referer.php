<?php
/**
 * Helper function for WordPress
 *
 * @author Sergio (kallookoo) <sergio@dsergio.com>
 * @license https://www.gnu.org/licenses/gpl-2.0.html GPL2 or later
 * @version 1.1.1
 * @package dsergio\com\WordPress\Plugin\Helpers
 */

if ( ! function_exists( 'wp_plugin_check_admin_referer' ) ) {
	/**
	 * Checks if using the plugin on register_activation_hook or register_deactivation_hook
	 * Use check_admin_referer to avoid security exploits
	 *
	 * @param  string $file Plugin filename.
	 *
	 * @return bool
	 */
	function wp_plugin_check_admin_referer( $file ) {
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			global $action;

			$current_action = (string) $action;

			if ( 'activate' === $current_action || 'deactivate' === $current_action ) {
				$plugin = ( ! empty( $_REQUEST['plugin'] ) ) ? (string) $_REQUEST['plugin'] : ''; // WPCS: CSRF ok, sanitization ok.
				return check_admin_referer( "{$current_action}-plugin_{$plugin}" );

			} elseif ( 'activate-selected' === $current_action || 'deactivate-selected' === $current_action ) {
				check_admin_referer( 'bulk-plugins' );

				$plugin  = plugin_basename( trim( $file ) );
				$plugins = ( ! empty( $_POST['checked'] ) ) ? (array) $_POST['checked'] : array(); // WPCS: sanitization ok.

				return in_array( $plugin, $plugins, true );
			}
		}

		return false;
	}
}
