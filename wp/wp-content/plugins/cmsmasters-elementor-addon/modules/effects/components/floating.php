<?php
namespace CmsmastersElementor\Modules\Effects\Components;

use CmsmastersElementor\Modules\Effects\Components\Base\Component_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Effects module Floating effects component.
 *
 * @since 1.0.0
 */
class Floating extends Component_Base {

	private $effects = array();


	private $units = array();

	private $slider = array();

	private $effect_toggle_args = array();

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	public function get_id() {
		return 'floating';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( 'Floating Effects', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	protected function set_effect_vars() {
		$this->set_effects();

		parent::set_effect_vars();
	}

	private function set_effects() {
		$this->effects = array(
			'translate',
			'rotate',
			'scale',
		);

		if ( $this->background_effect ) {
			$this->effects = array_diff( $this->effects, array( 'rotate' ) );
		}
	}

	public function get_effects() {
		return $this->effects;
	}

	protected function set_floating_vars() {
		$this->switcher_control = $this->get_control_name( '', 'effect_type' );

		$this->units = array(
			'translate' => array(
				'size_units' => array( 'px' ),
				'default' => array( 'unit' => 'px' ),
				'range' => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
						'step' => 5,
					),
				),
			),
			'rotate' => array(
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'tablet_default' => array( 'unit' => 'deg' ),
				'mobile_default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
						'step' => 5,
					),
				),
			),
			'scale' => array(
				'size_units' => array( 'x' ),
				'default' => array( 'unit' => 'x' ),
				'tablet_default' => array( 'unit' => 'x' ),
				'mobile_default' => array( 'unit' => 'x' ),
				'range' => array(
					'x' => array(
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					),
				),
			),
		);

		if ( $this->background_effect ) {
			$this->units['translate'] = array(
				'size_units' => array( '%' ),
				'default' => array( 'unit' => '%' ),
				'range' => array(
					'%' => array(
						'min' => -50,
						'max' => 50,
					),
				),
			);

			$this->units['scale']['range']['x']['min'] = 1;
			$this->units['scale']['range']['x']['max'] = 2;
		}

		$effect_name = $this->get_id();

		$this->slider = array(
			'type' => Controls_Manager::SLIDER,
			'labels' => array(
				__( 'From', 'cmsmasters-elementor' ),
				__( 'To', 'cmsmasters-elementor' ),
			),
			'scales' => 1,
			'handles' => 'range',
			'render_type' => 'ui',
			'frontend_available' => true,
			'condition' => array( $this->switcher_control => $effect_name ),
		);

		$this->effect_toggle_args = array(
			'type' => Controls_Manager::POPOVER_TOGGLE,
			'render_type' => 'ui',
			'frontend_available' => true,
			'condition' => array( $this->switcher_control => $effect_name ),
		);
	}

	public function register_effect_controls() {
		foreach ( $this->get_effects() as $effect ) {
			$this->register_single_effect_controls( $effect );
		}

		$duration_condition_terms = array();

		foreach ( $this->get_effects() as $effect ) {
			$duration_condition_terms[] = array(
				'name' => $this->get_control_name( "{$effect}_toggle" ),
				'value' => 'yes',
			);
		}

		$this->add_control(
			$this->get_control_name( 'duration' ),
			array(
				'label' => __( 'Floating Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'in seconds', 'cmsmasters-elementor' ),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'default' => array(
					'size' => ( $this->background_effect ) ? 3 : 1.5,
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => $this->switcher_control,
							'value' => $this->get_id(),
						),
						array(
							'relation' => 'or',
							'terms' => $duration_condition_terms,
						),
					),
				),
			)
		);
	}

	protected function register_single_effect_controls( $effect ) {
		$effect_name = $this->get_id();
		$toggle = $this->get_control_name( "{$effect}_toggle" );

		$this->add_control(
			$toggle,
			array_replace_recursive( $this->effect_toggle_args, array(
				'label' => $this->effect_labels[ $effect ]['simple'],
			) )
		);

		$this->start_popover();

		$type_exists = in_array( 'none', $this->axis[ $effect ], true );

		if ( $type_exists ) {
			$type = $this->get_control_name( "{$effect}_type" );

			$this->add_control(
				$type,
				array_replace_recursive( $this->effect_type_args, array(
					'render_type' => 'ui',
					'frontend_available' => true,
					'condition' => array(
						$this->switcher_control => $effect_name,
						$toggle => 'yes',
					),
				) )
			);
		}

		foreach ( $this->axis[ $effect ] as $axis ) {
			$name = $effect;
			$label = sprintf( $this->effect_labels[ $effect ]['axis'], strtoupper( $axis ) );

			if ( 'none' === $axis ) {
				$label = $this->effect_labels[ $effect ]['simple'];
			} else {
				$name .= "_{$axis}";
			}

			$control_name = $this->get_control_name( $name );

			$unique_args = array(
				'label' => $label,
				'default' => $this->get_effect_default( $effect ),
				'condition' => array( $toggle => 'yes' ),
			);

			if ( $type_exists ) {
				$unique_args['condition'][ $type ] = ( 'none' === $axis ) ? 'simple' : 'advanced';
			}

			$this->add_control(
				$control_name,
				array_replace_recursive(
					$this->slider,
					$this->units[ $effect ],
					$unique_args
				)
			);
		}

		$this->add_control(
			$this->get_control_name( "{$effect}_delay" ),
			array(
				'label' => __( 'Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'in seconds', 'cmsmasters-elementor' ),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$toggle => 'yes',
				),
			)
		);

		$this->end_popover();
	}

	private function get_effect_default( $effect ) {
		$default = array();

		switch ( $effect ) {
			case 'translate':
				if ( ! $this->background_effect ) {
					$default = array(
						'sizes' => array(
							'from' => 0,
							'to' => 50,
						),
						'unit' => 'px',
					);
				} else {
					$default = array(
						'sizes' => array(
							'from' => -15,
							'to' => 15,
						),
						'unit' => '%',
					);
				}

				break;
			case 'rotate':
				$default = array(
					'sizes' => array(
						'from' => 0,
						'to' => 30,
					),
					'unit' => 'deg',
				);

				break;
			case 'scale':
				$default = array(
					'sizes' => array(
						'from' => 0.8,
						'to' => 1.2,
					),
					'unit' => 'x',
				);

				if ( $this->background_effect ) {
					$default['sizes']['from'] = 1;
				}

				break;
		}

		return $default;
	}
}
