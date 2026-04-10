<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Base\Base_App;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Common App.
 *
 * Addon common app that groups shared functionality,
 * components and configuration.
 *
 * @since 1.0.0
 */
class Common extends Base_App {

	private $templates = array();

	/**
	 * Get name.
	 *
	 * Retrieve the app name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Common app name.
	 */
	public function get_name() {
		return 'common';
	}

	/**
	 * Init actions.
	 *
	 * Initialize app actions.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		// Editor
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/editor/footer', array( $this, 'print_templates' ) );

		// Admin
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'admin_footer', array( $this, 'print_templates' ) );

		// Frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 9 );
		add_action( 'wp_footer', array( $this, 'print_templates' ) );
	}

	/**
	 * Init filters.
	 *
	 * Initialize app filters.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/app/settings', array( $this, 'get_app_settings' ) );
	}

	/**
	 * Register scripts.
	 *
	 * Register common scripts.
	 *
	 * @access public
	 */
	public function register_scripts() {
		// wp_enqueue_script(
		// 	'elementor-common',
		// 	$this->get_js_assets_url( 'common' ),
		// 	[
		// 		'jquery',
		// 		'jquery-ui-draggable',
		// 		'backbone-marionette',
		// 		'backbone-radio',
		// 		'elementor-common-modules',
		// 		'elementor-dialog',
		// 		'wp-api-request',
		// 	],
		// 	ELEMENTOR_VERSION,
		// 	true
		// );

		$this->print_config();

		// Used for external plugins.
		do_action( 'cmsmasters_elementor/common/after_register_scripts', $this );
	}

	/**
	 * Register styles.
	 *
	 * Register common styles.
	 *
	 * @since 1.0.0
	 */
	public function register_styles() {
		// wp_register_style(
		// 	'cmsmasters-icons',
		// 	$this->get_css_assets_url( 'cmsmasters-icons', 'assets/lib/icons/css/' ),
		// 	array(),
		// 	'1.0.0'
		// );
	}

	/**
	 * Print Templates
	 *
	 * Prints all registered templates.
	 *
	 * @since 1.0.0
	 */
	public function print_templates() {
		foreach ( $this->templates as $template ) {
			echo $template;
		}
	}

	/**
	 * Get app settings.
	 *
	 * Define the default/initial settings of the common app.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_app_settings( $settings ) {
		return array_merge_recursive( $settings, array(
			'activeComponents' => array_keys( $this->get_components() ),
			'urls' => array( 'rest' => get_rest_url() ),
		) );
	}

}
