<?php
namespace CmsmastersFramework\Admin\Installer\Merlin;

use CmsmastersFramework\Admin\Installer\Merlin\Includes\WpContentImporter\Class_Merlin_Importer;
use CmsmastersFramework\Admin\Installer\Merlin\Includes\Merlin_Customizer_Importer;
use CmsmastersFramework\Admin\Installer\Merlin\Includes\Merlin_Downloader;
use CmsmastersFramework\Admin\Installer\Merlin\Includes\Merlin_Hooks;
use CmsmastersFramework\Admin\Installer\Merlin\Includes\Merlin_Widget_Importer;
use CmsmastersFramework\Admin\Installer\Merlin\Merlin_Utils;
use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Logger;
use CmsmastersFramework\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Merlin WP
 * Better WordPress Theme Onboarding
 *
 * The following code is a derivative work from the
 * WordPress Theme Setup Wizard by David Baker.
 *
 * @since 1.0.0
 */
class Class_Merlin {
	/**
	 * Current theme.
	 *
	 * @since 1.0.0
	 *
	 * @var object WP_Theme
	 */
	protected $theme;

	/**
	 * Current/parent theme name.
	 *
	 * @since 1.0.0
	 *
	 * @var string current/parent theme name.
	 */
	protected $theme_name;

	/**
	 * Current step.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $step = '';

	/**
	 * Steps.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $steps = array();

	/**
	 * Demos list.
	 *
	 * @since 1.0.0
	 */
	private $demos_list = array();

	/**
	 * TGMPA instance.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $tgmpa;

	/**
	 * Importer.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $importer;

	/**
	 * WP Hook class.
	 *
	 * @since 1.0.0
	 *
	 * @var Merlin_Hooks
	 */
	protected $hooks;

	/**
	 * Holds the verified import files.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $import_files;

	/**
	 * The base import file name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $import_file_base_name;

	/**
	 * Helper.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $helper;

	/**
	 * Updater.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $updater;

	/**
	 * The text string array.
	 *
	 * @since 1.0.0
	 *
	 * @var array $strings
	 */
	protected $strings = null;

	/**
	 * The base path where Merlin is located.
	 *
	 * @since 1.0.0
	 *
	 * @var array $strings
	 */
	protected $base_path = null;

	/**
	 * The base url where Merlin is located.
	 *
	 * @since 1.0.0
	 *
	 * @var array $strings
	 */
	protected $base_url = null;

	/**
	 * The location where Merlin is located within the theme or plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string $directory
	 */
	protected $directory = null;

	/**
	 * Top level admin page.
	 *
	 * @since 1.0.0
	 *
	 * @var string $merlin_url
	 */
	protected $merlin_url = null;

	/**
	 * The wp-admin parent page slug for the admin menu item.
	 *
	 * @since 1.0.0
	 *
	 * @var string $parent_slug
	 */
	protected $parent_slug = null;

	/**
	 * The capability required for this menu to be displayed to the user.
	 *
	 * @since 1.0.0
	 *
	 * @var string $capability
	 */
	protected $capability = null;

	/**
	 * The URL for the "Learn more about child themes" link.
	 *
	 * @since 1.0.0
	 *
	 * @var string $child_action_btn_url
	 */
	protected $child_action_btn_url = null;

	/**
	 * The flag, to mark, if the theme license step should be enabled.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean $license_step_enabled
	 */
	protected $license_step_enabled = false;

	/**
	 * The URL for the "Where can I find the license key?" link.
	 *
	 * @since 1.0.0
	 *
	 * @var string $theme_license_help_url
	 */
	protected $theme_license_help_url = null;

	/**
	 * Remove the "Skip" button, if required.
	 *
	 * @since 1.0.0
	 *
	 * @var string $license_required
	 */
	protected $license_required = null;

	/**
	 * Turn on dev mode if you're developing.
	 *
	 * @since 1.0.0
	 *
	 * @var string $dev_mode
	 */
	protected $dev_mode = false;

	protected $ready_big_button_url = '';

	protected $slug = '';

	public $hook_suffix;

	/**
	 * Ignore.
	 *
	 * @since 1.0.0
	 *
	 * @var string $ignore
	 */
	public $ignore = null;

	/**
	 * Class Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Package-specific configuration args.
	 * @param array $strings Text for the different elements.
	 */
	public function __construct( $config = array(), $strings = array() ) {
		$config = wp_parse_args(
			$config, array(
				'base_path'            => CMSMASTERS_FRAMEWORK_PATH,
				'base_url'             => CMSMASTERS_FRAMEWORK_URL,
				'directory'            => 'merlin',
				'merlin_url'           => 'merlin',
				'parent_slug'          => 'themes.php',
				'capability'           => 'manage_options',
				'child_action_btn_url' => '',
				'dev_mode'             => '',
				'ready_big_button_url' => home_url( '/' ),
			)
		);

		// Set config arguments.
		$this->base_path              = $config['base_path'];
		$this->base_url               = $config['base_url'];
		$this->directory              = $config['directory'];
		$this->merlin_url             = $config['merlin_url'];
		$this->parent_slug            = $config['parent_slug'];
		$this->capability             = $config['capability'];
		$this->child_action_btn_url   = $config['child_action_btn_url'];
		$this->license_step_enabled   = $config['license_step'];
		$this->theme_license_help_url = $config['license_help_url'];
		$this->license_required       = $config['license_required'];
		$this->dev_mode               = $config['dev_mode'];
		$this->ready_big_button_url   = $config['ready_big_button_url'];

		// Strings passed in from the config file.
		$this->strings = $strings;

		// Retrieve a WP_Theme object.
		$this->theme = wp_get_theme();
		$this->theme_name = ( $this->theme->parent() ? $this->theme->parent()->name : $this->theme->name );
		$this->slug  = strtolower( preg_replace( '#[^a-zA-Z]#', '', $this->theme->template ) );

		// Set the ignore option.
		$this->ignore = $this->slug . '_ignore';

		// Is Dev Mode turned on?
		if ( true !== $this->dev_mode ) {

			// Has this theme been setup yet?
			$already_setup = get_option( 'merlin_' . $this->slug . '_completed' );

			// Return if Merlin has already completed it's setup.
			if ( $already_setup ) {
				return;
			}
		}

		// Get TGMPA.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : \TGM_Plugin_Activation::get_instance();
		}

		add_action( 'admin_init', array( $this, 'required_classes' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 30 );
		add_action( 'admin_init', array( $this, 'steps' ), 30, 0 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_page' ), 30, 0 );
		add_action( 'admin_init', array( $this, 'ignore' ), 5 );
		add_action( 'admin_footer', array( $this, 'svg_sprite' ) );
		add_filter( 'tgmpa_load', array( $this, 'load_tgmpa' ), 10, 1 );
		add_action( 'wp_ajax_merlin_content', array( $this, 'ajax_content' ), 10, 0 );
		add_action( 'wp_ajax_merlin_get_total_content_import_items', array( $this, 'ajax_get_total_content_import_items' ), 10, 0 );
		add_action( 'wp_ajax_merlin_plugins', array( $this, 'ajax_plugins' ), 10, 0 );
		add_action( 'wp_ajax_merlin_child_theme', array( $this, 'generate_child' ), 10, 0 );
		add_action( 'wp_ajax_merlin_activate_license', array( $this, 'ajax_activate_license' ), 10, 0 );
		add_action( 'wp_ajax_merlin_update_selected_import_data_info', array( $this, 'update_selected_import_data_info' ), 10, 0 );
		add_action( 'wp_ajax_merlin_import_finished', array( $this, 'import_finished' ), 10, 0 );
		add_filter( 'pt-importer/new_ajax_request_response_data', array( $this, 'pt_importer_new_ajax_request_response_data' ) );
		add_action( 'import_end', array( $this, 'after_content_import_setup' ) );
		add_action( 'import_start', array( $this, 'before_content_import_setup' ) );
		add_action( 'admin_init', array( $this, 'register_import_files' ) );

		$this->remove_plugins_activation_early_redirect();

		add_action( 'admin_init', array( $this, 'init_actions' ) );

		add_action( 'wp_ajax_cmsmasters_installer', array( $this, 'run_installer' ) );
	}

	/**
	 * Require necessary classes.
	 *
	 * @since 1.0.0
	 */
	public function required_classes() {
		$this->importer = new Class_Merlin_Importer( array( 'fetch_attachments' => true ) );

		$this->hooks = new Merlin_Hooks();
	}

	/**
	 * Redirection transient.
	 *
	 * @since 1.0.0
	 */
	public function redirect() {
		if ( ! get_option( CMSMASTERS_OPTIONS_PREFIX . 'merlin_redirect' ) ) {
			return;
		}

		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'merlin_redirect' );

		wp_safe_redirect( menu_page_url( $this->merlin_url, false ) );

		exit;
	}

