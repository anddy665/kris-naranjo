<?php
namespace CmsmastersElementor\Modules\Effects\Components;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Effects\Components\Base\Component_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Effects module Clip-Path effects component.
 *
 * @since 1.0.0
 */
class Clip_Path extends Component_Base {

	private $polygon_vertices = array();

	private $slider = array();


	private $type_control;

	private $css_var;


	private $tabs = array();

	private $is_hover = false;

	/**
	 * Get ID.
	 *
	 * Retrieve the component ID.
	 *
	 * @return string Component ID.
	 */
	public function get_id() {
		return 'clip-path';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the component title.
	 *
	 * @return string Component title.
	 */
	public function get_title() {
		return __( 'Clip-Path', 'cmsmasters-elementor' );
	}

	protected function set_clip_path_vars() {
		$this->switcher_control = $this->get_control_name();

		$this->polygon_vertices = array(
			'cut-corner' => array(
				'label' => __( 'Cut Corner', 'cmsmasters-elementor' ),
				'option' => '',
			),
			'triangle-up' => array(
				'label' => __( 'Triangle Up', 'cmsmasters-elementor' ),
				'option' => '50% 0%, 0% 100%, 100% 100%',
			),
			'triangle-right' => array(
				'label' => __( 'Triangle Right', 'cmsmasters-elementor' ),
				'option' => '100% 50%, 0% 0%, 0% 100%',
			),
			'triangle-down' => array(
				'label' => __( 'Triangle Down', 'cmsmasters-elementor' ),
				'option' => '50% 100%, 100% 0%, 0% 0%',
			),
			'triangle-left' => array(
				'label' => __( 'Triangle Left', 'cmsmasters-elementor' ),
				'option' => '0% 50%, 100% 100%, 100% 0%',
			),
			'trapezoid-up' => array(
				'label' => __( 'Trapezoid Up', 'cmsmasters-elementor' ),
				'option' => '20% 0%, 80% 0%, 100% 100%, 0% 100%',
			),
			'trapezoid-right' => array(
				'label' => __( 'Trapezoid Right', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 100% 20%, 100% 80%, 0% 100%',
			),
			'trapezoid-down' => array(
				'label' => __( 'Trapezoid Down', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 100% 0%, 80% 100%, 20% 100%',
			),
			'trapezoid-left' => array(
				'label' => __( 'Trapezoid Left', 'cmsmasters-elementor' ),
				'option' => '0% 20%, 100% 0%, 100% 100%, 0% 80%',
			),
			'parallelogram-up' => array(
				'label' => __( 'Parallelogram Up', 'cmsmasters-elementor' ),
				'option' => '0% 25%, 100% 0%, 100% 75%, 0% 100%',
			),
			'parallelogram-right' => array(
				'label' => __( 'Parallelogram Right', 'cmsmasters-elementor' ),
				'option' => '25% 0%, 100% 0%, 75% 100%, 0% 100%',
			),
			'parallelogram-down' => array(
				'label' => __( 'Parallelogram Down', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 100% 25%, 100% 100%, 0% 75%',
			),
			'parallelogram-left' => array(
				'label' => __( 'Parallelogram Left', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 75% 0%, 100% 100%, 25% 100%',
			),
			'rhombus' => array(
				'label' => __( 'Rhombus', 'cmsmasters-elementor' ),
				'option' => '50% 0%, 100% 50%, 50% 100%, 0% 50%',
			),
			'pentagon' => array(
				'label' => __( 'Pentagon', 'cmsmasters-elementor' ),
				'option' => '50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%',
			),
			'point-up' => array(
				'label' => __( 'Point Up', 'cmsmasters-elementor' ),
				'option' => '50% 0%, 100% 25%, 100% 100%, 0% 100%, 0% 25%',
			),
			'point-right' => array(
				'label' => __( 'Point Right', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 75% 0%, 100% 50%, 75% 100%, 0% 100%',
			),
			'point-down' => array(
				'label' => __( 'Point Down', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 100% 0%, 100% 75%, 50% 100%, 0% 75%',
			),
			'point-left' => array(
				'label' => __( 'Point Left', 'cmsmasters-elementor' ),
				'option' => '25% 0%, 100% 0%, 100% 100%, 25% 100%, 0% 50%',
			),
			'hexagon' => array(
				'label' => __( 'Hexagon', 'cmsmasters-elementor' ),
				'option' => '25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%',
			),
			'chevron-up' => array(
				'label' => __( 'Chevron Up', 'cmsmasters-elementor' ),
				'option' => '0% 25%, 50% 0%, 100% 25%, 100% 100%, 50% 75%, 0% 100%',
			),
			'chevron-right' => array(
				'label' => __( 'Chevron Right', 'cmsmasters-elementor' ),
				'option' => '75% 0%, 100% 50%, 75% 100%, 0% 100%, 25% 50%, 0% 0%',
			),
			'chevron-down' => array(
				'label' => __( 'Chevron Down', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 50% 25%, 100% 0%, 100% 75%, 50% 100%, 0% 75%',
			),
			'chevron-left' => array(
				'label' => __( 'Chevron Left', 'cmsmasters-elementor' ),
				'option' => '100% 0%, 75% 50%, 100% 100%, 25% 100%, 0% 50%, 25% 0%',
			),
			'arrow-up' => array(
				'label' => __( 'Arrow Up', 'cmsmasters-elementor' ),
				'option' => '0% 40%, 50% 0%, 100% 40%, 80% 40%, 80% 100%, 20% 100%, 20% 40%',
			),
			'arrow-right' => array(
				'label' => __( 'Arrow Right', 'cmsmasters-elementor' ),
				'option' => '0% 20%, 60% 20%, 60% 0%, 100% 50%, 60% 100%, 60% 80%, 0% 80%',
			),
			'arrow-down' => array(
				'label' => __( 'Arrow Down', 'cmsmasters-elementor' ),
				'option' => '0% 60%, 20% 60%, 20% 0%, 80% 0%, 80% 60%, 100% 60%, 50% 100%',
			),
			'arrow-left' => array(
				'label' => __( 'Arrow Left', 'cmsmasters-elementor' ),
				'option' => '40% 0%, 40% 20%, 100% 20%, 100% 80%, 40% 80%, 40% 100%, 0% 50%',
			),
			'message' => array(
				'label' => __( 'Message', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 100% 0%, 100% 75%, 75% 75%, 75% 100%, 50% 75%, 0% 75%',
			),
			'octagon' => array(
				'label' => __( 'Octagon', 'cmsmasters-elementor' ),
				'option' => '30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%',
			),
			'star' => array(
				'label' => __( 'Star', 'cmsmasters-elementor' ),
				'option' => '50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%',
			),
			'frame' => array(
				'label' => __( 'Frame', 'cmsmasters-elementor' ),
				'option' => '0% 0%, 0% 100%, 25% 100%, 25% 25%, 75% 25%, 75% 75%, 25% 75%, 25% 100%, 100% 100%, 100% 0%',
			),
			'close' => array(
				'label' => __( 'Close', 'cmsmasters-elementor' ),
				'option' => '20% 0%, 0% 20%, 30% 50%, 0% 80%, 20% 100%, 50% 70%, 80% 100%, 100% 80%, 70% 50%, 100% 20%, 80% 0%, 50% 30%',
			),
			'plus' => array(
				'label' => __( 'Plus', 'cmsmasters-elementor' ),
				'option' => '0% 35%, 35% 35%, 35% 0%, 65% 0%, 65% 35%, 100% 35%, 100% 65%, 65% 65%, 65% 100%, 35% 100%, 35% 65%, 0% 65%',
			),
		);

		$this->slider = array(
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'default' => array( 'unit' => '%' ),
			'tablet_default' => array( 'unit' => '%' ),
			'mobile_default' => array( 'unit' => '%' ),
			'range' => array(
				'%' => array(
					'min' => 0,
					'max' => 100,
				),
				'px' => array(
					'min' => 0,
					'max' => 500,
					'step' => 10,
				),
			),
		);
	}

	public function register_effect_controls() {
		$this->switcher_control = $this->get_control_name( '', false );
		$this->type_control = $this->get_control_name( 'type', false );

		$this->tabs = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$effect_name = $this->get_id();

		$this->add_control(
			$this->switcher_control,
			array(
				'label' => $this->get_title(),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'prefix_class' => "cmsmasters-{$effect_name}-",
				'separator' => 'before',
			)
		);

		$this->add_control(
			$this->type_control,
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'ellipse' => __( 'Ellipse', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'polygon' => __( 'Polygon', 'cmsmasters-elementor' ),
				),
				'default' => 'circle',
				'toggle' => false,
				'render_type' => 'ui',
				'condition' => array( $this->switcher_control => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			$this->get_control_name( 'tabs', false ),
			array(
				'condition' => array( $this->switcher_control => 'yes' ),
			)
		);

		foreach ( $this->tabs as $tab_key => $tab_label ) {
			$this->is_hover = ( 'hover' === $tab_key ) ? true : false;

			$this->css_var = $this->get_control_css_var();

			$this->start_controls_tab(
				$this->get_control_name( "tabs_{$tab_key}", false ),
				array( 'label' => $tab_label )
			);

			$this->register_circle_ellipse_controls();

			$this->register_inset_controls();

			$this->register_polygon_controls();

			$output_selector = ( $this->is_hover ) ? $this->transform_hover_wrapper : $this->transform_wrapper;

			$this->parent->add_responsive_control(
				$this->get_control_name( 'output' ),
				array(
					'type' => Controls_Manager::HIDDEN,
					'default' => 1,
					'selectors' => array(
						$output_selector => "-webkit-clip-path: var({$this->css_var});" .
							"clip-path: var({$this->css_var});",
					),
					'condition' => array( $this->switcher_control => 'yes' ),
				)
			);

			if ( $this->is_hover ) {
				$this->add_control(
					$this->get_control_name( 'duration' ),
					array(
						'label' => __( 'Animation Duration', 'cmsmasters-elementor' ),
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
							$this->transform_wrapper => 'transition-duration: {{SIZE}}s;',
						),
						'condition' => array( $this->switcher_control => 'yes' ),
					)
				);
			}

			$this->end_controls_tab();

			$this->is_hover = false;
		}

		$this->end_controls_tabs();
	}

	private function register_circle_ellipse_controls() {
		$radius_default = ( $this->is_hover ) ? array() : array(
			'default' => array( 'size' => 50 ),
		);

		$radius_hor_default = ( $this->is_hover ) ? array() : array(
			'default' => array( 'size' => 30 ),
		);

		$this->add_responsive_control(
			$this->get_control_name( 'radius' ),
			array_replace_recursive( $this->slider, $radius_default, array(
				'label' => __( 'Radius', 'cmsmasters-elementor' ),
				'selectors' => $this->get_selector_value( 'radius', array(
					array(
						'SIZE' => 50,
						'UNIT' => '%',
					),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'circle',
				),
			) )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'radius_hor' ),
			array_replace_recursive( $this->slider, $radius_hor_default, array(
				'label' => __( 'Horizontal Radius', 'cmsmasters-elementor' ),
				'selectors' => $this->get_selector_value( 'radius-hor', array(
					array(
						'SIZE' => 30,
						'UNIT' => '%',
					),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'ellipse',
				),
			) )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'radius_vert' ),
			array_replace_recursive( $this->slider, $radius_default, array(
				'label' => __( 'Vertical Radius', 'cmsmasters-elementor' ),
				'selectors' => $this->get_selector_value( 'radius-vert', array(
					array(
						'SIZE' => 50,
						'UNIT' => '%',
					),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'ellipse',
				),
			) )
		);

		$this->register_position_controls();

		$circle_output_pattern = ( $this->is_hover ) ?
			'%1$s: circle(var(%2$s, var(%3$s)) at var(%4$s, var(%5$s)) var(%6$s, var(%7$s)));' :
			'%1$s: circle(var(%2$s) at var(%4$s) var(%6$s));';

		$circle_output_selector = sprintf(
			$circle_output_pattern,
			$this->css_var,
			$this->get_control_css_var( 'radius' ),
			$this->get_control_css_var( 'radius', false ),
			$this->get_control_css_var( 'position-hor' ),
			$this->get_control_css_var( 'position-hor', false ),
			$this->get_control_css_var( 'position-vert' ),
			$this->get_control_css_var( 'position-vert', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'circle_output' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $circle_output_selector ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'circle',
				),
			)
		);

		$ellipse_output_pattern = ( $this->is_hover ) ?
			'%1$s: ellipse(var(%2$s, var(%3$s)) var(%4$s, var(%5$s)) at var(%6$s, var(%7$s)) var(%8$s, var(%9$s)));' :
			'%1$s: ellipse(var(%2$s) var(%4$s) at var(%6$s) var(%8$s));';

		$ellipse_output_selector = sprintf(
			$ellipse_output_pattern,
			$this->css_var,
			$this->get_control_css_var( 'radius-hor' ),
			$this->get_control_css_var( 'radius-hor', false ),
			$this->get_control_css_var( 'radius-vert' ),
			$this->get_control_css_var( 'radius-vert', false ),
			$this->get_control_css_var( 'position-hor' ),
			$this->get_control_css_var( 'position-hor', false ),
			$this->get_control_css_var( 'position-vert' ),
			$this->get_control_css_var( 'position-vert', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'ellipse_output' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $ellipse_output_selector ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'ellipse',
				),
			)
		);
	}

	private function register_position_controls() {
		$position_control = $this->get_control_name( 'pos' );

		$position_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => $this->switcher_control,
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => $this->type_control,
							'value' => 'circle',
						),
						array(
							'name' => $this->type_control,
							'value' => 'ellipse',
						),
					),
				),
			),
		);

		$this->add_control(
			$position_control,
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'center',
				'toggle' => false,
				'render_type' => 'ui',
				'conditions' => $position_conditions,
			)
		);

		if ( ! $this->is_hover ) {
			$pos_input_selector = sprintf(
				'%1$s: 50%%; %2$s: 50%%;',
				$this->get_control_css_var( 'position-hor' ),
				$this->get_control_css_var( 'position-vert' )
			);

			$this->add_control(
				$this->get_control_name( 'pos_input' ),
				array(
					'type' => Controls_Manager::HIDDEN,
					'default' => 1,
					'selectors' => array( $this->transform_wrapper => $pos_input_selector ),
					'conditions' => $position_conditions,
				)
			);
		}

		$position_conditions['terms'][] = array(
			'name' => $position_control,
			'value' => 'custom',
		);

		$position_default = ( $this->is_hover ) ? array() : array(
			'default' => array( 'size' => 50 ),
		);

		$this->add_responsive_control(
			$this->get_control_name( 'pos_hor' ),
			array_replace_recursive( $this->slider, $position_default, array(
				'label' => __( 'Horizontal Position', 'cmsmasters-elementor' ),
				'selectors' => $this->get_selector_value( 'position-hor', array(
					array(
						'SIZE' => 50,
						'UNIT' => '%',
					),
				) ),
				'conditions' => $position_conditions,
			) )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'pos_vert' ),
			array_replace_recursive( $this->slider, $position_default, array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'selectors' => $this->get_selector_value( 'position-vert', array(
					array(
						'SIZE' => 50,
						'UNIT' => '%',
					),
				) ),
				'conditions' => $position_conditions,
			) )
		);
	}

