<?php
namespace CmsmastersFramework\Admin;

use CmsmastersFramework\Admin\Installer\Installer;
use CmsmastersFramework\Admin\Options\Options_Manager;
use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin modules.
 *
 * Main class for admin modules.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Admin modules constructor.
	 *
	 * Run modules for admin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_action( 'wp_ajax_cmsmasters_hide_admin_notice', array( $this, 'ajax_hide_admin_notice' ) );

		add_action( 'wp_ajax_cmsmasters_dismiss_api_notice', array( $this, 'ajax_dismiss_api_notice' ) );

		add_filter( 'register_post_type_args', array( $this, 'remove_export_post_types' ), 10, 2 );

		add_filter( 'cmsmasters_plugins_list_filter', array( $this, 'add_plugins_list' ) );

		add_action( 'cmsmasters_remove_temp_data', array( $this, 'remove_plugins_list' ) );

		add_filter( 'cmsmasters_ei_export_theme_options_name', function () {
			return CMSMASTERS_OPTIONS_NAME;
		} );

		$this->add_notices();

		new Installer();

		new Options_Manager();
	}

	/**
	 * Plugin row meta.
	 *
	 * Adds row meta links to the plugin list table
	 *
	 * @since 1.0.2
	 *
	 * @param array $plugin_meta Plugin metadata.
	 * @param string $plugin_file Path to the plugin file.
	 *
	 * @return array An array of plugin row meta links.
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( CMSMASTERS_FRAMEWORK_PLUGIN_BASE !== $plugin_file ) {
			return $plugin_meta;
		}

		/* translators: Plugin name in WordPress admin plugins page */
		$plugin_name = __( 'CMSMasters Framework', 'cmsmasters-framework' );

		$meta = array(
			'changelog' => sprintf( '<a href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
				esc_url( 'https://docs.cmsmasters.net/cmsmasters-framework-plugin-changelog/' ),
				/* translators: Plugin changelog link aria-label attribute. %s: Plugin name */
				esc_attr( sprintf( __( 'View %s Changelog', 'cmsmasters-framework' ), $plugin_name ) ),
				esc_html__( 'Changelog', 'cmsmasters-framework' )
			),
		);

		return array_merge( $plugin_meta, $meta );
	}

	/**
	 * Filter filesystem method.
	 *
	 * @since 1.0.0
	 */
	public function filter_filesystem_method( $method ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $method;
		}

		return 'direct';
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_assets() {
		// Scripts
		wp_enqueue_script(
			'cmsmasters-framework-admin',
			File_Manager::get_js_assets_url( 'admin' ),
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'cmsmasters-framework-admin', 'cmsmasters_framework_admin_params', array(
			'nonce' => wp_create_nonce( 'cmsmasters_framework_admin_nonce' ),
		) );
	}

	/**
	 * Hide admin notice.
	 *
	 * @since 1.0.0
	 */
	public function ajax_hide_admin_notice() {
		if ( ! check_ajax_referer( 'cmsmasters_framework_admin_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Invalid nonce. Notice was not deleted.', 'cmsmasters-framework' ),
			) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_permissions',
				'message' => esc_html__( 'You don\'t have permissions.', 'cmsmasters-framework' ),
			) );
		}

		if ( ! isset( $_POST['option_key'] ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'empty_option_key',
				'message' => esc_html__( 'Empty option key.', 'cmsmasters-framework' ),
			) );
		}

		update_option( $_POST['option_key'], 'hide' );
	}

	/**
	 * Add admin notices.
	 *
	 * @since 1.0.0
	 */
	protected function add_notices() {
		if ( ! did_action( 'elementor/loaded' ) && current_user_can( 'install_plugins' ) ) {
			add_action( 'admin_notices', array( $this, 'elementor_activation_notice' ) );
		}

		if ( ! API_Requests::check_token_status() ) {
			add_action( 'admin_notices', array( $this, 'license_activation_notice' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'license_deactivation_notice' ) );
		}

		add_action( 'admin_notices', array( $this, 'render_api_notices' ) );

		add_action( 'admin_notices', array( $this, 'apply_demo_notice' ) );

		add_action( 'admin_notices', array( $this, 'invalid_admin_email_notice' ) );
	}

	/**
	 * Elementor activation notice.
	 *
	 * @since 1.0.0
	 */
	public function elementor_activation_notice() {
		$screen = get_current_screen();

		if (
			isset( $screen->parent_file ) &&
			'plugins.php' === $screen->parent_file &&
			'update' === $screen->id
		) {
			return;
		}

		$plugins = get_plugins();

		if ( isset( $plugins['elementor/elementor.php'] ) ) {
			$link_url = wp_nonce_url(
				self_admin_url( 'plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=active' ),
				'activate-plugin_elementor/elementor.php'
			);
			$link_text = esc_html__( 'Activate', 'cmsmasters-framework' );
		} else {
			$link_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
			$link_text = esc_html__( 'Install', 'cmsmasters-framework' );
		}

		echo '<div class="notice notice-error">' .
			'<p>' .
				sprintf(
					esc_html__( '%s requires Elementor to be activate.', 'cmsmasters-framework' ),
					'<strong>' . esc_html__( 'The Theme', 'cmsmasters-framework' ) . '</strong>'
				) .
				'&nbsp;&nbsp;&nbsp;<a href="' . esc_url( $link_url ) . '" class="button button-primary">' . esc_html( $link_text ) . '</a>' .
			'</p>' .
		'</div>';
	}

	/**
	 * License activation notice.
	 *
	 * @since 1.0.0
	 */
	public function license_activation_notice() {
		if ( isset( $_GET['page'] ) && 'cmsmasters-options-license' === $_GET['page'] ) {
			return;
		}

		echo '<div class="notice notice-warning is-dismissible">' .
			'<p><strong>' . esc_html__( 'Your license is not activated.', 'cmsmasters-framework' ) . '</strong></p>' .
			'<p>' .
				sprintf(
					esc_html__(
						/* translators: %s: License activation link */
						'To use the full functionality of the theme, please %s',
						'cmsmasters-framework'
					),
					'<strong><a href="' . esc_url( self_admin_url( 'admin.php?page=cmsmasters-options-license' ) ) . '">' . esc_html__( 'activate the license', 'cmsmasters-framework' ) . '</a></strong>'
				) .
			'</p>' .
		'</div>';
	}

	/**
	 * License deactivation notice.
	 *
	 * @since 1.0.0
	 */
	public function license_deactivation_notice() {
		if ( 'hide' === get_option( 'cmsmasters_license_deactivation_notice_visibility' ) ) {
			return;
		}

		echo '<div class="notice notice-info is-dismissible cmsmasters-dismiss-notice-permanent" data-option-key="cmsmasters_license_deactivation_notice_visibility">' .
			'<p>' .
				sprintf(
					esc_html__(
						/* translators: %s: License deactivation link */
						'Please %s so that it can be reused before deleting the site or moving it to a new domain or server.',
						'cmsmasters-framework'
					),
					'<a href="' . esc_url( self_admin_url( 'admin.php?page=cmsmasters-options-license' ) ) . '">' . esc_html__( 'deactivate theme license', 'cmsmasters-framework' ) . '</a>'
				) .
			'</p>' .
		'</div>';
	}

	/**
	 * Apply demo notice.
	 *
	 * @since 1.0.0
	 */
	public function apply_demo_notice() {
		if ( 'demos' !== CMSMASTERS_THEME_IMPORT_TYPE || 'show' !== get_option( 'cmsmasters_apply_demo_notice_visibility' ) ) {
			return;
		}

		echo '<div class="notice notice-info is-dismissible cmsmasters-dismiss-notice-permanent" data-option-key="cmsmasters_apply_demo_notice_visibility">' .
			'<p>' .
				sprintf(
					__( 'You have applied a new design concept to your website. Image sizes in design concepts may differ, this is why it is recommended to run a %1$sRegenerate Thumbnails%2$s tool to generate new image sizes.', 'cmsmasters-framework' ),
					'<a href="' . esc_url( 'https://wordpress.org/plugins/regenerate-thumbnails/' ) . '" target="_blank">',
					'</a>'
				) .
			'</p>' .
		'</div>';
	}

	/**
	 * Invalid admin email notice.
	 *
	 * @since 1.0.0
	 */
	public function invalid_admin_email_notice() {
		$current_user = wp_get_current_user();

		if (
			false === strpos( $current_user->user_email, '@cmsmasters.net' ) &&
			false === strpos( $current_user->user_email, '@cmsmasters.zendesk.com' )
		) {
			return;
		}

		echo '<div class="notice notice-warning is-dismissible">' .
			'<p>' .
				sprintf(
					__( 'Oops, looks like you tried to use our email: %s', 'cmsmasters-framework' ),
					'<strong>' . $current_user->user_email . '</strong>'
				) .
			'</p>' .
			'<p>' . esc_html__( 'Please enter yours instead.', 'cmsmasters-framework' ) . '</p>' .
		'</div>';
	}

	/**
	 * Render API-driven notices.
	 *
	 * Fetches theme config from API (cached), evaluates conditions,
	 * and renders matching notices dynamically.
	 *
	 * @since 1.0.14
	 */
	public function render_api_notices() {
		$config = API_Requests::get_theme_config();

		if ( empty( $config ) || empty( $config['notices'] ) ) {
			return;
		}

		$token_data = get_option( CMSMASTERS_OPTIONS_PREFIX . 'token_data', array() );
		$token_status = get_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', '' );
		$ee_unavailable = (bool) get_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_theme_unavailable', false );

		$dismissed_permanent = get_option( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices', array() );
		$dismissed_session = get_transient( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices_session' );

		if ( ! is_array( $dismissed_permanent ) ) {
			$dismissed_permanent = array();
		}

		if ( ! is_array( $dismissed_session ) ) {
			$dismissed_session = array();
		}

		foreach ( $config['notices'] as $notice_id => $notice ) {
			// Check expiry.
			if ( ! empty( $notice['expires'] ) && gmdate( 'Y-m-d' ) > $notice['expires'] ) {
				continue;
			}

			// Check conditions.
			if ( ! empty( $notice['conditions'] ) ) {
				$conditions = $notice['conditions'];

				if ( isset( $conditions['source_code'] ) ) {
					$local_source = isset( $token_data['source_code'] ) ? $token_data['source_code'] : '';

					if ( $conditions['source_code'] !== $local_source ) {
						continue;
					}
				}

				if ( isset( $conditions['token_status'] ) ) {
					if ( $conditions['token_status'] !== $token_status ) {
						continue;
					}
				}

				if ( isset( $conditions['ee_theme_unavailable'] ) ) {
					if ( (bool) $conditions['ee_theme_unavailable'] !== $ee_unavailable ) {
						continue;
					}
				}
			}

			// Check dismiss state.
			$dismiss = isset( $notice['dismiss'] ) ? $notice['dismiss'] : false;

			if ( 'permanent' === $dismiss && isset( $dismissed_permanent[ $notice_id ] ) ) {
				continue;
			}

			if ( 'session' === $dismiss && isset( $dismissed_session[ $notice_id ] ) ) {
				continue;
			}

			// Render notice.
			$level = isset( $notice['level'] ) ? $notice['level'] : 'info';
			$text = isset( $notice['text'] ) ? $notice['text'] : '';

			if ( empty( $text ) ) {
				continue;
			}

			$classes = 'notice notice-' . esc_attr( $level ) . ' cmsmasters-api-notice';

			if ( $dismiss ) {
				$classes .= ' is-dismissible';
			}

			$data_attrs = ' data-notice-id="' . esc_attr( $notice_id ) . '"';

			if ( $dismiss ) {
				$data_attrs .= ' data-dismiss-type="' . esc_attr( $dismiss ) . '"';
			}

			echo '<div class="' . $classes . '"' . $data_attrs . '>';
			echo '<p>' . wp_kses_post( $text ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * AJAX handler for dismissing API-driven notices.
	 *
	 * @since 1.0.14
	 */
	public function ajax_dismiss_api_notice() {
		if ( ! check_ajax_referer( 'cmsmasters_framework_admin_nonce', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce', 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'invalid_permissions', 403 );
		}

		$notice_id = isset( $_POST['notice_id'] ) ? sanitize_key( $_POST['notice_id'] ) : '';
		$dismiss_type = isset( $_POST['dismiss_type'] ) ? sanitize_key( $_POST['dismiss_type'] ) : '';

		if ( empty( $notice_id ) || ! in_array( $dismiss_type, array( 'permanent', 'session' ), true ) ) {
			wp_send_json_error( 'invalid_params', 400 );
		}

		if ( 'permanent' === $dismiss_type ) {
			$dismissed = get_option( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices', array() );

			if ( ! is_array( $dismissed ) ) {
				$dismissed = array();
			}

			$dismissed[ $notice_id ] = true;
			update_option( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices', $dismissed, false );
		} elseif ( 'session' === $dismiss_type ) {
			$dismissed = get_transient( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices_session' );

			if ( ! is_array( $dismissed ) ) {
				$dismissed = array();
			}

			$dismissed[ $notice_id ] = true;
			set_transient( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices_session', $dismissed, DAY_IN_SECONDS );
		}

		wp_send_json_success();
	}

	/**
	 * Remove export post_types from wp export.
	 *
	 * @since 1.0.0
	 */
	public function remove_export_post_types( $args, $post_type ) {
		if (
			'acf-field-group' === $post_type ||
			'acf-field' === $post_type ||
			'give_payment' === $post_type ||
			'give_forms' === $post_type
		) {
			$args['can_export'] = false;
		}

		return $args;
	}

	public function add_plugins_list() {
		$plugins_list = get_transient( 'cmsmasters_plugins_list' );

		if ( empty( $plugins_list ) ) {
			$plugins_list = $this->get_api_plugins();

			set_transient( 'cmsmasters_plugins_list', $plugins_list, DAY_IN_SECONDS );
		}

		return $plugins_list;
	}

	/**
	 * Get plugins list from API.
	 *
	 * @since 1.0.0
	 *
	 * @return array Plugins list.
	 */
	private function get_api_plugins() {
		if ( API_Requests::is_empty_token_status() ) {
			return array();
		}

		$data = API_Requests::post_request( 'get-plugins-list', array( 'demo' => Utils::get_demo() ) );

		if ( is_wp_error( $data ) ) {
			Logger::error( $data->get_error_message() );

			return array();
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return array();
		}

		return $data;
	}

	/**
	 * Remove plugins list.
	 *
	 * @since 1.0.0
	 */
	public function remove_plugins_list() {
		delete_transient( 'cmsmasters_plugins_list' );
	}

}
