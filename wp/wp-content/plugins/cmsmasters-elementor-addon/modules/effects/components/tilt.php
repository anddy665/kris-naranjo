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
class Tilt extends Component_Base {

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
		return 'tilt';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( '3D Tilt', 'cmsmasters-elementor' );
	}

	protected function set_tilt_vars() {
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
				'raw' => __( 'Note: When viewing 3D Tilt effects in Safari, some elements may not display correctly due to browser-specific limitations.', 'cmsmasters-elementor' ),
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
		if ( ! $this->background_effect ) {
			$this->add_control(
				$this->get_control_name( 'angle' ),
				array_replace_recursive(
					$this->slider,
					array(
						'label' => __( 'Max Tilt Angle', 'cmsmasters-elementor' ),
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 60,
							),
						),
						'default' => array( 'size' => 0 ),
					)
				)
			);
		}

		$this->add_control(
			$this->get_control_name( 'axis' ),
			array_replace_recursive(
				$this->choose,
				array(
					'label' => __( 'Axis', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'both' => array(
							'title' => __( 'Transform element on both axis.', 'cmsmasters-elementor' ),
							'icon' => 'eicon-cursor-move',
						),
						'x' => array(
							'title' => __( 'Transform element only on X axis.', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-stretch',
						),
						'y' => array(
							'title' => __( 'Transform element only on Y axis.', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-stretch',
						),
					),
					'default' => 'both',
					'prefix_class' => 'cmsmasters-tilt-axis-',
				)
			)
		);

		if ( ! $this->background_effect ) {
			$this->add_control(
				$this->get_control_name( 'event_area' ),
				array_replace_recursive(
					$this->choose,
					array(
						'label' => __( 'Event Area', 'cmsmasters-elementor' ),
						'options' => array(
							'element' => __( 'Element', 'cmsmasters-elementor' ),
							'window' => __( 'Window', 'cmsmasters-elementor' ),
						),
						'default' => 'window',
						'prefix_class' => 'cmsmasters-tilt-event-area-',
					)
				)
			);
		}

		$shift_args = array();

		if ( ! $this->background_effect ) {
			$shift_args = array(
				'condition' => array( $this->get_control_name( 'event_area' ) => 'window' ),
			);
		}

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
					'default' => array( 'size' => 0.3 ),
				),
				$shift_args
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

		if ( ! $this->background_effect ) {
			$this->add_control(
				$this->get_control_name( 'direction' ),
				array_replace_recursive(
					$this->choose,
					array(
						'label' => __( 'Tilt Direction', 'cmsmasters-elementor' ),
						'options' => array(
							'default' => __( 'Default', 'cmsmasters-elementor' ),
							'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
						),
						'default' => 'reverse',
						'prefix_class' => 'cmsmasters-tilt-direction-',
					)
				)
			);
		}

		$shift_condition = array();

		if ( ! $this->background_effect ) {
			$shift_condition = array(
				'condition' => array( $this->get_control_name( 'event_area' ) => 'window' ),
			);
		}

		$this->add_control(
			$this->get_control_name( 'shift_direction' ),
			array_replace_recursive(
				$this->choose,
				array(
					'label' => __( 'Shift Direction', 'cmsmasters-elementor' ),
					'options' => array(
						'default' => __( 'Default', 'cmsmasters-elementor' ),
						'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
					),
					'default' => ( ! $this->background_effect ) ? 'default' : 'reverse',
					'prefix_class' => 'cmsmasters-tilt-shift-direction-',
				),
				$shift_condition
			)
		);

		if ( ! $this->background_effect ) {
			$this->add_control(
				$this->get_control_name( 'glare' ),
				array_replace_recursive(
					$this->slider,
					array(
						'label' => __( 'Glare', 'cmsmasters-elementor' ),
						'description' => __( 'Tilt glare layer opacity.', 'cmsmasters-elementor' ),
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 1,
								'step' => 0.05,
							),
						),
					)
				)
			);
		}

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

		if ( ! $this->background_effect ) {
			$this->add_control(
				$this->get_control_name( 'perspective' ),
				array(
					'label' => __( 'Perspective', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::NUMBER,
					'description' => __( 'Transform perspective (in pixels), the lower the more extreme the tilt gets.', 'cmsmasters-elementor' ),
					'min' => 0,
					'max' => 3000,
					'step' => 100,
					'frontend_available' => true,
					'condition' => array( $this->switcher_control => $effect_name ),
				)
			);
		}

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
