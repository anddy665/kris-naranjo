<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as AddonControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon button background control.
 *
 * A base controls group for creating button background controls.
 * Displays fields to define the background color or gradient.
 *
 * @since 1.1.0
 */
class Group_Control_Button_Background extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the group control fields.
	 *
	 * @since 1.1.0
	 *
	 * @var array Group control fields.
	 */
	protected static $fields;

	/**
	 * Background Types.
	 *
	 * Holds all the available background types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private static $background_types;

	/**
	 * Get background control type.
	 *
	 * Retrieve the control type, in this case `background`.
	 *
	 * @since 1.0.0
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return AddonControls::BUTTON_BACKGROUND_GROUP;
	}

	/**
	 * Get background control types.
	 *
	 * Retrieve available background types.
	 *
	 * @since 1.1.0
	 *
	 * @return array Available background types.
	 */
	public static function get_background_types() {
		if ( null === self::$background_types ) {
			self::$background_types = self::get_default_background_types();
		}

		return self::$background_types;
	}

	/**
	 * Get Default background types.
	 *
	 * Retrieve button background control initial types.
	 *
	 * @since 1.1.0
	 *
	 * @return array Default background types.
	 */
	private static function get_default_background_types() {
		return array(
			'color' => array(
				'title' => _x( 'Color', 'Button Background Control', 'cmsmasters-elementor' ),
				'icon' => 'eicon-paint-brush',
			),
			'gradient' => array(
				'title' => _x( 'Gradient', 'Button Background Control', 'cmsmasters-elementor' ),
				'icon' => 'eicon-barcode',
			),
		);
	}

	/**
	 * Init fields.
	 *
	 * Initialize control group fields.
	 *
	 * @since 1.1.0
	 *
	 * @return array Control fields.
	 */
	public function init_fields() {
		$fields = array();

		$fields['background'] = array(
			'label' => _x( 'Background Type', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::CHOOSE,
			'toggle' => false,
			'render_type' => 'ui',
		);

		$fields['color'] = array(
			'label' => _x( 'Background Color', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array(
				'{{SELECTOR}}' => '--button-bg-color: {{VALUE}}; ' .
					'background: var( --button-bg-color );',
			),
			'condition' => array(
				'background' => array(
					'color',
					'gradient',
				),
			),
		);

		$fields['color_stop'] = array(
			'label' => _x( 'Location', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default' => array(
				'unit' => '%',
				'size' => 0,
			),
			'render_type' => 'ui',
			'condition' => array(
				'background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['color_b'] = array(
			'label' => _x( 'Second Background Color', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#f2295b',
			'render_type' => 'ui',
			'condition' => array(
				'background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['color_b_stop'] = array(
			'label' => _x( 'Location', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'default' => array(
				'unit' => '%',
				'size' => 100,
			),
			'render_type' => 'ui',
			'condition' => array(
				'background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['gradient_type'] = array(
			'label' => _x( 'Type', 'Button Background Control', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => AddonControls::CHOOSE_TEXT,
			'options' => array(
				'linear' => _x( 'Linear', 'Button Background Control', 'cmsmasters-elementor' ),
				'radial' => _x( 'Radial', 'Button Background Control', 'cmsmasters-elementor' ),
			),
			'default' => 'linear',
			'render_type' => 'ui',
			'condition' => array(
				'background' => array( 'gradient' ),
			),
			'of_type' => 'gradient',
		);

		$fields['gradient_angle'] = array(
			'label' => _x( 'Angle', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'deg' ),
			'default' => array(
				'unit' => 'deg',
				'size' => 180,
			),
			'range' => array(
				'deg' => array( 'step' => 10 ),
			),
			'selectors' => array(
				'{{SELECTOR}}' => 'background-color: transparent; ' .
					'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
			),
			'condition' => array(
				'background' => array( 'gradient' ),
				'gradient_type' => 'linear',
			),
			'separator' => 'after',
			'of_type' => 'gradient',
		);

		$fields['gradient_position'] = array(
			'label' => _x( 'Position', 'Button Background Control', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'center center' => _x( 'Center Center', 'Button Background Control', 'cmsmasters-elementor' ),
				'center left' => _x( 'Center Left', 'Button Background Control', 'cmsmasters-elementor' ),
				'center right' => _x( 'Center Right', 'Button Background Control', 'cmsmasters-elementor' ),
				'top center' => _x( 'Top Center', 'Button Background Control', 'cmsmasters-elementor' ),
				'top left' => _x( 'Top Left', 'Button Background Control', 'cmsmasters-elementor' ),
				'top right' => _x( 'Top Right', 'Button Background Control', 'cmsmasters-elementor' ),
				'bottom center' => _x( 'Bottom Center', 'Button Background Control', 'cmsmasters-elementor' ),
				'bottom left' => _x( 'Bottom Left', 'Button Background Control', 'cmsmasters-elementor' ),
				'bottom right' => _x( 'Bottom Right', 'Button Background Control', 'cmsmasters-elementor' ),
			),
			'default' => 'center center',
			'selectors' => array(
				'{{SELECTOR}}' => 'background-color: transparent; ' .
					'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
			),
			'condition' => array(
				'background' => array( 'gradient' ),
				'gradient_type' => 'radial',
			),
			'separator' => 'after',
			'of_type' => 'gradient',
		);

		return $fields;
	}

	/**
	 * Get child default args.
	 *
	 * Retrieve the default arguments for all the child controls for a
	 * specific group control.
	 *
	 * @since 1.1.0
	 *
	 * @return array Default arguments for all the child controls.
	 */
	protected function get_child_default_args() {
		return array(
			'types' => array(
				'color',
				'gradient',
			),
			'selector' => '{{WRAPPER}}',
		);
	}

	/**
	 * Filter fields.
	 *
	 * Filter which controls to display, using `include`, `exclude`,
	 * `condition` and `of_type` arguments.
	 *
	 * @since 1.1.0
	 *
	 * @return array Control fields.
	 */
	protected function filter_fields() {
		$fields = parent::filter_fields();

		$args = $this->get_args();

		foreach ( $fields as &$field ) {
			if (
				isset( $field['of_type'] ) &&
				! in_array( $field['of_type'], $args['types'], true )
			) {
				unset( $field );
			}
		}

		return $fields;
	}

	/**
	 * Prepare fields.
	 *
	 * Process button background control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 *
	 * @param array $fields Control group fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		$background_types = self::get_background_types();

		$choose_types = array();

		foreach ( $args['types'] as $type ) {
			if ( isset( $background_types[ $type ] ) ) {
				$choose_types[ $type ] = $background_types[ $type ];
			}
		}

		$fields['background']['options'] = $choose_types;
		$fields['background']['default'] = key( $choose_types );

		return parent::prepare_fields( $fields );
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the button background control.
	 * Used to return the default options while initializing the button background control.
	 *
	 * @since 1.1.0
	 *
	 * @return array Default background control options.
	 */
	protected function get_default_options() {
		return array( 'popover' => false );
	}

}
