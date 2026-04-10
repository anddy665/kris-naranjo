<?php
/**
 * Plugin Name: CMSMasters Framework
 * Plugin URI: https://cmsmasters.studio/
 * Description: Provides core functionality for your CMSMasters theme.
 * Author: CMSMasters
 * Version: 1.0.16
 * Author URI: https://cmsmasters.studio/
 *
 * Text Domain: cmsmasters-framework
 */

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Logger;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Updater\Updater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'CMSMASTERS_FRAMEWORK_VERSION', '1.0.16' );

define( 'CMSMASTERS_FRAMEWORK__FILE__', __FILE__ );
define( 'CMSMASTERS_FRAMEWORK_PLUGIN_BASE', plugin_basename( CMSMASTERS_FRAMEWORK__FILE__ ) );
define( 'CMSMASTERS_FRAMEWORK_PATH', plugin_dir_path( CMSMASTERS_FRAMEWORK__FILE__ ) );
define( 'CMSMASTERS_FRAMEWORK_URL', plugins_url( '/', CMSMASTERS_FRAMEWORK__FILE__ ) );
define( 'CMSMASTERS_FRAMEWORK_API_ROUTES_URL', 'https://api.cmsmasters.net/wp-json/cmsmasters-api/v1/' );

define( 'CMSMASTERS_FRAMEWORK_CORE_PATH', CMSMASTERS_FRAMEWORK_PATH . 'core/' );

/**
 * CMSMasters Framework initial class.
 *
 * The plugin file that checks all the plugin requirements and
 * run main plugin class.
 *
 * @since 1.0.0
 */