	/**
	 * Give the user the ability to ignore Merlin WP.
	 *
	 * @since 1.0.0
	 */
	public function ignore() {
		// Bail out if not on correct page.
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'merlinwp-ignore-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->ignore ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		update_option( 'merlin_' . $this->slug . '_completed', 'ignored', false );
	}

	/**
	 * Conditionally load TGMPA
	 *
	 * @since 1.0.0
	 *
	 * @param string $status User's manage capabilities.
	 */
	public function load_tgmpa( $status ) {
		return is_admin() || current_user_can( 'install_themes' );
	}

	/**
	 * Determine if the user already has theme content installed.
	 * This can happen if swapping from a previous theme or updated the current theme.
	 * We change the UI a bit when updating / swapping to a new theme.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	protected function is_possible_upgrade() {
		return false;
	}

	/**
	 * Add the admin menu item, under Appearance.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {

		// Strings passed in from the config file.
		$strings = $this->strings;

		$this->hook_suffix = add_submenu_page(
			esc_html( $this->parent_slug ), esc_html( $strings['admin-menu'] ), esc_html( $strings['admin-menu'] ), sanitize_key( $this->capability ), sanitize_key( $this->merlin_url ), array( $this, 'admin_page' )
		);
	}

	/**
	 * Init actions.
	 *
	 * @since 1.0.0
	 */
	public function init_actions() {
		// Do not proceed, if we're not on the right page.
		if ( empty( $_GET['page'] ) || $this->merlin_url !== $_GET['page'] ) {
			return;
		}

		$current_step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		if (
			'child' !== $current_step &&
			'plugins' !== $current_step &&
			'content' !== $current_step
		) {
			delete_option( CMSMASTERS_OPTIONS_PREFIX . 'installer_type' );
			delete_option( CMSMASTERS_OPTIONS_PREFIX . 'content_import' );
		}

		if ( 'ready' === $current_step ) {
			$demo = Utils::get_demo();

			do_action( 'cmsmasters_import_ready' );

			if ( false === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_content_import_status' ) ) {
				do_action( 'cmsmasters_remove_unique_elementor_locations' );
			}
		}

		$this->enqueue_assets();

		$this->remove_plugins_activation_redirect();
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		// Styles
		wp_enqueue_style(
			'cmsmasters-framework-installer',
			File_Manager::get_css_assets_url( 'installer', null, 'default', true ),
			array( 'merlin' ),
			'1.0.0',
			'screen'
		);

		// Scripts
		wp_enqueue_script(
			'cmsmasters-framework-installer',
			File_Manager::get_js_assets_url( 'installer' ),
			array( 'merlin' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'cmsmasters-framework-installer', 'cmsmasters_framework_installer_params', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'type' => get_option( CMSMASTERS_OPTIONS_PREFIX . 'installer_type' ),
				'content_import' => get_option( CMSMASTERS_OPTIONS_PREFIX . 'content_import' ),
				'wpnonce' => wp_create_nonce( CMSMASTERS_OPTIONS_PREFIX . 'installer_nonce' ),
			)
		);
	}

	/**
	 * Remove plugins redirect on activation.
	 *
	 * @since 1.0.0
	 */
	protected function remove_plugins_activation_redirect() {
		delete_transient( 'cptui_activation_redirect' );
		update_option( 'wpforms_activation_redirect', true );

		if ( defined( 'WP_REDIS_CONFIG' ) || defined( 'WP_REDIS_DIR' ) ) {
			update_option( 'woocommerce_version', '9.8.5' );
		}

		if ( class_exists( '\Give_Cache' ) ) {
			\Give_Cache::delete( \Give_Cache::get_key( '_give_activation_redirect' ) );
		}
	}

	/**
	 * Remove plugins redirect on activation.
	 *
	 * @since 1.0.0
	 */
	protected function remove_plugins_activation_early_redirect() {
		if ( defined( 'PMPRO_VERSION' ) ) {
			update_option( 'pmpro_dashboard_version', PMPRO_VERSION, 'no' );
		}

		delete_transient( 'elementor_activation_redirect' );
		delete_transient( '_sp_activation_redirect' );
		add_filter( 'woocommerce_enable_setup_wizard', '__return_false', 99999 );
		add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_false', 99999 );
		add_filter( 'fs_redirect_on_activation_interactive-geo-maps', '__return_false', 99999 );
		add_filter( 'fs_redirect_on_activation_ajax-search-for-woocommerce', '__return_false', 99999 );
		add_filter( 'tribe_get_option_skip_welcome', '__return_true', 99999 );

		if ( class_exists( '\Give_Cache' ) ) {
			\Give_Cache::delete( \Give_Cache::get_key( '_give_activation_redirect' ) );
		}
	}

	/**
	 * Run installer.
	 *
	 * @since 1.0.0
	 */
	public function run_installer() {
		$type = ! isset( $_POST['type'] ) ? false : $_POST['type'];
		$content_import = ! isset( $_POST['content_import'] ) ? false : $_POST['content_import'];
		$demo_key = ! isset( $_POST['demo_key'] ) ? false : $_POST['demo_key'];

		if (
			false === $type ||
			false === $content_import ||
			false === $demo_key
		) {
			wp_send_json_error( array(
				'code' => 'invalid_demo_data',
				'message' => 'Invalid demo data.',
			), 403 );
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'installer_type', $type, false );

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'content_import', $content_import, false );

		if ( 'demos' !== CMSMASTERS_THEME_IMPORT_TYPE ) {
			Utils::set_demo_kit( $demo_key );
		}

		if ( 'only_kit' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			$demo_key = 'main';
		}

		$old_demo = Utils::get_demo();

		if ( $old_demo !== $demo_key ) {
			do_action( 'cmsmasters_remove_all_elementor_locations' );
		}

		Utils::set_demo( $demo_key );

		do_action( 'cmsmasters_set_import_status', 'pending' );

