<?php
namespace CmsmastersFramework\Updater;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Updater module.
 *
 * Handles automatic updates for themes and plugins. Tied to WordPress update flow:
 * hooks into pre_set_site_transient_update_themes and pre_set_site_transient_update_plugins.
 * When WP runs the update check (Dashboard → Updates or cron), our filters run and we fetch
 * private data from the API in real time (no transient cache for private data: tokens and
 * API paths may change). Single request may still use in-memory cache so theme + plugin
 * checks do not call the API twice.
 *
 * On multisite: iterates all sites, skips sites without CMSMasters theme/token_data,
 * aggregates update data and merges into the transient that WP then uses.
 *
 * @since 1.0.12
 */
class Updater {

	/**
	 * Singleton instance.
	 *
	 * @var Updater|null
	 */
	private static $instance = null;

	/**
	 * Private update data (aggregated from all sites on multisite).
	 *
	 * @var array|null
	 */
	private $private_data = null;

	/**
	 * Public update data.
	 *
	 * @var array|null
	 */
	private $public_data = null;

	/**
	 * Get singleton instance.
	 *
	 * @since 1.0.12
	 *
	 * @return Updater
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Updater constructor.
	 *
	 * @since 1.0.12
	 */
	private function __construct() {
		// Theme updates
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_theme_updates' ) );

		// Plugin updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_plugin_updates' ) );
		// Ensure our plugins appear in no_update when transient is read (e.g. Plugins list page).
		add_filter( 'site_transient_update_plugins', array( $this, 'ensure_our_plugins_in_transient' ) );

		// Plugin information display
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 20, 3 );

		// Allow "Enable auto-updates" to work for our plugins (non–wordpress.org).
		add_filter( 'auto_update_plugin', array( $this, 'allow_auto_update_plugin' ), 10, 2 );

		// Force "Enable/Disable auto-updates" link HTML for our plugins (WP may leave it empty for non-.org plugins).
		add_filter( 'plugin_auto_update_setting_html', array( $this, 'plugin_auto_update_setting_html' ), 999, 3 );

		// Clear cache when plugins/themes are updated
		add_action( 'upgrader_process_complete', array( $this, 'clear_update_cache' ), 10, 2 );

		// Clear cache when license is activated/deactivated
		add_action( 'cmsmasters_remove_temp_data', array( $this, 'clear_all_cache' ) );

		// Before WP runs auto-updates, refresh our data and the transient so our plugins are included.
		add_action( 'wp_maybe_auto_update', array( $this, 'refresh_transient_before_auto_update' ), 5 );

