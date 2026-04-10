<?php
namespace CmsmastersElementor\Modules\Effects\Components;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Effects\Components\Base\Component_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Effects module Scrolling effects component.
 *
 * @since 1.0.0
 */
class Scroll extends Component_Base {

	private $scroll_effects = array();


	private $speed_control = array();

	private $timing_control = array();

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	public function get_id() {
		return 'scroll';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( 'Scrolling Effects', 'cmsmasters-elementor' );
	}

	protected function set_scroll_vars() {
		$this->scroll_effects = array(
			'vertical' => __( 'Translate Y', 'cmsmasters-elementor' ),
			'horizontal' => __( 'Translate X', 'cmsmasters-elementor' ),
			'rotate' => __( 'Rotate', 'cmsmasters-elementor' ),
			'scale' => __( 'Scale', 'cmsmasters-elementor' ),
			'opacity' => __( 'Opacity', 'cmsmasters-elementor' ),
			'blur' => __( 'Blur', 'cmsmasters-elementor' ),
			'grayscale' => __( 'Grayscale', 'cmsmasters-elementor' ),
			'sepia' => __( 'Sepia', 'cmsmasters-elementor' ),
			'saturate' => __( 'Saturate', 'cmsmasters-elementor' ),
			'brightness' => __( 'Brightness', 'cmsmasters-elementor' ),
			'contrast' => __( 'Contrast', 'cmsmasters-elementor' ),
			'huerotate' => __( 'Hue Rotate', 'cmsmasters-elementor' ),
		);

		if ( $this->background_effect ) {
			unset( $this->scroll_effects['rotate'] );
		}

		$this->speed_control = array(
			'label' => __( 'Level', 'cmsmasters-elementor' ),
			'range' => array(
				'px' => array(
					'min' => 0,
					'max' => 10,
					'step' => 0.5,
				),
			),
			'default' => array( 'size' => 4 ),
		);

		$this->timing_control = array(
			'options' => array(
				'linear' => __( 'Linear', 'cmsmasters-elementor' ),
				'sineIn' => __( 'Ease in Sine', 'cmsmasters-elementor' ),
				'sineOut' => __( 'Ease out Sine', 'cmsmasters-elementor' ),
				'sineInOut' => __( 'Ease in-out Sine', 'cmsmasters-elementor' ),
				'quadIn' => __( 'Ease in Quad', 'cmsmasters-elementor' ),
				'quadOut' => __( 'Ease out Quad', 'cmsmasters-elementor' ),
				'quadInOut' => __( 'Ease in-out Quad', 'cmsmasters-elementor' ),
				'cubicIn' => __( 'Ease in Cubic', 'cmsmasters-elementor' ),
				'cubicOut' => __( 'Ease out Cubic', 'cmsmasters-elementor' ),
				'cubicInOut' => __( 'Ease in-out Cubic', 'cmsmasters-elementor' ),
				'quartIn' => __( 'Ease in Quart', 'cmsmasters-elementor' ),
				'quartOut' => __( 'Ease out Quart', 'cmsmasters-elementor' ),
				'quartInOut' => __( 'Ease in-out Quart', 'cmsmasters-elementor' ),
				'quintIn' => __( 'Ease in Quint', 'cmsmasters-elementor' ),
				'quintOut' => __( 'Ease out Quint', 'cmsmasters-elementor' ),
				'quintInOut' => __( 'Ease in-out Quint', 'cmsmasters-elementor' ),
				'expoIn' => __( 'Ease in Expo', 'cmsmasters-elementor' ),
				'expoOut' => __( 'Ease out Expo', 'cmsmasters-elementor' ),
				'expoInOut' => __( 'Ease in-out Expo', 'cmsmasters-elementor' ),
				'circIn' => __( 'Ease in Circ', 'cmsmasters-elementor' ),
				'circOut' => __( 'Ease out Circ', 'cmsmasters-elementor' ),
				'circInOut' => __( 'Ease in-out Circ', 'cmsmasters-elementor' ),
				'backIn' => __( 'Ease in Back', 'cmsmasters-elementor' ),
				'backOut' => __( 'Ease out Back', 'cmsmasters-elementor' ),
				'backInOut' => __( 'Ease in-out Back', 'cmsmasters-elementor' ),
				'elasticIn' => __( 'Ease in Elastic', 'cmsmasters-elementor' ),
				'elasticOut' => __( 'Ease out Elastic', 'cmsmasters-elementor' ),
				'elasticInOut' => __( 'Ease in-out Elastic', 'cmsmasters-elementor' ),
				'bounceIn' => __( 'Ease in Bounce', 'cmsmasters-elementor' ),
				'bounceOut' => __( 'Ease out Bounce', 'cmsmasters-elementor' ),
				'bounceInOut' => __( 'Ease in-out Bounce', 'cmsmasters-elementor' ),
			),
			'default' => 'expoOut',
		);
	}

