<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager;

use Elementor\Controls_Manager as ElementorControls;
use Elementor\Group_Control_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addon colors control.
 *
 * A control for creating colors control. Displays input fields to define colors.
 *
 * @since 1.0.0
 */
class Group_Control_Colors extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the colors control fields.
	 *
	 * @since 1.0.0
	 */
	protected static $fields;

	/**
	 * Get colors control type.
	 *
	 * Retrieve the control type, in this case `colors`.
	 *
	 * @since 1.0.0
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return Controls_Manager::COLORS_GROUP;
	}

	/**
	 * Init fields.
	 *
	 * Initialize colors control fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Control fields.
	 */
	protected function init_fields() {
		$controls = array();

		// Controls Normal Colors
		$controls['normal_first_color'] = array(
			'label' => __( 'Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'color: {{VALUE}}',
			),
		);

		$controls['normal_second_color'] = array(
			'label' => __( 'Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'color: {{VALUE}}',
			),
		);

		$controls['normal_third_color'] = array(
			'label' => __( 'Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'color: {{VALUE}}',
			),
		);

		// Controls Normal Background Colors
		$controls['normal_first_bg_color'] = array(
			'label' => __( 'Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'background-color: {{VALUE}}',
			),
		);

		$controls['normal_second_bg_color'] = array(
			'label' => __( 'Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'background-color: {{VALUE}}',
			),
		);

		$controls['normal_third_bg_color'] = array(
			'label' => __( 'Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}' => 'background-color: {{VALUE}}',
			),
		);

		// Controls Hover Colors
		$controls['hover_first_color'] = array(
			'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'color: {{VALUE}}',
			),
		);

		$controls['hover_second_color'] = array(
			'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'color: {{VALUE}}',
			),
		);

		$controls['hover_third_color'] = array(
			'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'color: {{VALUE}}',
			),
		);

		// Controls Hover Background Colors
		$controls['hover_first_bg_color'] = array(
			'label' => __( 'Hover Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'background-color: {{VALUE}}',
			),
		);

		$controls['hover_second_bg_color'] = array(
			'label' => __( 'Hover Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'background-color: {{VALUE}}',
			),
		);

		$controls['hover_third_bg_color'] = array(
			'label' => __( 'Hover Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}:hover' => 'background-color: {{VALUE}}',
			),
		);

		// Controls Active Colors
		$controls['active_first_color'] = array(
			'label' => __( 'Active Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'color: {{VALUE}}',
			),
		);

		$controls['active_second_color'] = array(
			'label' => __( 'Active Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'color: {{VALUE}}',
			),
		);

		$controls['active_third_color'] = array(
			'label' => __( 'Active Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'color: {{VALUE}}',
			),
		);

		// Controls Active Background Colors
		$controls['active_first_bg_color'] = array(
			'label' => __( 'Active Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'background-color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'background-color: {{VALUE}}',
			),
		);

		$controls['active_second_bg_color'] = array(
			'label' => __( 'Active Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'background-color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'background-color: {{VALUE}}',
			),
		);

		$controls['active_third_bg_color'] = array(
			'label' => __( 'Active Background Color', 'cmsmasters-elementor' ),
			'type' => ElementorControls::COLOR,
			'selectors' => array(
				'{{SELECTOR}}[class*=" elementor-active"].active' => 'background-color: {{VALUE}}',
				'{{SELECTOR}}[class*=" elementor-active"].active:hover' => 'background-color: {{VALUE}}',
			),
		);

		// Controls Transition
		$controls['hover_transition'] = array(
			'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
			'type' => ElementorControls::SLIDER,
			'default' => array(
				'size' => 0.3,
			),
			'range' => array(
				'px' => array(
					'max' => 3,
					'step' => 0.1,
				),
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array(
				'{{SELECTOR}}' => 'transition: color {{hover_transition.SIZE}}s, background-color {{hover_transition.SIZE}}s',
			),
		);

		return $controls;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the CSS filter control. Used to return the
	 * default options while initializing the CSS filter control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default CSS filter control options.
	 */
	protected function get_default_options() {
		return array(
			'popover' => array(
				'starter_name' => 'colors',
				'starter_title' => __( 'Colors', 'cmsmasters-elementor' ),
				'settings' => array(
					'render_type' => 'ui',
				),
			),
		);
	}

}