		// Log auto-update result (success/errors) to Logger.
		add_action( 'upgrader_process_complete', array( $this, 'log_auto_update_result' ), 10, 2 );
	}

	/**
	 * Get map of our plugins (plugin_file => slug) from installed list only, without calling API.
	 * Used when transient is read (e.g. Plugins page) to avoid blocking the request.
	 * Identifies "our" plugins by folder prefix cmsmasters-.
	 *
	 * @since 1.0.13
	 *
	 * @param object|null $transient update_plugins transient (optional). If provided, used to get installed list.
	 *
	 * @return array Map of plugin_file => slug.
	 */
	private function get_our_plugins_map_without_api( $transient = null ) {
		$installed = array();
		if ( $transient && isset( $transient->checked ) && is_array( $transient->checked ) ) {
			$installed = array_keys( $transient->checked );
		} else {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$installed = array_keys( get_plugins() );
		}

		$map = array();
		foreach ( $installed as $plugin_file ) {
			$parts = explode( '/', str_replace( '\\', '/', $plugin_file ) );
			$slug = isset( $parts[0] ) ? $parts[0] : '';
			if ( $slug && strpos( $slug, 'cmsmasters-' ) === 0 ) {
				$map[ $plugin_file ] = $slug;
			}
		}
		return $map;
	}

	/**
	 * Get map of our updatable plugins (plugin_file => slug) from API and GitHub.
	 * Only includes plugins that are installed. Uses private_data and public_data (fetched on demand).
	 *
	 * @since 1.0.12
	 *
	 * @param object|null $transient update_plugins transient (optional). If provided, used to get installed list.
	 *
	 * @return array Map of plugin_file => slug.
	 */
	private function get_our_plugins_map( $transient = null ) {
		$installed = array();
		if ( $transient && isset( $transient->checked ) && is_array( $transient->checked ) ) {
			$installed = array_keys( $transient->checked );
		} else {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$installed = array_keys( get_plugins() );
		}

		$our_slugs = array();
		$private_data = $this->get_private_update_data();
		if ( ! is_wp_error( $private_data ) && ! empty( $private_data['plugins'] ) ) {
			$our_slugs = array_merge( $our_slugs, array_keys( $private_data['plugins'] ) );
		}
		$public_data = $this->get_public_update_data();
		if ( ! is_wp_error( $public_data ) && ! empty( $public_data['plugins'] ) ) {
			$our_slugs = array_merge( $our_slugs, array_keys( $public_data['plugins'] ) );
		}
		$our_slugs = array_unique( $our_slugs );

		$map = array();
		foreach ( $installed as $plugin_file ) {
			$parts = explode( '/', str_replace( '\\', '/', $plugin_file ) );
			$slug = isset( $parts[0] ) ? $parts[0] : '';
			if ( $slug && in_array( $slug, $our_slugs, true ) ) {
				$map[ $plugin_file ] = $slug;
			}
		}
		return $map;
	}

	/**
	 * Respect "Enable auto-updates" for CMSMasters plugins.
	 *
	 * Non–wordpress.org plugins are excluded by default. For our plugins we allow
	 * auto-update only when the user has enabled it (plugin is in auto_update_plugins).
	 *
	 * @since 1.0.12
	 *
	 * @param bool|null $update Whether to update. Null = use default (often false for non-wp.org).
	 * @param object    $item  Update offer (has ->plugin with plugin file path).
	 *
	 * @return bool|null
	 */
	public function allow_auto_update_plugin( $update, $item ) {
		if ( empty( $item->plugin ) || ! is_string( $item->plugin ) ) {
			return $update;
		}

		$our_plugin_files = array_keys( $this->get_our_plugins_map_without_api( null ) );

		if ( ! in_array( $item->plugin, $our_plugin_files, true ) ) {
			return $update;
		}

		// Only allow auto-update when the user has enabled it in the Plugins screen.
		$auto_update_plugins = (array) get_site_option( 'auto_update_plugins', array() );

		return in_array( $item->plugin, $auto_update_plugins, true );
	}

	/**
	 * Output "Enable/Disable auto-updates" link for our plugins when WP leaves it empty.
	 * WP only shows the link when the plugin is in update_plugins transient (response or no_update).
	 * We use the same HTML structure as core so the existing JS (wp_nonce_url with 'updates') works.
	 *
	 * @since 1.0.12
	 *
	 * @param string $html        Current HTML for the auto-update column.
	 * @param string $plugin_file Plugin file path (e.g. cmsmasters-framework/cmsmasters-framework.php).
	 * @param array  $plugin_data Plugin data from get_plugin_data().
	 *
	 * @return string
	 */
	public function plugin_auto_update_setting_html( $html, $plugin_file, $plugin_data ) {
		$our_plugin_files = array_keys( $this->get_our_plugins_map_without_api( null ) );

		// Normalize path for Windows (backslashes).
		$plugin_file_norm = str_replace( '\\', '/', $plugin_file );
		if ( ! in_array( $plugin_file, $our_plugin_files, true ) && ! in_array( $plugin_file_norm, $our_plugin_files, true ) ) {
			return $html;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return $html;
		}

		$auto_updates   = (array) get_site_option( 'auto_update_plugins', array() );
		$is_enabled     = in_array( $plugin_file, $auto_updates, true );
		$status        = isset( $plugin_data['status'] ) ? $plugin_data['status'] : '';
		$page          = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;

		if ( $is_enabled ) {
			$text   = __( 'Disable auto-updates', 'cmsmasters-framework' );
			$action = 'disable';
		} else {
			$text   = __( 'Enable auto-updates', 'cmsmasters-framework' );
			$action = 'enable';
		}

		$query_args = array(
			'action'        => $action . '-auto-update',
			'plugin'        => $plugin_file,
			'paged'         => $page,
			'plugin_status' => $status,
		);
		$url = add_query_arg( $query_args, 'plugins.php' );
		$url = wp_nonce_url( $url, 'updates' );

		// Same structure as WP_Plugins_List_Table so the core JS (toggle-auto-update, data-wp-action) works.
		$out = sprintf(
			'<a href="%s" class="toggle-auto-update aria-button-if-js" data-wp-action="%s">',
			esc_url( $url ),
			esc_attr( $action )
		);
		$out .= '<span class="dashicons dashicons-update spin hidden" aria-hidden="true"></span>';
		$out .= '<span class="label">' . esc_html( $text ) . '</span>';
		$out .= '</a>';

		return $out;
	}

	/**
	 * Check for theme updates.
	 *
	 * On multisite, checks all sites and aggregates theme updates.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient Update transient object.
	 *
	 * @return object Modified transient object.
	 */
	public function check_theme_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$update_data = $this->get_private_update_data();

		if ( is_wp_error( $update_data ) ) {
			return $transient;
		}
		if ( empty( $update_data['themes'] ) ) {
			return $transient;
		}

		// Process all themes from aggregated data
		foreach ( $update_data['themes'] as $theme_name => $theme_data ) {
			if ( empty( $theme_data['version'] ) || empty( $theme_data['package'] ) ) {
				continue;
			}
			// Ensure package URL matches this theme slug (avoid applying wrong theme zip).
			if ( strpos( $theme_data['package'], '/' . $theme_name . '.zip' ) === false ) {
				continue;
			}

			// Check if theme is installed
			if ( ! isset( $transient->checked[ $theme_name ] ) ) {
				continue;
			}

			$current_version = $transient->checked[ $theme_name ];

			// Check if new version is available
			if ( version_compare( $theme_data['version'], $current_version, '>' ) ) {
				$transient->response[ $theme_name ] = array(
					'theme'       => $theme_name,
					'new_version' => $theme_data['version'],
					'url'         => $theme_data['package'],
					'package'     => $theme_data['package'],
				);
			}
		}

		return $transient;
	}

	/**
	 * Check for plugin updates.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient Update transient object.
	 *
	 * @return object Modified transient object.
	 */
	public function check_plugin_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Check private plugins (require license)
		$transient = $this->check_private_plugins( $transient );

		// Check public plugins (no license required)
		$transient = $this->check_public_plugins( $transient );

		// Required for "Enable/Disable auto-updates" links to show (plugins not from wordpress.org).
		$transient = $this->add_plugins_to_no_update( $transient );

		return $transient;
	}

	/**
	 * Add plugins to no_update so "Enable/Disable auto-updates" links appear.
	 *
	 * WordPress shows these links only when the plugin is in response or no_update.
	 * Plugins not from wordpress.org must be in no_update when no update is available.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient   Update transient object.
	 * @param bool   $skip_api    When true, do not call API (used when transient is read to avoid blocking).
	 *
	 * @return object Modified transient object.
	 */
	private function add_plugins_to_no_update( $transient, $skip_api = false ) {
		$our_plugins_map = $skip_api
			? $this->get_our_plugins_map_without_api( $transient )
			: $this->get_our_plugins_map( $transient );

		if ( empty( $our_plugins_map ) ) {
			return $transient;
		}

		if ( ! isset( $transient->no_update ) ) {
			$transient->no_update = array();
		}

		foreach ( $our_plugins_map as $plugin_file => $plugin_slug ) {
			if ( ! isset( $transient->checked[ $plugin_file ] ) ) {
				continue;
			}
			// Add even when in response so "Enable/Disable auto-updates" always shows (WP may not show it otherwise).
			$current_version = isset( $transient->response[ $plugin_file ] ) && isset( $transient->response[ $plugin_file ]->new_version )
				? $transient->response[ $plugin_file ]->new_version
				: $transient->checked[ $plugin_file ];
			// package empty by design: no update available, nothing to download.
			$transient->no_update[ $plugin_file ] = (object) array(
				'id'            => $plugin_file,
				'slug'          => $plugin_slug,
				'plugin'        => $plugin_file,
				'new_version'   => $current_version,
				'url'           => 'https://cmsmasters.net/',
				'package'       => '',
				'icons'         => array(),
				'banners'       => array(),
				'banners_rtl'   => array(),
				'tested'        => '',
				'requires_php'  => '7.4',
				'compatibility' => new \stdClass(),
			);
		}

		return $transient;
	}

	/**
	 * When transient is read (e.g. on Plugins list page), ensure our plugins are in no_update.
	 * Fixes missing "Enable auto-updates" link when transient was cached without no_update.
	 *
	 * @since 1.0.12
	 *
	 * @param object|null $transient update_plugins transient.
	 *
	 * @return object|null
	 */
	public function ensure_our_plugins_in_transient( $transient ) {
		if ( ! $transient || ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		return $this->add_plugins_to_no_update( $transient, true );
	}

	/**
	 * Check for private plugin updates.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient Update transient object.
	 *
	 * @return object Modified transient object.
	 */
	private function check_private_plugins( $transient ) {
		$update_data = $this->get_private_update_data();

		if ( is_wp_error( $update_data ) ) {
			return $transient;
		}
		if ( empty( $update_data['plugins'] ) ) {
			return $transient;
		}

		foreach ( $update_data['plugins'] as $plugin_slug => $plugin_info ) {
			$plugin_file = isset( $plugin_info['plugin_file'] ) ? $plugin_info['plugin_file'] : $plugin_slug . '/' . $plugin_slug . '.php';
			$transient = $this->maybe_add_plugin_update( $transient, $plugin_slug, $plugin_file, $update_data['plugins'] );
		}

		return $transient;
	}

	/**
	 * Check for public plugin updates.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient Update transient object.
	 *
	 * @return object Modified transient object.
	 */
	private function check_public_plugins( $transient ) {
		$update_data = $this->get_public_update_data();

		if ( is_wp_error( $update_data ) ) {
			return $transient;
		}
		if ( empty( $update_data['plugins'] ) ) {
			return $transient;
		}

		foreach ( $update_data['plugins'] as $plugin_slug => $plugin_info ) {
			$plugin_file = isset( $plugin_info['plugin_file'] ) ? $plugin_info['plugin_file'] : $plugin_slug . '/' . $plugin_slug . '.php';
			$transient = $this->maybe_add_plugin_update( $transient, $plugin_slug, $plugin_file, $update_data['plugins'] );
		}

		return $transient;
	}

	/**
	 * Resolve plugin file path to the actual key in transient->checked.
	 * Handles Windows backslashes and API path differing from installed path.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient   Update transient object.
	 * @param string $plugin_file Plugin file path from API or default.
	 * @param string $plugin_slug Plugin slug (first path segment).
	 *
	 * @return string|null The key in $transient->checked or null if not installed.
	 */
	private function resolve_plugin_file_in_checked( $transient, $plugin_file, $plugin_slug ) {
		if ( empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
			return null;
		}
		if ( isset( $transient->checked[ $plugin_file ] ) ) {
			return $plugin_file;
		}
		$plugin_file_norm = str_replace( '\\', '/', $plugin_file );
		if ( isset( $transient->checked[ $plugin_file_norm ] ) ) {
			return $plugin_file_norm;
		}
		foreach ( array_keys( $transient->checked ) as $key ) {
			$parts = explode( '/', str_replace( '\\', '/', $key ) );
			if ( isset( $parts[0] ) && $parts[0] === $plugin_slug ) {
				return $key;
			}
		}
		return null;
	}

	/**
	 * Maybe add plugin update to transient.
	 *
	 * @since 1.0.12
	 *
	 * @param object $transient    Update transient object.
	 * @param string $plugin_slug  Plugin slug.
	 * @param string $plugin_file  Plugin file path.
	 * @param array  $plugins_data Plugins data from API.
	 *
	 * @return object Modified transient object.
	 */
	private function maybe_add_plugin_update( $transient, $plugin_slug, $plugin_file, $plugins_data ) {
		// Resolve to the actual key in checked (handles Windows backslashes or different path from API).
		$checked_key = $this->resolve_plugin_file_in_checked( $transient, $plugin_file, $plugin_slug );
		if ( ! $checked_key ) {
			return $transient;
		}

		// Skip if plugin not in API response
		if ( ! isset( $plugins_data[ $plugin_slug ] ) ) {
			return $transient;
		}

		$current_version = $transient->checked[ $checked_key ];
		$plugin_info = $plugins_data[ $plugin_slug ];

		// Skip if no version info
		if ( empty( $plugin_info['version'] ) ) {
			return $transient;
		}

		// Get package URL
		$package = '';
		if ( ! empty( $plugin_info['package'] ) ) {
			$package = $plugin_info['package'];
		} elseif ( ! empty( $plugin_info['source'] ) ) {
			$package = $plugin_info['source'];
		}

		// Check if new version is available
		if ( version_compare( $plugin_info['version'], $current_version, '>' ) ) {
			$transient->response[ $checked_key ] = (object) array(
				'slug'         => $plugin_slug,
				'plugin'       => $checked_key,
				'new_version'  => $plugin_info['version'],
				'url'          => isset( $plugin_info['homepage'] ) ? $plugin_info['homepage'] : 'https://cmsmasters.net/',
				'package'      => $package,
				'icons'        => array(),
				'banners'      => array(),
				'tested'       => '',
				'requires_php' => '7.4',
			);
		}

		return $transient;
	}

	/**
	 * Get private update data from API.
	 *
	 * On multisite: iterates through all sites, checks each site's license,
	 * and aggregates update data for all themes and plugins.
	 *
	 * @since 1.0.12
	 *
	 * @return array|WP_Error Update data or error.
	 */
	private function get_private_update_data() {
		// In-request memory cache only: same HTTP request may call check_theme_updates and check_plugin_updates, avoid duplicate API calls.
		if ( null !== $this->private_data ) {
			return $this->private_data;
		}

		Logger::info( 'Auto-update: fetching private update data.', array( 'multisite' => is_multisite() ) );
		// Multisite: require framework to be network-activated for theme/plugin auto-updates.
		if ( is_multisite() && defined( 'CMSMASTERS_FRAMEWORK__FILE__' ) && ! is_plugin_active_for_network( plugin_basename( CMSMASTERS_FRAMEWORK__FILE__ ) ) ) {
			Logger::info( 'Auto-update: Multisite detected but CMSMasters Framework is not network-activated. Skipping private update data.' );
			$this->private_data = array(
				'themes'  => array(),
				'plugins' => array(),
			);

			return $this->private_data;
		}
		// Fetch data
		if ( is_multisite() ) {
			$data = $this->get_multisite_private_data();
		} else {
			$data = $this->get_single_site_private_data();
		}

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'empty_data', 'Empty data from API' );
		}

		$this->private_data = $data;

		return $this->private_data;
	}


	/**
	 * Get private data for single site installation.
	 *
	 * Uses existing API routes:
	 * - get-theme-data for theme updates
	 * - get-plugins-list for plugin updates (without demo parameter)
	 *
	 * @since 1.0.12
	 *
	 * @return array|WP_Error Update data or error.
	 */
	private function get_single_site_private_data() {
		$theme_slug = wp_get_theme()->get_stylesheet();
		$token_data = $this->get_site_token_data();
		if ( empty( $token_data ) ) {
			return array(
				'themes'  => array(),
				'plugins' => array(),
			);
		}

		$token = $this->ensure_valid_token( $token_data );
		if ( empty( $token ) ) {
			return array(
				'themes'  => array(),
				'plugins' => array(),
			);
		}

		$result = array(
			'themes'  => array(),
			'plugins' => array(),
		);

		Logger::info( 'Auto-update: API request get-theme-data (single site).' );
		$theme_data = API_Requests::post_request( 'get-theme-data', array() );

		if ( ! is_wp_error( $theme_data ) && ! empty( $theme_data['theme_version'] ) ) {
			// Use template (parent) slug so update is for the correct theme and package matches.
			$theme_name = wp_get_theme()->get_template();
			Logger::info( 'Auto-update: get-theme-data ok.', array( 'theme_version' => $theme_data['theme_version'], 'theme_name' => $theme_name ) );
			if ( ! empty( $theme_name ) ) {
				$package_url = $theme_data['theme_path'];
				// Only use the response if the package URL matches this theme (avoids wrong theme zip when constant points to another product).
				if ( ! empty( $package_url ) && strpos( $package_url, '/' . $theme_name . '.zip' ) !== false ) {
					$result['themes'][ $theme_name ] = array(
						'name'    => $theme_name,
						'version' => $theme_data['theme_version'],
						'package' => $package_url,
					);
				} else {
					Logger::info( 'Auto-update: skipped theme response – package URL does not match theme slug.', array( 'theme_name' => $theme_name, 'package' => $package_url ) );
				}
			}
		}

		Logger::info( 'Auto-update: API request get-plugins-list (single site).' );
		$plugins_data = API_Requests::post_request( 'get-plugins-list', array() );

		if ( ! is_wp_error( $plugins_data ) && is_array( $plugins_data ) ) {
			Logger::info( 'Auto-update: get-plugins-list ok.', array( 'count' => count( $plugins_data ) ) );
			$skipped = array();
			foreach ( $plugins_data as $plugin_slug => $plugin_info ) {
				if ( empty( $plugin_info['version'] ) ) {
					$skipped[ $plugin_slug ] = 'no version';
					continue;
				}
				if ( empty( $plugin_info['source'] ) && empty( $plugin_info['package'] ) ) {
					$skipped[ $plugin_slug ] = 'no source/package';
					continue;
				}
				$pkg = ! empty( $plugin_info['source'] ) ? $plugin_info['source'] : $plugin_info['package'];
				if ( false === strpos( $pkg, 'cmsmasters-api-files' ) ) {
					$skipped[ $plugin_slug ] = 'external source (not our API archive)';
					continue;
				}
				if ( empty( $plugin_info['cmsmasters_updater_allow'] ) ) {
					$skipped[ $plugin_slug ] = 'updater not allowed (cmsmasters_updater_allow)';
					continue;
				}
				$result['plugins'][ $plugin_slug ] = array(
					'name'        => isset( $plugin_info['name'] ) ? $plugin_info['name'] : $plugin_slug,
					'slug'        => $plugin_slug,
					'version'     => $plugin_info['version'],
					'package'     => $pkg,
					'plugin_file' => isset( $plugin_info['plugin_file'] ) ? $plugin_info['plugin_file'] : $plugin_slug . '/' . $plugin_slug . '.php',
				);
			}
		}

		return $result;
	}

	/**
	 * Get private data for multisite installation.
	 *
	 * Iterates through all sites in the network, checks token data
	 * in each site's uploads folder, and aggregates update data.
	 *
	 * @since 1.0.12
	 *
	 * @return array Aggregated update data.
	 */
	private function get_multisite_private_data() {
		$aggregated_data = array(
			'themes'  => array(),
			'plugins' => array(),
		);

		$sites = get_sites( array(
			'number' => 0, // Get all sites
			'fields' => 'ids',
		) );

		if ( empty( $sites ) ) {
			return $aggregated_data;
		}

		foreach ( $sites as $site_id ) {
			$site_data = $this->get_site_private_data( $site_id );

			if ( is_wp_error( $site_data ) || empty( $site_data ) ) {
				continue;
			}

			if ( ! empty( $site_data['themes'] ) ) {
				foreach ( $site_data['themes'] as $theme_name => $theme_data ) {
					if (
						! isset( $aggregated_data['themes'][ $theme_name ] ) ||
						version_compare( $theme_data['version'], $aggregated_data['themes'][ $theme_name ]['version'], '>' )
					) {
						$aggregated_data['themes'][ $theme_name ] = $theme_data;
					}
				}
			}

			if ( ! empty( $site_data['plugins'] ) ) {
				foreach ( $site_data['plugins'] as $plugin_slug => $plugin_data ) {
					if (
						! isset( $aggregated_data['plugins'][ $plugin_slug ] ) ||
						version_compare( $plugin_data['version'], $aggregated_data['plugins'][ $plugin_slug ]['version'], '>' )
					) {
						$aggregated_data['plugins'][ $plugin_slug ] = $plugin_data;
					}
				}
			}
		}

		return $aggregated_data;
	}

	/**
	 * Get private data for a specific site in multisite.
	 *
	 * Switches to the site, checks if it has CMSMasters theme with license,
	 * regenerates token if needed, and fetches update data using existing routes.
	 *
	 * @since 1.0.12
	 *
	 * @param int $site_id Site ID.
	 *
	 * @return array|WP_Error Site update data or error.
	 */
	private function get_site_private_data( $site_id ) {
		switch_to_blog( $site_id );

		$site_url   = home_url();
		$theme      = wp_get_theme();
		$theme_slug = $theme->get_stylesheet();
		// Skip if active theme is not ours. Check DB option cmsmasters_{stylesheet}_version (set by theme on admin/upgrade); theme's functions.php is not loaded in this context.
		$version_option_name = 'cmsmasters_' . $theme_slug . '_version';
		$theme_version_opt   = get_option( $version_option_name, '' );
		if ( '' === $theme_version_opt || false === $theme_version_opt ) {
			Logger::info( 'Auto-update: subsite ' . $site_id . ' skipped (not our theme – option ' . $version_option_name . ' not set).' );
			restore_current_blog();
			return array(
				'themes'  => array(),
				'plugins' => array(),
			);
		}

		$result = array(
			'themes'  => array(),
			'plugins' => array(),
		);

		// Check if this site has token_data (license)
		$token_data = $this->get_site_token_data();
		if ( empty( $token_data ) ) {
			Logger::info( 'Auto-update: subsite skipped (no token_data).', array( 'site_id' => $site_id ) );
			restore_current_blog();
			return $result;
		}

		$token = $this->ensure_valid_token_for_site( $token_data );
		if ( empty( $token ) ) {
			Logger::info( 'Auto-update: subsite skipped (no valid token).', array( 'site_id' => $site_id ) );
			restore_current_blog();
			return $result;
		}

		$product_key = $this->get_site_product_key( $token_data );
		if ( empty( $product_key ) ) {
			Logger::info( 'Auto-update: subsite skipped (no product_key).', array( 'site_id' => $site_id ) );
			restore_current_blog();
			return $result;
		}

		$theme_name = $theme->get_template();

		$theme_response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'get-theme-data',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
				'body' => array(
					'product_key' => $product_key,
				),
				'timeout' => 60,
			)
		);

		$theme_code = ! is_wp_error( $theme_response ) ? wp_remote_retrieve_response_code( $theme_response ) : 0;
		if ( in_array( $theme_code, array( 401, 403 ), true ) ) {
			$prefix = $this->get_options_prefix();
			delete_transient( $prefix . 'token_regeneration_status' );
			update_option( $prefix . 'token_status', 'invalid', false );
			update_option( $prefix . 'token', '', false );
			$token = $this->ensure_valid_token_for_site( $token_data );
			if ( ! empty( $token ) ) {
				$theme_response = wp_remote_post(
					CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'get-theme-data',
					array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $token,
						),
						'body' => array(
							'product_key' => $product_key,
						),
						'timeout' => 60,
					)
				);
				$theme_code = ! is_wp_error( $theme_response ) ? wp_remote_retrieve_response_code( $theme_response ) : 0;
			}
		}

		if ( ! is_wp_error( $theme_response ) && 200 === $theme_code ) {
			$prefix = $this->get_options_prefix();
			update_option( $prefix . 'token_status', 'valid', false );
			$theme_body = json_decode( wp_remote_retrieve_body( $theme_response ), true );

			if ( ! empty( $theme_body['data']['theme_version'] ) && ! empty( $theme_body['data']['theme_path'] ) ) {
				$package_url = $theme_body['data']['theme_path'];
				// Only use the response if the package URL matches this theme (avoids wrong theme zip when CMSMASTERS_THEME_PRODUCT_KEY points to another product).
				if ( strpos( $package_url, '/' . $theme_name . '.zip' ) !== false ) {
					$result['themes'][ $theme_name ] = array(
						'name'    => $theme_name,
						'version' => $theme_body['data']['theme_version'],
						'package' => $package_url,
					);
				} else {
					Logger::info( 'Auto-update: skipped theme response – package URL does not match theme slug.', array( 'theme_name' => $theme_name, 'package' => $package_url ) );
				}
			}
		}

		$plugins_response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'get-plugins-list',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
				'body' => array(
					'product_key' => $product_key,
				),
				'timeout' => 60,
			)
		);

		$plugins_code = ! is_wp_error( $plugins_response ) ? wp_remote_retrieve_response_code( $plugins_response ) : 0;
		if ( in_array( $plugins_code, array( 401, 403 ), true ) ) {
			$prefix = $this->get_options_prefix();
			delete_transient( $prefix . 'token_regeneration_status' );
			update_option( $prefix . 'token_status', 'invalid', false );
			update_option( $prefix . 'token', '', false );
			$token = $this->ensure_valid_token_for_site( $token_data );
			if ( ! empty( $token ) ) {
				$plugins_response = wp_remote_post(
					CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'get-plugins-list',
					array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $token,
						),
						'body' => array(
							'product_key' => $product_key,
						),
						'timeout' => 60,
					)
				);
				$plugins_code = ! is_wp_error( $plugins_response ) ? wp_remote_retrieve_response_code( $plugins_response ) : 0;
			}
		}

		restore_current_blog();

		if ( ! is_wp_error( $plugins_response ) && 200 === $plugins_code ) {
			$prefix = $this->get_options_prefix();
			if ( 'valid' !== get_option( $prefix . 'token_status' ) ) {
				update_option( $prefix . 'token_status', 'valid', false );
			}
			$plugins_body = json_decode( wp_remote_retrieve_body( $plugins_response ), true );

			if ( ! empty( $plugins_body['data'] ) && is_array( $plugins_body['data'] ) ) {
				$skipped = array();
				foreach ( $plugins_body['data'] as $plugin_slug => $plugin_info ) {
					if ( empty( $plugin_info['version'] ) ) {
						$skipped[ $plugin_slug ] = 'no version';
						continue;
					}
					if ( empty( $plugin_info['source'] ) && empty( $plugin_info['package'] ) ) {
						$skipped[ $plugin_slug ] = 'no source/package';
						continue;
					}
					$pkg = ! empty( $plugin_info['source'] ) ? $plugin_info['source'] : $plugin_info['package'];
					if ( false === strpos( $pkg, 'cmsmasters-api-files' ) ) {
						$skipped[ $plugin_slug ] = 'external source (not our API archive)';
						continue;
					}
					if ( empty( $plugin_info['cmsmasters_updater_allow'] ) ) {
						continue;
					}
					$result['plugins'][ $plugin_slug ] = array(
						'name'        => isset( $plugin_info['name'] ) ? $plugin_info['name'] : $plugin_slug,
						'slug'        => $plugin_slug,
						'version'     => $plugin_info['version'],
						'package'     => $pkg,
						'plugin_file' => isset( $plugin_info['plugin_file'] ) ? $plugin_info['plugin_file'] : $plugin_slug . '/' . $plugin_slug . '.php',
					);
				}
			}
		}

		if ( ! empty( $result['themes'] ) || ! empty( $result['plugins'] ) ) {
			Logger::info( 'Auto-update: subsite ' . $site_id . ' got update data.', array(
				'themes'   => array_keys( $result['themes'] ),
				'plugins'  => array_keys( $result['plugins'] ),
				'theme_versions' => array_combine( array_keys( $result['themes'] ), array_column( $result['themes'], 'version' ) ),
			) );
		}
		return $result;
	}

	/**
	 * Get token data for current site.
	 *
	 * Checks both wp_options and uploads folder.
	 * This also serves as a check for CMSMasters theme - if no token_data,
	 * it's not our theme or license was never activated.
	 *
	 * @since 1.0.12
	 *
	 * @return array Token data or empty array.
	 */
	private function get_site_token_data() {
		$prefix = $this->get_options_prefix();

		// First check wp_options
		$token_data = get_option( $prefix . 'token_data', array() );

		if ( $this->is_valid_token_data( $token_data ) ) {
			return $token_data;
		}

		$upload_dir = wp_upload_dir();
		$theme = wp_get_theme();
		$theme_slug = $theme->get_stylesheet();
		$token_file = $upload_dir['basedir'] . '/cmsmasters/' . $theme_slug . '/token-data/token-data.json';

		if ( empty( $theme_slug ) || ! file_exists( $token_file ) ) {
			return array();
		}

		$file_contents = file_get_contents( $token_file );

		if ( empty( $file_contents ) ) {
			return array();
		}

		$token_data = json_decode( $file_contents, true );

		if ( ! $this->is_valid_token_data( $token_data ) ) {
			return array();
		}

		// Save to options for faster access next time
		update_option( $prefix . 'token_data', $token_data, false );

		return $token_data;
	}

	/**
	 * Check if token data is valid.
	 *
	 * @since 1.0.12
	 *
	 * @param mixed $token_data Token data to check.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	private function is_valid_token_data( $token_data ) {
		return is_array( $token_data ) &&
			isset( $token_data['user'] ) &&
			( ! empty( $token_data['purchase_code'] ) || ! empty( $token_data['envato_elements_token'] ) );
	}

	/**
	 * Ensure we have a valid token for current site (single site mode).
	 *
	 * Uses API_Requests which handles token regeneration automatically.
	 *
	 * @since 1.0.12
	 *
	 * @param array $token_data Token data.
	 *
	 * @return string|false Token or false.
	 */
	private function ensure_valid_token( $token_data ) {
		$prefix = $this->get_options_prefix();

		// Check token status
		$token_status = get_option( $prefix . 'token_status' );

		if ( 'valid' === $token_status ) {
			return get_option( $prefix . 'token' );
		}

		if ( API_Requests::regenerate_token( false ) ) {
			return get_option( $prefix . 'token' );
		}

		return false;
	}

	/**
	 * Ensure we have a valid token for a specific site in multisite.
	 *
	 * Called when already switched to the target blog.
	 *
	 * @since 1.0.12
	 *
	 * @param array $token_data Token data.
	 *
	 * @return string|false Token or false.
	 */
	private function ensure_valid_token_for_site( $token_data ) {
		$prefix = $this->get_options_prefix();

		// Check if we have a valid token
		$token_status = get_option( $prefix . 'token_status' );
		$token = get_option( $prefix . 'token' );

		if ( 'valid' === $token_status && ! empty( $token ) ) {
			return $token;
		}

		if ( ! empty( $token ) && 'invalid' === $token_status ) {
			return $token;
		}

		$regen_status = get_transient( $prefix . 'token_regeneration_status' );

		if ( 'regenerated' === $regen_status ) {
			if ( ! empty( $token_data ) && is_array( $token_data ) ) {
				delete_transient( $prefix . 'token_regeneration_status' );
			} else {
				return false;
			}
		}

		$token_data['source_code'] = ! empty( $token_data['source_code'] ) ? $token_data['source_code'] : 'purchase-code';

		$product_key = $this->get_site_product_key( $token_data );

		if ( ! empty( $product_key ) ) {
			$token_data['product_key'] = $product_key;
		}

		$response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'regenerate-token',
			array(
				'body'    => $token_data,
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			set_transient( $prefix . 'token_regeneration_status', 'regenerated', 1200 );

			Logger::error( 'Token regeneration failed: ' . $response->get_error_message() );
			update_option( $prefix . 'token_status', 'invalid', false );

			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$response_body_raw = wp_remote_retrieve_body( $response );

		if ( 503 === $code ) {
			// Marketplace temporarily unavailable — keep all data, shorter retry lock.
			set_transient( $prefix . 'token_regeneration_status', 'regenerated', 300 );

			return ! empty( $token ) ? $token : false;
		}

		set_transient( $prefix . 'token_regeneration_status', 'regenerated', 1200 );

		if ( 200 !== $code ) {
			$error_body = json_decode( $response_body_raw, true );

			if ( isset( $error_body['data']['code'] ) && 'ee_theme_unavailable' === $error_body['data']['code'] ) {
				Logger::error( 'Multisite regeneration: theme removed from Envato Elements. Token deleted.' );

				update_option( $prefix . 'ee_theme_unavailable', true, false );
				delete_option( $prefix . 'token' );
			}

			update_option( $prefix . 'token_status', 'invalid', false );

			return false;
		}

		$response_body = json_decode( $response_body_raw, true );

		if ( empty( $response_body['data']['token'] ) ) {
			update_option( $prefix . 'token_status', 'invalid', false );

			return false;
		}

		// Save new token
		$new_token = $response_body['data']['token'];
		update_option( $prefix . 'token', $new_token, false );
		update_option( $prefix . 'token_status', 'valid', false );

		return $new_token;
	}

	/**
	 * Get product key for current site.
	 *
	 * @since 1.0.12
	 *
	 * @param array $token_data Token data (may contain product info).
	 *
	 * @return string Product key or empty string.
	 */
	private function get_site_product_key( $token_data ) {
		// First try the constant (if theme defines it)
		if ( defined( 'CMSMASTERS_THEME_PRODUCT_KEY' ) ) {
			return CMSMASTERS_THEME_PRODUCT_KEY;
		}

		// Try to get from active theme: use parent (template) for updates, not child stylesheet.
		$theme = wp_get_theme();
		$theme_slug = $theme->get_template();

		// Convert theme slug to product key (hex encoded)
		if ( ! empty( $theme_slug ) ) {
			return bin2hex( $theme_slug );
		}

		return '';
	}

	/**
	 * Get public update data from GitHub.
	 *
	 * Fetches public plugins list from GitHub repository.
	 * Same source as theme's plugin-activator uses.
	 *
	 * @since 1.0.12
	 *
	 * @return array|WP_Error Update data or error.
	 */
	private function get_public_update_data() {
		if ( null !== $this->public_data ) {
			return $this->public_data;
		}

		Logger::info( 'Auto-update: GitHub request (public-plugins list).' );
		// Fetch from GitHub (same URL as theme's plugin-activator)
		$response = wp_remote_get(
			'https://github.com/cmsmasters/public-plugins/releases/download/list/public-plugins.json',
			array(
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			Logger::error( 'Public Update Data Error: ' . $response->get_error_message() );

			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			return new \WP_Error( 'github_error', 'Failed to fetch public plugins from GitHub' );
		}

		$plugins_data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $plugins_data ) || ! is_array( $plugins_data ) ) {
			return new \WP_Error( 'empty_data', 'Empty data from GitHub' );
		}

		// Only include plugins that allow updater (cmsmasters_updater_allow) and add plugin_file.
		$filtered = array();
		foreach ( $plugins_data as $plugin_slug => $plugin_info ) {
			if ( ! is_array( $plugin_info ) ) {
				continue;
			}
			if ( empty( $plugin_info['cmsmasters_updater_allow'] ) ) {
				continue;
			}
			$filtered[ $plugin_slug ] = $plugin_info;
			if ( ! isset( $filtered[ $plugin_slug ]['plugin_file'] ) ) {
				$filtered[ $plugin_slug ]['plugin_file'] = $plugin_slug . '/' . $plugin_slug . '.php';
			}
		}

		Logger::info( 'Auto-update: GitHub response ok.', array( 'plugins_count' => count( $filtered ) ) );

		$data = array(
			'plugins' => $filtered,
		);

		$this->public_data = $data;

		return $this->public_data;
	}

	/**
	 * Display plugin information in the WordPress plugins API.
	 *
	 * @since 1.0.12
	 *
	 * @param false|object|array $result The result object or array.
	 * @param string             $action The type of information being requested.
	 * @param object             $args   Plugin API arguments.
	 *
	 * @return false|object Plugin information or false.
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( ! isset( $args->slug ) ) {
			return $result;
		}

		$plugin_slug = $args->slug;
		$plugin_info = null;

		$update_data = $this->get_private_update_data();
		if ( ! is_wp_error( $update_data ) && isset( $update_data['plugins'][ $plugin_slug ] ) ) {
			$plugin_info = $update_data['plugins'][ $plugin_slug ];
		}
		if ( ! $plugin_info ) {
			$update_data = $this->get_public_update_data();
			if ( ! is_wp_error( $update_data ) && isset( $update_data['plugins'][ $plugin_slug ] ) ) {
				$plugin_info = $update_data['plugins'][ $plugin_slug ];
			}
		}

		if ( ! $plugin_info ) {
			return $result;
		}

		// Get package URL
		$package = '';
		if ( ! empty( $plugin_info['package'] ) ) {
			$package = $plugin_info['package'];
		} elseif ( ! empty( $plugin_info['source'] ) ) {
			$package = $plugin_info['source'];
		}

		return (object) array(
			'name'          => isset( $plugin_info['name'] ) ? $plugin_info['name'] : $plugin_slug,
			'slug'          => $plugin_slug,
			'version'       => isset( $plugin_info['version'] ) ? $plugin_info['version'] : '',
			'author'        => '<a href="https://cmsmasters.net/">CMSMasters</a>',
			'homepage'      => isset( $plugin_info['homepage'] ) ? $plugin_info['homepage'] : 'https://cmsmasters.net/',
			'download_link' => $package,
			'trunk'         => $package,
			'requires_php'  => '7.4',
			'last_updated'  => '',
			'sections'      => array(
				'description' => $this->get_plugin_description( $plugin_slug ) ?: sprintf(
					/* translators: %s: Plugin name */
					__( '%s is a plugin by CMSMasters.', 'cmsmasters-framework' ),
					isset( $plugin_info['name'] ) ? $plugin_info['name'] : $plugin_slug
				),
				'changelog' => isset( $plugin_info['changelog'] ) ? $plugin_info['changelog'] : '',
			),
			'banners'       => array(),
		);
	}

	/**
	 * Get plugin description from plugin header.
	 *
	 * @since 77.77.77
	 *
	 * @param string $plugin_slug Plugin slug.
	 * @return string Plugin description or empty string.
	 */
	private function get_plugin_description( $plugin_slug ) {
		$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_slug . '/' . $plugin_slug . '.php';
		if ( ! file_exists( $plugin_file ) ) {
			return '';
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_data = get_plugin_data( $plugin_file );
		return isset( $plugin_data['Description'] ) ? $plugin_data['Description'] : '';
	}

	/**
	 * Clear the update cache.
	 *
	 * @since 1.0.12
	 *
	 * @param WP_Upgrader $upgrader WP_Upgrader instance.
	 * @param array       $options  Array of bulk item update data.
	 */
	public function clear_update_cache( $upgrader, $options ) {
		if ( 'update' === $options['action'] ) {
			if ( 'plugin' === $options['type'] || 'theme' === $options['type'] ) {
				$this->clear_all_cache();
			}
		}
	}

	/**
	 * Clear all update cache.
	 *
	 * @since 1.0.12
	 */
	public function clear_all_cache() {
		$this->private_data = null;
		$this->public_data = null;
	}

	/**
	 * Log auto-update result (success/errors) to Logger.
	 *
	 * @since 1.0.12
	 *
	 * @param \WP_Upgrader $upgrader WP_Upgrader instance.
	 * @param array        $options  Update options.
	 */
	public function log_auto_update_result( $upgrader, $options ) {
		if ( empty( $options['action'] ) || 'update' !== $options['action'] ) {
			return;
		}
		if ( empty( $options['type'] ) || 'plugin' !== $options['type'] ) {
			return;
		}
		$plugins = array();
		if ( ! empty( $options['plugin'] ) ) {
			$plugins[] = $options['plugin'];
		} elseif ( ! empty( $options['plugins'] ) && is_array( $options['plugins'] ) ) {
			$plugins = $options['plugins'];
		}
		if ( empty( $plugins ) ) {
			return;
		}
		$our_plugin_files = array_keys( $this->get_our_plugins_map( null ) );
		$errors = array();
		if ( isset( $upgrader->skin ) && is_object( $upgrader->skin ) && method_exists( $upgrader->skin, 'get_error_messages' ) ) {
			$errors = $upgrader->skin->get_error_messages();
		}
		foreach ( $plugins as $plugin_file ) {
			if ( ! in_array( $plugin_file, $our_plugin_files, true ) ) {
				continue;
			}
			if ( ! empty( $errors ) ) {
				$msg = 'Auto-update failed for ' . $plugin_file . ': ' . implode( '; ', $errors );
				Logger::error( $msg, array( 'plugin' => $plugin_file, 'errors' => $errors ) );
			} else {
				Logger::info( 'Auto-update succeeded for ' . $plugin_file, array( 'plugin' => $plugin_file ) );
			}
		}
	}

	/**
	 * Refresh update transient before WP runs auto-updates (cron).
	 * Ensures our plugins' updates are in the list when the automatic upgrader runs.
	 *
	 * @since 1.0.12
	 */
	public function refresh_transient_before_auto_update() {
		try {
			$this->clear_all_cache();
			delete_site_transient( 'update_plugins' );
			wp_update_plugins();
		} catch ( \Throwable $e ) {
			Logger::error( 'Refresh transient before auto-update failed: ' . $e->getMessage(), array( 'trace' => $e->getTraceAsString() ) );
		}
	}

	/**
	 * Get options prefix for the current site/theme.
	 *
	 * Theme stores token_data under cmsmasters_{stylesheet}_token_data (e.g. cmsmasters_prana-yoga_token_data).
	 * When theme is not loaded (e.g. subsite in updater context), CMSMASTERS_OPTIONS_PREFIX is not defined;
	 * build prefix from current theme stylesheet so we read the correct option and token for this site.
	 *
	 * @since 1.0.12
	 *
	 * @return string Options prefix.
	 */
	private function get_options_prefix() {
		if ( defined( 'CMSMASTERS_OPTIONS_PREFIX' ) ) {
			return CMSMASTERS_OPTIONS_PREFIX;
		}
		$stylesheet = wp_get_theme()->get_stylesheet();
		return ( '' !== $stylesheet ) ? 'cmsmasters_' . $stylesheet . '_' : 'cmsmasters_';
	}

}
