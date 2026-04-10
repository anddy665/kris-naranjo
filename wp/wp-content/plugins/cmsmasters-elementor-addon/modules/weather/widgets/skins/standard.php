<?php
namespace CmsmastersElementor\Modules\Weather\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Standard extends Base {
	public function get_id() {
		return 'standard';
	}

	public function get_title() {
		return __( 'Standard', 'cmsmasters-elementor' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_controls_content();
		$this->register_controls_style_main_info();
		$this->register_controls_style_additional_info();

		$this->inject_control_content();

		$this->parent->update_control(
			'temperature_feels_before',
			array(
				'default' => __( 'Feels like', 'cmsmasters-elementor' ),
			)
		);

		$this->parent->update_control(
			'humidity_before',
			array(
				'default' => __( 'Humidity', 'cmsmasters-elementor' ),
			)
		);

		$this->parent->update_control(
			'pressure_before',
			array(
				'default' => __( 'Pressure', 'cmsmasters-elementor' ),
			)
		);

		$this->parent->update_control(
			'wind_before',
			array(
				'default' => __( 'Wind', 'cmsmasters-elementor' ),
			)
		);
	}

	protected function register_controls_content() {
		$this->parent->start_injection( array(
			'of' => 'section_content',
			'type' => 'section',
		) );

		$this->add_control(
			'type',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'options' => array(
					'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
					'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
				),
				'default' => 'horizontal',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-weather-standard-type-',
			)
		);

		$this->parent->end_injection();
	}

