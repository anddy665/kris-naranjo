<?php
namespace CmsmastersElementor\Modules\Effects\Components;

use CmsmastersElementor\Modules\Effects\Components\Base\Component_Base;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as Breakpoints_Manager;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Effects module Transform effects component.
 *
 * @since 1.0.0
 */
class Transform extends Component_Base {

	private $effects = array();


	private $units = array();

	private $slider = array();


	private $tabs = array();

	private $is_hover = false;


	private $origin_conditions = array();

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	public function get_id() {
		return 'transform';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( 'Transform Effects', 'cmsmasters-elementor' );
	}

	protected function set_effect_vars() {
		$this->set_effects();

		parent::set_effect_vars();
	}

	private function set_effects() {
		$this->effects = array(
			'translate',
			'rotate',
			'scale',
			'skew',
			'opacity',
			'blur',
		);
	}

	public function get_effects() {
		return $this->effects;
	}

	protected function set_transform_vars() {
		$this->switcher_control = $this->get_control_name( '', false, 'effect_type' );

		$this->units = array(
			'translate' => array(
				'size_units' => array(
					'px',
					'%',
					'em',
					'vw',
					'vh',
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'range' => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -30,
						'max' => 30,
						'step' => 0.5,
					),
					'vw' => array(
						'min' => -50,
						'max' => 50,
						'step' => 0.5,
					),
					'vh' => array(
						'min' => -50,
						'max' => 50,
						'step' => 0.5,
					),
				),
			),
			'rotate' => array(
				'size_units' => array( 'deg', 'turn' ),
				'default' => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'tablet_default' => array( 'unit' => 'deg' ),
				'mobile_default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
					'turn' => array(
						'min' => -0.5,
						'max' => 0.5,
						'step' => 0.01,
					),
				),
			),
			'scale' => array(
				'size_units' => array( 'x' ),
				'default' => array(
					'size' => 1,
					'unit' => 'x',
				),
				'tablet_default' => array( 'unit' => 'x' ),
				'mobile_default' => array( 'unit' => 'x' ),
				'range' => array(
					'x' => array(
						'min' => 0,
						'max' => 3,
						'step' => 0.05,
					),
				),
			),
			'skew' => array(
				'size_units' => array( 'deg' ),
				'default' => array(
					'size' => 0,
					'unit' => 'deg',
				),
				'tablet_default' => array( 'unit' => 'deg' ),
				'mobile_default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => -180,
						'max' => 180,
					),
				),
			),
			'opacity' => array(
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 1 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					),
				),
			),
			'origin' => array(
				'size_units' => array( 'px', '%' ),
				'default' => array( 'unit' => '%' ),
				'tablet_default' => array( 'unit' => '%' ),
				'mobile_default' => array( 'unit' => '%' ),
				'range' => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
			),
			'blur' => array(
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 0 ),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 300,
					),
				),
			),
		);

		$effect_name = $this->get_id();

		$this->slider = array(
			'type' => Controls_Manager::SLIDER,
			'condition' => array( $this->switcher_control => $effect_name ),
		);

		$this->tabs = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$origin_conditions_terms = array();

		foreach ( array_keys( $this->tabs ) as $tab ) {
			$this->is_hover = ( 'hover' === $tab ) ? true : false;

			foreach ( $this->get_effects() as $effect ) {
				$origin_conditions_terms[] = array(
					'name' => $this->get_control_name( "{$effect}_toggle" ),
					'value' => 'yes',
				);
			}

			$this->is_hover = false;
		}

		$this->origin_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => $this->switcher_control,
					'value' => $effect_name,
				),
				array(
					'relation' => 'or',
					'terms' => $origin_conditions_terms,
				),
			),
		);
	}

	public function register_effect_controls() {
		$effect_name = $this->get_id();
		$hover_type = $this->get_control_name( 'hover_type' );

		$in_control_selectors = sprintf(
			'%1$s: 0px; %2$s: 0px; %3$s: 0deg; %4$s: 0deg; %5$s: 0deg; %6$s: 1; %7$s: 1; %8$s: 0deg; %9$s: 0deg; %10$s: 1; %11$s: 0px;',
			$this->get_control_css_var( 'translate-x', false ),
			$this->get_control_css_var( 'translate-y', false ),
			$this->get_control_css_var( 'rotate-z', false ),
			$this->get_control_css_var( 'rotate-x', false ),
			$this->get_control_css_var( 'rotate-y', false ),
			$this->get_control_css_var( 'scale-x', false ),
			$this->get_control_css_var( 'scale-y', false ),
			$this->get_control_css_var( 'skew-x', false ),
			$this->get_control_css_var( 'skew-y', false ),
			$this->get_control_css_var( 'opacity', false ),
			$this->get_control_css_var( 'blur', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'in', false ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $in_control_selectors ),
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->register_control_tabs();

		$out_control_selectors_pattern = 'transform: ' .
			'translate(var(%1$s), var(%2$s)) ' .
			'rotateZ(var(%3$s)) rotateX(var(%4$s)) rotateY(var(%5$s)) ' .
			'scale(var(%6$s), var(%7$s)) ' .
			'skew(var(%8$s), var(%9$s)); ' .
			'opacity: var(%10$s); ' .
			'backdrop-filter: blur(var(%11$s));';

		$out_control_selectors = sprintf(
			$out_control_selectors_pattern,
			$this->get_control_css_var( 'translate-x', false ),
			$this->get_control_css_var( 'translate-y', false ),
			$this->get_control_css_var( 'rotate-z', false ),
			$this->get_control_css_var( 'rotate-x', false ),
			$this->get_control_css_var( 'rotate-y', false ),
			$this->get_control_css_var( 'scale-x', false ),
			$this->get_control_css_var( 'scale-y', false ),
			$this->get_control_css_var( 'skew-x', false ),
			$this->get_control_css_var( 'skew-y', false ),
			$this->get_control_css_var( 'opacity', false ),
			$this->get_control_css_var( 'blur', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'out', false ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $out_control_selectors ),
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->is_hover = true;

		$out_hover_control_selectors_pattern = 'transform: ' .
			'translate(var(%1$s, var(%2$s)), var(%3$s, var(%4$s))) ' .
			'rotateZ(var(%5$s, var(%6$s))) rotateX(var(%7$s, var(%8$s))) rotateY(var(%9$s, var(%10$s))) ' .
			'scale(var(%11$s, var(%12$s)), var(%13$s, var(%14$s))) ' .
			'skew(var(%15$s, var(%16$s)), var(%17$s, var(%18$s))); ' .
			'opacity: var(%19$s, var(%20$s)); ' .
			'backdrop-filter: blur(var(%21$s, var(%22$s)));';

		$out_hover_control_selectors = sprintf(
			$out_hover_control_selectors_pattern,
			$this->get_control_css_var( 'translate-x' ),
			$this->get_control_css_var( 'translate-x', false ),
			$this->get_control_css_var( 'translate-y' ),
			$this->get_control_css_var( 'translate-y', false ),
			$this->get_control_css_var( 'rotate-z' ),
			$this->get_control_css_var( 'rotate-z', false ),
			$this->get_control_css_var( 'rotate-x' ),
			$this->get_control_css_var( 'rotate-x', false ),
			$this->get_control_css_var( 'rotate-y' ),
			$this->get_control_css_var( 'rotate-y', false ),
			$this->get_control_css_var( 'scale-x' ),
			$this->get_control_css_var( 'scale-x', false ),
			$this->get_control_css_var( 'scale-y' ),
			$this->get_control_css_var( 'scale-y', false ),
			$this->get_control_css_var( 'skew-x' ),
			$this->get_control_css_var( 'skew-x', false ),
			$this->get_control_css_var( 'skew-y' ),
			$this->get_control_css_var( 'skew-y', false ),
			$this->get_control_css_var( 'opacity' ),
			$this->get_control_css_var( 'opacity', false ),
			$this->get_control_css_var( 'blur' ),
			$this->get_control_css_var( 'blur', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'out_element' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_hover_wrapper => $out_hover_control_selectors ),
				'condition' => array(
					$this->switcher_control => $effect_name,
					$hover_type => 'element',
				),
			)
		);

		$column_inner_selector = '.elementor-element.elementor-element-{{ID}}';

		$this->add_responsive_control(
			$this->get_control_name( 'out_column' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array(
					".elementor-column:hover > .elementor-widget-wrap > {$column_inner_selector}, " .
					".elementor-column:hover > .elementor-widget-wrap > .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".elementor-section .e-con.e-con-full.e-parent:hover > {$column_inner_selector}, " .
					".elementor-section .e-con.e-con-boxed.e-parent:hover > .e-con-inner > {$column_inner_selector}, " .
					".elementor-section .e-con.e-con-full.e-parent:hover > .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".elementor-section .e-con.e-con-boxed.e-parent:hover > .e-con-inner > .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".e-con.e-con-full.e-child:hover > {$column_inner_selector}, " .
					".e-con.e-con-boxed.e-child:hover > .e-con-inner > {$column_inner_selector}, " .
					".e-con.e-con-full.e-child:hover > .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".e-con.e-con-boxed.e-child:hover > .e-con-inner > .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}" => $out_hover_control_selectors,
				),
				'condition' => array(
					$this->switcher_control => $effect_name,
					$hover_type => 'column',
				),
			)
		);

		$this->add_responsive_control(
			$this->get_control_name( 'out_row' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array(
					".elementor-section:hover > .elementor-container > .elementor-column > .elementor-widget-wrap > {$column_inner_selector}, " .
					".elementor-section:hover > .elementor-container > .elementor-column.cmsmasters-effect-type-transform.cmsmasters-effect-hover-type-section > .elementor-widget-wrap, " .
					".elementor-section:hover > .elementor-container > .elementor-column > .elementor-widget-wrap .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".elementor-section:hover > .elementor-container > .elementor-column > .elementor-widget-wrap .e-con.e-con-full > {$column_inner_selector}, " .
					".elementor-section:hover > .elementor-container > .elementor-column > .elementor-widget-wrap .e-con.e-con-boxed > .e-con-inner > {$column_inner_selector}, " .
					".e-con.e-con-full.e-parent:hover > {$column_inner_selector}, " .
					".e-con.e-con-boxed.e-parent:hover > .e-con-inner > {$column_inner_selector}, " .
					".e-con.e-parent:hover .e-con.cmsmasters-effect-type-transform.elementor-element-{{ID}}, " .
					".e-con.e-parent:hover .e-con.e-con-full > {$column_inner_selector}, " .
					".e-con.e-parent:hover .e-con.e-con-boxed > .e-con-inner > {$column_inner_selector}" => $out_hover_control_selectors,
				),
				'condition' => array(
					$this->switcher_control => $effect_name,
					$hover_type => 'section',
					'cms_transform_out_row_id_hover' => '',
				),
			)
		);

		$this->add_responsive_control(
			$this->get_control_name( 'out_row_custom_selector' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => array(
					":hover {$column_inner_selector}",
					":hover .e-con.cmsmasters-effect-type-transform.elementor-element.elementor-element-{{ID}}",
					":hover .elementor-column.cmsmasters-effect-type-transform > .elementor-widget-wrap",
				),
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$hover_type => 'section',
					'cms_transform_out_row_id_hover!' => '',
				),
			)
		);

		$this->add_responsive_control(
			$this->get_control_name( 'out_row_custom_value' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => $out_hover_control_selectors,
				'frontend_available' => true,
				'condition' => array(
					$this->switcher_control => $effect_name,
					$hover_type => 'section',
					'cms_transform_out_row_id_hover!' => '',
				),
			)
		);

		$this->is_hover = false;

		$this->register_origin_controls();
	}

	/**
	 * Register tabs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.4 Added a "Container Selection" control for selection a custom container in the transform advanced effect on hover.
	 */
	private function register_control_tabs() {
		$effect_name = $this->get_id();

		$this->start_controls_tabs(
			$this->get_control_name( 'tabs', false ),
			array(
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		foreach ( $this->tabs as $tab_key => $tab_label ) {
			$this->is_hover = ( 'hover' === $tab_key ) ? true : false;

			$this->start_controls_tab(
				$this->get_control_name( "tabs_{$tab_key}", false ),
				array( 'label' => $tab_label )
			);

			foreach ( $this->get_effects() as $effect ) {
				call_user_func( array( $this, "register_{$effect}_controls" ) );
			}

			if ( $this->is_hover ) {
				$this->add_control(
					$this->get_control_name( 'duration', false ),
					array(
						'label' => __( 'Transform Duration', 'cmsmasters-elementor' ),
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
						'selectors' => array(
							$this->transform_wrapper => 'transition-duration: {{SIZE}}s !important;',
						),
						'condition' => array( $this->switcher_control => $effect_name ),
					)
				);

				$this->add_control(
					$this->get_control_name( 'hover_type', false ),
					array(
						'label' => __( 'Hover Element', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'element' => array(
								'title' => __( 'Element', 'cmsmasters-elementor' ),
								'description' => __( 'Apply transform effects on element hover.', 'cmsmasters-elementor' ),
							),
							'column' => array(
								'title' => __( 'Column', 'cmsmasters-elementor' ),
								'description' => __( 'Apply transform effects on element parent column hover.', 'cmsmasters-elementor' ),
							),
							'section' => array(
								'title' => __( 'Section', 'cmsmasters-elementor' ),
								'description' => __( 'Apply transform effects on element parent section hover.', 'cmsmasters-elementor' ),
							),
						),
						'default' => 'element',
						'frontend_available' => true,
						'prefix_class' => "cmsmasters-effect-hover-type-",
						'condition' => array( $this->switcher_control => $effect_name ),
					)
				);

				$this->add_control(
					$this->get_control_name( 'out_row_id' ),
					array(
						'label' => __( 'Container Selector', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( '#my-id or .my-class', 'cmsmasters-elementor' ),
						'description' => __( 'Add your custom parent block id or class.', 'cmsmasters-elementor' ),
						'title' => __( 'ID or class must be WITH the Pound key or the dot respectively. (e.g: #my-id or .my-class)', 'cmsmasters-elementor' ),
						'render_type' => 'template',
						'frontend_available' => true,
						'ai' => array( 'active' => false ),
						'condition' => array(
							$this->switcher_control => $effect_name,
							$this->get_control_name( 'hover_type', false ) => 'section',
						),
					)
				);
			}

			$this->end_controls_tab();

			$this->is_hover = false;
		}

		$this->end_controls_tabs();
	}

	private function register_translate_controls() {
		$this->register_single_effect_controls( 'translate' );
	}

	private function register_single_effect_controls( $effect ) {
		$toggle_control = $this->get_control_name( "{$effect}_toggle" );

		$this->add_control(
			$toggle_control,
			array(
				'label' => $this->effect_labels[ $effect ]['simple'],
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'ui',
				'condition' => array( $this->switcher_control => $this->get_id() ),
			)
		);

		$this->start_popover();

		if ( isset( $this->axis[ $effect ] ) ) {
			foreach ( $this->axis[ $effect ] as $axis ) {
				$this->add_responsive_control(
					$this->get_control_name( "{$effect}_{$axis}" ),
					array_replace_recursive(
						$this->slider,
						$this->units[ $effect ],
						array(
							'label' => sprintf( $this->effect_labels[ $effect ]['axis'], strtoupper( $axis ) ),
							'selectors' => array(
								$this->transform_wrapper => $this->get_control_css_var( "{$effect}-{$axis}" ) . ': {{SIZE}}{{UNIT}};',
							),
							'condition' => array( $toggle_control => 'yes' ),
						)
					)
				);
			}
		} elseif ( 'blur' === $effect ) {
			$this->add_responsive_control(
				$this->get_control_name( $effect ),
				array_replace_recursive(
					$this->slider,
					$this->units[ $effect ],
					array(
						'label' => $this->effect_labels[ $effect ]['simple'],
						'selectors' => array(
							$this->transform_wrapper => $this->get_control_css_var( $effect ) . ': {{SIZE}}{{UNIT}};',
						),
						'condition' => array( $toggle_control => 'yes' ),
					)
				)
			);
		} else {
			$this->add_responsive_control(
				$this->get_control_name( $effect ),
				array_replace_recursive(
					$this->slider,
					$this->units[ $effect ],
					array(
						'label' => $this->effect_labels[ $effect ]['simple'],
						'selectors' => array(
							$this->transform_wrapper => $this->get_control_css_var( $effect ) . ': {{SIZE}};',
						),
						'condition' => array( $toggle_control => 'yes' ),
					)
				)
			);
		}

		$this->end_popover();
	}

	private function register_rotate_controls() {
		$effect_name = $this->get_id();
		$toggle_control = $this->get_control_name( 'rotate_toggle' );
		$type_control = $this->get_control_name( 'rotate_type' );

		$this->add_control(
			$toggle_control,
			array(
				'label' => $this->effect_labels['rotate']['simple'],
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'ui',
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->start_popover();

		$this->add_control(
			$type_control,
			array_replace_recursive( $this->effect_type_args, array(
				'condition' => array(
					$this->switcher_control => $effect_name,
					$toggle_control => 'yes',
				),
			) )
		);

		foreach ( $this->axis['rotate'] as $axis ) {
			list( $control_label, $effect_control, $css_var ) = $this->get_rotate_axis_control_parameters( $axis );

			$this->add_responsive_control(
				$effect_control,
				array_replace_recursive(
					$this->slider,
					$this->units['rotate'],
					array(
						'label' => $control_label,
						'selectors' => array( $this->transform_wrapper => $css_var ),
						'condition' => array(
							$toggle_control => 'yes',
							$type_control => ( 'none' !== $axis ) ? 'advanced' : 'simple',
						),
					)
				)
			);
		}

		$this->end_popover();
	}

	private function get_rotate_axis_control_parameters( $axis ) {
		$effect_control = 'rotate';
		$control_label = $this->effect_labels[ $effect_control ]['simple'];
		$css_var = $this->get_control_css_var( $effect_control, false );
		$css_vars_simple = array();

		if ( 'none' !== $axis ) {
			$control_label = sprintf( $this->effect_labels[ $effect_control ]['axis'], strtoupper( $axis ) );
			$effect_control .= "_{$axis}";
			$css_var .= "-{$axis}";
		} else {
			$css_vars_simple = array(
				"{$css_var}-z",
				"{$css_var}-x",
				"{$css_var}-y",
			);
		}

		if ( $this->is_hover ) {
			if ( ! empty( $css_vars_simple ) ) {
				foreach ( $css_vars_simple as $key => $var ) {
					$css_vars_simple[ $key ] = "{$var}-hover";
				}
			} else {
				$css_var .= '-hover';
			}
		}

		if ( ! empty( $css_vars_simple ) ) {
			foreach ( $css_vars_simple as $key => $var ) {
				$value = 0;

				if ( "{$css_var}-z" === $var || "{$css_var}-z-hover" === $var ) {
					$value = '{{SIZE}}';
				}

				$css_vars_simple[ $key ] = "{$var}: {$value}{{UNIT}};";
			}

			$css_var = implode( ' ', $css_vars_simple );
		} else {
			$css_var .= ': {{SIZE}}{{UNIT}};';
		}

		return array(
			$control_label,
			$this->get_control_name( $effect_control ),
			$css_var,
		);
	}

	/**
	 * Register scale controls.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Fixed on breakpoints.
	 */
	private function register_scale_controls() {
		$effect_name = $this->get_id();
		$toggle_control = $this->get_control_name( 'scale_toggle' );
		$type_control = $this->get_control_name( 'scale_type' );

		$this->add_control(
			$toggle_control,
			array(
				'label' => $this->effect_labels['scale']['simple'],
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'ui',
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);

		$this->start_popover();

		if ( $this->is_hover ) {
			$this->add_control(
				$this->get_control_name( 'scale_in' ),
				array(
					'type' => Controls_Manager::HIDDEN,
					'default' => 1,
					'selectors' => array(
						$this->transform_wrapper => $this->get_control_css_var( 'scale-x' ) . ': 1; ' .
						$this->get_control_css_var( 'scale-y' ) . ': 1;',
					),
					'condition' => array(
						$this->switcher_control => $effect_name,
						$toggle_control => 'yes',
					),
				)
			);
		}

		$this->add_control(
			$type_control,
			array_replace_recursive( $this->effect_type_args, array(
				'condition' => array(
					$this->switcher_control => $effect_name,
					$toggle_control => 'yes',
				),
			) )
		);

		foreach ( $this->axis['scale'] as $axis ) {
			list( $control_label, $effect_control, $css_var ) = $this->get_scale_axis_control_parameters( $axis );

			$responsive_css_var = str_replace( '{{SIZE || 1}}', '{{SIZE}}', $css_var );

			$this->add_responsive_control(
				$effect_control,
				array_replace_recursive(
					$this->slider,
					$this->units['scale'],
					array(
						'label' => $control_label,
						'selectors' => array( $this->transform_wrapper => $css_var ),
						'device_args' => Breakpoints_Manager::get_devices_args( array(
							'selectors' => array( $this->transform_wrapper => $responsive_css_var ),
						) ),
						'condition' => array(
							$toggle_control => 'yes',
							$type_control => ( 'none' !== $axis ) ? 'advanced' : 'simple',
						),
					)
				)
			);
		}

		$this->end_popover();
	}

	private function get_scale_axis_control_parameters( $axis ) {
		$effect_control = 'scale';
		$control_label = $this->effect_labels[ $effect_control ]['simple'];
		$css_var = $this->get_control_css_var( $effect_control, false );
		$css_vars_simple = array();

		if ( 'none' !== $axis ) {
			$control_label = sprintf( $this->effect_labels[ $effect_control ]['axis'], strtoupper( $axis ) );
			$effect_control .= "_{$axis}";
			$css_var .= "-{$axis}";
		} else {
			$css_vars_simple = array(
				"{$css_var}-x",
				"{$css_var}-y",
			);
		}

		if ( $this->is_hover ) {
			if ( ! empty( $css_vars_simple ) ) {
				foreach ( $css_vars_simple as $key => $var ) {
					$css_vars_simple[ $key ] = "{$var}-hover";
				}
			} else {
				$css_var .= '-hover';
			}
		}

		if ( ! empty( $css_vars_simple ) ) {
			foreach ( $css_vars_simple as $key => $var ) {
				$css_vars_simple[ $key ] = "{$var}: {{SIZE || 1}};";
			}

			$css_var = implode( ' ', $css_vars_simple );
		} else {
			$css_var .= ': {{SIZE || 1}};';
		}

		return array(
			$control_label,
			$this->get_control_name( $effect_control ),
			$css_var,
		);
	}

	private function register_skew_controls() {
		$this->register_single_effect_controls( 'skew' );
	}

	private function register_opacity_controls() {
		$this->register_single_effect_controls( 'opacity' );
	}

	private function register_blur_controls() {
		$this->register_single_effect_controls( 'blur' );
	}

	private function register_origin_controls() {
		$effect_name = $this->get_id();
		$toggle_control = $this->get_control_name( 'origin_toggle', false );
		$type_control = $this->get_control_name( 'origin', false );

		$this->add_control(
			$toggle_control,
			array(
				'label' => $this->effect_labels['origin']['simple'],
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'ui',
				'conditions' => $this->origin_conditions,
			)
		);

		$this->start_popover();

		$this->add_control(
			$type_control,
			array_replace_recursive( $this->effect_type_args, array(
				'condition' => array(
					$this->switcher_control => $effect_name,
					$toggle_control => 'yes',
				),
			) )
		);

		foreach ( $this->axis['origin'] as $axis ) {
			list( $control_args, $css_var ) = $this->get_origin_axis_control_parameters( $axis );

			$this->add_responsive_control(
				$this->get_control_name( "origin_{$axis}", false ),
				array_replace_recursive( $control_args, array(
					'selectors' => array( $this->transform_wrapper => $css_var ),
					'condition' => array(
						$toggle_control => 'yes',
						$type_control => ( 'vertical' !== $axis && 'horizontal' !== $axis ) ? 'advanced' : 'simple',
					),
				) )
			);
		}

		$this->end_popover();

		$origin_out_control_selectors = sprintf(
			'transform-origin: var(%1$s, 50%%) var(%2$s, 50%%) var(%3$s, 0px);',
			$this->get_control_css_var( 'origin-x', false ),
			$this->get_control_css_var( 'origin-y', false ),
			$this->get_control_css_var( 'origin-z', false )
		);

		$this->add_control(
			$this->get_control_name( 'origin_out', false ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $origin_out_control_selectors ),
				'condition' => array( $this->switcher_control => $effect_name ),
			)
		);
	}

	private function get_origin_axis_control_parameters( $axis ) {
		$css_var = $this->get_control_css_var( 'origin', false );

		$control_args = array_replace_recursive( $this->slider, $this->units['origin'] );

		if ( 'vertical' !== $axis && 'horizontal' !== $axis ) {
			$control_args['label'] = sprintf( $this->effect_labels['origin']['axis'], strtoupper( $axis ) );

			if ( 'z' === $axis ) {
				$control_args['default'] = array(
					'size' => 0,
					'unit' => 'px',
				);
			} else {
				$control_args['default'] = array(
					'size' => 50,
					'unit' => '%',
				);
			}

			$css_var .= "-{$axis}: {{SIZE}}{{UNIT}};";
		} else {
			$control_args = array(
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'toggle' => false,
				'condition' => array( $this->switcher_control => $this->get_id() ),
			);

			$control_args['label'] = $this->effect_labels['origin'][ $axis ];

			if ( 'vertical' === $axis ) {
				$css_var .= '-y';

				$control_args['options'] = array(
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
				);
			} elseif ( 'horizontal' === $axis ) {
				$css_var .= '-x';

				$control_args['options'] = array(
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
				);
			}

			$css_var .= ': {{VALUE}};';
		}

		return array(
			$control_args,
			$css_var,
		);
	}

	protected function get_control_name( $name = '', $check_hover = true, $custom_suffix = false ) {
		if ( $check_hover && $this->is_hover ) {
			if ( ! empty( $name ) ) {
				$name .= '_';
			}

			$name .= 'hover';
		}

		return parent::get_control_name( $name, $custom_suffix );
	}

	protected function get_control_css_var( $name = '', $check_hover = true ) {
		if ( $check_hover && $this->is_hover ) {
			if ( ! empty( $name ) ) {
				$name .= '-';
			}

			$name .= 'hover';
		}

		return parent::get_control_css_var( $name );
	}
}
