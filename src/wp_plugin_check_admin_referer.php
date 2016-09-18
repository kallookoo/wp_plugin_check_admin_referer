<?php
/**
 * Checks if using the plugin on register_activation_hook or register_deactivation_hook
 * Use check_admin_referer to avoid security exploits
 *
 *
 * @author Sergio P.A. (23r9i0) <info@dsergio.com>
 * @license https://www.gnu.org/licenses/gpl-2.0.html GPL2 or later
 * @version 1.0
 *
 * @param  string  $file  The filename of the plugin including the path.
 *
 * @return bool|int       return check_admin_referer output on single plugin action or boolean.
 */
if ( ! function_exists( 'wp_plugin_check_admin_referer' ) ) {
	function wp_plugin_check_admin_referer( $file ) {
		if ( current_user_can( 'activate_plugins' ) ) {
			global $action;

			$current_action = (string) $action;

			if ( 'activate' === $current_action || 'deactivate' === $current_action ) {
				$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
				return check_admin_referer( "{$current_action}-plugin_{$plugin}" );

			} elseif ( 'activate-selected' === $current_action || 'deactivate-selected' === $current_action ) {
				check_admin_referer( 'bulk-plugins' );

				$plugin  = plugin_basename( trim( $file ) );
				$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

				return in_array( $plugin, $plugins );
			}
		}

		return false;
	}
}
