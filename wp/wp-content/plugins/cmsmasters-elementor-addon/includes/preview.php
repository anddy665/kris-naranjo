<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Base\Base_App;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon `Preview` class.
 *
 * Addon `Preview` class is responsible for loading scripts and
 * styles needed for the plugin editor-preview.
 *
 * @since 1.0.0
 *
 * @see \CmsmastersElementor\Base\Base_App
 */
class Preview extends Base_App {

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
		return 'cmsmasters-preview';
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Preview app.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue editor-preview scripts.
	 *
	 * Load all required editor-preview scripts.
	 *
	 * Fired by `elementor/preview/enqueue_scripts` Elementor action hook.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'cmsmasters-elementor-preview',
			$this->get_js_assets_url( 'preview' ),
			array( 'elementor-frontend' ),
			CMSMASTERS_ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * Enqueue editor-preview styles.
	 *
	 * Load all required editor-preview styles.
	 *
	 * Fired by `elementor/preview/enqueue_styles` Elementor action hook.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'cmsmasters-elementor-preview',
			$this->get_css_assets_url( 'preview', null, 'default', true ),
			array(),
			CMSMASTERS_ELEMENTOR_VERSION
		);
	}

}