		do_action( 'cmsmasters_remove_temp_data' );
	}

	/**
	 * Add the admin page.
	 *
	 * @since 1.0.0
	 */
	public function admin_page() {

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Do not proceed, if we're not on the right page.
		if ( empty( $_GET['page'] ) || $this->merlin_url !== $_GET['page'] ) {
			return;
		}

		if ( ob_get_length() ) {
			ob_end_clean();
		}

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		// Use minified libraries if dev mode is turned on.
		$suffix = ( ( true === $this->dev_mode ) ) ? '' : '.min';

		// Enqueue styles.
		wp_enqueue_style( 'merlin', trailingslashit( $this->base_url ) . $this->directory . '/assets/css/merlin' . $suffix . '.css', array( 'wp-admin' ), '1.0.0' );

		// Enqueue javascript.
		wp_enqueue_script( 'merlin', trailingslashit( $this->base_url ) . $this->directory . '/assets/js/merlin' . $suffix . '.js', array( 'jquery-core' ), '1.0.0' );

		$texts = array(
			'something_went_wrong' => esc_html__( 'Something went wrong. Please refresh the page and try again!', 'cmsmasters-framework' ),
		);

		// Localize the javascript.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			// Check first if TMGPA is included.
			wp_localize_script(
				'merlin', 'merlin_params', array(
					'tgm_plugin_nonce' => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'tgm_bulk_url'     => $this->tgmpa->get_tgmpa_url(),
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'wpnonce'          => wp_create_nonce( 'merlin_nonce' ),
					'texts'            => $texts,
				)
			);
		} else {
			// If TMGPA is not included.
			wp_localize_script(
				'merlin', 'merlin_params', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'wpnonce' => wp_create_nonce( 'merlin_nonce' ),
					'texts'   => $texts,
				)
			);
		}

		ob_start();

		if ( 'demos' === $this->step ) {
			$data = API_Requests::get_request( 'get-demos-list' );

			if ( is_wp_error( $data ) ) {
				Logger::error( $data->get_error_message() );
			} else {
				$this->demos_list = $data;
			}
		}

		/**
		 * Start the actual page content.
		 */
		$this->header();

		echo '<div class="merlin__outer">
			<div class="merlin__wrapper">
				<div class="merlin__content merlin__content--' . esc_attr( strtolower( $this->steps[ $this->step ]['name'] ) ) . '">';

					// Content Handlers.
					$show_content = true;

					if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
						$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
					}

					if ( $show_content ) {
						$this->body();
					}

					$this->step_output();

				echo '</div>';

				echo sprintf( '<a class="return-to-dashboard" href="%s">%s</a>', esc_url( admin_url( '/' ) ), esc_html( $strings['return-to-dashboard'] ) );

				$ignore_url = wp_nonce_url( admin_url( '?' . $this->ignore . '=true' ), 'merlinwp-ignore-nounce' );

				echo sprintf( '<a class="return-to-dashboard ignore" href="%s">%s</a>', esc_url( $ignore_url ), esc_html( $strings['ignore'] ) );

			echo '</div>
		</div>';

		$this->footer();

		exit;
	}

	/**
	 * Output the header.
	 *
	 * @since 1.0.0
	 */
	protected function header() {
		// Strings passed in from the config file.
		$strings = $this->strings;

		// Get the current step.
		$current_step = strtolower( $this->steps[ $this->step ]['name'] );
		$body_classes = 'merlin__body merlin__body--' . $current_step;

		if (
			'plugins' === $current_step &&
			(
				! did_action( 'elementor/loaded' ) ||
				! class_exists( 'Cmsmasters_Elementor_Addon' )
			)
		) {
			$body_classes .= ' no_required_plugins';
		}

		if ( 'demos' === $current_step ) {
			$body_classes .= ' cmsmasters-demos-count-' . count( $this->demos_list );
		}

		echo '<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" ' . get_language_attributes() . '>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
			printf( esc_html( $strings['title%s%s%s%s'] ), '<ti', 'tle>', esc_html( $this->theme->name ), '</title>' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			do_action( 'admin_print_styles' );
			do_action( 'admin_print_scripts' );
		echo '</head>' .
		'<body class="' . esc_attr( $body_classes ) . '">';
	}

	/**
	 * Output the content for the current step.
	 *
	 * @since 1.0.0
	 */
	protected function body() {
		isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
	}

	/**
	 * Output the footer.
	 *
	 * @since 1.0.0
	 */
	protected function footer() {
		echo '</body>';

		do_action( 'admin_footer' );
		do_action( 'admin_print_footer_scripts' );

		echo '</html>';
	}

	/**
	 * SVG
	 *
	 * @since 1.0.0
	 */
	public function svg_sprite() {
		// Define SVG sprite file.
		$svg = trailingslashit( $this->base_path ) . $this->directory . '/assets/images/sprite.svg';

		// If it exists, include it.
		if ( file_exists( $svg ) ) {
			require_once apply_filters( 'merlin_svg_sprite', $svg );
		}
	}

	/**
	 * Setup steps.
	 *
	 * @since 1.0.0
	 */
	public function steps() {
		$this->steps = array(
			'welcome' => array(
				'name'    => esc_html__( 'Welcome', 'cmsmasters-framework' ),
				'view'    => array( $this, 'welcome' ),
				'handler' => array( $this, 'welcome_handler' ),
			),
		);

		if ( $this->license_step_enabled ) {
			$this->steps['license'] = array(
				'name' => esc_html__( 'License', 'cmsmasters-framework' ),
				'view' => array( $this, 'license' ),
			);
		}

		$this->steps['demos'] = array(
			'name' => esc_html__( 'Demos', 'cmsmasters-framework' ),
			'view' => array( $this, 'demos' ),
		);

		$this->steps['child'] = array(
			'name' => esc_html__( 'Child', 'cmsmasters-framework' ),
			'view' => array( $this, 'child' ),
		);

		// Show the plugin importer, only if TGMPA is included.
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->steps['plugins'] = array(
				'name' => esc_html__( 'Plugins', 'cmsmasters-framework' ),
				'view' => array( $this, 'plugins' ),
			);
		}

		// Show the content importer, only if there's demo content added.
		if ( ! empty( $this->import_files ) && 'disabled' !== get_option( CMSMASTERS_OPTIONS_PREFIX . 'content_import' ) ) {
			$this->steps['content'] = array(
				'name' => esc_html__( 'Content', 'cmsmasters-framework' ),
				'view' => array( $this, 'content' ),
			);
		}

		$this->steps['ready'] = array(
			'name' => esc_html__( 'Ready', 'cmsmasters-framework' ),
			'view' => array( $this, 'ready' ),
		);

		$this->steps = apply_filters( $this->theme->template . '_merlin_steps', $this->steps );
	}

	/**
	 * Output the steps
	 *
	 * @since 1.0.0
	 */
	protected function step_output() {
		$ouput_steps = $this->steps;
		$array_keys = array_keys( $this->steps );
		$current_step = array_search( $this->step, $array_keys, true );

		array_shift( $ouput_steps );

		echo '<ol class="dots">';

			foreach ( $ouput_steps as $step_key => $step ) {
				$class_attr = '';
				$show_link  = false;

				if ( $step_key === $this->step ) {
					$class_attr = 'active';
				} elseif ( $current_step > array_search( $step_key, $array_keys, true ) ) {
					$class_attr = 'done';
					$show_link  = true;
				}

				echo '<li class="' . esc_attr( $class_attr ) . '">
					<a href="' . esc_url( $this->step_link( $step_key ) ) . '" title="' . esc_attr( $step['name'] ) . '"></a>
				</li>';
			}

		echo '</ol>';
	}

	/**
	 * Get the step URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $step Name of the step, appended to the URL.
	 */
	protected function step_link( $step ) {
		return add_query_arg( 'step', $step );
	}

	/**
	 * Get the next step link.
	 *
	 * @since 1.0.0
	 */
	protected function step_next_link() {
		$keys = array_keys( $this->steps );
		$step = array_search( $this->step, $keys, true ) + 1;

		return add_query_arg( 'step', $keys[ $step ] );
	}

	/**
	 * Introduction step
	 *
	 * @since 1.0.0
	 */
	protected function welcome() {
		// Has this theme been setup yet? Compare this to the option set when you get to the last panel.
		$already_setup = get_option( 'merlin_' . $this->slug . '_completed' );

		// Text strings.
		$header    = ! $already_setup ? $this->strings['welcome-header%s'] : $this->strings['welcome-header-success%s'];
		$paragraph = ! $already_setup ? $this->strings['welcome%s'] : $this->strings['welcome-success%s'];

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'welcome' ) ) .
			'<h1>' . esc_html( sprintf( $header, $this->theme_name ) ) . '</h1>' .
			'<p>' . esc_html( sprintf( $paragraph, $this->theme_name ) ) . '</p>' .
		'</div>';

		echo '<footer class="merlin__content__footer">' .
			'<a href="' . esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '/' ) ) . '" class="merlin__button merlin__button--skip">' . esc_html( $this->strings['btn-no'] ) . '</a>' .
			'<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange">' . esc_html( $this->strings['btn-start'] ) . '</a>';

			wp_nonce_field( 'merlin' );

		echo '</footer>';

		Logger::debug( 'The welcome step has been displayed' );
	}

	/**
	 * Handles save button from welcome page.
	 * This is to perform tasks when the setup wizard has already been run.
	 *
	 * @since 1.0.0
	 */
	protected function welcome_handler() {
		check_admin_referer( 'merlin' );

		return false;
	}

	/**
	 * Theme license step.
	 *
	 * @since 1.0.0
	 */
	protected function license() {
		$is_theme_registered = $this->is_theme_registered();
		$action_url = $this->theme_license_help_url;
		$required = $this->license_required;

		$is_theme_registered_class = ( $is_theme_registered ) ? ' is-registered' : null;

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header = ! $is_theme_registered ? $strings['license-header%s'] : $strings['license-header-success%s'];
		$action = $strings['license-tooltip'];
		$label = $strings['license-label'];
		$skip = $strings['btn-license-skip'];
		$next = $strings['btn-next'];
		$paragraph = ! $is_theme_registered ? $strings['license%s'] : $strings['license-success%s'];
		$install = $strings['btn-license-activate'];

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'license' ) ) .
			'<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>' .
			'<h1>' . esc_html( sprintf( $header, $this->theme_name ) ) . '</h1>';

			if ( $is_theme_registered ) {
				echo '<p class="cmsmasters-merlin-license__registered-notice">' . esc_html__( 'The theme is already registered, so you can go to the next step!', 'cmsmasters-framework' ) . '</p>';
			}

			if ( ! $is_theme_registered ) {
				echo '<div class="cmsmasters-merlin-license">';

					echo '<div class="cmsmasters-merlin-license__source-code">
							<label>
								<input type="radio" name="cmsmasters_merlin_license__source_code" value="purchase-code" checked="checked" />
								<span>' . esc_html__( 'I bought the theme on Themeforest', 'cmsmasters-framework' ) . '</span>
							</label>
							<label>
								<input type="radio" name="cmsmasters_merlin_license__source_code" value="envato-elements-token" />
								<span>' . esc_html__( 'I downloaded the theme from Envato Elements', 'cmsmasters-framework' ) . '</span>
							</label>
						</div>';

					echo '<div class="cmsmasters-merlin-license__code cmsmasters-merlin-license--purchase-code">
						<div class="cmsmasters-merlin-license__code-wrapper">
							<input type="text" name="cmsmasters_merlin_license__purchase_code" placeholder="' . esc_attr__( 'Enter Your Purchase code', 'cmsmasters-framework' ) . '" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
						</div>';

							echo '<span class="cmsmasters-merlin-license__code-description cmsmasters-merlin-license__code-description-bottom">' .
								sprintf(
									esc_html__( 'Where can I find my %1$s?', 'cmsmasters-framework' ),
									'<a href="' . esc_url( 'https://docs.cmsmasters.net/blog/how-to-find-your-envato-purchase-code/' ) . '" target="_blank">' .
										esc_html__( 'purchase code', 'cmsmasters-framework' ) .
									'</a>'
								) .
							'</span>';

					echo '</div>';

						echo '<div class="cmsmasters-merlin-license__code cmsmasters-merlin-license--envato-elements-token">
							<span class="cmsmasters-merlin-license__code-description cmsmasters-merlin-license__code-description-top">' .
								sprintf(
									esc_html__( 'In order to activate the theme you need to %1$s', 'cmsmasters-framework' ),
									'<a href="' . esc_url( 'https://api.extensions.envato.com/extensions/begin_activation?extension_id=cmsmasters-envato-elements&extension_type=envato-wordpress&extension_description=' . wp_get_theme()->get( 'Name' ) . ' (' . get_home_url() . ')&utm_content=settings' ) . '" target="_blank">' .
										esc_html__( 'generate Envato Elements token', 'cmsmasters-framework' ) .
									'</a>'
								) .
							'</span>
							<div class="cmsmasters-merlin-license__code-wrapper">
								<input type="text" name="cmsmasters_merlin_license__envato_elements_token" placeholder="' . esc_attr__( 'Envato Elements Token', 'cmsmasters-framework' ) . '" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
								<a href="https://docs.cmsmasters.net/how-to-activate-the-theme-using-the-envato-elements-token/" alt="' . esc_attr( $action ) . '" target="_blank">
									<span class="hint--top" aria-label="' . esc_attr( $action ) . '">' .
										Merlin_Utils::get_svg( array( 'icon' => 'help' ) ) .
									'</span>
								</a>
							</div>
						</div>';

					echo '<p class="cmsmasters-merlin-license__notice"></p>';

						echo '<div class="cmsmasters-merlin-license__user-info">
							<h3 class="cmsmasters-merlin-license__user-info--title">' . esc_html__( 'Register your copy', 'cmsmasters-framework' ) . '</h3>
							<p class="cmsmasters-merlin-license__user-info--text">' . esc_html__( 'Get information about promotions, new themes and theme updates directly to your inbox', 'cmsmasters-framework' ) . '</p>
							<div class="cmsmasters-merlin-license__user-info--item">
								<input type="text" name="cmsmasters_merlin_license__user_name" placeholder="' . esc_attr__( 'Your Name', 'cmsmasters-framework' ) . '" />
							</div>
							<div class="cmsmasters-merlin-license__user-info--item">
								<input type="text" name="cmsmasters_merlin_license__user_email" placeholder="' . esc_attr__( 'Your Email', 'cmsmasters-framework' ) . '" />
							</div>
							<p class="cmsmasters-merlin-license__user-info--privacy">' .
								sprintf(
									esc_html__( 'Your data is stored and processed in accordance with our %1$s', 'cmsmasters-framework' ),
									'<a href="' . esc_url( 'https://cmsmasters.studio/privacy-policy/' ) . '" target="_blank">' .
										esc_html__( 'Privacy Policy', 'cmsmasters-framework' ) .
									'</a>'
								) .
							'</p>
						</div>';

				echo '</div>';
			}

		echo '</div>';

		echo '<footer class="merlin__content__footer ' . esc_attr( $is_theme_registered_class ) . '">';

			if ( ! $is_theme_registered ) {
				if ( ! $required ) {
					echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--proceed">' . esc_html( $skip ) . '</a>';
				}

				echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next button-next js-merlin-license-activate-button" data-callback="activate_license">
					<span class="merlin__button--loading__text">' . esc_html( $install ) . '</span>' .
					Merlin_Utils::get_loading_spinner() .
				'</a>';
			} else {
				echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange">' . esc_html( $next ) . '</a>';
			}

			wp_nonce_field( 'merlin' );

		echo '</footer>';

		Logger::debug( 'The license activation step has been displayed' );
	}


	/**
	 * Check, if the theme is currently registered.
	 * On multisite, requires this site to have both valid token status and stored license data (token_data or token).
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	protected function is_theme_registered() {
		if ( ! API_Requests::check_token_status() ) {
			return false;
		}
		// On multisite, only treat as registered if this site has actually activated the license (has token_data or token).
		if ( is_multisite() ) {
			$token_data = API_Requests::get_token_data( false );
			$token      = get_option( CMSMASTERS_OPTIONS_PREFIX . 'token', '' );
			if ( empty( $token_data ) && empty( $token ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Demos step.
	 *
	 * @since 1.0.0
	 */
	protected function demos() {
		$parent_class = 'cmsmasters-installer-demos';

		echo $this->get_demos_notice();

		echo '<div class="' . esc_attr( $parent_class ) . '">' .
			'<ul class="' . esc_attr( $parent_class ) . '__list">';

				foreach ( $this->demos_list as $demo_key => $demo_args ) {
					$name = ( isset( $demo_args['name'] ) ? $demo_args['name'] : false );
					$preview_url = ( isset( $demo_args['preview_url'] ) ? $demo_args['preview_url'] : false );
					$preview_img_url = ( isset( $demo_args['preview_img_url'] ) ? $demo_args['preview_img_url'] : false );

					echo '<li class="' . esc_attr( $parent_class ) . '__item">' .
						'<figure class="' . esc_attr( $parent_class ) . '__item-image">' .
							'<span class="dashicons dashicons-format-image"></span>' .
							( $preview_img_url ? '<img src="' . esc_url( $preview_img_url ) . '" />' : '' ) .
							( $preview_url ? '<a href="' . esc_url( $preview_url ) . '" target="_blank" class="' . esc_attr( $parent_class ) . '__item-preview"><span title="' . esc_attr( $name ) . '">' . esc_html__( 'Demo Preview', 'cmsmasters-framework' ) . '</span></a>' : '' ) .
						'</figure>' .
						'<div class="' . esc_attr( $parent_class ) . '__item-info">' .
							( $name ? '<h3 class="' . esc_attr( $parent_class ) . '__item-title">' . esc_html( $name ) . '</h3>' : '' ) .
							'<div class="' . esc_attr( $parent_class ) . '__item-buttons">' .
								'<a href="' . esc_url( $this->step_next_link() ) . '" class="cmsmasters-install-button cmsmasters-custom" data-key="' . esc_attr( $demo_key ) . '">' . esc_html__( 'Manual', 'cmsmasters-framework' ) . '</a>' .
								'<div class="' . esc_attr( $parent_class ) . '__item-buttons-express-wrap">' .
									'<label>' .
										esc_html__( 'Import dummy content?', 'cmsmasters-framework' ) .
										'<input type="checkbox" checked="checked" class="cmsmasters-import-content-status" />' .
									'</label>' .
									'<a href="' . esc_url( $this->step_next_link() ) . '" class="cmsmasters-install-button cmsmasters-express" data-key="' . esc_attr( $demo_key ) . '">' . esc_html__( 'One-click Install', 'cmsmasters-framework' ) . '</a>' .
								'</div>' .
							'</div>' .
						'</div>' .
					'</li>';
				}

			echo '</ul>' .
		'</div>';

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'installation_status', 'run', false );
	}

	/**
	 * Get demos step notice.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Added XMLReader to required php modules.
	 *
	 * @return string Notice HTML.
	 */
	public function get_demos_notice() {
		$limits_to_increase = Merlin_Utils::get_server_limits_to_increase();
		$php_modules_to_include = Merlin_Utils::get_php_modules_to_include();

		if ( ! extension_loaded('xmlreader') ) {
			$php_modules_to_include[] = 'PHP extension, XMLReader, is not installed, or has been disabled.';
		}

		if ( empty( $limits_to_increase ) && empty( $php_modules_to_include ) ) {
			return '';
		}

		$notice_content = Merlin_Utils::get_notice_img( trailingslashit( $this->base_url ) . $this->directory . '/assets/images/demos-notice.svg' );

		$notice_content .= Merlin_Utils::get_notice_text( esc_html__( 'Your theme provides demo content for a ready website, including all pages, post types, templates and other elements, so in order for it to be installed please make sure your server has appropriate settings:', 'cmsmasters-framework' ) );

		if ( ! empty( $limits_to_increase ) ) {
			$notice_content .= Merlin_Utils::get_notice_title( esc_html__( 'Increase the PHP configuration limits to at least:', 'cmsmasters-framework' ) );

			$notice_content .= Merlin_Utils::get_notice_list( $limits_to_increase, 'grouped' );
		}

		if ( ! empty( $php_modules_to_include ) ) {
			$notice_content .= Merlin_Utils::get_notice_title( esc_html__( 'Enable PHP modules:', 'cmsmasters-framework' ) );

			$notice_content .= Merlin_Utils::get_notice_list( $php_modules_to_include, 'separated' );
		}

		$notice_content .= Merlin_Utils::get_notice_info( sprintf(
			esc_html__( 'You can find more information %s', 'cmsmasters-framework' ),
			'<a href="https://docs.cmsmasters.net/requirements/" target="_blank">' . esc_html__( 'here', 'cmsmasters-framework' ) . '</a>'
		) );

		return Merlin_Utils::get_notice( $notice_content, array( 'cmsmasters-installer-notice--demos' ) );
	}

	/**
	 * Child theme generator.
	 *
	 * @since 1.0.0
	 */
	protected function child() {
		// Variables.
		$is_child_theme = is_child_theme();
		$child_theme_option = get_option( 'merlin_' . $this->slug . '_child' );
		$theme = $child_theme_option ? wp_get_theme( $child_theme_option )->name : $this->theme . ' Child';
		$action_url = $this->child_action_btn_url;

		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$header = ! $is_child_theme ? $strings['child-header'] : $strings['child-header-success'];
		$action = $strings['child-action-link'];
		$skip = $strings['btn-skip'];
		$next = $strings['btn-next'];
		$paragraph = ! $is_child_theme ? $strings['child'] : $strings['child-success%s'];
		$install = $strings['btn-child-install'];

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'child' ) ) .
			'<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>' .
			'<h1>' . esc_html( $header ) . '</h1>' .
			'<p id="child-theme-text">' . esc_html( sprintf( $paragraph, $theme ) ) . '</p>' .
			'<a class="merlin__button merlin__button--knockout merlin__button--no-chevron merlin__button--external" href="' . esc_url( $action_url ) . '" target="_blank">' . esc_html( $action ) . '</a>' .
		'</div>';

		echo '<footer class="merlin__content__footer">';

			if ( ! $is_child_theme ) {
				echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--proceed">' . esc_html( $skip ) . '</a>' .
				'<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next button-next" data-callback="install_child">
					<span class="merlin__button--loading__text">' . esc_html( $install ) . '</span>' .
					Merlin_Utils::get_loading_spinner() .
				'</a>';
			} else {
				echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange">' . esc_html( $next ) . '</a>';
			}

			wp_nonce_field( 'merlin' );

		echo '</footer>';

		Logger::debug( 'The child theme installation step has been displayed' );
	}

	/**
	 * Theme plugins
	 *
	 * @since 1.0.0
	 */
	protected function plugins() {
		// Variables.
		$url = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'merlin' );
		$method = '';
		$fields = array_keys( $_POST );
		$creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields );

		tgmpa_load_bulk_installer();

		if ( false === $creds ) {
			return true;
		}

		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
			return true;
		}

		// Are there plugins that need installing/activating?
		$plugins = $this->get_tgmpa_plugins();
		$required_plugins = $recommended_plugins = array();
		$count = count( $plugins['all'] );
		$class = $count ? null : 'no-plugins';

		// Split the plugins into required and recommended.
		foreach ( $plugins['all'] as $slug => $plugin ) {
			if ( ! empty( $plugin['required'] ) ) {
				$required_plugins[ $slug ] = $plugin;
			} else {
				$recommended_plugins[ $slug ] = $plugin;
			}
		}

		echo '<div class="cmsmasters-installer-exiting-step-message">
			<p>' . esc_html__( 'Please do not reload or leave the page, the import is still in progress. This will take some time, please wait.', 'cmsmasters-framework' ) . '</p>
		</div>';

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'plugins' ) ) .
			'<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>' .
			'<h1>' . ( $count ? esc_html( $this->strings['plugins-header'] ) : esc_html( $this->strings['plugins-header-success'] ) ) . '</h1>' .
			'<p>' . ( $count ? esc_html( $this->strings['plugins'] ) : esc_html( $this->strings['plugins-success%s'] ) ) . '</p>';

			if ( $count ) {
				echo '<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout">
					<span>' . esc_html( $this->strings['plugins-action-link'] ) . '</span>
					<span class="chevron"></span>
				</a>';
			}

		echo '</div>';

		echo '<form action="" method="post">';

			if ( $count ) {
				echo '<ul class="merlin__drawer merlin__drawer--install-plugins">';

					if ( ! empty( $required_plugins ) ) {
						foreach ( $required_plugins as $slug => $plugin ) {
							echo '<li data-slug="' . esc_attr( $slug ) . '">
								<input type="checkbox" name="default_plugins[' . esc_attr( $slug ) . ']" class="checkbox" id="default_plugins_' . esc_attr( $slug ) . '" value="1" checked>
								<label for="default_plugins_' . esc_attr( $slug ) . '">
									<i></i>
									<span>' . esc_html( $plugin['name'] ) . '</span>
									<span class="badge">
										<span class="hint--top" aria-label="' . esc_attr__( 'Required', 'cmsmasters-framework' ) . '">' .
											esc_html__( 'req', 'cmsmasters-framework' ) .
										'</span>
									</span>
								</label>
							</li>';
						}
					}

					if ( ! empty( $recommended_plugins ) ) {
						foreach ( $recommended_plugins as $slug => $plugin ) {
							echo '<li data-slug="' . esc_attr( $slug ) . '">
								<input type="checkbox" name="default_plugins[' . esc_attr( $slug ) . ']" class="checkbox" id="default_plugins_' . esc_attr( $slug ) . '" value="0">
								<label for="default_plugins_' . esc_attr( $slug ) . '">
									<i></i>
									<span>' . esc_html( $plugin['name'] ) . '</span>
								</label>
							</li>';
						}
					}

				echo '</ul>';
			}

			echo '<footer class="merlin__content__footer ' . esc_attr( $class ) . '">';

				if ( $count ) {
					echo '<a id="close" href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--closer merlin__button--proceed">' . esc_html( $this->strings['btn-skip'] ) . '</a>
					<a id="skip" href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--proceed">' . esc_html( $this->strings['btn-skip'] ) . '</a>
					<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next button-next" data-callback="install_plugins">
						<span class="merlin__button--loading__text">' . esc_html( $this->strings['btn-plugins-install'] ) . '</span>' .
						Merlin_Utils::get_loading_spinner() .
					'</a>';
				} else {
					echo '<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next merlin__button--proceed merlin__button--colorchange">' . esc_html( $this->strings['btn-next'] ) . '</a>';
				}

				wp_nonce_field( 'merlin' );

			echo '</footer>' .
		'</form>';

		Logger::debug( 'The plugin installation step has been displayed' );
	}

	/**
	 * Page setup
	 *
	 * @since 1.0.0
	 */
	protected function content() {
		echo '<div class="cmsmasters-installer-exiting-step-message">
			<p>' . esc_html__( 'Please do not reload or leave the page, the import is still in progress. This will take some time, please wait.', 'cmsmasters-framework' ) . '</p>
		</div>';

		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			echo '<div class="cmsmasters-installer-error-message">' .
				'<p>' .
					sprintf(
						esc_html__( 'Oops! Something went wrong. Required plugins are not installed or activated and import couldn\'t be processed. Please contact %1$s for further assistance.', 'cmsmasters-framework' ),
						'<a href="https://cmsmasters.studio/help-center/" target="_blank">' . esc_html__( 'Support', 'cmsmasters-framework' ) . '</a>'
					) .
				'</p>' .
			'</div>';

			Logger::error( 'The required plugins are not installed.' );

			return;
		}

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'content' ) ) .
			'<svg class="icon icon--checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="icon--checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="icon--checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
			</svg>' .
			'<h1>' . esc_html( $this->strings['import-header'] ) . '</h1>' .
			'<p>' . esc_html( $this->strings['import'] ) . '</p>';

			if ( 1 < count( $this->import_files ) ) {
				echo '<div class="merlin__select-control-wrapper">' .
					'<select class="merlin__select-control js-merlin-demo-import-select">';

						foreach ( $this->import_files as $index => $import_file ) {
							echo '<option value="' . esc_attr( $index ) . '">' . esc_html( $import_file['import_file_name'] ) . '</option>';
						}

					echo '</select>' .
					'<div class="merlin__select-control-help">
						<span class="hint--top" aria-label="' . esc_attr__( 'Select Demo', 'cmsmasters-framework' ) . '">' .
							Merlin_Utils::get_svg( array( 'icon' => 'downarrow' ) ) .
						'</span>
					</div>' .
				'</div>';
			}

			echo '<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout">
				<span>' . esc_html( $this->strings['import-action-link'] ) . '</span>
				<span class="chevron"></span>
			</a>' .
		'</div>';

		echo '<form action="" method="post"' . ( 1 < count( $this->import_files ) ? ' class="is-multi-import"' : '' ) . '>' .
			'<ul class="merlin__drawer merlin__drawer--import-content js-merlin-drawer-import-content">' .
				$this->get_import_steps_html( $this->get_import_data_info() ) .
			'</ul>' .
			'<footer class="merlin__content__footer">' .
				'<a id="close" href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--closer merlin__button--proceed">' . esc_html( $this->strings['btn-skip'] ) . '</a>' .
				'<a id="skip" href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--skip merlin__button--proceed">' . esc_html( $this->strings['btn-skip'] ) . '</a>' .
				'<a href="' . esc_url( $this->step_next_link() ) . '" class="merlin__button merlin__button--next button-next" data-callback="install_content">
					<span class="merlin__button--loading__text">' . esc_html( $this->strings['btn-import'] ) . '</span>
					<div class="merlin__progress-bar">
						<span class="js-merlin-progress-bar"></span>
					</div>
					<span class="js-merlin-progress-bar-percentage">0%</span>
				</a>';

				wp_nonce_field( 'merlin' );

			echo '</footer>' .
		'</form>';

		Logger::debug( 'The content import step has been displayed' );
	}

	/**
	 * Final step
	 *
	 * @since 1.0.0
	 */
	protected function ready() {
		if ( 'started' === get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status' ) ) {
			echo '<div class="cmsmasters-installer-error-message">' .
				'<p>' .
					sprintf(
						esc_html__( 'Oops! Something went wrong. Content are not correctly imported and import couldn\'t be processed. Please contact %1$s for further assistance.', 'cmsmasters-framework' ),
						'<a href="https://cmsmasters.studio/help-center/" target="_blank">' . esc_html__( 'Support', 'cmsmasters-framework' ) . '</a>'
					) .
				'</p>' .
			'</div>';

			delete_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status' );

			Logger::error( 'The content was not imported correctly.' );

			return;
		}

		delete_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status' );

		if ( class_exists( 'ElementorPro\Plugin' ) ) {
			$notice_content = Merlin_Utils::get_notice_img( trailingslashit( $this->base_url ) . $this->directory . '/assets/images/ready-notice.svg' );

			$notice_content .= Merlin_Utils::get_notice_title( esc_html__( 'Please Note:', 'cmsmasters-framework' ) );

			$notice_content .= Merlin_Utils::get_notice_text( esc_html__( 'CMSmasters Elementor Addon and Elementor Pro are both very powerful plugins that work seamlessly together. However, they both provide the Template Builder functionality, and to make your website look exactly like the demo one after import, CMSmasters Elementor Addon templates are prioritized by default.', 'cmsmasters-framework' ) );

			$notice_content .= Merlin_Utils::get_notice_info( sprintf(
				esc_html__( 'You can change the priority at any time by adjusting your settings %1$s.', 'cmsmasters-framework' ),
				'<a href="' . admin_url( 'admin.php?page=cmsmasters-addon-settings#tab-pro' ) . '" target="_blank">' . esc_html__( 'here', 'cmsmasters-framework' ) . '</a>'
			) );

			$notice_content .= Merlin_Utils::get_notice_info( sprintf(
				esc_html__( 'More information about template priority can be found %1$s.', 'cmsmasters-framework' ),
				'<a href="https://docs.cmsmasters.net/how-to-manage-template-priority-between-cmsmasters-elementor-addon-and-elementor-pro/" target="_blank">' . esc_html__( 'here', 'cmsmasters-framework' ) . '</a>'
			) );

			$notice_content .= Merlin_Utils::get_notice_button( array(
				'tag' => 'button',
				'text' => esc_html__( 'I understand', 'cmsmasters-framework' ),
				'add_classes' => array( 'cmsmasters-installer-notice__close-js' ),
			) );

			echo Merlin_Utils::get_notice( $notice_content );
		}

		$links = array();

		for ( $i = 1; $i < 4; $i++ ) {
			if ( ! empty( $this->strings[ "ready-link-$i" ] ) ) {
				$links[] = $this->strings[ "ready-link-$i" ];
			}
		}

		$allowed_html_array = array(
			'a' => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
		);

		update_option( 'merlin_' . $this->slug . '_completed', time(), false );

		echo '<div class="merlin__content--transition">' .
			Merlin_Utils::get_svg( array( 'icon' => 'done' ) ) .
			'<h1>' . esc_html( sprintf( $this->strings['ready-header'], $this->theme_name ) ) . '</h1>' .
			'<p>' . wp_kses( sprintf( $this->strings['ready%s'], $this->theme->author ), $allowed_html_array ) . '</p>' .
		'</div>';

		echo '<footer class="merlin__content__footer merlin__content__footer--fullwidth' . ( empty( $links ) ? ' merlin__content__footer--nolinks' : '' ) . '">' .
			'<a href="' . esc_url( $this->ready_big_button_url ) . '" class="merlin__button merlin__button--blue merlin__button--fullwidth merlin__button--popin">' . esc_html( $this->strings['ready-big-button'] ) . '</a>';

			if ( ! empty( $links ) ) {
				echo '<a id="merlin__drawer-trigger" class="merlin__button merlin__button--knockout"><span>' . esc_html( $this->strings['ready-action-link'] ) . '</span><span class="chevron"></span></a>';

				echo '<ul class="merlin__drawer merlin__drawer--extras">';

					foreach ( $links as $link ) {
						echo '<li>' . wp_kses( $link, $allowed_html_array ) . '</li>';
					}

				echo '</ul>';
			}

		echo '</footer>';

		Logger::debug( 'The final step has been displayed' );
	}

	/**
	 * Get registered TGMPA plugins
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_tgmpa_plugins() {
		$plugins = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $this->tgmpa->plugins as $slug => $plugin ) {
			if ( $this->tgmpa->is_plugin_active( $slug ) && false === $this->tgmpa->does_plugin_have_update( $slug ) ) {
				continue;
			} else {
				$plugins['all'][ $slug ] = $plugin;
				if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}
					if ( $this->tgmpa->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}
			}
		}

		return $plugins;
	}

	/**
	 * Generate the child theme via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function generate_child() {
		// Strings passed in from the config file.
		$strings = $this->strings;

		// Text strings.
		$success = $strings['child-json-success%s'];
		$already = $strings['child-json-already%s'];

		$name = $this->theme . ' Child';
		$slug = sanitize_title( $name );

		$path = get_theme_root() . '/' . $slug;

		if ( ! file_exists( $path ) ) {
			File_Manager::generate_child();
		} else {
			if ( $this->theme->template !== $slug ) :
				update_option( 'merlin_' . $this->slug . '_child', $name, false );
				switch_theme( $slug );
			endif;

			Logger::debug( 'The existing child theme was activated' );

			wp_send_json(
				array(
					'done'    => 1,
					'message' => sprintf(
						esc_html( $success ), $slug
					),
				)
			);
		}

		if ( $this->theme->template !== $slug ) :
			update_option( 'merlin_' . $this->slug . '_child', $name, false );
		endif;

		Logger::debug( 'The newly generated child theme was activated' );

		wp_send_json(
			array(
				'done'    => 1,
				'message' => sprintf(
					esc_html( $already ), $name
				),
			)
		);
	}

	/**
	 * Activate the theme (license key) via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function ajax_activate_license() {
		if ( ! check_ajax_referer( 'merlin_nonce', 'wpnonce' ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Yikes! The theme activation failed. Please try again or contact support.', 'cmsmasters-framework' ),
				)
			);
		}

		$error_code = '';
		$source_code = empty( $_POST['source_code'] ) ? 'purchase-code' : $_POST['source_code'];

		if ( 'purchase-code' === $source_code && empty( $_POST['purchase_code'] ) ) {
			$error_code = 'empty_purchase_code';
		} elseif ( 'envato-elements-token' === $source_code && empty( $_POST['envato_elements_token'] ) ) {
			$error_code = 'empty_envato_elements_token';
		}

		if ( ! empty( $error_code ) ) {
			wp_send_json(
				array(
					'success' => false,
					'code' => $error_code,
					'error_field' => 'license_key',
					'message' => esc_html__( 'License key field is empty', 'cmsmasters-framework' ),
				)
			);
		}

		API_Requests::generate_token( array(
			'user_name' => empty( $_POST['user_name'] ) ? '' : $_POST['user_name'],
			'user_email' => empty( $_POST['user_email'] ) ? '' : $_POST['user_email'],
			'source_code' => $source_code,
			'purchase_code' => empty( $_POST['purchase_code'] ) ? '' : $_POST['purchase_code'],
			'envato_elements_token' => empty( $_POST['envato_elements_token'] ) ? '' : $_POST['envato_elements_token'],
			'input_data_source' => 'installer',
		) );

		wp_send_json(
			array(
				'done' => 1,
				'success' => true,
				'message' => sprintf( esc_html( $this->strings['license-json-success%s'] ), $this->theme_name ),
			)
		);
	}

	/**
	 * Do plugins' AJAX
	 *
	 * @since 1.0.0
	 *
	 * @internal Used as a calback.
	 */
	public function ajax_plugins() {
		if ( ! check_ajax_referer( 'merlin_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
			exit( 0 );
		}

		$json = array();
		$tgmpa_url = $this->tgmpa->get_tgmpa_url();
		$plugins = $this->get_tgmpa_plugins();

		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'message'       => esc_html__( 'Activating', 'cmsmasters-framework' ),
				);
				break;
			}
		}

		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'message'       => esc_html__( 'Updating', 'cmsmasters-framework' ),
				);
				break;
			}
		}

		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( $_POST['slug'] === $slug ) {
				$json = array(
					'url'           => $tgmpa_url,
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa->menu,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'message'       => esc_html__( 'Installing', 'cmsmasters-framework' ),
				);
				break;
			}
		}

		if ( $json ) {
			Logger::debug(
				'A plugin with the following data will be processed',
				array(
					'plugin_slug' => $_POST['slug'],
					'message'     => $json['message'],
				)
			);

			$json['hash']    = md5( serialize( $json ) );
			$json['message'] = esc_html__( 'Installing', 'cmsmasters-framework' );
			wp_send_json( $json );
		} else {
			Logger::debug(
				'A plugin with the following data was processed',
				array(
					'plugin_slug' => $_POST['slug'],
				)
			);

			wp_send_json(
				array(
					'done'    => 1,
					'message' => esc_html__( 'Success', 'cmsmasters-framework' ),
				)
			);
		}

		exit;
	}

	/**
	 * Do content's AJAX
	 *
	 * @since 1.0.0
	 */
	public function ajax_content() {
		static $content = null;

		$selected_import = intval( $_POST['selected_index'] );

		if ( null === $content ) {
			$content = $this->get_import_data( $selected_import );
		}

		if ( ! check_ajax_referer( 'merlin_nonce', 'wpnonce' ) || empty( $_POST['content'] ) && isset( $content[ $_POST['content'] ] ) ) {
			Logger::error( 'The content importer AJAX call failed to start, because of incorrect data' );

			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Invalid content!', 'cmsmasters-framework' ),
				)
			);
		}

		$json = false;
		$this_content = $content[ $_POST['content'] ];

		if ( isset( $_POST['proceed'] ) ) {
			if ( is_callable( $this_content['install_callback'] ) ) {
				Logger::info(
					'The content import AJAX call will be executed with this import data',
					array(
						'title' => $this_content['title'],
						'data'  => $this_content['data'],
					)
				);

				$logs = call_user_func( $this_content['install_callback'], $this_content['data'] );

				if ( 'content' === $_POST['content'] && class_exists( 'mp_timetable\classes\models\Import' ) ) {
					$mptt_content_url = $this->import_files[0]['import_mptt_file_url'];

					if ( ! empty( $mptt_content_url ) ) {
						$mptt_import = new \mp_timetable\classes\models\Import();

						$mptt_import->fetch_attachments = true;

						$mptt_import->process_start( $mptt_content_url );
					}
				}

				if ( $logs ) {
					$json = array(
						'done'    => 1,
						'message' => $this_content['success'],
						'debug'   => '',
						'logs'    => $logs,
						'errors'  => '',
					);

					// The content import ended, so we should mark that all posts were imported.
					if ( 'content' === $_POST['content'] ) {
						$json['num_of_imported_posts'] = 'all';
					}
				}
			}
		} else {
			$json = array(
				'url'            => admin_url( 'admin-ajax.php' ),
				'action'         => 'merlin_content',
				'proceed'        => 'true',
				'content'        => $_POST['content'],
				'_wpnonce'       => wp_create_nonce( 'merlin_nonce' ),
				'selected_index' => $selected_import,
				'message'        => $this_content['installing'],
				'logs'           => '',
				'errors'         => '',
			);
		}

		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) );
			wp_send_json( $json );
		} else {
			Logger::error(
				'The content import AJAX call failed with this passed data',
				array(
					'selected_content_index' => $selected_import,
					'importing_content'      => $_POST['content'],
					'importing_data'         => $this_content['data'],
				)
			);

			wp_send_json(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Error', 'cmsmasters-framework' ),
					'logs'    => '',
					'errors'  => '',
				)
			);
		}
	}


	/**
	 * AJAX call to retrieve total items (posts, pages, CPT, attachments) for the content import.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_total_content_import_items() {
		if ( ! check_ajax_referer( 'merlin_nonce', 'wpnonce' ) && empty( $_POST['selected_index'] ) ) {
			Logger::error( 'The content importer AJAX call for retrieving total content import items failed to start, because of incorrect data.' );

			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Invalid data!', 'cmsmasters-framework' ),
				)
			);
		}

		$selected_import = intval( $_POST['selected_index'] );
		$import_files = $this->get_import_files_paths( $selected_import );

		wp_send_json_success( $this->importer->get_number_of_posts_to_import( $import_files['content'] ) );
	}


	/**
	 * Get import data from the selected import.
	 * Which data does the selected import have for the import.
	 *
	 * @since 1.0.0
	 *
	 * @param int $selected_import_index The index of the predefined demo import.
	 *
	 * @return bool|array
	 */
	public function get_import_data_info( $selected_import_index = 0 ) {
		$import_data = array(
			'content'      => false,
			'widgets'      => false,
			'options'      => false,
			'after_import' => false,
		);

		if ( empty( $this->import_files[ $selected_import_index ] ) ) {
			return false;
		}

		if ( ! empty( $this->import_files[ $selected_import_index ]['import_file_url'] ) ) {
			$import_data['content'] = true;
		}

		if ( ! empty( $this->import_files[ $selected_import_index ]['import_widget_file_url'] ) ) {
			$import_data['widgets'] = true;
		}

		if ( ! empty( $this->import_files[ $selected_import_index ]['import_customizer_file_url'] ) ) {
			$import_data['options'] = true;
		}

		if ( false !== has_action( 'merlin_after_all_import' ) ) {
			$import_data['after_import'] = true;
		}

		return $import_data;
	}


	/**
	 * Get the import files/data.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed customizer and widgets import.
	 *
	 * @param int $selected_import_index The index of the predefined demo import.
	 *
	 * @return array
	 */
	protected function get_import_data( $selected_import_index = 0 ) {
		$content = array();

		$import_files = $this->get_import_files_paths( $selected_import_index );

		if ( ! empty( $import_files['content'] ) ) {
			$content['content'] = array(
				'title'            => esc_html__( 'Content', 'cmsmasters-framework' ),
				'description'      => esc_html__( 'Demo content data.', 'cmsmasters-framework' ),
				'pending'          => esc_html__( 'Pending', 'cmsmasters-framework' ),
				'installing'       => esc_html__( 'Installing', 'cmsmasters-framework' ),
				'success'          => esc_html__( 'Success', 'cmsmasters-framework' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'install_callback' => array( $this->importer, 'import' ),
				'data'             => $import_files['content'],
			);
		}

		if ( ! empty( $import_files['widgets'] ) ) {
			$content['widgets'] = array(
				'title'            => esc_html__( 'Widgets', 'cmsmasters-framework' ),
				'description'      => esc_html__( 'Sample widgets data.', 'cmsmasters-framework' ),
				'pending'          => esc_html__( 'Pending', 'cmsmasters-framework' ),
				'installing'       => esc_html__( 'Installing', 'cmsmasters-framework' ),
				'success'          => esc_html__( 'Success', 'cmsmasters-framework' ),
				'install_callback' => array( Merlin_Widget_Importer::class, 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['widgets'],
			);
		}

		if ( ! empty( $import_files['options'] ) ) {
			$content['options'] = array(
				'title'            => esc_html__( 'Options', 'cmsmasters-framework' ),
				'description'      => esc_html__( 'Sample theme options data.', 'cmsmasters-framework' ),
				'pending'          => esc_html__( 'Pending', 'cmsmasters-framework' ),
				'installing'       => esc_html__( 'Installing', 'cmsmasters-framework' ),
				'success'          => esc_html__( 'Success', 'cmsmasters-framework' ),
				'install_callback' => array( Merlin_Customizer_Importer::class, 'import' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $import_files['options'],
			);
		}

		if ( false !== has_action( 'merlin_after_all_import' ) ) {
			$content['after_import'] = array(
				'title'            => esc_html__( 'After import setup', 'cmsmasters-framework' ),
				'description'      => esc_html__( 'After import setup.', 'cmsmasters-framework' ),
				'pending'          => esc_html__( 'Pending', 'cmsmasters-framework' ),
				'installing'       => esc_html__( 'Installing', 'cmsmasters-framework' ),
				'success'          => esc_html__( 'Success', 'cmsmasters-framework' ),
				'install_callback' => array( $this->hooks, 'after_all_import_action' ),
				'checked'          => $this->is_possible_upgrade() ? 0 : 1,
				'data'             => $selected_import_index,
			);
		}

		$content = apply_filters( 'merlin_get_base_content', $content, $this );

		return $content;
	}

	/**
	 * Change the new AJAX request response data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The default data.
	 *
	 * @return array The updated data.
	 */
	public function pt_importer_new_ajax_request_response_data( $data ) {
		$data['url'] = admin_url( 'admin-ajax.php' );
		$data['message'] = esc_html__( 'Installing', 'cmsmasters-framework' );
		$data['proceed'] = 'true';
		$data['action'] = 'merlin_content';
		$data['content'] = 'content';
		$data['_wpnonce'] = wp_create_nonce( 'merlin_nonce' );
		$data['hash'] = md5( rand() ); // Has to be unique (check JS code catching this AJAX response).

		return $data;
	}

	/**
	 * After content import setup code.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 Fixed standard WP import.
	 */
	public function after_content_import_setup() {
		if ( empty( get_option( CMSMASTERS_OPTIONS_PREFIX . 'installer_type' ) ) ) {
			return;
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status', 'finished', false );

		do_action( 'cmsmasters_content_import_end' );

		$query_args = array(
			'post_type' => 'page',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'no_found_rows' => true,
			'ignore_sticky_posts' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby' => 'post_date ID',
			'order' => 'ASC',
		);

		// Set static homepage.
		$homepage = new \WP_Query( array_merge( $query_args, array(
			'title' => apply_filters( 'merlin_content_home_page_title', 'Home' ),
		) ) );

		if ( ! empty( $homepage->post ) && ! empty( $homepage->post->ID ) ) {
			update_option( 'page_on_front', $homepage->post->ID );
			update_option( 'show_on_front', 'page' );

			Logger::debug( 'The home page was set', array( 'homepage_id' => $homepage->post->ID ) );
		}

		// Set static blog page.
		$blogpage = new \WP_Query( array_merge( $query_args, array(
			'title' => apply_filters( 'merlin_content_blog_page_title', 'Blog' ),
		) ) );

		if ( ! empty( $blogpage->post ) && ! empty( $blogpage->post->ID ) ) {
			update_option( 'page_for_posts', $blogpage->post->ID );
			update_option( 'show_on_front', 'page' );

			Logger::debug( 'The blog page was set', array( 'blog_page_id' => $blogpage->post->ID ) );
		}
	}

	/**
	 * Before content import setup code.
	 *
	 * @since 1.0.0
	 * @since 1.0.6 Fixed standard WP import.
	 */
	public function before_content_import_setup() {
		if ( empty( get_option( CMSMASTERS_OPTIONS_PREFIX . 'installer_type' ) ) ) {
			return;
		}

		if ( empty( get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status' ) ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_import_content_status', 'started', false );
		}
		
		do_action( 'cmsmasters_content_import_start' );

		$query_args = array(
			'post_type' => 'page',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'no_found_rows' => true,
			'ignore_sticky_posts' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'orderby' => 'post_date ID',
			'order' => 'ASC',
		);

		// Update the Hello World! post by making it a draft.
		$hello_world = new \WP_Query( array_merge( $query_args, array(
			'post_type' => 'post',
			'title' => 'Hello world!',
		) ) );

		if ( ! empty( $hello_world->post ) ) {
			$hello_world->post->post_status = 'draft';
			wp_update_post( $hello_world->post );

			Logger::debug( 'The Hello world post status was set to draft' );
		}

		// Update the Sample Page post by making it a draft.
		$sample_page = new \WP_Query( array_merge( $query_args, array(
			'title' => 'Sample Page',
		) ) );

		if ( ! empty( $sample_page->post ) ) {
			$sample_page->post->post_status = 'draft';
			wp_update_post( $sample_page->post );

			Logger::debug( 'The Sample Page post status was set to draft' );
		}
	}

	/**
	 * Register the import files.
	 *
	 * @since 1.0.0
	 */
	public function register_import_files() {
		$data = Utils::get_import_demo_data( 'files_urls' );

		if ( empty( $data ) ) {
			$this->import_files = array();

			return;
		}

		$files_list = array(
			'import_file_name' => Utils::get_demo(),
		);

		if ( ! empty( $data['content'] ) ) {
			$files_list['import_file_url'] = $data['content'];
		}

		if ( ! empty( $data['mptt-content'] ) ) {
			$files_list['import_mptt_file_url'] = $data['mptt-content'];
		}

		if ( ! empty( $data['widgets'] ) ) {
			$files_list['import_widget_file_url'] = $data['widgets'];
		}

		if ( ! empty( $data['options'] ) ) {
			$files_list['import_customizer_file_url'] = $data['options'];
		}

		$this->import_files = array( $files_list );
	}

	/**
	 * Set the import file base name.
	 * Check if an existing base name is available (saved in a transient).
	 *
	 * @since 1.0.0
	 */
	public function set_import_file_base_name() {
		$existing_name = get_option( 'merlin_import_file_base_name' );

		if ( ! empty( $existing_name ) ) {
			$this->import_file_base_name = $existing_name;
		} else {
			$this->import_file_base_name = date( 'Y-m-d__H-i-s' );
		}

		update_option( 'merlin_import_file_base_name', $this->import_file_base_name, false );
	}

	/**
	 * Get the import file paths.
	 * Grab the defined local paths, download the files or reuse existing files.
	 *
	 * @since 1.0.0
	 *
	 * @param int $selected_import_index The index of the selected import.
	 *
	 * @return array
	 */
	public function get_import_files_paths( $selected_import_index ) {
		$selected_import_data = empty( $this->import_files[ $selected_import_index ] ) ? false : $this->import_files[ $selected_import_index ];

		if ( empty( $selected_import_data ) ) {
			return array();
		}

		// Set the base name for the import files.
		$this->set_import_file_base_name();

		$base_file_name = $this->import_file_base_name;
		$import_files   = array(
			'content' => '',
			'widgets' => '',
			'options' => '',
		);

		$downloader = new Merlin_Downloader();

		// Check if 'import_file_url' is not defined.
		if ( ! empty( $selected_import_data['import_file_url'] ) ) {
			// Set the filename string for content import file.
			$content_filename = 'content-' . $base_file_name . '.xml';

			// Retrieve the content import file.
			$import_files['content'] = $downloader->fetch_existing_file( $content_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['content'] ) ) {
				$import_files['content'] = $downloader->download_file( $selected_import_data['import_file_url'], $content_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['content'] ) ) {
				$import_files['content'] = '';
			}
		}

		// Get widgets file as well. If defined!
		if ( ! empty( $selected_import_data['import_widget_file_url'] ) ) {
			// Set the filename string for widgets import file.
			$widget_filename = 'widgets-' . $base_file_name . '.json';

			// Retrieve the content import file.
			$import_files['widgets'] = $downloader->fetch_existing_file( $widget_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['widgets'] ) ) {
				$import_files['widgets'] = $downloader->download_file( $selected_import_data['import_widget_file_url'], $widget_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['widgets'] ) ) {
				$import_files['widgets'] = '';
			}
		}

		// Get customizer import file as well. If defined!
		if ( ! empty( $selected_import_data['import_customizer_file_url'] ) ) {
			// Setup filename path to save the customizer content.
			$customizer_filename = 'options-' . $base_file_name . '.dat';

			// Retrieve the content import file.
			$import_files['options'] = $downloader->fetch_existing_file( $customizer_filename );

			// Download the file, if it's missing.
			if ( empty( $import_files['options'] ) ) {
				$import_files['options'] = $downloader->download_file( $selected_import_data['import_customizer_file_url'], $customizer_filename );
			}

			// Reset the variable, if there was an error.
			if ( is_wp_error( $import_files['options'] ) ) {
				$import_files['options'] = '';
			}
		}

		return $import_files;
	}

	/**
	 * AJAX callback for the 'merlin_update_selected_import_data_info' action.
	 *
	 * @since 1.0.0
	 */
	public function update_selected_import_data_info() {
		$selected_index = ! isset( $_POST['selected_index'] ) ? false : intval( $_POST['selected_index'] );

		if ( false === $selected_index ) {
			wp_send_json_error();
		}

		$import_info = $this->get_import_data_info( $selected_index );
		$import_info_html = $this->get_import_steps_html( $import_info );

		wp_send_json_success( $import_info_html );
	}

	/**
	 * Get the import steps HTML output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $import_info The import info to prepare the HTML for.
	 *
	 * @return string
	 */
	public function get_import_steps_html( $import_info ) {
		$out = '';

		foreach ( $import_info as $slug => $available ) {
			if ( ! $available ) {
				continue;
			}

			if ( 'content' === $slug ) {
				$item_text = __( 'Dummy Content', 'cmsmasters-framework' );
			} elseif ( 'widgets' === $slug ) {
				$item_text = __( 'Sidebars Widgets', 'cmsmasters-framework' );
			} elseif ( 'options' === $slug ) {
				$item_text = __( 'Customizer Settings', 'cmsmasters-framework' );
			} else {
				$item_text = ucfirst( str_replace( '_', ' ', $slug ) );
			}

			$out .= '<li class="merlin__drawer--import-content__list-item status status--Pending" data-content="' . esc_attr( $slug ) . '">
				<input type="checkbox" name="default_content[' . esc_attr( $slug ) . ']" class="checkbox checkbox-' . esc_attr( $slug ) . '" id="default_content_' . esc_attr( $slug ) . '" value="1" checked>
				<label for="default_content_' . esc_attr( $slug ) . '">
					<i></i>
					<span>' . esc_html( $item_text ) . '</span>
				</label>
			</li>';
		}

		return $out;
	}

	/**
	 * AJAX call for cleanup after the importing steps are done -> import finished.
	 *
	 * @since 1.0.0
	 */
	public function import_finished() {
		delete_option( 'merlin_import_file_base_name' );
		wp_send_json_success();
	}

}
