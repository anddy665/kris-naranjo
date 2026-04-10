<?php
namespace CmsmastersElementor\Modules\ProgressTracker\Widgets;

use CmsmastersElementor\Base\Base_Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addon progress tracker widget.
 *
 * Addon widget that displays progress tracker.
 *
 * @since 1.7.0
 */
class Progress_Tracker extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve progress tracker widget name.
	 *
	 * @since 1.7.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-progress-tracker';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve progress tracker widget title.
	 *
	 * @since 1.7.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Progress Tracker', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve progress tracker widget icon.
	 *
	 * @since 1.7.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-progress-tracker';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'progress',
			'tracker',
			'read',
			'scroll',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
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
			'widget-cmsmasters-progress-tracker',
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

	protected function register_controls() {
		$this->register_content_controls();

		$this->register_container_style_controls();

		$this->register_indicator_style_controls();

		$this->register_content_style_controls();
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_content_scrolling_tracker',
			array(
				'label' => esc_html__( 'Progress Tracker', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'type',
			array(
				'label' => esc_html__( 'Tracker Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'cmsmasters-elementor' ),
					'circular' => esc_html__( 'Circular', 'cmsmasters-elementor' ),
				),
				'default' => 'horizontal',
			)
		);

		$this->add_control(
			'relative_to',
			array(
				'label' => esc_html__( 'Progress relative to', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => array(
					'entire_page' => esc_html__( 'Entire Page', 'cmsmasters-elementor' ),
					'post_content' => esc_html__( 'Post Content', 'cmsmasters-elementor' ),
					'selector' => esc_html__( 'Selector', 'cmsmasters-elementor' ),
				),
				'default' => 'entire_page',
			)
		);

		$this->add_control(
			'selector',
			array(
				'label' => esc_html__( 'Selector', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'Add the CSS ID or Class of a specific element on this page to track its progress separately', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'relative_to' => 'selector',
				),
				'placeholder' => '#id, .class',
			)
		);

		$this->add_control(
			'relative_to_description',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note: You can only track progress relative to Post Content on a single post template.', 'cmsmasters-elementor' ),
				'separator' => 'none',
				'content_classes' => 'elementor-descriptor',
				'condition' => array(
					'relative_to' => 'post_content',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label' => esc_html__( 'Direction', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'ltr' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'rtl' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'render_type' => 'template',
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--direction: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'percentage',
			array(
				'label' => esc_html__( 'Percentage', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'no',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'percentage_position',
			array(
				'label' => esc_html__( 'Percentage Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'rtl' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'ltr' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'condition' => array(
					'type' => 'horizontal',
					'percentage' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--text-direction: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_container_style_controls() {
		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => esc_html__( 'Container', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'container_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--circular-width: {{SIZE}}{{UNIT}}; --circular-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_responsive_control(
			'container_bg_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--circular-background-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_control(
			'container_circular_bg',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--circular-background-color: {{VALUE}}',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'container_horizontal_bg',
				'types' => array( 'classic', 'gradient' ),
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-scrolling-tracker-horizontal',
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_responsive_control(
			'container_height',
			array(
				'label' => esc_html__( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'vh' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
					'vh',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--horizontal-height: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--tracker-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_responsive_control(
			'container_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'horizontal_container_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-scrolling-tracker',
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_control(
			'circle_container_box_shadow_popover',
			array(
				'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( 'type' => 'circular' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-scrolling-tracker .circle' => 'filter: drop-shadow({{horizontal_box_shadow.SIZE}}px {{vertical_box_shadow.SIZE}}px {{blur_box_shadow.SIZE}}px {{box_shadow_color.VALUE}});',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'box_shadow_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'circle_container_box_shadow!' => '' ),
			)
		);

		$this->add_responsive_control(
			'horizontal_box_shadow',
			array(
				'label' => esc_html__( 'Horizontal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
				'default' => array( 'size' => '0' ),
				'condition' => array( 'circle_container_box_shadow!' => '' ),
			)
		);

		$this->add_responsive_control(
			'vertical_box_shadow',
			array(
				'label' => esc_html__( 'Vertical', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
				'default' => array( 'size' => '0' ),
				'condition' => array( 'circle_container_box_shadow!' => '' ),
			)
		);

		$this->add_responsive_control(
			'blur_box_shadow',
			array(
				'label' => esc_html__( 'Blur', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array( 'px' ),
				'default' => array( 'size' => '10' ),
				'condition' => array( 'circle_container_box_shadow!' => '' ),
			)
		);

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'container_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'none',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array( 'none' ),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array( 'none' ),
						),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-scrolling-tracker-horizontal',
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_indicator_style_controls() {
		$this->start_controls_section(
			'section_style_indicator',
			array(
				'label' => esc_html__( 'Indicator', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'indicator_align',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left' => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right' => 'margin-left: auto; margin-right: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-scrolling-tracker' => '{{VALUE}};',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_responsive_control(
			'indicator_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--circular-progress-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_control(
			'indicator_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--circular-color: {{VALUE}}',
				),
				'condition' => array( 'type' => 'circular' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'indicator_bg',
				'types' => array( 'classic', 'gradient' ),
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'background' => array(
						'label' => esc_html__( 'Progress Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .current-progress',
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_responsive_control(
			'indicator_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--progress-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'indicator_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'none',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array( 'none' ),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array( 'none' ),
						),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-scrolling-tracker-horizontal .current-progress',
				'condition' => array( 'type' => 'horizontal' ),
			)
		);

		$this->end_controls_section();
	}

	private function register_content_style_controls() {
		$this->start_controls_section(
			'section__content_style_scrolling_tracker',
			array(
				'label' => esc_html__( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'percentage' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'percentage_typography',
				'selector' => '{{WRAPPER}} .current-progress-percentage',
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->add_control(
			'percentage_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--percentage-color: {{VALUE}}',
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'percentage_text_shadow',
				'selector' => '{{WRAPPER}} .current-progress-percentage',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$horizontal = 'horizontal' === $settings['type'];

		$this->add_render_attribute( 'scrolling-tracker', 'class', array(
			'cmsmasters-scrolling-tracker',
			'cmsmasters-scrolling-tracker-' . $settings['type'],
		) );

		$this->add_render_attribute( 'scrolling-percentage', 'class', 'current-progress-percentage' );

		echo '<div ' . $this->get_render_attribute_string( 'scrolling-tracker' ) . '>';

		if ( $horizontal ) {
			echo '<div class="current-progress">' .
				'<div ' . $this->get_render_attribute_string( 'scrolling-percentage' ) . '></div>' .
			'</div>';
		} else {
			echo '<svg width="100%" height="100%">' .
				'<circle class="circle" r="40%" cx="50%" cy="50%"/>' .
				'<circle class="current-progress" r="40%" cx="50%" cy="50%"/>' .
			'</svg>' .
			'<div ' . $this->get_render_attribute_string( 'scrolling-percentage' ) . '></div>';
		}

		echo '</div>';
	}

	public function render_plain_content() {}
}
