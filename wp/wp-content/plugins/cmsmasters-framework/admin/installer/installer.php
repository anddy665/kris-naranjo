<?php
namespace CmsmastersFramework\Admin\Installer;

use CmsmastersFramework\Admin\Installer\Merlin\Class_Merlin;
use CmsmastersFramework\Admin\Installer\Importer\Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Installer module.
 *
 * Main class for installer module.
 *
 * @since 1.0.0
 */
class Installer {

	/**
	 * Installer module constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'first_setup' ) );

		new Importer();

		$this->run_wizard();
	}

	/**
	 * First setup actions.
	 *
	 * @since 1.0.0
	 * @since 1.0.7 Fixed elementor container on import.
	 */
	public function first_setup() {
		if ( 'pending' !== get_option( CMSMASTERS_OPTIONS_PREFIX . 'first_setup', 'pending' ) ) {
			return;
		}

		update_option( 'elementor_experiment-container', 'active' );

		do_action( 'cmsmasters_set_backup_options', true );

		do_action( 'cmsmasters_set_import_status', 'pending' );

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'first_setup', 'done', false );
	}

	/**
	 * Require Merlin Installer.
	 *
	 * @since 1.0.0
	 */
	private function run_wizard() {
		$config = array(
			'directory' => 'admin/installer/merlin', // Location / directory where Merlin WP is placed in your theme.
			'merlin_url' => 'merlin', // The wp-admin page slug where Merlin WP loads.
			'parent_slug' => 'themes.php', // The wp-admin parent page slug for the admin menu item.
			'capability' => 'manage_options', // The capability required for this menu to be displayed to the user.
			'child_action_btn_url' => 'https://developer.wordpress.org/themes/advanced-topics/child-themes/', // URL for the 'child-action-link'.
			'dev_mode' => false, // Enable development mode for testing.
			'license_step' => true, // License activation step.
			'license_required' => true, // Require the license activation step.
			'license_help_url' => 'https://docs.cmsmasters.net/blog/how-to-find-your-envato-purchase-code/', // URL for the 'license-tooltip'.
			'ready_big_button_url' => home_url( '/' ), // Link for the big button on the ready step.
		);

		$strings = array(
			'admin-menu' => esc_html__( 'Theme Setup', 'cmsmasters-framework' ),
			/* translators: 1: Title Tag 2: Theme Name 3: Closing Title Tag */
			'title%s%s%s%s' => esc_html__( '%1$s%2$s Themes &lsaquo; Theme Setup: %3$s%4$s', 'cmsmasters-framework' ),
			'return-to-dashboard' => esc_html__( 'Return to the dashboard', 'cmsmasters-framework' ),
			'ignore' => '',

			'btn-skip' => esc_html__( 'Skip', 'cmsmasters-framework' ),
			'btn-next' => esc_html__( 'Next', 'cmsmasters-framework' ),
			'btn-start' => esc_html__( 'Start', 'cmsmasters-framework' ),
			'btn-no' => esc_html__( 'Cancel', 'cmsmasters-framework' ),
			'btn-plugins-install' => esc_html__( 'Install', 'cmsmasters-framework' ),
			'btn-child-install' => esc_html__( 'Install', 'cmsmasters-framework' ),
			'btn-content-install' => esc_html__( 'Install', 'cmsmasters-framework' ),
			'btn-import' => esc_html__( 'Import', 'cmsmasters-framework' ),
			'btn-license-activate' => esc_html__( 'Activate', 'cmsmasters-framework' ),
			'btn-license-skip' => esc_html__( 'Later', 'cmsmasters-framework' ),

			/* translators: Theme Name */
			'license-header%s' => esc_html__( 'Activate %s', 'cmsmasters-framework' ),
			/* translators: Theme Name */
			'license-header-success%s' => esc_html__( '%s license is Activated', 'cmsmasters-framework' ),
			/* translators: Theme Name */
			'license%s' => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'cmsmasters-framework' ),
			'license-label' => esc_html__( 'License key', 'cmsmasters-framework' ),
			'license-success%s' => esc_html__( 'The theme is already registered, so you can go to the next step!', 'cmsmasters-framework' ),
			'license-json-success%s' => esc_html__( 'Your license is activated!', 'cmsmasters-framework' ),
			'license-tooltip' => esc_html__( 'Need help?', 'cmsmasters-framework' ),

			/* translators: Theme Name */
			'welcome-header%s' => esc_html__( 'Welcome to %s', 'cmsmasters-framework' ),
			'welcome-header-success%s' => esc_html__( 'Hi. Welcome back', 'cmsmasters-framework' ),
			'welcome%s' => esc_html__( 'This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'cmsmasters-framework' ),
			'welcome-success%s' => esc_html__( 'You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'cmsmasters-framework' ),

			'child-header' => esc_html__( 'Install Child Theme', 'cmsmasters-framework' ),
			'child-header-success' => esc_html__( 'You\'re good to go!', 'cmsmasters-framework' ),
			'child' => esc_html__( 'Let\'s build & activate a child theme so you may easily make theme changes.', 'cmsmasters-framework' ),
			'child-success%s' => esc_html__( 'Your child theme has already been installed and is now activated, if it wasn\'t already.', 'cmsmasters-framework' ),
			'child-action-link' => esc_html__( 'Learn about child themes', 'cmsmasters-framework' ),
			'child-json-success%s' => esc_html__( 'Awesome. Your child theme has already been installed and is now activated.', 'cmsmasters-framework' ),
			'child-json-already%s' => esc_html__( 'Awesome. Your child theme has been created and is now activated.', 'cmsmasters-framework' ),

			'plugins-header' => esc_html__( 'Install Plugins', 'cmsmasters-framework' ),
			'plugins-header-success' => esc_html__( 'You\'re up to speed!', 'cmsmasters-framework' ),
			'plugins' => esc_html__( 'Let\'s install some essential WordPress plugins to get your site up to speed.', 'cmsmasters-framework' ),
			'plugins-success%s' => esc_html__( 'The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'cmsmasters-framework' ),
			'plugins-action-link' => esc_html__( 'Advanced', 'cmsmasters-framework' ),

			'import-header' => esc_html__( 'Import Content', 'cmsmasters-framework' ),
			'import' => esc_html__( 'Let\'s import content to your website, to help you get familiar with the theme.', 'cmsmasters-framework' ),
			'import-action-link' => esc_html__( 'Advanced', 'cmsmasters-framework' ),

			'ready-header' => esc_html__( 'All done. Have fun!', 'cmsmasters-framework' ),

			/* translators: Theme Author */
			'ready%s' => esc_html__( 'Your theme has been all set up. Enjoy your new theme by %s.', 'cmsmasters-framework' ),
			'ready-action-link' => esc_html__( 'Extras', 'cmsmasters-framework' ),
			'ready-big-button' => esc_html__( 'View your website', 'cmsmasters-framework' ),
			'ready-link-2' => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=go_theme_settings' ), esc_html__( 'Theme Settings', 'cmsmasters-framework' ) ),
		);

		if ( 'cmsmasters' === wp_get_theme()->get( 'Author' ) ) {
			$strings['ready-link-1'] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://cmsmasters.net/', esc_html__( 'Get Theme Support', 'cmsmasters-framework' ) );
		}

		new Class_Merlin( $config, $strings );
	}

}
