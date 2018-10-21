<?php
/**
 * Helper function for WordPress
 *
 * @author Sergio (kallookoo) <sergio@dsergio.com>
 * @license https://www.gnu.org/licenses/gpl-2.0.html GPL2 or later
 * @version 1.2
 * @package dsergio\com\WordPress\Plugin\Helpers
 */

if ( ! function_exists( 'wp_plugin_check_admin_referer' ) ) {
	/**
	 * Checks if using the plugin on register_activation_hook, register_deactivation_hook and register_uninstall_hook
	 *
	 * Note: Use check_admin_referer to avoid security exploits
	 *
	 * @param  string $file Plugin filename.
	 *
	 * @return bool
	 */
	function wp_plugin_check_admin_referer( $file ) {
		if ( is_admin() ) {
			global $action;

			$actions = array(
				'single'   => array( 'activate', 'deactivate' ),
				'multiple' => array( 'activate-selected', 'deactivate-selected', 'delete-selected' ),
			);

			if ( ! empty( $action ) && is_string( $action ) ) {
				$cap = ( 'delete-selected' === $action ) ? 'delete_plugins' : 'activate_plugins';
				if ( current_user_can( $cap ) ) {
					if ( in_array( $action, $actions['single'], true ) ) {
						$plugin = ( ! empty( $_REQUEST['plugin'] ) ) ? (string) $_REQUEST['plugin'] : ''; // WPCS: CSRF ok, sanitization ok.
						return check_admin_referer( "{$action}-plugin_{$plugin}" );

					} elseif ( in_array( $action, $actions['multiple'], true ) ) {
						if ( false !== check_admin_referer( 'bulk-plugins' ) ) {
							$plugin = plugin_basename( trim( $file ) );

							/**
							 * Get correct global
							 *
							 * If delete-selected action use $_REQUEST otherwise, POST is used
							 *
							 * Note from /wp-admin/plugins.php for delete-selected action
							 * $_POST = from the plugin form;
							 * $_GET  = from the FTP details screen.
							 *
							 * @var array
							 */
							$global = ( 'delete-selected' === $action ) ? $_REQUEST : $_POST;

							/**
							 * Checked plugins
							 *
							 * @var array
							 */
							$plugins = isset( $global['checked'] ) ? (array) wp_unslash( $global['checked'] ) : array();

							return in_array( $plugin, $plugins, true );
						}
					}
				}
			}
		}

		return false;
	}
}
