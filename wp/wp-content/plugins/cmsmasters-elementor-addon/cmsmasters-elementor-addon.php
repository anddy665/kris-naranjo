<?php
/**
 * Plugin Name: CMSMasters Elementor Addon
 * Description: Provides an extended Elementor functionality and tools that allow to get a professional website, quickly and easily.
 * Plugin URI: https://cmsmasters.studio/
 * Author: CMSMasters
 * Version: 1.24.1
 * Elementor tested up to: 4.0.1
 * Elementor Pro tested up to: 4.0.1
 * Author URI: https://cmsmasters.studio/
 *
 * Text Domain: cmsmasters-elementor
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


define( 'CMSMASTERS_ELEMENTOR_VERSION', '1.24.1' );
define( 'CMSMASTERS_ELEMENTOR_MIN_PARENT_VER', '3.27.0' );
define( 'CMSMASTERS_ELEMENTOR_MIN_PHP_VER', '7.4' );
define( 'CMSMASTERS_ELEMENTOR_MIN_WP_VER', '6.5' );

define( 'CMSMASTERS_ELEMENTOR__FILE__', __FILE__ );
define( 'CMSMASTERS_ELEMENTOR_PLUGIN_BASE', plugin_basename( CMSMASTERS_ELEMENTOR__FILE__ ) );
define( 'CMSMASTERS_ELEMENTOR_PATH', plugin_dir_path( CMSMASTERS_ELEMENTOR__FILE__ ) );

define( 'CMSMASTERS_ELEMENTOR_ASSETS_PATH', CMSMASTERS_ELEMENTOR_PATH . 'assets/' );
define( 'CMSMASTERS_ELEMENTOR_INCLUDES_PATH', CMSMASTERS_ELEMENTOR_PATH . 'includes/' );
define( 'CMSMASTERS_ELEMENTOR_MODULES_PATH', CMSMASTERS_ELEMENTOR_PATH . 'modules/' );

define( 'CMSMASTERS_ELEMENTOR_URL', plugins_url( '/', CMSMASTERS_ELEMENTOR__FILE__ ) );
define( 'CMSMASTERS_ELEMENTOR_ASSETS_URL', CMSMASTERS_ELEMENTOR_URL . 'assets/' );
define( 'CMSMASTERS_ELEMENTOR_ASSETS_CSS_URL', CMSMASTERS_ELEMENTOR_ASSETS_URL . 'css/' );
define( 'CMSMASTERS_ELEMENTOR_ASSETS_JS_URL', CMSMASTERS_ELEMENTOR_ASSETS_URL . 'js/' );
define( 'CMSMASTERS_ELEMENTOR_ASSETS_LIB_URL', CMSMASTERS_ELEMENTOR_ASSETS_URL . 'lib/' );
define( 'CMSMASTERS_ELEMENTOR_MODULES_URL', CMSMASTERS_ELEMENTOR_URL . 'modules/' );

define( 'CMSMASTERS_ADDON_OPTIONS_PREFIX', 'cmsmasters_' );

/**
 * CMSMasters Elementor Addon initial class.
 *
 * The plugin file that checks all the plugin requirements and
 * run main plugin class.
 *
 * @since 1.0.0
 */
