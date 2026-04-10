<?php
namespace CmsmastersElementor\Modules\Effects\Components\Base;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Stack;
use Elementor\Sub_Controls_Stack;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon effects module component base.
 *
 * @since 1.0.0
 */
abstract class Component_Base extends Sub_Controls_Stack {

	protected $background_effect = false;

	protected $transform_element;

	protected $transform_wrapper;

	protected $transform_hover_wrapper;


	protected $switcher_control;


	protected $axis = array();

	protected $effect_labels = array();

	protected $effect_type_args = array();

	/**
	 * Constructor.
	 *
	 * Initializing the base class by setting parent stack.
	 *
	 * @param Controls_Stack $parent
	 * @param string $transform_element
	 *
	 * @since 1.8.0 Changed selector for widgets with popup container.
	 */
	public function __construct( $parent, $transform_element, $background_effect ) {
		parent::__construct( $parent );

		$this->background_effect = $background_effect;

		$this->transform_element = $transform_element;

		$exclusion_widgets = array(
			'.elementor-widget-cmsmasters-offcanvas' => '.elementor-widget-cmsmasters-offcanvas__trigger',
			'.cmsmasters-search-type-search-popup' => '.elementor-widget-cmsmasters-search__popup-trigger-inner',
		);

		if ( 'common' === $this->parent->get_unique_name() || 'common-optimized' === $this->parent->get_unique_name() ) {
			$exclusions = '{{WRAPPER}}';
			$exclusion_widget = '';
			$exclusion_hover_widget = '';

			foreach ( $exclusion_widgets as $widget => $triger ) {
				$exclusions .= ':not(' . $widget . ')';
				$exclusion_widget .= ', {{WRAPPER}}' . $widget . ' ' . $triger;
				$exclusion_hover_widget .= ', {{WRAPPER}}' . $widget . ' ' . $triger . ':hover';
			}

			$this->transform_wrapper = $exclusions . $exclusion_widget . ', {{WRAPPER}} .elementor-widget-wrap';

			$this->transform_hover_wrapper = $exclusions . ':hover' . $exclusion_hover_widget;
		} else {
			$this->transform_wrapper = "{{WRAPPER}}:not(.e-con) {$transform_element}, {{WRAPPER}}.e-con";

			$this->transform_hover_wrapper = "{{WRAPPER}}:not(.e-con):hover {$transform_element}, {{WRAPPER}}.e-con:hover";
		}

		if ( 'column' === $this->parent->get_unique_name() ) {
			$this->transform_wrapper = "{{WRAPPER}}.elementor-column > .elementor-widget-wrap";

			$this->transform_hover_wrapper = "{{WRAPPER}}.elementor-column:hover > .elementor-widget-wrap";
		}

		$this->switcher_control = $this->get_control_name( '', 'effect_type' );

		$this->set_effect_vars();

		$this->register_effect_controls();
	}

	/**
	 * Set effect vars.
	 *
	 * Sets effect global variables.
	 *
	 * @since 1.0.0
	 */
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
			'opacity' => array(
				'simple' => __( 'Opacity', 'cmsmasters-elementor' ),
				/* translators: Addon effects module opacity effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Opacity %s', 'cmsmasters-elementor' ),
			),
			'origin' => array(
				'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
				'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
				'simple' => __( 'Transform Origin', 'cmsmasters-elementor' ),
				/* translators: Addon effects module transform origin axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Transform Origin %s', 'cmsmasters-elementor' ),
			),
			'blur' => array(
				'simple' => __( 'Blur', 'cmsmasters-elementor' ),
				/* translators: Addon effects module blur effect axis controls label. %s: Axis (uppercase) */
				'axis' => __( 'Blur %s', 'cmsmasters-elementor' ),
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

	abstract protected function register_effect_controls();

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
	 * Start controls tabs.
	 *
	 * Used to add a new set of tabs inside a section.
	 *
	 * @param string $id Tabs ID.
	 * @param array $args Tabs arguments.
	 */
	public function start_controls_tabs( $id, array $args = array() ) {
		$this->parent->start_controls_tabs( $this->get_control_id( $id ), $args );
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