final class Cmsmasters_Framework {

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 * That's why cloning instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-framework' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-framework' ), '1.0.0' );
	}

	/**
	 * Initial class constructor.
	 *
	 * Initializing file class.
	 *
	 * @since 1.0.0
	 * @since 1.0.8 Fixed Give API rest.
	 */
	public function __construct() {
		$this->register_autoloader();

		new Logger();

		add_action( 'init', array( $this, 'i18n' ) );

		Updater::instance();

		register_activation_hook( CMSMASTERS_FRAMEWORK__FILE__, array( $this, 'plugin_activation_actions' ) );

		add_action( 'network_admin_notices', array( $this, 'multisite_network_activation_notice' ) );
		add_action( 'admin_notices', array( $this, 'multisite_network_activation_notice' ) );

		// In network admin only Updater and activation hook run; no theme-dependent code.
		if ( is_multisite() && is_network_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'check_theme_compatibility' ), 1 );
		add_action( 'after_switch_theme', array( $this, 'maybe_set_merlin_redirect_on_theme_switch' ) );

		add_action( 'after_setup_theme', array( $this, 'init' ) );

		add_action( 'plugins_loaded', function () {
			if ( class_exists( 'Give' ) ) {
				add_filter( 'rest_pre_dispatch', function ( $result, $server, $request ) {
					if ( '/wp/v2/users/me' === $request->get_route() && ! is_user_logged_in() ) {
						return rest_ensure_response( array( 'id' => 0 ) );
					}

					return $result;
				}, 10, 3 );
			}
		}, 20 );
	}

	/**
	 * Register autoloader.
	 *
	 * Autoloader loads all the plugin files.
	 *
	 * @since 1.0.0
	 */
	private function register_autoloader() {
		require_once CMSMASTERS_FRAMEWORK_CORE_PATH . 'autoloader.php';

		CmsmastersFramework\Autoloader::run();
	}

	/**
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'cmsmasters-framework', false, dirname( CMSMASTERS_FRAMEWORK_PLUGIN_BASE ) . '/languages/' );
	}

	/**
	 * Show notice on multisite when framework is not network-activated.
	 * Theme and plugin update functionality requires network activation.
	 *
	 * @since 1.0.12
	 */
	public function multisite_network_activation_notice() {
		if ( ! is_multisite() ) {
			return;
		}

		if ( is_plugin_active_for_network( CMSMASTERS_FRAMEWORK_PLUGIN_BASE ) ) {
			return;
		}

		if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_network_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>' . esc_html__( 'To enable theme and bundled plugins updates on this multisite, please Network Activate the CMSMasters Framework plugin.', 'cmsmasters-framework' ) . '</p></div>';
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after other plugins are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added check for required PHP modules.
	 *
	 * @return void Or require main Plugin class
	 */
	public function init() {
		if ( ! Utils::has_theme_constants() || ! Utils::is_required_php_modules_enabled() ) {
			return;
		}

		/**
		 * The main handler class.
		 */
		require CMSMASTERS_FRAMEWORK_CORE_PATH . 'plugin.php';

		new CmsmastersFramework\Plugin();
	}

	/**
	 * Plugin activation actions.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added check for required PHP modules.
	 */
	public function plugin_activation_actions() {
		// On single site require theme; on multisite allow (each subsite may have different theme).
		if ( ! is_multisite() && ! Utils::has_theme_constants() ) {
			deactivate_plugins( CMSMASTERS_FRAMEWORK_PLUGIN_BASE );

			wp_die(
				esc_html__( "Your theme doesn't support CMSMasters Framework plugin. Please use appropriate CMSMasters theme.", 'cmsmasters-framework' ),
				esc_html__( "Error!", 'cmsmasters-framework' ),
				array(
					'back_link' => 	true,
				)
			);
		}

		if ( ! Utils::is_required_php_modules_enabled() ) {
			deactivate_plugins( CMSMASTERS_FRAMEWORK_PLUGIN_BASE );

			wp_die(
				'<strong>Required PHP modules are missing.</strong><br /><br />
				To activate the plugin and import demo content properly, your server must have the <strong>GD</strong> and <strong>ZIP</strong> PHP modules enabled.<br />
				It looks like one or both of these modules are currently <strong>missing</strong> on your server.<br />
				Please contact your hosting provider and ask them to enable the <strong>GD</strong> and <strong>ZIP</strong> extensions.<br /><br />
				Once they are enabled, you can continue the theme installation process.',
				esc_html__( "Error!", 'cmsmasters-framework' ),
				array(
					'back_link' => 	true,
				)
			);
		}

		// Token and theme options only when theme constants are loaded (e.g. not on network activation without CMSMasters theme).
		if ( Utils::has_theme_constants() ) {
			$this->set_token();
			if ( ! is_child_theme() ) {
				update_option( CMSMASTERS_OPTIONS_PREFIX . 'merlin_redirect', 1, false );
			}
		}

		// Multisite: set merlin_redirect for each site that uses a CMSMasters theme so first admin load redirects to installer.
		if ( is_multisite() && is_plugin_active_for_network( CMSMASTERS_FRAMEWORK_PLUGIN_BASE ) ) {
			$sites = get_sites( array( 'fields' => 'ids', 'number' => 0 ) );
			foreach ( $sites as $blog_id ) {
				switch_to_blog( $blog_id );
				$theme = wp_get_theme( get_stylesheet() );
				if ( $theme->exists() && 'CMSMasters' === $theme->get( 'Author' ) && ! is_child_theme() ) {
					$prefix = 'cmsmasters_' . get_stylesheet() . '_';
					update_option( $prefix . 'merlin_redirect', 1, false );
				}
				restore_current_blog();
			}
		}
	}

	/**
	 * Set token if theme has been activated earlier.
	 *
	 * @since 1.0.0
	 */
	public function set_token() {
		if (
			API_Requests::check_token_status() ||
			empty( API_Requests::get_token_data( false ) )
		) {
			return;
		}

		API_Requests::regenerate_token();
	}

	/**
	 * Set merlin_redirect when theme is switched to a CMSMasters theme and framework is active.
	 * Ensures redirect to installer on next admin load (e.g. multisite: framework already active, user activates theme on site).
	 *
	 * @since 1.0.0
	 */
	public function maybe_set_merlin_redirect_on_theme_switch() {
		if ( ! Utils::has_theme_constants() || is_child_theme() ) {
			return;
		}
		$framework_active = is_plugin_active( CMSMASTERS_FRAMEWORK_PLUGIN_BASE );
		if ( is_multisite() && ! $framework_active ) {
			$framework_active = is_plugin_active_for_network( CMSMASTERS_FRAMEWORK_PLUGIN_BASE );
		}
		if ( ! $framework_active ) {
			return;
		}
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'merlin_redirect', 1, false );
	}

	/**
	 * Check theme compatibility.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added check for required PHP modules.
	 */
	public function check_theme_compatibility() {
		if ( Utils::has_theme_constants() && Utils::is_required_php_modules_enabled() ) {
			return;
		}

		// On multisite do not deactivate: main site may have non-CMSMasters theme; Plugin simply won't load there.
		if ( is_multisite() ) {
			return;
		}

		deactivate_plugins( CMSMASTERS_FRAMEWORK_PLUGIN_BASE );

		add_action('admin_notices', function() {
			echo '<div class="notice notice-warning is-dismissible">
				<p><strong>' . esc_html__( "CMSMasters Framework plugin was deactivated, because your theme doesn't support it. Please use appropriate CMSMasters theme.", 'cmsmasters-framework') . '</strong></p>
			</div>';
		} );
	}

}

new Cmsmasters_Framework();
