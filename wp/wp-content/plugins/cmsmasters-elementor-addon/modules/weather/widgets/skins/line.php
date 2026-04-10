<?php
namespace CmsmastersElementor\Modules\Weather\Widgets\Skins;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Classes\Separator;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Line extends Base {
	private $items;
	private $separator;

	public function get_id() {
		return 'line';
	}

	public function get_title() {
		return __( 'Line', 'cmsmasters-elementor' );
	}

	public function __construct( Base_Widget $parent ) {
		parent::__construct( $parent );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->add_control_item();
		$this->register_controls_style_common();

		$this->update_section_weather();
	}

	private function update_section_weather() {
		$conditions_template = array(
			'terms' => array(
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => '_skin',
							'operator' => '==',
							'value' => $this->get_id(),
						),
					),
				),
			),
		);

		foreach ( $this->get_items() as $item => $item_label ) {
			$section_name = "section_style_{$item}";
			$section = $this->parent->get_controls( $section_name );

			if ( ! $section ) {
				continue;
			}

			$conditions = $conditions_template;

			$conditions['terms'][0]['terms'][] = array(
				'name' => $this->get_control_id( 'items' ),
				'operator' => 'contains',
				'value' => $item,
			);

			if ( isset( $section['conditions']['terms'] ) ) {
				$conditions['terms'] = $section['conditions']['terms'];
			}

			$this->parent->update_control( $section_name, array(
				'conditions' => $conditions,
			) );
		}
	}

	private function get_separator_config() {
		return array(
			'name' => 'weather_line_separator',
			'selector' => '{{WRAPPER}} .cmsmasters-weather-inner > .weather-field',
			'skin' => $this,
		);
	}

	private function add_control_item() {
		$this->parent->start_injection( array(
			'of' => 'section_content',
			'type' => 'section',
		) );

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'flex-start',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather-inner' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::SELECTIZE,
				'default' => array(
					'region',
					'temperature',
					'description',
				),
				'options' => $this->get_items(),
				'multiple' => true,
			)
		);

		$this->separator = new Separator( $this->parent, $this->get_separator_config() );

		$this->separator->add_controls();

		$this->parent->end_injection();
	}

	protected function register_controls_style_common() {
		$this->start_controls_section(
			'section_style_common',
			array(
				'label' => __( 'Common', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				),
			)
		);

		$common_icon_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'region_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'temperature_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'temperature_feels_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'humidity_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'pressure_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'wind_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $common_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $common_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field-outer' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $common_icon_conditions,
			)
		);

		$common_before_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'temperature_feels_before',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'pressure_before',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'humidity_before',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'wind_before',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->add_control(
			'before_heading',
			array(
				'label' => __( 'Before Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $common_before_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_before',
				'selector' => '{{WRAPPER}} .cmsmasters_weather_field_before',
				'conditions' => $common_before_conditions,
			)
		);

		$this->add_control(
			'_color_before',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_weather_field_before' => 'color: {{VALUE}};',
				),
				'conditions' => $common_before_conditions,
			)
		);

		$this->add_responsive_control(
			'gap_before',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_weather_field_before' => '--cmsmasters-weather-before-spacing: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $common_before_conditions,
			)
		);

		$this->add_control(
			'align_vertical_before',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'baseline' => __( 'Default', 'cmsmasters-elementor' ),
					'flex-start' => __( 'Top', 'cmsmasters-elementor' ),
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'flex-end' => __( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'baseline',
				'selectors' => array(
					'{{WRAPPER}} .weather-field-inner' => 'align-items: {{VALUE}};',
				),
				'conditions' => $common_before_conditions,
			)
		);

		$this->end_controls_section();
	}

	protected function get_items() {
		return array(
			'region' => __( 'Region', 'cmsmasters-elementor' ),
			'temperature' => __( 'Temperature', 'cmsmasters-elementor' ),
			'temperature_feels' => __( 'Temperature Feels', 'cmsmasters-elementor' ),
			'description' => __( 'Description', 'cmsmasters-elementor' ),
			'humidity' => __( 'Humidity', 'cmsmasters-elementor' ),
			'pressure' => __( 'Pressure', 'cmsmasters-elementor' ),
			'wind' => __( 'Wind', 'cmsmasters-elementor' ),
		);
	}

	protected function render_field_close() {
		if ( next( $this->items ) ) {

			$separator = new Separator( $this->parent, $this->get_separator_config() );

			$separator->render();
		}

		parent::render_field_close();
	}

	public function render_inner() {
		$this->items = $this->get_instance_value( 'items' );

		if ( ! $this->items ) {
			return;
		}

		foreach ( $this->items as $item ) {
			$this->render_field( $item );
		}
	}
}