	private function register_inset_controls() {
		$this->add_responsive_control(
			$this->get_control_name( 'inset_edges' ),
			array(
				'label' => __( 'Edges Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default' => array( 'unit' => '%' ),
				'tablet_default' => array( 'unit' => '%' ),
				'mobile_default' => array( 'unit' => '%' ),
				'selectors' => $this->get_selector_value( 'inset-edges', array(
					array(
						'TOP' => 10,
						'UNIT' => '%',
					),
					array(
						'RIGHT' => 10,
						'UNIT' => '%',
					),
					array(
						'BOTTOM' => 10,
						'UNIT' => '%',
					),
					array(
						'LEFT' => 10,
						'UNIT' => '%',
					),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'inset',
				),
			)
		);

		$this->add_responsive_control(
			$this->get_control_name( 'inset_radius' ),
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => $this->get_selector_value( 'inset-radius', array(
					array(
						'TOP' => 0,
						'UNIT' => '%',
					),
					array(
						'RIGHT' => 0,
						'UNIT' => '%',
					),
					array(
						'BOTTOM' => 0,
						'UNIT' => '%',
					),
					array(
						'LEFT' => 0,
						'UNIT' => '%',
					),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'inset',
				),
			)
		);

		$inset_output_pattern = ( $this->is_hover ) ?
			'%1$s: inset(var(%2$s, var(%3$s)) round var(%4$s, var(%5$s)));' :
			'%1$s: inset(var(%2$s) round var(%4$s));';

		$inset_output_selector = sprintf(
			$inset_output_pattern,
			$this->css_var,
			$this->get_control_css_var( 'inset-edges' ),
			$this->get_control_css_var( 'inset-edges', false ),
			$this->get_control_css_var( 'inset-radius' ),
			$this->get_control_css_var( 'inset-radius', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'inset_output' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $inset_output_selector ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'inset',
				),
			)
		);
	}

	private function register_polygon_controls() {
		$polygon_type_control = $this->get_control_name( 'polygon_type' );

		$this->add_control(
			$polygon_type_control,
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Predefined', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'toggle' => false,
				'render_type' => 'ui',
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'polygon',
				),
			)
		);