final class Cmsmasters_Elementor_Addon {

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
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-elementor' ), '1.0.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-elementor' ), '1.0.0' );
	}

	/**
	 * Addon initial class constructor.
	 *
	 * Initializing the Addon initial file class.
	 *
	 * @since 1.0.0
	 * @since 1.18.0 Added check theme compatibility.
	 */
	public function __construct() {
		$this->register_autoloader();

		register_activation_hook( CMSMASTERS_ELEMENTOR__FILE__, array( $this, 'plugin_activation_actions' ) );
		add_action( 'admin_init', array( $this, 'check_theme_compatibility' ), 1 );

		add_action( 'init', array( $this, 'i18n' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Register autoloader.
	 *
	 * Addon autoloader loads all the plugin files.
	 *
	 * @since 1.0.0
	 */
	private function register_autoloader() {
		require_once CMSMASTERS_ELEMENTOR_INCLUDES_PATH . 'autoloader.php';

		CmsmastersElementor\Autoloader::run();
	}

	/**
	 * Plugin activation actions.
	 *
	 * @since 1.18.0
	 * @since 1.18.1 Fixed check theme compatibility.
	 * @since 1.18.3 Fixed check theme compatibility for old themes.
	 */
	public function plugin_activation_actions() {
		if ( defined( 'CMSMASTERS_THEME_VERSION' ) ) {
			return;
		}

		deactivate_plugins( CMSMASTERS_ELEMENTOR_PLUGIN_BASE );

		wp_die(
			esc_html__( "Your theme doesn't support CMSMasters Elementor Addon plugin. Please use appropriate CMSMasters theme.", 'cmsmasters-elementor' ),
			esc_html__( "Error!", 'cmsmasters-elementor' ),
			array(
				'back_link' => 	true,
			)
		);
	}

	/**
	 * Check theme compatibility.
	 *
	 * @since 1.18.0
	 * @since 1.18.1 Fixed check theme compatibility.
	 * @since 1.18.3 Fixed check theme compatibility for old themes.
	 */
	public function check_theme_compatibility() {
		if ( defined( 'CMSMASTERS_THEME_VERSION' ) ) {
			return;
		}

		deactivate_plugins( CMSMASTERS_ELEMENTOR_PLUGIN_BASE );

		add_action('admin_notices', function() {
			echo '<div class="notice notice-warning is-dismissible">
				<p><strong>' . esc_html__( "CMSMasters Elementor Addon plugin was deactivated, because your theme doesn't support it. Please use appropriate CMSMasters theme.", 'cmsmasters-elementor') . '</strong></p>
			</div>';
		} );
	}

	/**
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fixed translations for the plugin.
	 * @since 1.6.1 Fixed basedir in translations.
	 */
	public function i18n() {
		load_plugin_textdomain( 'cmsmasters-elementor', false, dirname( CMSMASTERS_ELEMENTOR_PLUGIN_BASE ) . '/languages/' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @return void Or require main Plugin class
	 */
	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );

			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, CMSMASTERS_ELEMENTOR_MIN_PARENT_VER, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );

			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, CMSMASTERS_ELEMENTOR_MIN_PHP_VER, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );

			return;
		}

		// Check for required WordPress version
		if ( version_compare( get_bloginfo( 'version' ), CMSMASTERS_ELEMENTOR_MIN_WP_VER, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );

			return;
		}

		$this->register_plugin();
	}

	/**
	 * Register plugin.
	 *
	 * Initialize Addon main plugin handler class.
	 *
	 * @since 1.0.0
	 */
	private function register_plugin() {
		/**
		 * The main Addon handler class.
		 */
		require CMSMASTERS_ELEMENTOR_INCLUDES_PATH . 'plugin.php';

		CmsmastersElementor\Plugin::instance();
	}

	/**
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_missing_main_plugin() {
		/* translators: Addon 'missing Elementor plugin' admin notice. 1: Plugin name - CMSMasters Elementor Addon, 2: Elementor */
		$message = __( '"%1$s" requires "%2$s" to be installed and activated.', 'cmsmasters-elementor' );

		$this->admin_notice_missing_something(
			__( 'Elementor', 'cmsmasters-elementor' ),
			false,
			$message
		);
	}

	/**
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_minimum_elementor_version() {
		$this->admin_notice_missing_something(
			__( 'Elementor', 'cmsmasters-elementor' ),
			CMSMASTERS_ELEMENTOR_MIN_PARENT_VER
		);
	}

	/**
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_minimum_php_version() {
		$this->admin_notice_missing_something(
			__( 'PHP', 'cmsmasters-elementor' ),
			CMSMASTERS_ELEMENTOR_MIN_PHP_VER
		);
	}

	/**
	 * Warning when the site doesn't have a minimum required WordPress version.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice_minimum_wp_version() {
		$this->admin_notice_missing_something(
			__( 'WordPress', 'cmsmasters-elementor' ),
			CMSMASTERS_ELEMENTOR_MIN_WP_VER
		);
	}

	/**
	 * Admin notice when something is missing.
	 *
	 * Warning when the site doesn't correspond some required technology.
	 *
	 * @since 1.0.0
	 */
	private function admin_notice_missing_something( $technology, $version = '1.0', $custom_message = false ) {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		/* translators: Addon 'minimum some technology' admin notice. 1: Plugin name - CMSMasters Elementor Addon, 2: Technology name, 3: Required version */
		$default_message = __( '"%1$s" requires "%2$s" version %3$s or greater.', 'cmsmasters-elementor' );
		$message_text = ( $custom_message ) ? $custom_message : $default_message;

		$message = sprintf(
			esc_html( $message_text ),
			'<strong>' . esc_html__( 'CMSMasters Elementor Addon', 'cmsmasters-elementor' ) . '</strong>',
			'<strong>' . esc_html( $technology ) . '</strong>',
			$version
		);

		printf( '<div class="notice notice-error is-dismissible">
			<p>%s</p>
		</div>', $message );
	}

}

new Cmsmasters_Elementor_Addon();
