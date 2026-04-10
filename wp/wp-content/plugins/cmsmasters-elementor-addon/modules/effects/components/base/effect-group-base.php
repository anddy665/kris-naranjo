<?php
namespace CmsmastersElementor\Modules\Effects\Components\Base;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon effects module component base.
 *
 * @since 1.0.0
 */
abstract class Effect_Group_Base extends Group_Control_Base {

	protected static $fields;

	protected $background_effect = false;

	protected $transform_element;

	protected $transform_wrapper;

	protected $transform_hover_wrapper;


	protected $switcher_control;


	protected $axis = array();

	protected $effect_labels = array();

	protected $effect_type_args = array();

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	abstract public function get_id();

	/**
	 * Constructor.
	 *
	 * Initializing the base class by setting parent stack.
	 *
	 * @param string $transform_element
	 */
	public function __construct( $transform_element, $background_effect ) {
		$this->background_effect = $background_effect;

		$this->transform_element = $transform_element;
		$this->transform_wrapper = "{{WRAPPER}} {$transform_element}";
		$this->transform_hover_wrapper = "{{WRAPPER}}:hover {$transform_element}";

		$this->switcher_control = $this->get_control_name( '', 'effect_type' );

		$this->set_effect_vars();
	}

	protected function set_effect_vars() {
		$this->axis = array(
			'translate' => array(
				'x',
				'y',
			),
			'rotate' => array(
				'none',
				'z',
				'x',
				'y',
			),
			'scale' => array(
				'none',
				'x',
				'y',
			),
			'skew' => array(
				'x',
				'y',
			),
			'origin' => array(
				'vertical',
				'horizontal',
				'x',
				'y',
				'z',
			),
		);

		$this->effect_labels = array(
			'translate' => array(
				'simple' => __( 'Translate', 'cmsmasters-elementor' ),
				/* translators: Addon effects module translate effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Translate %s', 'cmsmasters-elementor' ),
			),
			'rotate' => array(
				'simple' => __( 'Rotate', 'cmsmasters-elementor' ),
				/* translators: Addon effects module rotate effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Rotate %s', 'cmsmasters-elementor' ),
			),
			'scale' => array(
				'simple' => __( 'Scale', 'cmsmasters-elementor' ),
				/* translators: Addon effects module scale effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Scale %s', 'cmsmasters-elementor' ),
			),
			'skew' => array(
				'simple' => __( 'Skew', 'cmsmasters-elementor' ),
				/* translators: Addon effects module skew effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Skew %s', 'cmsmasters-elementor' ),
			),
			'origin' => array(
				'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
				'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
				'simple' => __( 'Transform Origin', 'cmsmasters-elementor' ),
				/* translators: Addon effects module transform origin axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Transform Origin %s', 'cmsmasters-elementor' ),
			),
		);

		$this->effect_type_args = array(
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'simple' => array(
					'title' => __( 'Simple', 'cmsmasters-elementor' ),
					'description' => __( 'Simple customizing values', 'cmsmasters-elementor' ),
				),
				'advanced' => array(
					'title' => __( 'Advanced', 'cmsmasters-elementor' ),
					'description' => __( 'Advanced customizing values', 'cmsmasters-elementor' ),
				),
			),
			'default' => 'simple',
		);

		$name = str_replace( '-', '_', $this->get_id() );
		$method = "set_{$name}_vars";

		if ( method_exists( $this, $method ) ) {
			call_user_func( array( $this, $method ) );
		}
	}

	protected function get_control_name( $name = '', $custom_suffix = false ) {
		$current_suffix = ( $custom_suffix ) ? $custom_suffix : $this->get_id();

		if ( $this->background_effect ) {
			$current_suffix = "bg_{$current_suffix}";
		}

		$suffix = str_replace( '-', '_', $current_suffix );

		$control_name = "cms_{$suffix}";

		if ( ! empty( $name ) ) {
			$control_name .= "_{$name}";
		}

		return $control_name;
	}

	protected function get_control_css_var( $name = '' ) {
		$suffix = $this->get_id();

		$control_name = "--cmsmasters-{$suffix}";

		if ( ! empty( $name ) ) {
			$control_name .= "-{$name}";
		}

		return $control_name;
	}

	/**
	 * Start popover.
	 *
	 * Used to add a new set of controls in a popover.
	 *
	 * @since 1.0.0
	 */
	public function start_popover() {
		$this->parent->start_popover();
	}

	/**
	 * End popover.
	 *
	 * Used to close an existing open popover.
	 *
	 * @since 1.0.0
	 */
	public function end_popover() {
		$this->parent->end_popover();
	}
}
