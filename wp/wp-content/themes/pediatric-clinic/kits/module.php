<?php
namespace PediatricClinicSpace\Kits;

use PediatricClinicSpace\Kits\Classes;
use PediatricClinicSpace\Kits\Controls\Controls_Manager;
use PediatricClinicSpace\Kits\Documents\Kit;
use PediatricClinicSpace\Core\Utils\File_Manager;

use Elementor\Core\Base\Module as ElementorBaseModule;
use Elementor\Core\Documents_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon kits module.
 *
 * Addon kits module handler class is responsible for registering and
 * managing Elementor theme styles kits document types.
 */
class Module extends ElementorBaseModule {

	const KIT_NAMESPACE = __NAMESPACE__;

	/**
	 * Module features.
	 *
	 * Holds the module features.
	 *
	 * @var array
	 */
	private $features = array();

	/**
	 * Modifies Elementor default kit design system
	 * global settings.
	 *
	 * @var Classes\Kit_Globals
	 */
	public $kit_globals;

	/**
	 * Enqueue fonts and icons to frontend and editor.
	 *
	 * @var Classes\Frontend
	 */
	public $kit_frontend;

	/**
	 * Controls Manager.
	 *
	 * Holds the plugin controls manager.
	 *
	 * @var Controls_Manager
	 */
	public $controls_manager;

	/**
	 * Kits module class constructor.
	 */
	public function __construct() {
		$this->init_actions();

		$this->controls_manager = new Controls_Manager();

		$this->kit_globals = new Classes\Kit_Globals();

		$this->kit_frontend = new Classes\Frontend();
	}

	/**
	 * Get module name.
	 *
	 * Retrieve the Addon module name.
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'kits';
	}

	/**
	 * Add actions initialization.
	 *
	 * Register action hooks for the module.
	 */
	protected function init_actions() {
		if ( current_user_can( 'manage_options' ) ) {
			// Enqueue kit scripts
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Enqueue kit styles
			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
		}

		// Common
		add_action( 'elementor/documents/register', array( $this, 'register_documents' ), 11 );
	}

	/**
	 * Enqueue kits scripts.
	 *
	 * Load all required kit scripts.
	 *
	 * Fired by `elementor/editor/before_enqueue_scripts` Elementor action hook.
	 */
	public function enqueue_scripts() {
		if ( ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			wp_register_script(
				'selectize',
				File_Manager::get_js_assets_url( 'selectize', 'assets/lib/selectize/js/' ),
				array( 'jquery' ),
				'0.12.6',
				true
			);

			wp_enqueue_script(
				'pediatric-clinic-elementor-kits-controls',
				File_Manager::get_js_assets_url( 'kits-controls' ),
				array(
					'jquery',
					'backbone-marionette',
					'elementor-common',
					'elementor-editor-modules',
					'elementor-editor-document',
					'selectize',
				),
				'1.0.0',
				true
			);
		}

		wp_enqueue_script(
			'pediatric-clinic-elementor-kits',
			File_Manager::get_js_assets_url( 'kits' ),
			array(
				'jquery',
				'backbone-marionette',
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
				'selectize',
			),
			'1.0.0',
			true
		);
	}

	/**
	 * Enqueue kits styles.
	 *
	 * Load all required kits styles.
	 *
	 * Fired by `elementor/editor/after_enqueue_styles` Elementor action hook.
	 */
	public function enqueue_styles() {
		if ( ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			wp_enqueue_style(
				'selectize',
				File_Manager::get_css_assets_url( 'selectize', 'assets/lib/selectize/css/', false ),
				array(),
				'0.12.6'
			);

			wp_enqueue_style(
				'pediatric-clinic-elementor-kits-controls',
				File_Manager::get_css_assets_url( 'kits-controls' ),
				array(),
				'1.0.0',
				'screen'
			);
		}
	}

	/**
	 * Register Elementor library documents.
	 *
	 * Register custom Elementor templates library document types.
	 *
	 * Fired by `elementor/documents/register` action.
	 *
	 * @param Documents_Manager $documents_manager Elementor documents manager.
	 */
	public function register_documents( $documents_manager ) {
		$documents_manager->register_document_type( 'kit', Kit::get_class_full_name() );
	}

}
