<?php
namespace PediatricClinicSpace\Kits\Controls;

use Elementor\Plugin as ElementorPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters controls manager.
 *
 * CMSMasters controls manager handler class is responsible for registering and
 * managing plugin regular controls and the group controls.
 *
 * @final
 */
final class Controls_Manager {

	/**
	 * WP Query control id.
	 */
	const CHOOSE_TEXT = 'choose_text';

	/**
	 * Custom Repeater control id.
	 */
	const CUSTOM_REPEATER = 'custom_repeater';

	/**
	 * Selectize control id.
	 */
	const SELECTIZE = 'selectize';

	private $controls;

	/**
	 * Controls manager constructor.
	 *
	 * Initializing the Addon controls manager.
	 */
	public function __construct() {
		$this->set_controls();

		if ( ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			$this->init_actions();
		}
	}

	private function set_controls() {
		$this->controls = array(
			self::CHOOSE_TEXT,
			self::CUSTOM_REPEATER,
			self::SELECTIZE,
		);
	}

	/**
	 * Register actions.
	 *
	 * Register Addon controls manager init actions.
	 */
	private function init_actions() {
		add_action( 'elementor/controls/controls_registered', array( $this, 'register_controls' ) );
	}

	/**
	 * Register Addon controls.
	 *
	 * This method extends a list of all the supported controls by initializing
	 * each one of appropriate control files.
	 *
	 * Fired by `elementor/controls/controls_registered` Elementor plugin action hook.
	 */
	public function register_controls() {
		$controls_manager = ElementorPlugin::$instance->controls_manager;

		foreach ( $this->controls as $control_id ) {
			$class_name = __NAMESPACE__ . '\Control_' . ucwords( $control_id, '_' );

			$controls_manager->register( new $class_name(), $control_id );
		}
	}

}