	public function register_effect_controls() {
		$this->add_control(
			$this->get_control_name( 'effects' ),
			array(
				'label' => __( 'Choose Effects', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'options' => $this->scroll_effects,
				'multiple' => true,
				'frontend_available' => true,
				'condition' => array( $this->switcher_control => $this->get_id() ),
			)
		);

		foreach ( $this->scroll_effects as $effect_type => $effect_label ) {
			$this->register_single_effect_controls( $effect_type, $effect_label );
		}

		if ( ! $this->background_effect ) {
			$this->register_origin_controls();
		}

		$this->register_advanced_controls();
	}

	private function register_single_effect_controls( $effect_type, $effect_label ) {
		$effect_name = $this->get_id();
		$effects_control = $this->get_control_name( 'effects' );
		$effect_toggle_control = $this->get_control_name( $effect_type );

		$this->add_control(
			$effect_toggle_control,
			array(
				'label' => $effect_label,
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$effects_control => $effect_type,
				),
			)
		);

		$this->start_popover();

		list( $speed_control, $timing_control ) = $this->get_single_effect_parameters( $effect_type );

		$this->add_control(
			$this->get_control_name( "{$effect_type}_speed" ),
			array(
				'label' => $speed_control['label'],
				'type' => Controls_Manager::SLIDER,
				'range' => $speed_control['range'],
				'default' => $speed_control['default'],
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$effects_control => $effect_type,
					$effect_toggle_control => 'yes',
				),
			)
		);

		$this->add_control(
			$this->get_control_name( "{$effect_type}_direction" ),
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'toggle' => false,
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$effects_control => $effect_type,
					$effect_toggle_control => 'yes',
				),
			)
		);

		$this->add_control(
			$this->get_control_name( "{$effect_type}_timing" ),
			array(
				'label' => __( 'Easing Function', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $timing_control['options'],
				'default' => $timing_control['default'],
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$effects_control => $effect_type,
					$effect_toggle_control => 'yes',
				),
			)
		);

		$this->end_popover();
	}

	private function get_single_effect_parameters( $effect_type ) {
		$speed_control = $this->speed_control;
		$timing_control = $this->timing_control;

		switch ( $effect_type ) {
			case 'vertical':
			case 'horizontal':
				$speed_control['label'] = __( 'Speed', 'cmsmasters-elementor' );

				$speed_control['range']['px']['step'] = 0.1;

				$timing_control['default'] = 'linear';

				break;
			case 'rotate':
				$speed_control['label'] = __( 'Speed', 'cmsmasters-elementor' );

				$speed_control['range']['px']['max'] = 18;
				$speed_control['range']['px']['step'] = 0.1;

				$speed_control['default']['size'] = 6;

				$timing_control['default'] = 'linear';

				break;
			case 'scale':
				$speed_control['range']['px']['min'] = -10;
				$speed_control['range']['px']['max'] = 20;

				$timing_control['default'] = 'linear';

				break;
			case 'opacity':
				$speed_control['range']['px']['min'] = 1;
				$speed_control['range']['px']['step'] = 0.1;

				$speed_control['default']['size'] = 10;

				break;
			case 'blur':
				$speed_control['range']['px']['min'] = 1;

				break;
			case 'grayscale':
			case 'sepia':
			case 'huerotate':
				$speed_control['range']['px']['min'] = 1;

				$speed_control['default']['size'] = 10;

				break;
			case 'saturate':
			case 'brightness':
			case 'contrast':
				$speed_control['range']['px']['min'] = -10;

				break;
		}

		return array( $speed_control, $timing_control );
	}

	private function register_origin_controls() {
		$effect_name = $this->get_id();
		$effects_control = $this->get_control_name( 'effects' );

		$origin_control_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => $this->switcher_control,
					'value' => $effect_name,
				),
				array(
					'name' => $effects_control,
					'operator' => '!==',
					'value' => '',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => $effects_control,
							'operator' => 'contains',
							'value' => 'rotate',
						),
						array(
							'name' => $effects_control,
							'operator' => 'contains',
							'value' => 'scale',
						),
					),
				),
			),
		);

		$this->add_control(
			$this->get_control_name( 'origin_heading' ),
			array(
				'label' => __( 'Transform Origin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $origin_control_conditions,
			)
		);

		$this->add_control(
			$this->get_control_name( 'origin_y' ),
			array(
				'label' => __( 'Vertical', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'frontend_available' => true,
				'conditions' => $origin_control_conditions,
			)
		);

		$this->add_control(
			$this->get_control_name( 'origin_x' ),
			array(
				'label' => __( 'Horizontal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'frontend_available' => true,
				'conditions' => $origin_control_conditions,
			)
		);
	}

	private function register_advanced_controls() {
		$effect_name = $this->get_id();
		$effects_control = $this->get_control_name( 'effects' );

		$this->add_control(
			$this->get_control_name( 'viewport' ),
			array(
				'label' => __( 'Effects Viewport', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'sizes' => array(
						'start' => 0,
						'end' => 100,
					),
					'unit' => '%',
				),
				'labels' => array(
					__( 'Bottom', 'cmsmasters-elementor' ),
					__( 'Top', 'cmsmasters-elementor' ),
				),
				'handles' => 'range',
				'scales' => 1,
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					"{$effects_control}!" => array(),
				),
			)
		);

		$this->add_control(
			$this->get_control_name( 'range' ),
			array(
				'label' => __( 'Effects relative to:', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'viewport' => __( 'Viewport', 'cmsmasters-elementor' ),
					'page' => __( 'Entire Page', 'cmsmasters-elementor' ),
				),
				'default' => 'viewport',
				'toggle' => false,
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					"{$effects_control}!" => array(),
				),
			)
		);

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
					'mobile',
				),
				'multiple' => true,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					"{$effects_control}!" => array(),
				),
			)
		);
	}
}