		$polygon_vertices_labels = array();
		$polygon_vertices_options = array();

		foreach ( $this->polygon_vertices as $vertex_name => $vertex_array ) {
			$polygon_vertices_labels[ $vertex_name ] = $vertex_array['label'];
			$polygon_vertices_options[ $vertex_name ] = $vertex_array['option'];
		}

		if ( ! $this->is_hover ) {
			$polygon_input_selector = sprintf(
				'%1$s: %2$s;',
				$this->get_control_css_var( 'polygon' ),
				current( $polygon_vertices_options )
			);

			$this->add_responsive_control(
				$this->get_control_name( 'polygon_input' ),
				array(
					'type' => Controls_Manager::HIDDEN,
					'default' => 1,
					'selectors' => array( $this->transform_wrapper => $polygon_input_selector ),
					'condition' => array(
						$this->switcher_control => 'yes',
						$this->type_control => 'polygon',
					),
				)
			);
		}

		$polygon_vertices_default = ( ! $this->is_hover ) ? 'chevron-right' : '';

		$this->add_control(
			$this->get_control_name( 'polygon_vertices' ),
			array(
				'label' => __( 'Shapes', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $polygon_vertices_labels,
				'default' => $polygon_vertices_default,
				'selectors_dictionary' => $polygon_vertices_options,
				'selectors' => $this->get_selector_value( 'polygon', array(
					array( 'VALUE' ),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'polygon',
					$polygon_type_control => 'default',
				),
			)
		);

		$cut_size_css_var = $this->get_control_css_var( 'cut-size' );
		$cut_size_hover_css_var = $this->get_control_css_var( 'cut-size', false );

		$cut_corner_vertices = array(
			'top_left' => array(
				'label'  => __( 'Top Left', 'cmsmasters-elementor' ),
				'option' => "var({$cut_size_css_var}, 30px) 0, 100% 0, 100% 100%, 0 100%, 0 var({$cut_size_css_var}, 30px)",
			),
			'top_right' => array(
				'label'  => __( 'Top Right', 'cmsmasters-elementor' ),
				'option' => "0 0, calc(100% - var({$cut_size_css_var}, 30px)) 0, 100% var({$cut_size_css_var}, 30px), 100% 100%, 0 100%",
			),
			'bottom_left' => array(
				'label'  => __( 'Bottom Left', 'cmsmasters-elementor' ),
				'option' => "0 0, 100% 0, 100% 100%, var({$cut_size_css_var}, 30px) 100%, 0 calc(100% - var({$cut_size_css_var}, 30px))",
			),
			'bottom_right' => array(
				'label'  => __( 'Bottom Right', 'cmsmasters-elementor' ),
				'option' => "0 0, 100% 0, 100% calc(100% - var({$cut_size_css_var}, 30px)), calc(100% - var({$cut_size_css_var}, 30px)) 100%, 0 100%",
			),
		);

		$cut_corner_vertices_labels  = array();
		$cut_corner_vertices_options = array();

		foreach ( $cut_corner_vertices as $vertex_name => $vertex_array ) {
			$cut_corner_vertices_labels[ $vertex_name ]  = $vertex_array['label'];
			$cut_corner_vertices_options[ $vertex_name ] = $vertex_array['option'];
		}

		if ( ! $this->is_hover ) {
			$this->add_control(
				$this->get_control_name( 'cut_position' ),
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $cut_corner_vertices_labels,
					'selectors_dictionary' => $cut_corner_vertices_options,
					'selectors' => $this->get_selector_value( 'polygon', array(
						array( 'VALUE' ),
					) ),
					'default' => ( ! $this->is_hover ) ? 'top_left' : '',
					'condition' => array(
						$this->switcher_control => 'yes',
						$this->type_control     => 'polygon',
						$polygon_type_control   => 'default',
						$this->get_control_name( 'polygon_vertices' ) => 'cut-corner',
					),
				)
			);
		}

		$this->add_responsive_control(
			$this->get_control_name( 'cut_size' ),
			array(
				'label' => __( 'Cut Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$this->transform_wrapper => "{$cut_size_css_var}: {{SIZE}}{{UNIT}};",
					$this->transform_wrapper . ':hover' => "{$cut_size_hover_css_var}: {{SIZE}}{{UNIT}};",
				),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'polygon',
					$polygon_type_control => 'default',
					$this->get_control_name( 'polygon_vertices' ) => 'cut-corner',
				),
			)
		);

