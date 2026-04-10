<?php

namespace CmsmastersElementor\Modules\CircleProgressBar\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Circle_Progress_Bar extends Base_Widget {

	protected $widget_name = 'circle-progress-bar';

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Circle Progress Bar', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-progress-bar';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array( 'circle', 'progress', 'bar' );
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'donutty',
		), parent::get_script_depends() );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-circle-progress-bar',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void Widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'progress_bar_items',
			array(
				'label' => __( 'Progress Bars', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'progress_tabs' );

		$repeater->start_controls_tab( 'content_tab', array( 'label' => __( 'Content', 'cmsmasters-elementor' ) ) );

		$repeater->add_control(
			'value',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'Progress Title', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'label_block' => false,
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'settings_tab', array( 'label' => __( 'Settings', 'cmsmasters-elementor' ) ) );

		$repeater->add_control(
			'custom_value',
			array(
				'label' => __( 'Custom Value', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'type' => Controls_Manager::NUMBER,
			)
		);

		$repeater->add_control(
			'start_value',
			array(
				'label' => __( 'Start Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'dynamic' => array( 'active' => true ),
				'condition' => array(
					'custom_value!' => '',
				),
			)
		);

		$repeater->add_control(
			'value_prefix',
			array(
				'label' => __( 'Value Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'value_suffix',
			array(
				'label' => __( 'Value Suffix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '%', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'line_color',
			array(
				'label' => __( 'Line Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-current-item-line-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'line_background_color',
			array(
				'label' => __( 'Line Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-current-item-line-bg-color: {{VALUE}};',
				),
			)
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'progress_bars',
			array(
				'label' => __( 'Progress Bars', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'text' => __( 'Progress Bar 1', 'cmsmasters-elementor' ),
						'value' => array(
							'size' => 25,
							'unit' => 'px',
						),
					),
					array(
						'text' => __( 'Progress Bar 2', 'cmsmasters-elementor' ),
						'value' => array(
							'size' => 50,
							'unit' => 'px',
						),
					),
					array(
						'text' => __( 'Progress Bar 3', 'cmsmasters-elementor' ),
						'value' => array(
							'size' => 75,
							'unit' => 'px',
						),
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'content_items',
			array(
				'label' => __( 'Content Items', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::SELECTIZE,
				'default' => array(
					'value',
					'title',
				),
				'options' => $this->get_progress_content_items(),
				'multiple' => true,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'Title Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
				),
				'default' => 'h5',
				'condition' => array( 'content_items' => 'title' ),
			)
		);

		$this->add_control(
			'progress_bar_global_settings',
			array(
				'label' => __( 'Global Settings', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Colums', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 6,
					),
				),
				'default' => array(
					'size' => '3',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-progress-bar-columns: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'progress_bar_gap',
			array(
				'label' => __( 'Colums Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 20,
					),
					'vw' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-progress-bar-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'columns[size]!' => '1',
				),
			)
		);

		$this->add_control(
			'thickness',
			array(
				'label' => __( 'Line Thickness', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 2,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->add_control(
			'padding',
			array(
				'label' => __( 'Background Line Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 4,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->add_control(
			'rotate',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-progress-bar-rotate: {{SIZE}}deg;',
				),
			)
		);

		$this->add_control(
			'circle',
			array(
				'label' => __( 'Arch View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'default' => 'no',
			)
		);

		$this->add_control(
			'round',
			array(
				'label' => __( 'Round Corners', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'duration',
			array(
				'label' => __( 'Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '1500',
			)
		);

		$this->add_control(
			'step_by_step',
			array(
				'label' => __( 'Step By Step', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'default' => 'no',
			)
		);

		$this->add_control(
			'widget_delay',
			array(
				'label' => __( 'Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '500',
				'condition' => array(
					'step_by_step' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'progress_bar_wrapper_section',
			array(
				'label' => __( 'Progress Bar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'progress_bar_wrapper_heading',
			array(
				'label' => __( 'Box', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'wrapper_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wrapper-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wrapper_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'wrapper_box_shadow',
				'label' => __( 'Box Shadow', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__wrapper',
			)
		);

		$this->add_responsive_control(
			'wrapper_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wrapper-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wrapper-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'progress_bar_line_heading',
			array(
				'label' => __( 'Progress Line', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'default_progress_bar_color',
			array(
				'label' => __( 'Line Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'mediumslateblue',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-default-item-line-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'default_progress_bar_bg_color',
			array(
				'label' => __( 'Background Line Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#E0E7EB',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-default-item-line-bg-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Progress Bar Content Section
		$this->start_controls_section(
			'progress_bar_content_section',
			array(
				'label' => __( 'Progress Bar Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_wrapper_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-content-wrapper-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'content_wrapper_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'content_wrapper_box_shadow',
				'label' => __( 'Box Shadow', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__content-wrapper',
			)
		);

		$this->add_responsive_control(
			'box_size',
			array(
				'label' => __( 'Box Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
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
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-content-wrapper-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_wrapper_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-content-wrapper-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Value
		$this->add_control(
			'value_heading',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'content_items' => 'value' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'value_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__wrapper-value',
				'condition' => array( 'content_items' => 'value' ),
			)
		);

		$this->add_control(
			'value_color',
			array(
				'label' => __( 'Value Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--value-color: {{VALUE}};',
				),
				'condition' => array( 'content_items' => 'value' ),
			)
		);

		$this->add_control(
			'value_prefix_color',
			array(
				'label' => __( 'Value Prefix Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--value-pr-color: {{VALUE}};',
				),
				'condition' => array( 'content_items' => 'value' ),
			)
		);

		$this->add_control(
			'value_suffix_color',
			array(
				'label' => __( 'Value Suffix Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--value-sff-color: {{VALUE}};',
				),
				'condition' => array( 'content_items' => 'value' ),
			)
		);

		$this->add_responsive_control(
			'value_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--value-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Title
		$this->add_control(
			'title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'content_items' => 'title' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-circle-progress-bar__title',
				'condition' => array( 'content_items' => 'title' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-color: {{VALUE}};',
				),
				'condition' => array( 'content_items' => 'title' ),
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'content_items' => 'title' ),
			)
		);

		// Icon
		$this->add_control(
			'icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'content_items' => 'icon' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-color: {{VALUE}};',
				),
				'condition' => array( 'content_items' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
					'rem' => array(
						'min' => 0,
						'max' => 50,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'content_items' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'content_items' => 'icon' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$this->render_progress_bar();
	}

	protected function render_progress_bar() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'widget', array(
			'class' => array( 'cmsmasters-circle-progress-bar' ),
		));

		if ( 'yes' === $settings['circle'] ) {
			$this->add_render_attribute( 'widget', array(
				'class' => array( 'cmsmasters-circle-progress-bar__arch' ),
			));
		}

		$attr_widget = $this->get_render_attribute_string( 'widget' );

		$is_editor = CmsmastersPlugin::elementor()->editor->is_edit_mode();

		if ( $is_editor ) {
			echo '<span class="cms-prss-hide">progress</span>';
		}

		if ( ! empty( $settings['progress_bars'] ) ) {
			echo "<div {$attr_widget}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			foreach ( $settings['progress_bars'] as $index => $progress_bar ) {

				$duration = ( empty( $settings['duration'] ) ? 1500 : (int) $settings['duration'] );

				if ( 'yes' === $settings['step_by_step'] ) {
					$widget_delay = (int) $settings['widget_delay'] * $index;
				} else {
					$widget_delay = 0;
				}

				$widget_delay = (string) $widget_delay;

				$options = array(
					'min' => 0,
					'max' => 100,
					'value' => (int) $progress_bar['value']['size'],
					'custom_value' => ( ! empty( $progress_bar['custom_value'] ) ? (int) $progress_bar['custom_value'] : (int) $progress_bar['value']['size'] ),
					'start_value' => ( ! empty( $progress_bar['start_value'] ) ? (int) $progress_bar['start_value'] : 0 ),
					'round' => 'yes' === $settings['round'] ? true : false,
					'circle' => 'yes' === $settings['circle'] ? false : true,
					'padding' => (int) $settings['padding']['size'],
					'radius' => 50,
					'thickness' => (int) $settings['thickness']['size'],
					'bg' => 'transparent',
					'color' => 'transparent',
					'duration' => (int) $duration,
					'transition' => "all {$duration}ms cubic-bezier(0.57, 0.13, 0.18, 0.98) {$widget_delay}ms",
					'step_by_step' => esc_attr( $settings['step_by_step'] ),
					'widget_delay' => (int) $widget_delay,
				);

				$repeater_item_setting_key = $this->get_repeater_setting_key( 'wrapper', 'progress_bars', $index );

				$this->add_render_attribute( $repeater_item_setting_key, 'class', "elementor-repeater-item-{$progress_bar['_id']}" );

				$this->add_render_attribute( $repeater_item_setting_key, array(
					'data-options' => wp_json_encode( $options ),
					'id' => 'donut_' . $index,
				));

				$attr_wrapper = $this->get_render_attribute_string( $repeater_item_setting_key );

				echo "<div {$attr_wrapper}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$this->progress_content( $progress_bar );
				echo "</div>";
			}
			echo '</div>';
		}
	}

	public function get_progress_content_items() {
		return array(
			'icon' => __( 'Icon', 'cmsmasters-elementor' ),
			'title' => __( 'Title', 'cmsmasters-elementor' ),
			'value' => __( 'Value', 'cmsmasters-elementor' ),
		);
	}

	public function progress_content( $progress_bar ) {
		$settings = $this->get_settings_for_display();

		echo "<div class='cmsmasters-circle-progress-bar__content-wrapper'>";

		foreach ( $settings['content_items'] as $item ) {
			$this->progress_content_items( $progress_bar, $item );
		}

		echo "</div>";
	}

	public function progress_content_items( $progress_bar, $item ) {

		switch ( $item ) {
			case 'icon':
				$this->get_progress_icon( $progress_bar );

				break;
			case 'title':
				$this->get_progress_title( $progress_bar );

				break;
			case 'value':
				$this->get_progress_value( $progress_bar );

				break;
		}
	}

	public function get_progress_icon( $progress_bar ) {
		if ( ! empty( $progress_bar['icon'] ) ) {
			CmsmastersUtils::render_icon( $progress_bar['icon'], array( 'aria-hidden' => 'true' ), true );
		}
	}

	public function get_progress_title( $progress_bar ) {
		$settings = $this->get_settings_for_display();

		$title_tag = $settings['title_tag'];
		$title = ( ! empty( $progress_bar['title'] ) ? esc_html( $progress_bar['title'] ) : __( 'Progress Title', 'cmsmasters-elementor' ) );

		echo "<" . Utils::validate_html_tag( $title_tag ) . " class='cmsmasters-circle-progress-bar__title'>" . $title . "</" . Utils::validate_html_tag( $title_tag ) . ">";
	}

	public function get_progress_value( $progress_bar ) {
		$settings = $this->get_settings_for_display();

		$default_value = $progress_bar['value']['size'];
		$custom_value = $progress_bar['custom_value'];
		$value_prefix = $progress_bar['value_prefix'];
		$value_suffix = $progress_bar['value_suffix'];

		$value = ( '0' === $custom_value ? $default_value : $custom_value );

		echo "<span class='cmsmasters-circle-progress-bar__wrapper-value'>";
			if ( ! empty( $value_prefix ) ) {
				echo "<span class='cmsmasters-circle-progress-bar__prefix-value'>" . esc_html( $value_prefix ) . "</span>";
			}

			echo "<span class='cmsmasters-circle-progress-bar__value'>" . esc_html( $value ) . "</span>";

			if ( ! empty( $value_suffix ) ) {
				echo "<span class='cmsmasters-circle-progress-bar__suffix-value'>" . esc_html( $value_suffix ) . "</span>";
			}
		echo "</span>";
	}

		/**
	 * Render Hotspot widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
		var widgetClasses = ['cmsmasters-circle-progress-bar'];

		if (settings.circle === 'yes' ) {
			widgetClasses.push( 'cmsmasters-circle-progress-bar__arch' );
		}

		view.addRenderAttribute( 'widget', 'class', widgetClasses.join( ' ' ));
		var attrWidget = view.getRenderAttributeString( 'widget' );
		#>

		<span class="cms-prss-hide">progress</span>
		<div {{{ attrWidget }}}>
			<#
			if (settings.progress_bars && settings.progress_bars.length) {
				_.each(settings.progress_bars, function(progress_bar, index) {
					var duration = settings.duration ? parseInt(settings.duration) : 1500;
					var widgetDelay = settings.step_by_step === 'yes' ? parseInt(settings.widget_delay) * index : 0;

					var options = {
						min: 0,
						max: 100,
						value: parseInt(progress_bar.value.size) || 0,
						custom_value: progress_bar.custom_value ? parseInt(progress_bar.custom_value) : parseInt(progress_bar.value.size),
						start_value: progress_bar.start_value ? parseInt(progress_bar.start_value) : 0,
						round: settings.round === 'yes',
						circle: settings.circle !== 'yes',
						padding: parseInt(settings.padding.size) || 0,
						radius: 50,
						thickness: parseInt(settings.thickness.size) || 0,
						bg: 'transparent',
						color: 'transparent',
						duration: duration,
						transition: `all ${duration}ms cubic-bezier(0.57, 0.13, 0.18, 0.98) ${widgetDelay}ms`,
						step_by_step: settings.step_by_step,
						widget_delay: widgetDelay
					};

					var wrapperSettingKey = view.getRepeaterSettingKey( 'progress_bar', 'progress_bars', index);

					var repeaterItemClass = 'elementor-repeater-item-' + progress_bar._id;

					view.addRenderAttribute(wrapperSettingKey, {
						'class': repeaterItemClass
					});

					view.addRenderAttribute(wrapperSettingKey, {
						'data-options': JSON.stringify(options),
						'id': 'donut_' + index
					});

					var attrWrapper = view.getRenderAttributeString(wrapperSettingKey);
					#>
					<div {{{ view.getRenderAttributeString(wrapperSettingKey) }}}>
					<#

					var contentHtml = '';

					_.each(settings.content_items, function(item) {
						switch (item) {
							case 'icon':
								if (progress_bar.icon) {
									var iconHTML = elementor.helpers.renderIcon(view, progress_bar.icon, { 'aria-hidden': 'true' }, 'i', 'object' );
									contentHtml += `<div class='cmsmasters-wrap-icon'>${iconHTML.value}</div>`;
								}
								break;
							case 'title':
								var titleTag = settings.title_tag || 'h3';
								var title = progress_bar.title || 'Progress Title';
								contentHtml += `<${titleTag} class='cmsmasters-circle-progress-bar__title'>${title}</${titleTag}>`;
								break;
							case 'value':
								$default_value = progress_bar.value.size;
								custom_value = progress_bar['custom_value'];
								var value = '0' === custom_value ? default_value : custom_value;
								var valuePrefix = progress_bar.value_prefix || '';
								var valueSuffix = progress_bar.value_suffix || '';

								contentHtml += `
									<span class='cmsmasters-circle-progress-bar__wrapper-value'>
										${valuePrefix ? `<span class='cmsmasters-circle-progress-bar__prefix-value'>${valuePrefix}</span>` : ''}
										<span class='cmsmasters-circle-progress-bar__value'>${value}</span>
										${valueSuffix ? `<span class='cmsmasters-circle-progress-bar__suffix-value'>${valueSuffix}</span>` : ''}
									</span>`;
								break;
						}
					});
					#>
						<div class='cmsmasters-circle-progress-bar__content-wrapper'>
							{{{ contentHtml }}}
						</div>
					</div>
					<#
				});
			}
			#>
		</div>
		<?php
	}
}
