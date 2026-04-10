<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Admin;
use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Editor;
use CmsmastersElementor\Frontend;
use CmsmastersElementor\Modules_Manager;
use CmsmastersElementor\Preview;
use CmsmastersElementor\Tags_Manager;
use CmsmastersElementor\Traits\Singleton;
use CmsmastersElementor\Upgrader\Upgrader;

use Elementor\Core\Base\Document;
use Elementor\Plugin as ElementorPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor Addon plugin.
 *
 * The main plugin handler class is responsible for initializing Addon.
 * The class registers all the components required for the plugin.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Instantiate singleton trait.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @var array $_instances Array with instance of the class.
	 * @method object instance() Single instance of the class.
	 */
	use Singleton;

	/**
	 * Editor.
	 *
	 * Holds the plugin editor.
	 *
	 * @since 1.0.0
	 *
	 * @var Editor
	 */
	public $editor;

	/**
	 * Frontend.
	 *
	 * Holds the plugin frontend.
	 *
	 * @since 1.0.0
	 *
	 * @var Frontend
	 */
	public $frontend;

	/**
	 * Preview.
	 *
	 * Holds the plugin preview.
	 *
	 * @since 1.0.0
	 *
	 * @var Preview
	 */
	public $preview;

	/**
	 * Controls Manager.
	 *
	 * Holds the plugin controls manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Controls_Manager
	 */
	public $controls_manager;

	/**
	 * Modules Manager.
	 *
	 * Holds the plugin modules manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Modules_Manager
	 */
	public $modules_manager;

	/**
	 * Tags Manager.
	 *
	 * Holds the plugin tags manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Tags_Manager
	 */
	public $tags_manager;

	/**
	 * Admin.
	 *
	 * Holds the plugin admin.
	 *
	 * @since 1.0.0
	 *
	 * @var Admin
	 */
	public $admin;

	/**
	 * Upgrader.
	 *
	 * Holds the plugin upgrader.
	 *
	 * @since 1.7.4
	 *
	 * @var Upgrader
	 */
	public $upgrader;

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
	 * @since 1.0.0
	 *
	 * @return ElementorPlugin Elementor plugin instance.
	 */
	public static function elementor() {
		return ElementorPlugin::$instance;
	}

	/**
	 * Main class constructor.
	 *
	 * Constructs the Addon main Plugin class.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_actions();
		$this->init_filters();
	}

	/**
	 * Add plugin init actions.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Added kit globals injection.
	 */
	private function init_actions() {
		add_action( 'elementor/init', array( $this, 'init_components' ) );

		add_action( 'elementor/document/save_version', array( $this, 'save_version' ) );

		add_action( 'cmsmasters_elementor/documents/kit/before_register_addon_kit_controls', array( $this, 'kit_globals_injection' ) );
	}

	/**
	 * Add plugin init filters.
	 *
	 * @since 1.0.0
	 */
	private function init_filters() {
		add_filter( 'elementor/template_library/sources/local/register_post_type_args', array( $this, 'elementor_templates_no_export' ) );
	}

	/**
	 * Init components.
	 *
	 * Initialize Addon components & runs init action.
	 *
	 * Fired by `elementor/init` Elementor action hook.
	 *
	 * @since 1.0.0
	 */
	public function init_components() {
		$this->editor = new Editor();
		$this->frontend = new Frontend();
		$this->preview = new Preview();

		$this->controls_manager = new Controls_Manager();
		$this->modules_manager = new Modules_Manager();
		$this->tags_manager = new Tags_Manager();

		if ( is_admin() ) {
			$this->admin = new Admin();
		}

		$this->upgrader = new Upgrader();

		/**
		 * CMSMasters Elementor Addon init.
		 *
		 * Fires on Elementor init, after Elementor has finished loading but
		 * before any headers are sent.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/init' );
	}

	/**
	 * Document version save.
	 *
	 * Save Addon version on Elementor version save.
	 *
	 * Fired by `elementor/document/save_version` Elementor action hook.
	 *
	 * @since 1.0.0
	 *
	 * @param Document $document Elementor base document.
	 */
	public function save_version( $document ) {
		$document->update_meta( '_cmsmasters_elementor_version', CMSMASTERS_ELEMENTOR_VERSION );
	}

	/**
	 * Elementor templates no export.
	 *
	 * Disable Elementor Templates post type export.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Elementor Templates post type arguments.
	 *
	 * @return array Modified Elementor Templates post type arguments.
	 */
	public function elementor_templates_no_export( $args ) {
		$args['can_export'] = false;

		return $args;
	}

	/**
	 * Kit globals injection.
	 *
	 * @since 1.17.3
	 *
	 * @param object $document Elementor Kit document.
	 */
	public function kit_globals_injection( $document ) {
		$document->start_injection( array( 'of' => 'space_between_widgets' ) );

		$document->update_control(
			'space_between_widgets',
			array(
				'default' => array(
					'row' => '',
					'column' => '',
					'unit' => 'px',
				),
			),
			array(
				'recursive' => true,
			)
		);

		$document->end_injection();
	}

}
