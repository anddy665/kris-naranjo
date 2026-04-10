<?php
namespace PediatricClinicSpace\Admin;

use PediatricClinicSpace\Admin\Plugin_Activator\Plugin_Activator;
use PediatricClinicSpace\Admin\Upgrader\Upgrader;
use PediatricClinicSpace\Core\Utils\File_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin modules.
 *
 * Main class for admin modules.
 */
class Admin {

	/**
	 * Admin modules constructor.
	 *
	 * Run modules for admin.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_filter( 'filesystem_method', array( $this, 'filter_filesystem_method' ) );
		add_action( 'admin_init', array( $this, 'set_defaults' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		$this->set_first_theme_version();

		$this->add_notices();

		new Plugin_Activator();

		new Upgrader();
	}

	/**
	 * First setup actions.
	 */
	public function set_defaults() {
		if ( 'pending' !== get_option( 'cmsmasters_pediatric-clinic_set_defaults', 'pending' ) ) {
			return;
		}

		$this->set_settings();

		$this->set_default_data();

		do_action( 'cmsmasters_first_setup' );

		update_option( 'cmsmasters_pediatric-clinic_set_defaults', 'done' );
	}

	/**
	 * Set settings.
	 */
	private function set_settings() {
		update_option( 'default_pingback_flag', 0 );

		update_option( 'elementor_page_title_selector', '.cmsmasters-headline' );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
		update_option( 'elementor_unfiltered_files_upload', 1 );
	}

	/**
	 * Set default data.
	 */
	private function set_default_data() {
		$kits_path = get_parent_theme_file_path( '/theme-config/defaults/default-kits.json' );
		$options_path = get_parent_theme_file_path( '/theme-config/defaults/default-theme-options.json' );

		$kits = File_Manager::get_file_contents( $kits_path );
		$options = File_Manager::get_file_contents( $options_path );

		if ( '' !== $kits && empty( get_option( 'cmsmasters_pediatric-clinic_default_kits' ) ) ) {
			$kits = json_decode( $kits, true );

			update_option( 'cmsmasters_pediatric-clinic_default_kits', $kits );
		}

		if ( '' !== $options && empty( get_option( 'cmsmasters_pediatric-clinic_options' ) ) ) {
			$options = json_decode( $options, true );

			update_option( 'cmsmasters_pediatric-clinic_options', $options );
		}
	}

	/**
	 * Filter filesystem method.
	 */
	public function filter_filesystem_method( $method ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $method;
		}

