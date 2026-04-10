<?php
namespace CmsmastersElementor\Modules\Countdown\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Countdown extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-countdown';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Countdown', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-countdown';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'countdown',
			'number',
			'timer',
			'time',
			'date',
			'evergreen',
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
			'widget-cmsmasters-countdown',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-countdown';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.8.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_countdown_controls_content();

		$this->register_boxes_controls_style();

		$this->register_content_controls_style();

		$this->register_expire_message_controls_style();
	}

	/**
	 * Register content controls.
	 *
	 * Adds different input fields to allow the user to change and customize the content settings.
	 *
	 * @since 1.8.0
	 * @since 1.9.2 Added a new type "Dynamic" and `Type`, `Dates` and `Choose Dynamic Tag` controls for this type.
	 * @since 1.18.0 Fixed countdown_due_date format.
	 *
	 * @access protected
	 */
	protected function register_countdown_controls_content() {
		$this->start_controls_section(
			'countdown_section_content',
			array( 'label' => esc_html__( 'Countdown', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'countdown_type',
			array(
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'due_date' => esc_html__( 'Due Date', 'cmsmasters-elementor' ),
					'dynamic' => esc_html__( 'Dynamic', 'cmsmasters-elementor' ),
					'evergreen' => esc_html__( 'Evergreen Timer', 'cmsmasters-elementor' ),
				),
				'default' => 'due_date',
			)
		);

		$this->add_control(
			'countdown_due_date',
			array(
				'label' => esc_html__( 'Due Date', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => gmdate( 'Y-m-d H:i', strtotime( '+1 month' ) + ( floatval( get_option( 'gmt_offset' ) ) * HOUR_IN_SECONDS ) ),
				'description' => sprintf( esc_html__( 'Date set according to your timezone: %s.', 'cmsmasters-elementor' ), Utils::get_timezone_string() ),
				'condition' => array( 'countdown_type' => 'due_date' ),
			)
		);

		$this->add_control(
			'countdown_dynamic_type',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'current' => __( 'Current', 'cmsmasters-elementor' ),
					'selected' => __( 'Selected', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'current',
				'render_type' => 'template',
				'condition' => array( 'countdown_type' => 'dynamic' ),
			)
		);

		$this->add_control(
			'countdown_dynamic_current_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Current type can only be applied for Events by Events Calendar and WooCommerce Products.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'countdown_type' => 'dynamic',
					'countdown_dynamic_type' => 'current',
				),
			)
		);

		$this->add_control(
			'countdown_dynamic_dates',
			array(
				'label' => __( 'Dates', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'start' => __( 'Start', 'cmsmasters-elementor' ),
					'end' => __( 'End', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'end',
				'condition' => array(
					'countdown_type' => 'dynamic',
					'countdown_dynamic_type' => 'current',
				),
			)
		);

		$this->add_control(
			'countdown_dynamic',
			array(
				'label' => esc_html__( 'Choose Dynamic Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'countdown_type' => 'dynamic',
					'countdown_dynamic_type' => 'selected',
				),
				'description' => sprintf(
					'%1$s <a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%2$s</a>',
					__( 'For manual date entry see the', 'cmsmasters-elementor' ),
					__( 'documentation on date and time formatting', 'cmsmasters-elementor' )
				),
			)
		);

		$this->add_control(
			'countdown_evergreen_counter_hours',
			array(
				'label' => esc_html__( 'Hours', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 47,
				'placeholder' => esc_html__( 'Hours', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'countdown_type' => 'evergreen' ),
			)
		);

		$this->add_control(
			'countdown_evergreen_counter_minutes',
			array(
				'label' => esc_html__( 'Minutes', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 59,
				'placeholder' => esc_html__( 'Minutes', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'countdown_type' => 'evergreen' ),
			)
		);

		$this->add_control(
			'countdown_show_labels',
			array(
				'label' => esc_html__( 'Show Labels', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'countdown_custom_labels_tabs' );

		foreach ( array(
			'days' => 'Days',
			'hours' => 'Hours',
			'minutes' => 'Minutes',
			'seconds' => 'Seconds',
		) as $label => $value ) {
			$this->start_controls_tab(
				"countdown_custom_labels_{$label}_tab",
				array( 'label' => $value )
			);

			$this->add_control(
				"countdown_show_{$label}",
				array(
					'label' => esc_html__( 'Show', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
					'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				"countdown_label_{$label}",
				array(
					'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => esc_html( $value ),
					'dynamic' => array( 'active' => true ),
					'condition' => array(
						'countdown_show_labels!' => '',
						"countdown_show_{$label}" => 'yes',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'countdown_colon_separator_show',
			array(
				'label' => esc_html__( 'Colon Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => '',
				'separator' => 'before',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'countdown_expire_actions',
			array(
				'label' => esc_html__( 'Actions After Expire', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => array(
					'redirect' => esc_html__( 'Redirect', 'cmsmasters-elementor' ),
					'hide' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
					'message' => esc_html__( 'Show Message', 'cmsmasters-elementor' ),
				),
				'label_block' => true,
				'render_type' => 'none',
				'multiple' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'countdown_message_after_expire',
			array(
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'countdown_expire_actions' => 'message' ),
			)
		);

		$this->add_control(
			'countdown_expire_redirect_url',
			array(
				'label' => esc_html__( 'Redirect URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'countdown_expire_actions' => 'redirect' ),
			)
		);

		$this->add_control(
			'countdown_responsive_view',
			array(
				'label' => __( 'Responsive View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'tablet' => __( 'Tablet', 'cmsmasters-elementor' ),
					'mobile' => __( 'Mobile', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'mobile',
				'render_type' => 'template',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-countdown-responsive-view-',
			)
		);

		$this->end_controls_section();
	}

	protected function register_boxes_controls_style() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'boxes_section_style',
			array(
				'label' => esc_html__( 'Boxes', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'boxes_align',
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
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'boxes_container_width',
			array(
				'label' => esc_html__( 'Container Max Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
					'vw',
					'vh',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array( 'unit' => '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-container-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'boxes_bg',
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'color' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__item',
			)
		);

		$this->add_control(
			'boxes_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-border-color: {{VALUE}};',
				),
				'condition' => array( 'boxes_border_border!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'boxes_spacing',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'boxes_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'boxes_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'boxes_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array(
							'{{SELECTOR}}' => '--boxes-border-style: {{VALUE}};',
						),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'{{SELECTOR}}' => '--boxes-border-top-width: {{TOP}}{{UNIT}}; --boxes-border-right-width: {{RIGHT}}{{UNIT}}; --boxes-border-bottom-width: {{BOTTOM}}{{UNIT}}; --boxes-border-left-width: {{LEFT}}{{UNIT}};',
						),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'boxes_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--boxes-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'boxes_colon_separator_heading',
			array(
				'label' => esc_html__( 'Colon Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'countdown_colon_separator_show!' => '' ),
			)
		);

		$this->add_responsive_control(
			'boxes_colon_separator_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-colon-separator-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'countdown_colon_separator_show!' => '' ),
			)
		);

		$this->add_control(
			'boxes_colon_separator_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--boxes-colon-separator-color: {{VALUE}};',
				),
				'condition' => array( 'countdown_colon_separator_show!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_controls_style() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'content_section_style',
			array(
				'label' => esc_html__( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_digits_heading',
			array(
				'label' => esc_html__( 'Digits', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_digits_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'content_digits_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--content-digits-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'content_digits_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			array(
				'name' => 'content_digits_text_stroke',
				'fields_options' => array(
					'text_stroke' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-text-stroke-width: {{SIZE}}{{UNIT}};',
						),
					),
					'stroke_color' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-digits-text-stroke-color: {{VALUE}}; stroke: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'content_label_heading',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'content_label_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
				),
				'selectors_dictionary' => array(
					'top' => '--content-label-position: row; --content-label-order: -1;',
					'right' => '--content-label-position: column; --content-label-order: 1;',
					'bottom' => '--content-label-position: row; --content-label-order: 1;',
					'left' => '--content-label-position: column; --content-label-order: -1;',
				),
				'label_block' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_label_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'center' => __( 'Center', 'cmsmasters-elementor' ),
					'baseline' => __( 'Baseline', 'cmsmasters-elementor' ),
				),
				'toggle' => true,
				'label_block' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--content-label-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_label_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'content_label_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--content-label-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_label_gap_between',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--content-label-gap-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'content_label_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			array(
				'name' => 'content_label_text_stroke',
				'fields_options' => array(
					'text_stroke' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-text-stroke-width: {{SIZE}}{{UNIT}};',
						),
					),
					'stroke_color' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--content-label-text-stroke-color: {{VALUE}}; stroke: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_controls_section();
	}

	protected function register_expire_message_controls_style() {
		$this->start_controls_section(
			'expire_message_section_style',
			array(
				'label' => esc_html__( 'Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'countdown_expire_actions' => 'message' ),
			)
		);

		$this->add_responsive_control(
			'expire_message_align',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--expire-message-align: {{VALUE}};',
				),
				'condition' => array( 'countdown_expire_actions' => 'message' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'expire_message_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--expire-message-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'countdown_expire_actions' => 'message' ),
			)
		);

		$this->add_control(
			'expire_message_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--expire-message-color: {{VALUE}};',
				),
				'condition' => array( 'countdown_expire_actions' => 'message' ),
			)
		);

		$this->add_responsive_control(
			'expire_message_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--expire-message-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'countdown_expire_actions' => 'message',
					'countdown_expire_actions!' => 'hide',
				),
			)
		);

		$this->end_controls_section();
	}

	private $default_countdown_labels;

	private function init_default_countdown_labels() {
		$this->default_countdown_labels = array(
			'label_months' => esc_html__( 'Months', 'cmsmasters-elementor' ),
			'label_weeks' => esc_html__( 'Weeks', 'cmsmasters-elementor' ),
			'countdown_label_days' => esc_html__( 'Days', 'cmsmasters-elementor' ),
			'countdown_label_hours' => esc_html__( 'Hours', 'cmsmasters-elementor' ),
			'countdown_label_minutes' => esc_html__( 'Minutes', 'cmsmasters-elementor' ),
			'countdown_label_seconds' => esc_html__( 'Seconds', 'cmsmasters-elementor' ),
		);
	}

	public function get_default_countdown_labels() {
		if ( ! $this->default_countdown_labels ) {
			$this->init_default_countdown_labels();
		}

		return $this->default_countdown_labels;
	}

	private function render_countdown_item( $settings, $label, $part_class ) {
		echo '<div class="' . $this->get_widget_class() . '__item">' .
			'<span class="' . $this->get_widget_class() . '__digits ' . esc_attr( $part_class ) . '"></span>';

		if ( $settings['countdown_show_labels'] ) {
			$default_labels = $this->get_default_countdown_labels();

			$label = ( ! empty( $settings[ $label ] ) ? $settings[ $label ] : $default_labels[ $label ] );

			echo ' <span class="' . $this->get_widget_class() . '__label">' .
				esc_html( $label ) .
			'</span>';
		}

		echo '</div>';
	}

	private function get_strftime( $settings ) {
		$limits = array(
			'countdown_show_days' => 'days',
			'countdown_show_hours' => 'hours',
			'countdown_show_minutes' => 'minutes',
			'countdown_show_seconds' => 'seconds',
		);

		$last_index = array_key_last( $limits );

		foreach ( $limits as $limit => $key ) {
			if ( $settings[ $limit ] ) {
				$this->render_countdown_item( $settings, 'countdown_label_' . $key, $this->get_widget_class() . '__' . $key );
			}

			if ( $last_index !== $limit && '' !== $settings['countdown_colon_separator_show'] ) {
				echo '<span class="' . $this->get_widget_class() . '__colon">' .
					'<span>:</span>' .
				'</span>';
			}
		}
	}

	private function get_evergreen_interval( $settings ) {
		$hours = empty( $settings['countdown_evergreen_counter_hours'] ) ? 0 : ( $settings['countdown_evergreen_counter_hours'] * HOUR_IN_SECONDS );
		$minutes = empty( $settings['countdown_evergreen_counter_minutes'] ) ? 0 : ( $settings['countdown_evergreen_counter_minutes'] * MINUTE_IN_SECONDS );
		$evergreen_interval = $hours + $minutes;

		return $evergreen_interval;
	}

	private function get_actions( $settings ) {
		if ( empty( $settings['countdown_expire_actions'] ) || ! is_array( $settings['countdown_expire_actions'] ) ) {
			return false;
		}

		$actions = array();

		foreach ( $settings['countdown_expire_actions'] as $action ) {
			$action_to_run = array( 'type' => $action );

			if ( 'redirect' === $action ) {
				if ( empty( $settings['countdown_expire_redirect_url']['url'] ) ) {
					continue;
				}

				$action_to_run['redirect_url'] = $settings['countdown_expire_redirect_url']['url'];
			}

			$actions[] = $action_to_run;
		}

		return $actions;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.8.0
	 * @since 1.9.2 Added rendering countdown for dynamic tags.
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$due_date = $settings['countdown_due_date'];

		if ( 'evergreen' === $settings['countdown_type'] ) {
			$this->add_render_attribute( 'wrapper', 'data-evergreen-interval', $this->get_evergreen_interval( $settings ) );
		} else {
			if ( 'dynamic' === $settings['countdown_type'] ) {
				if ( 'current' === $settings['countdown_dynamic_type'] ) {
					$post_type = get_post_type();

					$post_data = '';

					if ( 'product' === $post_type ) {
						$post_data = wc_get_product( get_the_ID() );
					} elseif ( 'tribe_events' === $post_type ) {
						$post_data = tribe_get_event( get_the_ID() );
					}

					if ( ! $post_data ) {
						return;
					}

					if ( 'product' === $post_type ) {
						$start_date = $post_data->get_date_on_sale_from( 'edit' );
						$end_date = $post_data->get_date_on_sale_to( 'edit' );
					} elseif ( 'tribe_events' === $post_type ) {
						$event_id = $post_data->ID;

						$start_date = get_post_meta( $event_id, '_EventStartDate', true );
						$end_date = get_post_meta( $event_id, '_EventEndDate', true );

					}

					$select_date = ( isset( $settings['countdown_dynamic_dates'] ) && 'start' === $settings['countdown_dynamic_dates'] ? $start_date : $end_date );
				} else {
					$select_date = ( isset( $settings['countdown_dynamic'] ) ? $settings['countdown_dynamic'] : '' );
					$select_date = str_replace( '/', '-', $select_date );
				}

				$timestamp = strtotime( $select_date );
				$gmt_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
				$due_date = gmdate( 'Y-m-d H:i', $timestamp + $gmt_offset );
			}

			$gmt = get_gmt_from_date( $due_date . ':00' );
			$due_date = strtotime( $gmt );
		}

		$actions = false;

		if ( ! CmsmastersPlugin::elementor()->editor->is_edit_mode() ) {
			$actions = $this->get_actions( $settings );
		}

		if ( $actions ) {
			$this->add_render_attribute( 'wrapper', 'data-expire-actions', wp_json_encode( $actions ) );
		}

		$this->add_render_attribute( 'wrapper', array(
			'class' => $this->get_widget_class() . '__wrapper',
			'data-date' => $due_date,
		) );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

			$this->get_strftime( $settings );

		echo '</div>';

		if ( $actions && is_array( $actions ) ) {
			foreach ( $actions as $action ) {
				if ( 'message' !== $action['type'] ) {
					continue;
				}

				echo '<div class="' . $this->get_widget_class() . '__expire-message">' .
					esc_html( $settings['countdown_message_after_expire'] ) .
				'</div>';
			}
		}
	}
}
