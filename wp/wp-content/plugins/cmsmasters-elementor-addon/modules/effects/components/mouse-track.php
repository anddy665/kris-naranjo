<?php
namespace CmsmastersElementor\Modules\Effects\Components;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Effects\Components\Base\Component_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Effects module Tilt effects component.
 *
 * @since 1.0.0
 */
class Mouse_Track extends Component_Base {

	private $slider = array();

	private $choose = array();

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	public function get_id() {
		return 'mouse_track';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( 'Mouse Track', 'cmsmasters-elementor' );
	}

	protected function set_mouse_track_vars() {
		$effect_name = $this->get_id();

		$this->slider = array(
			'type' => Controls_Manager::SLIDER,
			'frontend_available' => true,
			'condition' => array( $this->switcher_control => $effect_name ),
		);

		$this->choose = array(
			'label_block' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'toggle' => false,
			'condition' => array( $this->switcher_control => $effect_name ),
		);
	}

	/**
	 * Register tilt effect controls.
	 *
	 * Adds different input fields to allow the user to change and customize the tilt effect settings.
	 *
	 * @since 1.0.0
	 * @since 1.6.3 Added `Apply effects on:` control.
	 */
	public function register_effect_controls() {
		$effect_name = $this->get_id();

		$this->add_control(
			$this->get_control_name( 'warning' ),
			array(
				'raw' => __( 'Note: When viewing Mouse Track effects in Safari, some elements may not display correctly due to browser-specific limitations.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->register_effect_basic_controls();

		$this->register_effect_advanced_controls();

		$this->add_control(
			$this->get_control_name( 'devices' ),
			array(
				'label' => __( 'Apply effects on:', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'options' => array(
					'desktop' => __( 'Desktop', 'cmsmasters-elementor' ),
					'tablet' => __( 'Tablet', 'cmsmasters-elementor' ),
					'mobile' => __( 'Mobile', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'desktop',
					'tablet',
				),
				'multiple' => true,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);
	}

	private function register_effect_basic_controls() {
		$this->add_control(
			$this->get_control_name( 'shift' ),
			array_replace_recursive(
				$this->slider,
				array(
					'label' => __( 'Shift Multiplier', 'cmsmasters-elementor' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 5,
							'step' => 0.1,
						),
					),
					'default' => array( 'size' => 2 ),
				),
			)
		);

		$this->add_control(
			$this->get_control_name( 'scale' ),
			array_replace_recursive(
				$this->slider,
				array(
					'label' => __( 'Scale', 'cmsmasters-elementor' ),
					'range' => array(
						'px' => array(
							'min' => 0.5,
							'max' => 2,
							'step' => 0.05,
						),
					),
					'default' => array( 'size' => 1 ),
				)
			)
		);
	}

	private function register_effect_advanced_controls() {
		$effect_name = $this->get_id();

		$this->add_control(
			$this->get_control_name( 'advanced' ),
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->start_popover();

		$this->add_control(
			$this->get_control_name( 'shift_direction' ),
			array_replace_recursive(
				$this->choose,
				array(
					'label' => __( 'Tilt Direction', 'cmsmasters-elementor' ),
					'options' => array(
						'default' => __( 'Default', 'cmsmasters-elementor' ),
						'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
					),
					'default' => ( ! $this->background_effect ) ? 'default' : 'reverse',
					'prefix_class' => 'cmsmasters-tilt-shift-direction-',
				),
			)
		);

		$this->add_control(
			$this->get_control_name( 'speed' ),
			array_replace_recursive(
				$this->slider,
				array(
					'label' => __( 'Transition Speed', 'cmsmasters-elementor' ),
					'description' => __( 'in seconds', 'cmsmasters-elementor' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						),
					),
					'default' => array( 'size' => 1 ),
				)
			)
		);

		$this->add_control(
			$this->get_control_name( 'reset' ),
			array(
				'label' => __( 'Reset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Reset tilt effect on event area exit.', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-tilt-reset-',
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->end_popover();
	}
}