		return 'direct';
	}

	/**
	 * Enqueue admin assets.
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_style(
			'pediatric-clinic-admin',
			File_Manager::get_css_assets_url( 'admin' ),
			array(),
			'1.0.0',
			'all'
		);
	}

	/**
	 * Set first theme version.
	 */
	private function set_first_theme_version() {
		if ( get_option( 'cmsmasters_pediatric-clinic_version', false ) ) {
			return;
		}

		update_option( 'cmsmasters_pediatric-clinic_version', CMSMASTERS_THEME_VERSION );
	}

	/**
	 * Add admin notices.
	 */
	protected function add_notices() {
		if ( class_exists( 'Cmsmasters_Framework' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'cmsmasters_framework_activation_notice' ) );
	}

	/**
	 * CMSMasters Framework activation notice.
	 */
	public function cmsmasters_framework_activation_notice() {
		$screen = get_current_screen();

		if (
			'about' === $screen->id ||
			(
				isset( $screen->id ) &&
				'appearance_page_tgmpa-install-plugins' === $screen->id
			)
		) {
			return;
		}

		$plugins = get_plugins();

		if ( isset( $plugins['cmsmasters-framework/cmsmasters-framework.php'] ) ) {
			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file ) {
				return;
			}

			echo '<div class="cmsmasters-popup-notice notice notice-error is-dismissible">' .
				'<div class="cmsmasters-popup-notice__inner">
					<p>' .
						sprintf(
							esc_html__( '%s requires CMSMasters Framework to be activate.', 'pediatric-clinic' ),
							'<strong>' . esc_html__( 'The Theme', 'pediatric-clinic' ) . '</strong>'
						) . '&nbsp;&nbsp;&nbsp;' .
						'<a href="' . esc_url( wp_nonce_url(
							self_admin_url( 'plugins.php?action=activate&plugin=cmsmasters-framework/cmsmasters-framework.php&plugin_status=active' ),
							'activate-plugin_cmsmasters-framework/cmsmasters-framework.php'
						) ) . '" class="button button-primary">' . esc_html__( 'Activate', 'pediatric-clinic' ) . '</a>' .
					'</p>
				</div>
			</div>';
		} else {
			$public_plugins_list = get_transient( 'cmsmasters_public_plugins_list' );

			if ( ! empty( $public_plugins_list ) && is_array( $public_plugins_list ) ) {
				if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file ) {
					return;
				}

				echo '<div class="cmsmasters-popup-notice notice notice-error is-dismissible">' .
					'<div class="cmsmasters-popup-notice__inner">
						<p>' .
							sprintf(
								esc_html__( '%s requires CMSMasters Framework to be activate.', 'pediatric-clinic' ),
								'<strong>' . esc_html__( 'The Theme', 'pediatric-clinic' ) . '</strong>'
							) . '&nbsp;&nbsp;&nbsp;' .
							'<a href="' . esc_url( wp_nonce_url(
								self_admin_url( 'themes.php?page=tgmpa-install-plugins&plugin=cmsmasters-framework&tgmpa-install=install-plugin&tgmpa-activate=true' ),
								'tgmpa-install',
								'tgmpa-nonce'
							) ) . '" class="button button-primary">' . esc_html__( 'Install', 'pediatric-clinic' ) . '</a>' .
						'</p>
					</div>
				</div>';
			} elseif ( is_numeric( $public_plugins_list ) ) {
				echo '<div class="notice notice-error is-dismissible">
					<p>' .
						sprintf(
							esc_html__( '%2$sGitHub is currently unavailable%3$s%1$s%1$s
							Your theme requires the %2$sCMSMasters Framework plugin%3$s, which is essential for the theme\'s core functionality.%1$s%1$s
							At the moment, we\'re unable to connect to GitHub to download the plugin, as the server is not responding.%1$s%1$s
							We\'ll automatically try again in 10 minutes. Please wait until then and the installation will resume.%1$s%1$s
							If you need assistance, %4$s', 'pediatric-clinic' ),
							'<br />',
							'<strong>',
							'</strong>',
							'<a href="https://cmsmasters.studio/help-center/" target="_blank">' . esc_html__( 'please contact our support team', 'pediatric-clinic' ) . '</a>'
						) .
					'</p>
				</div>';
			} elseif ( 'failed_format' === $public_plugins_list ) {
				echo '<div class="notice notice-error is-dismissible">
					<p>' .
						sprintf(
							esc_html__( '%2$sUnexpected plugin data format%3$s%1$s%1$s
							Your theme requires the %2$sCMSMasters Framework plugin%3$s, which is essential for the theme\'s core functionality.%1$s%1$s
							At the moment, the plugin could not be loaded due to a data format error.%1$s
							Please contact our %4$s%1$s%1$s
							You can retry the installation manually in 10 minutes.', 'pediatric-clinic' ),
							'<br />',
							'<strong>',
							'</strong>',
							'<a href="https://cmsmasters.studio/help-center/" target="_blank">' . esc_html__( 'support team for assistance', 'pediatric-clinic' ) . '</a>'
						) .
					'</p>
				</div>';
			}
		}
	}

	/**
	 * CMSMasters Framework failed response notice.
	 */
	public function cmsmasters_framework_failed_response_notice() {
		$plugins = get_plugins();

		if ( isset( $plugins['cmsmasters-framework/cmsmasters-framework.php'] ) ) {
			return;
		}
		$public_plugins_list = get_transient( 'cmsmasters_public_plugins_list' );

		if ( ! empty( $public_plugins_list ) && is_array( $public_plugins_list ) ) {
			return;
		}

		if ( is_numeric( $public_plugins_list ) ) {
			echo '<div class="notice notice-error is-dismissible">
				<p>
					<strong>' . esc_html__( 'GitHub is currently unavailable.', 'pediatric-clinic' ) . '</strong><br />' .
					esc_html__( 'We were unable to retrieve the plugin list because GitHub was not responding. Please try again in 10 minutes.', 'pediatric-clinic' ) .
				'</p>
			</div>';
		}

		if ( 'failed_format' === $public_plugins_list ) {
			echo '<div class="notice notice-error is-dismissible">
				<p>
					<strong>' . esc_html__( 'Unexpected plugin data format.', 'pediatric-clinic' ) . '</strong><br />' .
					sprintf(
						esc_html__( 'The plugin list could not be loaded due to a data format error. Please contact our %s. You can retry the installation manually in 10 minutes.', 'pediatric-clinic' ),
						'<a href="https://cmsmasters.studio/help-center/" target="_blank">' . esc_html__( 'support team', 'pediatric-clinic' ) . '</a>'
					) .
				'</p>
			</div>';
		}
	}

}