	protected function register_controls_style_main_info() {
		$block_type = 'main_info';

		$this->start_controls_section(
			"section_style_{$block_type}",
			array(
				'label' => __( 'Main Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$block_type}_align_horizontal",
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Align Horizontal', 'cmsmasters-elementor' ),
				'label_block' => false,
				'options' => array(
					'space-between' => __( 'Default', 'cmsmasters-elementor' ),
					'space-around' => __( 'Around', 'cmsmasters-elementor' ),
					'space-evenly' => __( 'Evenly', 'cmsmasters-elementor' ),
				),
				'default' => 'space-between',
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .weather-row' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'standard_type' => 'horizontal' ),
			)
		);

		$this->add_control(
			"{$block_type}_align_vertical",
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'label_block' => false,
				'options' => array(
					'flex-start' => __( 'Top', 'cmsmasters-elementor' ),
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'flex-end' => __( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .weather-column' => 'align-self: {{VALUE}};',
				),
				'condition' => array( 'standard_type' => 'horizontal' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$block_type}_typography",
				'selector' => '{{WRAPPER}} .weather-main-info',
			)
		);

		$this->add_control(
			"{$block_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => "{$block_type}_bg",
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-weather',
			)
		);

		$this->add_responsive_control(
			"{$block_type}_padding",
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$main_info_icon_conditions = array(
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
					'name' => 'description_clear_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'description_clouds_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'description_rain_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'description_snow_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'description_storm_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'description_mist_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->add_control(
			"{$block_type}_icon_heading",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $main_info_icon_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_icon_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $main_info_icon_conditions,
			)
		);

		$this->add_responsive_control(
			"{$block_type}_icon_size",
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .weather-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-main-info .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $main_info_icon_conditions,
			)
		);

		$this->add_responsive_control(
			"{$block_type}_icon_spacing",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .weather-field-outer' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $main_info_icon_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_icon_align",
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'baseline' => __( 'Default', 'cmsmasters-elementor' ),
					'flex-start' => __( 'Top', 'cmsmasters-elementor' ),
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'flex-end' => __( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-main-info .weather-field-outer' => 'align-items: {{VALUE}};',
				),
				'conditions' => $main_info_icon_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_before_heading",
			array(
				'label' => __( 'Before Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'temperature_feels_before!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$block_type}_typography_before",
				'selector' => '{{WRAPPER}} .weather-main-info .cmsmasters_weather_field_before',
				'condition' => array( 'temperature_feels_before!' => '' ),
			)
		);

		$this->add_control(
			"{$block_type}_color_before",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .cmsmasters_weather_field_before' => 'color: {{VALUE}};',
				),
				'condition' => array( 'temperature_feels_before!' => '' ),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_gap_before",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .weather-main-info .cmsmasters_weather_field_before' => '--cmsmasters-weather-before-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'temperature_feels_before!' => '' ),
			)
		);

		$this->add_control(
			"{$block_type}_align_vertical_before",
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
					'{{WRAPPER}} .weather-field--temperature_feels .weather-field-inner' => 'align-items: {{VALUE}};',
				),
				'condition' => array( 'temperature_feels_before!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_controls_style_additional_info() {
		$block_type = 'additional_info';

		$this->start_controls_section(
			"{$block_type}_section_style",
			array(
				'label' => __( 'Additional Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => $this->get_control_id( 'humidity_show' ),
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => $this->get_control_id( 'pressure_show' ),
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => $this->get_control_id( 'wind_show' ),
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_align_horizontal",
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Align Horizontal', 'cmsmasters-elementor' ),
				'options' => array(
					'space-between' => __( 'Default', 'cmsmasters-elementor' ),
					'space-around' => __( 'Around', 'cmsmasters-elementor' ),
					'space-evenly' => __( 'Evenly', 'cmsmasters-elementor' ),
				),
				'default' => 'space-between',
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .weather-row' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$block_type}_typography",
				'selector' => '{{WRAPPER}} .weather-additional-info',
			)
		);

		$this->add_control(
			"{$block_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => "{$block_type}_bg",
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .weather-additional-info',
			)
		);

		$this->add_responsive_control(
			"{$block_type}_top_gap",
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_padding",
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$additional_info_icon_conditions = array(
			'relation' => 'or',
			'terms' => array(
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
			"{$block_type}_icon_heading",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $additional_info_icon_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_icon_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $additional_info_icon_conditions,
			)
		);

		$this->add_responsive_control(
			"{$block_type}_icon_size",
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .weather-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-additional-info .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $additional_info_icon_conditions,
			)
		);

		$this->add_responsive_control(
			"{$block_type}_icon_spacing",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .weather-field-outer' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $additional_info_icon_conditions,
			)
		);

		$additional_info_before_conditions = array(
			'relation' => 'or',
			'terms' => array(
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
			"{$block_type}_before_heading",
			array(
				'label' => __( 'Before Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $additional_info_before_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$block_type}_typography_before",
				'selector' => '{{WRAPPER}} .weather-additional-info .cmsmasters_weather_field_before',
				'conditions' => $additional_info_before_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_color_before",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .cmsmasters_weather_field_before' => 'color: {{VALUE}};',
				),
				'conditions' => $additional_info_before_conditions,
			)
		);

		$this->add_responsive_control(
			"{$block_type}_gap_before",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info .cmsmasters_weather_field_before' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $additional_info_before_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_divider_heading",
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $additional_info_icon_conditions,
			)
		);

		$this->add_control(
			"{$block_type}_divider_style",
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
				),
				'default' => 'solid',
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info:before' => 'border-top-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_alignment",
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left' => 'left: 0; right: auto;',
					'center' => 'left: 0; right: 0;',
					'right' => 'left: auto; right: 0;',
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info:before' => '{{VALUE}};',
				),
				'condition' => array( "standard_{$block_type}_divider_style!" => 'none' ),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_divider_width",
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info:before' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( "standard_{$block_type}_divider_style!" => 'none' ),
			)
		);

		$this->add_responsive_control(
			"{$block_type}_divider_height",
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-additional-info:before' => 'height: {{SIZE}}{{UNIT}}; border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( "standard_{$block_type}_divider_style!" => 'none' ),
			)
		);

		$this->add_control(
			"{$block_type}_divider_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-additional-info:before' => 'border-color: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( "standard_{$block_type}_divider_style!" => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	private function inject_control_content() {
		$this->parent->start_injection( array(
			'of' => 'section_content',
			'type' => 'section',
		) );

		$this->add_control(
			'temperature_feels_show',
			array(
				'label' => __( 'Temperature Feels', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'humidity_show',
			array(
				'label' => __( 'Humidity', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pressure_show',
			array(
				'label' => __( 'Pressure', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'wind_show',
			array(
				'label' => __( 'Wind', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->parent->end_injection();
	}

	protected function set_main_info() {
		echo '<div class="weather-main-info">' .
			'<div class="weather-row">' .
				'<div class="weather-column">';

					$this->render_field( 'region' );

		if ( 'text' === $this->get_instance_value( 'description_view' ) ) {
			$this->render_field( 'description', false );
		} else {
			$this->render_field( 'description' );
		}

					$this->render_field( 'temperature' );

		if ( $this->get_instance_value( 'temperature_feels_show' ) ) {
			$this->render_field( 'temperature_feels' );
		}

				echo '</div>' .
				'<div class="weather-column">';

		if ( 'text' === $this->get_instance_value( 'description_view' ) ) {
			$this->render_field( 'description', false );
		} else {
			$this->render_field( 'description' );
		}

				echo '</div>' .
			'</div>' .
		'</div>';
	}

	protected function set_additional_info() {
		if (
			$this->get_instance_value( 'humidity_show' ) ||
			$this->get_instance_value( 'pressure_show' ) ||
			$this->get_instance_value( 'wind_show' )
		) {
			echo '<div class="weather-additional-info">' .
				'<div class="weather-additional-info-inner">' .
					'<div class="weather-row">';

			if ( $this->get_instance_value( 'humidity_show' ) ) {
				echo '<div class="weather-column">';
					$this->render_field( 'humidity' );
				echo '</div>';
			}

			if ( $this->get_instance_value( 'pressure_show' ) ) {
				echo '<div class="weather-column">';
					$this->render_field( 'pressure' );
				echo '</div>';
			}

			if ( $this->get_instance_value( 'wind_show' ) ) {
				echo '<div class="weather-column">';
					$this->render_field( 'wind' );
				echo '</div>';
			}

					echo '</div>' .
				'</div>' .
			'</div>';
		}
	}

	protected function render_inner() {
		$this->set_main_info();

		$this->set_additional_info();
	}
}