		$polygon_vertices_custom_description = sprintf(
			/* translators: Addon effects module clip-path custom vertices control description. %s: CSS clip-path maker link. */
			__( 'Here you can create your custom clip-path polygon. To do this, please go to %s and insert only values from generated field.', 'cmsmasters-elementor' ),
			sprintf(
				'<a href="%2$s" title="%3$s" target="_blank">%1$s</a>',
				__( 'clip-path maker website', 'cmsmasters-elementor' ),
				'https://bennettfeely.com/clippy/',
				__( 'CSS clip-path maker', 'cmsmasters-elementor' )
			)
		);

		$this->add_control(
			$this->get_control_name( 'polygon_vertices_custom' ),
			array(
				'label' => __( 'Vertices', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::TEXTAREA,
				'description' => $polygon_vertices_custom_description,
				'placeholder' => $polygon_vertices_options['star'],
				'selectors' => $this->get_selector_value( 'polygon', array(
					array( 'VALUE' ),
				) ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'polygon',
					$polygon_type_control => 'custom',
				),
			)
		);

		$polygon_output_pattern = ( $this->is_hover ) ?
			'%1$s: polygon(var(%2$s, var(%3$s)));' :
			'%1$s: polygon(var(%2$s));';

		$polygon_output_selector = sprintf(
			$polygon_output_pattern,
			$this->css_var,
			$this->get_control_css_var( 'polygon' ),
			$this->get_control_css_var( 'polygon', false )
		);

		$this->add_responsive_control(
			$this->get_control_name( 'polygon_output' ),
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
				'selectors' => array( $this->transform_wrapper => $polygon_output_selector ),
				'condition' => array(
					$this->switcher_control => 'yes',
					$this->type_control => 'polygon',
				),
			)
		);

