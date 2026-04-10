<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Base\Base_App;
use CmsmastersElementor\Modules\Settings\Settings_Page;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon `Admin` class.
 *
 * Addon `Admin` class is responsible for loading scripts and
 * styles needed for the plugin admin.
 *
 * @since 1.0.0
 *
 * @see \CmsmastersElementor\Base\Base_App
 */
class Admin extends Base_App {

	/**
	 * Get app name.
	 *
	 * Retrieve the name of the application.
	 *
	 * @since 1.0.0
	 *
	 * @return string App name.
	 */
	public function get_name() {
		return 'cmsmasters-admin';
	}

	/**
	 * Ensure admin settings.
	 *
	 * Ensures that the admin `$settings` member is initialized.
	 *
	 * @since 1.0.0
	 *
	 * @return array Admin settings.
	 */
	protected function get_init_settings() {
		$settings = array(
			'i18n' => array(
				'cmsmasters' => __( 'CMSMasters', 'cmsmasters-elementor' ),
			),
		);

		$settings = array_replace_recursive( parent::get_init_settings(), $settings );

		/**
		 * Admin settings.
		 *
		 * Filters the admin settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Admin settings.
		 */
		$settings = apply_filters( 'cmsmasters_elementor/admin/settings', $settings );

		return $settings;
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Admin app.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Admin app.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'plugin_action_links_' . CMSMASTERS_ELEMENTOR_PLUGIN_BASE, array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		add_filter( 'elementor/finder/categories', array( $this, 'add_finder_categories' ) );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * Load all required admin scripts.
	 *
	 * Fired by `admin_enqueue_scripts` WordPress action hook.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'cmsmasters-elementor-admin',
			$this->get_js_assets_url( 'admin' ),
			array( 'elementor-common' ),
			CMSMASTERS_ELEMENTOR_VERSION,
			true
		);

		$this->print_config( 'cmsmasters-elementor-admin' );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * Load all required admin styles.
	 *
	 * Fired by `admin_enqueue_scripts` WordPress action hook.
	 *
	 * @since 1.0.0
	 * @since 1.18.4 Fixed icons on widgets in Elementor Element manager.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'cmsmasters-icons',
			$this->get_css_assets_url( 'cmsmasters-icons', self::get_lib_src( 'cmsicons/css' ) ),
			array(),
			CMSMASTERS_ELEMENTOR_VERSION
		);

		wp_enqueue_style(
			'cmsmasters-elementor-admin',
			$this->get_css_assets_url( 'admin', null, 'default', true ),
			array(),
			CMSMASTERS_ELEMENTOR_VERSION
		);
	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `'plugin_action_links_' . CMSMASTERS_ELEMENTOR_PLUGIN_BASE` WordPress filter hook.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array Modified array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=' . Settings_Page::PAGE_ID ), __( 'Settings', 'cmsmasters-elementor' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Plugin row meta.
	 *
	 * Adds row meta links to the plugin list table
	 *
	 * Fired by `plugin_row_meta` WordPress filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $plugin_meta An array of the plugin's metadata,
	 * including the version, author, author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file,
	 * relative to the plugins directory.
	 *
	 * @return array An array of plugin row meta links.
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( CMSMASTERS_ELEMENTOR_PLUGIN_BASE !== $plugin_file ) {
			return $plugin_meta;
		}

		/* translators: Plugin name in WordPress admin plugins page */
		$plugin_name = __( 'CMSMasters Elementor Addon', 'cmsmasters-elementor' );
		$view_details_args = array(
			'tab' => 'plugin-information',
			'plugin' => basename( CMSMASTERS_ELEMENTOR__FILE__, '.php' ),
			'TB_iframe' => 'false',
			'width' => '600',
			'height' => '550',
		);

		$addon_meta = array(
			// 'view-details' => sprintf( '<a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s" data-title="%3$s">%4$s</a>',
			// 	esc_url( add_query_arg( $view_details_args, network_admin_url( 'plugin-install.php' ) ) ),
			// 	/* translators: Plugin view details link aria-label attribute. %s: Plugin name */
			// 	esc_attr( sprintf( __( 'More information about %s', 'cmsmasters-elementor' ), $plugin_name ) ),
			// 	esc_attr( $plugin_name ),
			// 	esc_html__( 'View details', 'cmsmasters-elementor' )
			// ),
			'docs' => sprintf( '<a href="%1$s" class="cmsmasters-plugin-docs" aria-label="%2$s" target="_blank">%3$s</a>',
				esc_url( 'https://go.cmsmasters.net/addon-plugin-docs/' ),
				/* translators: Plugin documentation link aria-label attribute. %s: Plugin name */
				esc_attr( sprintf( __( 'View %s Documentation', 'cmsmasters-elementor' ), $plugin_name ) ),
				esc_html__( 'Docs & FAQs', 'cmsmasters-elementor' )
			),
			// 'video' => sprintf( '<a href="%1$s" class="cmsmasters-plugin-video" aria-label="%2$s" target="_blank">%3$s</a>',
			// 	esc_url( 'https://go.cmsmasters.net/addon-plugin-video/' ),
			// 	/* translators: Plugin video tutorial link aria-label attribute. %s: Plugin name */
			// 	esc_attr( sprintf( __( 'View %s Video Tutorials', 'cmsmasters-elementor' ), $plugin_name ) ),
			// 	esc_html__( 'Video Tutorials', 'cmsmasters-elementor' )
			// ),
			'changelog' => sprintf( '<a href="%1$s" class="cmsmasters-plugin-log" aria-label="%2$s" target="_blank">%3$s</a>',
				esc_url( 'https://go.cmsmasters.net/addon-plugin-changelog/' ),
				/* translators: Plugin changelog link aria-label attribute. %s: Plugin name */
				esc_attr( sprintf( __( 'View %s Changelog', 'cmsmasters-elementor' ), $plugin_name ) ),
				esc_html__( 'Changelog', 'cmsmasters-elementor' )
			),
		);

		return array_merge( $plugin_meta, $addon_meta );
	}

	/**
	 * Add Finder categories.
	 *
	 * Extends Elementor Finder by Addon categories.
	 *
	 * Fired by `elementor/finder/categories` Elementor filter hook.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $categories Finder categories.
	 *
	 * @return array Modified Finder categories.
	 */
	public function add_finder_categories( $categories ) {
		$settings_url = self::get_url();

		$categories['settings']['items']['integrations'] = array(
			'title' => __( 'CMSMasters Integrations', 'cmsmasters-elementor' ),
			'icon' => 'integration',
			'url' => $settings_url,
			'keywords' => array(
				'integrations',
				'settings',
				'facebook',
				'twitter',
				'pinterest',
				'google',
				'youtube',
				'vimeo',
				'twitch',
				'soundcloud',
				'dribbble',
				'behance',
				'reddit',
				'weather',
				'elementor',
				'cmsmasters',
			),
		);

		return $categories;
	}

	/**
	 * Get settings page URL.
	 *
	 * Retrieve the URL of the Addon settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings page URL.
	 */
	final public static function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_ID );
	}

}