		if ( $this->is_hover ) {
			$this->add_responsive_control(
				$this->get_control_name( 'hover_fallback_cut_position' ),
				array(
					'type' => Controls_Manager::HIDDEN,
					'default' => 1,
					'selectors' => array(
						$this->transform_wrapper . ':hover' => '--cmsmasters-clip-path-hover: polygon(var(--cmsmasters-clip-path-polygon));',
					),
					'condition' => array(
						$this->switcher_control => 'yes',
						$this->type_control     => 'polygon',
						$this->get_control_name( 'polygon_vertices') => 'cut-corner',
					),
				)
			);
		}
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

	protected function get_selector_value( $name = '', $values = '' ) {
		$css_var_value = '';

		if ( is_string( $values ) ) {
			$css_var_value = "{{{$values}}}";
		} elseif ( is_array( $values ) && ! empty( $values ) ) {
			$css_var_array = array();

			foreach ( $values as $values_key => $values_value ) {
				if ( is_string( $values_key ) ) {
					$css_var_array_value = "{{{$values_key}";

					$css_var_array_value .= ( ! $this->is_hover ) ? " || {$values_value}}}" : '}}';

					$css_var_array[] = $css_var_array_value;
				} elseif ( is_array( $values_value ) && ! empty( $values_value ) ) {
					$css_var_values_array = array();

					foreach ( $values_value as $key => $value ) {
						if ( is_string( $key ) ) {
							$css_var_values_array_value = "{{{$key}";

							$css_var_values_array_value .= ( ! $this->is_hover ) ? " || {$value}}}" : '}}';

							$css_var_values_array[] = $css_var_values_array_value;
						} else {
							$css_var_values_array[] = "{{{$value}}}";
						}
					}

					$css_var_array[] = implode( '', $css_var_values_array );
				} else {
					$css_var_array[] = "{{{$values_value}}}";
				}
			}

			$css_var_value = implode( ' ', $css_var_array );
		}

		return array(
			$this->transform_wrapper => sprintf(
				'%1$s: %2$s;',
				$this->get_control_css_var( $name ),
				$css_var_value
			),
		);
	}
}
